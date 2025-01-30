<?php

namespace App\Models;

use App\Enum\Partner\PartnerStatusEnum;
use Illuminate\Database\Eloquent\Model;

class Partner extends Model
{
    protected $table = 'partners';

    protected $guarded = ['id'];

    protected $casts = [
        'status' => PartnerStatusEnum::class,
    ];
}
