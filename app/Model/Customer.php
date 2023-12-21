<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    //
    protected $table = 'customers';

    const STATUS_LOCKED = 0;
    const STATUS_ACTIVE = 1;

    protected $fillable = [
        'ct_name', 'ct_code', 's_email', 'ct_email', 'ct_phone', 'ct_status', 'created_at', 'updated_at',
    ];
}