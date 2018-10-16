<?php
namespace App\Http\Controllers\FrontEnd;
/**
 * Created by PhpStorm.
 * User: DELL
 * Date: 2018/4/21
 * Time: 12:48
 * 订单交易控件，通过其他调用
 *
 */
use App\Http\Controllers\Controller;
use App\Jobs\CreateLastPrice;
use App\Library\Currency\AccountAmount;
use App\Library\Trade\Account;
use App\Models\Blockchain;
use App\Models\Xchange;

use App\Models\XchangeDetail;
use App\Models\XchangInfo;
use App\UserCurr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ACXWalletController extends Controller{
    /**
     * 发送消息 买和卖
     * @param Request $request
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function currencyTrade_001(Request $request)
    {
        //判断数据是不是合法
        $this->validate($request,[
            'amount' => 'bail|required', // 数字货币总量
            'price' => 'bail|required',  // 价格
            'total' => 'bail|required',   // 数量
            'fee' => 'bail|required', //手续费
            'netTotal' => 'bail|required', //发起交易的总值
        ]);
        //--获取前端页面发送过来的fee
        $feeRate = substr($request->feeRate,0,-1);
        $feeRate = bcdiv($feeRate,100,3);
        //进来立即获取汇率转换  读取 美金 人名币  币种对应的id
       // $currencySet = CurrencySet::where('curr_abb',$request->tradeCurr)->first(['price_cny','price_usd','curr_id']);
        //判断 主市场是否是    btc usd cny
        $traAccount= new Account();
        $referBool = $traAccount->judgeContain($request->currency,['btc','usd','cny']);
        if (empty($referBool)) return response()->json(['status'=>0,'message' => __('ac.referenceCurrencyError')]);
        //交易类型是否正确  10是买进 20是卖出
        $tranBool = $traAccount->judgeContain($request->type,[10,20]);
        if (empty($tranBool)) return response()->json(['status'=>0,'message' => __('ac.WrongTransactionType')]);
        $account = new AccountAmount();
        //传入数据本身是否有误
        $numValBool = $account->numValidate($request->total, $request->price, $request->amount, $feeRate, $request->fee, $request->netTotal, $request->type,$request->tradeCurr);
        $result=$numValBool['result'];

        if (empty($result)) return response()->json(['status'=>0,'message' => __('ac.IncomingDataIsIncorrect'),'msg'=>$numValBool['msg']]);
        //判断交易类型是买还是卖 -并判断余额是不是足够
        
        $currency[0]=$request->tradeCurr;
        $currency[1]=$request->currency;//"btc";
        $buy_client = self::getClient($currency[1]);
        $sell_client = self::getClient($currency[0]);
        $api_user=Auth::id(); //--用户id//

        if ($request->type == 10){
            $currency_balance=$buy_client->getBalance($api_user);
            $comp = bccomp($request->total,$currency_balance,8);
            if ($comp === 1){
                return response()->json(['status'=>0,'message' => __('ac.InsufficientCurrency')]);
            }
        }else{
            //--卖看是否余额rpz
            $currency_balance=$sell_client->getBalance($api_user);
            $comp = bccomp($request->amount,$currency_balance,8);
           // dump("currency_balance:".$currency_balance);
           // dump("request->amount:".$request->amount);
           // dump("comp:".$comp);
            
            if ($comp === 1){
                return response()->json(['status'=>0,'message' => __('ac.InsufficientCurrency')]);
            }
        }

     //
     //   if ($request->type == 10){
     //
     //       $accountBool = $account->judgeBalance($request->netTotal,$request->currency,Auth::id(),$request->tradeCurr,$request->type);
     //   }else{
     //
     //       $accountBool = $account->judgeBalance($request->amount,$request->currency,Auth::id(),$request->tradeCurr,$request->type);
     //   }
     //   if (empty($accountBool)) return response()->json(['status'=>0,'message' => '货币不足']);




        $request->request->set("id",Auth::id());
        $request->request->set("opear","trade");
        //--加入队列
        //TaskController::push($request->request->all());
       //if(Auth::id()==128){
       //    dump($request->request->all());
       //}//
        /*if(1==1)*/

        return self::jiaoyi_shuzihuobi($request);
       // return response()->json(['status'=>1,'message' => __('ac.tradeSuccess')]);

      // //RPZ_BTC 拼接成这样的格式
      // $currency[0]=$request->tradeCurr;
      // $currency[1]="btc";
      // $buy_client = $this->getClient($currency[1]);
      // $sell_client = $this->getClient($currency[0]);
      // $market_name=$currency[0]."_".$currency[1];
      // $request->tradeamount=$request->amount;
      // $api_user=Auth::id(); //--用户id
      // $market_id=$this->getMarkId($request->tradeCurr);
      // if($market_id==-1)return response()->json(['status'=>0,'message' => '选择的市场错误']);
      // $order_id = 'acx'.$api_user.uniqid();
      // $fee=$request->fee;
      // $fee_base=$feeRate;
      // DB::beginTransaction();

      // $request->tradetype=$request->type==10?"buy":"sell";
      // Log::info("tradetype: ".$request->tradetype);

      // try {
      //     if($request->tradetype == 'buy') {
      //        // $fee=0;//nova传的是$request-fee是0
      //         $target_id = 1;
      //         $total=$request->netTotal; //-买需要支付 btc数额(需要加上fee)
      //         BlockchainController::insertBuyOption($market_name,$api_user,$target_id,$total,$order_id,2);
      //         $this->buy($api_user,$order_id,$market_name,$market_id,$request->amount,$request->price,$request->amount,$fee,$fee_base);
      //         $current_blockchain_opt = Blockchain::where('order_id',$order_id)->get();
      //         $opt_count = count($current_blockchain_opt);
      //         $opt_status = 0;
      //         foreach ($current_blockchain_opt as $blockchain_opt) {
      //             if($blockchain_opt->currency == $currency[0]) {
      //                 $client = $sell_client;
      //             }elseif($blockchain_opt->currency == $currency[1]) {
      //                 $client = $buy_client;
      //             }else {
      //                 DB::rollback();
      //                 return response()->json(['status'=>10002,'message' => 'Trade failed']);
      //             }
      //             $status = $client->move($blockchain_opt->user_id,$blockchain_opt->target_id,$blockchain_opt->amount);
      //             if($status) {
      //                 $blockchain_opt->status = 1;
      //                 $blockchain_opt->save();
      //                 //--更新数据库余额
      //                 $client->_get_balance($blockchain_opt->user_id);
      //                 $client->_get_balance($blockchain_opt->target_id);
      //             }
      //         }
      //         //    $this->dispatch(new CreateLastPrice($request->market,$market_name,$request->price));
      //         DB::commit();

      //         return response()->json(['status'=>1,'message' => 'Trade success']);
      //     } elseif ($request->tradetype == 'sell') {
      //         $target_id = 1;
      //         BlockchainController::insertSellFirstOption($market_name,$api_user,$target_id,round($request->tradeamount,8),$order_id,1);
      //         //BlockchainController::insertSellFeeOption($market_name,\Auth::id(),$target_id,$fee,$order_id,1);
      //         //$fee = 0.0;// 后续逻辑处理了fee
      //         $this->sell($api_user,$order_id,$market_name,$market_id,$request->amount,$request->price,$request->amount,$fee,$fee_base);


      //         $current_blockchain_opt = Blockchain::where('order_id',$order_id)->get();
      //         $opt_count = count($current_blockchain_opt);
      //         $opt_status = 0;

      //         foreach ($current_blockchain_opt as $blockchain_opt) {
      //             if($blockchain_opt->currency == $currency[0]) {
      //                 $client = $sell_client;
      //             }elseif($blockchain_opt->currency == $currency[1]) {
      //                 $client = $buy_client;
      //             }else {
      //                 DB::rollback();
      //                 return response()->json(['status'=>10003,'message' => 'Trade failed']);
      //             }
      //             $status = $client->move($blockchain_opt->user_id,$blockchain_opt->target_id,$blockchain_opt->amount);
      //             if($status) {
      //                 $blockchain_opt->status = 1;
      //                 $blockchain_opt->save();
      //                 //--更新数据库余额
      //                 $client->_get_balance($blockchain_opt->user_id);
      //                 $client->_get_balance($blockchain_opt->target_id);
      //             }
      //         }
      //         DB::commit();
      //         return response()->json(['status'=>1,'message' => 'Trade success']);
      //     } else {
      //         DB::rollback();
      //         return response()->json(['status'=>10004,'message' => 'Trade failed ']);
      //     }
      // } catch (\Exception $e) {
      //    // dump($e->getMessage());
      //     DB::rollback();
      //     return response()->json(['status'=>10005,'message' => 'Trade failed']);
      // }
    }

    //--交易數字貨幣
    public static function jiaoyi_shuzihuobi($request){
        $result=new ACXWalletController();
        //RPZ_BTC 拼接成这样的格式
        $feeRate = substr($request->feeRate,0,-1);
        $feeRate = bcdiv($feeRate,100,3);


        $currency[0]=$request->tradeCurr;
        $currency[1]=$request->currency;//"btc";
        $buy_client = $result->getClient($currency[1]);
        $sell_client = $result->getClient($currency[0]);
        $api_user=$request->id;//Auth::id(); //--用户id

        //--判断余额是否足够
        //--买，看是否金额btc
        if ($request->type == 10){


            $total_ns=bcmul($request->amount,$request->price,8);
            $comp = bccomp($request->total,$total_ns,8);
            if ($comp !=0){
//                Log::info("交易 货币不足".json_encode($request,JSON_FORCE_OBJECT));
                //--插入数据异常
                return "error";
            }
            $currency_balance=$buy_client->_get_balance($api_user);
            $comp = bccomp($request->total,$currency_balance,8);
            if ($comp === 1){
//                Log::info("交易 货币不足".json_encode($request,JSON_FORCE_OBJECT));
                return "error";
            }
            }else{
            //--卖看是否余额rpz
            $currency_balance=$sell_client->_get_balance($api_user);
            $comp = bccomp($request->amount,$currency_balance,8);
            if ($comp === 1){
//                Log::info("交易 货币不足".json_encode($request,JSON_FORCE_OBJECT));
                return "error";
            }
            }

        $market_name=$currency[0]."_".$currency[1];
        $request->tradeamount=$request->amount;

        $market_id=$result->getMarkId($request->tradeCurr);
        if($market_id==-1)return response()->json(['status'=>0,'message' => '选择的市场错误']);
        $order_id = 'acx'.$api_user.uniqid();
        $fee=$request->fee;
        $fee_base=$feeRate;
        DB::beginTransaction();

        $request->tradetype=$request->type==10?"buy":"sell";

        try {
            if($request->tradetype == 'buy') {
                // $fee=0;//nova传的是$request-fee是0
                $target_id = 1;
                $total=$request->total; //-不额外收取手续费
               // dump($total);
                BlockchainController::insertBuyOption($market_name,$api_user,$target_id,$total,$order_id,2);
                $result->buy($api_user,$order_id,$market_name,$market_id,$request->amount,$request->price,$request->amount,$fee,$fee_base);
                $current_blockchain_opt = Blockchain::where('order_id',$order_id)->get();
                $opt_count = count($current_blockchain_opt);
                $opt_status = 0;
                foreach ($current_blockchain_opt as $blockchain_opt) {
                    if($blockchain_opt->currency == $currency[0]) {
                        $client = $sell_client;
                    }elseif($blockchain_opt->currency == $currency[1]) {
                        $client = $buy_client;
                    }else {
                        DB::rollback();
                        return response()->json(['status'=>10002,'message' => 'Trade failed']);
                    }
                    $status = $client->move($blockchain_opt->user_id,$blockchain_opt->target_id,$blockchain_opt->amount);
                    if($status) {
                        $blockchain_opt->status = 1;
                        $blockchain_opt->save();
                        //--更新数据库余额
                        $client->_get_balance($blockchain_opt->user_id);
                        $client->_get_balance($blockchain_opt->target_id);
                    }
                }
                //    $this->dispatch(new CreateLastPrice($request->market,$market_name,$request->price));
                DB::commit();

                $buy_client ->_get_balance($api_user);
                $sell_client->_get_balance($api_user);

                return response()->json(['status'=>1,'message' => 'Trade success']);
            } elseif ($request->tradetype == 'sell') {
                $target_id = 1;
                BlockchainController::insertSellFirstOption($market_name,$api_user,$target_id,round($request->tradeamount,8),$order_id,1);
                //BlockchainController::insertSellFeeOption($market_name,\Auth::id(),$target_id,$fee,$order_id,1);
                //$fee = 0.0;// 后续逻辑处理了fee

                $result->sell($api_user,$order_id,$market_name,$market_id,$request->amount,$request->price,$request->amount,$fee,$fee_base);

                $current_blockchain_opt = Blockchain::where('order_id',$order_id)->get();
                $opt_count = count($current_blockchain_opt);
                $opt_status = 0;
                foreach ($current_blockchain_opt as $blockchain_opt) {
                    if($blockchain_opt->currency == $currency[0]) {
                        $client = $sell_client;
                    }elseif($blockchain_opt->currency == $currency[1]) {
                        $client = $buy_client;
                    }else {
                        DB::rollback();
                        return response()->json(['status'=>10003,'message' => 'Trade failed']);
                    }
                    $status = $client->move($blockchain_opt->user_id,$blockchain_opt->target_id,$blockchain_opt->amount);
                    if($status) {
                        $blockchain_opt->status = 1;
                        $blockchain_opt->save();
                        //--更新数据库余额
                        $client->_get_balance($blockchain_opt->user_id);
                        $client->_get_balance($blockchain_opt->target_id);
                    }
                }
                DB::commit();
                $buy_client ->_get_balance($api_user);
                $sell_client->_get_balance($api_user);
                return response()->json(['status'=>1,'message' => 'Trade success']);
            } else {
                DB::rollback();
                return response()->json(['status'=>10004,'message' => 'Trade failed ']);
            }
        } catch (\Exception $e) {
            DB::rollback();
            Log::info('jiaoyi_shuzihuobi=>'.$e->getMessage());
            return response()->json(['status'=>10005,'message' => 'Trade failed']);
        }
     }

    private function ouputData($data){
        if(Auth::id()==33){
            dump($data);
        }
    }
    //--市场id  一定要对应
    public  static function getMarkId($curr_abb){
        $curr_abb = strtoupper($curr_abb);
        switch ($curr_abb){
            case "BTC":
                return 1;
            case "BCH":
                return 2;
            case "LTC":
                return 3;
            case "RPZ":
                return 4;
            case "ETH":
                return 5;
            case "XVG":
                return 6;
            case "BTG"://btg
                return 7;
            case "DASH":
                return 8;
        }
        //--
        return -1;
    }

    //--发起撮合交易买
    private function buy($api_user,$order_id,$market_name,$market_id,$vol,$price,$original_vol,$fee=0,$fee_base){
        if(($vol <= 0) || ((($market_id == 4) || ($market_id == 5)) && ($vol < 0.01))) {return;}
        if(($market_id == 4) || ($market_id == 5)) {
            $find_flag = '>=';
            $find_rvolume = 0.01;
        }else{
            $find_flag = '>';
            $find_rvolume = 0;
        }

        $row = DB::table('xchange')->where([
                ['status',0],
                ['rvolume',$find_flag,$find_rvolume],
                ['market_id',$market_id],
                ['type',1],
                ['price','<=',$price]]
         );
        $min = $row->min('price');
        $row = $min ? $row->where('price',$min)->lockForUpdate()->first() : false;
        if($row){
            if(($vol-$row->rvolume) >= 0) {

                DB::table('xchange')
                    ->where(['id'=>$row->id])
                    ->update(['rvolume'=>0,'status'=>1,'updated_at'=>date('Y-m-d H:i:s',time())]);
                $idInsert = DB::table('xchange')->insertGetId(['type'=>2,'market_id'=>$market_id,'user_id'=>$api_user,'price'=>$row->price,'volume'=>$row->rvolume,'rvolume'=>0,'status'=>1,'fee'=>round(/*$row->price **/ $row->rvolume * $fee_base/*$fee*/,8),'created_at'=>date('Y-m-d H:i:s',time()),'updated_at'=>date('Y-m-d H:i:s',time()),'order_id'=>$order_id,'market_name'=>$market_name,'total_price'=>round($row->price*$row->rvolume,8)]);
                $user_id = 1;
                //--实际得到余额
                $shouru_amunt=bcmul($row->rvolume,bcsub(1,$fee_base,8),8);

                //改成和前端一样的算法   用总数乘以手续费  再用总数减去手续费 等到应该得的钱
              //  $shou_xue_fei=bcmul($row->rvolume,$fee_base,8);
              //  $shouru_amunt = bcsub($row->rvolume,$shou_xue_fei,8);


                //dump("实际得到的余额是: $shouru_amunt");
                //-
                BlockchainController::insertBuyExchangeOption($market_name,$user_id,$api_user,$shouru_amunt/*$row->rvolume*/,$order_id,2);

                $userCurr = new UserCurr();
                $feeCurrRate = $userCurr->getFeeRate($row->user_id,$market_name);
                $seller_receive = $this->doSellFee($row->rvolume,$row->price,$feeCurrRate);

                if($seller_receive) {
                    BlockchainController::insertSellOption($market_name,$user_id,$row->user_id,$seller_receive,$order_id,2);
                }

                $tuiqian=bcmul($row->rvolume,bcsub($price,$row->price,8),8);
                // $tuiqian = bcmul($tuiqian,bcadd(1,$feeCurrRate,8), 8); //--加上手续费
                if($tuiqian>=0.00000001){
                    BlockchainController::insertSellOption($market_name, $user_id, $api_user, $tuiqian, $order_id, 2);
                    // echo "我插入 退钱  ,user_id: $user_id ,tart_getID: ".\Auth::id()." ,tuiqian: $tuiqian ";
                }

                DB::table('xchange_info')
                    ->insert(['user_id'=>$api_user,'type'=>2,'market_id'=>$market_id,'market_name'=>$market_name,'fee'=>0,'last_price'=>$row->price,'volume'=>$row->rvolume,'created_at'=>time(),'updated_at'=>time()]);
                $totalVolBuy = $vol;
                $vol=bcsub($vol,$row->rvolume,8);
                //$vol = $vol - $row->rvolume;

                $logData = [
                    'buy_user' => $api_user,
                    'sell_user' => $row->user_id,
                    'buy_order' => $order_id,
                    'sell_order' => $row->order_id,
                    'trade_volume' => $row->rvolume,
                    'buy_volume' => $totalVolBuy,
                    'sell_volume' => $row->rvolume,
                    'buy_surplus' => $vol,
                    'sell_surplus' => 0.00000000,
                    'buy_receive' => $shouru_amunt,
                    'sell_receive' => $seller_receive,
                    'buy_fee' => bcsub($row->rvolume,$shouru_amunt,8),
                    'sell_fee' => bcsub(bcmul($row->rvolume,$row->price,8),$seller_receive,8),
                    'buy_rate' => $fee_base,
                    'sell_rate' => $feeCurrRate,
                    'market_name' => $market_name,
                    'relationship' => 10,
                    'price' => $row->price,
                    'sell_id' => $row->id,
                    'buy_id' => $idInsert,
                    'tui_qian'=> $tuiqian,
                ];
                XchangeDetail::create($logData);
                $check_order_controller=new CheckOrderController();
                $check_order_controller->check_order_list_dingshi("admin","fZGGz4BmSNbHzYr1cEYgfMEl0UOs59cn");
                $this->buy($api_user,$order_id,$market_name,$market_id,$vol,$price,$vol,$fee,$fee_base);
                $this->lastpriceJob($market_id,$market_name,$row->price);
            } else {

                //$rvolume = $row->rvolume - $vol;
                $rvolume=bcsub($row->rvolume,$vol,8);
                DB::table('xchange')
                    ->where(['id'=>$row->id])
                    ->update(['rvolume'=>$rvolume,'updated_at'=>date('Y-m-d H:i:s',time())]);
                //--手术费修改成当前币种的多少
                $idInsert = DB::table('xchange')->insertGetId(['type'=>2,'market_id'=>$market_id,'user_id'=>$api_user,'price'=>$row->price,'volume'=>$vol,'rvolume'=>0,'status'=>1,'fee'=>round(/*$row->price * */$vol * $fee_base/*$fee*/,8),'created_at'=>date('Y-m-d H:i:s',time()),'updated_at'=>date('Y-m-d H:i:s',time()),'order_id'=>$order_id,'market_name'=>$market_name,'total_price'=>round($row->price*$vol,8)]);
                DB::table('xchange_info')
                    ->insert(['user_id'=>$api_user,'type'=>2,'market_id'=>$market_id,'market_name'=>$market_name,'fee'=>0,'last_price'=>$row->price,'volume'=>$vol,'created_at'=>time(),'updated_at'=>time()]);

               // dump($fee);
               // dump(round(/*$row->price * */$vol * $fee_base/*$fee*/,8));
               // dump(/*$row->price * */$vol/*$fee*/);
              //  dump(/*$row->price * */$fee_base/*$fee*/);
                $user_id = 1;

                $shouru_amunt=bcmul($vol,bcsub(1,$fee_base,8),8);
                //dump("实际得到的余额是: $shouru_amunt");

                BlockchainController::insertBuyExchangeOption($market_name,$user_id,$api_user,$shouru_amunt/*$vol*/,$order_id,2);

                $userCurr = new UserCurr();
                $feeCurrRate = $userCurr->getFeeRate($row->user_id,$market_name);
                $seller_receive = $this->doSellFee($vol,$row->price,$feeCurrRate);
                if($seller_receive) {
                    BlockchainController::insertSellOption($market_name,$user_id,$row->user_id,$seller_receive,$order_id,2);
                }

                $tuiqian=bcmul($vol,bcsub($price,$row->price,8),8);
                // $tuiqian = bcmul($tuiqian,bcadd(1,$feeCurrRate,8), 8); //--加上手续费
                if($tuiqian>=0.00000001){
                    BlockchainController::insertSellOption($market_name, $user_id, $api_user, $tuiqian, $order_id, 2);
                    // echo "我插入 退钱  ,user_id: $user_id ,tart_getID: ".\Auth::id()." ,tuiqian: $tuiqian ";
                }

                $logData = [
                    'buy_user' => $api_user,
                    'sell_user' => $row->user_id,
                    'buy_order' => $order_id,
                    'sell_order' => $row->order_id,
                    'trade_volume' => $vol,
                    'buy_volume' => $vol,
                    'sell_volume' => $row->rvolume,
                    'buy_surplus' => 0.00000000,
                    'sell_surplus' => $rvolume,
                    'buy_receive' => $shouru_amunt,
                    'sell_receive' => $seller_receive,
                    'buy_fee' => bcsub($vol,$shouru_amunt,8),
                    'sell_fee' => bcsub(bcmul($vol,$row->price,8),$seller_receive,8),
                    'buy_rate' => $fee_base,
                    'sell_rate' => $feeCurrRate,
                    'market_name' => $market_name,
                    'relationship' => 10,
                    'price' => $row->price,
                    'sell_id' => $row->id,
                    'buy_id' => $idInsert,
                    'tui_qian'=> $tuiqian,
                ];
                XchangeDetail::create($logData);
                $check_order_controller=new CheckOrderController();
                $check_order_controller->check_order_list_dingshi("admin","fZGGz4BmSNbHzYr1cEYgfMEl0UOs59cn");
                $this->lastpriceJob($market_id,$market_name,$row->price);
            }
        } else {

            DB::table('xchange')->insert(['type'=>2,'market_id'=>$market_id,'user_id'=>$api_user,'price'=>$price,'volume'=>$vol,'rvolume'=>$original_vol,'status'=>0,'fee'=>$fee,'created_at'=>date('Y-m-d H:i:s',time()),'updated_at'=>date('Y-m-d H:i:s',time()),'order_id'=>$order_id,'market_name'=>$market_name,'total_price'=>round($price*$vol,8)]);
        }
    }

    //--最后成交价格
    private function lastpriceJob($market_id,$market_name,$price)
    {
        $this->dispatch(new CreateLastPrice($market_id,$market_name,$price));
    }

    private function doSellFee($vol,$price,$fee_base=0.005)
    {
        $temp=bcmul($vol,$price,8);
        $sell_fee=bcmul($temp,$fee_base,8);
       // $sell_fee = round($sell_fee,8);
        if($sell_fee < 0.00000001) {$sell_fee = 0.00000001;}
        $temp=bcmul($vol,$price,8);
        $seller_receive=bcsub($temp,$sell_fee,8);
       // $seller_receive = round($temp,8);
        if($seller_receive > 0.00000001) {
            return $seller_receive;
        }else{
            return 0;
        }
    }
    /*private function doSellFee($vol,$price,$fee_base=0.005)
    {
        $sell_fee = round($vol*$price*$fee_base,8);
        if($sell_fee < 0.00000001) {$sell_fee = 0.00000001;}
        $seller_receive = round($vol*$price-$sell_fee,8);
        if($seller_receive > 0.00000001) {
            return $seller_receive;
        }else{
            return 0;
        }
    }*/

    private function sell($api_user,$order_id,$market_name,$market_id,$vol,$price,$original_vol,$fee=0,$fee_base)
    {
        if(($vol <= 0) || ((($market_id == 4) || ($market_id == 5)) && ($vol < 0.01))) {return;}
        if(($market_id == 4) || ($market_id == 5)) {
            $find_flag = '>=';
            $find_rvolume = 0.01;
        }else{
            $find_flag = '>';
            $find_rvolume = 0;
        }

        $row = DB::table('xchange')->where([
                ['status','0'],
                ['rvolume',$find_flag,$find_rvolume],
                ['market_id',$market_id],
                ['type',2],
                ['price','>=',$price]]
        );
        $max = $row->max('price');
        $row = $max ? $row->where('price',$max)->lockForUpdate()->first() : false;
        if($row){
            if(($vol-$row->rvolume) >= 0) {

                DB::table('xchange')->where('id',$row->id)->update(['rvolume'=>0,'status'=>1,'updated_at'=>date('Y-m-d H:i:s',time())]);
                $idInsert = DB::table('xchange')->insertGetId(['type'=>1,'market_id'=>$market_id,'user_id'=>$api_user,'price'=>$row->price,'volume'=>$row->rvolume,'rvolume'=>0,'status'=>1,'fee'=>round($row->price * $row->rvolume *$fee_base/* $fee*/,8),'created_at'=>date('Y-m-d H:i:s',time()),'updated_at'=>date('Y-m-d H:i:s',time()),'order_id'=>$order_id,'market_name'=>$market_name,'total_price'=>round($row->price*$row->rvolume,8)]);
                DB::table('xchange_info')->insertGetId(['user_id'=>$api_user,'fee'=>0,'type'=>1,'market_id'=>$market_id,'market_name'=>$market_name,'last_price'=>$row->price,'volume'=>$row->rvolume,'created_at'=>time(),'updated_at'=>time()]);
                $this->lastpriceJob($market_id,$market_name,$row->price);
                $user_id = 1;

                $seller_receive = $this->doSellFee($row->rvolume,$row->price,$fee_base);
                if($seller_receive) {
                    BlockchainController::insertSellOption($market_name,$user_id,$api_user,$seller_receive,$order_id,1);
                }

                //--买家不应该收那么多钱
                //--买家的汇率
                $userCurr = new UserCurr();
                $feeCurrRate = $userCurr->getFeeRate($api_user,$market_name);
                $shouru=bcmul($row->rvolume,bcsub(1,$feeCurrRate,8),8);
                BlockchainController::insertSellOptionTrans($market_name,$user_id,$row->user_id,$shouru/*$row->rvolume*/,$order_id,1);
                $totalVolSell = $vol;
                //$vol = $vol - $row->rvolume;
                $vol=bcsub($vol,$row->rvolume,8);
                $logData = [
                    'buy_user' =>$row->user_id,
                    'sell_user' => $api_user,
                    'buy_order' => $row->order_id,
                    'sell_order' => $order_id,
                    'trade_volume' => $row->rvolume,
                    'buy_volume' => $row->rvolume,
                    'sell_volume' => $totalVolSell,
                    'buy_surplus' => 0.00000000,
                    'sell_surplus' => $vol,
                    'buy_receive' => $shouru,
                    'sell_receive' => $seller_receive,
                    'buy_fee' => bcsub($row->rvolume,$shouru,8),
                    'sell_fee' => bcsub(bcmul($row->rvolume,$row->price,8),$seller_receive,8),
                    'buy_rate' => $feeCurrRate,
                    'sell_rate' => $fee_base,
                    'market_name' => $market_name,
                    'relationship' => 20,
                    'price' => $row->price,
                    'sell_id' => $idInsert,
                    'buy_id' => $row->id,
                    'tui_qian'=> 0.00000000,
                ];
                XchangeDetail::create($logData);
                $check_order_controller=new CheckOrderController();
                $check_order_controller->check_order_list_dingshi("admin","fZGGz4BmSNbHzYr1cEYgfMEl0UOs59cn");
                $this->sell($api_user,$order_id,$market_name,$market_id,$vol,$price,$vol,$fee,$fee_base);
            } else {

                //$rvolume = $row->rvolume - $vol;
                $rvolume=bcsub($row->rvolume,$vol,8);
                DB::table('xchange')->where('id',$row->id)->update(['rvolume'=>$rvolume,'updated_at'=>date('Y-m-d H:i:s',time())]);
                $idInsert = DB::table('xchange')->insertGetId(['type'=>1,'market_id'=>$market_id,'user_id'=>$api_user,'price'=>$row->price,'volume'=>$vol,'rvolume'=>0,'status'=>1,'fee'=>round($row->price * $vol * $fee_base/*$fee*/,8),'created_at'=>date('Y-m-d H:i:s',time()),'updated_at'=>date('Y-m-d H:i:s',time()),'order_id'=>$order_id,'market_name'=>$market_name,'total_price'=>round($row->price*$vol,8)]);
                DB::table('xchange_info')->insert(['user_id'=>$api_user,'fee'=>0,'type'=>1,'market_id'=>$market_id,'market_name'=>$market_name,'last_price'=>$row->price,'volume'=>$vol,'created_at'=>time(),'updated_at'=>time()]);
                $user_id = 1;
                $seller_receive = $this->doSellFee($vol,$row->price,$fee_base);
                if($seller_receive) {
                    BlockchainController::insertSellOption($market_name,$user_id,$api_user,$seller_receive,$order_id,1);
                }

                //--买家不应该收那么多钱
                //--买家的汇率
                $userCurr = new UserCurr();
                $feeCurrRate = $userCurr->getFeeRate($api_user,$market_name);
                //dump("卖出基础手续费:$feeCurrRate");
                $shouru=bcmul($vol,bcsub(1,$feeCurrRate,8),8);

                BlockchainController::insertSellOptionTrans($market_name,$user_id,$row->user_id,$shouru/*$vol*/,$order_id,1);

                $logData = [
                    'buy_user' =>$row->user_id,
                    'sell_user' => $api_user,
                    'buy_order' => $row->order_id,
                    'sell_order' => $order_id,
                    'trade_volume' => $vol,
                    'buy_volume' => $row->rvolume,
                    'sell_volume' => $vol,
                    'buy_surplus' => $rvolume,
                    'sell_surplus' => 0.00000000,
                    'buy_receive' => $shouru,
                    'sell_receive' => $seller_receive,
                    'buy_fee' => bcsub($vol,$shouru,8),
                    'sell_fee' => bcsub(bcmul($vol,$row->price,8),$seller_receive,8),
                    'buy_rate' => $feeCurrRate,
                    'sell_rate' => $fee_base,
                    'market_name' => $market_name,
                    'relationship' => 20,
                    'price' => $row->price,
                    'sell_id' => $idInsert,
                    'buy_id' => $row->id,
                    'tui_qian'=> 0.00000000,
                ];
                XchangeDetail::create($logData);
                $check_order_controller=new CheckOrderController();
                $check_order_controller->check_order_list_dingshi("admin","fZGGz4BmSNbHzYr1cEYgfMEl0UOs59cn");
                $this->lastpriceJob($market_id,$market_name,$row->price);
            }
        } else {

            DB::table('xchange')->insert(['type'=>1,'market_id'=>$market_id,'user_id'=>$api_user,'price'=>$price,'volume'=>$vol,'rvolume'=>$original_vol,'status'=>0,'fee'=>$fee,'created_at'=>date('Y-m-d H:i:s',time()),'updated_at'=>date('Y-m-d H:i:s',time()),'order_id'=>$order_id,'market_name'=>$market_name,'total_price'=>round($price*$vol,8)]);
        }
    }




    /** 获取交易市场挂单 一个市场的未完成订单 自己的未完成挂单
     * @param Request $request
     * @return array
     */
    public function getOpenOrders(Request $request)
    {
        $market_name  =$request->tradeCurr."_".$request->currency;
        $where = ['market_name'=>$market_name,'status'=>0,'user_id'=>Auth::id(),['rvolume','>',0]];//'market_id'=>$market_id
        $open_order = DB::table('xchange')
            //->leftjoin('market as m','market_id','=','m.id')
            //->select('xchange.*','m.market_name')
            ->where($where)
            ->paginate(10);

        $data = [];
        foreach ($open_order as $key => $value){
            $data['data'][$key]['value'] = $value->total_price;
            $data['data'][$key]['id'] = $value->order_id;
            $data['data'][$key]['net_volume'] = $value->volume;
            $data['data'][$key]['residual_num'] = $value->rvolume;
            $data['data'][$key]['price_btc'] = $value->price;
            $data['data'][$key]['trade_type'] = $value->type==1?20:10;
            $data['data'][$key]['operation'] = __('ac.Cancel');
            $data['data'][$key]['add_time'] = strtotime($value->created_at);
            $data['data'][$key]['trade']= $value->type ==1?__('ac.Sell'):__('ac.Buy');
        }
        $data['total'] = $open_order->total();
        $data['last_page']=$open_order->lastPage();
        $data['current_page']=$open_order->currentPage();
        $data['per_page']=$open_order->perPage();
        return response()->json(['status'=>1,'message' => 'successful','data'=>$data]);
    }


    /**
     * @未完成的挂单
     * 一个市场的所有人未完成的挂单
     * @param market_id , type (买或卖)
     * @return
     */
    public function unCompeleteOrder(Request $request)
    {


        //"price_btc":"1.00000000",
        //"residual_num":"0.20000000",
          //      "value":"0.20000000

     //   $this->validate($request,['market_id'=>'required']);
        $request->market_id=$this->getMarkId($request->curr_abb);
        //dump($request->market_id);

        if(($request->market_id == 4) ||($request->market_id == 5)) {
            $where = [['market_id',$request->market_id],['status',0],['rvolume','>=',0.01]];  //$request->market_id
        }else {
            $where = ['market_id'=>$request->market_id,'status'=>0];  //$request->market_id
        }
        $order = Xchange::where($where)->where("rvolume",">",0)->select('type', 'rvolume', 'price','total_price')->orderBy('price','desc')->get();

        $type = 1;
        if ($request->tradeType==10) $type = 2;

        $total = Xchange::where([
            ['market_id','=',$request->market_id],
            ['status',"=",0],
            ['type','=',$type],
            ['rvolume','>',0],
        ])->select(DB::raw('sum(TRUNCATE(rvolume*price,8)) as total'))->value('total');



        if (count($order)) {
            $order = $order->groupBy('type')->sortBy('price')->toArray();
            if (!empty($order[2])) {
                $order['buy'] = $order[2];
                unset($order[2]);
                $collection = collect($order['buy']);
                //取十五条
                $group_by_buy = $collection->groupBy('price')->take(15)->toArray();
                $i = 0;
                $total_vol = 0;
                foreach ($group_by_buy as $key=>$value) {
                    ++$i;
                    $sum_price = 0;
                    foreach ($value as $kk=>$vv) {
                        $sum_price += $vv['rvolume'];
                        $arr['buy'][$i]['rvolume'] = number_format($sum_price,8,'.','');
                        $arr['buy'][$i]['price'] = $key;
                        $arr['buy'][$i]['type'] = 2;
                        //--add
                        $total_price=bcmul($sum_price,$key,8);
                        $arr['buy'][$i]['total_price']=$total_price;

                        if($i == 1) {
                            $arr['buy'][$i]['total'] = number_format($sum_price * $key,8,'.','');
                        }else{
                            $arr['buy'][$i]['total'] = number_format($arr['buy'][$i-1]['total'] + $sum_price * $key,8,'.','');
                        }
                        $total_vol += number_format($vv['rvolume'] * $vv['price'],8,'.','');
                    }
                }
                $arr['buy_total_vol'] = number_format($total_vol,8,'.','');
            }
            if (!empty($order[1])) {
                $order['sell'] = $order[1];
                unset($order[1]);
                $collection = collect($order['sell']);
                $group_by_sell = array_reverse($collection->groupBy('price')->toArray());
                //十五条
                $group_by_sell = array_slice($group_by_sell,0,15);
                $i = 0;
                $total_vol = 0;
                foreach ($group_by_sell as $key=>$value) {
                    ++$i;
                    $sum_price = 0;
                    foreach ($value as $kk=>$vv) {
                        $sum_price += $vv['rvolume'];
                        $arr['sell'][$i]['rvolume'] = number_format($sum_price,8,'.','');
                        $arr['sell'][$i]['price'] = $key;
                        $arr['sell'][$i]['type'] = 1;
                        //add
                        $total_price=bcmul($sum_price,$key,8);
                        $arr['sell'][$i]['total_price']=$total_price;

                        if($i == 1) {
                            $arr['sell'][$i]['total'] = number_format($sum_price * $key,8,'.','');
                        }else{
                            $arr['sell'][$i]['total'] = number_format($arr['sell'][$i-1]['total']+$sum_price * $key,8,'.','');
                        }
                        $total_vol += $vv['total_price'];
                    }
                }
                $arr['sell_total_vol'] = number_format($total_vol,8,'.','');
            }
            //TODO 封装上面的方法
        } else {
            $arr = [];
        }



        $data['current_page']=1;
        $data['total']=1;
        $data['lastPage']=1;
        $data['last_page']=1;


        if($request->tradeType==10){
            if(empty($arr['buy_total_vol'])){
                $data['totalNum']='0.000000';
                $data['totalOne']='0.000000';
               // $arr['buy_total_vol']='0.000000';
                return response()->json(['status'=>1,'message' => 'successful','data'=>$data]);
            }
            $data['totalNum']=$arr['buy_total_vol'];
            $data['totalOne']=$total;
            $data['data']=array_values($arr['buy']);
            foreach ($data['data'] as $key =>$value){
                $data['data'][$key]['price_btc']=$value['price'];
                $data['data'][$key]['residual_num']=$value['rvolume'];
                $data['data'][$key]['value']=$value['total_price'];
            }
        }else {
            if(empty($arr['sell_total_vol'])){
                $data['totalNum']='0.000000';
                $data['totalOne']='0.000000';
                //$arr['buy_total_vol']='0.000000';
                return response()->json(['status'=>1,'message' => 'successful','data'=>$data]);
            }
            $data['totalNum']=$arr['sell_total_vol'];
            $data['data']=array_values($arr['sell']);
            $data['totalOne']=$total;
            foreach ($data['data'] as $key =>$value){
                $data['data'][$key]['price_btc']=$value['price'];
                $data['data'][$key]['residual_num']=$value['rvolume'];
                $data['data'][$key]['value']=$value['total_price'];
            }
        }
        return response()->json(['status'=>1,'message' => 'successful','data'=>$data]);
    }




    /**
     * [getHistory description] market页面获取成交的历史记录
     * @param  Request $request [description] market_id
     * @return [type]           [description] markethistory
     */
    public function getHistory(Request $request)
    {
        $market_name=$request->curr_abb."_".$request->currency;
        $data = XchangInfo::where('market_name',$market_name)//$request->market_id
        ->select('created_at','volume','last_price','type')
            ->orderBy('created_at','desc')
            ->paginate(10);

        if(!empty($data)) {
            $market_array[0] =$request->curr_abb;
            $market_array[1] =$request->currency;
            foreach ($data as $key=>$value) {
                $another_volume              = round($value->last_price * $value->volume,8);
                $data[$key]->another_volume  = $another_volume;
                $data[$key]->value_btc       = $another_volume;
            }
        }

        foreach ($data as $key=>$value){
            $data_data['data'][$key]['price_btc']=$value['last_price'];
            $data_data['data'][$key]['initial_mun']= number_format($value['volume'],8,".","");
            $data_data['data'][$key]['value']=number_format($value['another_volume'],8,".","");
            $data_data['data'][$key]['add_time']=strtotime($value['created_at']);
            $data_data['data'][$key]['trade_type']=$value['type']==1?20:10;
            $data_data['data'][$key]['trade']=$value['type']==1?__('ac.Sell'):__('ac.Buy');
        }

        $data_data['total'] = $data->total();
        $data_data['last_page']=$data->lastPage();
        $data_data['current_page']=$data->currentPage();
        $data_data['per_page']=$data->perPage();
        $data_data['currAbb']=$request->curr_abb;

        return response()->json(['status'=>1,'message' => 'successful','data'=>$data_data]);
    }
}