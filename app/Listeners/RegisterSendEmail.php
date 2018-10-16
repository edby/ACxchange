<?php
/**
 * Created by PhpStorm.
 * User: ZRothschild
 * Date: 2018/5/7
 * Time: 10:14
 */

namespace App\Listeners;




use App\Mail\PasswordReset;
use App\Models\MailLog;
use Illuminate\Support\Facades\Mail;
use App\Events\RegisterSendEmail as Register;

class RegisterSendEmail
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     * @param  Register $event
     * @return void
     */
    public function handle(Register $event)
    {
        $url = route('confirmRegisterToken',['register'=>base64_encode(encrypt($event->email))]);
        $here = "<a href='".$url."'>".__('ac.ClickOn')."</a>";
        $emailData = ['email'=>$event->email,'message'=>$here,'text'=>__('ac.RegistrationConfirmationText'),'title'=>__('ac.RegistrationConfirmation')];
        try{
            $msg = Mail::to($event->email)->send(new PasswordReset($emailData));
        }catch (\Exception $exception){
            $msg = $exception->getMessage();
        }
        $logData = [
            'mail_type'=>'confirmRegisterToken',
            'email'=>$event->email,
            'email_content'=>json_encode($emailData),
            'message'=>$msg?$msg:'',
        ];
        MailLog::create($logData);
    }
}