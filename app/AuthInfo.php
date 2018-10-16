<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AuthInfo extends Model
{

    protected $table = 'auth_info';
    protected $fillable = ['user_id','auth_type'];

}
