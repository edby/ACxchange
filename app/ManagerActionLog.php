<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ManagerActionLog extends Model
{
    protected $fillable = [
        'type', 'author_id', 'author_name','ip_address', 'action'
    ];
}
