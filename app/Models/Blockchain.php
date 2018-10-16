<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Blockchain extends Model
{
    //
    protected $table = 'blockchain_opt';

    protected $fillable = ['order_id','currency','user_id','target_id','amount','status'];


}
