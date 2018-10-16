<?php
/**
 * Created by PhpStorm.
 * User: ZRothschild
 * Date: 2018/1/12
 * Time: 17:44
 */

namespace App\Http\Controllers\FrontEnd;


use App\CurrencySet;
use App\Http\Controllers\Controller;
use App\Library\Currency\AccountAmount;
use App\Order;
use App\OrderDetail;
use App\UserCurr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    /**
     *订单中心
     */
    public function index()
    {
       // $openOrdersField = ['add_time','price_btc','net_volume','volume_btc','residual_volume','curr_abb','operation','trade_type'];
       //// $openOrders = ////::where([
       ////     ['user_id','=',Auth::id()],
       ////     ['operation','=',10],
       ////     ['order_status','<>',30],
       //// ])->orderByDesc('add_time')->get($openOrdersField);

     //  $orderHistoryField = ['fee_money','price_btc','last_time','initial_volume','curr_abb','trade_type'];
     //  $orderHistory = OrderDetail::where([
     //      ['user_id','=',Auth::id()],
     //      ['order_status','=',30],
     //  ])->orderByDesc('last_time')->get($orderHistoryField);

       return view('front.order'/*,['openOrders'=>$openOrders,'orderHistory'=>$orderHistory]*/);
    }

    /**
     * order 页面  openOrder
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
   // public function openOrder(Request $request)
   // {
   //     $openOrdersField = ['add_time','price_btc','net_volume','volume_btc','residual_volume','curr_abb','operation','trade_type','id'];
//
   //     Log::info('openOrderopenOrderopenOrderopenOrderopenOrder1234');
   //     Log::info($request);
//
   //     $openOrders = OrderDetail::where([
   //         ['user_id','=',Auth::id()],
   //         ['operation','=',10],
   //         ['order_status','<>',30],
   //         ['curr_abb','=',$request->currAbb],
   //     ])->orderByDesc('add_time')->paginate(10,$openOrdersField)->toArray();
//
   //     foreach ($openOrders['data'] as $key => $value){
   //         $openOrders['data'][$key]['img'] = asset('images/'.$value['curr_abb'].'.png');
   //         $openOrders['data'][$key]['add_time'] = date("d-m-Y H:i:s",$value['add_time']);
   //     }
////
   //     Log::info($openOrders);
//
   //     return response()->json(['status'=>1,'message' =>'successful','data'=>$openOrders]);
   // }

    /**
     * order 页面  orderHistory
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
  //// public function orderHistory(Request $request)
  //// {
  ////     $orderHistory////Field = ['fee_money','price_btc','last_time','initial_volume','curr_abb','trade_type','operation'];

  ////     Log::info('orderHistoryorderHistoryorderHistoryorderHistory');
  ////     Log::info($request);

  ////     $orderHistory = OrderDetail::where([
  ////         ['user_id','=',Auth::id()],
  ////         ['order_status','=',30],
  ////         ['curr_abb','=',$request->currAbb],
  ////     ])->orwhere([
  ////         ['operation','=',20],
  ////         ['curr_abb','=',$request->currAbb],
  ////         ['user_id','=',Auth::id()],
  ////     ])->orderByDesc('last_time')->paginate(10,$orderHistoryField)->toArray();
  ////     Log::info($orderHistory);

  ////     foreach ($orderHistory['data'] as $key => $value){
  ////         $orderHistory['data'][$key]['img'] = asset('images/'.$value['curr_abb'].'.png');
  ////         $orderHistory['data'][$key]['last_time'] = date("d-m-Y H:i:s",$value['last_time']);
  ////     }
//////       Log::info($orderHistory);
  ////     return response()->json(['status'=>1,'message' =>'successful','data'=>$orderHistory]);
  //// }

    /**
     * order 页面     Open orders 取消 订单
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
  //  public function  cancelOrder(Request $request)
  //  {
//
  //      $checkPin = Hash::check($request->pin,Auth::user()->pin);
//
  //      if (!$checkPin) return response()->json(['status'=>0,'message' =>'pin error']);
//
  //      $detail = OrderDetail::where([
  //          ['id','=',$request->id],
  //          ['order_status','<>',30],
  //          ['operation','=',10],
  //      ])->first(['order_id','rate','curr_id','curr_abb','user_id','price_btc','residual_num','trade_type','residual_volume','end_amount','end_btc']);
//
//
  //      if (empty($detail)) response()->json(['status'=>0,'message' =>'fail']);
//
  //      Log::info('cancelOrdercancelOrdercancelOrdercancelOrdercancelOrdercancelOrdercancelOrder');
  //      Log::info($detail);
  //      Log::info($request);
  //      $order = Order::where([
  //          ['id','=',$detail['order_id']],
  //      ])->first(['residual_num','total_volume','total_volume_btc','residual_volume']);
//
  //      $accountAmount = new AccountAmount();
//
  //      if ( 10 ==$detail['trade_type']){
  //          //返回比特币本金
  //          $reMoney = bcmul($detail['residual_num'],$detail['price_btc'],8);
  //          //返回手续费
  //          $reRate = bcmul($reMoney,$detail['rate'],8);
//
  //          $res = bcadd($reMoney,$reRate,8);
//
  //          $balanceMoney = $accountAmount->getBalance($detail['curr_abb'],$detail['user_id']);
//
  //          $detailData = [
  //              'end_btc' =>bcadd($balanceMoney,$res,8),
  //              'residual_num' => 0,
  //              'residual_volume' => 0,
  //              'operation' => 20,
  //          ];
//
  //          $residualVolume = bcsub($order['residual_volume'],$detail['residual_volume'],8);
//
  //          $orderData = [
  //              'residual_num' => bcsub($order['residual_num'],$detail['residual_num'],8),
  //              'total_volume' => bcsub($order['total_volume'],$detail['residual_num'],8),
  //              'total_volume_btc' => bcsub($order['total_volume_btc'],$detail['residual_volume'],8),
  //              'residual_volume' => $residualVolume,
  //          ];
//
  //          if (bccomp($residualVolume,0,8) != 1) $orderData['order_status'] = 30;
//
  //      }else{
  //          $res = $detail['residual_num'];
  //          $balanceMoney = $accountAmount->getBalance($detail['curr_abb'],$detail['user_id']);
//
  //          $detailData = [
  //              'end_amount' =>bcadd($balanceMoney,$res,8),
  //              'residual_num' => 0,
  //              'residual_volume' => 0,
  //              'operation' => 20,
  //          ];
//
  //          $residualVolume = bcsub($order['residual_volume'],$detail['residual_volume'],8);
//
  //          $orderData = [
  //              'residual_num' => bcsub($order['residual_num'],$detail['residual_num'],8),
  //              'total_volume' => bcsub($order['total_volume'],$detail['residual_num'],8),
  //              'total_volume_btc' => bcsub($order['total_volume_btc'],$detail['residual_volume'],8),
  //              'residual_volume' => $residualVolume,
  //          ];
  //      }
  //      DB::beginTransaction();
  //      try{
  //          $orderDetail = OrderDetail::where('id',$request->id)->update($detailData);
  //          $order = Order::where('id',$order['id'])->update($orderData);
//
  //          Log::info($detailData);
  //          Log::info($orderDetail);
//
  //          Log::info($orderData);
  //          Log::info($order);
  //          Log::info($detail);
//
  //          Log::info('cancelOrdercancelOrdercancelOrdercancelOrdercancelOrdercancelOrdercancelOrder');
  //          DB::commit();
  //          if ($detail['trade_type'] == 10){
  //              //返回比特币本金
  //              $reMoney = bcmul($detail['residual_num'],$detail['price_btc'],8);
  //              //返回手续费
  //              $reRate = bcmul($reMoney,$detail['rate'],8);
  //              $res = bcadd($reMoney,$reRate,8);
  //              $accountAmount->move($detail['curr_abb'],1,$detail['user_id'],$res);
  //          }else{
  //              $accountAmount->move($detail['curr_abb'],1,$detail['user_id'],$detail['residual_num']);
  //          }
  //          return response()->json(['status'=>1,'message' =>'successful']);
  //      } catch (\Exception $exception){
  //          DB::rollBack();
  //          return response()->json(['status'=>0,'message' =>'Errors in the system']);
  //      }
  //  }

    /**货币手续费 比例接口
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getFeeRate(Request $request)
    {
       // $feeRate = UserCurr::where([
       //     ['user_id','=',Auth::id()],
       //     ['switch','=',10],
       //     ['curr_abb','=',$request->currAbb]
       // ])->value('fee_rate');
//
        $currency="btc";
        if($request->currency){
            $currency=$request->currency;
        }
        $feeRate=UserCurr::getFeeRate(Auth::id(),$request->currAbb."_".$currency);
        $tpm = $feeRate*100;
        return response()->json(['status'=>0,'message' =>'successful','data'=>['feeRate'=>$tpm.'%']]);
    }

}