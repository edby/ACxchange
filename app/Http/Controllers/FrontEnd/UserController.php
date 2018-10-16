<?php
/**
 * Created by PhpStorm.
 * User: ZRothschild
 * Date: 2018/1/12
 * Time: 17:46
 */

namespace App\Http\Controllers\FrontEnd;


use App\AuthInfo;
use App\CountryRegion;
use App\Http\Controllers\Controller;
use App\Http\Controllers\FrontEnd\sdk\api\SendSmsApi;
use App\IdCard;
use App\Mail\ChangeEmail;
use App\Mail\PasswordReset;
use App\Models\Apikey;
use App\Models\KYCRejectlist;
use App\Models\KYCRejectReason;
use App\Models\UserBind;
use App\Passport;
use App\SmsAuth;
use App\Tool\FileUpload;
use App\Tool\TmpCurl;
use App\Tool\WithdrawalControl;
use App\User;
use App\UserCurr;
use App\WithdrawLimit;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Validator;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Authy;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;


class UserController extends Controller
{
    /**
     *用户个人中心 titleI
     */
    public function index()
    {
        $countryRegion = CountryRegion::all();
        $user = Auth::user();
        $reasons = [];
        if($user->is_certification == 3){
            $reasons_ids = KYCRejectlist::where('user_id',$user->id)->first();
            $lang = App::getLocale();
            if($reasons_ids){
                $reasons_ids = json_decode($reasons_ids->reason_ids);
                foreach ($reasons_ids as $one){
                    $reasons[] = KYCRejectReason::where('id',$one)->value('reason_'.$lang);
                }
            }
        }
        $data = [
            'countryRegion'=>$countryRegion,
            'user' => $user,
            'reasons' => $reasons,
        ];

        $key = Apikey::where('user_id',Auth::id())->value('key');
        $secretKey = Apikey::where('user_id',Auth::id())->value('secret');
        $data['key'] = $key;
        $data['secretKey'] = $secretKey;
        //Authy验证信息
        $authy = AuthInfo::where('user_id',Auth::id())->where('auth_type',2)->first();
        $data['authy'] = $authy ? Authy::where('user_id',Auth::id())->first() : false;
        //Sms验证信息
        $sms = AuthInfo::where('user_id',Auth::id())->where('auth_type',3)->first();
        $data['sms'] = $sms ? SmsAuth::where('user_id',Auth::id())->first() : false;
        //谷歌验证
        $ga = new \PHPGangsta_GoogleAuthenticator();
        if (empty($user->secret)){
            $secret = $ga->createSecret();
            $qrCodeUrl = $ga->getQRCodeGoogleUrl(getenv('APP_TITLE'), $secret,null,['width'=>256,'height'=>256]);
            $data['qrCodeUrl'] = route('open')."?url=".$qrCodeUrl;
            $data['secret'] = $secret;
        }
        return view('front.user',$data,$reasons);
    }

    /**
     * 更新用户idCard 信息 eg 切记开启每个表的filled 或者 guard
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function authIdCard(Request $request)
    {
        try{
            //数据验证是否合规
            $validator = Validator::make($request->all(), [
                'first_name' => 'bail|required|string|max:50',
                'last_name' => 'bail|required|string|max:50',
                'nationality' => 'bail|required|string|max:50',
                'year' => 'bail|required|string|string',
                'month' => 'bail|required|string|string',
                'day' => 'bail|required|string|string',
                'residential_address' => 'bail|required|string|max:100',
                'region_ode' => 'bail|required|string',
                'phone' => 'bail|required|string|max:100',
                'img_front' => 'bail|mimes:jpeg,bmp,png|max:2097152',
                'img_back' => 'bail|mimes:jpeg,bmp,png|max:2097152',
                'img_hand' => 'bail|mimes:jpeg,bmp,png|max:2097152',
                'card_number' => 'bail|required|string|max:100',
            ]);
            if ($validator->fails()) {
                return response()->json(['status'=>0, 'message'=>$validator->errors()->first()]);
            }
            //图片上传返回路径数组
            $pathArr = FileUpload::fileUploads($request->allFiles());
            $only = $request->only(['card_number']);
            Auth::user()->update(['card_id'=>null]);
            //idCards 表插入
            $idCardResult = Auth::user()->IdCards()->save(new IdCard(array_merge($only,$pathArr)));
            //用户更新用户数据
            $except = $request->except(['img_front','img_back','img_hand','card_number','pFrontBin','pRearBin','pHandBin']);
            $birthday = $request->year.'-'.$request->month.'-'.$request->day;
            $result = $this->userUpdate($except,['card_id'=>$idCardResult->id,'passport_id'=>null,'birthday'=>$birthday,'is_certification'=>1,'certification_time'=>date('Y-m-d H:i:s')]);
            return response()->json(['status'=>1,'message'=>__('ac.successfully')]);
        }catch (\Exception $exception){
            $data['userId'] = Auth::id();
            $data['errorMsg'] = $exception->getMessage();
            Log::info('authIdCard',$data);
            return response()->json(['status'=>0,'message'=>$exception->getMessage()]);
        }
    }

    /**
     * 更新用户passport 信息
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function authPassport(Request $request)
    {
        try{
            //数据验证是否合规
            $validator = Validator::make($request->all(), [
                'first_name' => 'bail|required|string|max:50',
                'last_name' => 'bail|required|string|max:50',
                'nationality' => 'bail|required|string|max:50',
                'year' => 'bail|required|string|string',
                'month' => 'bail|required|string|string',
                'day' => 'bail|required|string|string',
                'residential_address' => 'bail|required|string|max:100',
                'region_ode' => 'bail|required|string',
                'phone' => 'bail|required|string|max:100',
                'img_front' => 'bail|mimes:jpeg,bmp,png|max:2097152',
                'img_back' => 'bail|mimes:jpeg,bmp,png|max:2097152',
                'passport_number' => 'bail|required|string|max:100',
            ]);
            if ($validator->fails()) {
                return response()->json(['status'=>0, 'message'=>$validator->errors()->first()]);
            }
            $pathArr = FileUpload::fileUploads($request->allFiles(),'passport');
            $only = $request->only(['passport_number']);
            Auth::user()->update(['passport_id'=>null]);
            //passports表插入
            $passportsResult = Auth::user()->Passports()->save(new Passport(array_merge($only,$pathArr)));
            $except = $request->except(['img_front','img_back','passport_number','pFrontBin','pRearBin']);
            $birthday = $request->year.'-'.$request->month.'-'.$request->day;
            $result = $this->userUpdate($except,['passport_id'=>$passportsResult->id,'card_id'=>null,'birthday'=>$birthday,'is_certification'=>1,'certification_time'=>date('Y-m-d H:i:s')]);
            return response()->json(['status'=>1,'message'=>__('ac.successfully')]);
        }catch (\Exception $exception){
            $data['userId'] = Auth::id();
            $data['errorMsg'] = $exception->getMessage();
            Log::info('authPassport',$data);
            return response()->json(['status'=>0,'message'=>$exception->getMessage()]);
        }
    }

    /**
     * 用户中心修改密码
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function changePassword(Request $request)
    {
        $validData = [
            'password' => 'bail|required|string|min:6|max:18|confirmed',
            'password_confirmation' => 'bail|required|string',
        ];
        $this->validate($request,$validData);
        //验证老旧密码是否一致
        $checkPas = Hash::check($request->old_password,Auth::user()->password);
        if (!$checkPas) return response()->json(['status'=>0,'message'=>__('ac.originalPassword')]);
        $user = Auth::user();
        $user->password = $request->password;
        $user->save();
        Auth::logout();
        return response()->json(['status'=>1]);
    }

    /**
     * 生成交易密码与修改交易密码
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function changePin(Request $request)
    {
        $validData = [
            'pin' => 'bail|required|string|min:6|confirmed',
            'pin_confirmation' => 'bail|required|string',
        ];
        $this->validate($request,$validData);
        //验证老旧密码是否一致 如果存在 old_pin 表示为修改
        if ($request->has('old_pin')){
            $checkPin = Hash::check($request->old_pin,Auth::user()->pin);
            if (!$checkPin) return response()->json(['status'=>0,'message'=>__('ac.originalPin')]);
        }
        $user = Auth::user();
        $user->pin = $request->pin;
        $user->save();
        return response()->json(['status'=>1,'message'=>__('ac.Success')]);
    }


    /**
     * 重新生成
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function resetPin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pin' => 'bail|required|string|min:6|confirmed',
            'pin_confirmation' => 'bail|required|string',
            'code' => 'bail|required|max:6|alpha_num',
        ]);
        if ($validator->fails()) {
            return response()->json(['status'=>0, 'message'=>$validator->errors()->first()]);
        }

        $email = $request->session()->get('emailPin');
        $cache = Cache::get($email);
        if (empty($email) || empty($cache)) return response()->json(['status'=>0,'message'=>__('ac.MailboxExpired')]);
        if ( $cache != $request->code) return response()->json(['status'=>0,'message'=>__('ac.IncorrectMailbox')]);

        $user = Auth::user();
        $user->pin = $request->pin;
        $user->save();
        return response()->json(['status'=>1,'message'=>__('ac.Success')]);
    }


    /**
     * 忘记pin 发送邮件
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function pinForgetSendEmail(Request $request)
    {
        $email = Auth::user()->email;
        $request->session()->put('emailPin',$email);
        $randCode = random_int(100000,999999);
        Cache::forget($email);
        Cache::add($email,$randCode,10);
        $emailData = ['email'=>$email,'message'=>$randCode,'text'=>__('ac.ResetPINText'),'title'=>__('ac.ResetPin')];
        $flag = Mail::to($email)->send(new PasswordReset($emailData));
        return response()->json(['status'=>1,'message'=>__('ac.Success')]);
    }


    /**
     * 修改邮箱
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function changeEmail(Request $request)
    {
        try{
            $this->validate($request,['email' => 'bail|required|email|max:255|unique:users']);
            $token = base64_encode(Hash::make($request->email));
            Cache::add($token,['email'=>$request->email,'userId'=>Auth::id()],1440);
            $data = ['email'=>Auth::user()->email,'newEmail'=>$request->email,'url'=>'user/restEmail/'.$token];
            Mail::to(Auth::user()->email)->send(new ChangeEmail($data));
            return response()->json(['status'=>1,'message'=>__('ac.MailDelivery')]);
        }catch (\Exception $e){
            return response()->json(['status'=>0,'message'=>$e->getMessage()]);
        }
    }

    /**
     * 重置邮箱
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function restEmail(Request $request)
    {
        $data = Cache::pull($request->token);
        if (!Hash::check($data['email'],base64_decode($request->token))) return redirect('/index/restEmail');;
        $count = User::find($data['userId'])->update(['email'=>$data['email']]);
        if (Auth::check()) Auth::logout();
        return redirect('/login/restEmail');
    }

    /**
     * 谷歌验证
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function googleAuth(Request $request)
    {
        try{
            $ga = new \PHPGangsta_GoogleAuthenticator();
            // 2 = 2*30sec clock tolerance
            $checkResult = $ga->verifyCode($request->secret, $request->code, 4);
            if (!$checkResult) return response()->json(['status'=>0,'message'=>__('ac.Fail')]);
            if (Auth::check()){
                $updateData['secret'] = $request->secret;
                if (Auth::user()->auth_type == 0) $updateData['auth_type'] = 1;
                $updateResult = Auth::user()->update($updateData);
                if ($updateResult != 1) return response()->json(['status'=>0,'message'=>'Update Fail']);
                AuthInfo::create(['user_id'=>Auth::user()->id,'auth_type'=>1]);
            }

            $withdrawal = new WithdrawalControl();
            $kyc = $withdrawal->accessKyc(Auth::user()->id);
            if ($kyc) {
                $data = [
                    'level' => 1,
                    'btc_balance' => 2,
                    'withdraw_time' => time(),
                ];
                WithdrawLimit::updateOrCreate(['user_id' => Auth::user()->id],$data);
            }

            return response()->json(['status'=>1,'message'=>__('ac.Success')]);
        }catch (\Exception $e){
            return response()->json(['status'=>0,'message'=>$e->getMessage()]);
        }
    }


    /**
     * Authy注册信息
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    //aua--------------------------------------------------
    public function authyAuth(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'region'    => 'required',
            'phone'     => 'required|regex:/^\d{6,16}$/',
            'email'     => 'required|email'
        ]);
        if ($validator->fails()) {
            return response()->json(['status'=>0, 'message'=>$validator->errors()->first()]);
        }
        $user_authy = Authy::where('user_id',\Auth::id())->where('verified',1)->count();
        if($user_authy > 0) {
            return response()->json(['code'=>402,'message'=>__('ac.AlreadyAuthy')]);
        }
        DB::beginTransaction();
        $authyApi = new \Authy\AuthyApi(env('AUTHY_API_KEY'));
        $authyUser = $authyApi->registerUser($request->email, $request->phone, $request->region);
        if ($authyUser->ok()) {
            try {
                Authy::updateOrCreate(
                    ['user_id'       => \Auth::id()],
                    ['country_code'  => $request->region,
                        'phone_number'  => $request->phone,
                        'authy_id'      => encrypt($authyUser->id()),
                        'verified'      => 0,
                        'email'         =>$request->email]
                );
            } catch (\Exception $e) {
                DB::rollback();
                return response()->json(['status'=>0,'message'=>__('ac.PhoneSame')]);
            }
            //Send Authy code SMS
            $authyApi->requestSms($authyUser->id());
            DB::commit();
            return response()->json(['status'=>1,'message'=>__('ac.AlreadySendSms')]);
        } else {
            DB::rollback();
            return response()->json(['status'=>0,'message'=>__('ac.AuthyError')]);
        }
    }
    /** 设置2FA时的验证
     * @param Request $request
     * @return array
     */
    public function setAuthyVerify(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'authy_code'    => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['status'=>0, 'message'=>$validator->errors()->first()]);
        }
        $user_id = Auth::id();
        if($this->checkAuthy($user_id,$request->authy_code)) {
            try {
                Authy::where('user_id',$user_id)->update(['verified'=>1]);
                AuthInfo::create(['user_id'=>$user_id,'auth_type'=>2]);
                $user = User::find($user_id);
                if($user->auth_type == 0)
                    $user->update(['auth_type'=>2]);
            }catch (\Exception $e) {
                return response()->json(['status'=>0,'message'=>__('ac.FailureVerified')]);
            }

            $withdrawal = new WithdrawalControl();
            $kyc = $withdrawal->accessKyc(Auth::user()->id);
            if ($kyc) {
                $data = [
                    'level' => 1,
                    'btc_balance' => 2,
                    'withdraw_time' => time(),
                ];
                WithdrawLimit::updateOrCreate(['user_id' => Auth::user()->id],$data);
            }

            return response()->json(['status'=>1,'message'=>__('ac.Success!')]);
        }else{
            return response()->json(['status'=>0,'message'=>__('ac.AuthyErrorCode')]);
        }
    }
    public static function checkAuthy($user_id,$token)
    {
        $authyApi = new \Authy\AuthyApi(env('AUTHY_API_KEY'));
        $authyId = Authy::where('user_id',$user_id)->first()->authy_id;
        $authyId = decrypt($authyId);
        $verification = $authyApi->verifyToken($authyId, $token);
        if($verification->ok()){
            return true;
        }else{
            return false;
        }
    }
    //-------------------SMS-----------------------------------------------

    /**
     * SMS 注册验证
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function smsAuth(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'region'    => 'required',
            'phone'     => 'required|regex:/^\d{6,16}$/',
        ]);
        if ($validator->fails()) {
            return response()->json(['status'=>0, 'message'=>$validator->errors()->first()]);
        }
        $sms_auth = SmsAuth::where('user_id',\Auth::id())->where('verified',1)->count();
        if($sms_auth > 0) {
            return response()->json(['code'=>402,'message'=>__('ac.AlreadySms')]);
        }
        try {
            $code = mt_rand(1000,9999);
            Cache::put('sms_'.$request->phone, $code, 5);
            $request->code = $code;
            $result = TestController::TestSubmit($request);
            if ($result === 0) {
                SmsAuth::updateOrCreate(
                    ['user_id'       => Auth::id()],[
                        'country_code'  => $request->region,
                        'phone_number'  => $request->phone,
                        'verified'      => 0]
                );
                return response()->json(['status'=>1,'message'=>__('ac.AlreadySendSms')]);
            } else {
                return response()->json(['status'=>0,'message'=>__('ac.SMSCodeError')]);
            }
        }catch (\Exception $e) {//号码被注册
//            return response()->json(['status'=>0,'message'=>$e->getMessage()]);
            return response()->json(['status'=>0,'message'=>__('ac.PhoneSame')]);
        }

    }

    /** 设置SMS Auth时的验证
     * @param Request $request
     * @return array
     */
    public function setSmsVerify(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'sms_code'    => 'required',
            'phone_number'    => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['status'=>0, 'message'=>$validator->errors()->first()]);
        }
        $user_id = Auth::id();
        if($request->sms_code == Cache::get('sms_'.$request->phone_number)) {
            try {
                SmsAuth::where('user_id',$user_id)->update(['verified'=>1]);
                AuthInfo::create(['user_id'=>$user_id,'auth_type'=>3]);
                $user = User::find($user_id);
                if($user->auth_type == 0)
                    $user->update(['auth_type'=>3]);
            }catch (\Exception $e) {
                return response()->json(['status'=>0,'message'=>__('ac.FailureVerified')]);
            }

            $withdrawal = new WithdrawalControl();
            $kyc = $withdrawal->accessKyc(Auth::user()->id);
            if ($kyc) {
                $data = [
                    'level' => 1,
                    'btc_balance' => 2,
                    'withdraw_time' => time(),
                ];
                WithdrawLimit::updateOrCreate(['user_id' => Auth::user()->id],$data);
            }

            return response()->json(['status'=>1,'message'=>__('ac.Success')]);
        }else{
            return response()->json(['status'=>0,'message'=>__('ac.SMSCodeError')]);
        }
    }
//-----------------Public--------------------------------------------------
    /** 设置用户默认的2fa验证方式
     * @param Request $request
     * @return array
     */
    public function setAuthDefault(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type'  => 'required|integer|min:1|max:3'
        ]);
        if ($validator->fails()) {
            return response()->json(['status'=>0, 'message'=>$validator->errors()->first()]);
        }
        switch ($request->type){
            case 1: $authName = 'Google'; break;
            case 2: $authName = 'Authy'; break;
            case 3: $authName = 'SMS'; break;
            default: $authName = ''; break;
        }
        $user_id = Auth::id();
        $auth_info = AuthInfo::where('user_id',Auth::id())->pluck('auth_type');
        if(($auth_info->search($request->type) === false )) {
            return response()->json(['status'=>0,'message'=>'You have not yet registered '.$authName]);
        }
        try {
            User::where('id',$user_id)->update(['auth_type'=>$request->type]);
        } catch (\Exception $e) {
            return response()->json(['status'=>0,'message'=>__('ac.Fail')]);
        }
        return response()->json(['status'=>1,'message'=>__('ac.Success')]);
    }

//----------------------------------------------------------------------
    /**
     *绑定user_id 使用API
     * @param Request $request
     * @return \Illuminate\Http\JsonResponses
     */
    public function bindApiUser(Request $request)
    {
        if (!empty(Auth::user()->bind_user_id)) return response()->json(['status'=>0,'message'=>__('ac.accountTryAnother')]);

        $this->validate($request,[
            'userName' => 'bail|required',
            'bindPass' => 'bail|required'
        ]);
        $api = 'https://api.alliancecapitals.com/check.php';
        $token = md5(rand(10000,99999));
        $route = substr($api, strpos($api, '/'));
        $time = time();
        $nonce = rand();
        $str = 'time'.$time."controller$nonce@action/".$route;
        $signature = sha1($token.$str.$token);
        $params = [];
        $array = array('uname'=>$request->userName,'password'=>$request->bindPass,'method'=>'check_login');
        //method = check_login | uname = 登陆账号 | password = 密码 ** 返回 user_id 或者 false
        //method = profile | user_id =  user id | 返回 账号uname 或者false
        $array = json_encode($array);
        $headers = [
            "nonce:$nonce",
            "route:$route",
            "timestamp:$time",
            "signature:$signature",
            "token:$token",
            "params:$array",
        ];
        $curl = new TmpCurl();
        $userId = $curl->postAPI($api,$params,$headers);
        if (!is_numeric($userId)) return response()->json(['status'=>0,'message'=>__('ac.passwordenteredisincorrect')]);
        $result = Auth::user()->update(['bind_user_id'=>$userId]);
        DB::table('user_fee')->where('user_id',Auth::id())->update(['fee_rate'=>0.001]);
        $data = [
            'user_id'=> Auth::id(),
            'fee_new'=> 0.001,
            'fee_old'=> 0.002,
        ];
        UserBind::create($data);
        self::bindUserInfo($userId);
        if (empty($result)) return response()->json(['status'=>0,'message'=>__('ac.passwordenteredisincorrect')]);
        return response()->json(['status'=>1,'message'=>__('ac.successfullybound')]);
    }

    /**
     * 获取 bind info
     * @param $user_bind_id
     * @return \Illuminate\Http\JsonResponses
     */
    public static function bindUserInfo($bind_user_id)
    {
        try{
            $api = 'https://api.alliancecapitals.com/check.php';
            $token = md5(rand(10000,99999));
            $route = substr($api, strpos($api, '/'));
            $time = time();
            $nonce = rand();
            $str = 'time'.$time."controller$nonce@action/".$route;
            $signature = sha1($token.$str.$token);
            $params = [];
            $array = array('user_id'=>$bind_user_id,'method'=>'profile');
            $array = json_encode($array);
            $headers = [
                "nonce:$nonce",
                "route:$route",
                "timestamp:$time",
                "signature:$signature",
                "token:$token",
                "params:$array",
            ];
            $curl = new TmpCurl();
            $info = $curl->postAPI($api,$params,$headers);
            User::where('bind_user_id',$bind_user_id)->update(['bind_info'=>$info]);
            return $info;
        }catch (\Exception $e){
            Log::info($bind_user_id.' bindUserInfo error ----- '.$e->getMessage());
            return 'error';
        }
    }

    /**
     * 获取已定义验证规则的错误消息。
     *
     * @return array
     */
    public function messages()
    {
        return [
            'userName.required' => __('ac.thanCharacters'),
            'bindPass.required'  =>  __('ac.thanCharacters'),
            'bindPass.min' =>  __('ac.thanCharacters'),
        ];
    }

    /**
     * 重置Authy验证
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function restAuthy(Request $request)
    {
        try{
            if(!Hash::check($request->pin,Auth::user()->pin)){
                return $this->ajax_jason(['status'=>0,'message'=>__('ac.pinError')]);
            }
            DB::beginTransaction();
            Authy::where([['user_id','=',Auth::id()],['verified','=',1]])->update(['verified'=>0]);
            AuthInfo::where([['user_id','=',Auth::id()],['auth_type','=',2]])->delete();
            if (Auth::user()->auth_type == 2){
                $auth = AuthInfo::where('user_id',Auth::id())->first(['auth_type']);
                if (empty($auth)){
                    Auth::user()->update(['auth_type'=>0]);
                }else{
                    Auth::user()->update(['auth_type'=>$auth->auth_type]);
                }
            }
            DB::commit();
            return $this->ajax_jason(['status'=>1,'message'=>__('ac.Rest')]);
        }catch (\Exception $exception){
            DB::rollback();
            return $this->ajax_jason(['status'=>0,'message'=>__('ac.ResetFailed'),'data'=>$exception->getMessage()]);
        }
    }

    /**
     * 重置sms
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function restSmS(Request $request)
    {
        try{
            if(!Hash::check($request->pin,Auth::user()->pin)){
                return $this->ajax_jason(['status'=>0,'message'=>__('ac.pinError')]);
            }
            DB::beginTransaction();
            SmsAuth::where([['user_id','=',Auth::id()],['verified','=',1]])->update(['verified'=>0]);
            AuthInfo::where([['user_id','=',Auth::id()],['auth_type','=',3]])->delete();
            if (Auth::user()->auth_type == 3){
                $auth = AuthInfo::where('user_id',Auth::id())->first(['auth_type']);
                if (empty($auth)){
                    Auth::user()->update(['auth_type'=>0]);
                }else{
                    Auth::user()->update(['auth_type'=>$auth->auth_type]);
                }
            }
            DB::commit();
            return $this->ajax_jason(['status'=>1,'message'=>__('ac.Rest')]);
        }catch (\Exception $exception){
            DB::rollback();
            return $this->ajax_jason(['status'=>0,'message'=>__('ac.ResetFailed'),'data'=>$exception->getMessage()]);
        }
    }


    /**
     * 重置google
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function restGoogle(Request $request)
    {
        try{
            if(!Hash::check($request->pin,Auth::user()->pin)){
                return $this->ajax_jason(['status'=>0,'message'=>__('ac.pinError')]);
            }
            DB::beginTransaction();
            AuthInfo::where([['user_id','=',Auth::id()],['auth_type','=',1]])->delete();
            if (Auth::user()->auth_type == 1){
                $auth = AuthInfo::where('user_id',Auth::id())->first(['auth_type']);
                if (empty($auth)){
                    Auth::user()->update(['auth_type'=>0,'secret'=>'']);
                }else{
                    Auth::user()->update(['auth_type'=>$auth->auth_type,'secret'=>'']);
                }
            }
            DB::commit();
            return $this->ajax_jason(['status'=>1,'message'=>__('ac.Rest')]);
        }catch (\Exception $exception){
            DB::rollback();
            return $this->ajax_jason(['status'=>0,'message'=>__('ac.ResetFailed'),'data'=>$exception->getMessage()]);
        }
    }


    /**
     *用户上传验证
     * @param Request $request
     * @param string $type
     * @param bool $independent
     * @return mixed
     */
    protected function userValida(Request $request,$type,$independent = false)
    {
        $base = [
            'first_name' => 'bail|required|string|max:50',
            'last_name' => 'bail|required|string|max:50',
            'nationality' => 'bail|required|string|max:50',
            'year' => 'bail|required|string|string',
            'month' => 'bail|required|string|string',
            'day' => 'bail|required|string|string',
            'residential_address' => 'bail|required|string|max:100',
            'region_ode' => 'bail|required|string',
            'phone' => 'bail|required|string|max:100',
        ];
        $alternative = [
            'idCard' => [
                'img_front' => 'bail|mimes:jpeg,bmp,png|max:2097152',
                'img_back' => 'bail|mimes:jpeg,bmp,png|max:2097152',
                'img_hand' => 'bail|mimes:jpeg,bmp,png|max:2097152',
                'card_number' => 'bail|required|string|max:100',
            ],
            'passport' => [
                'img_front' => 'bail|mimes:jpeg,bmp,png|max:2097152',
                'img_back' => 'bail|mimes:jpeg,bmp,png|max:2097152',
                'passport_number' => 'bail|required|string|max:100',
            ],
        ];
        $data = array_merge($base,$alternative[$type]);
        if ($independent) $data = $alternative[$type];
        return $this->validate($request,$data);
    }

    /**
     * 上传更新数据User表 返回修改的条数
     * @param array $data
     * @param array $additional
     * @return int
     */
    protected function userUpdate(array $data,array $additional = [])
    {
        $tmp = array_merge($data,$additional);
        return Auth::user()->update($tmp);
    }
}