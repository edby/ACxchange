<?php
/**
 * Created by PhpStorm.
 * User: YAO
 * Date: 2018/7/6
 * 检测是否有买价大于卖价
 * 如果有就取消掉低的卖单
 * 重新下单
 * Time: 17:25
 */
namespace App\Http\Controllers\FrontEnd;

use App\Http\Controllers\Controller;
use App\UserCurr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckOrderController extends  Controller
{

    //检查市场所有订单
    public function check_order_list(Request $request){
        $user=$request->user;
        $passWord=$request->passWord;
        if($user=="admin" && $passWord=="fZGGz4BmSNbHzYr1cEYgfMEl0UOs59cn"){
            set_time_limit(0);
            $market_isd=[2,3,4,5,6,7,8];
            foreach ($market_isd as $market_id){
//                echo "开始检测 市场id ".$market_id."</br>";
                $request->request->set("market_id",$market_id);
                $this->check_order($request);
            }
        }else{
            abort(404);
        }
    }

    public function check_order_list_dingshi($user,$passWord){
        if($user=="admin" && $passWord=="fZGGz4BmSNbHzYr1cEYgfMEl0UOs59cn"){
            $request_static=Request::capture();
            set_time_limit(0);
            $market_isd=[2,3,4,5,6,7,8];
            foreach ($market_isd as $market_id){
//                echo "开始检测 市场id ".$market_id."</br>";
                $request_static->request->set("market_id",$market_id);
                $this->check_order($request_static);
            }
        }else{
//            echo "404</br>";
        }
        return "finish";
    }


    //检查订单是不是有卖价 小于 买价 的
    private function check_order(Request $request){
        $market_id=$request->market_id;
        $min_sell_price_result=DB::select("SELECT price FROM `xchange` WHERE `market_id` = ".$market_id." AND `status` = '0' AND `type` = '1' and `rvolume`>0  ORDER BY price limit 1");
        $max_buy_price_result=DB::select("SELECT price FROM `xchange` WHERE `market_id` =  ".$market_id." AND `status` = '0' AND `type` = '2'  and `rvolume`>0 ORDER BY  price desc limit 1");
        if(!is_null($min_sell_price_result) && count($min_sell_price_result)>0){
            $min_sell_price=$min_sell_price_result[0]->price;
        }else{
//            echo "卖单数量不足，退出当前id 检测</br>";
//            is_null($request);
//            is_null($min_sell_price_result);
            return;
        }
        if(!is_null($max_buy_price_result) && count($max_buy_price_result)>0){
            $max_buy_price=$max_buy_price_result[0]->price;
        }else{
            is_null($request);
            is_null($max_buy_price_result);
//            echo "买单数量不足，退出当前id 检测</br>";
            return;
        }
//        echo ("最小卖价:".$min_sell_price."</br>");
//        echo ("最大买价:".$max_buy_price."</br>");
        $bijiao_result=bccomp($max_buy_price,$min_sell_price,8);
        if($bijiao_result!=-1){
//            echo( "买价大于卖价</br>");
            //获取所有小于买价的订单所有卖单,我查到的订单，不允许有人交易，锁住
            DB::beginTransaction();
            $sell_price_small_result=DB::table("xchange")->where([["market_id","=",$market_id],["status","=",0],["type","=",1],["price","<=",$max_buy_price]])->orderBy('price', 'ASC')->lockForUpdate()->get();
            $cancel_result=[];
            foreach ($sell_price_small_result as $key=>$result){
                //--取消订单
                try{
//                    echo("用户: ".$result->user_id."取消订单: ".$result->order_id."</br>");
//                    echo($this->jiaoyi_cancel_order($request,$result->user_id,$result->order_id)."</br>");
                    $this->jiaoyi_cancel_order($request,$result->user_id,$result->order_id);
                    $cancel_result[$key]=true;
                }catch (\Exception $e){
//                    echo "取消订单失败</br>";
                    $cancel_result[$key]=false;
                }
            }
            DB::commit();
            //if(1==1)die();#暂时不重新下单
            foreach ($sell_price_small_result as $key=>$result){
                //-重新交易
                try{
                    if($cancel_result[$key]){
//                        echo("用户: ".$result->user_id."重新下单: 市场:".$result->market_name." 数量:".$result->rvolume." 价格:".$result->price."</br>");
                        if($result->user_id != "131"){
                            echo($this->jiaoyi_sell($request,$result->user_id,$result->market_name,$result->rvolume,$result->price)."</br>");
                        }else{
//                            echo "是机器人，不需要重新下单</br>";
                        }
                    }else{
//                        echo "订单没有取消成功，不允许重新下单</br>";
                    }
                }catch (\Exception $e){
//                    echo "重新交易失败</br>";
                }
            }
            is_null($sell_price_small_result);
        }else{
//            echo "价格正常,无需操作</br>";
        }
        is_null($request);
        is_null($max_buy_price_result);
        is_null($min_sell_price_result);


    }


    private function jiaoyi_sell(Request $request,$user_id,$market_name,$amount,$price){
        //--卖单从最低价格开始卖
        $request_copy=$request;
        $request_copy->request->set("id",$user_id);
        $request_copy->request->set("opear","trade");
        $request_copy->request->set("amount",$amount);
        $request_copy->request->set("price",$price);
        //--获取费率
        $userCurr = new UserCurr();
        $fee_base = $userCurr->getFeeRate($user_id,$market_name);
        $feeRate=($fee_base*100)."%";
        $currency = explode('_',$market_name);
        $request_copy->request->set("feeRate",$feeRate);
        //-计量币种
        $request_copy->request->set("currency",$currency[1]);
        //--交易币种
        $request_copy->request->set("tradeCurr",$currency[0]);
        $total=bcmul($amount,$price,8);
        //成交的总价
        $request_copy->request->set("total",$total);
        $request_copy->request->set("type",20);
        //收入
        $fee_shouru=bcsub(1,$fee_base,9);
        $shouru = bcmul($total,$fee_shouru,8);
        $request_copy->request->set("netTotal",$shouru);
        //手续费
        $fee=bcmul($total,$fee_base,8);
        $request_copy->request->set("fee",$fee);
        //dump($request_copy);
        $result=ACXWalletController::jiaoyi_shuzihuobi($request_copy);
        is_null($request_copy);
        return $result;
    }

    //--取消订单
    private function jiaoyi_cancel_order(Request $request,$user_id,$order_id){
        $request_copy=$request;
        $request_copy->request->set("orderid",$order_id);
        $request_copy->request->set("id",$user_id);
        $request_copy->request->set("opear","cancelOrder");
        //dump($request_copy);
        $result=ACOrderController::globle_cancel($request_copy);
        is_null($request_copy);
        return $result;
    }
}