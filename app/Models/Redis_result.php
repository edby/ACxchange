<?php
/**
 * Created by PhpStorm.
 * User: DELL
 * Date: 2018/5/8
 * Time: 14:31
 */

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Redis_result extends Model
{
    protected $table="redis_result";
    protected $fillable=['id','request','result'];
    protected $primaryKey="id";//--主键
    //public $timestamps=false;//--不需要时间 true表示需要时间

}