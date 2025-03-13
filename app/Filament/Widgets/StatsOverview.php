<?php

namespace App\Filament\Widgets;

use App\Models\Customer;
use App\Models\Order;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Müştərilər', count(Customer::all()))->chart([5, 9, 7, 10, 12, 15, 20])->color('primary'),
            Stat::make('Sifarişlər', count(Order::all()))->chart([5, 9, 7, 10, 12, 15, 20])->color('primary'),
        ];
    }
}
