<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    //
    protected $table = 'currency';

//    public function fee()
//    {
//        return $this->hasOne('App\Models\Fee');
//    }

    public function market()
    {
        return $this->hasMany('App\Models\Market','from_currency','id');
    }
}
