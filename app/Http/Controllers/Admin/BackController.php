<?php

namespace App\Http\Controllers\Admin;

use App\AuthInfo;
use App\Authy;
use App\Events\RegisterSendEmail;
use App\Http\Controllers\FrontEnd\BlockchainController;
use App\Jobs\SendEmail;
use App\Models\BalanceDB;
use App\Models\DepositHistory;
use App\Models\IgnoredUser;
use App\Models\KYCRejectlist;
use App\Models\KYCRejectReason;
use App\SmsAuth;
use App\Tool\WithdrawalControl;
use App\User;
use App\WithdrawHistory;
use App\Manager;
use App\WithdrawLimit;
use Illuminate\Http\Request;
use App\ManagerActionLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Validator;
use Excel;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Exception;
use Carbon\Carbon;
use App\Models\Currency;
use App\Models\Xchange;
use App\Http\Controllers\Controller as Controller;

class BackController extends Controller
{
    protected $guard = 'back';
    protected $client = '';

    public function __construct()
    {
        $this->middleware('checkmanager',['except'=>'IgnoredUsers,auatest']);
    }

    public static function getCurrency(Request $request){
        if(!$request->currency_id){
            $str1 = 'currency.is_show'; $str2 = 1;
        }else{
            $str1 = 'currency.id'; $str2 = $request->currency_id;
        }
        $currency_list = Currency::where($str1,$str2)
            ->leftJoin('market','currency.id','=','market.from_currency')
            ->select('currency','full_currency','last_price')
            ->orderBy('currency.id','asc')
            ->get()
            ->toArray();
        return $currency_list;
    }

    public function getCurrencyInfo(Request $request){
        $currency_list = $this->getCurrency($request);
        $outGoing = $this->todayOutGoing($request);
        $data = [];
        foreach ($currency_list as $k=>$currency) {
            //balance
            $db = BalanceDB::whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('ignored_users')
                    ->whereRaw('ignored_users.user_id = balance_db.user_id');
            })->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('users')
                    ->where('users.register_confirm',1)
                    ->whereRaw('users.id = balance_db.user_id');
            })->where('currency',$currency['currency'])
                ->where('user_id','>',2)
                ->select(DB::raw('sum(balance) as balance'))->get();
            $balance = $db[0]->balance;
            $data[$currency['currency']]['balance'] = $balance;
            $data[$currency['currency']]['withdraw'] = Cache::get($currency['currency'].'_withdraw_balance');
            $data[$currency['currency']]['node'] = Cache::get($currency['currency'].'_node_balance');
            $data[$currency['currency']]['address'] = Cache::get($currency['currency'].'_address');
        }
        return view('dashboard.dashboard',compact('data','outGoing'));
    }

    public function todayOutGoing(Request $request){
        $currency_list = $this->getCurrency($request);
        $data = [];
        foreach ($currency_list as $currency) {
            $change = WithdrawHistory::where('currency',$currency['currency'])->where('status',4)
                ->where(DB::raw('date(created_at)'), Carbon::parse(date('Y-m-d')))
                ->get();
            $data[$currency['currency']]['total'] = 0;
            foreach ($change as $key=>$list){
//                $data[$currency['currency']][$key]['txid'] = $list->txid;
                $data[$currency['currency']][$key]['no'] = $key+1;
                $data[$currency['currency']][$key]['user_id'] = $list->user_id;
                $data[$currency['currency']][$key]['amount'] = trim($list->amount,'-');
                $data[$currency['currency']][$key]['date'] = $list->created_at;
                $data[$currency['currency']]['total'] += $data[$currency['currency']][$key]['amount'];
            }
        }
        return $data;
    }

    public static function getCurrencyInfoRun(){
        $currency_list = self::getCurrency(new Request());
        foreach ($currency_list as $k=>$currency) {
            $client = parent::getStaticClient($currency['currency']);
            $withdraw = $client->getSlaveBalance(1);
            $address = $client->getAddress(1);
            $node = $client->getInfo();
            Cache::forever($currency['currency'].'_withdraw_balance',number_format($withdraw,8,'.',''));
            Cache::forever($currency['currency'].'_node_balance',number_format($node,8,'.',''));
            Cache::forever($currency['currency'].'_address',$address);
        }
    }


    public function auatest(Request $request){
        if($request->runCache)
            $this->getCurrencyInfoRun();
        if($request->job&&$request->user_id){
            $w = WithdrawHistory::where('user_id',$request->user_id)->first();
            SendEmail::dispatch($w,'withdraw_reject')->onConnection('database')->onQueue('email');
            return 'ok';
        }
        if($request->txid) {
            if($request->currency){
                $client = $this->getClient($request->currency);
                $list = $client->gettransactionDetail($request->txid);
                dump($list);
            }
        }
        if($request->check) {
            if($request->currency&&$request->user_id){
                $client = $this->getClient($request->currency);
                $list = $client->getTransactionList($request->user_id);
                foreach ($list as $row){
                    if($row['category'] != 'move')
                        $row['timereceived'] = date('Y-m-d H:i:s',$row['timereceived']);
                }
                dump($list);
            }
        }
        if($request->balance) {
            if($request->user_id){
                if($request->currency){
                    $client = $this->getClient($request->currency);
                    dump($request->currency.' Balance');
                    dump($client->_get_balance($request->user_id,1));
                }else{
                    $currency_list = $this->getCurrency(new Request());
                    foreach ($currency_list as $row){
                        $client = $this->getClient($row['currency']);
                        dump($row['currency'].' Balance');
                        dump($client->_get_balance($request->user_id,1));
                    }
                }
            }
        }
    }

    public function getHistory(Request $request){
        if($request->page || $request->export) {
            if(!$request->currency_id)
                $request->currency_id = 1;
            $currency = Currency::where('id',$request->currency_id)->first();
            $filename = $currency['currency'].' History list';
            $wheresD = $wheresW = [];
            if($request->user_id){
                $filename .= ' user['.$request->user_id.']';
                $wheresD[] = ['user_id',$request->user_id];
                $wheresW[] = ['user_id',$request->user_id];
            }
            if($request->t_start){
                $filename .= '('.$request->t_start.' - '.$request->t_end.')';
                $t_start = $request->t_start; $t_end = $request->t_end;
                $wheresD[] = [DB::raw('date(created_at)'), '>=', $t_start];
                $wheresD[] = [DB::raw('date(created_at)'), '<=', $t_end];
                $wheresW[] = [DB::raw('date(agree_time)'), '>=', $t_start];
                $wheresW[] = [DB::raw('date(agree_time)'), '<=', $t_end];
            }
            $dataW = WithdrawHistory::where($wheresW)
                ->where('status',1)
                ->where('currency',$currency['currency'])
                ->select('agree_time as created_at','txid','amount','currency','address','user_id','status','remarks');
            $wsum = clone $dataW;
            $wsum = $wsum->select(DB::raw('sum(amount) as sum'))->first()->sum;
            $wsum = $wsum ? $wsum : 0;
            $dataD = DepositHistory::where($wheresD)
                ->where('currency_id',$request->currency_id)
                ->select('created_at','txid','amount',DB::raw('null as currency'),'address','user_id','status',DB::raw('null as remarks'));
            $dsum = clone $dataD;
            $dsum = $dsum->select(DB::raw('sum(amount) as sum'))->first()->sum;
            $dsum = $dsum ? $dsum : 0;
            $data = $dataD->union($dataW)->orderby('created_at','asc');
            $totalPage = count($data->get());
            $page = $request->page ? $request->page * 10 - 10 : 0;
            $data = $request->export ? $data->get() : $data->skip($page)->take(10)->get();

            foreach ($data as $key=>$row){
                $user = User::leftJoin('country_regions','users.nationality','country_regions.country_id')
                    ->where('id',$row->user_id)->select('name','email','country_regions.en_country')->first();
                if($user){
                    $rpz_history = Xchange::where('user_id',$row->user_id)->where('market_id',4)->where('status',1)->first();
                    $row->trade_rpz = $rpz_history ? 'YES' : 'NO';
                    $vit_history = Xchange::where('user_id',$row->user_id)->where('market_id',5)->where('status',1)->first();
                    $row->trade_vit = $vit_history ? 'YES' : 'NO';
                    $deposit = DB::select('select sum(amount) as sum from deposit_history where user_id=? and currency_id=1',[$row->user_id]);
                    $row->deposit_btc = isset($deposit[0]->sum) ? number_format($deposit[0]->sum,8,'.','') : 'NO';
                    $row->name = $user->name;
                    $row->email = $user->email;
                    $row->nationality = $user->en_country;
                }else{
                    $row->trade_rpz = $row->trade_vit = $row->deposit_btc = $row->name = $row->email = '';
                }
                $row->receive = $row->currency ? '' : $row->amount;
                $row->send = $row->currency ? $row->amount : '';
                $row->time = $row->created_at ? $row->created_at->format('Y-m-d H:i') : '0000-00-00 00:00:00';
            }
            if($request->export){//print
                $other = [$dsum,$wsum];
                $header = ['Data','UserID','Name','Email','Nationality','Deposit','Withdrawal','Txid','Address','RPZ','VIT','Fund BTC','Remarks'];
                return $this->exportExcel($data,$header,$filename,$type = $request->export,$other);
            }
            return ['code'=>200,$data,$totalPage,$dsum,$wsum];
        }else{
            return view('dashboard.WalletHistory');
        }
    }

    public function withdrawalCheck(Request $request,$status){
        if($request->page || $request->export){
            switch ($status)
            {
                case 'pending':
                    $s = 2;
                    break;
                case 'approve':
                    $s = 1;
                    break;
                case 'reject':
                    $s = 3;
                    break;
            }
            if(!$request->currency_id)
                $request->currency_id = 1;
            $currency = Currency::find($request->currency_id)->currency;
            $filename = $currency.' Withdraw '.$status;
            $withdrawal = WithdrawHistory::leftJoin('users','withdraw_history.user_id','=','users.id')
                ->where('withdraw_history.status',$s)
                ->where('withdraw_history.currency',$currency)
                ->where(function ($query) use($request) {
                    $query->where('withdraw_history.user_id','like','%'.$request->user_id.'%')
                        ->orWhere('users.name','like','%'.$request->user_id.'%');
                })->select('withdraw_history.*');
            if($request->t_start){
                $filename .= '('.$request->t_start.' - '.$request->t_end.')';
                $t_start = Carbon::parse($request->t_start);
                $t_end = Carbon::parse($request->t_end);
                $withdrawal = $withdrawal->where(DB::raw('date(created_at)'), '>=', $t_start)
                    ->where(DB::raw('date(created_at)'), '<=', $t_end);
            }
            $withdrawal = $withdrawal->paginate(10);
            foreach ($withdrawal as $row){
                $user = User::leftJoin('country_regions','users.nationality','country_regions.country_id')
                    ->where('users.id',$row->user_id)->first();
                $row->bind_user_id = $user->bind_user_id;
                $row->nationality = $user->en_country;
                if($user){
                    $row->name = $user->name;
                }else{
                    $row->name = '';
                }
            }
            if($request->export){//print
                $header = ['Data','UserID','Name','Nationality','Amount','Fee','To Address','Status'];
                return $this->exportExcel($withdrawal,$header,$filename,$type = $request->export,$status);
            }
            $totalPage = $withdrawal->toArray()['total'];
            return ['code'=>200,$withdrawal,$totalPage];
        }else{
            return view('dashboard.withdrawal-'.$status);
        }
    }

    public function withdrawAction(Request $request){
        $validator = Validator::make($request->all(), [
            'id_list' => 'required',
            'status' => 'required'
        ]);
        if ($validator->fails()) {
            return $this->failure(-1, $validator->errors()->first());
        }
        if($request->status == 1){//Approve
            foreach ($request->id_list as $id){
                DB::beginTransaction();
                $with_status = false;
                $withdrawal = WithdrawHistory::where('id',$id)->lockForUpdate()->first();
                if($withdrawal){
                    if($withdrawal->status == 2){
                        Log::info($id);
                        $client = $this->getAllClicent($withdrawal->currency);
                        $current_balance = $client->_get_balance($withdrawal->user_id);
                        if ($current_balance<0 ){//验证余额
                            $withdrawal->status = 3;
                            $withdrawal->save();
                            $client->_get_balance($withdrawal->user_id);
                            $this->logAction('withdrawal_reject',$withdrawal,'User balance not enough');
                        }else if(self::shifou_wanquan_xieru_tixian($withdrawal->user_id) == false){
                            $withdrawal->status = 3;
                            $withdrawal->save();
                            $client->_get_balance($withdrawal->user_id);
                            $this->logAction('withdrawal_reject',$withdrawal,'withdraw_history  error');
                        }else{//审核通过
                            try{
                                //验证地址
                                if (!$client->validateaddress($withdrawal->address)['isvalid'])
                                    return ['code'=>403,'msg'=>'Address error or Link error'];
                                // 对比节点余额
                                $node_balace = $client->getInfo();
                                if($withdrawal->btc_amount > $node_balace)
                                    return ['code'=>403,'msg'=>'Withdraw fail(node balance not enough)'];

                                if($withdrawal->currency == 'RPZ'||$withdrawal->currency == 'PIVX') {
                                    $amount = floatval($withdrawal->amount);
                                }else{
                                    $amount = $withdrawal->amount;
                                }
                                Log::info('(Dashboard)-withdraw-- amount:'.$amount.$withdrawal->currency.'-- user:'.$withdrawal->user_id.' ---');
                                $with_status = $client->acWithdraw($withdrawal->address,$amount,$withdrawal->user_id,$withdrawal->max_fee);
                                if($with_status == null){
                                    $withdrawal->status = 3;
                                    $withdrawal->save();
                                    $client->_get_balance($withdrawal->user_id);
                                    $this->logAction('withdrawal_reject',$withdrawal,'withdraw error');
                                }
                            }catch (Exception $e){//连不上区块链报错
                                return ['code'=>403,'msg'=>'Block chain link error'];
                            }
                        }
                        if($with_status) {
                            $this->logAction('withdrawal_approve',$withdrawal);
                            $tx = $client->gettransactionDetail($with_status);
                            $withdrawal->max_fee = bcsub(0,number_format($tx['fee'],8,'.',''),8);
                            $withdrawal->txid = $with_status;
                            $withdrawal->status = 1;
                            $withdrawal->agree_time = date('Y-m-d H:i:s',time());
                            $withdrawal->save();
                            $client->_get_balance($withdrawal->user_id);
                            //send Email
                            SendEmail::dispatch($withdrawal,'withdraw_approve')->onConnection('database')->onQueue('email');
                        }else{
                            //发送邮件提醒被拒绝
                            SendEmail::dispatch($withdrawal,'withdraw_reject')->onConnection('database')->onQueue('email');
                        }
                    }
                }
                DB::commit();
            }
        }else{//Reject
            foreach ($request->id_list as $id){
                DB::beginTransaction();
                $withdrawal = WithdrawHistory::where('id',$id)->lockForUpdate()->first();
                if($withdrawal){
                    if($withdrawal->status == 2){
                        $withdrawal->status = 3;
                        $withdrawal->save();
                        $client = $this->getAllClicent($withdrawal->currency);
                        $client->_get_balance($withdrawal->user_id);
                        $this->logAction('withdrawal_reject',$withdrawal);
                        //发送邮件提醒被拒绝
                        SendEmail::dispatch($withdrawal,'withdraw_reject')->onConnection('database')->onQueue('email');
                    }
                }
                DB::commit();
            }
        }
        return ['code'=>200];
    }

    public function IgnoredUsers(Request $request){
        $validator = Validator::make($request->all(), [
            'ignored' => 'exists:users,id',
            'type' => 'required'
        ]);
        if ($validator->fails()) {
            return $this->failure(-1, $validator->errors()->first());
        }
        if($request->type == 1){//add
            if(!IgnoredUser::where('user_id',$request->ignored)->first()){
                $ignored = new IgnoredUser();
                $ignored->user_id = $request->ignored;
                $ignored->save();
            }
        }else{//minus
            IgnoredUser::where('user_id',$request->ignored)->delete();
        }
        return ['code'=>200];
    }

    public function getChange(Request $request){
        if($request->page || $request->export){
            $t2 = '>'; $s2 = '>';//rank排序
            $t3 = 0; $s3 = -1;
            $filename = '';
            if($request->rank){
                $rank = explode('-',$request->rank);
                if(isset($rank[0])){
                    switch ($rank[0]){
                        case 'Buy':
                            $t2 = '='; $t3 = 2; break;
                        case 'Sell':
                            $t2 = '='; $t3 = 1; break;
                        default:
                            $t2 = '>'; break;
                    }
                    $filename = $rank[0] . ' ';
                }
                if(isset($rank[1])){
                    switch ($rank[1]){
                        case 'Success':
                            $s2 = '='; $s3 = 1; break;
                        case 'In Progress':
                            $s2 = '='; $s3 = 0; break;
                        case 'Cancel':
                            $s2 = '='; $s3 = 3; break;
                        default:
                            $s2 = '>'; break;
                    }
                    $filename .= $rank[1] . ' ';
                }
            }
            if($request->tran){
                switch ($request->tran){
                    case 2: $market = 'bch_btc'; break;
                    case 3: $market = 'ltc_btc'; break;
                    case 4: $market = 'rpz_btc'; break;
                    case 5: $market = 'eth_btc'; break;
                    case 6: $market = 'xvg_btc'; break;
                    case 7: $market = 'btg_btc'; break;
                    default:$market = 'bch_btc'; break;
                }
            }else{
                $market = 'bch_btc';
            }
            $filename .= $market;
            $data = Xchange::where('market_name',$market);
            if($request->user_id){
                $data = $data->where('user_id',$request->user_id);
                $filename .= ' user['.$request->user_id.']';
            }
            $data = $data->where('type', $t2, $t3)->where('status', $s2, $s3);
            if($request->t_start){
                $filename .='('.$request->t_start.' - '.$request->t_end.')';
                $t_start = $request->t_start; $t_end = $request->t_end;
                $data = $data->where(DB::raw('date(updated_at)'), '>=', $t_start)
                    ->where(DB::raw('date(updated_at)'), '<=', $t_end);
            }
            //ignored
            $ignored = null;
            if($request->ignored){
                $ignored = IgnoredUser::select('user_id')->get();
                $data = $data->whereNotIn('user_id',$ignored);
            }
            //sum
            if(session('xchange_search') != $request->user_id.$request->rank.$market.$request->t_start.$request->t_end.$request->ignored){
                $ssum = clone $data;
                $bsum = clone $data;
                $ssum = $ssum->where('type',1)
                    ->select(
                        DB::raw('sum(volume-rvolume) as volume'),
                        DB::raw('sum((volume-rvolume)*price) as total_price'),
                        DB::raw('sum(fee/volume*(volume-rvolume)) as fee')
                    )->get()->toArray()[0];
                $bsum = $bsum->where('type',2)
                    ->select(
                        DB::raw('sum(volume-rvolume) as volume'),
                        DB::raw('sum((volume-rvolume)*price) as total_price'),
                        DB::raw('sum(fee/volume*(volume-rvolume)) as fee')
                    )->get()->toArray()[0];
                $fee = bcadd($ssum['fee'],$bsum['fee'],8);
                $ssum['volume'] = number_format($ssum['volume'],8,'.','');
                $ssum['total_price'] = number_format($ssum['total_price'],8,'.','');
                $bsum['volume'] = number_format($bsum['volume'],8,'.','');
                $bsum['total_price'] = number_format($bsum['total_price'],8,'.','');
                session([
                    'xchange_search'=>$request->user_id.$request->rank.$market.$request->t_start.$request->t_end.$request->ignored,
                    'ssum'=>$ssum,
                    'bsum'=>$bsum,
                    'fee'=>$fee
                ],5);
            }else{
                $ssum = session('ssum');
                $bsum = session('bsum');
                $fee = session('fee');
            }
            //total
            $totalPage = $data->count();
            $data = $data->orderBy('updated_at','desc');
            if($request->export) $data = $data->get();
            else $data = $data->paginate(10);
            foreach ($data as $list){
                $list->partially = bcsub($list->volume,$list->rvolume,8);
                switch ($list->status){
                    case 0:
                        $list->status = 'unfinished';
                        break;
                    case 1:
                        $list->status = 'success';
                        break;
                    case 3:
                        $list->status = 'cancel';
                        break;
                    default:
                        break;
                }
                $list->type = $list->type == 1 ? 'sell' : 'buy';
                $user = User::where('id',$list->user_id)->select('name','email')->first();
                if($user){
                    $list->name = $user->name;
                    $list->email = $user->email;
                }else{
                    $list->name = '';
                    $list->email = '';
                }
            }

            if($request->export){//print
                $other = [$ssum,$bsum,$fee];
                $header = ['Data','UserID','Name','Email','Type','Amount','Value','Partially Filled','Total Value','Transaction Fee','Status'];
                return $this->exportExcel($data,$header,$filename,$type = $request->export,$other);
            }else//data
                return ['code'=>200,$data,$totalPage,$ssum,$bsum,$fee,$ignored];
        }else{
            if($request->ignored)
                return view('dashboard.transactions-report');
            else
                return view('dashboard.transactions');
        }
    }

    public function showUnCheck(Request $request,$status){
        if($request->page || $request->export){
            $auth_status = 0;
            $auth_style = 'btn-nov';
            switch ($status)
            {
                case 'unchecked':
                    $auth_status = 1;
                    $auth_style = 'nov-btn';
                    break;
                case 'passed':
                    $auth_status = 4;
                    $auth_style = 'btn-success';
                    break;
                case 'rejected':
                    $auth_status = 3;
                    $auth_style = 'btn-danger';
                    break;
            }
            $uncheck_list = User::with(['IdCards','Passports'])
                ->where('is_certification',$auth_status)
                ->where(function ($query) use($request) {
                    $query->where('users.id','like','%'.$request->something.'%')
                        ->orWhere('first_name','like','%'.$request->something.'%')
                        ->orWhere('last_name','like','%'.$request->something.'%')
                        ->orWhere('nationality','like','%'.$request->something.'%');
                })
                ->leftJoin('country_regions','users.nationality','=','country_regions.country_id')
                ->orderBy('certification_time');
            $totalPage = $uncheck_list->count();
            $uncheck_list = $uncheck_list->paginate(10);
            if($auth_status != 3){
                foreach ($uncheck_list as $row){
                    $arr1 = $this->contrastName($row->id,$row->first_name,$row->last_name);
                    $arr2 = $this->contrastName($row->id,$row->last_name,$row->first_name);
                    $arr = array_merge($arr1,$arr2);
                    if($arr)
                        $row->same = implode("<br>",array_unique($arr));
                    else
                        $row->same = [];
                }
            }
            return ['code'=>200,$uncheck_list,$totalPage,$status,$auth_style];
        }else{
            return view('dashboard.'.$status);
        }
    }

    public function contrastName($user_id,$first_name,$last_name){
        $first_name = explode(' ',str_replace('_',' ',$first_name));
        $last_name = explode(' ',str_replace('_',' ',$last_name));
        $user = User::find($user_id);
        $authenType = $user->card_id ? 'card_id' : 'passport_id';
        $same = User::where('id','!=',$user_id)
            ->whereNotNull($authenType)
            ->where('is_certification','<>',3);
        foreach ($first_name as $like)
            $same = $same->where('first_name','like','%'.$like.'%');
        foreach ($last_name as $like)
            $same = $same->where('last_name','like','%'.$like.'%');
        $same = $same->select('id')->get();
        $arr = [];
        if(count($same) > 0){
            foreach ($same as $s)
                array_push($arr,$s->id);
        }
        return $arr;
    }

    public function checkDetail(Request $request){
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'status' => 'required'
        ]);
        if ($validator->fails()) {
            return ['code'=>400,'message'=>$validator->errors()->first()];
        }
        $status = $request->status;
        $user = User::where('id',$request->user_id)
            ->with(['IdCards','Passports'])
            ->leftJoin('country_regions','users.nationality','=','country_regions.country_id')
            ->first();
        if($user->card_id){
            $user->type = 'ID Card';
            $user->id_number = $user->IdCards->card_number;
            $user->pictures = [$user->IdCards->img_front,$user->IdCards->img_back,$user->IdCards->img_hand];
        }
        if($user->passport_id){
            $user->type = 'Passport';
            $user->id_number = $user->Passports->passport_number;
            $user->pictures = [$user->Passports->img_front,$user->Passports->img_back];
        }
        switch ($status)
        {
            case 3:
                $auth_status = 'nopass-content';
                break;
            case 4:
                $auth_status = 'pass-content';
                break;
            default:
                $auth_status = '';
        }
        return view('dashboard.auditStatus',compact('user','auth_status','status'));
    }

    public function exportExcel($data,$header,$filename,$type,$other){
        ini_set('memory_limit','1024M');
        iconv('UTF-8', 'GB2312//IGNORE', $filename);
        $print = [$header];
        if($type == 'balance'){
            foreach ($data as $row){
                $list = [$row['id'],$row['email'],$row['BTC'],$row['BCH'],$row['LTC'],$row['RPZ'],$row['XVG'],$row['BTG'],$row['DASH']];
                array_push($print,$list);
            }
        }
        if($type == 'withdrawTerm'){
            foreach ($data as $row)
                $print[] = [$row['user_id'],$row['email'],$row['en_country'],$row['btc_balance'],$row['start_interval'],$row['end_interval']];
        }
        if($type == 'log'){
            switch($other){
                case 1: $other = 'normal';break;
                case 2: $other = 'move';break;
                case 3: $other = 'withdraw';break;
                default: $other = 'normal';break;
            }
            foreach ($data as $row){
                $list = [$row['updated_at'],$other,$row['author_name'],$row['ip_address'],$row['action']];
                array_push($print,$list);
            }
        }
        if($type == 'withdraw'){
            foreach ($data as $row){
                $list = [$row['created_at'],$row['user_id'],$row['name'],$row['nationality'],$row['amount'],$row['fee'],$row['address'],$other];
                array_push($print,$list);
            }
        }
        if($type == 'withdraw_report'){
            foreach ($data as $row){
                $list = [$row['user_id'],$row['sum']];
                array_push($print,$list);
            }
            $list = ['Total',$other];
            array_push($print,$list);
        }
        if($type == 'client'){
            foreach ($data as $row){
                $row['register_confirm'] = $row['register_confirm'] ? 'Yes' : 'No';
                $row['authenType'] = '';
                $row['id_number'] = '';
                if($row['card_id']){
                    $row['authenType'] = 'ID Card';
                    $table = DB::table('id_cards')->where('id',$row['card_id'])->first();
                    $row['id_number'] = $table ? $table->card_number : '';
                }
                if($row['passport_id']){
                    $row['authenType'] = 'Passport';
                    $table = DB::table('passports')->where('id',$row['passport_id'])->first();
                    $row['id_number'] = $table ? $table->passport_number : '';
                }
                switch ($row['is_certification']){
                    case 0:$row['is_certification'] = 'Not yet upload';
                        break;
                    case 1:$row['is_certification'] = 'Upload';
                        break;
                    case 3:$row['is_certification'] = 'NO PASS';
                        break;
                    case 4:$row['is_certification'] = 'PASS';
                        break;
                    default:$row['is_certification'] = '';
                }
                switch ($row['auth_type']){//2FA
                    case 0:$row['auth_type'] = 'Not yet';
                        break;
                    case 1:$row['auth_type'] = 'Google';
                        break;
                    case 2:$row['auth_type'] = 'AUTHY';
                        break;
                    case 3:$row['auth_type'] = 'SMS';
                        break;
                    default:$row['auth_type'] = '';
                }
                $list = [$row['name'],$row['email'],$row['first_name'].' '.$row['last_name'],$row['authenType'],$row['id_number'],$row['register_confirm'],$row['is_certification'],$row['auth_type'],$row['login_ip']];
                array_push($print,$list);
            }
        }
        if($type == 'balance'){
            foreach ($data as $row){
                $list = [$row['id'],$row['email'],$row['BTC'],$row['BCH'],$row['LTC'],$row['RPZ'],$row['XVG'],$row['BTG'],$row['DASH']];
                array_push($print,$list);
            }
        }
        if($type == 'history'){
            foreach ($data as $row){
                $list = [$row['created_at'],$row['user_id'],$row['name'],$row['email'],$row['nationality'],$row['receive'],$row['send'],$row['txid'],$row['address'],$row['remarks']];
                array_push($print,$list);
            }
            $list = ['','','','','Total',$other[0],$other[1],'',''];
            array_push($print,[]);
            array_push($print,$list);
        }
        if($type == 'tran'){
            foreach ($data as $row){
                $list = [$row['updated_at'],$row['user_id'],$row['name'],$row['email'],$row['type'],$row['volume'],$row['price'],$row['total_price'],$row['fee'],$row['status']];
                array_push($print,$list);
            }
            $list1 = ['','','','Total','Sell',$other[0]['volume'],'',$other[0]['total_price'],$other[2],''];
            $list2 = ['','','','','Buy',$other[1]['volume'],'',$other[1]['total_price'],'',''];
            array_push($print,[]);
            array_push($print,$list1);
            array_push($print,$list2);
        }
        Excel::create($filename,function($excel) use ($print){
            $excel->sheet('sheet', function($sheet) use ($print) {
                $sheet->rows($print);
            });
        })->export('xlsx');
    }

    public function getGroups($type=null){
        $currency_list = Currency::where('currency.is_show',1)
            ->select('id','currency','full_currency')
            ->orderBy('id','asc')
            ->get();
        $userCount = $type ? User::count() : false;
        if($type == 'airdrop')
            return view('dashboard.airdrop',compact('currency_list','userCount'));
        if($type == 'adjust')
            return view('dashboard.adjust',compact('currency_list'));
    }

    public function getMove(Request $request){
        $validator = Validator::make($request->all(), [
            'currency_id' => 'required|exists:currency,id',
            'amount' => 'required',
            'target_id' => 'required|exists:users,id'
        ]);
        if ($validator->fails()) {
            return $this->failure(-1, $validator->errors()->first());
        }
        $order_id = 'acx'.$request->target_id.uniqid();
        $currency = Currency::where('currency.id',$request->currency_id)->first();
        BlockchainController::slaveMove($currency->currency,$request->target_id,$request->amount,$order_id);
        $this->logAction('move',$request,$currency->currency);
        return ['code'=>200];
    }

    public function airdrop(Request $request){
        $validator = Validator::make($request->all(), [
            'amount' => 'required',
            'currency_id' => 'required'
        ]);
        if ($validator->fails()) {
            return $this->failure(-1, $validator->errors()->first());
        }
        $currency = Currency::where('id',$request->currency_id)->first();
        $user = DB::table('users')->select('id')->get();
        $total_raw = $request->amount * count($user);
        $total = number_format($total_raw,8,'.','');
        $order_id = 'acx'.$request->target_id.uniqid();
        foreach ($user as $row){
            BlockchainController::slaveMove($currency->currency,$row->id,$request->amount,$order_id);
        }
        $data = [$currency->currency,$request->amount,count($user),$total];
        $remark = $request->remarks ? $request->remarks : '';
        $this->logAction('airdrop',$data,$remark);
        return ['code'=>200];
    }

    public function getManagerList(Request $request){
        if($request->page || $request->export) {
            $userAll = Manager::select('id')->get();
            $totalPage = count($userAll);
            $manager = Manager::paginate(10);
            return ['code'=>200,$manager,$totalPage];
        }else{
            return view('dashboard.administrators');
        }
    }

    public function managerAction(Request $request){
        $validator = Validator::make($request->all(), [
            'id'=>'sometimes|required|exists:managers,id',
            'name' => 'sometimes|required',
            'role' => 'sometimes|required',
        ]);
        if ($validator->fails()) {
            return $this->failure(-1, $validator->errors()->first());
        }
        if($request->id){//update
            if(!$request->name){
                Manager::find($request->id)->delete();
                $this->logAction('manager_delete',$request);
            }else{
                $man = Manager::find($request->id);
                $this->logAction('manager_update',$request,$man);
                $man->name = $request->name;
                if($request->password){
                    $man->password = bcrypt($request->password);
                }
                $man->role = $request->role;
//                $man->email = $request->name;
                $man->save();
            }
        }else{//create
            $validator = Validator::make($request->all(), [
                'name' => 'unique:managers',
            ]);
            if ($validator->fails()) {
                return $this->failure(-1, $validator->errors()->first());
            }
            $man = new Manager();
            $man->name = $request->name;
            $man->password = bcrypt($request->password);
            $man->role = $request->role;
            $man->email = $request->name.str_random(10);
            $man->save();
            $this->logAction('manager_create',$man);
        }
        return ['code'=>200];
    }

    public function editManager(Request $request){
        $manager = Manager::where('id',$request->id)->first();
        return view('dashboard.editadmin',compact('manager'));
    }

    public function getUserList(Request $request){
        if($request->page || $request->export){
            $filename = 'Client list';
            if($request->something)
                $filename .= ' filter('.$request->something.')';
            $data = User::where('id','>',2)->where(function ($query) use($request) {
                $query->where('id','like','%'.$request->something.'%')
                    ->orWhere('name','like','%'.$request->something.'%')
                    ->orWhere('email','like','%'.$request->something.'%')
                    ->orWhere('first_name','like','%'.$request->something.'%')
                    ->orWhere('last_name','like','%'.$request->something.'%');
            });
            if($request->t_start){
                $filename .= '('.$request->t_start.' - '.$request->t_end.')';
                $t_start = Carbon::parse($request->t_start);
                $t_end = Carbon::parse($request->t_end);
                $data = $data->where(DB::raw('date(users.created_at)'), '>=', $t_start)
                    ->where(DB::raw('date(users.created_at)'), '<=', $t_end);
            }
            $totalPage = $data->count();
            if($request->export){
                $list = $data->get()->toArray();
            }else{
                $list = $data->paginate(10);
            }
            if($request->export){
                $header = ['Name','Email','Auth Name','Auth Type','ID Number','Email Confirmed','Certification Status','Default 2FA','Last Login IP'];
                return $this->exportExcel($list,$header,$filename,$type = $request->export,'');
            }
            return ['code'=>200,$list,$totalPage];
        }else{
            return view('dashboard.listclient');
        }
    }

    public function userBalance(Request $request){
        $filename = 'Client Balance';
        if($request->something)
            $filename .= ' filter('.$request->something.')';
        if($request->t_start){
            $filename .= '('.$request->t_start.' - '.$request->t_end.')';
            $t_start = Carbon::parse($request->t_start);
            $t_end = Carbon::parse($request->t_end);
            $data = User::where('id','like','%'.$request->something.'%')
                ->where('id','>',2)
                ->where(DB::raw('date(created_at)'), '>=', $t_start)
                ->where(DB::raw('date(created_at)'), '<=', $t_end)
                ->orWhere('users.email','like','%'.$request->something.'%')
                ->where('id','>',2)
                ->where(DB::raw('date(created_at)'), '>=', $t_start)
                ->where(DB::raw('date(created_at)'), '<=', $t_end)
                ->select('created_at','id','email');
        }else{
            $data = User::where('users.id','like','%'.$request->something.'%')
                ->where('id','>',2)
                ->orWhere('users.email','like','%'.$request->something.'%')
                ->where('id','>',2)
                ->select('created_at','id','email');
        }
        $user = [];
        if($request->export)
            $list = $data->get()->toArray();
        else
            $list = $data->paginate(10);
        $curr_list = $this->getCurrency(new Request());
        foreach ($list as $take){
            foreach ($curr_list as $curr) {
                $balance = BalanceDB::where('user_id',$take['id'])->where('currency',$curr['currency'])->first();
                $take[$curr['currency']] = $balance?number_format($balance->balance,8,'.',''):'';
            }
            array_push($user,$take);
        }
        if($request->export){
            $header = ['ID','Email','BTC','LTC','BCH','RPZ','XVG','BTG','DASH'];
            return $this->exportExcel($user,$header,$filename,$type = $request->export,'');
        }else{
            $take = (array)$list;
            foreach ($take as $a){
                $totalPage = $a;
                break;
            }
            if($request->page){
                return ['code'=>200,$user,$totalPage];
            }else{
                return view('dashboard.userBalance');
            }
        }
    }

    public function refreshBalance(Request $request){
        if($request->id){
            $curr_list = $this->getCurrency(new Request());
            foreach ($curr_list as $curr){
                $client = $this->getClient($curr['currency']);
                $client->_get_balance($request->id);
            }
        }
        $balance = BalanceDB::where('user_id',$request->id)->get();
        $user = User::where('id',$request->id)->select('id','created_at','email')->first();
        foreach ($balance as $one){
            if($one->currency)
                $user[$one->currency] = number_format($one->balance,8,'.','');
        }
        return ['code'=>200,$user];
    }

    public function getUserDisabled(Request $request){
        if($request->page || $request->export){
            if($request->something){
                $filename = 'Disabled Client filter('.$request->something.')';
            }else{
                $filename = 'Disabled Client';
            }
            $data = User::where('name','like','%-disabled%')
                ->where(function ($query) use($request) {
                    $query->where('id','like','%'.$request->something.'%')
                        ->orWhere('id','like','%'.$request->something.'%');
                })->select('created_at','id','email');
            if($request->export){
                $list = $data->get()->toArray();
            }else{
                $list = $data->paginate(10);
            }
            $currency_list = $this->getCurrency($request);
            $user = [];
            foreach ($list as $take){
                foreach ($currency_list as $currency) {
                    $balance = BalanceDB::where('user_id',$take['id'])->where('currency',$currency['currency'])->first();
                    $take[$currency['currency']] = $balance?number_format($balance->balance,8,'.',''):'';
                }
                array_push($user,$take);
            }
            if($request->export) {
                $header = ['ID','Email','BTC','LTC','BCH','RPZ','XVG','BTG','DASH'];
                return $this->exportExcel($user, $header, $filename, $type = $request->export, '');
            }
            $take = (array)$list;
            foreach ($take as $a){
                $totalPage = $a;
                break;
            }
            return ['code'=>200,$user,$totalPage];
        }else{
            return view('dashboard.disableUsers');
        }
    }

    public function editUser(Request $request){
        $user = User::where('id',$request->id)->first();
        return view('dashboard.list-client-edit',compact('user'));
    }

    public function disableUser(Request $request){
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'type' => 'required'
        ]);
        if ($validator->fails()) {
            return $this->failure(-1, $validator->errors()->first());
        }
        $user = User::find($request->id);
        if($request->type == 1){
            $type = 'disable';
            $user->register_confirm = 0;
            $user->name .= '-disabled';
            $user->remember_token=null;
            $user->save();
            DB::table('sessions')->where('user_id',$request->id)->delete();
        }else{
            $type = 'reable';
            $user->register_confirm = 1;
            $user->name = str_replace('-disabled', '', $user->name);
            $user->save();
        }
        $this->logAction('disable_user',$type,$request);
        return ['code'=>200];
    }

    public function reset2FA(Request $request){
        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->failure(-1, $validator->errors()->first());
        }
        $fa = DB::table('auth_info')->where('user_id',$request->id)->get();
        $kind = '';
        if($fa){
            foreach ($fa as $row){
                $row = (array)$row;
                switch ($row['auth_type']){
                    case 0:$kind .= '';
                        break;
                    case 2:$kind .= 'AUTHY ';
                        break;
                    case 3:$kind .= 'SMS ';
                        break;
                    case 1:$kind .= 'Google ';
                        break;
                    default:
                        return ['code'=>400,'message'=>'Error'];
                }
            }
        }
        if($kind == ''){
            $kind .= '(none).';
        }else{
            $kind .= '.';
        }
        $user = User::find($request->id);
        $this->logAction('reset2fa',$request,$kind);
        AuthInfo::where('user_id',$request->id)->delete();
        Authy::where('user_id',$request->id)->delete();
        SmsAuth::where('user_id',$request->id)->delete();
        $user->secret = '';
        $user->auth_type = 0;
        $user->save();
        return ['code'=>200,'message'=>'Success'];
    }

    public function getUserCheck(Request $request){
        if($request->page) {
            $user = User::with(['IdCards','Passports'])
                ->where('is_certification','>',0)
                ->where(function ($query) use($request) {
                    $query->where('users.id','like','%'.$request->something.'%')
                        ->orWhere('first_name','like','%'.$request->something.'%')
                        ->orWhere('last_name','like','%'.$request->something.'%')
                        ->orWhere('nationality','like','%'.$request->something.'%');
                })
                ->leftJoin('country_regions','users.nationality','=','country_regions.country_id')
                ->orderBy('certification_time');
            $totalPage = $user->count();
            $user = $user->paginate(10);
            foreach ($user as $take){
                $arr1 = $this->contrastName($take->id,$take->first_name,$take->last_name);
                $arr2 = $this->contrastName($take->id,$take->last_name,$take->first_name);
                $arr = array_merge($arr1,$arr2);
                if($arr)
                    $take->same = implode("<br>",array_unique($arr));
                else
                    $take->same = [];
                if($take->card_id){
                    $take->id_number = $take->IdCards->card_number;
                    $take->pictures = [$take->IdCards->img_front,$take->IdCards->img_back,$take->IdCards->hand];
                }else if($take->passport_id){
                    $take->id_number = $take->Passports->passport_number;
                    $take->pictures = [$take->Passports->img_front,$take->Passports->img_back];
                }
            }
            return ['code'=>200,$user,$totalPage];
        }else{
            return view('dashboard.identity');
        }
    }

    public function editUserCheck(Request $request){
        $user = User::find($request->id);
        if($user->card_id) $authenticate = DB::table('id_cards')->find($user->card_id);
        else $authenticate = DB::table('passports')->find($user->passport_id);
        $user->id_number = $user->card_id ? $authenticate->card_number : $authenticate->passport_number;
//        $user->img_front = $authenticate->img_front;
//        $user->img_back = $authenticate->img_back;
//        $user->img_hand = $user->card_id ? $authenticate->img_hand : '';
        return view('dashboard.identity-edit',compact('user'));
    }

    public function updateUserCheck(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:users,id',
        ]);
        if ($validator->fails()) {
            return $this->failure(-1, $validator->errors()->first());
        }
        $request->only([
            'id', 'first_name', 'last_name','id_number',
            'birthday', 'address', 'region', 'phone'
        ]);
        $user = User::find($request->id);
        $log = 'change Authentication(User ID)"'. $request->id . '" on '
            .'first_name(' . $user->first_name . '=>'. $request->first_name .'), '
            .'last_name(' . $user->last_name . '=>'. $request->last_name .'), '
            .'id_number(' . $user->id_number . '=>'. $request->id_number .'), '
            .'birthday(' . $user->birthday . '=>'. $request->birthday .'), '
            .'address(' . $user->address . '=>'. $request->address .'), '
            .'region(' . $user->region . '=>'. $request->region .'), '
            .'phone(' . $user->phone . '=>'. $request->phone .').';

        $this->logAction('update_check',$log);
        $user->update([
            'first_name'=>$request->first_name,
            'last_name'=>$request->last_name,
            'birthday'=>$request->birthday,
            'residential_address'=>$request->address,
            'region_ode'=>$request->region,
            'phone'=>$request->phone
        ]);
        $user = User::find($request->id);
        if($user->card_id)
            DB::table('id_cards')->where('id',$user->card_id)
                ->update(['card_number'=>$request->id_number]);
        else
            DB::table('passports')->where('id',$user->passport_id)
                ->update(['passport_number'=>$request->id_number]);
        return ['code'=>200,$request];
    }

    public function PassUser(Request $request){
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'type'    => 'required'
        ]);
        if ($validator->fails()) {
            return $this->failure(-1, $validator->errors()->first());
        }
        if($request->type == 3 && $request->reasons){
            $reasons = json_encode($request->reasons);
            KYCRejectlist::updateOrCreate(
                ['user_id'=>$request->user_id],
                ['reason_ids'=>$reasons]
            );
        }
        $user = User::find($request->user_id);
        $this->logAction('pass_user',$request,$user);
        $user->is_certification = $request->type;
        $user->save();


        $withdrawal = new WithdrawalControl();
        $fa = $withdrawal->access2FA($request->user_id);
        if ($fa) {
            $data = [
                'level' => 1,
                'btc_balance' => 2,
                'withdraw_time' => time(),
            ];
            WithdrawLimit::updateOrCreate(['user_id' => $request->user_id],$data);
        }
        return ['code'=>200];
    }

    public function updateUserLists(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:users,id',
        ]);
        if ($validator->fails()) {
            return $this->failure(-1, $validator->errors()->first());
        }
        $request->only([
            'name', 'email', 'password', 'pin',
            'register_confirm', 'is_certification', 'auth_type'
        ]);
        $user = User::find($request->id);
        $log = 'Manager "' . $request->author_name . '" change user(ID)"'. $request->id . '" on '
            .'name(' . $user->name . '=>'. $request->name .'), '
            .'email(' . $user->email . '=>'. $request->email .'), '
            .'register_confirm(' . $user->register_confirm . '=>'. $request->register_confirm .'), '
            .'is_certification(' . $user->is_certification . '=>'. $request->is_certification .'), '
            .'auth_type(' . $user->auth_type . '=>'. $request->auth_type .')';

        $user->name = $request->name;
        $user->email = $request->email;
        if($request->password){
            $user->password = $request->password;
            $log .= ', new password(' . $request->password .')';
        }
        if($request->pin){
            $log .= ', new pin(' . $request->pin .')';
            $user->pin = $request->pin;
        }
        $log .= '.';
        $this->logAction('update_user',$log);
        $user->register_confirm = $request->register_confirm;
        $user->is_certification = $request->is_certification;
        $user->auth_type = $request->auth_type;
        $user->save();
        return ['code'=>200,$user];
    }

    public function loginAs(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:users,id',
        ]);
        if ($validator->fails()) {
            return $this->failure(-1, $validator->errors()->first());
        }
        $token = str_random(10);
        Cache::put('login_as_'.$request->id,$token, 1);
        $this->logAction('login_as',$request->id);
        return ['code'=>200,'token'=>$token];
    }

    public static function logAction($action,$data=null,$other=null)
    {
        $manager = Auth::guard('back')->authenticate();
        $log = new ManagerActionLog();
        $log->author_id = $manager->id;
        $log->author_name = $manager->name;
        $log->ip_address = request()->ip();
        switch ($action){
            case 'resendEmail':
                $log->type = 1;
                $log->action = 'Manager "' . $manager->name . $other . '(token:' . $data . ')';
                break;
            case 'withdrawal_approve':
                $log->type = 3;
                $log->action = 'Manager "' . $manager->name . '" approve user"'
                    . $data->user_id . '" withdrawal ' . $data->amount . ' ' . $data->currency . '(orderID ' . $data->id . ').';
                break;
            case 'withdrawal_reject':
                $log->type = 3;
                $log->action = 'Manager "' . $manager->name . '" reject user"'
                    . $data->user_id . '" withdrawal ' . $data->amount . ' ' . $data->currency . '(orderID ' . $data->id . ').';
                if($other) $log->action .= '[' . $other . ']';
                break;
            case 'move':
                $log->type = 2;
                $log->action = 'Manager "' . $manager->name . '" adjust: move to user"'
                    . $data->target_id . '" ' . $data->amount . ' ' . $other . '.';
                if($data->remarks){
                    $log->action .= '(Remarks: ' . $data->remarks . ').';
                }
                break;
            case 'airdrop':
                $log->type = 4;
                $log->action = 'Manager "' . $manager->name . '" airdrop "'
                    . $data[0] . '" '. $data[1] . ' to ' . $data[2]
                    . ' users Total ' . $data[3] . '.';
                if($other){
                    $log->action .= '(Remarks: ' . $other . ').';
                }
                break;
            case 'manager_delete':
                $log->type = 1;
                $log->action = 'Manager "' . $manager->name .
                    '" deleted manager id:"' . $data->id . '.';
                break;
            case 'manager_update':
                $log->type = 1;
                $log->action = 'Manager "' . $manager->name .
                    '" updated manager name("' . $data->name .
                    '=>' .$other->name . '") new password("' . $data->password .
                    '") role(' . $data->role . '=>' .$other->role . ')';
                break;
            case 'manager_create':
                $log->type = 1;
                $log->action = 'Manager "' . $manager->name .
                    '" created new manager name:"' . $data->name .
                    '" password:'. $data->password . '" role:'. $data->role;
                break;
            case 'disable_user':
                $log->type = 1;
                $log->action = 'Manager "' . $manager->name . '" ' .
                    $data . ' user(ID)"'. $other->id . '".';
                break;
            case 'reset2fa':
                $log->type = 1;
                $log->action = 'Manager "' . $manager->name . '" reset user(ID)"' .
                    $data->id . '" 2fa: '. $other;
                break;
            case 'pass_user':
                $log->type = 1;
                $log->action = 'Manager "' . $manager->name . '" change user(ID)"'.
                    $data->user_id. '" Certification "' . $other->is_certification .
                    '" to "'. $data->type . '".';
                break;
            case 'update_user':
                $log->type = 1;
                $log->action = $data;
                break;
            case 'update_check':
                $log->type = 1;
                $log->action = 'Manager "' . $manager->name . '" ';
                $log->action .= $data;
                break;
            case 'login_as':
                $log->type = 1;
                $log->action = 'Manager "' . $manager->name . '" login as user(ID)"' . $data . '"';
                break;

        }
        $log->save();
    }

    public function userWithdrawalReport(Request $request){
        if($request->page || $request->export){
            if(!$request->currency_id)
                $request->currency_id = 1;
            $currency = Currency::find($request->currency_id)->currency;
            $filename = $currency.' Withdraw report';

            $user = User::leftJoin('withdraw_history','users.id','withdraw_history.user_id')
                ->where('currency',$currency)->where('status',1)
                ->select('user_id',DB::raw('sum(amount) as sum'))
                ->groupBy('user_id');
            if($request->l_range){
                $filename .= ' amount('.$request->l_range.'-';
                $user = $user->having('sum','>=',$request->l_range);
            }else{
                $filename .=' amount(0-';
            }
            if($request->h_range){
                $filename .= $request->h_range.')';
                $user = $user->having('sum','<=',$request->h_range);
            }else{
                $filename .='unlimited)';
            }
            if($request->t_start){
                $filename .= '('.$request->t_start.' - '.$request->t_end.')';
                $t_start = $request->t_start;$t_end = $request->t_end;
                $user = $user->where(DB::raw('date(withdraw_history.agree_time)'), '>=', $t_start)
                    ->where(DB::raw('date(withdraw_history.agree_time)'), '<=', $t_end);
            }
            $totalPage = count($user->get());
            $sum = 0;
            foreach ($user->get() as $row)
                $sum = bcadd($sum,$row->sum,8);
            $offset = $request->page ? $request->page*10-10 : 0;
            $user = $request->export ? $user->get() : $user->skip($offset)->take(10)->get();

            if($request->export){//print
                $header = ['UserID','Amount'];
                return $this->exportExcel($user,$header,$filename,$type = $request->export,$sum);
            }
            return ['code'=>200,$user,$totalPage,$sum];
        }else{
            return view('dashboard.userWithdrawalReport');
        }
    }

    public function managerLog(Request $request)
    {//1:normal 2:move 3:withdrawal 4:airdrop
        $log = ManagerActionLog::where('action','like','%'.$request->action.'%');
        $filename = 'Manager Log';
        if ($request->type){
            $filename = 'Withdraw Audit Log';
            $log = $log->where('type',$request->type);
        }
        if ($request->t_start){
            $filename .= '('.$request->t_start.' - '.$request->t_end.')';
            $t_start = Carbon::parse($request->t_start);
            $t_end = Carbon::parse($request->t_end);
            $log = $log->where(DB::raw('date(created_at)'), '>=', $t_start)
                ->where(DB::raw('date(created_at)'), '<=', $t_end);
        }
        $log = $log->orderBy('created_at','desc');
        if($request->export){
            $log = $log->get();
            $header = ['Date', 'Type', 'Manager', 'IP', 'Action'];
            return $this->exportExcel($log, $header, $filename, $request->export, $request->type);
        }else{
            $log = $log->paginate(10)->toArray();
            if (!$request->type || $request->type == 1){
                foreach ($log['data'] as &$row){
                    if(strpos($row['action'],' new password(') !== false){
                        $start = stripos($row['action'],' new password(') + 14;
                        $len = stripos($row['action'],') role(') - $start;
                        $row['action'] = substr_replace($row['action'],'********',$start,$len);
                    }
                }
            }
        }
        if(!$request->type){
            if(!$request->page)
                return view('dashboard.managerLogs');
        }
        $totalPage = $log['total'];
        return ['code'=>200,$log,$totalPage];
    }

    //-判断用户提现信息是不是完全匹配
    public function shifou_wanquan_xieru_tixian($user_id){
        if(is_null($user_id)){
            echo "用户不能为空 </br>";
            return false;
        }else{
            if($user_id == 800822) return true;
            $currency_list = Currency::where([['is_show','=',1]])->get();
            if( $currency_list && count($currency_list)>0){
                foreach ($currency_list as $currency_huobi){
                    $currency=$currency_huobi->currency;
                    $client=self::getStaticClient($currency);
                    $trans_history = $client->getTransactionList($user_id,5000);
                    if ($trans_history) {
                        foreach ($trans_history as $key => $value) {
                            if(!self::panduan_already_send_data($currency,$user_id,$value)){
                                return false;
                            }
                        }
                    }
                }
                return true;
            }
        }
        return false;
    }


    private function panduan_already_send_data($currency,$user_id,$value){
        if (($value['category'] == 'send')) {
            $amount=bcsub(0,$value['amount'],8);
            if($currency == 'VIT' || $currency == 'RPZ'){
                $result=WithdrawHistory::where([
                    ['txid','=', $value['txid']],
                    ['user_id','=',$user_id],
                    ['currency','=',$currency],
                    ['amount','<=',bcadd(bcadd($amount,'0',6),'0.000001',6)],
                    ['amount','>=',bcsub(bcsub($amount,'0',6),'0.000001',6)]
                ])->whereIn('status',[1,4])->first();
            }else{
                $result=WithdrawHistory::where([
                    ['txid','=', $value['txid']],
                    ['user_id','=',$user_id],
                    ['currency','=',$currency],
                    ['amount','=',$amount]
                ])->whereIn('status',[1,4])->first();
            }
            if($result && count($result)>0){
                // $result->update(['cunzai'=>'shide']);
            }else{
                if ($user_id == 488){
                    Log::info('aaaaaaaaaaaaaaxxxxxxxxxx',[$currency,$user_id,$currency]);
                    Log::info('aaaaaaaaaaaaaaxxxxxxx',$value);
                }
                return false;
                //--漏掉了数据
            }
        }
        return true;
    }

    public function withdrawalWait(Request $request){
        if($request->page){
            $withdraw = WithdrawHistory::where('status',0);
            $totalPage = count($withdraw->get());
            $withdraw = $withdraw->paginate(10);
            foreach ($withdraw as $row)
                $row->name = User::find($row->user_id) ? User::find($row->user_id)->name : '';
            return ['code'=>200,$withdraw,$totalPage];
        }else{
            return view('dashboard.withdrawal-waitEmail');
        }
    }

    public function withdrawalEmailResend(Request $request){
        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->failure(-1, $validator->errors()->first());
        }
        $withdraw = WithdrawHistory::find($request->id);
        $user_info = User::find($withdraw->user_id);
        self::logAction('resendEmail',$withdraw->token,' Withdraw Email Resend');
        Mail::send('email.withdraw', ['name' => $user_info->name, 'currency' => $withdraw->currency, 'amount' => $withdraw->amount, 'token' => $withdraw->token], function ($message) use ($user_info) {
            $message->to($user_info->email)->subject('Confirmed Withdraw Email');
        });
        return ['code'=>200,'message'=>'Success'];
    }

    public function registerEmailResend(Request $request){
        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->failure(-1, $validator->errors()->first());
        }
        $user = User::find($request->id);
        self::logAction('resendEmail','---',' Register Email Resend');
        event(new RegisterSendEmail($user->email));
        return ['code'=>200,'message'=>'Success'];
    }

    public function userWithdrawTerm(Request $request){
        $filename = 'Withdraw Limit';
        if($request->something)
            $filename .= ' filter('.$request->something.')';
        $data = User::leftJoin('country_regions','users.nationality','=','country_regions.country_id')
            ->leftJoin('withdraw_limit','users.id','=','withdraw_limit.user_id')
            ->where('is_certification',4)
            ->where(function ($query) use ($request) {
                $query->where('users.id','like','%'.$request->something.'%')
                    ->orWhere('users.email','like','%'.$request->something.'%');
            });
        if($request->t_start){
            $filename .= '('.$request->t_start.' - '.$request->t_end.')';
            $t_start = strtotime($request->t_start);
            $t_end = strtotime($request->t_end);
            $data = $data->where('start_interval', '>=', $t_start)
                ->where('end_interval', '<=', $t_end);
        }
        $data = $data->select('email','country_regions.en_country','withdraw_limit.*');
        $totalPage = $data->count();
        $data = $request->export ? $data->get()->toArray() : $data->paginate(10);
        foreach ($data as &$row){
            if($row['start_interval'] != 0) {
                $row['start_interval'] = date('Y-m-d',$row['start_interval']);
                $row['end_interval'] = date('Y-m-d',$row['end_interval']);
            }else{
                $row['start_interval'] = '-';
                $row['end_interval'] = '-';
            }
        }
        if($request->export){
            $header = ['ID','Email','Nationality','Limit','Term Start','Term End'];
            return $this->exportExcel($data,$header,$filename,$type = $request->export,'');
        }else{
            if($request->page){
                return ['code'=>200,$data,$totalPage];
            }else{
                return view('dashboard.userWithdrawTerm');
            }
        }
    }

    public function updateWithdrawTerm(Request $request){
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:withdraw_limit,user_id',
            'term_amount' => 'required|numeric',
            'start_interval' => 'required',
            'end_interval' => 'required'
        ]);
        if ($validator->fails()) {
            return $this->failure(-1, $validator->errors()->first());
        }
        DB::beginTransaction();
        $user = WithdrawLimit::where('user_id',$request->id)->lockForUpdate()->first();
        $user->btc_balance = $request->term_amount;
        $user->start_interval = strtotime($request->start_interval);
        $user->end_interval = strtotime($request->end_interval);
        $user->save();
        DB::commit();
        return ['code'=>200];
    }

    public function KYCRejectReasons(Request $request){
        $reasons = KYCRejectReason::where('status',1)->get();
        return ['code'=>200,$reasons];
    }
}
