<?php

namespace App;



class CurrencySet extends Base
{
    /**
     * @var array
     */
    protected $guarded = [];

    /**
     * @var string
     */
    protected $primaryKey = 'curr_id';

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * 获取用户开通的币种信息
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function userCurr()
    {
        //foreign key 表示 userCurr 表的字段
        return $this->hasMany('App\UserCurr','curr_id', 'curr_id');
    }

    /**
     * 获得此货币的所有交易。
     */
    public function order()
    {
        return $this->hasMany('App\Order','curr_id','curr_id');
    }

    /**
     * 获得此货币的所有交易用户的。
     */
    public function orderDetail()
    {
        return $this->hasMany('App\OrderDetail','curr_id','curr_id');
    }

    /**
     * 获得此货币下的所有订单细节
     */
    public function orderDetails()
    {
        return $this->hasManyThrough(
            'App\OrderDetails',
            'App\Order',
            'curr_id', // 订单表外键  也就是 curr_id  对应 currencySet （外键参考值）
            'order_id', // 订单细节表表外键  order_id  对应是 order （外键参考）
            'curr_id', // 货币集合表表本地键 Order外键的参考建
            'id' // order表本地键  及 OrderDetails 外键参考 order 的外键
        );
    }

    /**
     * 注意  货币全称必须和 接口定义 一致 否则会报错
     * @param string $currName 货币全称
     * @param string $currAbb  货币简称
     * @return bool
     */
    public function createCurr($currName,$currAbb)
    {
        $currName = strtolower($currName);
        $url = 'https://api.coinmarketcap.com/v1/ticker/';
        $cny= '/?convert=CNY';
        $currRes = $this->where('curr_abb',$currAbb)->first();
        if ($currRes->isEmpty()){
            $data = file_get_contents($url.$currName.$cny);
            $result = json_decode($data,true)[0];

            $createData = [
                'curr_name' => $currName,
                'curr_abb' => $currAbb,
                'price_usd' => $result['price_usd'],
                'price_cny' => $result['price_cny'],
                'price_btc' =>$result['price_btc'],
                'last_updated' => $result['last_updated'],
            ];
            return $this->create($createData);
        }
        return false;
    }
}
