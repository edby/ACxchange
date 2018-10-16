<?php

namespace App\Console;

use App\Http\Controllers\Admin\BackController;
use App\Http\Controllers\FrontEnd\ACXCronController;
use App\Http\Controllers\FrontEnd\CheckOrderController;
use App\Http\Controllers\FrontEnd\CronController;
use App\Mail\ChangeEmail;
use App\Tool\WithdrawalControl;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        //每天五点钟执行
        $cron = new ACXCronController();
        $schedule->call(function ()use($cron){
            try{
                $cron->bak_block_opt_data();;
            }catch (\Exception $exception){
                $data = ['email'=>'873908960@qq.com','newEmail'=>$exception->getMessage(),'url'=>now()];
                Mail::to('873908960@qq.com')->send(new ChangeEmail($data));
            }
        })->dailyAt('5:01');

        //每一个小时执行一次
        $schedule->call(function ()use($cron){
            try{
                $cron->update_withdraw_change_from_24hour();
            }catch (\Exception $exception){
                $data = ['email'=>'873908960@qq.com','newEmail'=>$exception->getMessage().'###update_withdraw_change_from_24hour','url'=>now()];
                Mail::to('873908960@qq.com')->send(new ChangeEmail($data));
            }
        })->hourly();


        //--每分钟执行一次图表
        $schedule->call(function (){
            try{
               $croncontroller = new CronController();
               $croncontroller->oneMinute();
               $minute = date("i",time());
                 //--执行分钟任务
                $croncontroller->oneMinute();
                if($minute%5==0)$croncontroller->fiveMinute();
                if($minute%15==0)$croncontroller->fifteen();
                if($minute%30==0) $croncontroller->halfHour();
            }catch (\Exception $exception){
                $data = ['email'=>'873908960@qq.com','newEmail'=>$exception->getMessage().'###update_withdraw_change_from_24hour','url'=>now()];
                Mail::to('873908960@qq.com')->bcc('413027075@qq.com')->send(new ChangeEmail($data));
            }
        }) ->everyMinute();

        //--每小时
        //--每分钟执行一次图表
        $schedule->call(function (){
            try{
                $croncontroller = new CronController();
                $hour =date("H",time());
                //--执行小时任务
                $croncontroller->oneHour();
                if($hour%2==0)$croncontroller->twoHour();
                if($hour%6==0)$croncontroller->sixHour();
                if($hour%12==0)$croncontroller->twelveHour();
                if($hour==0)$croncontroller->oneDay();
            }catch (\Exception $exception){
                $data = ['email'=>'873908960@qq.com','newEmail'=>$exception->getMessage().'###update_withdraw_change_from_24hour','url'=>now()];
                Mail::to('873908960@qq.com')->bcc('413027075@qq.com')->send(new ChangeEmail($data));
            }
        }) ->hourly();


        $schedule->call(function (){
            try{
                $croncontroller = new CronController();
                $croncontroller->week();
            }catch (\Exception $exception){
                $data = ['email'=>'873908960@qq.com','newEmail'=>$exception->getMessage().'###update_withdraw_change_from_24hour','url'=>now()];
                Mail::to('873908960@qq.com')->bcc('413027075@qq.com')->send(new ChangeEmail($data));
            }
        }) ->weekly();


        //充值确认定时任务 15分钟一次
        $schedule->call(function (){
            try{
                $acxCron = new ACXCronController();
                $acxCron->getReceiveHistory();
            }catch (\Exception $exception){
                $data = ['email'=>'873908960@qq.com','newEmail'=>$exception->getMessage().'###update_withdraw_change_from_24hour','url'=>now()];
                Mail::to('873908960@qq.com')->bcc('413027075@qq.com')->send(new ChangeEmail($data));
            }
        })->everyFiveMinutes();

        //每月执行当
        $withdrawal = new WithdrawalControl();
        $schedule->call(function ()use($withdrawal){
            $resOne = $withdrawal->upgrade();
            $dataOne = ['email'=>'873908960@qq.com','newEmail'=>'upgrade'.json_encode($resOne),'url'=>now()];
            Mail::to('873908960@qq.com')->send(new ChangeEmail($dataOne));
        })->monthly();

        //自定义规则提现额度 每是分钟
        $schedule->call(function ()use($withdrawal){
            //开始
            $resOne = $withdrawal->customizeRunStart();
            if (!$resOne[0]){
                $dataOne = ['email'=>'873908960@qq.com','newEmail'=>'customizeRunStart'.json_encode($resOne),'url'=>now()];
                Mail::to('873908960@qq.com')->send(new ChangeEmail($dataOne));
            }
            //结束
            $resTwo = $withdrawal->customizeRunEnd();
            if (!$resTwo[0]){
                $dataTwo = ['email'=>'873908960@qq.com','newEmail'=>'customizeRunEnd'.json_encode($resTwo),'url'=>now()];
                Mail::to('873908960@qq.com')->send(new ChangeEmail($dataTwo));
            }
        })->daily();


        //增加每分钟判断是不是有低价订单
        $schedule->call(function(){
            #CronController::getSendHistory('BTC');
            $check_order_controller=new CheckOrderController();
            $result=$check_order_controller->check_order_list_dingshi("admin","fZGGz4BmSNbHzYr1cEYgfMEl0UOs59cn");
            $data = ['email'=>'413027075@qq.com','newEmail'=>$result,'url'=>now()];
            # Mail::to('413027075@qq.com')->send(new ChangeEmail($data));
        })->everyMinute();

        
        $schedule->call(function() {
            CronController::getWithdrawConfirmations1('BTC');
        })->everyFiveMinutes();
        $schedule->call(function() {
            CronController::getWithdrawConfirmations1('LTC');
        })->everyFiveMinutes();
        $schedule->call(function() {
            CronController::getWithdrawConfirmations1('BCH');
        })->everyFiveMinutes();
        $schedule->call(function() {
            CronController::getWithdrawConfirmations1('RPZ');
        })->everyFiveMinutes();
        $schedule->call(function() {
            CronController::getWithdrawConfirmations1('XVG');
        })->everyFiveMinutes();
        $schedule->call(function() {
            CronController::getWithdrawConfirmations1('BTG');
        })->everyFiveMinutes();
        $schedule->call(function() {
            CronController::getWithdrawConfirmations1('DASH');
        })->everyFiveMinutes();

        //后台-----------------------
        $schedule->call(function() {
            BackController::getCurrencyInfoRun();
        })->everyTenMinutes();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
