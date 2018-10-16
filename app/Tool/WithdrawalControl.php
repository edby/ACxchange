<?php
/**
 * Created by PhpStorm.
 * User: ZRothschild
 * Date: 2018/8/6
 * Time: 14:30
 */

namespace App\Tool;

use App\Customize;
use App\Http\Controllers\Controller;
use App\Http\Controllers\FrontEnd\WalletController;
use App\Models\Market;
use App\Models\Xchange;
use App\Models\XchangeDetail;
use App\User;
use App\WithdrawHistory;
use App\WithdrawLimit;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WithdrawalControl
{
    /**
     * 提现金额判断
     * @param $userId
     * @param $amount
     * @param int $all
     * @return array
     */
    public function withdrawal($userId,$amount,$all = 2)
    {
        try{
            DB::beginTransaction();
            $WithdrawLimit = WithdrawLimit::where('user_id',$userId)->lockForUpdate()->first(['btc_balance']);

            $fa = $this->access2FA($userId);
            if (!$fa) {
                DB::rollback();
                return [false,__('ac.2FANotVerified')];
            }
            $ak = $this->accessKyc($userId);
            if (!$ak){
                DB::rollback();
                return [false,__('ac.kycHasVerified')];
            }

            if (empty($WithdrawLimit)){
                $level = 1;
                $reminder = $this->todayReminder($userId);
                $allAmount = bcadd((string)$amount,(string)$reminder,8);
                $data = [
                    'user_id' => $userId,
                    'level' => $level,
                    'btc_balance' => $all,
                    'withdraw_time' => time(),
                    'usage_amount' => $reminder,
                ];
                if (bccomp((string)$all,(string)$allAmount,8) === -1){
                    DB::rollback();
                    Log::info('withdrawal11',['user_id'=>$userId,'all'=>$all,'amount'=>$amount,'reminder'=>$reminder,'time'=>date('Y-m-d H:i:s')]);
                    return [false,__('ac.WithdrawalOver')];
                }
                $data['usage_amount'] = bcadd($reminder,$amount,8);
                WithdrawLimit::create($data);
                DB::commit();
                return [true,__('ac.withdrawalOperationCompleted')];
            }else{
                $reminder = $this->todayReminder($userId);
                $allAmount = bcadd((string)$amount,(string)$reminder,8);
                if (bccomp((string)$WithdrawLimit->btc_balance,(string)$allAmount,8) === -1){
                    $WithdrawLimit->usage_amount = $reminder;
                    $WithdrawLimit->withdraw_time = time();
                    $WithdrawLimit->save();
                    DB::rollback();
                    Log::info('withdrawal22',['user_id'=>$userId,'all'=>$all,'amount'=>$amount,'reminder'=>$reminder,'time'=>date('Y-m-d H:i:s')]);
                    return [false,__('ac.WithdrawalOver')];
                }
                $WithdrawLimit->usage_amount = bcadd($reminder,$amount,8);
                $WithdrawLimit->withdraw_time = time();
                $WithdrawLimit->save();
                DB::commit();
                return [true,__('ac.withdrawalOperationCompleted')];
            }
        }catch (\Exception $exception){
            DB::rollback();
            return [false,$exception->getMessage()];
        }
    }

    /**
     * 是否通过2FA 其中一个
     * @param $userId
     * @return bool
     */
    public function access2FA($userId)
    {
        $authType = User::where('id',$userId)->value('auth_type');
        if (empty($authType)) return false;
        return $authType;
    }

    /**
     * 是否通过Kyc 其中一个
     * @param $userId
     * @return bool
     */
    public function accessKyc($userId)
    {
        $authType = User::where('id',$userId)->first(['card_id','passport_id','is_certification']);
        if ((int)$authType->is_certification != 4) return false;
        if (!empty($authType->passport_id))  return $authType->passport_id;
        if (!empty($authType->card_id))  return $authType->card_id;
        return false;
    }

    /**
     * 上个月交易金额 btc 计算
     * @param $userId
     * @param $amount
     * @return array
     */
    public function monthTradeVolume($userId,$amount = 15)
    {
        //todo 未做美元
        //买金额
        $btcAmountBuy = XchangeDetail::select(DB::raw('cast(sum(cast(trade_volume*price as decimal(18,8))) as decimal(18,8)) as btc'))->where([
            ['buy_user','=',$userId],
//            ['created_at','>=',date('Y-m-01 00:00:00',strtotime("-1 month"))],
            ['created_at','<',date('Y-m-01 00:00:00')],
        ])->value('btc');

        //卖金额
        $btcAmountSell = XchangeDetail::select(DB::raw('cast(sum(cast(trade_volume*price as decimal(18,8))) as decimal(18,8)) as btc'))->where([
            ['sell_user','=',$userId],
//            ['created_at','>=',date('Y-m-01 00:00:00',strtotime("-1 month"))],
            ['created_at','<',date('Y-m-01 00:00:00')],
        ])->value('btc');
        $btcAmount = bcadd($btcAmountBuy,$btcAmountSell,8);
        if (empty($btcAmount)) return [false,null];
        if (bccomp((string)$btcAmount,(string)$amount,8) === -1) return [false,$btcAmount];
        return [true,$btcAmount];
    }

    /**
     *
     * @param $userId
     * @param int $amount
     * @return array
     */
    public function monthTradeVolumeBtc($userId,$amount = 15)
    {
        $btcAmountBuy = Xchange::select(DB::raw('cast(sum(cast((volume-rvolume)*price as decimal(18,8))) as decimal(18,8)) as btc'))->where([
            ['user_id','=',$userId],
            ['created_at','<',date('Y-m-01 00:00:00')],
            ['market_name','like',"%_btc"],
        ])->value('btc');

        if (empty($btcAmountBuy)) return [false,$btcAmountBuy];
        if (bccomp((string)$btcAmountBuy,(string)$amount,8) === -1) return [false,$btcAmountBuy];
        return [true,$btcAmountBuy];
    }

    /**
     * 今日已提现提现
     * @param $userId
     * @return int|string
     */
    public function todayReminder($userId)
    {
        $withdrawHistories = WithdrawHistory::where('user_id',$userId)
            ->where(function ($query){
                $query->where('agree_time', '>=', date("Y-m-d 00:00:00"))
                    ->where('status',1)
                    ->orWhereIn('status',[0,2]);
            })->select('currency',DB::raw('sum(amount+max_fee) as sum'))
            ->groupBy('currency')->get();

        $last_price = Market::pluck('last_price', 'market_name','fee');

        if ($withdrawHistories->isEmpty()) return '0.00000000';
        $hadWithdraw = 0;
        foreach ($withdrawHistories as $history) {
            if ($history->currency != 'BTC') {
                $hadWithdraw = bcadd($hadWithdraw,bcmul($history->sum,$last_price[strtolower($history->currency).'_btc'],8),8);
            }else{
                $hadWithdraw = bcadd($hadWithdraw,$history->sum,8);
            }
        }
        if (bccomp((string)$hadWithdraw,'0.00000001',8) === -1 ) $hadWithdraw = '0.00000001';
        return $hadWithdraw;
    }

    /**
     * todo 加入定时任务 每个月计算
     * 获取可以升级的用户
     * @param int $amount
     * @return array
     */
    public function upgrade($amount = 15)
    {
        try{
            $withDrawUsers = WithdrawLimit::join('users','withdraw_limit.user_id','=','users.id')->where([
                ['auth_type','>',0],
                ['is_certification','=',4]
            ])->whereIn('level',[1,5])->get(['withdraw_limit.id','withdraw_limit.user_id','withdraw_limit.level']);

            if ($withDrawUsers->isEmpty()) return ['result'=>false,'msg'=>'没有用户通过第一级验证'];
            $res = [];
            foreach ($withDrawUsers as $key => $user){
                $resArr = $this->monthTradeVolumeBtc($user->user_id,$amount);
                if ($resArr[0]){
                    $data = [];
                    if ((int)$user->level === 1){
                        $data['level'] = $user->level + 2;
                        $data['btc_balance'] = $user->btc_balance = 5;
                    }elseif ((int)$user->level === 5){
                        $data['level'] = $user->level + 2;
                    }else{
                        continue;
                    }
                    $res[$user->id]['effect'] = WithdrawLimit::where('id',$user->id)->update($data);
                    $res[$user->id]['btc'] = $resArr;
                }else{
                    continue;
                }
            }
            return $res;
        }catch (\Exception $exception){
            return [$exception->getMessage()];
        }
    }

    /**
     * 生成自定义规则
     * @param $userId
     * @param $start
     * @param $end
     * @param $btc
     * @return array
     */
    public function customize($userId,$start,$end,$btc)
    {
        try{
            if ($start > $end)  return ['res'=>false,'msg'=>"Time is false"];
            $fa = $this->access2FA($userId);
            if (!$fa) return [false,__('ac.2FANotVerified')];
            $ak = $this->accessKyc($userId);
            if (!$ak) return [false,__('ac.kycHasVerified')];

            $exist =  Customize::join('withdraw_limit','customizes.user_id','=','withdraw_limit.user_id')
                ->whereRaw("withdraw_limit.user_id = {$userId}")->whereIn('status',[0,1])->first(['customizes.status','level']);

            DB::beginTransaction();
            if (!empty($exist)){
                if ((int)$exist->status === 0){
                    Customize::where([
                        ['user_id','=',$userId],
                        ['status','=',0],
                    ])->update(['status'=>3]);
                }else{
                    Customize::where([
                        ['user_id','=',$userId],
                        ['status','=',1],
                    ])->delete();
                    $exData = ['start_interval'=>0,'end_interval'=>0,'level'=>$exist->level -4,'btc_balance'=>WithdrawLimit::$level[$exist->level-4]];
                    WithdrawLimit::where('user_id',$userId)->update($exData);
                }
            }
            $data['user_id'] = $userId;
            $data['start'] = $start;
            $data['end'] = $end;
            $data['btc'] = $btc;

            Customize::create($data);
            $withDrawlData = [
                'start_interval' => $start,
                'end_interval' => $end,
            ];
            WithdrawLimit::where('user_id',$userId)->update($withDrawlData);
            DB::commit();
            return ['res'=>true,'msg'=>'success'];
        }catch (\Exception $exception){
            DB::rollback();
            return ['res'=>false,'msg'=>$exception->getMessage(),'title'=>1344];
        }
    }

    //开始时间
    public function customizeRunStart()
    {
        try{
            //加入
            $withdrawLimits = WithdrawLimit::join('customizes', 'withdraw_limit.user_id', '=', 'customizes.user_id')
                ->where([
                    ['start_interval','<=',time()],
                    ['status','=',0],
                    ['start','<=',time()],
                ])->get(['withdraw_limit.user_id','level','btc']);

            if ($withdrawLimits->isEmpty())  return ['res'=>true,'msg'=>'data empty'];

            DB::beginTransaction();
            foreach ($withdrawLimits as $key =>$withdrawLimit ){
                $withDrawl = WithdrawLimit::where('user_id',$withdrawLimit->user_id)
                    ->update([
                        'level'=>$withdrawLimit->level+4,
                        'btc_balance'=>$withdrawLimit->btc,
                    ]);

                if ($withDrawl < 1){
                    DB::rollback();
                    return ['res'=>false,'msg'=> 'update false WithdrawLimit'];;
                }

                $customize = Customize::where([
                    ['user_id','=',$withdrawLimit->user_id],
                    ['status','=',0],
                ])->update(['status'=>1]);

                if ($customize < 1){
                    DB::rollback();
                    return ['res'=>false,'msg'=> 'update false Customize'];
                }
            }
            DB::commit();
            return ['res'=>true,'msg'=> 'ok'];
        }catch (\Exception $exception){
            DB::rollback();
            return ['res'=>false,'msg'=> $exception->getMessage()];
        }
    }

    /**
     * 结束时间
     * @return array
     */
    public function customizeRunEnd()
    {
        try{
            $withdrawLimits = WithdrawLimit::join('customizes', 'withdraw_limit.user_id', '=', 'customizes.user_id')
                ->where([
                    ['end_interval','<=',time()],
                    ['status','=',1],
                    ['end','<=',time()],
                ])->get(['withdraw_limit.user_id','level']);


            if (empty($withdrawLimits))  return ['res'=>true,'msg'=>'data empty'];

            DB::beginTransaction();
            foreach ($withdrawLimits as $key =>$withdrawLimit ){
                $withDrawl = WithdrawLimit::where('user_id',$withdrawLimit->user_id)
                    ->update([
                        'level'=>$withdrawLimit->level-4,
                        'btc_balance'=>WithdrawLimit::$level[$withdrawLimit->level-4],
                        'start_interval' => 0,
                        'end_interval' => 0,
                    ]);

                if ($withDrawl < 1){
                    DB::rollback();
                    return ['res'=>false,'msg'=> 'update false WithdrawLimit'];
                }

                $customize = Customize::where([
                    ['user_id','=',$withdrawLimit->user_id],
                    ['status','=',1],
                ])->update(['status'=>2]);

                if ($customize < 1){
                    DB::rollback();
                    return ['res'=>false,'msg'=> 'update false Customize'];
                }
            }
            DB::commit();
            return ['res'=>true,'msg'=> 'ok'];
        }catch (\Exception $exception){
            DB::rollback();
            return ['res'=>false,'msg'=> $exception->getMessage()];
        }
    }
}