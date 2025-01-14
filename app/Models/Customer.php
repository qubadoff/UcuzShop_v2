<?php

namespace App\Models;

use App\Enum\Customer\CustomerStatusEnum;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;

class Customer extends Authenticatable
{
    use HasApiTokens, SoftDeletes;

    protected $table = 'customers';

    protected $guarded = ['id'];

    protected $casts = [
        'status' => CustomerStatusEnum::class,
    ];

    public function cart(): HasMany
    {
        return $this->hasMany(Cart::class, 'customer_id', 'id');
    }
}
