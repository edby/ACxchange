<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IdCard extends Base
{
    protected $guarded = [];
    public $timestamps = false;
    /**
     * 获得拥有此idCard的用户。
     */
    public function user()
    {
        return $this->belongsTo('App\User','id','card_id');
    }

    /**
     * @param $value
     * @return mixed
     */
    public function getImgFrontAttribute($value)
    {
        return str_replace("public","/storage",$value);
    }

    /**
     * @param $value
     * @return mixed
     */
    public function getImgBackAttribute($value)
    {
        return str_replace("public","/storage",$value);
    }

    /**
     * @param $value
     * @return mixed
     */
    public function getImgHandAttribute($value)
    {
        return str_replace("public","/storage",$value);
    }


}
