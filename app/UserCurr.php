<?php

namespace App;



use Illuminate\Support\Facades\DB;

class UserCurr extends Base
{

    /**
     * 不可被批量赋值的属性。
     *
     * @var array
     */
    protected $guarded = [];

    public $timestamps = false;

    public static $confirmNum = [
        'btc' => 2,
        'zec' => 2,
        'bch' => 2,
        'ltc' => 2,
        'rpz' => 2,
    ];


    /**
     * 获取货币的手续费率
     * @param $userId
     * @param $currAbb
     * @return mixed
     */
    public static function getFeeRate($userId,$currAbb)
    {

        //--currAbb 必须是 rpz_btc格式
        $currency = explode('_',$currAbb);
      $coin = $currency[0];
       if(count($coin)==1){
           $currency[1]="btc";
       }
     //  dump($currency);
    //   dump($userId);

      return  DB::table("user_fee")->where([
            ['user_id','=',$userId],
          ['trade_curr','=',$currency[0]],
            ['currency','=',$currency[1]]

        ])->value('fee_rate');
    }

    /**
     * 获取币种信息
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function currencySets()
    {
        return $this->belongsTo('App\CurrencySet','curr_id', 'curr_id');
    }

    /**
     * 获取用户信息
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function users()
    {
        return $this->belongsTo('App\User','user_id','id');
    }
}