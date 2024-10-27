<?php

namespace App\Filament\Widgets;

use App\Enums\OrderTypeEnum;
use App\Models\Order;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class OrderChart extends ChartWidget
{
    protected static ?string $heading = 'Orders Chart';
    //sort
    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $data = Order::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // dd(vars: $data);

        return [
            'datasets' => [
                [
                    'label' => 'Order',
                    'data' => array_values($data),
                    'backgroundColor' => '#6200EE',
                    'borderColor' => '#6200EE',
                ]
            ],
            'labels' => OrderTypeEnum::cases(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
