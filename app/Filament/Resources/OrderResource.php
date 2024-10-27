<?php

namespace App\Filament\Resources;

use App\Enums\OrderTypeEnum;
use App\Exports\OrderExport;
use App\Filament\Resources\OrderResource\Pages;
use App\Models\Order;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Set;
use Filament\Tables\Actions\Action;
use Maatwebsite\Excel\Facades\Excel;
use pxlrbt\FilamentExcel\Exports\ExcelExport;
use pxlrbt\FilamentExcel\Columns\Column;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;


class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?string $navigationGroup = 'Shop';
    protected static ?int $navigationSort = 4;
    
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', OrderTypeEnum::PROCESSING->value)->count();
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return static::getModel()::where('status', OrderTypeEnum::PROCESSING->value)->count() >10 ? 'warning' : 'success';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Wizard::make([
                    Step::make('Order Details')
                        ->schema([
                            Forms\Components\TextInput::make('order_number')
                                ->label('Order Number')
                                ->default("OR-" . random_int(1000000, 9999999))
                                ->required()
                                ->disabled()
                                ->dehydrated(),
                            Forms\Components\Select::make('customer_id')
                                ->relationship('customer', 'name')
                                ->required()
                                ->searchable()
                                ->label('Customer Name'),
                            Forms\Components\TextInput::make('shipping_cost')
                                ->label('Shipping Cost')
                                ->numeric()
                                ->required(),
                            Forms\Components\Select::make('status')
                                ->required()
                                ->label('Status')
                                ->options([
                                    'pinding' => OrderTypeEnum::PENDING->value,
                                    'completed' => OrderTypeEnum::COMPLETED->value,
                                    'declined' => OrderTypeEnum::DECLINED->value,
                                    'processing' => OrderTypeEnum::PROCESSING->value,
                                ]),
                            Forms\Components\MarkdownEditor::make('notes')
                                ->label('Notes')
                                ->placeholder('Enter notes here')
                                ->columnSpanFull()
                        ])->columns(2),
                    Step::make('Order Items')
                        ->schema([
                            Forms\Components\Repeater::make('items')
                                ->relationship('items')
                                ->schema([
                                    Forms\Components\Select::make('product_id')
                                        ->label('Product')
                                        ->options(Product::query()->pluck('name', 'id'))
                                        ->searchable()
                                        ->reactive()
                                        ->afterStateUpdated(function (Set $set, ?string $state) {
                                            $set('unit_price', Product::find($state)?->price ?? 0);
                                        })
                                        ->required(),
                                    Forms\Components\TextInput::make('quantity')
                                        ->label('Quantity')
                                        ->required()
                                        ->live()
                                        ->dehydrated()
                                        ->minValue(1)
                                        ->numeric()
                                        ->default(1),
                                    Forms\Components\TextInput::make('unit_price')
                                        ->label('Unit Price')
                                        ->disabled()
                                        ->dehydrated()
                                        ->numeric()
                                        ->required(),
                                    Forms\Components\Placeholder::make('total_price')
                                        ->label('Total Price')
                                        ->content(fn($get) => $get('quantity') * $get('unit_price') + $get('shipping_cost')),
                                ])->columns(4)
                        ])
                ])->columnSpanFull()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order_number')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('customer.name')
                    ->searchable()
                    ->toggleable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->sortable()
                    ->toggleable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Order Date')
                    ->date()
                    ->sortable()
            ])
            // ->headerActions([
            //     Action::make('exportButton')
            //         ->label('Export Orders')
            //         ->color('success')
            //         ->icon('heroicon-o-arrow-down')
            //         ->action(function () {
            //             $data = [];
            //             $orders = Order::all();
            //             foreach($orders as $order){
            //                 $data[] = [
            //                     $order->order_number,
            //                     $order->customer->name,
            //                     $order->customer->email,
            //                     $order->status,
            //                     $order->order_date
            //                 ];
            //             }
            //             return Excel::download(new OrderExport($data, env('APP_LOCALE')), 'orders.xlsx');      
            //         })
            // ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    ExportBulkAction::make()->exports([
                        ExcelExport::make(Order::all())->withColumns([
                            Column::make('order_number')->heading('Order Number')->width(20),
                            Column::make('customer.name')->heading('Customer Name')->width(20),
                            Column::make('shipping_cost')->heading('Shipping Cost')->width(20),
                            Column::make('status')->heading('Status')->width(20),
                            Column::make('created_at')->heading('Order Date')->width(20),
                        ])->withFilename(date('Y-m-d') . ' - order'),
                    ])
                ])
            ]);
            
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
