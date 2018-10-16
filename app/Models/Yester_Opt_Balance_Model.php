<?php
/**
 * Created by PhpStorm.
 * User: DELL
 * Date: 2018/5/25
 * Time: 16:11
 */

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

//--昨天之间的交易汇总余额
class Yester_Opt_Balance_Model extends  Model
{

    //
    protected $table = 'yester_opt_balance';
    //--

    protected $fillable = ['user_id', 'currency', 'balance'];

    //--获取前天opt余额  和时间
    public static function get_opt_balance($currency,$user){
        $result_opt=[];
        $time=0;
        $result=DB::table("yester_opt_balance")->where([
            ['user_id','=',$user],
            ['currency','=',$currency]])->first();
        if($result){
            $balance=$result->balance;
            $time=$result->time;
        }else{
            $balance=0;
        }
        $result_opt['time']=$time;
        $result_opt['balance']=$balance;
        return $result_opt;
    }

    //设置余额
    public static function set_opt_balance($currency,$user,$balance,$kais_time){
        $currency=strtoupper($currency);
        //  dump("$currency");
        //  dump("$user");
        //  dump("$balance");

        Yester_Opt_Balance_Model::firstOrCreate(['user_id'=>$user,
            'currency'=>$currency]);
        //// dump("$currency");////
        //// dump("$user");
        //// dump("$balance");
        ///
        ///
        $update_time= date("Y-m-d H:i:s",time());//把数字型时间按格式转换成时间格  前天的
        DB::table("yester_opt_balance")->where([
            ['user_id','=',$user],
            ['currency','=',$currency]
        ])->update(['balance'=>$balance,"time"=>$kais_time,'updated_at'=>$update_time]);
    }
}