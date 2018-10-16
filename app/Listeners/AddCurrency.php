<?php
/**
 * Created by PhpStorm.
 * User: ZRothschild
 * Date: 2018/5/3
 * Time: 17:17
 */

namespace App\Listeners;


use App\CurrencySet;
use App\Events\AddCurr;
use App\Http\Controllers\Controller;

use App\UserCurr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class AddCurrency
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     * @param  AddCurr  $event
     * @return void
     */
    public function handle(AddCurr $event)
    {
        $allCurr = CurrencySet::where([
            ['switch_on','=',10],
        ])->get();

        $dex=0;
        $user_fee_data = [];
        foreach ($allCurr as $curr){
            $currAbb = UserCurr::where([
                ['curr_id','=',$curr->curr_id],
                ['user_id','=',$event->user->id],
            ])->first();

            if ($currAbb) continue;

            try{
                $data['curr_id'] = $curr->curr_id;
                $data['user_id'] =  $event->user->id;
                $data['curr_name'] =  $curr->curr_name;
                $data['curr_abb'] =  $curr->curr_abb;

                if (PHP_OS === 'WINNT'){
                    $fileName = 'qrcodes/'.$event->user->id.$curr->curr_abb.'.png';
                }else{
                    $fileName = base_path().'/public/qrcodes/'.$event->user->id.$curr->curr_abb.'.png';
                }

                $client=Controller::getStaticClient(strtoupper($curr->curr_abb));
                $data['address']=$client->getNewAddress($event->user->id);

                QrCode::format('png')->size(220)->generate($data['address'],$fileName);
                UserCurr::create($data);


                $market=DB::table("market")->get();

                $market_fees=[];
                if($market){
                    foreach ($market as $market1){
                        $market_name=$market1->market_name;
                        $market_fee=$market1->fee;
                        $market_fees[$market_name]=$market_fee;
                    }
                }

                if($curr->curr_abb !="btc"){
                    $user_fee_data[$dex]['user_id']=$event->user->id;
                    $user_fee_data[$dex]['trade_curr']= $curr->curr_abb;;
                    $user_fee_data[$dex]['currency']="btc";
                    $temp_key=$user_fee_data[$dex]['trade_curr']."_".$user_fee_data[$dex]['currency'];
                    // dump($temp_key);
                    $user_fee_data[$dex]['fee_rate']=$market_fees[$temp_key];
                    $dex++;
                }
            }catch (\Exception $exception){
                $res[$curr->curr_abb] = $exception->getCode();
            }
        }
        DB::table("user_fee")->insert($user_fee_data);
    }
}