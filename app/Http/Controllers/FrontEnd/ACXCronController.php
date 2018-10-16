<?php


namespace App\Http\Controllers\FrontEnd;

use App\Handlers\Client;

use App\Http\Controllers\Controller;
use App\Mail\ChangeEmail;
use App\Mail\PasswordReset;
use App\Models\BalanceDB;
use App\Models\Currency;
use App\Models\DepositHistory;
use App\Models\Yester_Opt_Balance_Model;
use App\User;
use App\UserCurr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class ACXCronController extends Controller
{


    private static function get_confirms_huobi($currency)
    {
        $currency=strtoupper($currency);
        switch ($currency){
            case "BTC":
                return 2;
            case "LTC":
                return 8;
            case "RPZ":
                return 6;
            case "BCH":
                return 6;
            default:
                return 6;
        }
    }


    public function getNewRec()
    {
        $client = $this->getClient('BTC');
        $listRec = $client->listreceivedbyaddress();
        foreach ($listRec as $key => $lists){
            $data = [];
            foreach ($lists['txid'] as $list){
                preg_match_all('/(\d+)$/',$lists['account'],$str);
                $bool = DepositHistory::where([
                    ['user_id','=',$str[0]],
                    ['txid','=',$list],
                ])->exists();
                if ($bool)  continue;

                $currId = UserCurr::where('address',$list)->value('curr_id');
                $data[] = [
                    'txid'=>$list,
                    'user_id'=>$str[0],
                    'amount'=>$lists['amount'],
                    'address'=>$lists['address'],
                    'created_at'=>date('Y-m-d H:i:s',time()),
                    'currency_id'=>$currId,
                ];
            }
            DepositHistory::insert($data);
        }
    }

    //
    public function sixConfirmations()
    {
        $sixConfirmations = DepositHistory::where([
            ['confirmations','<',10],
        ])->get();
        $this->confirmations($sixConfirmations,'BTC');
    }

    //
    public function tenConfirmations()
    {
        $tenConfirmations = DepositHistory::where([
            ['confirmations','>',10],
        ])->get();
        $this->confirmations($tenConfirmations,'BTC');
    }


    private function confirmations($confirmations,$currency)
    {
        $data = [];
        $client = $this->getClient($currency);
        foreach ($confirmations as $key => $value){
            $data[$value->user_id][] =  ['txid'=>$value->txid,'id'=>$value->id];
        }

        foreach ($data as $userId => $value){
            $transHistory = $client->getTransactionList($client->prefix().$userId);
            foreach ($value as $ke => $val){
                foreach ($transHistory as $k => $v){
                    if (($v['category'] != 'receive') ) continue;
                    if ($v['txid'] != $val['txid']) continue;
                    if($v['confirmations']>=self::get_confirms_huobi($currency)){
                        $updateData = ['status'=>1,'updated_at'=>date('Y-m-d H:i:s', time()),'confirmations'=>$v['confirmations']];
                        DepositHistory::where('id',$val['id'])->update($updateData);
                        //--重新计算余额
                        $client->_get_balance($userId);
                    }
                }
            }
        }
    }

    public function getReceiveHistory()
    {
        try{
            dump("开始获取所有用户信息") ;
            $user = User::all();
            $currency = Currency::where('is_show', 1)->select('currency', 'id')->get();
            foreach ($currency as $coin) {

                if($coin->currency=='BTC'){
                    try{
                        $testController=new TestController();
                        $testController->get_all_btc_txid();
                    }catch (\Exception $exception){
                        dump($coin->currency);
                        dump($exception->getMessage());
                    }
                    continue;
                }
                if($coin->currency=='USD'){
                    continue;
                }
                try{
                    $client = $this->getClient($coin->currency);
                }catch (\Exception $exception){
                    dump($coin->currency);
                    dump($exception->getMessage());
                }

                foreach ($user as $u) {
                    dump("获取用户".$u->id."的".$coin->currency."交易记录") ;
                    $trans_history = $client->getTransactionList($client->prefix().$u->id);
                    if ($trans_history) {
                        //   dump($trans_history);
                        foreach ($trans_history as $key => $value) {
                            if (($value['category'] == 'receive') ) {
                                if((DepositHistory::where('txid', $value['txid'])->count()) == 0){
                                    $time = date('Y-m-d H:i:s', time());
                                    $deposit = new DepositHistory();
                                    $deposit->user_id = $u->id;
                                    $deposit->currency_id = $coin->id;
                                    $deposit->amount = $value['amount'];
                                    $deposit->address = $value['address'];
                                    $deposit->txid = $value['txid'];
                                    $deposit->timereceived = date('Y-m-d H:i:s', $value['timereceived']);
                                    $deposit->created_at = $time;
                                    $deposit->updated_at = $time;

                                    if($value['confirmations']>=self::get_confirms_huobi($coin->currency)){
                                        $deposit->status=1;
                                        //--重新计算余额
                                        $client->_get_balance($u->id);
                                    }else{
                                        $deposit->status=0;
                                    }
                                    $deposit->save();
                                    dump("记录保存 txid:".$value['txid']) ;
                                }else{
                                    if($value['confirmations']>=self::get_confirms_huobi($coin->currency)){
                                        $current_status=1;
                                        //--重新计算余额
                                        //--重新计算余额
                                        $client->_get_balance($u->id);
                                    }else{
                                        $current_status=0;
                                    }
                                    dump("修改txid: ".$value['txid']." 的状态为:".$current_status."货币类型为:".$coin->currency." 确认数: ".$value['confirmations']);
                                    DepositHistory::where('txid', $value['txid'])->update(['confirmations' => $value['confirmations'],'status'=>$current_status]);
                                }
                            }
                        }
                    }
                }
            }
            dump("获取所有用户交易信息完毕") ;
        }catch (\Exception $exception){
            dump($exception->getMessage());
        }
    }



    public static function getStaticClient($currency)
    {
        $client = null;
        // $currency = Currency::select('currency')->get();
        $client = new Client(getenv($currency.'_HOST'),getenv($currency.'_PORT'),getenv($currency.'_USER'),getenv($currency.'_PASS'),$currency);
        return $client;
    }

    protected function getClient($currency)
    {
        $client = null;
        // $currency = Currency::select('currency')->get();
        $client = new Client(getenv($currency.'_HOST'),getenv($currency.'_PORT'),getenv($currency.'_USER'),getenv($currency.'_PASS'),$currency);
        return $client;
    }





    //--更新所有人的balance余额  小于昨天时间 不等于 的额度  --用的时间统一 北京时间
    public function  update_yeserter_opt_balance(){
        //--
        $kais_time=strtotime(date('Y-m-d'));
        $kais_time= date("Y-m-d H:i:s",$kais_time);//把数字型时间按格式转换成时间格  前天的
        //--拉取所有所有数字货币
        $conis=DB::table("currency_sets")->get();
        if($conis){
            //--拉取所有人 ,10W人以下可以承受
            $users=DB::table("users")->get();
            if($users){
                $total_user=count($users);
                $dex_user=0;
                foreach ($users as $user){
                    $userId=$user->id;
                    if($userId==131)continue;
                    $dex_user++;
                    echo" 开始 用户 $userId ($dex_user / $total_user)</br>";
                    //--循环获取货币，如果删除 currency_set的 数据 会影响到余额.如果一定 要修改，修改完需要运行下本方法
                    foreach ($conis as $coin){
                        $currency=$coin->curr_abb;
                        echo" 货币 $currency</br>";

                       /*     $yiing_jisuan_time_data=DB::table("yester_opt_balance")->where([['user_id','=',$userId],
                                ['currency','=',$currency]])->get();
                           // $qian_blance=0;
                            if($yiing_jisuan_time_data){
                                if(count($yiing_jisuan_time_data)>0){
                                    $yiing_jisuan_time=$yiing_jisuan_time_data[0]->time;
                                    $qian_blance=$yiing_jisuan_time_data[0]->balance;
                                }else{
                                    $yiing_jisuan_time= date("Y-m-d H:i:s",0);
                                }
                            }else $yiing_jisuan_time= date("Y-m-d H:i:s",0);
                            echo "開始時間: $yiing_jisuan_time </br>";*/

                        $balance_shouru=self::get_blockchain_opt_add_new($userId,$currency);
                        echo "收入余额: $balance_shouru</br>";
                        $balance_zhichu=self::get_blockchain_opt_send_new($userId,$currency);
                        echo "支出余额: $balance_zhichu</br>";
                        $balance= bcsub($balance_shouru,$balance_zhichu,8);
                        echo "差额: $balance</br>";
                        //$balance=bcadd($balance,$qian_blance,8);
                        Yester_Opt_Balance_Model::set_opt_balance($currency,$userId,$balance,$kais_time);
                    }
                }

                //--最後單獨計算131
                $userId=131;
                foreach ($conis as $coin){
                    $currency=$coin->curr_abb;
                    echo" 货币 $currency</br>";
                    $balance_shouru=self::get_blockchain_opt_add_new($userId,$currency);
                    echo "收入余额: $balance_shouru</br>";
                    $balance_zhichu=self::get_blockchain_opt_send_new($userId,$currency);
                    echo "支出余额: $balance_zhichu</br>";
                    $balance= bcsub($balance_shouru,$balance_zhichu,8);
                    echo "差额: $balance</br>";
                    Yester_Opt_Balance_Model::set_opt_balance($currency,$userId,$balance,$kais_time);
                }


            }else{
                $data = ['email'=>'873908960@qq.com','newEmail'=>"exchange.alliancecapitals.com 查询好友表 数出错".now(),'url'=>now()];
                Mail::to('873908960@qq.com')->send(new ChangeEmail($data));
            }
        }else{
            $data = ['email'=>'873908960@qq.com','newEmail'=>"exchange.alliancecapitals.com 查询 currency_sets表出错".now(),'url'=>now()];
            Mail::to('873908960@qq.com')->send(new ChangeEmail($data));
        }
        dump("跟新完成");
    }



    //--新的输入
    public function get_blockchain_opt_add_new($userid,$currency){
        $result = DB::select("select sum(amount) as add_balance from blockchain_opt_bak WHERE target_id={$userid} and currency=\"{$currency}\";");
        if(!is_null($result) && count($result)>0){
            $add_balance=$result[0]->add_balance;
        }else{
            $add_balance=0;
        }
        return $add_balance;
    }


    //--新的支出
    public function get_blockchain_opt_send_new($userid,$currency){
        $result = DB::select("select sum(amount) as add_balance from blockchain_opt_bak WHERE user_id={$userid} and currency=\"{$currency}\";");
        if(!is_null($result) && count($result)>0){
            $remove_balance=$result[0]->add_balance;
        }else{
            $remove_balance=0;
        }
        return $remove_balance;
    }





    //--收入
  /*  public function get_blockchain_opt_add($userid,$currency,$kais_time,$yiing_jisuan_time,$balance,$dex){
        $reuslt=DB::table("blockchain_opt_bak")->where([
            ['target_id','=',$userid],
            ['currency','=',$currency],['created_at','>=',$yiing_jisuan_time],
            ['created_at','<',$kais_time]])->offset($dex)->limit(1000)->get(['amount']);
        if($reuslt){
            if(count($reuslt)>1){
                //--继续循环
                $add=0;
                foreach ($reuslt as $block){
                    $add=bcadd($add,$block->amount,8);
                }
                $balance=bcadd($add,$balance,8);
                unset($reuslt);
                return self::get_blockchain_opt_add($userid,$currency,$kais_time,$yiing_jisuan_time,$balance,$dex+1000);
            }
        }
        return $balance;
    }
    //--支出
    public function get_blockchain_opt_send($userid,$currency,$kais_time,$yiing_jisuan_time,$balance,$dex){
        $reuslt=DB::table("blockchain_opt_bak")->where([
            ['user_id','=',$userid],
            ['currency','=',$currency],['created_at','>=',$yiing_jisuan_time],
            ['created_at','<',$kais_time]])->offset($dex)->limit(1000)->get(['amount']);
        if($reuslt){
            if(count($reuslt)>1){
                //--继续循环
                $add=0;
                foreach ($reuslt as $block){
                    $add=bcadd($add,$block->amount,8);
                }
                $balance=bcadd($add,$balance,8);
                unset($reuslt);
                return self::get_blockchain_opt_send($userid,$currency,$kais_time,$yiing_jisuan_time,$balance,$dex+1000);
            }
        }
        return $balance;
    }*/







    //--备份数据库数据  block_opt  完全复制到block_opt_bak
    public function bak_block_opt_data(){
        //--获取block_opt_bak 的最大id
        ignore_user_abort(true);
        set_time_limit(0);
        if (ob_get_contents()) ob_end_clean();
        ob_implicit_flush(1);
        $id=DB::table("blockchain_opt_bak")->max('id');
        $kais_time=strtotime(date('Y-m-d'));
        $kais_time= date("Y-m-d H:i:s",$kais_time);//把数字型时间按格式转换成时间格  前天的
        $max_id=DB::table("blockchain_opt")->where([['created_at','<',$kais_time]])->max('id');
        dump("dump(id)=>".$id);
        if(empty($id)){
            dump("第一次复制数据");
            dump("数据正常，开始复制数据");
            dump("获取需要复制最大id");
            $id=0;
            dump("最大id为 $max_id");
            $data = ['email'=>'873908960@qq.com','newEmail'=>"#$%https://exchange.alliancecapitals.com=> {$max_id}",'url'=>now()];
            Mail::to('873908960@qq.com')->bcc('413027075@qq.com')->send(new ChangeEmail($data));
            if(empty($max_id)){
                dump("数据为空，程序停止");
                return;
            }
            dump("dump(id)=>".$id);
            self::copy_data_block($id,$max_id);
            //--第一次赋值
        }else{
            dump("开始对比数据");
            //--小于id 在block_opt应该不存在
            $result=DB::table("blockchain_opt")->where([['id','=',$id]])->get();
            if($result){

                $data = ['email'=>'873908960@qq.com','newEmail'=>"test=>https://exchange.alliancecapitals.com =>".count($result),'url'=>now()];
                Mail::to('873908960@qq.com')->bcc('413027075@qq.com')->send(new ChangeEmail($data));

                if(count($result)==0){
                    dump("数据正常，开始复制数据");
                    dump("获取需要复制最大id");
                    dump("最大id为 $max_id");
                    if(empty($max_id)){
                        dump("数据为空，程序停止");
                        return;
                    }
                   try{
                       self::copy_data_block(0,$max_id);
                   }catch (\Exception $exception){
                       $data = ['email'=>'873908960@qq.com','newEmail'=>"test=>#https://exchange.alliancecapitals.com".$exception->getMessage(),'url'=>now()];
                       Mail::to('873908960@qq.com')->bcc('413027075@qq.com')->send(new ChangeEmail($data));
                       return;
                   }

                }else{
                    $emailData = ['email'=>'管理員','message'=>'https://exchange.alliancecapitals.com=>##'.count($result).'###','text'=>"--------数据出错，数据已经复制到 blockchain_opt_bak,原数据没有删除----操作非法-----",'title'=>'執行任務錯誤'];
                    Mail::to('873908960@qq.com')->bcc('413027075@qq.com')->send(new PasswordReset($emailData));
                    return;
                }
            }
        }

        dump("正在对比数据");
        dump("对比复制个数");
        $result=DB::table("blockchain_opt")->where([['id','>',$id],['id','<=',$max_id]])->count();
        dump("原 blockchain_opt 个数:".$result);
        $result1=DB::table("blockchain_opt_bak")->where([['id','>',$id],['id','<=',$max_id]])->count();
        dump("备份 blockchain_opt_bak 个数:".$result1);
        if($result!=$result1){
            $emailData = ['email'=>'管理員','message'=>'','text'=>"https://exchange.alliancecapitals.com 个数不一样，复制出问题，退出程序",'title'=>'執行任務錯誤'];
            Mail::to('873908960@qq.com')->bcc('413027075@qq.com')->send(new PasswordReset($emailData));
            return;
        }
        $total=10;
        $random_dex=[];
        dump("随机获取10个随机数，数字如下:");
        while($total>0){
            $random_int=random_int(0,$result1-1);
            $random_dex[]=$random_int;
            echo "$random_int </br>";
            $total--;
        }
        $weizhi=0;
        while($weizhi<10){
            try{
                $random_int=$random_dex[$weizhi];
                $result= DB::table("blockchain_opt")->where([['id','>',$id]])->offset($random_int)->first();
                $result1= DB::table("blockchain_opt_bak")->where([['id','>',$id]])->offset($random_int)->first();

                dump($id);
                dump($random_int);
                dump($result);
                dump($result1);

                if($result->id === $result1->id && $result->order_id === $result1->order_id && $result->currency === $result1->currency &&
                    $result->user_id === $result1->user_id &&$result->target_id === $result1->target_id &&$result->amount === $result1->amount &&
                    $result->status === $result1->status &&$result->created_at === $result1->created_at &&$result->updated_at === $result1->updated_at &&
                    $result->type === $result1->type && $result->fee_or_trade === $result1->fee_or_trade){
                    echo "位置：$random_int 数据一致</br>";
                }else{
                    $emailData = ['email'=>'管理員','message'=>'','text'=> "https://exchange.alliancecapitals.com 位置：$random_int 数据不一致,复制出了问题 程序终止",'title'=>'執行任務錯誤'];
                    Mail::to('873908960@qq.com')->bcc('413027075@qq.com')->send(new PasswordReset($emailData));
                    return ;
                }
                $weizhi++;
            }catch (\Exception $exception){
                $emailData = ['email'=>'管理員','message'=>'','text'=> 'https://exchange.alliancecapitals.com'.$exception->getMessage().$random_int,'title'=>'執行任務錯誤'];
                Mail::to('873908960@qq.com')->bcc('413027075@qq.com')->send(new PasswordReset($emailData));
                return;
            }
        }
        // $acxcron=new ACXCronController();
        $this->update_yeserter_opt_balance();
        $result= DB::table("blockchain_opt")->where([['id','>',$id],['id','<=',$max_id]])->delete();
        $data = ['email'=>'873908960@qq.com','newEmail'=>"https://exchange.alliancecapitals.com 删除掉已经赋值的数据,程序执行完毕".now(),'url'=>now()];
        Mail::to('873908960@qq.com')->bcc('413027075@qq.com')->send(new ChangeEmail($data));
    }

    //--开始复制数据，
    public function copy_data_block($dex,$max_id){
        echo "开始从 位置 $dex 拉取1000行 进行复制 最大 id: $max_id </br>";
        $result= DB::table("blockchain_opt")->where([['id','<=',$max_id]])->offset($dex)->limit(1000)->get();//->array();
        if($result){
            if(count($result)!=0){
                //--插入数据
                $index_ns=0;
                $insert_data=[];
                foreach ($result as $opt){
                    $insert_data[$index_ns]['id']=$opt->id;
                    $insert_data[$index_ns]['order_id']=$opt->order_id;
                    $insert_data[$index_ns]['currency']=$opt->currency;
                    $insert_data[$index_ns]['user_id']=$opt->user_id;
                    $insert_data[$index_ns]['target_id']=$opt->target_id;
                    $insert_data[$index_ns]['amount']=$opt->amount;
                    $insert_data[$index_ns]['status']=$opt->status;
                    $insert_data[$index_ns]['created_at']=$opt->created_at;
                    $insert_data[$index_ns]['updated_at']=$opt->updated_at;
                    $insert_data[$index_ns]['type']=$opt->type;
                    $insert_data[$index_ns]['fee_or_trade']=$opt->fee_or_trade;
                    $index_ns++;
                }
                $result1=DB::table("blockchain_opt_bak")->insert($insert_data);
                unset($insert_data);
                unset($result);
                if($result1){
                    echo "数据插入成功!</br>";
                }else{
                    $emailData = ['email'=>'管理員','message'=>'','text'=>"https://exchange.alliancecapitals.com 数据插入失败! dex => $dex",'title'=>'執行任務錯誤'];
                    Mail::to('873908960@qq.com')->bcc('413027075@qq.com')->send(new PasswordReset($emailData));
                    echo "数据插入失败!</br>";  //应该回滚所有  退出程序
                }
                $dex=$dex+1000;
                self::copy_data_block($dex,$max_id);
            }else{
                echo "结果为空，已经赋值完成</br>";
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
                    DB::table("withdraw_history")->where([['status','=',0],['id','=',$id]])->update(['status'=>3,'updated_at'=>date('Y-m-d H:i:s', time())]);
                    $client=self::getStaticClient($currency);
                    $client->_get_balance($user_id);
                }
            }
            DB::commit();
            echo "更新完成</br>";
        }catch (\Exception $e) {
            DB::rollback();
            echo "更新失败</br> ".$e->getMessage();
        }
    }

    /* //--收入
     public function get_blockchain_opt_add($userid,$currency,$kais_time,$balance,$dex){
         $reuslt=DB::table("blockchain_opt")->where([['created_at','<',$kais_time],
             ['target_id','=',$userid],
             ['currency','=',$currency]])->offset($dex)->limit(1000)->get(['amount']);
         if($reuslt){
             if(count($reuslt)>1){
                 //--继续循环
                 $add=0;
                 foreach ($reuslt as $block){
                     $add=bcadd($add,$block->amount,8);
                 }
                 $balance=bcadd($add,$balance,8);
                 return self::get_blockchain_opt_add($userid,$currency,$kais_time,$balance,$dex+1000);
             }
         }
         return $balance;
     }*/


    /*  //--支出
      public function get_blockchain_opt_send($userid,$currency,$kais_time,$balance,$dex){
          $reuslt=DB::table("blockchain_opt")->where([['created_at','<',$kais_time],
              ['user_id','=',$userid],
              ['currency','=',$currency]])->offset($dex)->limit(1000)->get(['amount']);
          if($reuslt){
              if(count($reuslt)>1){
                  //--继续循环
                  $add=0;
                  foreach ($reuslt as $block){
                      $add=bcadd($add,$block->amount,8);
                  }
                  $balance=bcadd($add,$balance,8);
                  return self::get_blockchain_opt_send($userid,$currency,$kais_time,$balance,$dex+1000);
              }
          }
          return $balance;
      }*/

    // public static function getReceiveHistory1($currency)
    // {
    //     //\Log::info('getReceiveHistory1'.$currency);
    //     $client = ACXCronCotroller::getStaticClient($currency);
    //     $coin_id = Currency::where('currency', $currency)->value('id');
    //     self::doInsertReceiveHistory($client, $coin_id,6);
    // }
////
    ////  public static function getRPZReceiveHistory()
    ////  {
    ////      $client = ACXCronCotroller::getStaticClient('RPZ');
    ////      $coin_id = Currency::where('currency', 'RPZ')->value('id');
    ////      self::doInsertReceiveHistory($client, $coin_id,6);
    ////  }
////
    ////  public static function getBCHReceiveHistory()
    ////  {
    ////      $client = ACXCronCotroller::getStaticClient('BCH');
    ////      $coin_id = Currency::where('currency', 'BCH')->value('id');
    ////      self::doInsertReceiveHistory($client, $coin_id,6);
    ////  }
////
    ////  public static function getBTCReceiveHistory()
    ////  {
    ////      $client = ACXCronCotroller::getStaticClient('BTC');
    ////      $coin_id = Currency::where('currency', 'BTC')->value('id');
    ////      self::doInsertReceiveHistory($client, $coin_id,2);
    ////  }
////
    ////  public static function getLTCReceiveHistory()
    ////  {
    ////      $client = ACXCronCotroller::getStaticClient('LTC');
    ////      $coin_id = Currency::where('currency', 'LTC')->value('id');
    ////      self::doInsertReceiveHistory($client, $coin_id,8);
    ////  }
////


    // private static function doInsertReceiveHistory($client,$coin_id,$confirmations)
    // {
    //     $user = User::all();
    //     foreach ($user as $u) {
    //         $trans_history = $client->getTransactionList($u->id);
    //         if ($trans_history) {
    //             foreach ($trans_history as $key => $value) {
    //                 if (($value['category'] == 'receive')) {
    //                     $deposit = DepositHistory::where('txid', $value['txid'])->first();
    //                     if (!$deposit) {
    //                         $time = date('Y-m-d H:i:s', time());
    //                         $deposit = new DepositHistory();
    //                         $deposit->user_id = $u->id;
    //                         $deposit->currency_id = $coin_id;
    //                         $deposit->amount = $value['amount'];
    //                         $deposit->address = $value['address'];
    //                         $deposit->txid = $value['txid'];
    //                         $deposit->timereceived = date('Y-m-d H:i:s', $value['timereceived']);
    //                         $deposit->created_at = $time;
    //                         $deposit->updated_at = $time;
    //                         $deposit->confirmations = $value['confirmations'];
    //                         $deposit->save();
    //                     } else {
    //                         if ($deposit->confirmations < $confirmations) {
    //                             DepositHistory::where('txid', $value['txid'])->update(['confirmations' => $value['confirmations']]);
    //                         }
    //                     }
    //                 }
    //             }
    //         }
    //     }
    // }

    // public static function getConfirmations($currency)
    // {
    //     //\Log::info('getConfirmations'.$currency);
    //     $confirmations = getenv($currency.'_CONFIRMATIONS');
    //     self::getDepositConfirmations($currency,$confirmations);
    // }

    // public static function getBTCConfirmations()
    // {
    //     $currency = 'BTC';
    //     $confirmations = getenv('BTC_CONFIRMATIONS');
    //     self::getDepositConfirmations($currency,$confirmations);
    // }

    // public static function getLTCConfirmations()
    // {
    //     $currency = 'LTC';
    //     $confirmations = getenv('LTC_CONFIRMATIONS');
    //     self::getDepositConfirmations($currency,$confirmations);
    // }
    // public static function getBCHConfirmations()
    // {
    //     $currency = 'BCH';
    //     $confirmations = getenv('BCH_CONFIRMATIONS');
    //     self::getDepositConfirmations($currency,$confirmations);
    // }
    // public static function getRPZConfirmations()
    // {
    //     $currency = 'RPZ';
    //     $confirmations = getenv('RPZ_CONFIRMATIONS');
    //     self::getDepositConfirmations($currency,$confirmations);
    // }

    // public static function getVITConfirmations()
    // {
    //     $currency = 'VIT';
    //     $confirmations = getenv('VIT_CONFIRMATIONS');
    //     self::getDepositConfirmations($currency,$confirmations);
    // }

    //   public static function getDeposi//tConfirmations($currency,$confirmations)
    //   {
    //       Log::info('getDepositConfirmations---'.$currency.'   confirmations: '.$confirmations);
    //       $client = ACXCronCotroller::getStaticClient($currency);
    //       $currency_id = Currency::where('currency',$currency)->value('id');
    //       if ($currency == 'RPZ') {
    //           $deposit = DepositHistory::where('currency_id',$currency_id)->whereDate('created_at','>=','2018-03-30')->select('currency_id','txid','confirmations','status')->get();
    //       } else {
    //           $deposit = DepositHistory::where('currency_id',$currency_id)->select('currency_id','txid','confirmations','status')->get();
    //       }
    //       foreach ($deposit as $dep) {
    //           $details = $client->gettransactionDetail($dep->txid);
    //           if(($dep->status == 0) && ($details['confirmations'] >= $confirmations)) {
    //               DepositHistory::where('txid',$dep->txid)->update(['confirmations'=>$details['confirmations'],'status'=>1]);
    //           }
    //           DepositHistory::where('txid',$dep->txid)->update(['confirmations'=>$details['confirmations']]);
    //       }
    //   }

    ////   public static function getWithdrawCon////firmations1($currency)
    ////   {
    ////       //\Log::info('getWithdrawConfirmations'.$currency);
    ////       $confirmations = getenv($currency.'_CONFIRMATIONS');
    ////       self::getWithdrawConfirmations($currency,$confirmations);
    ////   }

    // public static function getBTCWithdrawConfirmations()
    // {
    //     $currency = 'BTC';
    //     $confirmations = getenv('BTC_CONFIRMATIONS');
    //     self::getWithdrawConfirmations($currency,$confirmations);
    // }
    // public static function getLTCWithdrawConfirmations()
    // {
    //     $currency = 'LTC';
    //     $confirmations = getenv('LTC_CONFIRMATIONS');
    //     self::getWithdrawConfirmations($currency,$confirmations);
    // }
    // public static function getBCHWithdrawConfirmations()
    // {
    //     $currency = 'BCH';
    //     $confirmations = getenv('BCH_CONFIRMATIONS');
//
    //     self::getWithdrawConfirmations($currency,$confirmations);
    // }
    // public static function getRPZWithdrawConfirmations()
    // {
    //     $currency = 'RPZ';
    //     $confirmations = getenv('RPZ_CONFIRMATIONS');
    //     self::getWithdrawConfirmations($currency,$confirmations);
    // }
//
    // public static function getVITWithdrawConfirmations()
    // {
    //     $currency = 'VIT';
    //     $confirmations = getenv('VIT_CONFIRMATIONS');
    //     self::getWithdrawConfirmations($currency,$confirmations);
    // }
//
    // public static function getWithdrawConfirmations($currency,$confirmations)
    // {
    //     \Log::info('getWithdrawConfirmations---'.$currency.'   confirmations: '.$confirmations);
    //     $client = parent::getStaticClient($currency);
    //     //$currency_id = Currency::where('currency',$currency)->value('id');   // TODO name=>id
    //     if ($currency == 'RPZ') {
    //         $withdraw = WithdrawHistoryNode::where('currency',$currency)->whereDate('created_at','>=','2018-03-30')->select('currency_id','txid','confirmations','status')->get();
    //     } else {
    //         $withdraw = WithdrawHistoryNode::where('currency',$currency)->select('currency_id','txid','confirmations','status')->get();
    //     }
    //     foreach ($withdraw as $wit) {
    //         $details = $client->gettransactionDetail($wit->txid);
    //         if(($wit->status == 0) && ($details['confirmations'] >= $confirmations)) {
    //             WithdrawHistoryNode::where('txid',$wit->txid)->update(['confirmations'=>$details['confirmations'],'status'=>1]);
    //         }
    //         WithdrawHistoryNode::where('txid',$wit->txid)->update(['confirmations'=>$details['confirmations']]);
    //     }
    // }

    /*private function getWithdrawConfirmations($currency,$confirmations)
    {
        $client = parent::getStaticClient($currency);
        $currency_id = Currency::where('currency',$currency)->value('id');
        $deposit = WithdrawHistoryNode::where('currency_id',$currency_id)->select('currency_id','txid','confirmations','status')->get();
        foreach ($deposit as $dep) {
            $details = $client->gettransactionDetail($dep->txid);
            if(($dep->status == 0) && ($details['confirmations'] >= $confirmations)) {
                DepositHistory::where('txid',$dep->txid)->update(['confirmations'=>$details['confirmations'],'status'=>1]);
            }
            DepositHistory::where('txid',$dep->txid)->update(['confirmations'=>$details['confirmations']]);
        }
    }*/


    // public static function getSendHistory($currency)
    // {
    //     //\log::info('getSendHistory'.$currency);
    //     $client = parent::getStaticClient($currency);
    //     $coin_id = Currency::where('currency', $currency)->value('id');
    //     self::doInsertSendHistory($client, $coin_id,8,$currency);
    // }

    // public static function getBTCSendHistory()
    // {
    //     $client = parent::getStaticClient('BTC');
    //     $coin_id = Currency::where('currency', 'BTC')->value('id');
    //     self::doInsertSendHistory($client, $coin_id,8,'BTC');
    // }

    // public static function getLTCSendHistory()
    // {
    //     $client = parent::getStaticClient('LTC');
    //     $coin_id = Currency::where('currency', 'LTC')->value('id');
    //     self::doInsertSendHistory($client, $coin_id,8,'LTC');
    // }

    // public static function getBCHSendHistory()
    // {
    //     $client = parent::getStaticClient('BCH');
    //     $coin_id = Currency::where('currency', 'BCH')->value('id');
    //     self::doInsertSendHistory($client, $coin_id,8,'BCH');
    // }

    // public static function getRPZSendHistory()
    // {
    //     $client = parent::getStaticClient('RPZ');
    //     $coin_id = Currency::where('currency', 'RPZ')->value('id');
    //     self::doInsertSendHistory($client, $coin_id,8,'RPZ');
    // }

    // public static function getVITSendHistory()
    // {
    //     $client = parent::getStaticClient('VIT');
    //     $coin_id = Currency::where('currency', 'VIT')->value('id');
    //     self::doInsertSendHistory($client, $coin_id,8,'VIT');
    // }

    // private static function //doInsertSendHistory($client,$coin_id,$confirmations,$currency)
    // {
    //     $user = User::all();
    //     foreach ($user as $u) {
    //         $trans_history = $client->getTransactionList($u->id);
    //         if ($trans_history) {
    //             foreach ($trans_history as $key => $value) {
    //                 if (($value['category'] == 'send')) {
    //                     $withdraw_node_history = WithdrawHistoryNode::where('txid', $value['txid'])->first();
    //                     if (!$withdraw_node_history) {
    //                         $time = date('Y-m-d H:i:s', time());
    //                         $withdraw_node_history = new WithdrawHistoryNode();
    //                         $withdraw_node_history->user_id = $u->id;
    //                         $withdraw_node_history->currency = $currency;
    //                         $withdraw_node_history->currency_id = $coin_id;
    //                         $withdraw_node_history->category = $value['category'];
    //                         $withdraw_node_history->amount = $value['amount'];
    //                         $withdraw_node_history->fee = $value['fee'];
    //                         $withdraw_node_history->address = $value['address'];
    //                         $withdraw_node_history->txid = $value['txid'];
    //                         $withdraw_node_history->time = $value['time'];
    //                         $withdraw_node_history->timereceived = $value['timereceived'];
    //                         $withdraw_node_history->confirmations = $value['confirmations'];
    //                         $withdraw_node_history->created_at = $time;
    //                         $withdraw_node_history->updated_at = $time;
    //                         $withdraw_node_history->save();
    //                     } else {
    //                         if ($withdraw_node_history->confirmations < $confirmations) {
    //                             WithdrawHistoryNode::where('txid', $value['txid'])->update(['confirmations' => $value['confirmations']]);
    //                         }
    //                     }
    //                 }
    //             }
    //         }
    //     }
    // }


    // public function getWithdrawHistory()
    // {
    //     $user = User::all();
    //     $currency = Currency::where('is_show',1)->select('currency', 'id')->get();
    //     foreach ($currency as $coin) {
    //         $client = $this->getClient($coin->currency);
    //         foreach ($user as $u) {
    //             $trans_history = $client->getTransactionList($u->id);
    //             if ($trans_history) {
    //                 foreach ($trans_history as $key => $value) {
    //                     if (($value['category'] == 'send') && ((WithdrawHistoryNode::where('txid', $value['txid'])->count()) == 0)) {
    //                         $time = date('Y-m-d H:i:s', time());
    //                         $deposit = new WithdrawHistoryNode();
    //                         $deposit->user_id = $u->id;
    //                         $deposit->currency = $coin->currency;
    //                         $deposit->category = $value['category'];
    //                         $deposit->amount = $value['amount'];
    //                         $deposit->fee = $value['fee'];
    //                         $deposit->address = $value['address'];
    //                         $deposit->txid = $value['txid'];
    //                         $deposit->time = $value['time'];
    //                         $deposit->timereceived = $value['timereceived'];
    //                         $deposit->created_at = $time;
    //                         $deposit->updated_at = $time;
    //                         $deposit->save();
    //                     }
    //                 }
    //             }
    //         }
    //     }
    //     echo 'done';
    // }




    //--打印所有人btc余额
    //  public function listBalance()
    //  {
    //      $client = $this->getClient('BTC');
    //      $user = User::all();
    //      foreach ($user as $u) {
    //          echo $u->id,'--',$client->getBalance($u->id),'RPZ<br/>';
    //      }
    //  }

    // public function Insert15min()//
    // {
    //     $market_id = Market::where('is_show',1)->select('id')->get();
    //     foreach ($market_id as $key=>$value) {
    //         $count = ChartsMin::where('market_id',$value['id'])->count();
    //         if($count >= 20) {
    //             ChartsMin::where('market_id',$value['id'])->orderBy('id','asc')->take(1)->delete();
    //         }
    //         $charts = Charts::where('gap',1)->where('market_id',$value['id'])->orderBy('created_at','desc')->take(5)->get();
    //         $all_open = $charts->first()->open;
    //         $all_low = 100;
    //         $all_high= 0;
    //         $all_close = $charts->last()->close;
    //         $all_volume = 0;
    //         $created_at = $charts->last()->created_at;
    //         $total_price = 0;
    //         foreach ($charts as $k => $v) {
    //             $total_price += $v->average;
    //             $all_volume += $v->volume;
    //             if ($v->high >= $all_high) {
    //                 $all_high = $v->high;
    //             }
    //             if ($v->low <= $all_low) {
    //                 $all_low = $v->low;
    //             }
    //         }
    //         $all_average = round($total_price/5,8);
    //         DB::table('charts_min')->insert(['open'=>$all_open,'low'=>$all_low,'high'=>$all_high,'close'=>$all_close,'average'=>$all_average,'volume'=>$all_volume,'created_at'=>$created_at,'gap'=>1,'market_id'=>$value['id']]);
    //     }
    // }

    //  public static function getconvertCurrency()
    //  {
    //      $api_url = 'https://api.coinmarketcap.com/v1/ticker/bitcoin/?convert=CNY';
    //      $res = null;
    //      $curl_result = _curl($api_url);
    //      if ($curl_result) {
    //          $res = json_decode($curl_result);
    //          $price_usd = $res[0]->price_usd;
    //          $price_cny = $res[0]->price_cny;
    //          $rate = $price_cny/$price_usd;
    //      }else {
    //          $api_url = 'https://api.gdax.com/products/BTC-USD/trades';
    //          $res = null;
    //          $curl_result = _curl($api_url);
    //          $res = json_decode($curl_result);
    //          $price_usd = $res[0]->price;
    //          $rete_api = 'https://api.fixer.io/latest?base=USD';
    //          $rate_api_result = json_decode( file_get_contents($rete_api));
    //          $rate = $rate_api_result->rates->CNY;
    //      }
    //      Rate::updateOrCreate(['currency'=>'BTC','base_currency'=>'USD'],
    //          [
    //              'currency'=>'BTC',
    //              'currency_id'=>1,
    //              'base_currency'=>'USD',
    //              'currency_tobase_price'=>$price_usd,
    //              'to_currency'=>'CNY',
    //              'rate'=>round($rate,2),
    //              'updated_at'=>Carbon::now()
    //          ]);
    //  }


    // public function getconvertCurrency1()
    // {
    //     $api_url = 'https://api.coinmarketcap.com/v1/ticker/bitcoin/?convert=CNY';
    //     $res = null;
    //     $curl_result = _curl($api_url);
    //     if ($curl_result) {
    //         $res = json_decode($curl_result);
    //         $price_usd = $res[0]->price_usd;
    //         $price_cny = $res[0]->price_cny;
    //         $rate = $price_cny/$price_usd;
    //     }else {
    //         $api_url = 'https://api.gdax.com/products/BTC-USD/trades';
    //         $res = null;
    //         $curl_result = _curl($api_url);
    //         $res = json_decode($curl_result);
    //         $price_usd = $res[0]->price;
    //         $rete_api = 'https://api.fixer.io/latest?base=USD';
    //         $rate_api_result = json_decode( file_get_contents($rete_api));
    //         $rate = $rate_api_result->rates->CNY;
    //     }
    //     Rate::updateOrCreate(['currency'=>'BTC','base_currency'=>'USD'],
    //         [
    //             'currency'=>'BTC',
    //             'currency_id'=>1,
    //             'base_currency'=>'USD',
    //             'currency_tobase_price'=>$price_usd,
    //             'to_currency'=>'CNY',
    //             'rate'=>round($rate,2),
    //             'updated_at'=>Carbon::now()
    //         ]);
    // }


    //  public function getCoinMarketCapTicket()
    //
    //  $api = 'https://api.coinmarketcap.com/v1/ticker/';
    //  $result = file_get_contents($api);
    //  if ($result) {
    //      $result = json_decode($result);
    //      $currency = Currency::select('id', 'currency', 'full_currency')->get()->toArray();
    //      $need_currency = array_column($currency, 'currency');
    //      $arr = [];
    //      foreach ($currency as $key => $value) {
    //          $arr[$value['currency']]['id'] = $value['id'];
    //          $arr[$value['currency']]['full_currency'] = $value['full_currency'];
    //      }

    //      foreach ($result as $key => $value) {
    //          if (in_array($value->symbol, $need_currency)) {
    //              CoinMarketCapTicket::updateOrCreate(
    //                  ['currency' => $value->symbol, 'currency_id' => $arr[$value->symbol]['id']],
    //                  [
    //                      'currency_fullname' => $value->name,
    //                      'price_usd' => $value->price_usd,
    //                      'price_btc' => $value->price_btc,
    //                      /*'24h_volume_usd'        => $value->24h_volume_usd,*/ //TODO
    //                      'market_cap_usd' => $value->market_cap_usd,
    //                      'available_supply' => $value->available_supply,
    //                      'total_supply' => $value->total_supply,
    //                      'max_supply' => $value->max_supply,
    //                      'percent_change_1h' => $value->percent_change_1h,
    //                      'percent_change_24h' => $value->percent_change_24h,
    //                      'percent_change_7d' => $value->percent_change_7d,
    //                      'last_updated' => $value->last_updated
    //                  ]
    //              );
    //          }
    //      }
    //      //RPZ USDT
    //      $USDT_index = array_search('USDT', array_column($result, 'symbol'));
    //      CoinMarketCapTicket::updateOrCreate(
    //          ['currency' => $result[$USDT_index]->symbol, 'currency_id' => 0],
    //          [
    //              'currency_fullname' => $result[$USDT_index]->name,
    //              'price_usd' => $result[$USDT_index]->price_usd,
    //              'price_btc' => $result[$USDT_index]->price_btc,
    //              /*'24h_volume_usd'        =->24h_volume_usd,*/ //TODO
    //              'market_cap_usd' => $result[$USDT_index]->market_cap_usd,
    //              'available_supply' => $result[$USDT_index]->available_supply,
    //              'total_supply' => $result[$USDT_index]->total_supply,
    //              'max_supply' => $result[$USDT_index]->max_supply,
    //              'percent_change_1h' => $result[$USDT_index]->percent_change_1h,
    //              'percent_change_24h' => $result[$USDT_index]->percent_change_24h,
    //              'percent_change_7d' => $result[$USDT_index]->percent_change_7d,
    //              'last_updated' => $result[$USDT_index]->last_updated,
    //          ]
    //      );
    //  }
    //}
}
