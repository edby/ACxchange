<?php
/**
 * Created by PhpStorm.
 * User: ZRothschild
 * Date: 2018/1/12
 * Time: 11:21
 */

namespace App\Http\Controllers\FrontEnd;

use App\CurrencySet;
use App\Http\Controllers\Controller;
use App\Models\Currency;
use App\Models\Market;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class IndexController extends Controller
{
    /**
     * 登陆注册 密码邮箱重置
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $env = asset('images');
        $data = ['restEmail'=>$request->type,'env'=>$env];
        $data['market'] = Market::where([
            ['is_show','=',1],
            ['market_name','like',"%_btc"],
        ])->get(['last_price','arrow','market_name','from_currency'])->toArray();

        $day = 60*60*24;
        $endTime = time()-$day;

        foreach ( $data['market'] as  $key => $value){
            $mark_name_temp=explode("_",$value['market_name']);
            $data['market'][$key]['curr_abb'] = strtoupper($mark_name_temp[0]);//
            $data['market'][$key]['curr'] = Currency::where('id',$value['from_currency'])->value('full_currency');
//            $data['market'][$key]['arrow'] = $value['arrow']=='↑'?'up':'down';

            $open= DB::table("xchange_info")->where([
                ['market_name','=',$value['market_name']],
                ['created_at','>=',$endTime],
            ])->first(['last_price']);
            if (empty($open)){
                $comp = "up";
                $change = '0%';
            } else{
                $open=$open->last_price;
                $diff = bcsub($value['last_price'],$open,8);
                $comp = bccomp($value['last_price'],$open,8);
                if ($comp >= 0){
                    $comp = "up";
                }else{
                    $comp = "down";
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
            $data['market'][$key]['flag'] = $comp;
            $data['market'][$key]['percent_change_24h'] = $change;
        }
        return view('front.index',$data);
    }

    /**
     * 退出登陆
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function logOut()
    {
        Auth::logout();
        return redirect('/');
    }

    public function getJson()
    {
        $langType = App::getLocale();
        $jsonPath = resource_path('lang/'.$langType.'/test.json');
        $jsonData = file_get_contents($jsonPath);
        $arrayData = json_decode($jsonData,true);
        $arrayData['last'] = __('ac.Last');
        $arrayData['Welcome'] = __('ac.Welcome');
        if (!empty(Auth::user())){
            $arrayData['userName'] = Auth::user()->name;
        }
        $arrayData['GoBackHome'] = __('ac.GoBackHome');
        $arrayData['ChoiceHave'] = __('ac.ChoiceHave');
        $arrayData['totalMustBe'] = __('ac.totalMustBe');
        $arrayData['languageChoice'] = __('ac.languageChoice');
        $arrayData['Success'] = __('ac.Success');
        return response()->json(['status'=>1,'message' => 'successful','data'=>$arrayData]);
    }
}