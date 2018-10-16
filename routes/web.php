<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
use Illuminate\Support\Facades\Auth;
Route::domain(env('APP_URL'))->middleware(['guest','SetLocale'])->namespace('FrontEnd')->group(function () {
    //LoginController
    Route::get('/login/{type?}', 'LoginController@login')->name('login');
    Route::get('/login', 'LoginController@login')->name('loginRegister');

    Route::get('/login', 'LoginController@login')->name('loginCurrent');

    Route::get('/confirmRegisterToken', 'LoginController@confirmRegisterToken')->name('confirmRegisterToken');

    Route::post('/loginAction', 'LoginController@loginAction')->middleware('StrToLower');
    Route::post('/registerAction','LoginController@registerAction')->middleware('StrToLower');
    Route::post('/restPrdOne', 'LoginController@restPrdOne')->middleware('StrToLower');
    Route::post('/restPrdTwo', 'LoginController@restPrdTwo');
    Route::post('/restPrdThree', 'LoginController@restPrdThree');
//    Route::post('/login/auth', 'LoginController@auth')->middleware('StrToLower');
    Route::get('/test/test/', 'LoginController@test');




    Route::post('/user/auth', 'Auth2FAController@auth')->middleware('StrToLower');
    //--留言
    //Route::post('/contact','ContactController@contactUs');//->middleware('StrToLower');//->name('contact')->middleware('throttle:2');

    //后台-------------------------------------------------
    Route::get('/loginAs', 'LoginController@loginAs');
    //----------------------------------------------------

});

//new
Route::domain(env('APP_URL_NEW'))->middleware(['guest','SetLocale'])->namespace('FrontEnd')->group(function () {
    //LoginController
    Route::get('/login/{type?}', 'LoginController@login')->name('login');
    Route::get('/login', 'LoginController@login')->name('loginRegister');

    Route::get('/login', 'LoginController@login')->name('loginCurrent');

    Route::get('/confirmRegisterToken', 'LoginController@confirmRegisterToken')->name('confirmRegisterToken');

    Route::post('/loginAction', 'LoginController@loginAction')->middleware('StrToLower');
    Route::post('/registerAction','LoginController@registerAction')->middleware('StrToLower');
    Route::post('/restPrdOne', 'LoginController@restPrdOne')->middleware('StrToLower');
    Route::post('/restPrdTwo', 'LoginController@restPrdTwo');
    Route::post('/restPrdThree', 'LoginController@restPrdThree');
//    Route::post('/login/auth', 'LoginController@auth')->middleware('StrToLower');
    Route::get('/test/test/', 'LoginController@test');
    Route::post('/user/auth', 'Auth2FAController@auth')->middleware('StrToLower');
    //--留言
    //Route::post('/contact','ContactController@contactUs');//->middleware('StrToLower');//->name('contact')->middleware('throttle:2');

    //后台-------------------------------------------------
    Route::get('/loginAs', 'LoginController@loginAs');
    //----------------------------------------------------

});

Route::domain(env('APP_URL'))->middleware(['auth','SetLocale'])->group(function () {
    Route::post('/user/createApiKey', 'Api\ApiController@createUserApikeys');
});


Route::domain(env('APP_URL_NEW'))->middleware(['auth','SetLocale'])->group(function () {
    Route::post('/user/createApiKey', 'Api\ApiController@createUserApikeys');
});


Route::domain(env('APP_URL'))->middleware(['auth','SetLocale'])->namespace('FrontEnd')->group(function () {
    Route::get('/index/logOut', 'IndexController@logOut');

    Route::get('/help/index', 'HelpController@index');
    Route::get('/help/contactUs', 'HelpController@contactUs');
    Route::get('/help/en', 'HelpController@en');
    Route::get('/help/ch', 'HelpController@cn');

    Route::get('/order/index', 'OrderController@index');
   // Route::get('/order/openOrder', 'OrderController@openOrder');
    Route::get('/order/openOrder', 'ACOrderController@openOrder');
    //Route::get('/order/orderHistory', 'OrderController@orderHistory');
    Route::get('/order/orderHistory', 'ACOrderController@history');

    Route::post('/order/getFeeRate', 'OrderController@getFeeRate');
    //Route::post('/order/cancelOrder', 'OrderController@cancelOrder');
    Route::post('/order/cancelOrder', 'ACOrderController@cancel');

    Route::post('/user/upload', 'UserController@upload');
    Route::post('/user/authIdCard', 'UserController@authIdCard');
    Route::post('/user/authPassport', 'UserController@authPassport');
    Route::post('/user/changePassword', 'UserController@changePassword');
    Route::post('/user/changePin', 'UserController@changePin');
    Route::post('/user/changeEmail', 'UserController@changeEmail')->middleware('StrToLower');
    Route::post('/user/bindApiUser', 'UserController@bindApiUser');
    Route::post('/user/pinForgetSendEmail', 'UserController@pinForgetSendEmail');
    Route::post('/user/resetPin', 'UserController@resetPin');
    //重置2FA
    Route::post('/user/restAuthy', 'UserController@restAuthy');
    Route::post('/user/restSmS', 'UserController@restSmS');
    Route::post('/user/restGoogle', 'UserController@restGoogle');

    Route::get('/user/index', 'UserController@index');



    //--获取充值订单
    Route::get('/wallet/getDepositHistory', 'DepositController@getDepositHistory');
    Route::get('/Test/get_update_yester_balance', 'TestController@get_update_yester_balance');
    Route::get('/Test/test_update_yester_balance', 'TestController@test_update_yester_balance');

    //钱包保证金
    Route::post('/wallet/depositCurrency', 'WalletController@depositCurrency');
    Route::post('/wallet/market', 'WalletController@market');
    Route::post('/wallet/withdraw', 'WalletController@withdraw');
    Route::post('/wallet/restWithdrawEmail', 'WalletController@restWithdrawEmail');
    Route::get('/wallet/orderMarket', 'WalletController@orderMarket');
    Route::get('/wallet/withdrawalInterval', 'WalletController@withdrawalInterval');
    Route::get('/wallet/minLimit', 'WalletController@minLimit');
    //Route::post('/wallet/currencyTrade', 'WalletController@currencyTrade');
    Route::post('/wallet/currencyTrade', 'ACXWalletController@currencyTrade_001');
    //Route::post('/wallet/tradeTemp', 'WalletController@tradeTemp');
    Route::post('/wallet/assets', 'WalletController@assets');
    Route::post('/wallet/getFullName', 'WalletController@getFullName');
    Route::post('/wallet/maxWithdraw', 'WalletController@maxWithdraw');

    Route::get('/wallet/index', 'WalletController@index')->name('wallet');
    Route::post('/wallet/authSelect','Auth2FAController@authSelect');//Auth Select


    Route::post('/trade/index', 'TradeController@index');
    Route::post('/trade/deposit', 'TradeController@deposit');
    Route::post('/trade/currList', 'TradeController@currList');
    Route::post('/trade/charts', 'TradeController@charts');
    Route::post('/trade/tranSummary', 'TradeController@tranSummary');
    Route::post('/trade/price', 'TradeController@price');
    Route::post('/trade/tradeTran', 'TradeController@tradeTran');




    //Route::get('/trade/myOpenOrder', 'TradeController@myOpenOrder');
    Route::get('/trade/myOpenOrder', 'ACXWalletController@getOpenOrders');


    //--获取可用余额
    Route::get('/withdraw/getBlance', 'WithdrawController@getBlance');

    //Route::get('/trade/tradeOrder', 'TradeController@tradeOrder');
    Route::get('/trade/tradeOrder', 'ACXWalletController@unCompeleteOrder');
   // Route::get('/trade/marketHistory', 'TradeController@marketHistory');
    Route::get('/trade/marketHistory', 'ACXWalletController@getHistory');
    Route::get('/trade/index', 'TradeController@index');

    //---------------------------------------------------------------------
    Route::get('/user/authy/auth', 'UserController@authyAuth');//Register Authy
    Route::post('/user/authy/check', 'UserController@setAuthyVerify');//Check Authy
    Route::get('/user/sms/auth', 'UserController@smsAuth');//SMS auth
    Route::post('/user/sms/check', 'UserController@setSmsVerify');//Check SMS
    Route::post('/user/set_auth_default','UserController@setAuthDefault');//Set Auth Default

    //---------------------------------------------------------------------

    Route::get('/open/url', 'OpenController@open')->name('open');


});

Route::domain(env('APP_URL'))->middleware(['SetLocale'])->namespace('FrontEnd')->group(function () {

    //邮件格式预览 start
    Route::get('/ChangeEmail',function (){
        $data = ['email'=>'aaaaaaaaaa','newEmail'=>'00000000','url'=>'user/restEmail/'];
        return new \App\Mail\ChangeEmail($data);
    });
    Route::get('/notiadmin',function (){
        $emailData = ['emaildata'=>"1111111111"];
        return new \App\Mail\NotiAdmin($emailData);
    });

    Route::get('/PasswordReset',function (){
        $emailData = ['email'=>"aaaaaaaaaaa",'message'=>"aaaaaaaa",'text'=>__('ac.ResetPINText'),'title'=>__('ac.ResetPin')];
        return new \App\Mail\PasswordReset($emailData);
    });

    Route::get('/Announcement',function (){
        return new \App\Mail\Announcement();
    });
    //邮件格式预览 end
    Route::post('/contact','ContactController@contactUs');
    //--不登录，登录都可以访问
    Route::post('/login/setLocale', 'LoginController@setLocale');
    //--开始工作
    Route::get('/task/work','TaskController@work');
   
    //--
    Route::get('/task/total','TaskController@get_count');


    Route::get('/list/get', 'ACXCronController@getReceiveHistory');
    Route::get('/cron/exchangeRate', 'CronController@exchangeRate');
    Route::get('/cron/oneMinute', 'CronController@oneMinute');
    Route::get('/cron/fiveMinute', 'CronController@fiveMinute');
    Route::get('/cron/fifteen', 'CronController@fifteen');
    Route::get('/cron/halfHour', 'CronController@halfHour');
    Route::get('/cron/oneHour', 'CronController@oneHour');
    Route::get('/cron/twoHour', 'CronController@twoHour');
    Route::get('/cron/sixHour', 'CronController@sixHour');
    Route::get('/cron/twelveHour', 'CronController@twelveHour');
    Route::get('/cron/oneDay', 'CronController@oneDay');
//    Route::get('/cron/twoDay', 'CronController@twoDay');
    Route::get('/cron/week', 'CronController@week');
//    Route::get('/cron/twoWeek', 'CronController@twoWeek');
//    Route::get('/cron/month', 'CronController@month');
//    Route::get('/cron/twoMonth', 'CronController@twoMonth');
//    Route::get('/cron/sixMonth', 'CronController@sixMonth');
    Route::get('/cron/bak_block_opt_data', 'ACXCronController@bak_block_opt_data');
    Route::get('/cron/update_withdraw_change_from_24hour', 'ACXCronController@update_withdraw_change_from_24hour');
    Route::get('/cron/testOne', 'CronController@ce');
    Route::get('/cron/deData', 'CronController@deData');
    Route::get('/cron/deDataMax', 'CronController@deDataMax');

    Route::get('/', 'IndexController@index');
    Route::get('/index/{type}', 'IndexController@index');
    Route::get('/index/getJson/data', 'IndexController@getJson');
    Route::get('/user/restEmail/{token}', 'UserController@restEmail');
    Route::post('/user/googleAuth', 'UserController@googleAuth');

    Route::get('/test/index', 'TestController@index');
    Route::get('/test/testStr', 'TestController@testStr');
    Route::get('/test/remove', 'TestController@remove');
    Route::get('/test/getBalance', 'TestController@getBalance');
    Route::get('/test/getTransactionList', 'TestController@getTransactionList');
    Route::get('/test/order', 'TestController@order');
    Route::get('/test/orderOne', 'TestController@orderOne');
    Route::get('/test/orderTest', 'TestController@orderTest');
    Route::get('/test/auatest', 'TestController@auatest');

    Route::get('/test/sendEmail', 'TestController@sendEmail');
    Route::get('/test/viewEmail', 'TestController@viewEmail');

    //--测试
    Route::get('/test/get_balance', 'TestController@get_balance');
    Route::get('/test/add_fee_user', 'TestController@add_fee_user');//增加汇率
    Route::get('/test/add_address_user', 'TestController@add_address_user');//增加地址
    Route::get('/test/make_salve_account', 'TestController@make_salve_account');//創建公司賬戶
    Route::get('/test/get_mima', 'TestController@get_mima');//得到密码

    Route::get('/test/check_order_list', 'CheckOrderController@check_order_list');//验证低价信息

    //电影网站发送短信
    Route::get('/test/ramaPhone', 'TestController@ramaPhone');


    Route::get('/test/get_user_info', 'TestController@get_user_info');//获取一个用户信息


    

    //redis local test
    //--跨域访问 
    Route::get('/test/kuayu_get_opt', 'TestController@kuayu_get_opt');
    Route::get('/test/copy_opt_ac_to_acx', 'TestController@copy_opt_ac_to_acx');


    Route::get('/redis/lLen','RedisController@lLen');
    Route::get('/redis/lPush','RedisController@lPush');
    Route::get('/redis/lPop','RedisController@lPop');
    Route::get('/redis/flushAll','RedisController@flushAll');
    Route::get('/redis/lTrim','RedisController@lTrim');


    Route::get('/withdrawEmailVerify/{token}','WalletController@withdrawEmailVerify');

    //--判断提现信息是不是相等
    Route::get('/test001/duibi_user_tixian', 'TestYaoController@duibi_user_tixian');
    //--获取个人交易信息
    Route::get('/test001/get_user_trans_info', 'TestYaoController@get_user_trans_info');

    //-测试地址是否相等
    //Route::get('/test001/test_duibi_a_user_address', 'TestYaoController@test_duibi_a_user_address');
//
    ////--对比所有人地址对比
    //Route::get('/test001/duibi_all_user_address', 'TestYaoController@duibi_all_user_address');



});

//new
Route::domain(env('APP_URL_NEW'))->middleware(['SetLocale'])->namespace('FrontEnd')->group(function () {

    Route::post('/index/wallet/market', 'WalletController@market');

    Route::post('/contact','ContactController@contactUs');
    //--不登录，登录都可以访问
    Route::post('/login/setLocale', 'LoginController@setLocale');
    //--开始工作
    Route::get('/task/work','TaskController@work');

    //--
    Route::get('/task/total','TaskController@get_count');


    Route::get('/list/get', 'ACXCronController@getReceiveHistory');
    Route::get('/cron/exchangeRate', 'CronController@exchangeRate');
    Route::get('/cron/oneMinute', 'CronController@oneMinute');
    Route::get('/cron/fiveMinute', 'CronController@fiveMinute');
    Route::get('/cron/fifteen', 'CronController@fifteen');
    Route::get('/cron/halfHour', 'CronController@halfHour');
    Route::get('/cron/oneHour', 'CronController@oneHour');
    Route::get('/cron/twoHour', 'CronController@twoHour');
    Route::get('/cron/sixHour', 'CronController@sixHour');
    Route::get('/cron/twelveHour', 'CronController@twelveHour');
    Route::get('/cron/oneDay', 'CronController@oneDay');
//    Route::get('/cron/twoDay', 'CronController@twoDay');
    Route::get('/cron/week', 'CronController@week');
//    Route::get('/cron/twoWeek', 'CronController@twoWeek');
//    Route::get('/cron/month', 'CronController@month');
//    Route::get('/cron/twoMonth', 'CronController@twoMonth');
//    Route::get('/cron/sixMonth', 'CronController@sixMonth');
    Route::get('/cron/bak_block_opt_data', 'ACXCronController@bak_block_opt_data');
    Route::get('/cron/update_withdraw_change_from_24hour', 'ACXCronController@update_withdraw_change_from_24hour');
    Route::get('/cron/testOne', 'CronController@ce');
    Route::get('/cron/deData', 'CronController@deData');
    Route::get('/cron/deDataMax', 'CronController@deDataMax');

    Route::get('/', 'IndexController@index');
    Route::get('/index/{type}', 'IndexController@index');
    Route::get('/index/getJson/data', 'IndexController@getJson');
    Route::get('/user/restEmail/{token}', 'UserController@restEmail');
    Route::post('/user/googleAuth', 'UserController@googleAuth');

    Route::get('/test/index', 'TestController@index');
    Route::get('/test/testStr', 'TestController@testStr');
    Route::get('/test/remove', 'TestController@remove');
    Route::get('/test/getBalance', 'TestController@getBalance');
    Route::get('/test/getTransactionList', 'TestController@getTransactionList');
    Route::get('/test/order', 'TestController@order');
    Route::get('/test/orderOne', 'TestController@orderOne');
    Route::get('/test/orderTest', 'TestController@orderTest');
    Route::get('/test/auatest', 'TestController@auatest');

    Route::get('/test/sendEmail', 'TestController@sendEmail');
    Route::get('/test/viewEmail', 'TestController@viewEmail');

    //--测试
    Route::get('/test/get_balance', 'TestController@get_balance');
    Route::get('/test/add_fee_user', 'TestController@add_fee_user');//增加汇率
    Route::get('/test/add_address_user', 'TestController@add_address_user');//增加地址
    Route::get('/test/make_salve_account', 'TestController@make_salve_account');//創建公司賬戶
    Route::get('/test/get_mima', 'TestController@get_mima');//得到密码

    Route::get('/test/check_order_list', 'CheckOrderController@check_order_list');//验证低价信息

    //电影网站发送短信
    Route::get('/test/ramaPhone', 'TestController@ramaPhone');


    Route::get('/test/get_user_info', 'TestController@get_user_info');//获取一个用户信息




    //redis local test
    //--跨域访问
    Route::get('/test/kuayu_get_opt', 'TestController@kuayu_get_opt');
    Route::get('/test/copy_opt_ac_to_acx', 'TestController@copy_opt_ac_to_acx');


    Route::get('/redis/lLen','RedisController@lLen');
    Route::get('/redis/lPush','RedisController@lPush');
    Route::get('/redis/lPop','RedisController@lPop');
    Route::get('/redis/flushAll','RedisController@flushAll');
    Route::get('/redis/lTrim','RedisController@lTrim');


    Route::get('/withdrawEmailVerify/{token}','WalletController@withdrawEmailVerify');
});

Route::get('/mailable', function () {
    return new App\Mail\PasswordReset(['email'=>strtolower('HSC14a5Ga'),'message'=>'3345']);
});
Route::get('/restEmail', function () {
    return new App\Mail\ChangeEmail(['email'=>'11111','newEmail'=>'2222','url'=>'/user/restEmail/222222']);
});



//add

Route::get('api/returnTicker','ApiController@returnTicker')->name('ticker')->middleware('throttle:60');
Route::get('api/returnCurrencyInfo','ApiController@getCurrencyInfo');
Route::get('api/getConvert','WalletController@getconvertCurrency');

Route::post('/api/v1/user/newapikeys','Api\ApiController@createUserApikeys')->name('createNewApikeys');  //生成用户Apikey
Route::get('/api/v1/test','Api\ApiController@test')->middleware('apiauth');


Route::group(['namespace'=>'Api'],function() {
    Route::get('/api/v1/gethighestbid/{market}','ApiController@getHighestBid')->where('market','[A-Z_]+')->middleware('throttle:60');
    Route::get('/api/v1/getlowestask/{market}','ApiController@getLowestAsk')->where('market','[A-Z_]+')->middleware('throttle:60');
    Route::get('/api/v1/getbuyorder/{market}','ApiController@getBuyOrder')->where('market','[A-Z_]+')->middleware('throttle:60');
    Route::get('/api/v1/getsellorder/{market}','ApiController@getSellOrder')->where('market','[A-Z_]+')->middleware('throttle:60');
    Route::post('/api/v1/trade/{market}','ApiController@trade')->where('market','[A-Z_]+');
    Route::post('/api/v1/cancel/{market}','ApiController@cancel')->where('market','[A-Z_]+')->middleware('apiauth');
    //Route::post('/api/v1/openorders','ApiController@openorders')->middleware('apiauth');
    //5-22新加 通过订单号取消订单
    Route::post('/api/v1/cancelbyorder/{market}','ApiController@cancelbyorder')->where('market','[A-Z_]+')->middleware('apiauth');
    Route::post('/api/v1/openorders', 'ApiController@openorders')->middleware('apiauth');
    Route::post('/api/v1/tradecount/{market}','ApiController@tradecount');
    Route::get('/api/v1/markets','ApiController@getMarkets')->middleware('throttle:60');
    Route::get('/api/v1/getprice/{market}','ApiController@getLastPrice');
});



//--------------------------------Dashboard---------------------------------------------
Route::group(['domain' => env('ADMIN_DOMAIN'),'namespace'=>'Admin'], function () {
    Route::get('/',function (){
        try{
            Auth::guard('back')->authenticate();
        }catch (Exception $e){
            return view('dashboard.login');
        }
        return redirect()->route('dashboard');
    });

//    Route::post('/withdraw_action','BackController@withdrawAction')->name('withdraw_action');
    Route::post('/withdraw_action','BackController@withdrawAction')->name('withdraw_action');

    Route::get('/loginAs','BackController@loginAs')->name('login_as');
    Route::post('/login','ManagerController@login')->name('manager_login');
    Route::get('/getManagerInfo','ManagerController@getManagerInfo')->name('manager_info');
    Route::get('/loginOut','ManagerController@logout')->name('manager_logout');
    Route::get('/ACdashboard','BackController@getCurrencyInfo')->name('dashboard');
    Route::get('/getUserList','BackController@getUserList')->name('client_list');
    Route::get('/userBalance','BackController@userBalance')->name('client_balance');
    Route::get('/editUser','BackController@editUser')->name('edit_user');
    Route::post('/updateUserLists','BackController@updateUserLists')->name('update_user');
    Route::post('/reset2FA','BackController@reset2FA')->name('reset_2FA');
    Route::get('/getUserDisabled','BackController@getUserDisabled')->name('disable_list');
    Route::post('/disableUser','BackController@disableUser')->name('disable_user');
    Route::get('/getUserCheck','BackController@getUserCheck')->name('check_list');
    Route::get('/editUserCheck','BackController@editUserCheck');
    Route::post('/updateUserCheck','BackController@updateUserCheck')->name('update_identity');
    Route::post('/PassUser', 'BackController@PassUser')->name('check_action');
    Route::get('/administrators','BackController@getManagerList')->name('manager_list');
    Route::post('/managerAction','BackController@managerAction')->name('manager_action');
    Route::get('/editManager','BackController@editManager')->name('edit_manager');
    Route::get('/WalletManagement/{type}','BackController@getGroups')->name('currency_list');
    Route::get('/getMove','BackController@getMove')->name('adjust_move');
    Route::get('/airdrop','BackController@airdrop')->name('adjust_airdrop');
    Route::get('/managerLog','BackController@managerLog')->name('manager_log');
    Route::get('/withdrawal/{status}','BackController@withdrawalCheck')->name('withdrawal_check');
//    Route::get('/withdrawal','BackController@withdrawalCheck')->name('withdrawal_check');

    Route::get('/getHistory','BackController@getHistory')->name('history_list');
    Route::get('/withdrawal_waitEmail', 'BackController@withdrawalWait')->name('withdrawal_wait');
    Route::post('/withdrawalEmailResend', 'BackController@withdrawalEmailResend')->name('withdrawal_resendEmail');
    Route::get('/userWithdrawalReport','BackController@userWithdrawalReport')->name('withdrawal_report');
    Route::get('/report','BackController@report');
    Route::get('/getChange','BackController@getChange')->name('change_list');
    Route::post('/ignoredUsers','BackController@IgnoredUsers')->name('ignored_users');
    Route::get('/check/{status}','BackController@showUnCheck')->name('check_status');
    Route::get('/check-detail','BackController@checkDetail')->name('check_detail');
    Route::get('/auatest','BackController@auatest');
    Route::post('/registerEmailResend', 'BackController@registerEmailResend')->name('email_resend');
    Route::post('/withdrawalEmailResend', 'BackController@withdrawalEmailResend')->name('withdrawal_resendEmail');
    Route::post('/refreshBalance', 'BackController@refreshBalance')->name('refresh_balance');
    Route::get('/userWithdrawTerm', 'BackController@userWithdrawTerm')->name('term_withdraw');
    Route::post('/updateWithdrawTerm', 'BackController@updateWithdrawTerm')->name('update_term_withdraw');
    Route::get('/KYCRejectReasons', 'BackController@KYCRejectReasons')->name('reject_reasons');
});
Route::domain(env('APP_URL'))->namespace('Admin')->group(function () {
    Route::get('/auatest', 'AuaTestController@script');
});


//--------------------------------Dashboard---------------------------------------------
Route::group(['domain' => env('ADMIN_DOMAIN_NEW'),'namespace'=>'Admin'], function () {
    Route::get('/',function (){
        try{
            Auth::guard('back')->authenticate();
        }catch (Exception $e){
            return view('dashboard.login');
        }
        return redirect()->route('dashboard');
    });

//    Route::post('/withdraw_action','BackController@withdrawAction')->name('withdraw_action');
    Route::post('/withdraw_action','BackController@withdrawAction')->name('withdraw_action');

    Route::get('/loginAs','BackController@loginAs')->name('login_as');
    Route::post('/login','ManagerController@login')->name('manager_login');
    Route::get('/getManagerInfo','ManagerController@getManagerInfo')->name('manager_info');
    Route::get('/loginOut','ManagerController@logout')->name('manager_logout');
    Route::get('/ACdashboard','BackController@getCurrencyInfo')->name('dashboard');
    Route::get('/getUserList','BackController@getUserList')->name('client_list');
    Route::get('/userBalance','BackController@userBalance')->name('client_balance');
    Route::get('/editUser','BackController@editUser')->name('edit_user');
    Route::post('/updateUserLists','BackController@updateUserLists')->name('update_user');
    Route::post('/reset2FA','BackController@reset2FA')->name('reset_2FA');
    Route::get('/getUserDisabled','BackController@getUserDisabled')->name('disable_list');
    Route::post('/disableUser','BackController@disableUser')->name('disable_user');
    Route::get('/getUserCheck','BackController@getUserCheck')->name('check_list');
    Route::get('/editUserCheck','BackController@editUserCheck');
    Route::post('/updateUserCheck','BackController@updateUserCheck')->name('update_identity');
    Route::post('/PassUser', 'BackController@PassUser')->name('check_action');
    Route::get('/administrators','BackController@getManagerList')->name('manager_list');
    Route::post('/managerAction','BackController@managerAction')->name('manager_action');
    Route::get('/editManager','BackController@editManager')->name('edit_manager');
    Route::get('/WalletManagement/{type}','BackController@getGroups')->name('currency_list');
    Route::get('/getMove','BackController@getMove')->name('adjust_move');
    Route::get('/airdrop','BackController@airdrop')->name('adjust_airdrop');
    Route::get('/managerLog','BackController@managerLog')->name('manager_log');
    Route::get('/withdrawal/{status}','BackController@withdrawalCheck')->name('withdrawal_check');
//    Route::get('/withdrawal','BackController@withdrawalCheck')->name('withdrawal_check');

    Route::get('/getHistory','BackController@getHistory')->name('history_list');
    Route::get('/withdrawal_waitEmail', 'BackController@withdrawalWait')->name('withdrawal_wait');
    Route::post('/withdrawalEmailResend', 'BackController@withdrawalEmailResend')->name('withdrawal_resendEmail');
    Route::get('/userWithdrawalReport','BackController@userWithdrawalReport')->name('withdrawal_report');
    Route::get('/report','BackController@report');
    Route::get('/getChange','BackController@getChange')->name('change_list');
    Route::post('/ignoredUsers','BackController@IgnoredUsers')->name('ignored_users');
    Route::get('/check/{status}','BackController@showUnCheck')->name('check_status');
    Route::get('/check-detail','BackController@checkDetail')->name('check_detail');
    Route::get('/auatest','BackController@auatest');
    Route::post('/registerEmailResend', 'BackController@registerEmailResend')->name('email_resend');
    Route::post('/withdrawalEmailResend', 'BackController@withdrawalEmailResend')->name('withdrawal_resendEmail');
    Route::post('/refreshBalance', 'BackController@refreshBalance')->name('refresh_balance');
    Route::get('/userWithdrawTerm', 'BackController@userWithdrawTerm')->name('term_withdraw');
    Route::post('/updateWithdrawTerm', 'BackController@updateWithdrawTerm')->name('update_term_withdraw');
});
Route::domain(env('APP_URL_NEW'))->namespace('Admin')->group(function () {
    Route::get('/auatest', 'AuaTestController@script');
});