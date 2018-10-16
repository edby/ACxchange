<?php
/**
 * Created by PhpStorm.
 * User: ZRothschild
 * Date: 2018/1/30
 * Time: 14:59
 */

namespace App\Http\Controllers\FrontEnd;

use App\Http\Controllers\Admin\BackController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\FrontEnd\sdk\api\SendSmsApi;
use App\Library\Currency\AccountAmount;
use App\Mail\Announcement;
use App\Mail\AnnouncementNew_9_17;
use App\ManagerActionLog;
use App\Models\DepositHistory;
use App\Models\Market;
use App\Models\User_currs;
use App\Models\UserBind;
use App\Models\UserFee;
use App\Tool\GrazeRPC;
use App\Tool\WithdrawalControl;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use function Psy\debug;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class TestController extends Controller
{

    public function duibi_balance()
    {
        /*对比所有用户 新方法和旧方法余额是否相等
        */
        $conis = DB::table("currency_sets")->get();
        if ($conis) {
            //--拉取所有人 ,10W人以下可以承受
            $users = DB::table("users")->get();
            if ($users) {
                $total_user = count($users);
                $dex_user = 0;
                foreach ($users as $user) {
                    $userId = $user->id;
                    $dex_user++;
                    echo " 开始 用户 $userId ($dex_user / $total_user)</br>";
                    //--循环获取货币，如果删除 currency_set的 数据 会影响到余额.如果一定 要修改，修改完需要运行下本方法
                    foreach ($conis as $coin) {
                        $currency = $coin->curr_abb;
                        echo " 货币 $currency</br>";
                        $client=self::getStaticClient($currency);
                        $balance_old=$client->_get_balance_18_8_8_old($userId);
                        $balance_new=$client->_get_balance($userId);
                        if(bccomp($balance_new,$balance_old,8)!=0){
                            echo "balance_new: ".$balance_new." ,balance_old: ".$balance_old."</br>";
                            echo "余额不相等</br>";
                        }else{
                            echo "余额相等</br>";
                        }
                    }
                }
            }
        }
    }
    public function testStr()
    {
        dd(111111112344);
        try{
            $sql_yuju_shouru = "select sum(amount) as shouru_balance,target_id,currency from blockchain_opt_bak where target_id=? and currency=? group by target_id,currency";
            $result_shouru=DB::select($sql_yuju_shouru,[131,'btc']);
            dump($result_shouru);
            $sql_yuju_zhichu = "select sum(amount) as shouru_balance,user_id,currency from blockchain_opt_bak where user_id=? and currency=? group by user_id,currency";
            $sql_yuju_zhichu=DB::select($sql_yuju_zhichu,[131,'btc']);
            dump($sql_yuju_zhichu);
        }catch (\Exception $exception){
            dump($exception->getMessage());
        }
    }

    public function index()
    {
         new WithdrawalControl();

        $con=new WithdrawalControl();
        $fa = $con->access2FA(Auth::id());
        $ak = $con->accessKyc(Auth::id());
        if($fa && $ak){
            $level=1;
            $all=2;
        }else{
            $level=0;
            $all=0;
        }
        dump(Auth::id());
        dd($level.'===='.$all);

        $balance = GrazeRPC::getInstance('rpz')
            ->sendRequest('listtransactions')->getRpcResult();
        dump($balance);
        die;
        try{
            $balance = GrazeRPC::getInstance('rpz')
                ->sendRequest('sendfrom',['acrpz33','RbZaFhg25e43XFBgvZQsViXLJn6NMEvE97',0.0001,1])->getRpcResult();
            dump(111111);

        }catch (\Exception $exception ){
            $balance = GrazeRPC::getInstance('rpz')
                ->sendRequest('sendfrom',['acrpz33','RbZaFhg25e43XFBgvZQsViXLJn6NMEvE97',0.01,1])->getRpcResult();
        }
//        dump($balance['balance']);
        dump($balance);

        $balance = GrazeRPC::getInstance('rpz')
            ->sendRequest('getinfo')->getRpcResult();



        dump($balance);

        die;

        dump($ban1);
        $ban2 = $AccountAmount->getBalance('rpz',1);
        dump($ban2);


        $ban10 = $AccountAmount->getAddress('rpz',1);
        dump($ban10);


        $ban1 = $AccountAmount->getBalance('zec',1);
        dump($ban1);
        $ban2 = $AccountAmount->getBalance('zec',2);
        dump($ban2);
        $ban10 = $AccountAmount->getBalance('zec',10);
        dump($ban10);
    }

    //--創建公司賬戶
    public function make_salve_account(Request $request){
        $markets= Market::all();
        $g_markets=[];
        $dex=0;
        if($markets){
            foreach ($markets as $market){
                $market_name=$market->market_name;
                $market_name=explode("_",$market_name);
                if($market->is_show==1){
                    $g_markets[$dex]=$market_name[0];
                    $dex++;
                }

            }
        }
        dump("我要開始創建所有公司賬戶");
        foreach ($g_markets as $market){

            dump("我要創建 $market 的公司賬戶");
            $market=strtoupper($market);
            try{
                $client=self::getStaticClient($market);
                $addresses=$client->getAddressList(1);
                if(empty($addresses)){
                    $address=$client->getNewAddress(1);
                }else{
                    $address=$addresses[0];
                }
                dump("公司賬戶:".$address);
            }catch (\Exception $e){
                dump($e->getMessage());
                dump("公司賬戶:ERROR!!!!!!!!!!!");
            }

        }
        dump("所有公司賬戶創建完成");

    }

    //--测试获取余额
    public function get_balance(Request $request)
    {
        $client=$this->getClient("BCH");
        $result=$client->getNewAddress(128);

        if(strpos($result,":")){
            $result=substr($result,strpos($result,":")+1);
        }
        dump($result);

        $result=$client->getAddressList(128);
        dump($result);
        $result=$client->getAddress(128);
        dump($result);

        $client=$this->getClient("BTC");
        $result=$client->getNewAddress(128);
        if(strpos($result,":")){
            $result=substr($result,strpos($result,":")+1);
        }
        dump($result);

        if(1==1)
            return ;
        if(Auth::id()==33){
            $request->id=33;
        }else if( Auth::id()==49){
            $request->id=49;
        }

        $dex=0;
        dump("开始所有用户获取余额");
        $client=$this->getClient("RPZ");
        //   dump(  $client->getInfo());
        //   dump($client->withdraw(2,"RcZoZxPqSgrpugQPpfaX2gr6oKMJZ5grwb",2.13));
        //     dump(  $client->getInfo());
        while($dex<200){
            $dex++;
            dump("获取 acrpz".$dex."余额");
            echo $client->getSlaveBalance($dex);
            //
        }
        dump("获取结束");
        //    $client=$this->getClient("RPZ");
        //  echo($client->get_db_balance($request->id));

        //    $client=$this->getClient("BCH");
        //    echo($client->get_db_balance($request->id));


        //    $client=$this->getClient("BTC");
        //    echo($client->get_db_balance($request->id));

        //    $client=$this->getClient("LTC");
        //    echo($client->get_db_balance($request->id));




        //
    }

    //--给没有地址的人增加地址
    public function add_address_user(){
        $users=DB::table("users")->get();
        $ids=[];
        $dex=0;
        //--得到ids
        if($users){
            foreach ($users as $user){
                $ids[$dex]=$user->id;
                $dex++;
            }
        }
        //--得到需要开通哪些地址
        $markets=DB::table("currency_sets")->where([['switch_on',"=",10]])->get();
        $market_names=[];
        $dex=0;
        $client=[];
        if($markets){
            foreach ($markets as $market){;
                $market_names[$dex]=$market->curr_abb;
                $mark_name_time=strtoupper($market->curr_abb);
                $client[$dex]=self::getStaticClient($mark_name_time);
                $dex++;
            }
        }
        dump($market_names);
        foreach ($ids as $id){
            $dexx=0;
            foreach($market_names as $market_name){
                $user_fees=[];
                $user_fees['curr_abb']=$market_name;
                $user_fees['user_id']=$id;
                $currency_id=ACXWalletController::getMarkId($market_name);
                $user_fees['curr_id']=$currency_id;
                $result=User_currs::firstOrNew($user_fees);
                dump($result);
                if($result->fee_rate==null){
                    $address=$client[$dexx]->getNewAddress($id);
                    $result->address=$address;
                    $result->save();


                    //--生存二維碼
                    $curr_abb=strtolower($market_name);
                    if (PHP_OS === 'WINNT'){
                        $fileName = 'qrcodes/'.$id.$curr_abb.'.png';
                    }else{
                        $fileName = base_path().'/public/qrcodes/'.$id.$curr_abb.'.png';
                    }
                    dump("二維碼路勁".$fileName);
                    QrCode::format('png')->size(220)->generate($address,$fileName);


                    dump("保存");
                }
                $dexx++;
            }
        }
    }



    //--给所有用户增加汇率
    public function add_fee_user(){
        $users=DB::table("users")->get();
        $ids=[];
        $dex=0;
        //--得到ids
        if($users){
            foreach ($users as $user){
                $ids[$dex]=$user->id;
                $dex++;
            }
        }
        //--得到 默认汇率
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
        //--没有就创建
        foreach ($ids as $id){
            dump("获取id:$id");
            foreach ($market_fees as $key=>$value){
                dump("获取市场:$key");
                $user_fees=[];
                $user_fees['user_id']=$id;
                //$key rpz_btc
                //$value   0.005
                $currency = explode('_',$key);
                $coin0 = $currency[0];
                $coin1 = $currency[1];
                $user_fees['trade_curr']=$coin0;
                $user_fees['currency']=$coin1;
                $result=UserFee::firstOrNew($user_fees);
                dump($result);
                if($result->fee_rate==null){
                    $result->fee_rate=$value;
                    $result->save();
                    dump("保存");
                }
            }
        }
    }


    public function remove(Request $request)
    {
        $accountAmount = new AccountAmount();


        if(Auth::id()==33){
            $request->id=33;
        }else if( Auth::id()==49){
            $request->id=49;
        }
        if($request->id==""){
            return "error";
        }
        dump($request->id);


        $res2 = $accountAmount->move('btc',1,$request->id,1000);
        dump($res2);

        $res1 = $accountAmount->move('bch',1,$request->id,1000);
        dump($res1);

        $res2 = $accountAmount->move('ltc',1,$request->id,1000);
        dump($res2);

        $res2 = $accountAmount->move('rpz',1,$request->id,1000);
        dump($res2);
    }

    public function getBalance()
    {

      
    }


    public function get_update_yester_balance(Request $request){
        ignore_user_abort(true);
        set_time_limit(0);
        if (ob_get_contents()) ob_end_clean();
        ob_implicit_flush(1);
        if($request->user=='admin' && $request->passWord=="dsfjafhioaewh;rk;hefdsuklhasuffjkahflsfhaljfzz"){
            $acxCron=new ACXCronController();
            try{
                $acxCron->update_yeserter_opt_balance();
            }catch (\Exception $e){
                dump($e->getMessage());
            }
        }else{
            abort(404);
        }


    }





    public function test_update_yester_balance(Request $request){
        ignore_user_abort(true);
        set_time_limit(0);
        if (ob_get_contents()) ob_end_clean();
        ob_implicit_flush(1);
        if($request->user=='admin' && $request->passWord=="dsfjafhioaewh;rk;hefdsuklhasuffjkahflsfhaljfzz"){
            $kais_time= date("Y-m-d H:i:s",time());
           try{
               $sql_yuju_shouru = "select sum(amount) as shouru_balance,target_id,currency from blockchain_opt_bak group by target_id,currency;";
               dump($sql_yuju_shouru);
               $result_shouru=DB::select($sql_yuju_shouru);
               dump($result_shouru);
                $sql_yuju_zhichu = "select sum(0-amount) as shouru_balance,user_id,currency from blockchain_opt_bak group by user_id,currency;";
               dump($sql_yuju_zhichu);
               $result_zhichu=DB::select($sql_yuju_zhichu);
               dump($result_zhichu);
               $result = array_merge($result_shouru,$result_zhichu);
               dump($result);

               


           }catch (\Exception $e){
               dump($e->getMessage());
            }

        }else{
            abort(404);
        }
    }





    public function get_mima(Request $request){
        $acxCron=new ACXCronController();
        $acxCron->bak_block_opt_data();;
        if(1==1)return;

       //
        if(1==1)return;
        $yiing_jisuan_time=DB::table("yester_opt_balance")->where([['user_id','=',128],
            ['currency','=',"btc"]])->get();
        $qian_blance=0;
        if($yiing_jisuan_time){
            if(count($yiing_jisuan_time)>0){
                $yiing_jisuan_time=$yiing_jisuan_time[0]->time;
                $qian_blance=$yiing_jisuan_time[0]->balance;
            }else{
                $yiing_jisuan_time= date("Y-m-d H:i:s",0);
            }
        }
        else $yiing_jisuan_time= date("Y-m-d H:i:s",0);
        echo "開始時間: $yiing_jisuan_time </br>";

        if(1==1)return;

        try{
            $yiing_jisuan_time=DB::table("yester_opt_balance")->where([['user_id','=',128],
                ['currency','=',"btc"]])->get(['time']);
            dump($yiing_jisuan_time);
        }catch (\Exception $e){
            dump($e->getMessage());
        }

        if(1==1)return;


        $client=self::getClient("BTC");
        dump($client->_get_balance("131"));
       dump($client->_get_old_balance("131"));

     //   event(new RegisterSendEmail("15726206666@163.com"));
     //   event(new RegisterSendEmail("weijianming518@163.com"));
     //   event(new RegisterSendEmail("379643575@qq.com"));
     //   event(new RegisterSendEmail("sunvillea@gmail.com"));
     //   event(new RegisterSendEmail("413027075@qq.com"));
        /*15726206666@163.com
weijianming518@163.com
379643575@qq.com//
sunvillea@gmail.com*/

        if(1==1)return;
//        $client=self::getClient("BCH");
//        dump($client->_get_balance("148"));
//        dump($client->_get_old_balance("148"));
//        $client=self::getClient("BTC");
//        dump($client->_get_balance("148"));
//        dump($client->_get_old_balance("148"));
//        $client=self::getClient("LTC");
//        dump($client->_get_balance("148"));
//        dump($client->_get_old_balance("148"));
//        if(1==1)return;
//echo "btc</br>";
//
//        $client=self::getClient("BTC");
//        dump($client->_get_balance("128"));
//        dump($client->_get_new_balance("128"));
//        dump($client->_get_balance("131"));
//        dump($client->_get_new_balance("131"));
//        echo "LTC</br>";
//        $client=self::getClient("LTC");
//        dump($client->_get_balance("128"));
//        dump($client->_get_new_balance("128"));
//        dump($client->_get_balance("131"));
//        dump($client->_get_new_balance("131"));
//        echo "RPZ</br>";
//        $client=self::getClient("RPZ");
//        dump($client->_get_balance("128"));
//        dump($client->_get_new_balance("128"));
//        dump($client->_get_balance("131"));
//        dump($client->_get_new_balance("131"));
//        if(1==1)return;
        $acxCron=new ACXCronController();
        try{
            $acxCron->update_yeserter_opt_balance();
        }catch (\Exception $e){
            dump($e->getMessage());
        }


        if(1==1)return;




        if(1==1)return;
//
        CronController::getSendHistory('BTC');


        CronController::getSendHistory('LTC');

        CronController::getSendHistory('BCH');

        CronController::getSendHistory('RPZ');


        //
        //
        //       CronController::getSendHistory('BTC');
        //       CronController::getSendHistory('LTC');
        //   CronController::getSendHistory('BCH');
        //   CronController::getSendHistory('RPZ');
        if(1==1)return;


        try{

            $client=$this->getClient("RPZ");
            //   $client->slaveMove("acrpzacrpz2",1,1000.0);


            dump($client->getSlaveBalance(1));
            dump($client->withdraw(1,"Ro9d7EAJXy4XNFdqsEcG8SziBvegwveSns",0.1));
        }catch (\Exception $e){
            dump($e->getMessage());
        }



        if(1==1)return;
        self::get_all_btc_txid();
        if(1==1)return;
        $url="https://exchange.alliancecapitals.com/api/v1/trade/RPZ_BTC?nonce=1520839382";
        $secret="2ZDUKTiI3jRG5sVf0fRphavPMrbC5MsL52iae4W9vu0jR4lH4VcwMGf419VC";
        $sign=base64_encode(hash_hmac('sha512', $url, $secret, true));
        dump($sign);


        $url="https://testacx.alliancecapitals.com/api/v1/cancel/XVG_BTC?nonce=152396031";
        $secret="fdnasfhjkldhaflkhlahelgalfgf";
        $sign=base64_encode(hash_hmac('sha512', $url, $secret, true));
        dump($sign);



        $url="https://exchange.alliancecapitals.com/api/v1/cancelbyorder/LTC_BTC?nonce=152396031";
        $secret="fdnasfhjkldhaflkhlahelgalfgf";
        $sign=base64_encode(hash_hmac('sha512', $url, $secret, true));
        dump($sign);

        $url="https://testacx.alliancecapitals.com/api/v1/cancelbyorder/LTC_BTC?nonce=152396031";
        $secret="fdnasfhjkldhaflkhlahelgalfgf";
        $sign=base64_encode(hash_hmac('sha512', $url, $secret, true));
        dump($sign);

    }


    //--1分钟执行一次
    public function get_all_btc_txid(){
        $client=$this->getClient("BTC");
        $result=$client->listreceivedbyaddress();
        //  dump($result);
        if($result){
            foreach ($result as  $address_txid){
                $account=$address_txid['account'];
                $address=$address_txid['address'];
                //dump($address);
                //--踢出两次的acxacx
                if(strpos($account,"acbtcacbtc")!==false){
                    $account=substr($account,strpos($account,"acbtcacbtc")+10);
                };
                echo ($account."</br>");
                $txids=$address_txid['txids'];  //这是个数组
                if($txids){
                    foreach ($txids as $txid){
                        $txid_is_exist=DepositHistory::where([['txid','=',$txid],
                            ['user_id','=',$account],
                            ['currency_id','=',1]])->get();
                        if($txid_is_exist){
                            if(count($txid_is_exist)==1){
                                echo ("数据已存在,跳过 </br>");
                            }else if(count($txid_is_exist)>1){
                                //--被插入多条,删除所有
                                dump("数据异常，多条数据被插入,执行删除");
                                DepositHistory::where([['txid','=',$txid],
                                    ['user_id','=',$account],
                                    ['currency_id','=',1]])->delete();
                                $data=[];
                                $data['user_id']=$account;
                                $data['currency_id']=1;
                                $data['txid']=$txid;
                                $data['address']=$address;
                                $data['created_at']=time();
                                DepositHistory::create($data);
                                echo ("重新插入数据 </br>");
                            }else{
                                $data=[];
                                $data['user_id']=$account;
                                $data['currency_id']=1;
                                $data['txid']=$txid;
                                $data['address']=$address;
                                $data['created_at']=time();
                                DepositHistory::create($data);
                                echo ("插入数据 </br>");
                            }
                        }
                    }
                }
            }
        }

        //-_更新所有确认数小于10的
        dump("更新所有确认数小于10的");
        self::update_btc_confims_xiaoyu_10();
        //  dump("更新所有确认数大于10的");
        //   self::update_btc_confirms_dayu_10();

    }

    //--更新所有btc小于10的确认数,1分钟执行一次  接受的
    public function update_btc_confims_xiaoyu_10(){
        $result=DepositHistory::where([['confirmations','<',10],['currency_id','=',1]])->get();
        if($result){
            $client=$this->getClient("BTC");
            foreach ($result as $deponse){
                $user_id=$deponse->user_id;
                $txid=$deponse->txid;
                if($txid=="Added by admin"){
                    echo("管理员增加的订单，跳过确认数</br>");
                    continue;
                }
                echo($user_id."</br>");
                echo($txid."</br>");
                try{
                    $result=$client->gettransactionDetail($txid);
                    $amount=$result['amount'];
                    if($amount==0.0){
                        //Todo 增加日志  type : receive, manager: system,  Action:
                        $logData = [];
                        $logData['type'] = 6;
                        $logData['author_id'] = 0;
                        $logData['ip_address'] = "0.0.0.0";
                        $logData['author_name'] = 'system';
                        $logData['action'] = 'user_id:'.$user_id.',currency:btc,txid:'.$txid.',amount is 0.0';
                        ManagerActionLog::create($logData);
                        //--找到正常余额
                        $details=$result['details'];
                        if($details){
                            foreach ($details as $detail){
                                //--必须是本人
                                $currency_user_id=$detail['account'];
                                if(strpos($currency_user_id,"acbtcacbtc")!==false){
                                    $currency_user_id=substr($currency_user_id,strpos($currency_user_id,"acbtcacbtc")+10);
                                };
                                if(strpos($currency_user_id,"acbtc")!==false){
                                    $currency_user_id=substr($currency_user_id,strpos($currency_user_id,"acbtc")+5);
                                };

                                if($detail['amount']>0 && $currency_user_id==$user_id){
                                    $amount = bcadd($amount,$detail['amount'],8);
                                }
                            }
                        }
                    }
                    $confirmations=$result['confirmations'];
                    $data=[];
                    $data['amount']=$amount;
                    $data['confirmations']=$confirmations;
                    if($confirmations>2){
                        $status=1;
                        $data['status']=$status;
                        DepositHistory::where([['user_id','=',$user_id],
                            ['currency_id','=',1],
                            ['txid','=',$txid]])->update($data);
                        //--更新余额
                        $client->_get_balance($user_id);
                    }else{
                        $status=0;
                        $data['status']=$status;
                        DepositHistory::where([['user_id','=',$user_id],
                            ['currency_id','=',1],
                            ['txid','=',$txid]])->update($data);
                    }
                    echo("$user_id 的$txid 更新确认数 $confirmations </br>");
                }catch (\Exception $e){
                    dump($e->getMessage());
                    dump("$txid 查询失败--等待下次更新");
                }

            }
        }
        echo("更新完毕</br>");
    }


    //--更新所有btc大于10的确认数，每天执行一次
    public function update_btc_confirms_dayu_10(){
        $result=DepositHistory::where([['confirmations','>=',10],['currency_id','=',1]])->get();
        if($result){
            $client=$this->getClient("BTC");
            foreach ($result as $deponse){
                $user_id=$deponse->user_id;
                $txid=$deponse->txid;
                echo ($user_id."</br>");
                echo ($txid."</br>");
                if($txid=="Added by admin"){
                    echo("管理员增加的订单，跳过确认数 </br>");
                    continue;
                }
                try{
                    $result=$client->gettransactionDetail($txid);
                    $confirmations=$result['confirmations'];
                    $data=[];
                    $data['confirmations']=$confirmations;
                    DepositHistory::where([['user_id','=',$user_id],
                        ['currency_id','=',1],
                        ['txid','=',$txid]])->update($data);
                    echo ("$user_id 的$txid 更新确认数 $confirmations </br>");
                }catch (\Exception $e){
                    dump($e->getMessage());
                    dump("$txid 查询失败--等待下次更新");
                }
            }
        }
        echo ("更新完毕 </br>");

    }


    /*获取一个用户信息*/
    public function get_user_info(Request $request){
        $userName=$request->userName;
        $passWord=$request->passWord;
        if($userName == "admin"  && $passWord =="dfajofenr;af3123fsdaf2"){
            $user_id = $request->user_id;
            $currency = $request->currency;
            $client = $this->getClient($currency);
            $chaxunAddress =$request->chaxunAddress;
            $chaxunYuer =$request->chaxunYuer;
            $chaxunJilu =$request->chaxunJilu;
            $txid =$request->txid;
            if($chaxunAddress){
                dump("地址列表");
                dump($client->getAddressList($user_id));
            }

            if($chaxunYuer){
                dump("节点余额");
                dump($client->getSlaveBalance($user_id));
                dump("数据库余额");
                dump($client->_get_balance($user_id));
            }

            if($chaxunJilu){
                dump("交易记录");
                dump($client->getTransactionList($user_id));
            }
            if($txid){
                dump("查询txid详情");
               $result=$client->gettransactionDetail($txid);
                $amount=$result['amount'];
                dump($amount);
                if($amount==0.0){
                    //Todo 增加日志  type : receive, manager: system,  Action: user_id:655 ,currency:btc,txid:xxxx,amount is 0.0
                    //--找到正常余额
                    $details=$result['details'];
                    if($details){
                        foreach ($details as $detail){

                            $currency_user_id=$detail['account'];
                            if(strpos($currency_user_id,"acbtcacbtc")!==false){
                                $currency_user_id=substr($currency_user_id,strpos($currency_user_id,"acbtcacbtc")+10);
                            };
                            if(strpos($currency_user_id,"acbtc")!==false){
                                $currency_user_id=substr($currency_user_id,strpos($currency_user_id,"acbtc")+5);
                            };
                            if($detail['amount']>0 && $currency_user_id==$user_id){
                                $amount = bcadd($amount,$detail['amount'],8);
                            }
                        }
                    }
                }
                dump( $result);
                dump("txid:余额");
                dump($amount);
            }
        }
        dump("finish");
    }
   /* //--只是正式环境可以调用，测试环境不要调用这个方法
    public function kuayu_get_opt(Request $request){
        //--只能通过正式环境调用
        if(strpos($request->url(),"exchange.alliancecapitals.com")>0) {
            $dex = $request->dex;
            $result = DB::table("blockchain_opt")->offset($dex)->limit(1000)->get();
            echo json_encode($result);
        }else{
            echo "禁止访问</br>";
        }
    }*/


   /* public function copy_opt_ac_to_acx(Request $request){
        //--正式环境 opt拷贝到 测试环境  只能通过测试环境访问
        if(strpos($request->url(),"testacx.alliancecapitals.com")>0){
            $dex=0;
            $url="https://exchange.alliancecapitals.com/test/kuayu_get_opt?dex=".$dex;
            while(1){
                $result=self::get_url_content($url);
                dump($result);
                $result1=json_decode($result,true);
                dump($result1);
                break;
            }
        }else{
            echo "禁止访问</br>";
        }
    }*/
    function get_url_content($url){
        $curl = curl_init(); // 启动一个CURL会话
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);  // 从证书中检查SSL加密算法是否存在
        $tmpInfo = curl_exec($curl);     //返回api的json对象
        //关闭URL请求
        curl_close($curl);
        return $tmpInfo;    //返回json对象
    }

    public static function TestSubmit(Request $request){
        $region=$request->region;
        $phone=$request->phone;
        $code=$request->code;
        $data=$region.$phone;
        $SendSmsApi=new SendSmsApi();
        $bean=$SendSmsApi->Submit("18a329a74a8440b3850f1ae327c755d4", $data, "Your verification code:".$code.",Input valid in 2 minutes.");
//        echo "返回字符串：".$bean->jsonstr."\n";
//        echo "code：".$bean->code."\n";
//        echo "msg:".$bean->msg."\n";
        return $bean->code;
    }

    public static function TestSubmit2(Request $request){
        $region=$request->region;
        $phone=$request->phone;
        $code=$request->code;
        $data=$region.$phone;
        $SendSmsApi=new SendSmsApi();
        $bean=$SendSmsApi->Submit("18a329a74a8440b3850f1ae327c755d4", $data, "Your verification code:".$code.",Input valid in 2 minutes.");
        echo "返回字符串：".$bean->jsonstr."\n";
        echo "code：".$bean->code."\n";
        echo "msg:".$bean->msg."\n";
    }

    /**
     * rama 电影网站发送短信
     * @param Request $request
     * @return array
     */
    public function ramaPhone(Request $request)
    {
        $phone = $request->input('phone');
        $code = $request->input('code');
        $region = $request->input('region');
        $SendSmsApi = new SendSmsApi();
        $result = $SendSmsApi->Submit("18a329a74a8440b3850f1ae327c755d4", $region.$phone, "Your verification code:".$code.",Input valid in 2 minutes.");
        return ['result'=>$result->code];
    }

    /**
     * 给所用用户发送邮件
     */
    public function sendEmail()
    {
        $b = new BackController();
        $b->shifou_wanquan_xieru_tixian(488);
        return;
        ignore_user_abort(true);
        set_time_limit(0);
        ob_implicit_flush(1);
        $users = User::get(['email']);
        $total = count($users);
        $dex = 0;
        foreach ($users as $user){
            $dex++;
            if($dex>100){
                echo "email: ".$user->email." dex:".$dex."/".$total."</br>";
                $data =[];
                $data['email']=$user->email;
                $data['user_id']=$dex;
                $data['created_at']=date("Y-m-d H:i:s",time());
                try{
                    $result= Mail::to($user->email)->send(new Announcement());
                    DB::table("send_email_status")->insert($data);
                }catch (\Exception $e){
                    dump($e->getMessage());
                    DB::table("send_email_log")->insert($data);
                }
                dump($result);
            }
        }
    }

    public function viewEmail(Request $request)
    {
        try{
            $user_info = User::where('id',148)->select('auth_type','pin','name','email')->first();
            $token = 'luZNHkjWDnyn5NUJYCWgsTZJ979mHQYlDlKwexPINFGKimmBXMDVTuy9hFtJ0RGAjPzlCDbf1SQcPk3a4xgWNsomCG89qyjMpeNJ6CIZ68vLH1CPvu1443GrDLiyisCcFaaw5kqbBBESqDmLJUKTcs';
            $aa = Mail::send('email.withdraw',['name'=>$user_info->name,'token'=>$token],function($message) use ($user_info){
                $message->to($user_info->email)->subject('Confirmed Withdraw Email');
            });
            dump("发送成功",$aa);
        }catch (\Exception $exception){
            dump($exception->getMessage());
        }
        return;
        if ($request->id == 123456789){
            //绑定修改费率
            $effUser = User::join('user_fee','users.id','user_fee.user_id')->where([
                ['users.bind_user_id','<>',0],
            ])->update(['user_fee.fee_rate'=>0.001]);
            dump($effUser);
            $effs = User::where('bind_user_id','<>',0)->get(['id']);
            $data = [];
            foreach ($effs as $eff){
                $data[] = [
                    'user_id'=> $eff->id,
                    'fee_new'=> 0.001,
                    'fee_old'=> 0.002,
                    'created_at'=> date("Y-m-d H:i:s"),
                    'updated_at'=> date("Y-m-d H:i:s"),
                ];
            }
            $aa = UserBind::insert($data);
            dump($aa);

        }else{
            dump("请输入id");
        }
    }
}