<?php

namespace App\Http\Controllers\Api;


/*use App\Http\Controllers\Controller;*/
use App\Http\Controllers\FrontEnd\ACOrderController;
use App\Http\Controllers\FrontEnd\ACXCronController;
use App\Http\Controllers\FrontEnd\ACXWalletController;
use App\Http\Controllers\FrontEnd\BlockchainController;
use App\Http\Controllers\FrontEnd\TaskController;
use App\Jobs\CreateLastPrice;
use App\Models\Apikey;
use App\Models\Blockchain;
use App\Models\XchangInfo;
use App\UserCurr;
use Illuminate\Support\Facades\Auth;
use App\Models\Market;
use App\Models\Xchange;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ApiController extends BaseController
{
    //
    protected $amountLimit = ['rpz_btc','vit_btc'];

    public function __construct()
    {
        $this->middleware('apiauth',['only'=>['trade']]);
    }

    public function getMarkets(Request $request)
    {
        $market = Market::where('is_show',1)->select('id','market_name as market')->get();
        //dump($market);
        //if(1==1)return "1";
        return $this->success(0,'',$market);
    }

    // 后台接入 后台调用。不走apiauth中间件，可以放到后台代码使用jwt认证。
    public function createUserApikeys(Request $request)
    {
        //   dump("123");
        //   if(1==1)return;
        //$this->check($request);
        $validator = Validator::make($request->all(), [//
            'user_id'       => 'required',
        ], [
            'user_id.required'    => 'The user_id field is required.',
        ]);
        if ($validator->fails()) {
            return $this->respondWithFailedValidation($validator);
        }
        $apikeys = Apikey::firstOrNew(['user_id'=>$request->user_id]);
        $apikeys->user_id = $request->user_id;
        $apikeys->key = str_random(50);
        $apikeys->secret = str_random(60);
        $apikeys->save();
        return $this->success(0,'success',['key'=>$apikeys->key,'secret'=>$apikeys->secret]);
    }

    protected function getUserIdByApikey($key)
    {
        return DB::table('apikeys')->where('key',$key)->value('user_id');
    }

    protected function checkMarket($market)
    {
        $market_id = Market::where('market_name',$market)->where('is_show',1)->value('id');
        return $market_id;
    }

    public function getHighestBid(Request $request)
    {
        $request->market=strtolower($request->market);
        $market_id = $this->checkMarket($request->market);
        if (is_null($market_id)) {
            return $this->failure('10001','No such market listed');
        }
        if (in_array($request->market,$this->amountLimit)) {
            $where = [['market_name',$request->market],['status',0],['rvolume','>=',0.01],['type',2]];
        } else {
            $where = ['market_name'=>$request->market,'status'=>0,'type'=>2];  //$request->market_id
        }

        $market_name=strtolower($request->market);
        $highest_bid =  Xchange::where('market_name',$market_name)
            ->where($where)
            ->orderBy('price','desc')
            ->limit(1)
            ->value('price');
        // dump($market_name);
        //   dump($where);

        //   dump($highest_bid);
        // return ['HighestBid'=>$highest_bid];
        return $this->success(0,'success',['highestBid'=>$highest_bid]);

    }

    public function getLowestAsk(Request $request)
    {
        $request->market=strtolower($request->market);
        $market_id = $this->checkMarket($request->market);
        if (is_null($market_id)) {
            return $this->failure('10001','No such market listed');
        }

        if (in_array($request->market,$this->amountLimit)) {
            $where = [['market_name',$request->market],['status',0],['rvolume','>=',0.01],['type',1]];
        } else {
            $where = ['market_name'=>$request->market,'status'=>0,'type'=>1];  //$request->market_id
        }
        $Lowest_ask =  Xchange::where('market_name',$request->market)
            ->where($where)
            ->orderBy('price','asc')
            ->limit(1)
            ->value('price');

        //return ['LowestAsk'=>$Lowest_ask];
        return $this->success(0,'success',['lowestAsk'=>$Lowest_ask]);
    }


    public function getSellOrder(Request $request)
    {
        $request->market=strtolower($request->market);
        // $request_uri = $_SERVER['REQUEST_URI'];
        $market_id = $this->checkMarket($request->market);
        if (is_null($market_id)) {
            return $this->failure('10001','No such market listed');
        }
        if(($market_id == 4) ||($market_id == 5)) {
            $where = [['market_id',$market_id],['status',0],['rvolume','>=',0.01],['type',1]];  //$request->market_id
        }else {
            $where = ['market_id'=>$market_id,'status'=>0,'type'=>1];  //$request->market_id
        }
        $order = Xchange::where($where)->where("rvolume",">",0)->select('type', 'rvolume', 'price')->orderBy('price','desc')->get();
        //dump($order);
        if (count($order)) {
            $order = $order->groupBy('type')->sortBy('price')->toArray();
            if (!empty($order[1])) {
                $order['sell'] = $order[1];
                unset($order[1]);
                $collection = collect($order['sell']);
                $group_by_sell = array_reverse($collection->groupBy('price')->toArray());
                $i = 0;
                $total_vol = 0;
                foreach ($group_by_sell as $key=>$value) {
                    ++$i;
                    $sum_volume = 0;
                    $current_total = 0;
                    foreach ($value as $kk=>$vv) {
                        $sum_volume += $vv['rvolume'];
                        $arr['volume'] = number_format($sum_volume,8,'.','');
                        $arr['price'] = $key;
                        $arr['total'] = number_format($sum_volume * $key,8,'.','');
                        $total_vol += $vv['rvolume'];
                    }
                    $temp[] = $arr;
                }
                // $arr['sell_total_vol'] = number_format($total_vol,8,'.','');
            }
        } else {
            $temp = [];
        }
        //  dump($where);
        return $this->success(0,'success',$temp);
    }

    public function getBuyOrder(Request $request)
    {
        $request->market=strtolower($request->market);
        // $request_uri = $_SERVER['REQUEST_URI'];
        $market_id = $this->checkMarket($request->market);
        if (is_null($market_id)) {
            return $this->failure('10001','No such market listed');
        }
        if(($market_id == 4) ||($market_id == 5)) {
            $where = [['market_id',$market_id],['status',0],['rvolume','>=',0.01],['type',2]];  //$request->market_id
        }else {
            $where = ['market_id'=>$market_id,'status'=>0,'type'=>2];  //$request->market_id
        }
        $order = Xchange::where($where)->where("rvolume",">",0)->select('type', 'rvolume', 'price')->orderBy('price','asc')->get();
        if (count($order)) {
            $order = $order->groupBy('type')->sortBy('price')->toArray();
            if (!empty($order[2])) {
                $order['buy'] = $order[2];
                unset($order[2]);
                $collection = collect($order['buy']);
                $group_by_sell = array_reverse($collection->groupBy('price')->toArray());
                $i = 0;
                $total_vol = 0;
                foreach ($group_by_sell as $key=>$value) {
                    ++$i;
                    $sum_volume = 0;
                    $current_total = 0;
                    foreach ($value as $kk=>$vv) {
                        $sum_volume += $vv['rvolume'];
                        $arr['volume'] = number_format($sum_volume,8,'.','');
                        $arr['price'] = $key;
                        $arr['total'] = number_format($sum_volume * $key,8,'.','');
                        $total_vol += $vv['rvolume'];
                    }
                    $temp[] = $arr;
                }
                // $arr['sell_total_vol'] = number_format($total_vol,8,'.','');
            }
        } else {
            $temp = [];
        }
        return $this->success(0,'success',$temp);
    }

    protected function respondWithFailedValidation(\Illuminate\Validation\Validator $validator)
    {
        //TODO API返回更详细的code
        /* $failed_info  = $validator->failed();
         $first_failed = key($failed_info[key($failed_info)]);
         /*switch ($first_valid)
         {
             case "tradetype" : return $this->failure(10010,'The selected tradetype is invalid.');break;
             case "tradeamount" : return $this->failure(10011,'The selected tradeamount is invalid.');break;
         }*/
        // 只取出一条错误信息
        return $this->failure(-1,$validator->messages()->first());
    }

    public function trade(Request $request)
    {
        // dump(132);
        //if(1==1)return;
        $request->market=strtolower($request->market);
        $validator = Validator::make($request->all(), [
            'tradetype'     => 'required|in:sell,buy',
            'tradeamount'   => 'required|numeric|min:0.00000001',
            'tradeprice'    => 'required|numeric|min:0.00000001',
            'market'        => 'required|exists:market,market_name',
        ], [
            'tradetype.required'    => 'The tradetype field is required.',
            'tradetype.in_array'    => 'The tradetype field does not exist in [buy,sell].',
            'tradeamount.required'  => 'The tradeamount field is required.',
            'tradeamount.numeric'   => 'The tradeamount must be a number.',
            'tradeamount.min'       => 'The tradeamount must be at least 0.00000001',
            'tradeprice.required'   => 'The tradeprice field is required.',
            'tradeprice.numeric'    => 'The tradeprice must be a number.',
            'tradeprice.min'        => 'The tradeprice must be at least 0.00000001',
            'market.required'       => 'The market field is required.'
        ]);
        if ($validator->fails()) {
            return $this->respondWithFailedValidation($validator);
        }

        if (in_array($request->market,$this->amountLimit) && $request->tradeamount < 0.01) {
            return $this->failure(-1,$request->market.' market trade amount must be at least 0.01');
        }
        //Test  TODO 根据API KEY 获取User id
        $api_user = $this->getUserIdByApikey($request->key);
        $market_info = Market::where('market_name',$request->market)->where('is_show',1)->select('market_name','fee','id')->first();
        if(is_null($market_info)) {
            return $this->failure(-1,'No such market listed');
        }
        //获取fee
        $market_name = $market_info->market_name;
        $currency = explode('_',$market_name);
        $buy_client = $this->getClient($currency[1]);
        $sell_client = $this->getClient($currency[0]);
        // $market_id = $market_info->id;
        if((!$buy_client) || (!$sell_client)) {
            return $this->failure(0,"failure");
        }

        //    $order_id = 'Nova'.$api_user.uniqid();

        $total=bcmul($request->tradeamount,$request->tradeprice,8);

        if($request->tradetype == 'buy') {
            $balances = $buy_client->getBalance($api_user);
            if($total > $balances) {
                return $this->failure(10001,trans('market.not_enough_balance'));
            }
        }elseif($request->tradetype == 'sell') {
            $balances     = $sell_client->getBalance($api_user);
            if($request->tradeamount > $balances) {
                return $this->failure(10001,trans('market.not_enough_balance'));
            }
        }

        //--获取费率
        $userCurr = new UserCurr();
        $fee_base = $userCurr->getFeeRate($api_user,$market_name);


        //--插入
        $request->request->set("id",$api_user);
        $request->request->set("opear","trade");


        $request->request->set("amount",$request->tradeamount);
        $request->request->set("price",$request->tradeprice);
        //--费率
        $feeRate=($fee_base*100)."%";
        $request->request->set("feeRate",$feeRate);
        //-计量币种
        $request->request->set("currency",$currency[1]);
        //--交易币种
        $request->request->set("tradeCurr",$currency[0]);

        //成交的总价
        $request->request->set("total",$total);

        if($request->tradetype == 'buy'){
            $request->request->set("type",10);
            //收入
            $fee_temp = bcmul($request->tradeamount,$fee_base,8);
            $shouru=bcsub($request->tradeamount,$fee_temp,8);
            $request->request->set("netTotal",$shouru);
            //手续费
            $fee=bcmul($request->tradeamount,$fee_base,8);
            $request->request->set("fee",$fee);
        }elseif ($request->tradetype == 'sell'){
            $request->request->set("type",20);
            //收入
            $fee_shouru=bcsub(1,$fee_base,9);
            $shouru = bcmul($total,$fee_shouru,8);
            $request->request->set("netTotal",$shouru);
            //手续费
            $fee=bcmul($total,$fee_base,8);
            $request->request->set("fee",$fee);
        }else {
            DB::rollback();
            return $this->failure(10002,'Trade failed');
        }


        return ACXWalletController::jiaoyi_shuzihuobi($request);//
        //  //--加入队列
        //  TaskController::push($request->request->all());
//
        //// if($api_user==128){
        ////   //  dump($request->request->all());
        //// }
        //  /*if(1==1)*/
        //  return $this->success(0,'Trade success');
    }

    //// private function sell($api_user,$order_id,$market_name,$market_id,$vol,$price,$original_vol,$fee=0,$fee_base){
    ////     if(($vol <= 0) || ((($market_id == 4) || ($market_id == 5)) && ($vol < 0.01))) {return;}
    ////     if(($market_id == 4) || ($market_id == 5)) {
    ////         $find_flag = '>=';
    ////         $find_rvolume = 0.01;
    ////     }else{
    ////         $find_flag = '>';
    ////         $find_rvolume = 0;
    ////     }
    ////     $row = DB::table('xchange')->where([
    ////             ['status','0'],
    ////             ['rvolume',$find_flag,$find_rvolume],
    ////             ['market_id',$market_id],
    ////             ['type',2],
    ////             ['price','>=',$price]]
    ////     )->orderBy('price','desc')->lockForUpdate()->first();
    ////     if($row){
    ////         if(($vol-$row->rvolume) >= 0) {
    ////             DB::table('xchange')->where('id',$row->id)->update(['rvolume'=>0,'status'=>1,'updated_at'=>date('Y-m-d H:i:s',time())]);
    ////             DB::table('xchange')->insert(['type'=>1,'market_id'=>$market_id,'user_id'=>$api_user,'price'=>$row->price,'volume'=>$row->rvolume,'rvolume'=>0,'status'=>1,'fee'=>round($row->price * $row->rvolume * $fee,8),'created_at'=>date('Y-m-d H:i:s',time()),'updated_at'=>date('Y-m-d H:i:s',time()),'order_id'=>$order_id,'market_name'=>$market_name,'total_price'=>round($row->price*$row->rvolume,8)]);
    ////             DB::table('xchange_info')->insert(['user_id'=>$api_user,'fee'=>0,'type'=>1,'market_id'=>$market_id,'last_price'=>$row->price,'volume'=>$row->rvolume,'created_at'=>time(),'updated_at'=>time()]);
    ////             $this->lastpriceJob($market_id,$market_name,$row->price);
    ////             $user_id = 'NovaCoinMain';
    ////             $seller_receive = $this->doSellFee($row->rvolume,$row->price);
    ////             if($seller_receive) {
    ////                 BlockchainController::insertSellOption($market_name,$user_id,$api_user,$seller_receive,$order_id,1);
    ////             }
    ////             BlockchainController::insertSellOptionTrans($market_name,$user_id,$row->user_id,$row->rvolume,$order_id,1);
    ////             $vol = $vol - $row->rvolume;
    ////             $this->////sell($api_user,$order_id,$market_name,$market_id,$vol,$price,$vol,$fee,$fee_base);
    ////         } else {
    ////             $rvolume = $row->rvolume - $vol;
    ////             DB::table('xchange')->where('id',$row->id)->update(['rvolume'=>$rvolume,'updated_at'=>date('Y-m-d H:i:s',time())]);
    ////             DB::table('xchange')->insert(['type'=>1,'market_id'=>$market_id,'user_id'=>$api_user,'price'=>$row->price,'volume'=>$vol,'rvolume'=>0,'status'=>1,'fee'=>round($row->price * $vol * $fee,8),'created_at'=>date('Y-m-d H:i:s',time()),'updated_at'=>date('Y-m-d H:i:s',time()),'order_id'=>$order_id,'market_name'=>$market_name,'total_price'=>round($row->price*$vol,8)]);
    ////             DB::table('xchange_info')->insert(['user_id'=>$api_user,'fee'=>0,'type'=>1,'market_id'=>$market_id,'last_price'=>$row->price,'volume'=>$vol,'created_at'=>time(),'updated_at'=>time()]);
    ////             $user_id = 'NovaCoinMain';
    ////             $seller_receive = $this->doSellFee($vol,$row->price);
    ////             if($seller_receive) {
    ////                 BlockchainController::insertSellOption($market_name,$user_id,$api_user,$seller_receive,$order_id,1);
    ////             }
    ////             BlockchainController::insertSellOptionTrans($market_name,$user_id,$row->user_id,$vol,$order_id,1);
    ////             $this->lastpriceJob($market_id,$market_name,$row->price);
    ////         }
    ////     } else {
    ////         DB::table('xchange')->insert(['type'=>1,'market_id'=>$market_id,'user_id'=>$api_user,'price'=>$price,'volume'=>$vol,'rvolume'=>$original_vol,'status'=>0,'fee'=>$fee,'created_at'=>date('Y-m-d H:i:s',time()),'updated_at'=>date('Y-m-d H:i:s',time()),'order_id'=>$order_id,'market_name'=>$market_name,'total_price'=>round($price*$vol,8)]);
    ////     }
    //// }

    //  private function last//priceJob($market_id,$market_name,$price)
    //  {
    //      $this->dispatch(new CreateLastPrice($market_id,$market_name,$price));
    //  }

    // private function buy($api_user,$order_id,$market_name,$market_id,$vol,$price,$original_vol,$fee=0,$fee_base){
    //     if(($vol <= 0) || ((($market_id == 4) || ($market_id == 5)) && ($vol < 0.01))) {return;}
    //     if(($market_id == 4) || ($market_id == 5)) {
    //         $find_flag = '>=';
    //         $find_rvolume = 0.01;
    //     }else{
    //         $find_flag = '>';
    //         $find_rvolume = 0;
    //     }
    //     $row = DB::table('xchange')->where([
    //             ['status',0],
    //             ['rvolume',$find_flag,$find_rvolume],
    //             ['market_id',$market_id],
    //             ['type',1],
    //             ['price','<=',$price]]
    //     )->orderBy('price','asc')->lockForUpdate()->first();
    //     if($row){
    //         if(($vol-$row->rvolume) >= 0) {
    //             DB::table('xchange')
    //                 ->where(['id'=>$row->id])
    //                 ->update(['rvolume'=>0,'status'=>1,'updated_at'=>date('Y-m-d H:i:s',time())]);
    //             DB::table('xchange')->insert(['type'=>2,'market_id'=>$market_id,'user_id'=>$api_user,'price'=>$row->price,'volume'=>$row->rvolume,'rvolume'=>0,'status'=>1,'fee'=>round($row->price * $row->rvolume * $fee,8),'created_at'=>date('Y-m-d H:i:s',time()),'updated_at'=>date('Y-m-d H:i:s',time()),'order_id'=>$order_id,'market_name'=>$market_name,'total_price'=>round($row->price*$row->rvolume,8)]);
    //             $user_id = 'NovaCoinMain';
    //             BlockchainController::insertBuyExchangeOption($market_name,$user_id,$api_user,$row->rvolume,$order_id,2);
    //             $seller_receive = $this->doSellFee($row->rvolume,$row->price,$fee_base);
    //             if($seller_receive) {
    //                 BlockchainController::insertSellOption($market_name,$user_id,$row->user_id,$seller_receive,$order_id,2);
    //             }
    //             DB::table('xchange_info')
    //                 ->insert(['user_id'=>$api_user,'type'=>2,'market_id'=>$market_id,'fee'=>0,'last_price'=>$row->price,'volume'=>$row->rvolume,'created_at'=>time(),'updated_at'=>time()]);
    //             $vol = $vol - $row->rvolume;
    //             $this->buy($order_id,$market_name,$market_id,$vol,$price,$vol,$fee,$fee_base);
    //             $this->lastpriceJob($market_id,$market_name,$row->price);
    //         } else {
    //             $rvolume = $row->rvolume - $vol;
    //             DB::table('xchange')
    //                 ->where(['id'=>$row->id])
    //                 ->update(['rvolume'=>$rvolume,'updated_at'=>date('Y-m-d H:i:s',time())]);
    //             DB::table('xchange')->insert(['type'=>2,'market_id'=>$market_id,'user_id'=>$api_user,'price'=>$row->price,'volume'=>$vol,'rvolume'=>0,'status'=>1,'fee'=>round($row->price * $vol * $fee,8),'created_at'=>date('Y-m-d H:i:s',time()),'updated_at'=>date('Y-m-d H:i:s',time()),'order_id'=>$order_id,'market_name'=>$market_name,'total_price'=>round($row->price*$vol,8)]);
    //             DB::table('xchange_info')
    //                 ->insert(['user_id'=>$api_user,'type'=>2,'market_id'=>$market_id,'fee'=>0,'last_price'=>$row->price,'volume'=>$vol,'created_at'=>time(),'updated_at'=>time()]);
    //             $user_id = 'NovaCoinMain';
    //             BlockchainController::insertBuyExchangeOption($market_name,$user_id,$api_user,$vol,$order_id,2);
    //             $seller_receive = $this->doSellFee($vol,$row->price,$fee_base);
    //             if($seller_receive) {
    //                 BlockchainController::insertSellOption($market_name,$user_id,$row->user_id,$seller_receive,$order_id,2);
    //             }
    //             $this->lastpriceJob($market_id,$market_name,$row->price);
    //         }
    //     } else {
    //         DB::table('xchange')->insert(['type'=>2,'market_id'=>$market_id,'user_id'=>$api_user,'price'=>$price,'volume'=>$vol,'rvolume'=>$original_vol,'status'=>0,'fee'=>$fee,'created_at'=>date('Y-m-d H:i:s',time()),'updated_at'=>date('Y-m-d H:i:s',time()),'order_id'=>$order_id,'market_name'=>$market_name,'total_price'=>round($price*$vol,8)]);
    //     }//
    // }

    //  private function doSellFee($vol,$price,$fee_base=0.005)
    //  {
    //      $sell_fee = round($vol*$price*$fee_base,8);
    //      if($sell_fee < 0.00000001) {$sell_fee = 0.00000001;}
    //      $seller_receive = round($vol*$price-$sell_fee,8);
    //      if($seller_receive > 0.00000001) {
    //          return $seller_receive;
    //      }else{
    //          return 0;
    //      }
    //  }

    //TODO trade API 合并 MarketController

    /** VIT last price
     * @param Request $request
     * @return array
     */
    public function getLastPrice(Request $request)
    {
        $market = Market::all()->pluck('market_name');
        $request->market=strtolower($request->market);
        $market = json_decode(json_encode($market),1);
        if(!in_array($request->market,$market)){
            return ['code'=>400,'error'=>'market is not exist'];
        }
        try{
            $lastprice = Market::where('market_name',$request->market)->value('last_price');
        }catch (\Exception $e) {
            return ['code'=>400];
        }
        return ['code'=>200,'last_price'=>$lastprice];
    }


    //
    public function success($code = 0, $message = '', $data = null)
    {
        $result = array(
            'code' => $code,
            'message' => $message ? $message : 'success',
            'data' => $data ? $data : null
        );
        return response()->json($result, 200, [], JSON_UNESCAPED_UNICODE);
    }


    public function failure($code, $message)
    {
        $result = array(
            'code' => $code,
            'message' => $message ? $message : 'failure',
            'data' => null,
        );
        return response()->json($result, 200, [], JSON_UNESCAPED_UNICODE);
    }



    /** 取消订单
     * @param Request $request
     * @return array
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function cancel(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tradetype' => 'required|in:sell,buy',
            'maxprice' => 'numeric|max:100',
            'minprice' => 'numeric|min:0.00000001',
            //'market'        => 'required|exists:market,market_name',
        ], [
            'tradetype.required' => 'The tradetype field is required.',
            'tradetype.in_array' => 'The tradetype field does not exist in [buy,sell].',
            'maxprice.numeric' => 'The maxprice must be a number.',
            'maxprice.max' => 'The maxprice must be at most 100',
            'minprice.numeric' => 'The minprice must be a number.',
            'minprice.min' => 'The minprice must be at least 0.00000001',
            //'market.required'       => 'The market field is required.'
        ]);
        if ($validator->fails()) {
            return $this->respondWithFailedValidation($validator);
        }
        $market = Market::all()->pluck('market_name');
        $market = json_decode(json_encode($market), 1);
        $request->market=strtolower($request->market);
        if (!in_array($request->market, $market)) {
            return ['code' => 400, 'error' => 'market is not exist'];
        }

        $user = Apikey::where('key', $request->key)->first();
        $user_id = $user->user_id;
        empty($request->maxprice) ? $request->maxprice = 100 : $request->maxprice;
        empty($request->minprice) ? $request->minprice = 0 : $request->minprice;

        if ($request->maxprice <= $request->minprice) {
            return ['code' => 400, 'error' => 'maxprice should be more than minprice'];
        }

        if ($request->tradetype == 'sell') {
            $whereType['type'] = 1;
        } else if ($request->tradetype == 'buy') {
            $whereType['type'] = 2;
        }

        $xchanges = Xchange::where('status', '0')
            ->whereBetween('price', [$request->minprice, $request->maxprice])
            ->where('user_id', $user_id)
            ->where('market_name', $request->market)
            ->where($whereType)
            ->get();

        if (count($xchanges) == 0) {
            return ['code' => 404, 'message' => trans('order.order_not_found')];   //TODO 设置了Pin才能交易
        }
        foreach ($xchanges as $xchange) {
            $request->request->set("orderid",$xchange->order_id);
            $request->request->set("id",$user_id);
            $request->request->set("opear","cancelOrder");

            ACOrderController::globle_cancel($request);

            //TaskController::push($request->request->all());
        }

        $code = 200;
        $message = trans('order.cancel_success');
        return ['code' => $code, 'message' => $message];
    }


    public function tradecount(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tradetype' => 'required|in:sell,buy',
            'maxprice' => 'numeric|max:100',
            'minprice' => 'numeric|min:0.00000001',
            //'market'        => 'required|exists:market,market_name',
        ], [
            'tradetype.required' => 'The tradetype field is required.',
            'tradetype.in_array' => 'The tradetype field does not exist in [buy,sell].',
            'maxprice.numeric' => 'The maxprice must be a number.',
            'maxprice.max' => 'The maxprice must be at most 100',
            'minprice.numeric' => 'The minprice must be a number.',
            'minprice.min' => 'The minprice must be at least 0.00000001',
            //'market.required'       => 'The market field is required.'
        ]);

        empty($request->maxprice) ? $request->maxprice = 100 : $request->maxprice;
        empty($request->minprice) ? $request->minprice = 0 : $request->minprice;
        if ($request->maxprice <= $request->minprice) {
            return ['code' => 400, 'error' => 'maxprice should be more than minprice'];
        }
        if ($validator->fails()) {
            return $this->respondWithFailedValidation($validator);
        }
        $market = Market::all()->pluck('market_name');
        $market = json_decode(json_encode($market), 1);
        $request->market=strtolower($request->market);
        if (!in_array($request->market, $market)) {
            return ['code' => 400, 'error' => 'market is not exist'];
        }
        if ($request->maxprice <= $request->minprice) {
            return ['code' => 400, 'error' => 'maxprice should be more than minprice'];
        }
        if ($request->tradetype == 'sell') {
            $whereType['type'] = 1;
        } else if ($request->tradetype == 'buy') {
            $whereType['type'] = 2;
        }

        $xchanges = Xchange::where('status', '0')
            ->whereBetween('price', [$request->minprice, $request->maxprice])
            ->where('market_name', $request->market)
            ->where($whereType)
            ->get();
        return ['code' => 200, 'count' => count($xchanges)];
    }


    //TODO 修改为API
    public function openorders(Request $request)
    {
        //   dump($request->request->all());
        //   dump($request->key);

        //if(1==1)return;
        $user = Apikey::where('key', $request->key)->first();
        //  $user = Apikey::where('key', $request->key)->first();
        // dump($user);
        //    if(1==1)return;
        //   $user_id = $user->user_id;
        if($user){
            $user_id = $user->user_id;
            $where = ['user_id' => $user_id, 'status' => 0];
            $open_order = DB::table('xchange')
                ->leftjoin('market as m', 'market_id', '=', 'm.id')
                ->select('xchange.order_id as order id', 'm.market_name as trading market', 'xchange.type as status', 'xchange.volume as order number', 'xchange.rvolume as Surplus order', 'xchange.price as price', 'xchange.total_price as total transaction price')
                ->where($where)
                ->get();


            //   dump($query);
            //  if(1==1)return;
            foreach ($open_order as $k => $v) {
                if ($v->status == 1) {
                    $v->status = 'sell';
                } elseif ($v->status == 2) {
                    $v->status = 'buy';
                }
            }
            return ['code' => '200', 'order' => $open_order];
        }

        return ['code' => '200', 'order' => [],'message'=>"user is null"];
    }


    /** 通过订单号取消订单
     * @param Request $request
     * @return array
     */
    public function cancelbyorder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tradetype' => 'required|in:sell,buy',
            'order_id' => 'required',
            //'market'        => 'required|exists:market,market_name',
        ], [
            'tradetype.required' => 'The tradetype field is required.',
            'tradetype.in_array' => 'The tradetype field does not exist in [buy,sell].',
            //'market.required'       => 'The market field is required.'
        ]);
        if ($validator->fails()) {
            return $this->respondWithFailedValidation($validator);
        }


        $market = Market::all()->pluck('market_name');
        $market = json_decode(json_encode($market), 1);
        if (!in_array($request->market, $market)) {
            return ['code' => 400, 'error' => 'market is not exist'];
        }

        $user = Apikey::where('key', $request->key)->first();
        $user_id = $user->user_id;

        if ($request->tradetype == 'sell') {
            $whereType['type'] = 1;
        } else if ($request->tradetype == 'buy') {
            $whereType['type'] = 2;
        }
        //   dump($request->order_id);
        //  dump(json_decode($request->order_id));
        //  dump($request->market);
        //  dump($whereType);


        $order_json_decode=json_decode($request->order_id);
        if(empty($order_json_decode)){
            $code=400;
            $message = trans('order.order_id_no_json');
            return ['code' => $code, 'message' => $message];
        }

        $xchanges = Xchange::where('status', '0')
            ->whereIn('order_id', json_decode($request->order_id))
            ->where('user_id', $user_id)
            ->where('market_name', $request->market)
            ->where($whereType)
            ->get();

        if (count($xchanges) == 0) {
            return ['code' => 404, 'message' => trans('order.order_not_found')];   //TODO 设置了Pin才能交易
        }

        foreach ($xchanges as $xchange) {
            //--调用 主线程的 取消订单
            $request->request->set("orderid", $xchange->order_id);
            $request->request->set("id", $user_id);
            $request->request->set("opear", "cancelOrder");
            ACOrderController::globle_cancel($request);
        }
        $code = 200;
        $message = trans('order.cancel_success');
        return ['code' => $code, 'message' => $message];
    }

}
