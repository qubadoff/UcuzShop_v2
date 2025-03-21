<?php

namespace App\Enum\Partner;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum PartnerStatusEnum: int implements HasLabel, HasColor
{
    case ACTIVE = 1;

    case INACTIVE = 2;

    public function getLabel(): string
    {
        return match ($this) {
            self::ACTIVE => 'Aktiv',
            self::INACTIVE => 'Deaktiv',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::ACTIVE => 'success',
            self::INACTIVE => 'danger',
        };
    }
}
