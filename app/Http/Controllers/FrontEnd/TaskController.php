<?php

namespace App\Http\Controllers\FrontEnd;

use App\Http\Controllers\Controller;
use App\Mail\NotiAdmin;
use App\Models\Redis_result;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redis;
use Exception;

class TaskController extends Controller
{

    //推入队列   -  静态方法
    public static function push($data)
    {
        $pre=getenv("REDIS_PRE","");
        $result = Redis::command('lpush', [$pre.'order', json_encode($data)]);
        return $result ? json_encode(['code'=>200]) : json_encode(['code'=>400]);
    }

    //踢出队列
    private  function pop()
    {
        $pre=getenv("REDIS_PRE","");
        $result = Redis::command('brpop', [$pre.'order', 1]);
        return $result;

    }



    //--打印个数
    public function get_count(){
        $pre=getenv("REDIS_PRE","");
        $len = Redis::llen($pre.'order');
        dump("$pre order 总数: $len");
        return $len;
    }
    private function getCount(){
        $pre=getenv("REDIS_PRE","");
        $len = Redis::llen($pre.'order');
        return $len;
    }

    //--开始工作  --uri方法
    public  function work(){
        dump("程序已经开始执行!!!!</br>");
        dump("可以关闭浏览器，程序将继续执行!</br>");
        ignore_user_abort(true);
        set_time_limit(0);
        if(self::need_is_cancel()){
            return;
        }
        if(1==1){
            dump("暂时停止走队列!!!!</br>");
            return;
        }
        $dex=0;
        while (true){

            try{
                $dex++;
                if(self::getCount()>0){
                    $result =self::pop();
                    if($result){
                        //--得到任务
                        $pop_result = json_decode($result[1]);
                        //--执行任务
                        try{
                            if($pop_result->opear=="cancelOrder"){
                                //--取消订单

                           //     $zhixing_result=ACOrderController::globle_cancel($pop_result);
                            }else if($pop_result->opear=="trade"){
                                //--交易
                        //        $zhixing_result=ACXWalletController::jiaoyi_shuzihuobi($pop_result);
                            }else if($pop_result->opear=="withdraw"){
                                //--预留提现操作
                                //TODO
                            }else{
                                echo "异常数据插入:".$pop_result;
                                Log::info("异常数据插入:".$pop_result);
                            }

                            //--执行情况插入数据库
                            //$redis_result['request']=$result[1];
                           // $redis_result['result']=$zhixing_result;
                          //  Redis_result::create($redis_result);

                            Log::info($zhixing_result);
                            echo $zhixing_result;
                        }catch (Exception $e){
                            dump($e->getMessage());
                            Log::info("执行结果出现异常....".$e->getMessage());
                            self::noti_admin("执行结果出现异常....".$e->getMessage());
                            //return view('new.login');
                        }
                        //dump($pop_result);
                    }else{
                        //--结果为空
                        // echo("结果为空,出现异常....$dex\r\n</br>");
                        Log::info("结果为空,出现异常....$dex\r\n</br>");
                        //--打印
                        self::noti_admin("结果为空,出现异常....$dex");
                    }
                }
                if($dex%10==0){
                    //--暫停1s
                    usleep(50000); //暂停50毫秒
                    //--查詢數據庫 是否讓我停止
                    $dex=0;
                    // echo("查询了10次了，我查询下数据库看是否还要继续执行下去</br>");
                    //打印
                    if(self::need_is_cancel()){
                        break;
                    }
                }
            }catch (Exception $e1){
                Log::info("执行结果出现异常....".$e->getMessage());
                self::noti_admin("执行结果出现异常....".$e1->getMessage());
            }
            gc_collect_cycles();
        }
    }

    //--判断是否需要取消执行
    public function need_is_cancel(){
        $result=DB::table("task_work")->first();
        if($result){
            $status=$result->status;
            if($status==0){
                echo("查询到要停止执行，马上停止</br>");
                self::noti_admin("查询到要停止执行，马上停止");
                return true;
            }
        }else{
            echo("数据库没有查询到配置 task_work,我停止执行了</br>");
            self::noti_admin("数据库没有查询到配置 task_work,我停止执行了");
           return true;
        }
        return false;
    }

    //--通知管理员
    public function noti_admin($data){
        $emails=[];
        $emails[0]="413027075@qq.com";
        $emails[1]="873908960@qq.com";
        foreach ($emails as $email){
            $emailData = ['emaildata'=>$data];
            Mail::to($email)-> send(new NotiAdmin($emailData));
        }
    }







    //获取队列
    public function lrange()
    {
        $pre=getenv("REDIS_PRE","");
        print_r(Redis::command('lrange', [$pre.'order', 0, -1]));
    }

    //测试推入方法
    public function testPush()
    {
        $id = rand(0,100);
        $data = [
            'id'=>$id,
            'name'=>'stn'
        ];
        $push_result = json_decode(TestController::push($data));
        if ($push_result->code == 200) {
            echo '<p>success</p>';
        }

    }
    //测试踢出方法
  //  public function testPop()
  //  {
  //      $pop_result = TestController::pop();
  //      if ($pop_result) {
  //          $pop_result = json_decode($pop_result[1], true);
  //          return $pop_result;
  //      }
  //  }
}
