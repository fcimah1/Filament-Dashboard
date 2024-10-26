<?php

namespace App\Filament\Widgets;

use App\Enums\OrderTypeEnum;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [    
            Stat::make('Total Customers', Customer::count())
                ->description('Increase in customers')
                ->descriptionIcon('heroicon-o-arrow-trending-up')
                ->color('success')
                ->chart([7,5,4,6,2,8,4,3]),
            Stat::make('Total Products', Product::count())
                ->description('Total Products in App')
                ->descriptionIcon('heroicon-o-arrow-trending-down')
                ->color('danger')
                ->chart([7,5,4,6,2,8,4,3]),
            Stat::make('Pinding Orders', Order::where('status', OrderTypeEnum::PENDING)->count())
                ->description('Orders in pending')
                ->descriptionIcon('heroicon-o-arrow-trending-down')
                ->color('danger')
                ->chart([7,5,4,6,2,8,4,3]),
        ];
    }
}
