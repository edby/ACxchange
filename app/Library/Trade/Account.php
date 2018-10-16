<?php
/**
 * Created by PhpStorm.
 * User: ZRothschild
 * Date: 2018/2/2
 * Time: 11:01
 */

namespace App\Library\Trade;


use App\CurrencySet;
use App\Http\Controllers\Controller;
use App\Http\Controllers\FrontEnd\ACXWalletController;
use App\Library\Currency\AccountAmount;
use App\Models\Market;
use App\Models\Xchange;
use App\Order;
use App\OrderDetail;

use App\UserCurr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Account
{
    /**
     * 获取用户账户详情
     * @param $userId
     * @return mixed
     */
    public function statusAccount($userId)
    {
        //$orderDetail = new OrderDetail();
        $statusAccount = UserCurr::where([
            ['user_id','=',$userId],
            ['switch','=',10],
        ])->get()->toArray();
        if (empty($statusAccount)) return false;
        //todo  线下不能使用此接口 线上记得开启
        $balance = 0;
        foreach ($statusAccount as $key => $account){
            if (PHP_OS != 'WINNT'){
                $client=Controller::getStaticClient($account['curr_abb']);
                $balance=$client->get_db_balance($userId);
               // $balance = GrazeRPC::getInstance($account['curr_abb'])
               //     ->sendRequest('getbalance',['ac'.$account['curr_abb'].$userId,UserCurr::$confirmNum[$account['curr_abb']]])
               //     ->getRpcResult();
                $balance = number_format($balance,8,'.','');
            }
           // $trade = $orderDetail->ordInTradMoney($account['curr_abb'],$userId);

            //--获取正在交易中的
            /*$currency_id=ACXWalletController::getMarkId($account['curr_abb']);
            $result= DB::table("xchange")->where([
                ["market_id",'=',$currency_id],
                ["user_id",'=',$userId],
                ["status",'=',0]
            ])->get();

            $trade=0;
            if($result){
                foreach ($result as $key1=>$value){
                    $trade= bcadd($trade,$value->rvolume,8);
                }
            }*/
           # 只兼容一个计量货币 btc 测试服不可用

            if ($account['curr_abb'] == 'btc'){
                $trade = Xchange::where([['status', 0], ['type', 2], ['user_id', $userId]])
                    ->select(DB::raw('sum(rvolume*price) as sum'))->first()->sum;
            } else {
                $trade = Xchange::where([['status', 0], ['type', 1], ['user_id', $userId],['market_name',$account['curr_abb'].'_btc']])
                    ->select(DB::raw('sum(rvolume) as sum'))->first()->sum;
            }
            $trade = null ? self::number_format_e(0) : self::number_format_e($trade);




            $statusAccount[$key]['balance'] = empty($balance)? '0.00000000':$balance;
           // $priceBtc = CurrencySet::where('curr_abb',$account['curr_abb'])->value('price_btc');

            if(empty($currency)){
                $currency="btc";
            }

            $priceBtc = Market::where('market_name',$account['curr_abb'].'_'.$currency)->value('last_price');
            if($account['curr_abb']===$currency){
                $priceBtc=1;
            }
            //$statusAccount[$key]['in_trade'] = empty($trade)?'0.00000000':$trade;
            $statusAccount[$key]['in_trade'] = $trade;

            $total_trade=bcadd($balance,$statusAccount[$key]['in_trade'],8);
            $statusAccount[$key]['btc_rate'] = bcmul($total_trade,$priceBtc,8);
           // $statusAccount[$key]['btc_rate'] = bcmul($balance,$priceBtc,8);

           
            $statusAccount[$key]['curr_img'] = asset('images/'.$account['curr_abb'].'.png');
        }
        return $statusAccount;
    }

    /** 数字格式化
     * @param $num
     * @return float|string
     */
    private static function number_format_e($num)
    {
        $number = number_format($num, 8, '.', '');
        return $number;
    }
    /**
     * 买卖订单生成
     * @param $existResult null|object 结果集
     * @param $total     string  需要总量
     * @param $price     string  价格
     * @param $amount    string  币种数量
     * @param $fee       string  手续费金额
     * @param $type      string  交易类型 （buy sell）
     * @param $tradeCurr string 交易货币名称
     * @param $currId    int   货币Id
     * @return int
     */
  // public function createOrder($existResult,$total,$price,$amount,$fee,$type,$tradeCurr,$currId)
  // {
  //     //数据进行撮合
  //     Log::info("%%AGGHHV");
  //     $reTotal = $this->ordTran($type,$tradeCurr,$price,$total,$amount);
  //     //剩余量
  //     $residualVolume = bcsub($total,$reTotal['initialVolume'],8);
  //     //消耗量
  //     $iniNum = bcsub($amount,$reTotal['residualNum'],8);

  //     Log::info("#####");
  //     Log::info($reTotal);
  //     //第一次交易 插入数据
  //     if (empty($existResult)){
  //         $orderData = [
  //             'curr_id'=>$currId,
  //             'trade_type'=>$type,
  //             'curr_abb'=>$tradeCurr,
  //             'add_time'=>time(),
  //             'total_volume'=>$amount,
  //             'total_volume_btc'=>$total,
  //             'initial_volume'=> $reTotal['initialVolume'],  //消费量
  //             'residual_volume'=>$residualVolume,
  //             'residual_num'=> $reTotal['residualNum'],
  //             'initial_mun'=> $iniNum,
  //             'order_status' =>$reTotal['status'],
  //             'price_btc'=>$price,
  //             'fee_money'=>$fee,
  //         ];
  //         Log::info("XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX");
  //         Log::info($orderData);
  //         Log::info("XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX");
  //         return DB::table('orders')->insertGetId($orderData);
  //     }else{
  //         //非第一 修改数据
  //         $orderData['total_volume'] = bcadd($existResult->total_volume,$amount,8);
  //         $orderData['total_volume_btc'] = bcadd($existResult->total_volume_btc,$total,8);
  //         $orderData['residual_volume'] = bcadd($existResult->residual_volume,$residualVolume,8);
  //         $orderData['initial_volume'] = bcadd($existResult->initial_volume,$reTotal['initialVolume'],8);  //消费量
  //         $orderData['fee_money'] =  bcadd($existResult->fee_money,$fee,8);
  //         $orderData['order_status'] = $reTotal['status'];
  //         $orderData['residual_num'] = bcadd($existResult->residual_num,$reTotal['residualNum'],8);
  //         $orderData['initial_mun'] = bcadd($existResult->initial_mun,$iniNum,8);;
  //         $orderData['last_time'] = time();
  //         return DB::table('orders')->where([
  //             ['trade_type', '=',$type],
  //             ['curr_abb', '=', $tradeCurr],
  //             ['price_btc', '=', $price],
  //         ])->update($orderData);
  //     }
  // }

    /**
     * @param $total
     * @param $price
     * @param $amount
     * @param $feeRate
     * @param $fee
     * @param $netTotal
     * @param $type
     * @param $tradeCurr
     * @param $currId
     * @param $orderId
     * @param $userId
     * @param $userName
     * @param $usdRate
     * @param $cnyRate
     * @return mixed
     */
  //  public function createDetail(
  //      $total,
  //      $price,
  //      $amount,
  //      $feeRate,
  //      $fee,
  //      $netTotal,
  //      $type,
  //      $tradeCurr,
  //      $currId,
  //      $orderId,
  //      $userId,
  //      $userName,
  //      $usdRate,
  //      $cnyRate
  //  )
  //  {
  //      //数据进行撮合
  //      $reTotal = $this->detailTran($type,$tradeCurr,$price,$total,$amount);
  //      Log::info("#$@@!$$");
  //      Log::info($reTotal);
//
  //      //剩余量
  //      $residualVolume = bcsub($total,$reTotal['initialVolume'],8);
  //      $iniNum = bcsub($amount,$reTotal['residualNum'],8);
//
  //      $AcAmount = new AccountAmount();
  //      // 获取用户 btc 账户金额
  //      $btcStart = $AcAmount->getBalance('btc',$userId);
  //      // 获取用户 交易货币 账户金额
  //      $currStart = $AcAmount->getBalance($tradeCurr,$userId);
  //      //数据组装
  //      $orderData = [
  //          'curr_id'=>$currId,
  //          'trade_type'=>$type,
  //          'curr_abb'=>$tradeCurr,
  //          'order_id'=>$orderId,
  //          'user_id'=>$userId,
  //          'user_alias'=>$userName,
  //          'add_time'=>time(),
  //          'start_amount'=>$currStart,  //操作前本币数量
////            'end_amount'=> $currEnd,     //操作后本币数量
  //          'price_btc'=>$price,
  //          'usd_rate' => $usdRate,
  //          'cny_rate' => $cnyRate,
  //          'volume_btc'=>$total,
  //          'fee_money_btc'=>$fee,
  //          'net_volume'=>$amount,
  //          'net_volume_btc'=>$netTotal,
  //          'rate'=>$feeRate,
  //          'start_btc'=>$btcStart,    //开始比特币数量
////            'end_btc'=> $btcEnd,         //结束比特币量
  //          'initial_volume'=> $reTotal['initialVolume'],  //消费量
  //          'residual_volume'=> $residualVolume,  //剩余量
  //          'residual_num'=> $reTotal['residualNum'],
  //          'initial_mun'=> $iniNum,
  //          'order_status'=> $reTotal['status'],
  //          'last_time'=>time(),
  //      ];
  //      Log::info("XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX");
  //      Log::info($orderData);
  //      Log::info("XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX");
  //      $insertId = DB::table('order_details')->insertGetId($orderData);
  //      $reData = ['id'=>$insertId,'data'=>$reTotal,'reInitial'=>$iniNum];
  //      return $reData;
  //  }
//
  //  /**
  //   * 订单 买卖撮合 交易
  //   * @param $type   int 交易类型
  //   * @param $tradeCurr string 货币名称
  //   * @param $price  float 价格
  //   * @param $total  float 操作数量
  //   * @param $amount  float 购买数量
  //   * @return array
  //   */
  //  public function ordTran($type,$tradeCurr,$price,$total,$amount)
  //  {
  //      //交易转换 为撮合
  //      $transType = $this->transType($type,$price);
  //      //获取合适的交易订单 撮合
  //      $orderMode = new Order();
  //      $appData = $orderMode->appOrder($transType['type'],$tradeCurr,$transType['diff'],$transType['order']);
  //      Log::info($appData);
  //      //找不到撮合订单
  //      if ($appData->isEmpty()){
  //          $reData = ['status'=>10,'residualNum'=>$amount,'initialVolume'=>0.00000000];
  //          return $reData;
  //      }
  //      return $this->transOrder($appData,$total,$amount,$price,'orders');
  //  }


    /**
     * 订单细节  买卖撮合 交易
     * @param $type
     * @param $tradeCurr
     * @param $price
     * @param $total
     * @param $amount
     * @return array
     */
  // public function detailTran($type,$tradeCurr,$price,$total,$amount)
  // {
  //     //转换 为撮合
  //     $transType = $this->transType($type,$price);
  //     //找到合适的撮合 订单细节表
  //     $orderDetail = new OrderDetail();
  //     Log::info($transType);
  //     $appData = $orderDetail->appDetail($transType['type'],$tradeCurr,$transType['diff'],$transType['order']);
  //     //找不到撮合订单
  //     Log::info("detailTrandetailTrandetailTran");
  //     Log::info("detailTran=>".$appData);
  //     if ($appData->isEmpty()) return ['status'=>10,'residualNum'=>$amount,'initialVolume'=>0.00000000,'data'=>false];
  //     return $this->transOrderDetials($appData,$total,$amount,$price,'order_details');
  // }

    /**
     * 判断 $key 是否包含在  $array 中
     * @param $key
     * @param $array
     * @return bool
     */
    public function judgeContain($key,$array)
    {
        return in_array($key,$array);
    }

    /**
     * 判断是否生成订单 生成返回对象  未生成返回false
     * @param $currId
     * @param $type
     * @param $price
     * @return bool|object
     */
 // public function judgeExistOrder($currId,$type,$price)
 // {
 //     $result = DB::table('orders')->where([
 //         ['trade_type', '=', $type],
 //         ['curr_id', '=', $currId],
 //         ['price_btc', '=', $price],
 //     ])->first(['total_volume_btc','residual_volume','residual_num','initial_mun','total_volume','initial_volume','fee_money','id']);
 //     if ($result === null) return false;
 //     return $result;
 // }

    /**
     * 判断是否生成订单细节表 生成返回对象  未生成返回false
     * @param $userId
     * @param $currId
     * @param $type
     * @param $price
     * @return bool |object
     */
  //  public function judgeExistDetail($userId,$currId,$type,$price)
  //  {
  //      $result = DB::table('order_details')->where([
  //          ['user_id', '=',$userId],
  //          ['trade_type', '=', $type],
  //          ['curr_id', '=', $currId],
  //          ['price_btc', '=', $price],
  //      ])->first(['total_volume_btc','total_volume','id']);
  //      if ($result === null) return false;
  //      return $result;
  //  }//

    /**
     * 修改以撮合 的订单
     * @param $value
     * @param $interval string 成交量
     * @param $price
     * @param $table
     * @param $orderStatus
     * @return array
     *
     */
 //  private function coverOrder($value,$interval,$price,$table = 'orders',$orderStatus = 30)
 //  {
 //      //剩余量 撮合后
 //      $re = bcsub($value->residual_num,$interval,8);
 //      //消耗量 撮合后
 //      $rc = bcadd($value->initial_mun,$interval,8);
 //      //消耗比特币量
 //      $reVol = bcmul($interval,$price,8);

 //      $initialVolume = bcadd($value->initial_volume,$reVol,8);
 //      $residualVolume = bcsub($value->residual_volume,$reVol,8);



 //      Log::info('residualVolume =>'.$residualVolume);
 //      Log::info('initialVolume =>'.$initialVolume);
 //      Log::info('value->initial_volume =>'.$value->initial_volume);
 //      Log::info('value->residual_volume =>'.$value->residual_volume);
 //      Log::info('reVol =>'.$reVol);


 //       // 把剩余 算出剩余个数   为卖的值 $price
 //      $updateData = ['order_status'=>$orderStatus,'last_time'=>time(),'residual_num'=>$re,'initial_mun'=>$rc,'initial_volume'=>$initialVolume,'residual_volume'=>$residualVolume];

 //      $result = ['initialVolume'=>$reVol];
 //      $result['bench'] = ['price'=> $price,'initialMun'=>$interval];

 //      Log::info("中国任克明start");
 //      Log::info($re);
 //      Log::info($rc);
 //      Log::info($interval);
 //      Log::info($updateData);

 //      if ($table === 'order_details'){
 //          if ($value->trade_type == 10){
 //              //货币消耗量
 //              $data  = ['currAbb'=>$value->curr_abb,'send' => 1,'rec'=> $value->user_id,'money'=>$interval,'tradeType'=>$value->trade_type,'id'=>$value->id];
 //          }else{
 //              $resInter = bcmul($reVol,$value->rate,8);
 //              $netMoney = bcsub($reVol,$resInter,8);
 //              $updateData['fee_money'] = bcadd($value->fee_money,$resInter,8);
 //              $data = ['currAbb'=>'btc','send' => 1,'rec'=> $value->user_id,'money'=> $netMoney,'tradeType'=>$value->trade_type,'id'=>$value->id];
 //          }
 //          $result['data'] = $data;
 //      }
 //      Log::info($result);
 //      Log::info("中国任克明end");
 //      DB::table($table)->where('id',$value->id)->update($updateData);
 //      return $result;
 //  }

    /**
     * 转换 撮合
     * @param $type
     * @param $price
     * @return array
     */
// private function tran//sType($type,$price)
// {
//     if ($type == 10){
//         $type = 20;
//         $diff = ['price_btc', '<=', $price];
//         $order = 'asc';
//     }else{
//         $type = 10;
//         $diff = ['price_btc', '>=', $price];
//         $order = 'desc';
//     }
//     return ['type'=>$type,'diff'=>$diff,'order'=>$order];
// }

    /**
     * 交易撮合
     * @param $appData
     * @param $total
     * @param $amount
     * @param $price
     * @param $table
     * @return array
     */
  //  public function transOrder($appData,$total,$amount,$price,$table)
  //  {
  //      Log::info("12334556456789");
  //      $recur = 0;
  //      $status = 30;
  //      $surplusRes = "0.00000000";
  //      $reVol = 0;
  //      $data = [];
  //      $bench = [];
  //      foreach ($appData as $key => $value){
  //          //个数比较
  //          $recur = bcadd($recur,$value->residual_num,8);
  //          $compResult = bccomp($amount,$recur,8);
  //          Log::info('##$compResult=>'.$compResult);
  //          Log::info('##$amount=>'.$amount);
  //          Log::info('##$recur=>'.$recur);
  //          Log::info('##$total_volume =>'.$value->residual_num);
  //          if ($value->trade_type == 20) $price = $value->price_btc;
  //          if ($compResult === 0){
  //              $result = $this->coverOrder($value,$value->residual_num,$price,$table);
  //              $reVol = bcadd($reVol,$result['initialVolume'],8);
  //              Log::info("##11111111111111111111");
  //              if ($table == 'order_details'){
  //                  $data[] = $result['data'];
  //                  $bench[] = $result['bench'];
  //              }
  //              Log::info($reVol);
  //              Log::info($data);
  //              Log::info($result);
  //              break;
  //          }elseif ($compResult === -1){
  //              //多出来成交不完的 剩余量
  //              $surplus = bcsub($recur,$amount,8);
  //              //成交不完的订单  使用量
  //              $interval = bcsub($value->residual_num,$surplus,8);
  //              $result = $this->coverOrder($value,$interval,$price,$table,20);
  //              $reVol = bcadd($reVol,$result['initialVolume'],8);
  //              Log::info("##22222222222222222222");
  //              if ($table == 'order_details'){
  //                  $data[] = $result['data'];
  //                  $bench[] = $result['bench'];
  //              }
  //              Log::info($reVol);
  //              Log::info($data);
  //              Log::info($result);
  //              break;
  //          }else{
  //              //当所有的订单撮合完了，还剩余未被撮合
  //              $result = $this->coverOrder($value,$value->residual_num,$price,$table);
  //              $reVol = bcadd($reVol,$result['initialVolume'],8);
  //              if ($table == 'order_details'){
  //                  $data[] = $result['data'];
  //                  $bench[] = $result['bench'];
  //              }
  //              Log::info("##33333333333333333333333333333");
  //              Log::info($reVol);
  //              Log::info($data);
  //              Log::info($result);
  //              if ($appData->count()-1 == $key){
  //                  $surplusRes = bcsub($amount,$recur,8);
  //                  $status = 20;
  //              }
  //          }
  //      }
  //      //$reVol 代表的是 这个提交的点单消耗的比特币
  //      $return = ['status'=>$status,'residualNum'=>$surplusRes,'initialVolume'=>$reVol];
  //      if ($table == 'order_details') $return = ['status'=>$status,'residualNum'=>$surplusRes,'initialVolume'=>$reVol,'data'=>$data,'bench'=>$bench];
  //      return $return;
  //  }

    /**
     * 交易撮合 -order_detials
     * @param $appData
     * @param $total
     * @param $amount
     * @param $price
     * @param $table
     * @return array
     */
  //  public function transOrderDetials($appData,$total,$amount,$price,$table)
  //  {
  //      Log::info("12334556456789xxxxxxxxx");
  //      $recur = 0;
  //      $status = 30;
  //      $surplusRes = "0.00000000";
  //      $reVol = 0;
  //      $data = [];
  //      $bench = [];
  //      foreach ($appData as $key => $value){
  //          //个数比较
  //          $recur = bcadd($recur,$value->residual_num,8);
  //          $compResult = bccomp($amount,$recur,8);
  //          Log::info('##$compResult=>'.$compResult);
  //          Log::info('##$amount=>'.$amount);
  //          Log::info('##$recur=>'.$recur);
  //          Log::info('##$total_volume =>'.$value->residual_num);
  //          if ($value->trade_type == 20) $price = $value->price_btc;
  //          if ($compResult === 0){
  //              $result = $this->coverOrder($value,$value->residual_num,$price,$table);
  //              $reVol = bcadd($reVol,$result['initialVolume'],8);
  //              Log::info("##11111111111111111111");
  //              if ($table == 'order_details'){
  //                  $data[] = $result['data'];
  //                  $bench[] = $result['bench'];
  //              }
  //              Log::info($reVol);
  //              Log::info($data);
  //              Log::info($result);
  //              break;
  //          }elseif ($compResult === -1){
  //              //多出来成交不完的 剩余量
  //              $surplus = bcsub($recur,$amount,8);
  //              //成交不完的订单  使用量
  //              $interval = bcsub($value->residual_num,$surplus,8);
  //              $result = $this->coverOrder($value,$interval,$price,$table,20);
  //              $reVol = bcadd($reVol,$result['initialVolume'],8);
  //              Log::info("##22222222222222222222");
  //              if ($table == 'order_details'){
  //                  $data[] = $result['data'];
  //                  $bench[] = $result['bench'];
  //              }
  //              Log::info($reVol);
  //              Log::info($data);
  //              Log::info($result);
  //              break;
  //          }else{
  //              //当所有的订单撮合完了，还剩余未被撮合
  //              $result = $this->coverOrder($value,$value->residual_num,$price,$table);
  //              $reVol = bcadd($reVol,$result['initialVolume'],8);
  //              if ($table == 'order_details'){
  //                  $data[] = $result['data'];
  //                  $bench[] = $result['bench'];
  //              }
  //              Log::info("##33333333333333333333333333333");
  //              Log::info($reVol);
  //              Log::info($data);
  //              Log::info($result);
  //              if ($appData->count()-1 == $key){
  //                  $surplusRes = bcsub($amount,$recur,8);
  //                  $status = 20;
  //              }
  //          }
  //      }
  //      //$reVol 代表的是 这个提交的点单消耗的比特币
  //      $return = ['status'=>$status,'residualNum'=>$surplusRes,'initialVolume'=>$reVol];
  //      if ($table == 'order_details') $return = ['status'=>$status,'residualNum'=>$surplusRes,'initialVolume'=>$reVol,'data'=>$data,'bench'=>$bench];
  //      return $return;
  //  }
    /**
     * 转钱
     * @param $type
     * @param $userId
     * @param $netTotal
     * @param $reInitial
     * @param $price
     * @param $tradeCurr
     * @param $amount
     * @param $feeRate
     * @return boolean
     */
  // public function move//Money($type,$userId,$netTotal,$reInitial,$price,$tradeCurr,$amount,$feeRate)
  // {
  //     $acAmount = new AccountAmount();
  //     //根据交易类型 各关联账户金额的变化
  //     if ($type == 10){
  //         //买操作 要把 btc 转到 公司账户
  //         Log::info('!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!'.$netTotal);
  //         if ($reInitial['data']['status'] == 30){
  //             $initialVolume = $reInitial['data']['initialVolume'];
  //             $fee = bcmul($initialVolume,$feeRate,8);
  //             $netTotal = bcadd($fee,$initialVolume,8);
  //             Log::info('xxxxxxxxxxxxxxx$$$$$$$xxxxxxxafdgdfgfhrt4t=>'.$netTotal);
  //             $acAmount->move('btc',$userId,1,$netTotal);
  //             $updateData['fee_money'] = $fee;
  //         }else{
  //             Log::info('xxxxxxxxxxxxxxx$$$$$$$xxxxxxxxxxxxaaaaaaaaaaaaaafdgdfgfhrt4t=>'.$netTotal);
  //             $acAmount->move('btc',$userId,1,$netTotal);
  //         }
  //         Log::info('xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxaaaaaaaaaaaaaafdgdfgfhrt4t=>'.$netTotal);
  //         Log::info('xxxxxxxxxxxxxxxxxdgdfgfhrt4t'.$reInitial['reInitial']);
  //         //把购买到了的货币移动 用户账户
  //         if (0 != $acAmount->zero($reInitial['reInitial'])) $acAmount->move($tradeCurr,1,$userId,$reInitial['reInitial']);
  //     }else{
  //         Log::info(1111111111111);
  //         //把卖去的货币得到的货币 转到用户账户
  //         Log::info($reInitial['reInitial']);
  //         $currInit = bcmul($reInitial['reInitial'],$price,8);
  //         Log::info(1111111111111);
  //         $fee = bcmul($currInit,$feeRate,8);
  //         $result = bcsub($currInit,$fee,8);
  //         if (0 != $acAmount->zero($currInit)) $acAmount->move('btc',1,$userId,$result);
  //         Log::info('^^^^^^^^^^^^^^^^^^'.$currInit);
  //         Log::info('^^^^^^^^^^^^^^^^^^'.$result);
  //         //卖操作 把要卖的货币移动 公司账户
  //         Log::info('^^^^^^^^^^^^amount^^^^^'.$amount);
  //         $acAmount->move($tradeCurr,$userId,1,$amount);
  //         $updateData['fee_money'] = $fee;
  //     }
  //     $updateData['end_btc'] = $acAmount->getBalance('btc',$userId);
  //     $updateData['end_amount'] = $acAmount->getBalance($tradeCurr,$userId);
  //     OrderDetail::where('id',$reInitial['id'])->update($updateData);
  //     return true;
  // }

    /**
     * 把所有的被撮合订单数据计算拿出来计算
     * 因为在里面计算 会因为失误而导致数据对应不上
     * @param $data array 被撮合的所有订单数据
     *
     */
 //  public function firstMoney($data)
 //  {
 //      $acAmount = new AccountAmount();

 //      foreach ($data as $key => $value){
 //          //转钱给用户因为订单以撮和成功
 //          $acAmount->move($value['currAbb'],$value['send'],$value['rec'],$value['money']);
 //          //获取货币
 //          $balance = $acAmount->getBalance($value['currAbb'],$value['rec']);
 //          //卖更新获得的货币 已收手续费 卖
 //          if ($value['tradeType'] == 20){
 //              OrderDetail::where('id',$value['id'])->update(['end_btc'=>$balance]);
 //          }else{
 //              //买同样转货币
 //              $orderDetail = OrderDetail::where('id',$value['id'])->first(['order_status','residual_num','initial_volume']);
 //              $residualNum = $acAmount->zero($orderDetail->residual_num);
 //              $updateData = ['end_amount'=>$balance];
 //              //当被撮合订单完成 而且 有剩余当转回去 还有 手续费
 //              if ($orderDetail->order_status == 30 && $residualNum >0 ){
 //                  $realFee = bcmul($orderDetail->initial_volume,$orderDetail->rate,8);
 //                  $returnBtc = bcadd($realFee,$orderDetail->residual_num,8);
 //                  $acAmount->move('btc',$value['send'],$value['rec'],$returnBtc);
 //                  $endBtc = $acAmount->getBalance('btc',$value['rec']);
 //                  $updateData['end_btc'] = $endBtc;
 //                  $updateData['fee_money'] = $realFee;
 //              }
 //              OrderDetail::where('id',$value['id'])->update($updateData);
 //          }
 //      }
 //  }
}