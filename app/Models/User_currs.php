<?php
/**
 * Created by PhpStorm.
 * User: DELL
 * Date: 2018/5/4
 * Time: 10:54
 */

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class User_currs extends Model
{

    protected $table="user_currs";
    protected $fillable=['curr_abb','address','user_id','curr_id'];
    protected $primaryKey="id";//--主键
    public $timestamps=false;//--不需要时间 true表示需要时间
}