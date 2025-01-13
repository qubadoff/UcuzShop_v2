<?php

namespace App\Enum\Order;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum OrderStatusEnum: int implements HasLabel, HasColor
{
    case PENDING = 1;

    case CANCELLED = 2;

    case COMPLETED = 3;

    case DELIVERED = 4;

    case RETURNED = 5;

    public function getLabel(): string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::CANCELLED => 'Cancelled',
            self::COMPLETED => 'Completed',
            self::DELIVERED => 'Delivered',
            self::RETURNED => 'Returned',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::PENDING => 'warning',
            self::CANCELLED, self::RETURNED => 'danger',
            self::COMPLETED, self::DELIVERED => 'success',
        };
    }
}
