<?php
namespace App\Handlers;
use App\Handlers\jsonRPCClient;
use App\Http\Controllers\FrontEnd\ACXWalletController;
use App\Models\BalanceDB;
use App\Models\Blockchain;
use App\Models\DepositHistory;
use App\WithdrawHistory;
use App\WithdrawHistoryNode;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Client {
    private $uri;
    private $jsonrpc;
    private $cli_flag;
    private $prefix;
    //添加cli_flag
    function __construct($host, $port, $user, $pass,$cli_flag)
    {
        $flag = array('BTC','BCH','LTC','RPZ','ETH','XVG');
        $this->uri = "http://" . $user . ":" . $pass . "@" . $host . ":" . $port . "/";

        $this->cli_flag = $cli_flag;
        $this->jsonrpc = new jsonRPCClient($this->uri,false,$cli_flag);

        if (in_array($cli_flag,$flag)) {   //VIA修改前缀 。
            $cli_flag=strtolower($cli_flag);
            $this->prefix = 'ac'.$cli_flag;//
        }else{
            $cli_flag=strtolower($cli_flag);
            $this->prefix = 'ac'.$cli_flag;
        }
    }

    function getInfo()
    {
        if($this->cli_flag == 'BTC')
            $info = $this->jsonrpc->getwalletinfo();
        else
            $info = $this->jsonrpc->getinfo();
        return $info['balance'];
    }

    //--暂时调用其它获取余额方法，这个没有调用  这个是调用数据库余额
    function getBalance($user_session)
    {
        $confirmations = 0;
        switch ($this->cli_flag) {
            case 'BTC':
                $confirmations = 2;
                break;
            case 'LTC':
                $confirmations = 8;
                break;
            case 'BCH':
                $confirmations = 6;
                break;
            case 'RPZ':
                $confirmations = 6;
                break;
            case 'VIT':
                $confirmations = 6;
                break;
            case 'PIVX':
                $confirmations = 6;
                break;
            case 'DASH':
                $confirmations = 6;
                break;
            case 'XVG':
                $confirmations = 6;
                break;
        }
        //--修改获取余额方法

        return  self::get_db_balance($user_session);
        //  return $this->sctonum($this->jsonrpc->getbalance("$this->prefix" . $user_session, $confirmations));
        //return 21;
    }



    public function get_db_balance($userId){
        return BalanceDB::get_balance($this->cli_flag,$userId);

    }
    //--获取余额
    //userId    33
    //return 1.0
    public function _get_balance_18_8_8_old($userId){
        //--不查询公司账户余额

        //if(1==1) return 0;
        if($userId==1)return "1000000";//--默认100w锐币
        //--查询当前已提现余额
        $currency=$this->cli_flag;
        $currency=strtoupper($currency);
        $result = DB::table('withdraw_history')->where([
            ['currency', '=',$currency],
            ['user_id', '=', $userId],
            ['status', '<>', 3]
        ])->get();
        $fee_total=0;
        $withdraw_total=0;
        if($result){
            foreach ($result as $key=>$value){
                $withdraw_total= bcadd($withdraw_total,$value->amount,8);
                $fee_total= bcadd($fee_total,$value->max_fee,8);
            }
        }
        //  dump("手续费总额：$fee_total");
        //   dump("转账总额: $withdraw_total");
        $withdraw_total_aount=bcadd($fee_total,$withdraw_total,8);
        //   dump("提现总额:$withdraw_total_aount");


        $currency=mb_strtolower($currency);
        $currency_id=ACXWalletController::getMarkId($currency);
        //已充值总额
        $result = DB::table('deposit_history')->where([
            ['currency_id', '=',$currency_id],
            ['user_id', '=', $userId],
            ['status', '=', 1]
        ])->get();
        $black_deposit_total=0;
        if($result){
            foreach ($result as $key=>$value){
                $black_deposit_total= bcadd($black_deposit_total,$value->amount,8);
            }
        }
        //  if(Auth::id()==44){
        //      dump($currency);
        //      dump($currency_id);
        //        dump("充值总额:$black_deposit_total");
        //  }


        //--获取缓存余额
        $chaer_result= DB::table("yester_opt_balance")->where([ ['currency', '=',$currency],
            ['user_id', '=', $userId]])->first();
        $insert_time=0;
        $chaer=0;
        if($chaer_result){
            $chaer=$chaer_result->balance;
            $insert_time=$chaer_result->time;
        }
        //   dump($insert_time);
        //  dump($chaer);
        //if(1==1)return;
        //$insert_time= date("Y-m-d H:i:s",$insert_time);//把数字型时间按格式转换成时间格



        //当前交易差额
        //--支出
        $result = DB::table('blockchain_opt')->where([
            ['currency', '=',$currency],
            ['user_id', '=', $userId],
            ['created_at','>=',$insert_time]
        ])->get(['amount']);
        // dump($result);
        $black_zhichu_total=0;
        if($result){
            foreach ($result as $key=>$value){
                $black_zhichu_total= bcadd($black_zhichu_total,$value->amount,8);
            }
        }
        //  dump("支出总额:$black_zhichu_total");

        //--收入
        $result = DB::table('blockchain_opt')->where([
            ['currency', '=',$currency],
            ['target_id', '=', $userId],
            ['created_at','>=',$insert_time]
        ])->get(['amount']);
        $black_shouru_total=0;
        if($result){
            foreach ($result as $key=>$value){
                $black_shouru_total= bcadd($black_shouru_total,$value->amount,8);
            }
        }
        //   dump("收入总额:$black_shouru_total");



        $balance=bcsub($black_deposit_total,$withdraw_total_aount,8);
        $balance=bcsub($balance,$black_zhichu_total,8);
        $balance=bcadd($balance,$black_shouru_total,8);

        //--加上 计算好的差额
        $balance=bcadd($balance,$chaer,8);

        // dump("该用户的余额是： $balance");

        //--更新余额
        BalanceDB::set_balance($currency,$userId,$balance);
        //dump("余额:".$balance);
        return $balance;
        //我的可用额度等于 已充值总额-当前已提现余额-当前交易差额
    }


    //--不加缓存的余额
    public function _get_balance($userId,$check=false){
        //--不查询公司账户余额
        //if(1==1) return 0;
        if($userId==1)return "1000000";//--默认100w锐币
        //--查询当前已提现余额
        $currency=$this->cli_flag;
        $currency=strtoupper($currency);
        $articles = DB::select('select sum(amount + max_fee) as sum1 from withdraw_history where `currency`=:currency and `user_id`=:user_id and `status`<>:status  ',['user_id'=>$userId,'currency'=>$currency,'status'=>3]);
//   dump(DB::getQueryLog());
//   dump($articles);
//$trade[$trade_name] = $articles[0]->sum1;
        if(is_null($articles[0]->sum1)){
            $withdraw_total_aount = 0;
        }else{
            $withdraw_total_aount = $articles[0]->sum1;
        }
        /*   $result = DB::table('withdraw_history')->where([
               ['currency', '=',$currency],
               ['user_id', '=', $userId],
               ['status', '<>', 3]
           ])->get();
           $fee_total=0;
           $withdraw_total=0;
           if($result){
               foreach ($result as $key=>$value){
                   $withdraw_total= bcadd($withdraw_total,$value->amount,8);
                   $fee_total= bcadd($fee_total,$value->max_fee,8);
               }
           }
           //  dump("手续费总额：$fee_total");
           //   dump("转账总额: $withdraw_total");
           $withdraw_total_aount=bcadd($fee_total,$withdraw_total,8);*/
        //   dump("提现总额:$withdraw_total_aount");
        $currency=mb_strtolower($currency);
        $currency_id=ACXWalletController::getMarkId($currency);
        //已充值总额
        $articles = DB::select('select sum(amount) as sum1 from deposit_history where `currency_id`=:currency_id and `user_id`=:user_id and `status`=:status  ',['user_id'=>$userId,'currency_id'=>$currency_id,'status'=>1]);
//   dump(DB::getQueryLog());
//   dump($articles);
//$trade[$trade_name] = $articles[0]->sum1;
        if(is_null($articles[0]->sum1)){
            $black_deposit_total = 0;
        }else{
            $black_deposit_total = $articles[0]->sum1;
        }
        /* $result = DB::table('deposit_history')->where([
             ['currency_id', '=',$currency_id],
             ['user_id', '=', $userId],
             ['status', '=', 1]
         ])->get();
         $black_deposit_total=0;
         if($result){
             foreach ($result as $key=>$value){
                 $black_deposit_total= bcadd($black_deposit_total,$value->amount,8);
             }
         }*/

        //--获取缓存余额
        $chaer_result= DB::table("yester_opt_balance")->where([ ['currency', '=',$currency],
            ['user_id', '=', $userId]])->first();
        $insert_time=0;
        $chaer=0;
        if($chaer_result){
            $chaer=$chaer_result->balance;
            $insert_time=$chaer_result->time;
        }

        //当前交易差额
        //--支出

        $articles = DB::select('select sum(amount) as sum1 from blockchain_opt where `currency`=:currency and `created_at`>=:created_at and `user_id`=:user_id  ',['user_id'=>$userId,'currency'=>$currency,'created_at'=>$insert_time]);
        if(is_null($articles[0]->sum1)){
            $black_zhichu_total = 0;
        }else{
            $black_zhichu_total = $articles[0]->sum1;
        }

        /*
                $result = DB::table('blockchain_opt')->where([
                    ['currency', '=',$currency],
                    ['user_id', '=', $userId]
                ])->get(['amount']);
                $black_zhichu_total=0;
                if($result){
                    foreach ($result as $key=>$value){
                        $black_zhichu_total= bcadd($black_zhichu_total,$value->amount,8);
                    }
                }*/
        //  dump("支出总额:$black_zhichu_total");
        $articles = DB::select('select sum(amount) as sum1 from blockchain_opt where `currency`=:currency and `created_at`>=:created_at and `target_id`=:target_id  ',['target_id'=>$userId,'currency'=>$currency,'created_at'=>$insert_time]);
        if(is_null($articles[0]->sum1)){
            $black_shouru_total = 0;
        }else{
            $black_shouru_total = $articles[0]->sum1;
        }

        //--收入
        /*   $result = DB::table('blockchain_opt')->where([
               ['currency', '=',$currency],
               ['target_id', '=', $userId]
           ])->get(['amount']);
           $black_shouru_total=0;
           if($result){
               foreach ($result as $key=>$value){
                   $black_shouru_total= bcadd($black_shouru_total,$value->amount,8);
               }
           }*/
        //   dump("收入总额:$black_shouru_total");
        $balance=bcsub($black_deposit_total,$withdraw_total_aount,8);
        $balance=bcsub($balance,$black_zhichu_total,8);
        $balance=bcadd($balance,$black_shouru_total,8);

        //--加上 计算好的差额
        $balance=bcadd($balance,$chaer,8);

        //     dump("该用户的余额是： $balance");

        if($check == 1){
            dump('deposit_total:'.$black_deposit_total);
            dump('zhichu_total:'.$black_zhichu_total);
            dump('withdraw_total:'.$withdraw_total_aount);
            dump('shouru_total:'.$black_shouru_total);
            dump('opt_balance:'.$chaer);
            dump('total:'.$balance);
        }
        //--更新余额
        BalanceDB::set_balance($currency,$userId,$balance);
        //dump("余额:".$balance);
        return $balance;
        //我的可用额度等于 已充值总额-当前已提现余额-当前交易差额
    }

    //--不加缓存的余额
    public function _get_old_balance($userId){
        //--不查询公司账户余额
        //if(1==1) return 0;
        if($userId==1)return "1000000";//--默认100w锐币
        //--查询当前已提现余额
        $currency=$this->cli_flag;
        $currency=strtoupper($currency);
        $result = DB::table('withdraw_history')->where([
            ['currency', '=',$currency],
            ['user_id', '=', $userId],
            ['status', '<>', 3]
        ])->get();
        $fee_total=0;
        $withdraw_total=0;
        if($result){
            foreach ($result as $key=>$value){
                $withdraw_total= bcadd($withdraw_total,$value->amount,8);
                $fee_total= bcadd($fee_total,$value->max_fee,8);
            }
        }
        //  dump("手续费总额：$fee_total");
        //   dump("转账总额: $withdraw_total");
        $withdraw_total_aount=bcadd($fee_total,$withdraw_total,8);
        //   dump("提现总额:$withdraw_total_aount");
        $currency=mb_strtolower($currency);
        $currency_id=ACXWalletController::getMarkId($currency);
        //已充值总额
        $result = DB::table('deposit_history')->where([
            ['currency_id', '=',$currency_id],
            ['user_id', '=', $userId],
            ['status', '=', 1]
        ])->get();
        $black_deposit_total=0;
        if($result){
            foreach ($result as $key=>$value){
                $black_deposit_total= bcadd($black_deposit_total,$value->amount,8);
            }
        }
        //当前交易差额
        //--支出
        $result = DB::table('blockchain_opt')->where([
            ['currency', '=',$currency],
            ['user_id', '=', $userId]
        ])->get(['amount']);
        $black_zhichu_total=0;
        if($result){
            foreach ($result as $key=>$value){
                $black_zhichu_total= bcadd($black_zhichu_total,$value->amount,8);
            }
        }
        //  dump("支出总额:$black_zhichu_total");

        //--收入
        $result = DB::table('blockchain_opt')->where([
            ['currency', '=',$currency],
            ['target_id', '=', $userId]
        ])->get(['amount']);
        $black_shouru_total=0;
        if($result){
            foreach ($result as $key=>$value){
                $black_shouru_total= bcadd($black_shouru_total,$value->amount,8);
            }
        }
        //   dump("收入总额:$black_shouru_total");
        $balance=bcsub($black_deposit_total,$withdraw_total_aount,8);
        $balance=bcsub($balance,$black_zhichu_total,8);
        $balance=bcadd($balance,$black_shouru_total,8);

        //     dump("该用户的余额是： $balance");
        //--更新余额
        BalanceDB::set_balance($currency,$userId,$balance);
        //dump("余额:".$balance);
        return $balance;
        //我的可用额度等于 已充值总额-当前已提现余额-当前交易差额
    }


    function prefix()
    {

        return $this->prefix;
    //   if ($this->cli_flag == 'VIT') {
    //       return "nova";
    //   }else {
    //       return "falchat";
    //   }
    }
    function getSlaveBalance($user_session)
    {
        $confirmations = 0;
        switch ($this->cli_flag) {
            case 'BTC':
                $confirmations = 2;
                break;
            case 'LTC':
                $confirmations = 8;
                break;
            case 'BCH':
                $confirmations = 6;
                break;
            case 'RPZ':
                $confirmations = 6;
                break;
            case 'VIT':
                $confirmations = 6;
                break;
            case 'PIVX':
                $confirmations = 6;
                break;
            case 'DASH':
                $confirmations = 6;
                break;
            case 'XVG':
                $confirmations = 6;
                break;
        }
        return $this->sctonum($this->jsonrpc->getbalance("$this->prefix" . $user_session, $confirmations));
        //return 21;
    }

    //--暂时调用其它获取余额方法，这个没有调用
    function getAddress($user_session)
    {
        if($this->cli_flag==="USD"){
            return "usd_".uniqid();
        }
        return $this->jsonrpc->getaccountaddress("$this->prefix" . $user_session );
        //string
    }

    //--暂时调用其它获取余额方法，这个没有调用
    function getAddressList($user_session)
    {

        if($this->cli_flag==="USD"){
            $address[]="usd_".uniqid();
            return $address;
        }

        return $this->jsonrpc->getaddressesbyaccount("$this->prefix" . $user_session );
        //return array("1test", "1test");
    }

    //--暂时调用其它获取余额方法，这个没有调用
    function getTransactionList($user_session,$num=200)
    {
        return $this->jsonrpc->listtransactions("$this->prefix" . $user_session, $num);
        //array
    }
//--暂时调用其它获取余额方法，这个没有调用
    function getNewAddress($user_session)
    {
        if($this->cli_flag==="USD"){
            return "usd_".uniqid();
        }
        //	echo "indise add";
        return $this->jsonrpc->getnewaddress("$this->prefix" . $user_session);
        //return "1test";
    }
//--暂时调用其它获取余额方法，这个没有调用
    function withdraw($user_session, $address, $amount)
    {
        //Log::info($user_session.'-------------'.$address.'-------------'.$amount);
        $amount = $this->sctonum($amount);
        return $this->jsonrpc->sendfrom("$this->prefix" . $user_session, $address, $amount,1);
        //true or error json
        //return "ok wow";
    }


    //-------------
    //--调用了这个 每次移动都去判断钱是否足够
    function move($user_session,$other_account, $amount)
    {
        return  self::_get_balance($user_session)>=$amount;
        // return 1;
        //--暂时不调用 rpc 方法，只数据库计算
        // return $this->jsonrpc->move($this->prefix. $user_session ,$this->prefix. $other_account, (float)$amount);
        //return "ok wow";
    }

    //--获取btc所有接受的区块链信息 返回txid account  total confirms  array return
    function listreceivedbyaddress()
    {
        return $this->jsonrpc->listreceivedbyaddress();
    }


    //---公司账户移动 -暂时没人调用
    function slaveMove($user_session,$other_account, $amount)
    {
        return $this->jsonrpc->move($user_session,$this->prefix. $other_account, (float)$amount);
        //return "ok wow";
    }
    //验证地址
    function validateaddress($address)
    {
        return $this->jsonrpc->validateaddress($address);
        //return "ok wow";
    }

    function gettransactionDetail($txid)
    {
        return $this->jsonrpc->gettransaction($txid);
    }

    function sctonum($num, $double = 8){
        if(false !== stripos($num, "e")){
            $a = explode("e",strtolower($num));
            return bcmul($a[0], bcpow(10, $a[1], $double), $double);
        }else{
            return $num;
        }
    }

    function acWithdraw($address, $amount,$user_session,$max_fee)
    {
        $amount = $this->sctonum($amount);
        $move_total=bcadd($amount,$max_fee,8);
        $this->jsonrpc->move('withdraw', "$this->prefix" . $user_session, (float)$move_total);
        return $this->jsonrpc->sendfrom("$this->prefix" . $user_session, $address, $amount, 1);
    }


    //-获取所有用户
    public function listAccounts(){
        $info = $this->jsonrpc->listaccounts();
        return $info;
    }
}
?>