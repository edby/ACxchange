<?php
/**
 * Created by PhpStorm.
 * User: DELL
 * Date: 2018/5/2
 * Time: 12:41
 */

namespace App\Models;


use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class BalanceDB extends Model
{
    //--余额
    protected $table = 'balance_db';

    protected $fillable = ['user_id', 'currency', 'balance'];

    //--获取余额，余额不是即时余额,如果余额为空就得到真的余额，并且去赋值
    public static function get_balance($currency,$user){
        $result=DB::table("balance_db")->where([
            ['user_id','=',$user],
            ['currency','=',$currency]])->first();
        if($result){
            $balance=$result->balance;
        }else{
            //--获取实际余额
            $currency=strtoupper($currency);
            $client=Controller::getStaticClient($currency);
           $balance=  $client->_get_balance($user);
          self::set_balance($currency,$user,$balance);
        }

       return $balance;
    }

    //设置余额
    public static function set_balance($currency,$user,$balance){
        $currency=strtoupper($currency);
      //  dump("$currency");
      //  dump("$user");
      //  dump("$balance");

       BalanceDB::firstOrCreate(['user_id'=>$user,
            'currency'=>$currency]);
       //// dump("$currency");////
       //// dump("$user");
       //// dump("$balance");
        DB::table("balance_db")->where([
            ['user_id','=',$user],
            ['currency','=',$currency]
        ])->update(['balance'=>$balance,'updated_at'=>date('Y-m-d H:i:s',time())]);
    }
}