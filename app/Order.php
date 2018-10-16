<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $guarded = [];
    public $timestamps = false;

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

    /**
     * 获得此订单汇率货币。
     */
    public function currencySet()
    {
        return $this->belongsTo('App\CurrencySet','curr_id','curr_id');
    }

    /**
     * 获得此订单表获取订单细节。
     */
    public function orderDetail()
    {
        return $this->hasMany('App\OrderDetail','order_id','id');
    }

    /**
     * 获取合适的订单 撮合
     * @param $type
     * @param $tradeCurr
     * @param $diff
     * @param $order
     * @return mixed
     */
    public function appOrder($type,$tradeCurr,$diff,$order)
    {
        return $this->where([
            ['trade_type', '=',$type],
            ['curr_abb', '=', $tradeCurr],
            $diff,
            ['residual_num','>',0],
        ])->orderBy('price_btc', $order)->get(['residual_volume','id','initial_volume','residual_num','trade_type','initial_mun','price_btc']);
    }

}
