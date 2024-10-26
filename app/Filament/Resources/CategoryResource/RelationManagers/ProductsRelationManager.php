<?php

namespace App\Filament\Resources\CategoryResource\RelationManagers;

use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;
use Filament\Forms\Get;
use Filament\Forms\Set;

class ProductsRelationManager extends RelationManager
{
    protected static string $relationship = 'products';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Products')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Informations')
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
                                }),
                            Forms\Components\TextInput::make('slug')
                                ->required()
                                ->disabled()
                                ->dehydrated()
                                ->unique('products', 'slug', ignoreRecord:true)
                                ->maxLength(255),
                                    Forms\Components\MarkdownEditor::make('description')
                                    ->required()
                                    ->maxLength(255)
                                    ->columnSpan('full'),
                            ])->columns(2),
                        Forms\Components\Tabs\Tab::make('Pricing')
                            ->schema([
                                Forms\Components\TextInput::make('price')
                                    ->required()
                                    ->numeric()
                                    ->regex('/^\d{1,6}(\.\d{0,2})?$/'),
                                    Forms\Components\TextInput::make('quantity')
                                    ->required()
                                    ->numeric()
                                    ->minValue(0),
                            ])->columns(2),
                        Forms\Components\Tabs\Tab::make('Additional Informations')
                            ->schema([
                                Forms\Components\Toggle::make('available')
                                    ->label('Availablilty')
                                    ->helperText('enable/disable product availability')
                                    ->default(true),
                                Forms\Components\Toggle::make('featured')
                                    ->label('Featured')
                                    ->helperText('enable/disable product as featured')
                                    ->default(true),
                                Forms\Components\Select::make("category_id")
                                    ->relationship('category', 'name')
                                    ->options(Category::all()->pluck('name', 'id'))
                                    ->required(),
                                Forms\Components\FileUpload::make('image')
                                    ->required()
                                    ->image()
                                    ->imageEditor()
                                    ->preserveFilenames()
                                    ->directory('products')
                                    ->visibility('public') // Make sure it's publicly accessible
                                    ->columnSpanFull()
                            ])->columns(2)
                    
                    ])->columnSpanFull()
            ]);
    }

    public function table(Table $table): Table
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
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
