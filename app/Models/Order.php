<?php

namespace App\Models;

use App\Enum\Order\OrderStatusEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $table = 'orders';

    protected $guarded = ['id'];

    protected $casts = [
        'status' => OrderStatusEnum::class,
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function orderProduct(): HasMany
    {
        return $this->hasMany(OrderProduct::class, 'order_id', 'id');
    }
}
