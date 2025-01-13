<?php

namespace App\Models;

use App\Enum\Customer\CustomerStatusEnum;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $table = 'customers';

    protected $guarded = ['id'];

    protected $casts = [
        'status' => CustomerStatusEnum::class,
    ];
}
