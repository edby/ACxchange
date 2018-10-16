<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IgnoredUser extends Model
{
    //
    protected $table = 'ignored_users';

    protected $fillable = [
        'user_id'
    ];


}
