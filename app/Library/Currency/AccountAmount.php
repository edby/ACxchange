<?php
/**
 * Created by PhpStorm.
 * User: ZRothschild
 * Date: 2018/2/28
 * Time: 16:22
 */

namespace App\Library\Currency;


use App\CurrencySet;
use App\Http\Controllers\Controller;
use App\Models\BalanceDB;

use App\UserCurr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AccountAmount
{
    /**
     * 获取货币账户金额
     * @param $currencyName string 获取货币账户金额的货币名称缩写
     * @param $userId int 用户ID
     * @return string 返回金额
     */
    public function getBalance($currencyName,$userId)
    {
      //  $existBool = array_key_exists($currencyName,GrazeRPC::$url);

      //  if ($existBool === false) return 0;

        //--获取数据库余额，不准确的余额
        $balance=BalanceDB::get_balance($currencyName,$userId);
      // try{
      //     $balance = GrazeRPC::getInstance($currencyName)
      //         ->sendRequest('getbalance',['ac'.$currencyName.$userId])->getRpcResult();
      // }catch (\Exception $exception){
      //     $balance = 0.00000000;
      // }
        return number_format($balance,8,".","");
    }

    /**
     * 对比金额是否大于账户金额 大于返回false
     * @param $amount  string|int 对比的金额
     * @param $currencyName string 获取货币账户金额的货币名称缩写
     * @param $userId  int 用户ID
     * @param $currTrade string 交易的货币
     * @param $type int 交易类型
     * @return bool
     */
    //-
   // public function judg//eBalance($amount,$currencyName,$userId,$currTrade,$type)
   // {
   //     $this->getBalance('btc',$userId);
   //     $boolArr = in_array($currencyName,['usd','cny']);
   //     if ($type == 10){
   //         $balance = $this->getBalance('btc',$userId);
   //     }else{
   //         $balance = $this->getBalance($currTrade,$userId);
   //     }
//
   //     if ($boolArr == true){
   //         $currPrice = CurrencySet::where('curr_abb',$currTrade)->value('price_'.$currencyName);
   //         $balance = bcmul($balance,$currPrice,8);
   //     }
   //     Log::info($balance."------".$amount);
   //     $comp = bccomp($amount,$balance,8);
   //     if ($comp === 1) return false;
   //     return true;
   // }

    /**
     * 买卖 数据验证
     * @param $total    string 需要总量
     * @param $price    string 价格
     * @param $amount   string 币种数量
     * @param $feeRate  float  手续费费率
     * @param $fee      string 手续费金额
     * @param $netTotal string 得到或者花费金额
     * @param $type     string 交易类型 （buy sell）
     * @param $tradeCurr string 交易货币
     * @return array result[result,msg]  [bool,string]
     */
    public function numValidate($total,$price,$amount,$feeRate,$fee,$netTotal,$type,$tradeCurr)
    {
        $data_ns=[];
       // dump("price: $price,amount:$amount,total:$total,fee:$fee");
        if ($price <0 ) {
            $data_ns['msg']="price <0";
            $data_ns['result']=false;
            return $data_ns;
        }
        if ($this->one($amount) < 0 ){
            $data_ns['msg']="amount <0";
            $data_ns['result']=false;
            return $data_ns;
        }
        if ($this->one($total) < 0 ){
            $data_ns['msg']="total <0";
            $data_ns['result']=false;
            return $data_ns;
        }
        if ($this->zero($fee) < 0 ){
            $data_ns['msg']="fee <0";
            $data_ns['result']=false;
            return $data_ns;
        }

        $userCurr = new UserCurr();
        $feeCurrRate = $userCurr->getFeeRate(Auth::id(),$tradeCurr);

      //  Log::info($feeCurrRate."ccjcjcjcjcjcjcjcjcjc".$feeRate);

     //   Log::info($tradeCurr);
        //dump("feeRate: $feeRate,feeCurrRate:$feeCurrRate");
        if ($feeRate != $feeCurrRate) {
            $data_ns['msg']="feeRate != feeCurrRate: $feeRate != $feeCurrRate";
            $data_ns['result']=false;
            return $data_ns;
        }

    //    Log::info($total."KKKKKKKK".$fee);

        //todo可能需要判断不能小于某个值
        $totalUnit = bcmul($amount,$price,8);
    //    Log::info($totalUnit."AAAAAA".$total);
       // if(Auth::id()==151){
       //     dump("total:".$total."totalUnit:$totalUnit");
       // }
        if (0 != bccomp($total,$totalUnit,8)){
            $data_ns['msg']="total != totalUnit: $total != $totalUnit";
            $data_ns['result']=false;
            return $data_ns;
        }
        // 买卖netTotal 核算;
        if ($type == 10){
         //   Log::info($total."KKKKKKKK".$fee);
            $netTotalTmp = bcmul($amount,$price,12);
            //--买不需要额外支付手续费
            //$netTotalTmp = bcmul($netTotalTmp,bcadd(1,$feeCurrRate,8),8);

            //$netTotalTmp = bcadd($total,$fee,8);//这不是我隐藏的
      //      Log::info("AAAAAc".$netTotalTmp);
            $fee_temp = bcmul($amount,$feeRate,8);
           // dump("fee_temp: $fee_temp,fee:$fee");

           // if(Auth::id()==151){
           //     dump("fee_temp:".$fee_temp."fee:$fee");
           // }

            if (0 != bccomp($fee_temp,$fee,8)){
                $data_ns['msg']="fee_temp != fee: $fee_temp != $fee";
                $data_ns['result']=false;
                return $data_ns;

            } //--手续费不对
            //$shouru=bcsub($amount,$fee_temp,8);
            $shouru_feilv=bcsub(1,$feeRate,8);
            $shouru=bcmul($amount,$shouru_feilv,8);

           // dump("shouru: $shouru,netTotal:$netTotal");
//
//           if(Auth::id()==151){
//               var_dump("shouru:".$shouru."netTotal:$netTotal");
//           }

            if (0 != bccomp($shouru,$netTotal,8)){
                $data_ns['msg']="shouru != netTotal: $shouru != $netTotal";
                $data_ns['result']=false;
                return $data_ns;
            } //--最后收入对不对
            $data_ns['result']=true;
            return $data_ns;
        }else{
            $netTotalTmp = bcmul($amount,$price,12);
            $netTotalTmp = bcmul($netTotalTmp,bcsub(1,$feeCurrRate,8),8);
           // $netTotalTmp = bcsub($total,$fee,8);
            if ($this->one($netTotalTmp) < 0 ) $netTotalTmp = 0;
        }
   //     Log::info($netTotalTmp."NNNNNNNN".$netTotal);
        if (0 != bccomp($netTotalTmp,$netTotal,8)){
            $data_ns['msg']="netTotalTmp != netTotal: $netTotalTmp != $netTotal";
            $data_ns['result']=false;
            return $data_ns;
        }
        $data_ns['result']=true;
        return $data_ns;
    }

    /**
     * move
     * @param $currType
     * @param $send
     * @param $rec
     * @param $money
     * @return \Graze\GuzzleHttp\JsonRpc\Message\ResponseInterface|mixed|null
     */
  // public function move($currType,$send,$rec,$money)
  // {
  //     if ('rpz'==$currType){
  //         return GrazeRPC::getInstance($currType)
  //             ->sendRequest('move',['ac'.$currType.$send,'ac'.$currType.$rec,(float)$money]);
  //     }else{
  //         return GrazeRPC::getInstance($currType)
  //             ->sendRequest('move',['ac'.$currType.$send,'ac'.$currType.$rec,$money]);
  //     }

  // }

    /**
     *手续费
     * @param $total
     * @param $rate
     * @return string
     */
    public function serviceCharge($total,$rate)
    {
        $fee = bcmul($total,$rate,8);
        $result = bccomp($fee,0.00000001,8);
        if ($result == -1) $fee = "0.00000001";
        return $fee;
    }

    public function one($num)
    {
        $tmp = bccomp($num,0.00000001,8);
        return $tmp;
    }

    public function zero($num)
    {
        $tmp = bccomp($num,0,8);
        return $tmp;
    }

    /**
     * 获取用户是否生成地址 没有生成就返回 空字符串
     * @param string $currencyName
     * @param int $userId
     * @return mixed
     */
    public function getAddress($currencyName,$userId)
    {
        $currencyName_up=strtoupper($currencyName);
        $client=Controller::getStaticClient($currencyName_up);
        $address=$client->getAddressList('ac'.$currencyName.$userId);
        if(empty($address)){
            $address=$client->getNewAddress('ac'.$currencyName.$userId);
            return$address;
        }else{
            return $address[0];
        }
        
     //  $existBool = array_key_exists($currencyName,GrazeRPC::$url);
     //  if ($existBool === false) return '';

     //  $address = GrazeRPC::getInstance($currencyName)
     //      ->sendRequest('getaddressesbyaccount',['ac'.$currencyName.$userId])->getRpcResult();
     //  if (empty($address)){
     //      return '';
     //  }else{
     //      return $address[0];
     //  }
    }

    /**
     * 获取交易订单 区块链
     * @param $currencyName
     * @param $userId
     * @return mixed|string
     */
   // public function getTransactionList($currencyName,$userId)
   // {
   //     $existBool = array_key_exists($currencyName,GrazeRPC::$url);
   //     if ($existBool === false) return '';
//
   //     $address = GrazeRPC::getInstance($currencyName)
   //         ->sendRequest('listtransactions',['ac'.$currencyName.$userId])->getRpcResult();
   //     return $address;
   // }


    /**
     * 转账 成功会返回 true
     * @param $currencyName
     * @param $userId
     * @param $address
     * @param $amount//
     * @return mixed|string
     */
 //   public function withdraw($currencyName,$userId,$address,$amount)
 //   {
 //       $existBool = array_key_exists($currencyName,GrazeRPC::$url);
 //       if ($existBool === false) return '';
//
 //       $instance = GrazeRPC::getInstance($currencyName);
 //       $confirmNum = $instance->getConfirm();
 //       try{
 //           $bool = $instance->sendRequest('sendfrom',['ac'.$currencyName.$userId,$address,(double)$amount,$confirmNum])->getRpcResult();
 //       }catch (\Exception $exception){
 //           $bool = '';
 //       }
 //       return $bool;
 //   }
}