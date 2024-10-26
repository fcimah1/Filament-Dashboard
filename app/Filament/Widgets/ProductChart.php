<?php

namespace App\Filament\Widgets;

use App\Models\Product;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class ProductChart extends ChartWidget
{
    protected static ?string $heading = 'Chart';

    // sort 
    protected static ?int $sort = 1;

    protected function getData(): array
    {
        $data = $this->getProductsPerMonth();
        return [
            'datasets' => [
                [
                    'label' => 'Products added per month',
                    'data' => $data['productsPerMonth'],
                ],
            ],
            'labels' => $data['months'],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    private function getProductsPerMonth(): array
    {
        $now = Carbon::now();
        $productsPerMonth = [];

        $months = collect(range(1, 12))->map(function ($month) use ($now, &$productsPerMonth) {
            $count = Product::whereMonth('created_at', Carbon::parse($now->month($month)->format('Y-m')))->count();
            $productsPerMonth[] = $count;
            return $now->month($month)->format('M');
        })->toArray();
        return [
            'months' => $months,
            'productsPerMonth' => $productsPerMonth
        ];
    }
}
