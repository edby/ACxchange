<?php

namespace App\Http\Controllers\Admin;

use App\AuthInfo;
use App\Authy;
use App\Events\RegisterSendEmail;
use App\Http\Controllers\FrontEnd\BlockchainController;
use App\IdCard;
use App\Jobs\SendEmail;
use App\Models\BalanceDB;
use App\Models\DepositHistory;
use App\Models\IgnoredUser;
use App\Passport;
use App\SmsAuth;
use App\Tool\WithdrawalControl;
use App\User;
use App\WithdrawHistory;
use App\WithdrawHistoryNode;
use App\Manager;
use App\WithdrawLimit;
use Illuminate\Http\Request;
use App\ManagerActionLog;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
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

class AuaTestController extends Controller
{

    public function script(Request $request){
        $user = User::where('is_certification','>',0)->select('id','card_id','passport_id')->get();
        foreach ($user as $row){
            try{
                if($row->card_id) $url = IdCard::find($row->card_id)->img_front;
                else $url = Passport::find($row->passport_id)->img_front;
            }catch (Exception $e){
                User::where('id',$row->id)->update(['certification_time'=>'2000-02-02 00:00:00']);
                dump('user error: '.$row->id);
                continue;
            }
            if(substr($url, 0,1) == '/') $url = substr($url, 1);
            $url = str_replace('public','',$url);
            try{$time = filemtime($url);}catch (Exception $e){ $time = null; }
            if($time){
                $time = date('Y-m-d H:i:s',$time);
                User::where('id',$row->id)->update(['certification_time'=>$time]);
            }
            else{
                dump('not find: '.$url);
                continue;
            }
        }
        return 'ok';
    }


}
