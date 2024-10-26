<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BrandResource\Pages;
use App\Filament\Resources\BrandResource\RelationManagers;
use App\Filament\Resources\BrandResource\RelationManagers\ProductsRelationManager;
use App\Models\Brand;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;
use Filament\Forms\Get;
use Filament\Forms\Set;

class BrandResource extends Resource
{
    protected static ?string $model = Brand::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Shop';
    protected static ?int $navigationSort = 1;
    
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Brand Information')
                            ->schema([

                                Forms\Components\TextInput::make('name')
                                    ->required()
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
                                    ->unique('brands', 'name', ignoreRecord: true),
                                Forms\Components\MarkdownEditor::make('description')
                                    ->required(),
                            ]),

                    ]),
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Brand Status')
                            ->schema([
                                Forms\Components\Toggle::make('status')
                                    ->label('Active')
                                    ->helperText('Active/Inactive brand')
                                    ->default(true),
                            ]),
                        Forms\Components\Section::make('Brand Image')
                            ->schema([
                                Forms\Components\FileUpload::make('image')
                                    ->disk('public')
                                    ->directory('brands')
                                    ->image()
                                    ->required(),
                            ]),
                        Forms\Components\Section::make('Brand Color')
                            ->schema([
                                Forms\Components\ColorPicker::make('primary_color')
                                    ->label('Primary Color')
                                    ->required(),
                                Forms\Components\ColorPicker::make('secondary_color')
                                    ->label('Secondary Color'),
                            ]),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                ->sortable()
                ->searchable(),
                Tables\Columns\TextColumn::make('slug')
                ->sortable()
                ->searchable(),
                Tables\Columns\ImageColumn::make('image')->size(50),
                Tables\Columns\IconColumn::make('status')
                ->toggleable()
                ->sortable()
                ->boolean(),
                Tables\Columns\ColorColumn::make('primary_color')
                ->label('Primary Color')
                ->toggleable(),
                Tables\Columns\ColorColumn::make('secondary_color')
                ->label('Secondary Color')
                ->toggleable(),
                Tables\Columns\TextColumn::make('description')
                ->limit(50),
            ])
            
            ->filters([
                Tables\Filters\TernaryFilter::make('status')
                ->label('Status')
                ->boolean()
                ->trueLabel('Only available brands')
                ->falseLabel('Only unavailable brands')
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ViewAction::make(),
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
            ProductsRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBrands::route('/'),
            'create' => Pages\CreateBrand::route('/create'),
            'edit' => Pages\EditBrand::route('/{record}/edit'),
        ];
    }
}
