<?php
/**
 * Created by PhpStorm.
 * User: ZRothschild
 * Date: 2018/2/1
 * Time: 11:23
 */

namespace App\Library\Currency;


use App\CurrencySet;
use App\Http\Controllers\Controller;
use App\OrderDetail;

use App\UserCurr;
use Illuminate\Support\Facades\DB;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class RegisterAddCurr
{
    /**
     * 为注册用户添加货币地址
     * @param $userId
     * @return bool
     *
     */
    static public function addCurrency($userId)
    {
        $currSet = CurrencySet::where('switch_on',10)->get(['curr_id','curr_name','curr_abb'])->toArray();
        if (empty($currSet)) return true;
        $user_fee_data=[];
        //--查询默认汇率是多少
        $market=DB::table("market")->get();

        $market_fees=[];
        if($market){
            foreach ($market as $market1){
                $market_name=$market1->market_name;
                $market_fee=$market1->fee;
                $market_fees[$market_name]=$market_fee;
            }
        }
       // dump($currSet);
        $dex=0;
        foreach ($currSet as $key => $value){
            $currSet[$key]['user_id'] = $userId;

            //--加入btc市场汇率
            if($value['curr_abb']!="btc"){
                $user_fee_data[$dex]['user_id']=$userId;
                $user_fee_data[$dex]['trade_curr']=$value['curr_abb'];
                $user_fee_data[$dex]['currency']="btc";
                $temp_key=$user_fee_data[$dex]['trade_curr']."_".$user_fee_data[$dex]['currency'];
               // dump($temp_key);
                $user_fee_data[$dex]['fee_rate']=$market_fees[$temp_key];
                $dex++;
            }

            //dump($dex);

            if (PHP_OS === 'WINNT'){
                $fileName = 'qrcodes/'.$userId.$value['curr_abb'].'.png';
            }else{
                $fileName = base_path().'/public/qrcodes/'.$userId.$value['curr_abb'].'.png';
            }

            $huobi_name=strtoupper($value['curr_abb']);
            $client=Controller::getStaticClient($huobi_name);
            $currSet[$key]['address'] =  $client->getNewAddress('ac'.$value['curr_abb'].$userId);
          // dump($currSet[$key]['address']);
            // $currSet[$key]['address'] = GrazeRPC::getInstance($value['curr_abb'])->sendRequest('getnewaddress',['ac'.$value['curr_abb'].$userId])->getRpcResult();
            QrCode::format('png')->size(220)->generate($currSet[$key]['address'],$fileName);
        }
       // dump($user_fee_data);
        $insertBool = UserCurr::insert($currSet);

        //--插入汇率
        //dump($user_fee_data);
        DB::table("user_fee")->insert($user_fee_data);

        return $insertBool;
    }

    /**
     * 货币汇率  默认汇率
     * @return bool
     */
    static public function getExchangeRate()
    {
        $currencyAll = CurrencySet::where('switch',10)->get();
        $url = 'https://api.coinmarketcap.com/v1/ticker/';
        $cny= '/?convert=CNY';
        foreach ($currencyAll as $key => $currency){

            //判断是否需要请求 api 的接口值做参考
            $orderDetail = OrderDetail::where('curr_abb',strtolower($currency->curr_name))->first();
            if (!empty($orderDetail)) continue;
            try{
                $data = file_get_contents($url.strtolower(str_replace(' ','-',$currency->curr_name)).$cny);
                $result = json_decode($data,true)[0];
                // 返回一个对象
                $resultStatus = CurrencySet::updateOrCreate(
                    ['curr_name' => strtolower($currency->curr_name)],
                    [
                        'price_usd' => $result['price_usd'],
                        'price_cny' => $result['price_cny'],
                        'price_btc' =>$result['price_btc'],
                        'last_updated' => $result['last_updated'],
                        'fee_rate' => 0.005,
                    ]
                );
            }catch (\Exception $exception){
//                Log::info($exception->getMessage());
            }
        }
        return 'aaaaa';
    }

    /**
     * 出售时候货币的相互转化
     * @param $currency  string 卖出的货币名称
     * @param $rateCurrency string 参考币种
     * @param $total float 卖出总价格 包括手续费
     * @param $fee float 收取的手续费
     * @return array
     */
    public function sellConversion($currency,$rateCurrency,$total,$fee)
    {
        $tmp = 'price_'.$rateCurrency;
        $filed = [$tmp];
        if ('btc' != $rateCurrency){
            $filed = array_merge($filed,['price_btc']);
        }
        $result = CurrencySet::where('curr_abb',$currency)->first($filed);
        //本币出售总数量 包括手续费
        $coinCount = bcdiv($total,$result->$tmp,8);
        //折合比特币总数量
        $btcCount = bcmul($coinCount,$result->price_btc,8);
        $feeCoin =  bcmul($coinCount,$fee,8);
        $feeBtc =  bcmul($btcCount,$fee,8);
        $data = [
            'coinCount' => $coinCount,
            'feeCoin' => $feeCoin,
            'ownCoin' => bcsub($coinCount,$feeCoin,8),
            'btcCount'=> $btcCount,
            'feeBtc'=> $feeBtc,
            'ownBtc'=>  bcsub($btcCount,$feeBtc,8),
        ];
        return $data;
    }
}