<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Doctrine\DBAL\Schema\Schema;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Tables\Filters\TernaryFilter;
use Illuminate\Database\Eloquent\Model;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $activeNavigationIcon = 'heroicon-o-check-badge';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
    protected static ?string $navigationIcon = 'heroicon-o-bolt';

    protected static ?string $navigationGroup = 'Shop';
    protected static ?int $navigationSort = 2;

    protected static int $globalSearchResultsLimit = 20;

    protected static ?string $recordTitleAttribute = 'name';

    protected static function getgGloballysearchableAttributes(): array
    {
        return ['name', 'slug', 'price', 'description'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Brand' => $record->brand->name,
            'Category' => $record->category->name,
            'Price' => $record->price,
        ];
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with(['brand', 'category']);
    }


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Product Information')
                            ->schema([

                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->unique()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (Get $get, Set $set, ?string $old, ?string $state) {
                                        if (($get('slug') ?? '') !== Str::slug($old)) {
                                            return;
                                        }
                                        $set('slug', Str::slug($state));
                                    })
                                    ->columnSpan('full'),
                                Forms\Components\TextInput::make('slug')
                                    ->required()
                                    // ->disabled()
                                    ->unique('products', 'slug', ignoreRecord:true)
                                    ->maxLength(255)
                                    ->columnSpan('full'),
                                Forms\Components\TextInput::make('price')
                                    ->required()
                                    ->numeric()
                                    ->regex('/^\d{1,6}(\.\d{0,2})?$/')
                                    ->columnSpan('full'),
                                // Forms\Components\TextInput::make('sale_price')
                                //     ->numeric()
                                //     ->rules(['regax:^[0-9]+(\.[0-9]{1,2})?$'])
                                //     ->columnSpan('full'),
                                // Forms\Components\TextInput::make('sku')
                                //     ->required()
                                //     ->unique('products', 'sku', ignoreRecord:true)
                                //     ->maxLength(255)
                                //     ->columnSpan('full'),
                                // Forms\Components\TextInput::make('weight')
                                //     ->numeric()
                                //     ->rules(['regax:^[0-9]+(\.[0-9]{1,2})?$' ])
                                //     ->columnSpan('full'),
                                Forms\Components\TextInput::make('quantity')
                                    ->required()
                                    ->numeric()
                                    ->minValue(0),
                                Forms\Components\MarkdownEditor::make('description')
                                    ->required()
                                    ->maxLength(255)
                                    ->columnSpan('full'),
                            ])->columns(2),
                    ]),
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Product Status')
                            ->schema([
                                Toggle::make('available')
                                    ->label('Availablilty')
                                    ->helperText('enable/disable product availability')
                                    ->default(true),
                                Toggle::make('featured')
                                    ->label('Featured')
                                    ->helperText('enable/disable product as featured')
                                    ->default(true),
                            ]),
                        Forms\Components\Section::make('Product Image')
                            ->schema([
                                Forms\Components\FileUpload::make('image')
                                    ->required()
                                    ->image()
                                    ->imageEditor()
                                    ->preserveFilenames()
                                    ->directory('products')
                                    ->visibility('public') // Make sure it's publicly accessible
                            ])->collapsible(),
                        Forms\Components\Section::make('Product Associations')
                            ->schema([
                                Select::make("category_id")
                                    ->relationship('category', 'name')
                                    ->options(Category::all()->pluck('name', 'id'))
                                    ->required(),
                                Select::make('brand_id')
                                    ->relationship('brand', 'name')
                                    ->options(Brand::all()->pluck('name', 'id'))
                                    ->required(),
                            ])->collapsible()
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                ->searchable()
                ->sortable(),
                Tables\Columns\TextColumn::make('slug')
                ->searchable()
                ->sortable(),
                Tables\Columns\TextColumn::make('price')
                ->sortable()
                ->toggleable(),
                Tables\Columns\TextColumn::make('quantity')
                ->sortable()
                ->toggleable(),
                Tables\Columns\ImageColumn::make('image'),
                Tables\Columns\TextColumn::make('category.name')
                ->sortable()
                ->searchable()
                ->toggleable()
                ->label('Category'),
                Tables\Columns\TextColumn::make('brand.name')
                ->sortable()
                ->searchable()
                ->toggleable()
                ->label('Brand'),
                Tables\Columns\IconColumn::make('available')
                ->sortable()
                ->toggleable()
                ->boolean(),
                Tables\Columns\IconColumn::make('featured')
                ->sortable()
                ->toggleable()
                ->boolean(),
            ])
            ->filters([
                TernaryFilter::make('available')
                    ->label('Availablibity')
                    ->boolean()
                    ->trueLabel('Only Available Products')
                    ->falseLabel('Only Unavailable Products'),
                TernaryFilter::make('featured')
                    ->label('Featured Products')
                    ->boolean()
                    ->trueLabel('Only Featured Products')
                    ->falseLabel('Only Unfeatured Products')
                    ->native(false),
                Tables\Filters\SelectFilter::make('category_id')
                    ->relationship('category', 'name'),
                Tables\Filters\SelectFilter::make('brand_id')
                    ->relationship('brand', 'name'),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                    Tables\Actions\ViewAction::make(),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
