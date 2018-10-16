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
use App\Events\RegisterSendEmail;
use App\Library\Currency\RegisterAddCurr;
use App\Mail\PasswordReset;
use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Mews\Captcha\Facades\Captcha;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{

    /**
     * 用户开始操作 包括登陆 注册 找回密码
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function login(Request $request)
    {
       // dump($request->all());
        //用于提示验证是否通过
        $data = ['restEmail'=>$request->type];
        $data['register'] = '';
        $data['current'] = '';
        $currentBool = $request->has('current');
        if ($currentBool) $data['current'] = $request->current;
        //邮箱确认
        if ($request->has('register')){
            $email = base64_decode($request->register);
            $email = Cache::get($email);
            if (!empty($email)){
                $registerConfirm = User::where('email',$email)->value('register_confirm');
                if ($registerConfirm == 1){//
                    $data['register'] = __('ac.EmailVerified');
                }else{
                    User::where('email',$email)->update(['register_confirm'=>1]);
                    Cache::forget($email);
                    $data['register'] = __('ac.registerConfirm');
                }
            }
        }
        return view('front.login',$data);
    }

    /**注册成功验证邮箱
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function confirmRegisterToken(Request $request)
    {
        //更新状态
        $bool = $request->has('register');
        if (!$bool) abort(404);
        $baseDecode = base64_decode($request->register);
        $email = decrypt($baseDecode);
       // User::where('email',$email)->update(['register_confirm'=>1]);
        if (!Cache::get($email)) Cache::forever($email,$email);
        //dd($request->all());
        return redirect('/login?register='.base64_encode($email));
    }

    /**
     * 用户登陆操作 ajax
     * @param mixed $request 请求数据
     * @return mixed
     */
    public function loginAction(Request $request)
    {
        $this->validate($request,[
            'email' => 'bail|required|string|email|max:100',
            'password' => 'bail|required|string|min:6|max:18',
        ]);

        $user = User::where('email',$request->email)->first(['id','auth_type','secret','password','register_confirm']);
        if (empty($user)) return response()->json(['status'=>0,'message'=>__('ac.NotRegisteredEmail')]);

        $registerConfirm = $user->register_confirm;
        if (empty($registerConfirm)) return response()->json(['status'=>0,'message'=>__('ac.pleaseVerifyEmail')]);
        $checkArray = ['true'=>true,'false'=>false];
        if (empty($user->auth_type) && Auth::attempt($request->except('check'),$checkArray[$request->check])){
            event(new LoginAction(Auth::user()));
            event(new AddCurr(Auth::user()));
            return response()->json(['status'=>1]);
        }elseif (empty($user->auth_type) && !Auth::attempt($request->except('check'),$checkArray[$request->check])){
            return response()->json(['status'=>0,'message'=>__('ac.LoginFailed')]);
        }
        if (!Hash::check($request->password,$user->password)){
            return response()->json(['status'=>0,'message'=>__('ac.LoginFailed')]);
        }
        $authSelect = Auth2FAController::authSelect($user,$checkArray[$request->check]);
        return $authSelect ? $authSelect : response()->json(['status'=>0,'message'=>__('ac.authselectfail')]);
    }

    /**
     * 用户注册操作 ajax
     * @param mixed $request 请求数据
     * @return mixed
     */
    public function registerAction(Request $request)
    {
        $this->validate($request,[
            'name' => 'bail|required|string|max:25',
            'email' => 'bail|required|string|email|max:255|unique:users|confirmed',
            'email_confirmation' => 'bail|required|string|email',
            'password' => 'bail|required|string|min:6|max:18|confirmed',
            'password_confirmation' => 'bail|required|string',
            'check'=>'bail|accepted',
        ]);
        $resCaptcha = captcha_check($request->captcha);

        if (!$resCaptcha) return response()->json(['status'=>0,'message'=>__('ac.VerificationCodeError')]);
        if (stripos($request->name,' ')) return response()->json(['status'=>0,'message'=>__('ac.NamePaces')]);

        $condition = [
            'login_ip' => $request->getClientIp(),
            'last_login' => time(),
        ];
        //创建用户
        DB::beginTransaction();
        $result = $this->create($request->all(),$condition);
        //为用户生成货币地址
        try{
            $addBool = RegisterAddCurr::addCurrency($result->id);
        }catch (\Exception $exception){
            $message = $exception->getMessage();
            DB::rollBack();
            return response()->json(['status'=>0,'message'=>$exception->getCode(),'data'=>$message]);

        }
        DB::commit();
        //注冊成功發送郵件
        event(new RegisterSendEmail($request->email));
        return response()->json(['status'=>1,'message'=>__('ac.RegistrationSuccessful')]);
    }

    /**
     * 用户密码找回  ajax step1 接收邮箱发送验证码
     * @param mixed $request 请求数据
     * @return mixed
     */
    public function restPrdOne(Request $request)
    {
        $this->validate($request,[
            'email' => 'bail|required|email|max:255|exists:users',
            'step' => 'bail|size:1',
        ]);
        $request->session()->put('email',$request->email);
        $randCode = random_int(100000,999999);
        Cache::forget($request->email);
        Cache::add($request->email,$randCode,10);
        $emailData = ['email'=>$request->email,'message'=>$randCode,'text'=>__('ac.PasswordResetText'),'title'=>__('ac.PasswordReset')];
        $flag = Mail::to($request->email)->send(new PasswordReset($emailData));
        return response()->json(['status'=>1,'email'=>$request->email,'message'=>__('ac.MailDelivery')]);
    }

    /**
     * 用户密码找回  ajax step2 验证码验证
     * @param mixed $request 请求数据
     * @return mixed
     */
    public function restPrdTwo(Request $request)
    {
        $this->validate($request,[
            'code' => 'bail|required|max:6|alpha_num',
            'step' => 'bail|size:1',
        ]);
        $email = $request->session()->get('email');
        $cache = Cache::get($email);
        if (empty($email) || empty($cache)) return response()->json(['status'=>0,'message'=>__('ac.MailboxExpired')]);
        if ( $cache != strtolower($request->code)) return response()->json(['status'=>0,'message'=>__('ac.Incorrectcode')]);
        return response()->json(['status'=>1,'message'=>__('ac.VerificationValidation')]);
    }

    /**
     * 用户密码找回  ajax step3 重置密码
     * @param mixed $request 请求数据
     * @return mixed
     */
    public function restPrdThree(Request $request)
    {
        $this->validate($request,[
            'password' => 'bail|required|string|min:7|confirmed',
            'password_confirmation' => 'bail|required|string',
            'step' => 'bail|size:1',
        ]);
        $email = $request->session()->get('email');

        User::where('email',$email)->update(['password'=>Hash::make($request->password),'register_confirm'=>1]);
        return response()->json(['status'=>1,'message'=>__('ac.successful')]);
    }

    /**
     * 设置语言环境
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function setLocale(Request $request)
    {
        session(['setLocale'=>$request->lang]);
        Cache::forever(Auth::id().'_lang',$request->lang);
        return response()->json(['status'=>1,'message'=>__('ac.successful'),'data'=>['lang'=>$request->lang]]);
    }

    public function test()
    {
        $emailData = ['email'=>'2329852037@qq.com','message'=>route('confirmRegisterToken',['register'=>base64_encode(encrypt('2329852037@qq.com'))]),'text'=>'Register','title'=>''];
        Mail::to('2329852037@qq.com')->send(new PasswordReset($emailData));
    }

    /**
     * Create a new user instance after a valid registration.
     * @param  array  $data
     * @param array $additional
     * @return \App\User
     */
    protected function create(array $data,array $additional = [])
    {
        $tmp = array_merge($data,$additional);
        return User::create($tmp);
    }

    //后台----------------------------------------------------
    public static function loginAs(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:users,id',
            'token' => 'required'
        ]);
        if ($validator->fails()) {
            return ['code' => 400, 'message' => $validator->errors()->first()];
        }
        $cacheToken = Cache::pull('login_as_' . $request->id);
        if ($cacheToken == $request->token) {
            \Auth::loginUsingId($request->id);
            return redirect('/');
        }
        abort('404');
    }
    //-------------------------------------------------------
}