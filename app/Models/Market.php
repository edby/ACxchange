<?php
/**
 * Created by PhpStorm.
 * User: DELL
 * Date: 2018/4/21
 * Time: 10:30
 */
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Market extends Model
{
    //
    protected $table = 'market';

    protected $guarded = [];

    public function currency()
    {
        return $this->belongsTo('App\Models\Currency','id','from_currency');
    }
}
