<?php

namespace App\Models;

use App\Enum\Partner\PartnerStatusEnum;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Partner extends Authenticatable
{
    use HasApiTokens, SoftDeletes, Notifiable;

    protected $table = 'partners';

    protected $guarded = ['id'];

    protected $casts = [
        'status' => PartnerStatusEnum::class,
    ];
}
