<?php
/**
 * Created by PhpStorm.
 * User: DELL
 * Date: 2018/4/25
 * Time: 11:11
 */
namespace App\Http\Controllers\FrontEnd;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Models\Contact;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Mail\PasswordReset;





class ContactController extends Controller{

    // 记录用户发送的contact信息
    public  function contactUs(Request $request) {
        $request->created_at=date('Y-m-d H:i:s',time());
        $request->updated_at=date('Y-m-d H:i:s',time());
        DB::beginTransaction();
        try {
            Contact::create([
                'email' => $request->email,
                'subject' =>$request->subject,
                'contact_content' => $request->contact_content,
                'created_at' => date('Y-m-d H:i:s',time()),
                'updated_at' => date('Y-m-d H:i:s',time())
            ]);

            $emailData = ['email'=>getenv('CONTACT_EMAIL'),'message'=>"email:".$request->email."\r\n".$request->contact_content,'text'=>'','title'=>__('ac.ContactUsDirectly')];
            Mail::to(getenv('CONTACT_EMAIL'))->send(new PasswordReset($emailData));

            $res = Mail::failures();

            if(empty($res)) {
                DB::commit();
                return ['code'=>200];
            }
            DB::rollback();
            return ['code'=>400];
        }catch (\Exception $e) {
            Log::info($e->getMessage());
            dump($e->getMessage());
            return ['code'=>400];
        }
    }


}