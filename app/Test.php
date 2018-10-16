<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Test extends Model
{
    //
    protected $fillable = ['id','name','age'];
    public $timestamps = false;
}
