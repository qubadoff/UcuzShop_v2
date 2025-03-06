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

    case PREPARING = 6;

    public function getLabel(): string
    {
        return match ($this) {
            self::PENDING => 'Gözləmədə',
            self::CANCELLED => 'Ləğv edildi',
            self::COMPLETED => 'Tamamlandı',
            self::DELIVERED => 'Çatdırıldı',
            self::RETURNED => 'Geri qaytarıldı',
            self::PREPARING => 'Hazırlanır',
        };
    }

    /**
     * @throws \Exception
     */
    public function getColor(): string
    {
        return match ($this) {
            self::PENDING => 'warning',
            self::CANCELLED, self::RETURNED => 'danger',
            self::COMPLETED, self::DELIVERED => 'success',
            self::PREPARING => 'info',
        };
    }
}
