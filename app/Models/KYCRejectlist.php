<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KYCRejectlist extends Model
{
    protected $table = 'kyc_reject_lists';
    public $guarded = [];
}
