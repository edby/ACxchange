<?php
/**
 * Created by PhpStorm.
 * User: DELL
 * Date: 2018/5/4
 * Time: 10:21
 */

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class UserFee extends Model
{

    protected $table="user_fee";
    protected $fillable=['currency','fee_rate','trade_curr','user_id'];
    protected $primaryKey="id";//--主键
    public $timestamps=false;//--不需要时间 true表示需要时间
}