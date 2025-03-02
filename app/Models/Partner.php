<?php

namespace App\Models;

use App\Enum\Partner\PartnerStatusEnum;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Partner extends Authenticatable
{
    use HasApiTokens, SoftDeletes;

    protected $table = 'partners';

    protected $guarded = ['id'];

    protected $casts = [
        'status' => PartnerStatusEnum::class,
    ];
}
