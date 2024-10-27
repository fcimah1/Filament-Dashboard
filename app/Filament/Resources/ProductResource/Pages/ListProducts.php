<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Exports\ProductsExport;
use App\Filament\Resources\ProductResource;
use App\Models\Product;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Maatwebsite\Excel\Facades\Excel;

class ListProducts extends ListRecords
{
    protected static string $resource = ProductResource::class;

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
                $products = Product::all();
                foreach($products as $product){
                    $data[] = [
                        $product->id,
                        $product->name,
                        $product->description,
                        $product->price,
                        $product->quantity,
                        $product->category->name,
                        $product->brand->name,
                        $product->featured ? 'Yes' : 'No',
                    ];
                }
                return Excel::download(new ProductsExport($data, env('APP_LOCALE')), date('Y-m-d') . "-products.xlsx");      
            })
        ];
    }
}
