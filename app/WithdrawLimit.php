<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WithdrawLimit extends Model
{
    //
    protected $table = 'withdraw_limit';

    protected $guarded = [];

    public static $level = [
        0 => 0,
        1 => 2,
        3 => 5,
    ];
}
