<?php
/**
 * Created by PhpStorm.
 * User: ZRothschild
 * Date: 2018/2/26
 * Time: 17:18
 */

namespace App\Http\Controllers\FrontEnd;

use App\CurrencySet;
use App\Http\Controllers\Admin\BackController;
use App\Http\Controllers\Controller;
use App\KLine;
use App\Library\Currency\RegisterAddCurr;
use App\Mail\ChangeEmail;
use App\Models\Market;
use App\Models\Xchange;
use App\Models\XchangInfo;
use App\OrderDetail;
use App\WithdrawHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\WithdrawHistoryNode;
use App\User;
use App\Models\Currency;

class CronController extends Controller
{
    /**
     *
     * @return bool
     */
    public function exchangeRate()
    {
        return RegisterAddCurr::getExchangeRate();
    }

    public function test()
    {
        Log::info('timetime=>'.now());
    }


    public function ce(Request $request)
    {
        $data = ['email'=>'873908960@qq.com','newEmail'=>'2329852037@aa.com','url'=>now().$request->id];
        Log::info('ceTimeceTime=>'.now());
        Log::info('idididididididididid=>'.$request->id);
        Mail::to('873908960@qq.com')->send(new ChangeEmail($data));
    }


    public function  oneMinute()
    {
        //$this->tool(2,1);
        $this->tool(60,1);
    }


    public function  fiveMinute()
    {
        //$this->tool(10,2);
        $this->tool(300,2);
    }


    /**
     * 15 分钟
     */
    public function fifteen()
    {
        //$this->tool(30,3);
        $this->tool(900,3);
    }

    /**
     * 30 分钟
     */
    public function halfHour()
    {
        //$this->tool(60,4);
        $this->tool(1800,4);
    }

    /**
     * 一小时
     */
    public function oneHour()
    {
       // $this->tool(120,5);
        $this->tool(3600,5);
    }

    /**
     * 2小时
     */
    public function twoHour()
    {
        //$this->tool(240,6);
        $this->tool(7200,6);
    }

    /**
     * 6小时
     */
    public function sixHour()
    {
        //$this->tool(720,7);
        $this->tool(21600,7);
    }

    /**
     * 12小时
     */
    public function twelveHour()
    {
        //$this->tool(1440,8);
        $this->tool(43200,8);
    }

    /**
     * 一天
     */
    public function oneDay()
    {
        //$this->tool(2880,9);
        $this->tool(86400,9);
    }

    /**
     * 一周
     */
    public function week()
    {
        //$this->tool(20160,10);
        $this->tool(604800,10);
    }


//    /**
//     * 2天
//     */
//    public function twoDay()
//    {
//        $this->tool(5760,9);
//    }




//    /**
//     * 2周
//     */
//    public function twoWeek()
//    {
//        $this->tool(10996,9);
//    }
//
//    /**
//     * 一月
//     */
//    public function month()
//    {
//        $this->tool(23564,10);
//    }
//
//    /**
//     * 2月
//     */
//    public function twoMonth()
//    {
//        $this->tool(47127,11);
//    }
//
//    /**
//     * 6个月
//     */
//    public function sixMonth()
//    {
//        $this->tool(131382,12);
//    }


    private function tool($gapTime,$type)
    {
        $endTime = time()-$gapTime;
        $currSet = Market::all(['market_name','last_price','id'])->toArray();
        dump($currSet);

        foreach ($currSet as $key => $curr){

            $totalSeconde=$gapTime*35;
            $time_diff=time()-$totalSeconde;
            $mark_name_temp=explode("_",$curr['market_name']);
            KLine::where([
                ['datum_type','=',$type],
                ['curr_abb','=',$mark_name_temp[0]],
                ['datum_time','=',$gapTime],
                ['add_time','<',$time_diff],
                ['currency','=',$mark_name_temp[1]]
            ])->delete();

            $whereOne = [
                ['market_name','=',$curr['market_name']],
                ['created_at','>=',$endTime],
            ];

            $dataBool= DB::table("xchange_info")->where($whereOne)->get(['id']);

            if ($dataBool->isEmpty()){
                echo "我是空的";
                $high = $curr['last_price'];
                $low = $curr['last_price'];
                $close = $curr['last_price'];
                $open = $curr['last_price'];
                $volume = 0.00000000;
                $average = $curr['last_price'];
            }else{

                $high= DB::table("xchange_info")->where($whereOne)->max('last_price');
                $low = DB::table("xchange_info")->where($whereOne)->min('last_price');

                $close = DB::table("xchange_info")->where($whereOne)->orderByDesc('created_at')->first(['last_price']);
               // dump($close);
                //if(1==1)return;
                $close = $close->last_price;//['last_price'];
                $open = DB::table("xchange_info")->where($whereOne)->orderBy('created_at')->first(['last_price']);
                $open = $open->last_price;//['last_price'];

                echo "最小值: \r\n";
                dump($low);
                echo "最大值:\r\n";
                dump($high);
                echo "开盘:\r\n";
                dump($open);
                echo "收盘:\r\n";
                dump($close);

                //==
                $data = DB::table('xchange_info')
                    ->select(DB::raw('last_price,volume,truncate(last_price*volume,8) as total_price'))->
                        where([['updated_at','>',$endTime],['market_id','=',$curr['id']]])->get();
                //
                $toatal_btc_price=0;
                $total_btc_value=0;

                foreach ($data as $ke=>$value){
                    $toatal_btc_price=bcadd($toatal_btc_price,$value->total_price,8);
                    $total_btc_value=bcadd($total_btc_value,$value->volume,8);
                }
                $volume=$total_btc_value;


                $toatalBool = bccomp($toatal_btc_price,'0.00000000',8);
                if($toatalBool > 0){
                    $average=bcdiv($toatal_btc_price,$total_btc_value,8);
                }else{
                    $average = $open;//--不等于0
                }

            }

            echo "high: $high"."\r\n low: ".$low."\r\n"." volume: ".$volume;
            $mark_name_temp=explode("_",$curr['market_name']);
            KLine::create([
                'open' => $open,
                'high' => $high,
                'low' => $low,
                'close' => $close,
                'average' => $average,
                'volume' => $volume,
                'add_time' => time(),
                'datum_time' => $gapTime,
                'datum_type' => $type,
                'late_time' => $endTime,
                //'curr_id' =>$curr['id'],
                'curr_abb' =>$mark_name_temp[0],
                'currency'=>$mark_name_temp[1],//
                'add_time_1'=>date('Y-m-d H:i:s',time())
            ]);
        }
    }

    public static function getWithdrawConfirmations1($currency)
    {
        //\Log::info('getWithdrawConfirmations'.$currency);
        $confirmations = getenv($currency.'_CONFIRMATIONS');
        self::getWithdrawConfirmations($currency,$confirmations);
    }

    public static function getWithdrawConfirmations($currency,$confirmations)
    {
        Log::info('getWithdrawConfirmations---'.$currency.'   confirmations: '.$confirmations);
        $client = parent::getStaticClient($currency);
        $withdraw = WithdrawHistory::where('currency',$currency)->select('txid','confirmations','status')->get();
        foreach ($withdraw as $wit) {
            if($wit->status == 1){
                try{
                    $details = $client->gettransactionDetail($wit->txid);
                    WithdrawHistory::where('txid', $wit->txid)->update(['confirmations' => $details['confirmations']]);
                }catch (\Exception $e){
                    Log::info('getWithdrawConfirmations Fail -----txid= '.$wit->txid.' .');
                }
            }
        }
    }

    //--超过24小时的订单  没有点击  邮箱，自动变 已取消
    public static function update_withdraw_change_from_24hour(){
        $jieshu_time=time()-24*3600;
        $time=date("Y-m-d H:i:s",$jieshu_time);
        try{
            DB::beginTransaction();
            $result=DB::table("withdraw_history")->where([['status','=',0],['created_at','<',$time]])->lockForUpdate()->get();
            echo "开始更新状态</br>";
            if($result && count($result)>0){
                $count=count($result);
                echo "超过24小时未确认邮箱 提现订单数量为 $count</br>";
                $dex=0;
                foreach ($result as $withdraw){
                    $dex++;
                    $id=$withdraw->id;
                    $currency=$withdraw->currency;
                    $user_id=$withdraw->user_id;
                    echo "第 $dex 个 ,总共 $count,当前id $id ,user_id: $user_id ,currency: $currency</br>";
                    DB::table("withdraw_history")->where([['status','=',0],['id','=',$id]])->update(['status'=>3,'txid'=>'Email Verification Failed','updated_at'=>date('Y-m-d H:i:s', time())]);
                    BackController::logAction('withdrawal_reject',$withdraw,'Email Verification Failed');
                    $client=self::getStaticClient($currency);
                    $client->_get_balance($user_id);

                    //email通知客户提现被取消
                    DB::beginTransaction();
                    try{$lang = Cache::get('user_lang_'.$user_id);}
                    catch (\Exception $e){$lang = 'en';}
                    App::setLocale($lang);
                    $mail = User::find($user_id)->email;
                    Mail::send('email.withdraw_auto_cancel',['currency'=>$currency,'amount'=>$withdraw->amount],function($message) use ($mail){
                        $message->to($mail)->subject('Withdraw Email Timeout');
                    });
                    $email_res = Mail::failures();
                    if(count($email_res) > 0 )
                        DB::rollback();
                    DB::commit();
                }
            }
            DB::commit();
            echo "更新完成</br>";
        }catch (\Exception $e) {
            DB::rollback();
            echo "更新失败</br> ".$e->getMessage();
        }
    }
}