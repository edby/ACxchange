<?php
/**
 * Created by PhpStorm.
 * User: DELL
 * Date: 2018/4/24
 * Time: 21:24
 */


namespace App\Http\Controllers\FrontEnd;
use App\Handlers\Client;
use App\Http\Controllers\Controller;
use App\Models\Currency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/*转出控制器
*/
class WithdrawController extends Controller
{

    //--返回 fee和 可用余额
    public function  getBlance(Request $request){
        $curr_abb=$request->curr_abb;
        $curr_abb="btc";
        $client=$this->getClient($curr_abb);
        $balance=$client->getBalance("ac".$curr_abb.Auth::id());
        dump($balance);
        $currency = Currency::where('currency', strtoupper($curr_abb))->select('withdraw_fee', 'id')->first();
        dump($currency->withdraw_fee);

    }



}