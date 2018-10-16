<?php

namespace App;


class Passport  extends Base
{
    protected $guarded = [];
    public $timestamps = false;
    /**
     * 获得拥有此护照的用户。
     */
    public function user()
    {
        return $this->belongsTo('App\User','id','passport_id');
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

}
