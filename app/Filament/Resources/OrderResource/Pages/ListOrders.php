<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Exports\OrderExport;
use App\Filament\Resources\OrderResource;
use App\Models\Order;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Maatwebsite\Excel\Facades\Excel;

class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('exportButton')
            ->label('Export Orders')
            ->color('success')
            ->icon('heroicon-o-arrow-down')
            ->action(function () {
                $data = [];
                $orders = Order::all();
                foreach($orders as $order){
                    $data[] = [
                        $order->order_number,
                        $order->customer->name,
                        $order->customer->email,
                        $order->status,
                        $order->order_date
                    ];
                }
                return Excel::download(new OrderExport($data, env('APP_LOCALE')), date('Y-m-d') . "-orders.xlsx");      
            })
    
        ];
    }
}
