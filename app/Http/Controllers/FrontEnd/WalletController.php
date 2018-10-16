<?php
/**
 * Created by PhpStorm.
 * User: ZRothschild
 * Date: 2018/1/12
 * Time: 17:47
 */

namespace App\Http\Controllers\FrontEnd;

use App\Http\Controllers\Controller;
use App\Library\Trade\Account;
use App\Models\Currency;
use App\Models\DepositHistory;
use App\Models\Market;
use App\Tool\WithdrawalControl;
use App\UserCurr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\User;
use App\WithdrawHistory;
use App\WithdrawHistoryNode;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use App\WithdrawLimit;
use Illuminate\Support\Facades\Hash;

class WalletController extends Controller
{
    /**
     *用户钱包
     */
    public function index()
    {
        $account = new Account();
        $userId = Auth::id();
        $accountWall = $account->statusAccount($userId);
        $data = [
            'currency' => 'BTC',
            'address' =>UserCurr::where([
                ['curr_abb','=','btc'],
                ['user_id','=',$userId]
            ])->value('address'),
            'qrcode' => asset('qrcodes/'.$userId.'btc.png'),
            'statusAccount' => $accountWall,
        ];
        $data['withDraws'] = WithdrawHistory::where([
            ['user_id','=',Auth::id()],
        ])->get();

        $data['deposits'] = DepositHistory::where('user_id',Auth::id())->get();
        $data['currencies'] = Currency::pluck('currency','id');

        //可以提现金额
        $WithdrawLimit = WithdrawLimit::where('user_id',$userId)->value('btc_balance');
        if (empty($WithdrawLimit)) {
            $data['withDrawOne'] = '0.00000000';
        }else{
            $con=new WithdrawalControl();
            $reminder = $con->todayReminder($userId);
            $shenyu_btc_can_withdraw=bcsub($WithdrawLimit,$reminder,8);
            $data['withDrawOne'] = $shenyu_btc_can_withdraw;
        }

        //todo foreach 不能为空
        return view('front.wallet',$data);
    }

    /**
     * 保证金 Deposit wallet
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function depositCurrency(Request $request)
    {
        //todo 记得修改js 替换 和 判断是否又图片 地址问题
        $userId = Auth::id();
        $whereData = [
            ['curr_abb','=',strtolower($request->type)],
            ['user_id','=',$userId]
        ];
        $address = UserCurr::where($whereData)->value('address');
        $qrcode = empty($address)?'': asset('qrcodes/'.$userId.strtolower($request->type).'.png');
        $data = ['currency'=>$request->type,'address'=>$address,'qrcode' =>$qrcode];
        return response()->json(['status'=>1,'data'=>$data,'messages'=>'successful']);
    }

    /**
     * order/index
     * @return \Illuminate\Http\JsonResponse
     */
    public function orderMarket()
    {
        $marketNames = Market::where([
            ['is_show','=',1],
            ['market_name','like',"%_btc"],
        ])->get(['market_name']);
        $data[] = 'All';
        foreach ($marketNames as $key => $marketName){
            $tmp = explode('_',$marketName->market_name);
            $data[] = strtoupper($tmp[0]);
        }
        return response()->json(['status'=>1,'data'=>$data,'messages'=>'successful']);
    }


    /**Market 市场汇率
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function market(Request $request)
    {
        Log::info('test',$request->all());
        try{
            $marketName = Market::where([
                ['is_show','=',1],
                ['market_name','like',"%_".$request->currency],
            ])->get(['market_name','arrow','last_price'])->toArray();

            $day = 60*60*24;
            $endTime = time()-$day;

            $data = [];
            $num = 0;
            foreach ($marketName as $key => $value){
                $mark_name_temp=explode("_",$value['market_name']);

                if($mark_name_temp[1]!=$request->currency){
                    continue;
                }

                $open= DB::table("xchange_info")->where([
                    ['market_name','=',$value['market_name']],
                    ['created_at','>=',$endTime],
                ])->first(['last_price']);
                if (empty($open)){
                    $comp = 1;
                    $change = '0%';
                } else{
                    $open=$open->last_price;

                    $diff = bcsub($value['last_price'],$open,8);

                    $comp = bccomp($value['last_price'],$open,8);

                    if ($comp >= 0){
                        $comp = 1;
                    }else{
                        $comp = 0;
                    }
                    if (empty($diff)){
                        $change = '0%';
                    }else{
                        $change = '0%';
                        if($open>0){
                            $change = abs(bcdiv($diff,$open,4)*100);
                            $change = $change.'%';
                        }
                    }
                }

                $mark_name_temp=explode("_",$value['market_name']);
                $data[$num]['curr_abb'] = $mark_name_temp[0];
                $data[$num]['currency'] = $mark_name_temp[1];
                $data[$num]['flag'] = $comp;
                $data[$num]['percent_change_24h'] = $change;
                //交易总金额

                $volume = DB::table('xchange_info')->where([
                    ['market_name','=',$value['market_name']],
                    ['created_at','>=',$endTime],
                ])->sum('volume');
                if (empty($volume)) $volume = "0.00000000";

                $priceField = 'price_'.$request->currency;
                $data[$num]['volume_btc_24h'] = substr($volume,0,11);;
                $data[$num][$priceField] = substr($value['last_price'],0,11);
                $num++;
            }
        }catch (\Exception $exception){
            $data = $exception->getMessage();
        }
        return response()->json(['status'=>1,'data'=>$data,'message' => 'successful']);
    }

    // /**
    //  * 买卖交易
    //  * @param Request $request
    //  * @return bool|\Illuminate\Http\JsonResponse
    //  */
    // public function currencyTrade(Request $request)
    // {
    //     $this->validate($request,[
    //         'amount' => 'bail|required', // 数字货币总量
    //         'price' => 'bail|required',  // 价格
    //         'total' => 'bail|required',   // 数量
    //         'fee' => 'bail|required', //手续费
    //         'netTotal' => 'bail|required', //发起交易的总值
    //     ]);
    //     Log::info('2222222222244444444466666666666666666');
    //     Log::info($request);

    //     Log::info('22222224444444466666666666666666');
    //     $feeRate = substr($request->feeRate,0,-1);
    //     $feeRate = bcdiv($feeRate,100,3);
    //     Log::info($feeRate);
    //     //进来立即获取汇率转换
    //     $currencySet = CurrencySet::where('curr_abb',$request->tradeCurr)->first(['price_cny','price_btc','price_usd','tmp_price','curr_id']);

    //     $traAccount= new Account();
    //     //判断参考币种是否正确 btc usd cny
    //     Log::info('$request->currency=>'.$request->currency);
    //     $referBool = $traAccount->judgeContain($request->currency,['btc','usd','cny']);
    //     if (empty($referBool)) return response()->json(['status'=>0,'message' => '参考汇率币种出错']);
    //     //交易类型是否正确
    //     $tranBool = $traAccount->judgeContain($request->type,[10,20]);
    //     if (empty($tranBool)) return response()->json(['status'=>0,'message' => '交易类型错误']);
    //     $account = new AccountAmount();
    //     Log::info(1111111111);
    //     //传入数据本身是否有误
    //     $numValBool = $account->numValidate($request->total, $request->price, $request->amount, $feeRate, $request->fee, $request->netTotal, $request->type,$request->tradeCurr);
    //     if (empty($numValBool)) return response()->json(['status'=>0,'message' => '传入数据有误']);
    //     //判断交易币种是否过大
    //     if ($request->type == 10){
    //         Log::info(222222);
    //         $accountBool = $account->judgeBalance($request->netTotal,$request->currency,Auth::id(),$request->tradeCurr,$request->type);
    //     }else{
    //         $accountBool = $account->judgeBalance($request->amount,$request->currency,Auth::id(),$request->tradeCurr,$request->type);
    //     }
    //     if (empty($accountBool)) return response()->json(['status'=>0,'message' => '货币不足1']);

    //     //判断是否 生成过订单
    //     $existResult = $traAccount->judgeExistOrder($currencySet->curr_id,$request->type,$request->price);
    //     //开启事务
    //     DB::beginTransaction();
    //     // 先撮合订单 后面撮合订单
    //     $ordBool = $traAccount->createOrder($existResult,$request->total,$request->price,$request->amount,$request->fee,$request->type,$request->tradeCurr,$currencySet->curr_id);
    //     Log::info('ordBoolordBoolordBoolordBool');
    //     if ($ordBool < 1){
    //         DB::rollBack();
    //         return response()->json(['status'=>0,'message' => '货币不足2']);
    //     }
    //     //订单细节 开始
    //     $user = Auth::user();
    //     //上面已经生成必定会查找到
    //     $orderMessage =  $traAccount->judgeExistOrder($currencySet->curr_id,$request->type,$request->price);
    //     //生成订单详情
    //     $detBool = $traAccount->createDetail($request->total,$request->price,$request->amount,$feeRate,$request->fee,$request->netTotal,$request->type,$request->tradeCurr,$currencySet->curr_id,$orderMessage->id,$user->id,$user->name,$currencySet->price_usd,$currencySet->price_cny);
    //     Log::info("***^%%%%%%%%%%%%%%%%%%%%EEEEEEEEEEEGCHbxhc");
    //     Log::info($detBool);
    //     if ($detBool['id'] < 1){
    //         DB::rollBack();
    //         return response()->json(['status'=>0,'message' => '货币不足3']);
    //     }
    //     //当个人订单 有成交需要生成 Klines
    //     if ($detBool['data']['status'] > 10){
    //         $kLine = new KLine();
    //         $bench = $detBool['data']['bench'];
    //         $benchCount = count($bench);
    //         $counter = 0;
    //         Log::info("benchbenchbenchbenchbenchbenchbenchbenchbenchbenchbench");
    //         Log::info($bench);
    //         $timeCalc = new TimeCalc();
    //         while ($benchCount > $counter){
    //             $date = $timeCalc->modularCopy(14,time());
    //             $resKLines = $kLine->createKLine(time(),$bench[$counter]['price'],$currencySet->curr_id,$request->tradeCurr,$bench[$counter]['initialMun'],14,$date);
    //             if ($benchCount-1 == $counter){

    //                 $priceBtcTmp = CurrencySet::where([
    //                     ['curr_abb','=',$request->tradeCurr],
    //                 ])->value('price_btc');

    //                 $timeCalc = new TimeCalc();
    //                 $day = 60*60*24;
    //                 $endTime =$timeCalc->modularCopy(14,time()-$day);

    //                 $openTmp = KLine::where([
    //                     ['datum_type','=',14],
    //                     ['datum_time','<',strtotime($endTime)],
    //                     ['curr_abb','=',$request->currAbb],
    //                 ])->orderByDesc('datum_time')->first(['close']);

    //                 if (empty($openTmp)){
    //                     $open = $currencySet->tmp_price;
    //                     Log::info("openopenopetmp_priceenopen=>".$open);

    //                 }else{
    //                     $open = $openTmp['close'];
    //                     Log::info("openopenopeclosepenopen=>".$open);
    //                 }
    //                 $diff = bcsub($bench[$counter]['price'],$open,8);

    //                 $comp = bccomp($bench[$counter]['price'],$open,8);
    //                 if ($comp > 0){
    //                     $comp = 1;
    //                 }else{
    //                     $comp = 0;
    //                 }
    //                 if (empty($diff)){
    //                     $change = '0.00';
    //                 }else{
    //                     $change = bcdiv(abs($diff),$open,4)*100;
    //                 }
    //                 $updateMonde =  CurrencySet::where([
    //                     ['curr_abb','=',$request->tradeCurr],
    //                 ])->update([
    //                     'price_btc'=>$bench[$counter]['price'],
    //                     'tmp_price'=>$priceBtcTmp,
    //                     'flag'=>$comp,
    //                     'percent_change_24h'=>$change,
    //                 ]);
    //             }
    //             ++ $counter;
    //         }
    //     }

    //     try{
    //         $traAccount->moveMoney($request->type,$user->id,$request->netTotal,$detBool,$request->price,$request->tradeCurr,$request->amount,$feeRate);
    //     }catch (\Exception $exception){
    //         DB::rollBack();
    //         $msg = $exception->getCode();
    //         Log::info($exception->getMessage());
    //         return response()->json(['status'=>0,'message' =>$msg]);
    //     }
    //     DB::commit();
    //     if ($detBool['data']['data'] != false) $traAccount->firstMoney($detBool['data']['data']);
    //     return response()->json(['status'=>1,'message' =>'successful']);
    // }

    /**
     * 返回货币全称
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getFullName(Request $request)
    {
        $data['currName'] = Currency::where('currency',strtoupper($request->currAbb))->value('full_currency');
        $data['currencyName'] = Currency::where('currency',strtoupper($request->currency))->value('full_currency');
        return response()->json(['status'=>1,'message' => 'successful','data'=>$data]);
    }


    /**
     * 默认请求
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    // public function tradeTemp(Request $request)
    // {
    //     Log::info('22222222222222222224444444444444466666666666666666');
    //     Log::info($request);
    //     Log::info('22222222222222222224444444444444466666666666666666');
    //     $this->validate($request,[
    //         'currency' => 'bail|required',
    //         'type' => 'bail|required',
    //     ]);

    //     $tradeCurrBool = $request->has('tradeCurr');
    //     $tradeCurr = empty($tradeCurrBool)?'btc':$request->tradeCurr;
    //     //进来立即获取汇率转换
    //     $currencySet = CurrencySet::where('curr_abb',$tradeCurr)->first(['price_btc','price_cny','price_usd','curr_id']);

    //     $traAccount= new Account();
    //     $aAmount = new AccountAmount();

    //     Log::info($request->currency);
    //     $referBool = $traAccount->judgeContain($request->currency,['btc','usd','cny']);
    //     if (empty($referBool)) return response()->json(['status'=>0,'message' => '参考汇率币种出错']);

    //     //交易类型是否正确
    //     $tranBool = $traAccount->judgeContain($request->type,[10,20]);
    //     if (empty($tranBool)) return response()->json(['status'=>0,'message' => '交易类型错误']);

    //     $curr = 'price_'.$request->currency;

    //     $userCurr = new UserCurr();
    //     $feeRate = $userCurr->getFeeRate(Auth::id(),$tradeCurr);

    //     $rateShow = $feeRate*100;

    //     $price = "price_btc";

    //     $data = ['price'=>$currencySet->$curr,'feeRate'=>$rateShow.'%'];

    //     if ($request->type == 20){
    //         Log::info('20 1111111111111111');
    //         $aAmount->getBalance('btc',Auth::id());
    //         $tradeCurrBalance =  $aAmount->getBalance($request->tradeCurr,Auth::id());

    //         $currBal = $aAmount->one($tradeCurrBalance);

    //         $total = bcmul($tradeCurrBalance,$currencySet->$curr,8);
    //         Log::info($total);
    //         Log::info('20 222222222222222222222222222222');
    //         $totalBal = $aAmount->one($total);

    //         $fee = bcmul($total,$feeRate,8);
    //         $feeBal = $aAmount->one($fee);

    //         if ($currBal != -1 ) {
    //             $data['amount'] = $tradeCurrBalance;
    //         }else{
    //             $data['amount'] = "0.00000000";
    //             $data['total'] = "0.00000000";
    //             $data['fee'] = "0.00000000";
    //             $data['netTotal'] = "0.00000000";
    //             return response()->json(['status'=>1,'message' =>'successful','data'=>$data]);
    //         }
    //         if ($totalBal != -1){
    //             $data['total'] = $total;
    //         }else{
    //             $data['total'] = "0.00000000";
    //             $data['fee'] = "0.00000000";
    //             $data['netTotal'] = "0.00000000";
    //             return response()->json(['status'=>1,'message' =>'successful','data'=>$data]);
    //         }

    //         if ($feeBal != -1){
    //             $data['fee'] = $fee;
    //         }else{
    //             $data['fee'] = "0.00000001";
    //         }
    //         $data['netTotal'] = bcsub($total,$data['fee'],8);
    //         return response()->json(['status'=>1,'message' =>'successful','data'=>$data]);
    //     }else{

    //         Log::info('10 1111111111111111');
    //         $tradeCurrBalance =  $aAmount->getBalance('btc',Auth::id());
    //         Log::info('10 22222222222222');

    //         $btcBal = bccomp($tradeCurrBalance,'0.00000002',8);

    //         if ($btcBal != -1){
    //             $data['netTotal'] = $tradeCurrBalance;

    //             if ($curr != $price) $data['netTotal'] = bcmul($tradeCurrBalance,$currencySet->$curr,8);

    //             $tmpRate = bcadd(1,$feeRate,8);
    //             $tmpA = bcmul($currencySet->$curr,$tmpRate,8);
    //             $data['amount'] = bcdiv($data['netTotal'],$tmpA,8);

    //             $data['total'] = bcmul($data['amount'],$currencySet->$curr,8);
    //             $fee = bcmul($feeRate,$data['total'],8);

    //             $feeRe = $aAmount->one($fee);
    //             if ($feeRe != -1){
    //                 $data['fee'] = $fee;
    //             }else{
    //                 $data['fee'] = "0.00000001";
    //             }

    //             $data['netTotal'] = bcadd($data['total'], $data['fee'],8);
    //         }else{
    //             $data['amount'] = "0.00000000";
    //             $data['total'] = "0.00000000";
    //             $data['fee'] = "0.00000000";
    //             $data['netTotal'] = $tradeCurrBalance;
    //             if ($curr != $price) $data['netTotal'] = bcmul($data['netTotal'],$currencySet->$curr,8);
    //         }
    //         $data['currName'] = UserCurr::where([
    //             ['curr_abb','=',$request->tradeCurr],
    //             ['user_id','=',Auth::id()],
    //         ])->value('curr_name');
    //         Log::info('@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@');
    //         Log::info($data);
    //         Log::info('#########################################################');
    //         return response()->json(['status'=>1,'message' =>'successful','data'=>$data]);
    //     }
    // }

    /**
     * 用户各种币 交易中金额 账户金额   smallBalance balance
     * @return \Illuminate\Http\JsonResponse
     */
    public function assets()
    {
        $account = new Account();
        $data = $account->statusAccount(Auth::id());
        $total = 0;
        foreach ($data as $key =>$value){
            $total = bcadd($value['btc_rate'],$total,8);
            $data[$key]['in_trade'] = substr($value['in_trade'],0,11);
            $data[$key]['balance'] = substr($value['balance'],0,11);
        }
        return response()->json(['status'=>1,'message' =>'successful','data'=>$data,'total'=>$total]);
    }

    /**
     * 最多可提货币
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function maxWithdraw(Request $request)
    {
        try{
            //   $client = $this->getClient($request->curr);
            // $user_balance = $client->getBalance(Auth::id());
            $user_balance=$this->show_withdraw_amount(Auth::id(),$request->curr);
        }catch (\Exception $e){
            $user_balance=$e->getMessage();
        }
        $currName = Currency::where('currency',strtoupper($request->curr))->first(['full_currency','withdraw_fee']);
        if ($request->curr == 'rpz'){
            $user_balance = bcadd($user_balance,'0',6);
        }
        return response()->json(['status'=>1,'message' =>'successful','data'=>['getBalance'=>$user_balance,'currName'=>$currName['full_currency'],'currAbb'=>$request->curr,'withdraw_fee'=>$currName['withdraw_fee']]]);
    }

    //-----------------------------提现-------------------------------------------
    /** 转账操作
     * @param Request $request
     * @return array
     */
    public function withdraw(Request $request)
    {
        $user_id = Auth::id();
        $user_info = User::where('id',$user_id)->select('auth_type','pin','name','email')->first();
        // TODO Pin码错误次数记录，超过次数记录黑名单
        if (!Hash::check($request->pin, $user_info->pin)) {
            return $this->ajax_jason(['status'=>0, 'message'=>__('ac.pinNoExists')],$request);
        }
        //判断提现最少与最大
        $minLimit = Currency::where([
            ['currency','=',strtoupper($request->curr)],
        ])->value('min_limit');
        if (bccomp($request->amount,$minLimit,8) == -1){
            return $this->ajax_jason(['status'=>0,'message' =>__('ac.minimumAmount').$minLimit],$request);
        }
        $validator = Validator::make($request->all(), [
            'address'   => 'required|string|min:30',
            'amount'    => 'required|numeric|min:0.00000001',
            'pin'       => 'required',
            'currency'  => 'required|exists:currency,currency',
            'code'      => 'required|numeric',
        ]);
        if (in_array($request->currency,['RPZ'])){
            $numTmp = explode('.',$request->amount);
            if (isset($numTmp[1])){
                $amount = $numTmp[0].".".substr($numTmp[1],0,6);
                $comp = bccomp($request->amount,$amount,8);
                if ($comp != 0) return $this->ajax_jason(['status'=>0, 'message'=>__('ac.withdrawalDecimal')],$request);
            }
        }
        if ($validator->fails()) {
            return $this->ajax_jason(['status'=>0, 'message'=>$validator->errors()->first()],$request);
        }
        $data = new Request();
        $data->type = $request->type;
        $data->code = $request->code;
        $data->hook = true;
        $authCheck = Auth2FAController::auth($data);
        $content=$authCheck->content();
        $content_array=json_decode($content,true);
        $status_a2=$content_array['status'];
        if($status_a2!='1'){
            return $this->ajax_jason(['status'=>0, 'message'=>__('ac.fa2IsError')],$request);
            # return $content_array;
        }
        $btc_value = $this->getBTCPrice($request->currency,$request->amount);
        if($btc_value < 0.00000001) $btc_value = 0.00000001;

        $control = new WithdrawalControl();
        $controlRes = $control->withdrawal($user_id,$btc_value);
        if (!$controlRes[0]){
            return $this->ajax_jason(['status'=>0, 'message'=>$controlRes[1]],$request);
        }
        // 获取余额验证余额---------------------
        $fee = Currency::where('currency',$request->currency)->value('withdraw_fee');
        if (bccomp((string)$fee,(string)$request->input('fee')) != 0 ) return response()->json(['status'=>0, 'message'=>__('ac.cashWithdrawalFeeError')]);
        $client = $this->getClient($request->currency);
        $user_balance = $client->_get_balance($user_id);
        $user_get_amount = bcsub($request->input('amount'), $fee ,8);

        if (bccomp($user_get_amount,'0.00000001',8) == -1) return response()->json(['status'=>0, 'message'=>__('ac.cashWithdrawalFeeError')]);

        if(bccomp($request->input('amount'),$user_balance,8) == 1 ) return response()->json(['status'=>0, 'message'=>__('ac.InsufficientAmountAccount')]);
        $token = str_random(150);
        try {
            //TODO 修改验证方式 加入hash_id 和 时间验证 ？
            WithdrawHistory::create([
                'currency'      => $request->currency,
                'address'       => $request->address,
                'user_id'       => $user_id,
                'amount'        => $user_get_amount,
                'max_fee'       => $fee,
                'btc_amount'    => $btc_value,
                'status'        => 0,
                'token'         => $token,
                'remarks'         => $request->remarks,
//                'sign'          => null
            ]);
            Mail::send('email.withdraw',['name'=>$user_info->name,'token'=>$token],function($message) use ($user_info){
                $message->to($user_info->email)->subject('Confirmed Withdraw Email');
            });
            return $this->ajax_jason(['status'=>1,'message' =>__('ac.pleaseConfirmYourEmail')],$request);
        } catch (\Exception $e) {
            Mail::send('email.withdraw',['name'=>$user_info->name,'token'=>$token],function($message) use ($user_info){
                $message->to($user_info->email)->subject('Confirmed Withdraw Email');
            });
            return $this->ajax_jason(['status'=>0,'message' =>$e->getMessage()],$request);
        }
    }

    /** 用户自己重新发送邮件
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function restWithdrawEmail(Request $request)
    {
        try{
            $token = str_random(150);
            $withdraw = WithdrawHistory::find($request->id);
            $withdraw->token = $token;
            $withdraw->save();
            $email = Auth::user()->email;
            Mail::send('email.withdraw',['name'=>Auth::user()->name,'token'=>$token],function($message) use ($email){
                $message->to($email)->subject('Confirmed Withdraw Email');
            });
            return $this->ajax_jason(['status'=>1,'message' =>__('ac.pleaseConfirmYourEmail')],$request);
        }catch (\Exception $exception){
            return $this->ajax_jason(['status'=>0,'message' =>$exception->getMessage()],$request);
        }
    }


    /** 点击邮箱链接确认转账
     * @param $token
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function withdrawEmailVerify($token)
    {
        //TODO 链接有效时间,修改成Cache 加入有效期限制
        $withdraw_info = WithdrawHistory::where('token',$token)->where('status',0)->first();
        if(!$withdraw_info) {
            return view('errors.404');
        }
        $client = $this->getAllClicent($withdraw_info->currency);
        if(!$client->validateaddress($withdraw_info->address)['isvalid']){
            $error['zh'] = '地址不正确';
            $error['en'] = 'address is not right';
            return view('errors.error',compact('error'));
        };
        $current_balance = $client->getBalance($withdraw_info->user_id);
        if($withdraw_info->total_amount > $current_balance) {
            $error['zh'] = '余额不足';
            $error['en'] = 'Balance Not enough';
            return view('errors.error',compact('error'));
        }
//        if($withdraw_info->currency == 'RPZ') {
//            $amount = floatval($withdraw_info->amount);
//        }else{
//            $amount = $withdraw_info->amount;
//        }
//        $do_limit = $this->doLimit($withdraw_info->user_id,$withdraw_info->currency,$withdraw_info->amount);
//        if(!$do_limit) {
//            $error['zh'] = '剩余的每日提现余额不足';
//            $error['en'] = 'Remaining amount for daily withdrawal is not enough';
//            return view('errors.error',compact('error'));
//        }
        $withdraw_info->status = 2;
        $withdraw_info->save();
        $client->_get_balance($withdraw_info->user_id);
        return view('errors.error');
        // 对比节点余额
//        $node_balance = $withdraw_info->currency=='BTC'? $client->getWalletInfo():$client->getInfo();
//        if($withdraw_info->total_amount > $node_balance) {
//            return response()->json(['status'=>0,'message' =>'Withdraw fail(node balance not enough)']);
//        }
//        $client->withdraw($withdraw_info->user_id,$withdraw_info->address,$amount);
//        $withdraw_info->status = 1;
//        $withdraw_info->save();
//        return view('errors.error');
    }
    //--------------------------------------------------------------------

    /** 返回转账货币的BTC价格
     * @param $currency
     * @param $amount
     * @return mixed
     */
    private function getBTCPrice($currency,$amount)
    {
        //TODO 后续修改last_price表结构,
        if($currency == 'BTC')
            return $amount;
        $last_price = Market::select('market_name','last_price')->where("market_name",'like','%'.$currency.'%')->value('last_price');
        return bcmul($last_price,$amount,8);
    }

    /** 获取用户转账限制初始值
     * @param $user_id
     * @param int $is_certification
     * @return int
     */
    private function getUserLimit($user_id)
    {
        $user = User::find($user_id);
        if($user->is_certification == 4)
            return 2;
        return 0;
    }

    /** 记录单日转账余额
     * @param $user_id
     * @param $currency
     * @param $amount
     * @return bool
     */
    public function doLimit($user_id,$currency,$amount)
    {
        $user_limit_info = WithdrawLimit::where('user_id',$user_id)->first();
        $btc_limit = $this->getUserLimit($user_id);
        $btc_price = $this->getBTCPrice($currency,$amount);
        if($btc_price < 0.00000001) {$btc_price = 0.00000001;}
        $day = date("Ymd");//获取当前的日期
        if($btc_price > $btc_limit) {
            return false;
        }
        if(!$user_limit_info) {
            WithdrawLimit::create(['user_id'=>$user_id,'btc_balance'=>$btc_price,'withdraw_time'=>time()]);
            return true;
        }else{
            if(date("Ymd",$user_limit_info->withdraw_time) == $day){ //今天兑换过了
                $current_btc_balance = round($btc_price + $user_limit_info->btc_balance,8);
                if($current_btc_balance > $btc_limit) {
                    return false;
                }else{
                    WithdrawLimit::where('user_id',$user_id)
                        ->update(['btc_balance'=>$current_btc_balance,'withdraw_time'=>time()]);
                }
                return true;
            }
            WithdrawLimit::where('user_id',$user_id)->update(['btc_balance'=>$btc_price,'withdraw_time'=>time()]);
            return true;
        }
    }

    /**
     * @
     * @param
     * @return   $api=true 直接返回用户今天已转账的总额度，为false json返回
     */
    public function getTodayRemainingAmount($user_id, $api = false)
    {
        $user_withdraw_info = WithdrawLimit::where('user_id',$user_id)->select('btc_balance','withdraw_time')->first(); //TODO 修改btc_balance=>btc_value
        if ($user_withdraw_info) {
            if (date('Y-m-d') == date('Y-m-d',$user_withdraw_info->withdraw_time)) {
                $today_withdraw_amount = $user_withdraw_info->btc_balance;
            }
        }
        $today_withdraw_amount = isset($today_withdraw_amount) ? $today_withdraw_amount : 0;
        return $api ? $today_withdraw_amount : response()->json(['status'=>1,'todayAmount'=>$today_withdraw_amount]);
    }

    /**
     * 判断提现最大最小
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function withdrawalInterval(Request $request)
    {
        try{
            $minLimit = Currency::where([
                ['currency','=',strtoupper($request->curr)],
            ])->value('min_limit');
            if (bccomp($request->amount,$minLimit,8) == -1){
                return $this->ajax_jason(['status'=>0,'message' =>__('ac.minimumAmount').$minLimit],$request);
            }
            $maxLimit = $this->show_withdraw_amount(Auth::id(),$request->curr);
            if (bccomp($maxLimit,$request->amount,8) == -1){
                return $this->ajax_jason(['status'=>0,'message' =>__('ac.maximumAmount').$maxLimit],$request);
            }
            return $this->ajax_jason(['status'=>1,'message' =>__('ac.successfully')],$request);
        }catch (\Exception $exception){
            return $this->ajax_jason(['status'=>0,'message' =>$exception->getMessage()],$request);
        }
    }

    /**
     * 最小成交量
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function minLimit(Request $request)
    {
        $minLimit = Currency::where([
            ['currency','=',strtoupper($request->curr)],
        ])->value('min_limit');
        return $this->ajax_jason(['status'=>1,'message' =>__('ac.successfully'),'data'=>['minLimit'=>__('ac.minimum').$minLimit]],$request);
    }

    /** 查询用户转账限制
     * @param $user_limit
     * @param $today_remaining_amount
     * @param $btc_value
     * @return bool
     */
    private function checkLimit($user_limit,$today_remaining_amount,$btc_value)
    {
        $current_btc_value = round($user_limit-$today_remaining_amount-$btc_value,8);
        return $current_btc_value>0 ? true : false;
    }

    /**
     * 当天剩余可提金额
     * @param $userId
     * @return string
     */
    public function todayRemain($userId,$curr)
    {
        $WithdrawLimit = WithdrawLimit::where('user_id',$userId)->value('btc_balance');
        //dump($WithdrawLimit);
        if (empty($WithdrawLimit)){
            $this->init_whdraw_info($userId);
            $WithdrawLimit = WithdrawLimit::where('user_id',$userId)->value('btc_balance');
            if (empty($WithdrawLimit)){
                return 0;
            }
        }
        $con=new WithdrawalControl();
        $reminder = $con->todayReminder($userId);
        $shenyu_btc_can_withdraw=bcsub($WithdrawLimit,$reminder,8);
        $withdraw_fee = Currency::select('currency','withdraw_fee')->where("currency",'like','%'.$curr.'%')->value('withdraw_fee');
        // dump($reminder);
        //  dump($shenyu_btc_can_withdraw);
        if($curr ==='btc'){
            $last_price=1;
        }else{
            $last_price = Market::select('market_name','last_price')->where("market_name",'like','%'.$curr.'%')->value('last_price');
        }
        //  dump($last_price);
        //  dump($withdraw_fee);
        $balance_max = bcdiv($shenyu_btc_can_withdraw,$last_price,8);
        //  dump($balance_max);
        if($balance_max<0)$balance_max='0.00000000';
        return $balance_max;
        // return [true,bcsub($WithdrawLimit,$reminder,8)];
    }

    /*
     * 当前货币可提现金额
     * */
    public function show_withdraw_amount($userId,$curr){
        $balance_max=$this->todayRemain($userId,$curr);
        $client = $this->getClient($curr);
        $balance=$client->getBalance(Auth::id());
        $show_balace_withdraw=$balance;
        if(bccomp($balance_max,$balance,8)===-1)$show_balace_withdraw=$balance_max;
        return $show_balace_withdraw;
    }


    /*如果没存在才添加*/
    public function init_whdraw_info($userId){
        $con=new WithdrawalControl();
        $fa = $con->access2FA($userId);
        $ak = $con->accessKyc($userId);
        if($fa && $ak){
            $level=1;
            $all=2;
        }else{
            $level=0;
            $all=0;
        }
        $data = [
            'user_id' => $userId,
            'level' => $level,
            'btc_balance' => $all,
            'withdraw_time' => time(),
        ];
        WithdrawLimit::create($data);
    }
}