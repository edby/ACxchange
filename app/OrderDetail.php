<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    protected $guarded = [];
    public $timestamps = false;

    //交易方式
    const BUY = 10;
    const SELL = 20;
    public static $tradeType = [
        self::BUY => 'buy',
        self::SELL => 'sell',
    ];

    //交易 状态
    const FRONT = 10;
    const IN = 20;
    const AFTER = 30;
    public static $orderStatus = [
        self::FRONT => 'front',
        self::IN => 'in',
        self::AFTER => 'after',
    ];

    //交易 开关
    const ON = 10;
    const OFF = 20;
    public static $operation = [
        self::ON => 'on',
        self::OFF => 'off',
    ];

    /**
     * 获得此订单货币细节
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function currencySet()
    {
        return $this->belongsTo('App\CurrencySet','curr_id','curr_id');
    }

    /**
     * 获得此订单表获取订单细节
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function order()
    {
        return $this->belongsTo('App\Order','order_id','id');
    }

    /**
     * 获得此订单表获取订单细节
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\User','user_id','id');
    }

    /**
     * 获取合适的订单  撮合
     * @param $type
     * @param $tradeCurr
     * @param $diff
     * @param $order
     * @return mixed
     */
    public function appDetail($type,$tradeCurr,$diff,$order)
    {
        return $this->where([
            ['trade_type', '=',$type],
            ['curr_abb', '=', $tradeCurr],
            $diff,
            ['residual_num','>',0],
        ])->orderBy('price_btc', $order)
            ->orderBy('add_time', 'asc')
            ->get(['curr_abb','user_id','net_volume','net_volume_btc','end_amount','end_btc','rate','residual_volume','id','initial_volume','residual_num','trade_type','initial_mun','price_btc']);
    }


     public function ordInTradMoney($currAbb,$userId)
    {
        if ('btc'===$currAbb){
            $total = $this->where([
                ['trade_type','=', 10],
//                ['curr_abb','=',$currAbb],
                ['operation', '=', 10],
                ['order_status', '<>' ,30],
                ['user_id','=',$userId]
            ])->sum('net_volume_btc');

            $initial = $this->where([
                ['trade_type','=' ,10],
//                ['curr_abb','=',$currAbb],
                ['operation', '=', 10],
                ['order_status', '<>' ,30],
                ['user_id','=',$userId]
            ])->sum('initial_volume');
            return bcsub($total,$initial,8);
        }else{
            return $this->where([
                ['trade_type','=', 20],
                ['curr_abb','=',$currAbb],
                ['operation', '=', 10],
                ['order_status', '<>' ,30],
                ['user_id','=',$userId]
            ])->sum('residual_num');
        }
    }
}
