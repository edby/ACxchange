<?php
/**
 * Created by PhpStorm.
 * User: ZRothschild
 * Date: 2018/5/4
 * Time: 9:18
 */

namespace App\Http\Controllers\FrontEnd;


use App\Http\Controllers\Controller;
use App\Models\Currency;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Models\Market;
use App\Tool\GrazeRPC;
use App\User;
use App\UserCurr;

class ToolControllers extends Controller
{
    /**
     * 为用户生成所有的未生成币种
     */
    public function currGen()
    {
        $currAll = Market::where([
            ['is_show','=',1],
        ])->get();

        $userAll = User::all();
        
        foreach ($currAll as $key =>$value){

            $tmpCurr = explode('_',$value->market_name);
            foreach ($userAll as $ke => $val){
                
                $oneCurr = UserCurr::where([
                    ['user_id','=',$val->id],
                    ['curr_abb','=',$tmpCurr[0]],
                ])->fisrt();

                if ($oneCurr) continue;

                DB::beginTransaction();
                $currencyName = Currency::find($value->from_currency);

                try{
                    $data['curr_id'] = $value->from_currency;
                    $data['user_id'] =  $val->id;
                    $data['curr_name'] =  $currencyName->full_currency;
                    $data['curr_abb'] =  $tmpCurr[0];

                    if (PHP_OS === 'WINNT'){
                        $fileName = 'qrcodes/'.$val->id.$tmpCurr[0].'.png';
                    }else{
                        $fileName = base_path().'/public/qrcodes/'.$val->id.$tmpCurr[0].'.png';
                    }
                    $data['address'] = GrazeRPC::getInstance($tmpCurr[0])->sendRequest('getnewaddress',['ac'.$tmpCurr[0].$val->id])->getRpcResult();
                    QrCode::format('png')->size(220)->generate($data['address'],$fileName);
                    UserCurr::create($data);
                    DB::commit();
                }catch (\Exception $exception){
                    DB::rollBack();
                    $msg = $exception->getMessage();
                    Log::info('errorerrorerrorerror');
                    Log::info($msg);
                }
            }
        }
    }


    /**
     * 为所用用户生成 对用市场
     */
    public function marketGen()
    {
        $allUserCurr = UserCurr::all();

        $dex =1;
        foreach ($allUserCurr as $key => $value){
            $market=DB::table("market")->get();

            $market_fees=[];
            if($market){
                foreach ($market as $market1){
                    $market_name=$market1->market_name;
                    $market_fee=$market1->fee;
                    $market_fees[$market_name]=$market_fee;
                }
            }
            if($value->curr_abb !="btc"){
                $user_fee_data[$dex]['user_id']=$value->user->id;
                $user_fee_data[$dex]['trade_curr']= $value->curr_abb;;
                $user_fee_data[$dex]['currency']="btc";
                $temp_key=$user_fee_data[$dex]['trade_curr']."_".$user_fee_data[$dex]['currency'];
                // dump($temp_key);
                $user_fee_data[$dex]['fee_rate']=$market_fees[$temp_key];
                $dex++;
            }
        }
        DB::table("user_fee")->insert($user_fee_data);
    }
}