<?php

namespace App\Jobs;

use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Exception;

class SendEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    private $withdraw;
    private $type;

    public function __construct($withdraw,$type)
    {
        $this->withdraw= $withdraw;
        $this->type = $type;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $withdraw = $this->withdraw;
        $type = $this->type;
        if($type == 'withdraw_approve'){
            try{
                $user = User::find($withdraw->user_id);
                $lang = Cache::get('user_lang_'.$user->id);
                $lang = $lang ? $lang : 'en';
                App::setLocale($lang);
                Mail::send('email.withdraw_success',['currency'=>$withdraw->currency,'amount'=>$withdraw->amount],function($message) use ($user){
                    $message->to($user->email)->subject('Confirmed Withdraw Email');
                });
            }catch (Exception $e){
                Log::info('--------Email connect error--------');
            }
        }
        if($type == 'withdraw_reject'){
            try{
                $user = User::find($withdraw->user_id);
                //发送邮件提醒被拒绝
                $data_cn=date("d-m-Y H:i:s",strtotime($withdraw->created_at));
                $data_en=date("d-m-Y H:i:s",strtotime($withdraw->created_at));
                $amount=$withdraw->amount;
                $currency_ns=$withdraw->currency;
                $amount=$amount." ".$currency_ns;
                Mail::send('email.withdrawrefuse',['date_cn'=>$data_cn,'date_en'=>$data_en,'amount'=>$amount],function($message) use ($user){
                    $message->to($user->email)->subject('Withdrawal Status');
                });
            }catch (Exception $e){
                Log::info('--------Email connect error--------');
            }
        }
    }

    public function failed()
    {
        Log::info('--------Email '.$this->type.' dispatch error--------');
    }

}
