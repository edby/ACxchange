<?php
/**
 * Created by PhpStorm.
 * User=>ZRothschild
 * Date=>2018/1/12
 * Time=>17=>45
 */

namespace App\Http\Controllers\FrontEnd;

use App;
use App\Chart;
use App\CurrencySet;
use App\Http\Controllers\Controller;
use App\KLine;
use App\Library\Currency\AccountAmount;
use App\OrderDetail;
use App\Tool\TimeCalc;
use App\UserCurr;
use App\Models\Market;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TradeController extends Controller
{
    /**
     *交易中心
     */
    public function index()
    {
        return view('front.trade');
    }


    public function tradeTran(Request $request)
    {
        $type = $request->type;
        if (1 == $type){
            return response()->json(['lo'=>__('ac.Buy'),'upp'=>__('ac.buying'),'payment'=>__('ac.payment')]);
        }else{
            return response()->json(['lo'=>__('ac.Sell'),'upp'=>__('ac.selling'),'payment'=>__('ac.Total')]);
        }
    }

    public function chartMap(Request $request)
    {
        $tmp = [
            'c6M'=>0,
            'c2M' =>11,
            'c1M' =>15,
            'c2W' =>17,
            'c1W' =>21,
            'c2D' =>12,
            'c1D' =>30,
            'c6H' =>23,
            'c2H' =>45,
            'c1H' =>50,
            'c30m' =>60,
            'c15m' =>70,
        ];
        $data = Chart::offset($tmp[$request->type])->limit(16)->orderBy('created_at','asc')->get(['open','low','high','close','average','volume','created_at as datum_time']);
        return response()->json(['status'=>1,'data'=>$data]);
    }

    /**My open orders 展示
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function myOpenOrder(Request $request)
    {
        $data = OrderDetail::where([
            ['user_id','=',Auth::id()],
            ['operation','=',10],
            ['curr_abb' ,'=',$request->tradeCurr],
            ['order_status','<>',30],
        ])->orderByDesc('add_time')->paginate(10,['volume_btc','id','net_volume','residual_num','price_btc','trade_type','operation','add_time'])->toArray();
        $data['lastPage'] = $data['last_page'];
        return response()->json(['status'=>1,'message' => 'successful','data'=>$data]);
    }


    /**
     * Sell Buy orders 展示
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function tradeOrder(Request $request)
    {
        $currAbb = empty($request->has('curr_abb'))?'btc':$request->curr_abb;
        if ($request->tradeType ==20){
            $orderBy = 'asc';
        }elseif ($request->tradeType ==10){
            $orderBy = 'desc';
        }else{
            return response()->json(['status'=>0,'message' => 'fail']);
        }

        $data = DB::table('orders')
            ->select(DB::raw('price_btc,residual_num,truncate(price_btc*residual_num,8) as value'))
            ->where([
                ['trade_type','=',$request->tradeType],
                ['order_status','<>',30],
                ['curr_abb','=',$currAbb],
            ])->orderBy('price_btc',$orderBy)
            ->paginate(10)->toArray();

        $total = DB::table('orders')->where([
            ['trade_type','=',$request->tradeType],
            ['order_status','<>',30],
            ['curr_abb','=',$currAbb],
        ])->sum('residual_volume');


        $data['currAbb'] = $currAbb;
        $data['totalNum'] = $total;
        $data['lastPage'] = $data['last_page'];
        return response()->json(['status'=>1,'message' => 'successful','data'=>$data]);
    }

    /**
     * Market history 展示
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function marketHistory(Request $request)
    {
        $currAbb = empty($request->has('curr_abb'))?'btc':$request->curr_abb;
        $data = DB::table('order_details')
            ->select(DB::raw('price_btc,initial_mun,truncate(price_btc*initial_mun,8) as value,add_time,trade_type'))
            ->where([
                ['order_status','=',30],
                ['curr_abb','=',$currAbb],
            ])->orwhere([
                ['operation','=',20],
            ])
            ->orderBy('add_time','desc')
            ->paginate(10)->toArray();

        $data['currAbb'] = $currAbb;
        $data['lastPage'] = $data['last_page'];
        return response()->json(['status'=>1,'message' => 'successful','data'=>$data]);
    }


    /**
     * 获取该币种的地址 二维码
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deposit(Request $request)
    {
        $userId = Auth::id();
        $currAbb = $request->currAbb;

        $accountAmount = new AccountAmount();

        $currName = CurrencySet::where([
            ['switch_on','=',10],
            ['curr_abb','=',$currAbb],
        ])->value('curr_name');


        $balance = $accountAmount->getBalance($currAbb,$userId);
        if (PHP_OS != 'WINNT'){
            $data['balance'] = empty($balance)? "0.00000000":$balance;
            $data['address'] = $accountAmount->getAddress($currAbb,$userId);
            $data['qcode'] = asset('qrcodes/'.$userId.$request->currAbb.'.png');
            $data['currName'] = empty($currName)?'':$currName;
            $data['currAbb'] = empty($currName)?'':$request->currAbb;
        }else{
            $data['balance'] = '0.00000000';
            $data['address'] = 'Lc7PPmKphWgAFXia6mmRXiYsFioWi63QmY';
            $data['qcode'] = 'img';
            $data['currName'] = empty($currName)?'BTC23':$currName;
            $data['currAbb'] = empty($currName)?'BTC':$request->currAbb;
        }
        return response()->json(['status'=>1,'message' => 'successful','data'=>$data]);
    }

    /**
     * 获取用户已开通币的名称
     * @return \Illuminate\Http\JsonResponse
     */
    public function currList()
    {
        $data = UserCurr::where('user_id',Auth::id())->get(['curr_abb']);
        return response()->json(['status'=>1,'message' => 'successful','data'=>$data]);

    }

    /**
     * 获取货币货币  24 小时的 最低价格 最高价格 上升幅度
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function tranSummary(Request $request)
    {
        try{
            $day = 60*60*24;
            $endTime = time()-$day;
            //获取最后价格
            $currency_name=$request->currency;
            $marketName = $request->currAbb.'_'.$currency_name;
            $lastPrice = Market::where('market_name',$marketName)->value('last_price');

            $curr_name = CurrencySet::where('curr_abb',$request->currAbb)->value('curr_name');

            $low = DB::table('xchange_info')->where([
                ['market_name','=',$marketName],
                ['created_at','>=',$endTime],
            ])->min('last_price');


            $high = DB::table('xchange_info')->where([
                ['market_name','=',$marketName],
                ['created_at','>=',$endTime],
            ])->max('last_price');

            $volume = DB::table('xchange_info')->where([
                ['market_name','=',$marketName],
                ['created_at','>=',$endTime],
            ])->sum('volume');
            if (empty($volume)) $volume = "0.00000000";

            $open= DB::table("xchange_info")->where([
                ['market_name','=',$marketName],
                ['created_at','>=',$endTime]
            ])->first(['last_price']);

            if (empty($open)){
                $comp = 1;
                $change = '0%';
            } else{
                $open=$open->last_price;
                $diff = bcsub($lastPrice,$open,8);
                $comp = bccomp($lastPrice,$open,8);
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

            $feeRate = UserCurr::where([
                ['curr_abb','=',$request->currAbb],
                ['switch','=',10],
                ['user_id','=',Auth::id()],
            ])->value('fee_rate');

            if($lastPrice>$high)$high=$lastPrice;
            if(empty($low))$low=$lastPrice;
            $data = [
                'currAbb' => $request->currAbb,
                'currName' => $curr_name,
                'lastPrice' => $lastPrice,
                'low' => $low,
                'change' => $change,
                'volume' => $volume,
                'high' => $high,
                'feeRate' => $feeRate,
                'status'=> $comp
            ];
        }catch (\Exception $exception){
            $data = $exception->getMessage();
        }

        return response()->json(['status'=>1,'message' => 'successful','data'=>$data]);
    }

    /**
     * K线图数据接口
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function charts(Request $request)
    {
        //$time = time()-KLine::BENCH[$request->datumType];
        $time = time()-KLine::BENCH_GUPIAO[$request->datumType];
        $where = [
            ['datum_type','=',$request->datumType],
            ['add_time','>',$time],
            ['curr_abb','=',$request->currAbb],
        ];
       
//        Log::info($where);
        $data = KLine::where($where)->orderBy('add_time')->limit(30)->get()->toArray();

        if (empty($data)){
            $data = 0;
        }else{
            foreach ($data as $key => $value){
                $data[$key]['add_time'] = date("Y-m-d H:i:s",$value['add_time']);
            }
        }
        return response()->json(['datumType'=>$request->datumType,'currAbb'=>$request->currAbb,'status'=>1,'message' => 'successful','data'=>$data]);
    }


    /**
     * 获取请求金额类型
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function price(Request $request)
    {
        $type = strtolower($request->type);

        if ('last'==$type){
            $res= Market::where('market_name',$request->currAbb.'_btc')->value('last_price');
            return response()->json(['status'=>1,'message' => 'successful','data'=>['price'=>$res]]);
        }elseif ('ask'==$type){
            $res= App\Models\Xchange::where([
                ['status','=',0],
                ['market_name','=',$request->currAbb."_btc"],
                ['type','=',1],
            ])->orderBy('price')->first(['price']);

            if (empty($res)){
                $res = '0.00000000';
            }else{
                $res = $res['price'];
            }
            return response()->json(['status'=>1,'message' => 'successful','data'=>['price'=>$res]]);
        }elseif ('bid'==$type){
            $res= App\Models\Xchange::where([
                ['status','=',0],
                ['market_name','=',$request->currAbb."_btc"],
                ['type','=',2],
            ])->orderBy('price','desc')->first(['price']);
            if (empty($res)){
                $res = '0.00000000';
            }else{
                $res = $res['price'];
            }
            return response()->json(['status'=>1,'message' => 'successful','data'=>['price'=>$res]]);
        }else{
            return response()->json(['status'=>0,'message' => 'fail']);
        }
    }
}
