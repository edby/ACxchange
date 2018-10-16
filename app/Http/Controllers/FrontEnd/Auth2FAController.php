<?php
/**
 * Created by PhpStorm.
 * User: ZRothschild
 * Date: 2018/1/8
 * Time: 16:23
 */

namespace App\Http\Controllers\FrontEnd;

use App\Events\AddCurr;
use App\Events\LoginAction;
use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Authy;
use App\SmsAuth;
use App\Tool\SmsSendConn;
use Exception;
use Illuminate\Support\Facades\Log;

class Auth2FAController extends Controller
{

    public static function authSelect($user,$check=false){
        $user = Auth::user() ? Auth::user() : $user;
        switch ($user['auth_type']){
            case 1:
                $dataGoogle = ['status'=>2,'len'=>6,'type'=>1,'check'=>$check,'title'=>__('ac.YouopenedCode')];
                return response()->json($dataGoogle);
            case 2:
                $dataAuthy = ['status'=>2,'len'=>7,'type'=>2,'check'=>$check,'title'=>__('ac.alreadyOpenedAuthy')];
                return response()->json($dataAuthy);
            case 3:
                $smsAuth = SmsAuth::where('user_id',$user['id'])->first();
                if($smsAuth){
                    try{
                        $code = mt_rand(1000,9999);
                        Cache::put('sms_'.$smsAuth->phone_number, $code, 5);
                        $request = new Request();
                        $request->code = $code;
                        $request->phone = $smsAuth->phone_number;
                        $request->region = $smsAuth->country_code;
                        $result = TestController::TestSubmit($request);
                        if (!$result === 0) {
                            return response()->json(['status'=>0,'message'=>__('ac.SendSmsFail')]);
                        }
                    }catch (\Exception $e){
                        return response()->json(['status'=>0,'message'=>__('ac.SendSmsFail')]);
                    }
                }
                $dataSMS = ['status'=>2,'len'=>4,'type'=>3,'check'=>$check,'title'=>__('ac.alreadyOpenedSMS')];
                return response()->json($dataSMS);
            default: return response()->json(['status'=>0,'message'=>__('ac.levelvalidationSet')]);
        }
    }
    /**
     * 所有验证 谷歌 authy sms
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public static function auth(Request $request)
    {
        $fun = User::$authType[$request->type];
        $result = self::$fun($request);
        return response()->json($result);
    }


    /**
     * 登陆二级密码谷歌验证
     * @param Request $request
     * @return array
     */
    private static function googleAuth($request)
    {
        try{
            $checkArray = ['true'=>true,'false'=>false];
            $user = $request->hook ? Auth::user() : User::where('email',$request->email)->first();
            if (empty($user)) return ['status'=>0,'message'=>__('ac.systemError')];
            $ga = new \PHPGangsta_GoogleAuthenticator();
            // 2 = 2*30sec clock tolerance
            $checkResult = $ga->verifyCode($user->secret, (string)$request->code, 2);
            Log::info('googleAuth',$request->all());
            if ($checkResult){
                if(!$request->hook) {
                    Auth::login($user, $checkArray[$request->check]);
                    event(new LoginAction($user));
                    event(new AddCurr(Auth::user()));
                }
                return ['status'=>1,'message'=>__('ac.successful')];
            }
            return ['status'=>0,'message'=>__('ac.systemError')];
        }catch (Exception $exception){
            Log::info('googleAuth',[$exception->getMessage()]);
            return ['status'=>0,'message'=>__('ac.systemError')];
        }
    }

    //----------------------------------------------+
    /**
     * 登陆二级密码Authy验证
     * @param Request $request
     * @return array
     */
    private static function authyVerification($request)
    {
        try{
            $checkArray = ['true'=>true,'false'=>false];
            $user = $request->hook ? Auth::user() : User::where('email',$request->email)->first();
            if (empty($user)) return ['status'=>0,'message'=>__('ac.systemError')];
            $authyApi = new \Authy\AuthyApi(env('AUTHY_API_KEY'));
            $authyId = Authy::where('user_id',$user->id)->value('authy_id');
            $authyId = decrypt($authyId);
            $checkResult = $authyApi->verifyToken($authyId, $request->code);
            Log::info('authyVerification',$request->all());
            if ($checkResult->ok()){
                if(!$request->hook){
                    Auth::login($user, $checkArray[$request->check]);
                    event(new LoginAction($user));
                    event(new AddCurr(Auth::user()));
                }
                return ['status'=>1,'message'=>'successful'];
            }
            return ['status'=>0,'message'=>__('ac.systemError')];
        }catch (Exception $exception){
            Log::info('authyVerification',[$exception->getMessage()]);
            return ['status'=>0,'message'=>__('ac.systemError')];
        }
    }
    //----------------------------------------------+
    /**
     * 登陆二级密码SMS验证
     * @param Request $request
     * @return array
     */
    private static function SMSVerification($request)
    {
        try{
            $checkArray = ['true'=>true,'false'=>false];
            $user = $request->hook ? Auth::user() : User::where('email',$request->email)->first();
            if($user){
                $smsPhome = SmsAuth::where('user_id',$user->id)->first()->phone_number;
                $code = Cache::get('sms_'.$smsPhome);
                if (empty($code)) return ['status'=>0,'message'=>__('ac.LoginFailed')];
                Log::info('SMSVerification',$request->all());
                Log::info('SMSVerification',[$request->code,$code]);
                if($request->code == $code){
                    if(!$request->hook) {
                        Auth::login($user, $checkArray[$request->check]);
                        event(new LoginAction($user));
                        event(new AddCurr(Auth::user()));
                    }
                    return ['status'=>1,'message'=>__('ac.successful')];
                }
            }
            return ['status'=>0,'message'=>__('ac.systemError')];
        }catch (Exception $exception){
            Log::info('SMSVerification',[$exception->getMessage()]);
            return ['status'=>0,'message'=>__('ac.systemError')];
        }
    }
}