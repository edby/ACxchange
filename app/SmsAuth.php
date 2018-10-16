<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SmsAuth extends Model
{
    protected $table = 'sms_auth';

    protected $fillable = ['user_id','phone_number','country_code','verified'];

}
