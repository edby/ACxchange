<?php

namespace App\Http\Controllers\FrontEnd;

use App\Http\Controllers\Controller;
use App\Models\Currency;

use App\Models\Market;
use App\Models\Xchange;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
//TODO 合并到app/Api
class ApiController extends Controller
{
    public function returnTicker(Request $request)
    {
        /*
        $data = [];
        $market = Market::where('is_show',1)->select('id','market_name','last_price')->get();
        foreach ($market as $key=>$value) {
            $data[$value->market_name]['id']            = (string)$value->id;
            $data[$value->market_name]['last_price']    = $value->last_price;
            $data[$value->market_name]['lowestAsk']     = $this->getlowestAsk($value->id);
            $data[$value->market_name]['highestBid']    = $this->gethighestBid($value->id);
            $_24h                                       = $this->get24Info($value->id);
            $data[$value->market_name]['percentChange'] = (string)number_format($_24h['percentChange'],8,'.','');
            $data[$value->market_name]['baseVolume']    = (string)number_format(round($_24h['baseVolume'],8),8,'.','');
            $data[$value->market_name]['quoteVolume']   = (string)number_format(round($_24h['quoteVolume'],8),8,'.','');
            $data[$value->market_name]['isFrozen']      = "0";
            $data[$value->market_name]['high24hr']      = (string)$_24h['high24hr'];
            $data[$value->market_name]['low24hr']       = (string)$_24h['low24hr'];
        }
        return $data;
        */
        if(session('applocale') == 'en'){
            return view('front.API-en');
        }else if(session('applocale') == 'zh_cn'){
            return view('front.API-ch');
        }else if(session('applocale') == 'zh_tw'){
            return view('front.API-ch');
        }else{
            return view('front.API-en');
        }
    }

    public function get24hInfo(Request $request)
    {
        $this->validate($request,['market_id'=>'required']);
        //$data = $this->get24Info($request->market_id);
        $exchange_info = Xchange::where('status',1)->where('market_id',$request->market_id)->whereRaw('updated_at > DATE_SUB(NOW(),INTERVAL 24 HOUR)')->get();
        if($exchange_info && (count($exchange_info) > 0)) {
            $first = $exchange_info->first()->price;
            $last  = $exchange_info->last()->price;
            $diff_pirce = $last-$first;
            $data['_24h_change'] = number_format(round($diff_pirce/$first*100,3),3,'.','');
            $exchange_info = $exchange_info->groupBy('type');
            $data['_24h_volume'] = number_format(round($exchange_info[1]->sum('volume'),8),8,'.','');
        }else{
            $data['_24h_change'] = $data['_24h_volume'] = 0;
        }

        return ['code'=>200,'result'=>$data];
    }

    private function getlowestAsk($market_id)
    {
        return Xchange::where('market_id',$market_id)->where('type',1)->where('status',0)->orderBy('price','asc')->limit(1)->value('price');
    }

    private function gethighestBid($market_id)
    {
        return Xchange::where('market_id',$market_id)->where('type',2)->where('status',0)->orderBy('price','desc')->limit(1)->value('price');
    }


    private function get24Info($market_id)
    {
        $exchange_info = Xchange::where('status',1)->where('market_id',$market_id)->whereRaw('updated_at > DATE_SUB(NOW(),INTERVAL 24 HOUR)')->get();
        if($exchange_info && (count($exchange_info) > 0)) {
            $first = $exchange_info->first()->price;
            $last = $exchange_info->last()->price;
            $diff_pirce = $last - $first;
            $data['percentChange'] = round($diff_pirce / $first, 8);
            $data['low24hr'] = $exchange_info->min('price');
            $data['high24hr'] = $exchange_info->max('price');
            $exchange_info = $exchange_info->groupBy('type');
            $data['baseVolume'] = $exchange_info[2]->sum('total_price');
            $data['quoteVolume'] = $exchange_info[1]->sum('volume');
        } else {
            $data['percentChange'] = 0.0;
            $data['low24hr'] = 0.0;
            $data['high24hr'] = 0.0;
            $data['baseVolume'] = 0.0;
            $data['quoteVolume'] = 0.0;
        }
        return $data;
    }

    public function getCurrencyInfo(Request $request)
    {
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
        $data = [];
        foreach ($currency_list as $k=>$currency) {
            $client = $this->getClient($currency['currency']);
            $balance = $client->getBalance('NovaCoinMain');
            $slave = $client->getSlaveBalance('slave');
            $total = $client->getInfo();
//            $test = $client->getTransactionList('slave');
            $data[$currency['currency']]['balance'] = number_format($balance,8,'.','');
            $data[$currency['currency']]['slave'] = number_format($slave,8,'.','');
            $data[$currency['currency']]['total'] = number_format($total,8,'.','');
//            $data[$currency['currency']]['test'] = $test;
            if($currency['currency'] != 'BTC') {
                $data[$currency['currency']]['btc_balance'] = number_format($currency['last_price']*$balance,8,'.','');
                $data[$currency['currency']]['btc_slave'] = number_format($currency['last_price']*$slave,8,'.','');
                $data[$currency['currency']]['btc_total'] = number_format($currency['last_price']*$total,8,'.','');
            }
        }
        return $this->success(0,'', $data);
    }
}
