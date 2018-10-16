<?php
/**
 * Created by PhpStorm.
 * User: ZRothschild
 * Date: 2018/1/12
 * Time: 17:44
 */

namespace App\Http\Controllers\FrontEnd;


use App\Handlers\Client;
use App\Http\Controllers\FrontEnd\BlockchainController;
use App\Http\Controllers\Controller;

use App\Models\CoinMarketCapTicket;
use App\Models\Market;
use App\Models\Xchange;


use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ACOrderController extends Controller
{

//--

    //order界面的我未完成的所有挂单
    public function openOrder(Request $request)
    {
        $where = [['status','=',0],['user_id','=',Auth::id()],['rvolume','>',0]];
        if($request->currAbb==$request->currency){
            $where[] = ['market_name','like',"%_{$request->currency}"];
        }else{
            if ($request->has('currAbb')){
                if (strtolower($request->currAbb) === 'all'){
                    $where[] = ['market_name','like',"%_{$request->currency}"];
                }else{
                    $where[] = ['market_name','=',$request->currAbb.'_'.$request->currency];
                }
            }else{
                $where[] = ['market_name','like',"%_{$request->currency}"];
            }
        }
        $open_order = DB::table('xchange')
            ->where($where)
            ->paginate(12);

        $data = [];
        foreach ($open_order as $key => $value){
            $data['data'][$key]['value'] = $value->total_price;
            $data['data'][$key]['id'] = $value->order_id;
            $data['data'][$key]['net_volume'] = $value->volume;
            $data['data'][$key]['residual_num'] = $value->rvolume;
            $data['data'][$key]['price_btc'] = $value->price;
            $data['data'][$key]['trade_type'] = $value->type==1?20:10;
            $data['data'][$key]['trade']= $value->type ==1?__('ac.Sell'):__('ac.Buy');
            $data['data'][$key]['operation'] = __('ac.Cancel');
            $data['data'][$key]['add_time'] = strtotime($value->created_at);

            $mark_name_temp=explode("_",$value->market_name);
            $data['data'][$key]['curr_abb'] = strtoupper($mark_name_temp[0]);
            $data['data'][$key]['img'] = asset('images/'.$mark_name_temp[0].'.png');
        }
        $data['total'] = $open_order->total();
        $data['last_page']=$open_order->lastPage();
        $data['current_page']=$open_order->currentPage();
        $data['per_page']=$open_order->perPage();
        return response()->json(['status'=>1,'message' => 'successful','data'=>$data]);
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function history(Request $request)
    {
        //TODO select
        $where = [['user_id','=',Auth::id()]]; //有一个下划线 //,['status','<>',0]
        if($request->currAbb==$request->currency){
            $where[] = ['market_name','like',"%_{$request->currency}"];
        }else{
            if ($request->has('currAbb')) {
                if (strtolower($request->currAbb) === 'all'){
                    $where[] = ['market_name','like',"%_{$request->currency}"];
                }else{
                    $where[] = ['market_name','=',$request->currAbb.'_'.$request->currency];
                }
            }else{
                $where[] = ['market_name','like',"%_{$request->currency}"];
            }
        }

        $data = DB::table('xchange')
            ->where($where)
            ->select('created_at','market_name','type','price','volume','rvolume','fee','status')
            ->orderBy('updated_at','desc')
            ->paginate(12);

        foreach ($data as $key=>$value){
            $data_data['data'][$key]['price_btc']=$value->price;
            $data_data['data'][$key]['initial_volume']=$value->volume;

            $another_volume              = round($value->price * $value->volume,8);
            $data_data['data'][$key]['value']=$another_volume;
            $data_data['data'][$key]['last_time']=strtotime($value->created_at);
            $data_data['data'][$key]['trade_type']=$value->type==1?20:10;
            $data_data['data'][$key]['trade']= $value->type ==1?__('ac.Sell'):__('ac.Buy');
            $mark_name_temp=explode("_",$value->market_name);
            $data_data['data'][$key]['curr_abb'] = strtoupper($mark_name_temp[0]);//
            $data_data['data'][$key]['img'] = asset('images/'.$mark_name_temp[0].'.png');
            $data_data['data'][$key]['fee_money']=$value->fee;
            $data_data['data'][$key]['operation']=$value->status;
            $data_data['data'][$key]['residual_num'] = $value->rvolume;

            if ($value->type ==1){
                $data_data['data'][$key]['curre'] =  'BTC';
            }else{
                $mark_name_temp=explode("_",$value->market_name);
                $data_data['data'][$key]['curre'] =  strtoupper($mark_name_temp[0]);//
            }

            if($value->status ==1){
                $data_data['data'][$key]['operation'] = __('ac.Completed');
            } elseif($value->status ==3){
                $data_data['data'][$key]['operation'] = __('ac.Canceled');
            }else {
                if($value->volume == $value->rvolume ){
                    $data_data['data'][$key]['operation'] = __('ac.Unfinished');
                }else{
                    $data_data['data'][$key]['operation'] = __('ac.PartiallyCompleted');
                }
            }
        }
        $data_data['total'] = $data->total();
        $data_data['last_page']=$data->lastPage();
        $data_data['current_page']=$data->currentPage();
        $data_data['per_page']=$data->perPage();
        $data_data['currAbb']=$request->curr_abb;
        return response()->json(['status'=>1,'message' => 'successful','data'=>$data_data]);
    }



    //TODO 功能没有完成
    /** 取消订单
     * @param Request $request
     * @return array
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function cancel(Request $request)
    {
      //  dump($request);

//        $checkPin = Hash::check($request->pin,Auth::user()->pin);
        $request->orderid=$request->id;

        $request->request->set("orderid",$request->orderid);
        $request->request->set("id",Auth::id());
//        if (!$checkPin) return response()->json(['status'=>0,'message' =>__('ac.pinNoExists')]);
            $xchange = Xchange::where([
                ['order_id','=',$request->orderid],
                ['user_id','=',$request->id]
            ])->where('status','0')->first();
            if(count($xchange)==0) {
                return response()->json(['status'=>0,'message' =>__('ac.cancelFail')]);
            }
            //取消订单
            $request->request->set("opear","cancelOrder");


           return self::globle_cancel($request);


     //       TaskController::push($request->request->all());
     //   $message = __('ac.cancelSuccess');
     //   return response()->json(['status'=>1,'message' =>$message]);
        }




    public static function globle_cancel($request){
        DB::beginTransaction();
        $xchange = Xchange::where([
            ['order_id','=',$request->orderid],
            ['user_id','=',$request->id]
        ])->where('status','0')->lockForUpdate()->first();
        if(count($xchange)==0) {
            return response()->json(['status'=>0,'message' =>__('ac.cancelFail')]);
        }

        $market = $xchange->market_name;
        $market_array = explode('_',$market);
        $user_id=$request->id;//Auth::id(); //--用户id

        if($xchange->type == 1) {
            $sell_client = self::getStaticClient($market_array[0]);
            $buy_client = self::getStaticClient($market_array[1]);
            try{
                $blockchain_opt = BlockchainController::InsertCancelOrder($xchange->order_id,$market_array[0],1,$user_id,$xchange->rvolume,0,1,3);
                /*$back_fee = round($xchange->rvolume * $xchange->price * 0.005,8);
                $blockchain_fee_opt = BlockchainController::InsertCancelOrder($xchange->order_id,$market_array[1],'NovaCoinMain',$user_id,$back_fee,0,0,4);*/
                $blockchain_opt_result = $sell_client->move(1,$user_id,$xchange->rvolume);
                // $blockchain_fee_opt_result = $buy_client->move('NovaCoinMain',$user_id,$back_fee);
                if($blockchain_opt_result ) { //TODO 具体判断是否成功
                    $blockchain_opt->status = 1;
                    // $blockchain_fee_opt->status = 1;
                    $blockchain_opt->save();
                    // $blockchain_fee_opt->save();
                    $xchange->status = 3;
                    $xchange->save();
                    DB::commit();

                    //--更新数据库余额
                    $sell_client->_get_balance($user_id);
                    $code = 1;
                    $message = __('ac.cancelSuccess');
                }else{
                    DB::rollback();
                    $code = 0;
                    $message =__('ac.cancelFail');
                }
            }catch (\Exception $e) {
                DB::rollback();
                $code = 0;
                $message = __('ac.cancelFail');
            }
        }elseif($xchange->type == 2) {
            $client = self::getStaticClient($market_array[1]);
            try{
                $move_amount = round(($xchange->rvolume * $xchange->price),8);
                $blockchain_opt = BlockchainController::InsertCancelOrder($xchange->order_id,$market_array[1],1,$user_id,$move_amount,0,2,3);
                $blockchain_opt_result = $client->move(1,$user_id,$move_amount);
                if($blockchain_opt_result) {
                    $blockchain_opt->status = 1;
                    $blockchain_opt->save();
                    $xchange->status = 3;
                    $xchange->save();
                    DB::commit();
                    $code = 1;
                    $message = __('ac.cancelSuccess');

                    //--更新数据库余额
                    $client->_get_balance($user_id);

                }else{
                    DB::rollback();
                    $code = 0;
                    $message = __('ac.cancelFail');
                }
            }catch (Exception $e) {
                DB::rollback();
                $code = 0;
                $message = __('ac.cancelFail');
            }
        }else{
            DB::rollback();
            $code = 0;
            $message = __('ac.cancelFail');
        }
        //--更新餘額
        //--更新数据库余额
        // $client->_get_balance($blockchain_opt->user_id);
        //  $client->_get_balance($blockchain_opt->target_id);
        return response()->json(['status'=>$code,'message' =>$message]);
    }

}