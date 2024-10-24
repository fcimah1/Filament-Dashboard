<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NavMenuResource\Pages;
use App\Models\NavMenu;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class NavMenuResource extends Resource
{
    protected static ?string $model = NavMenu::class;

    protected static ?string $navigationIcon = 'heroicon-o-bolt';

    protected static ?string $navigationLabel = 'Nav Menus';

    protected static ?string $navigationGroup = 'Nav';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                ->live(onBlur: true)
                ->afterStateUpdated(function (Get $get, Set $set, ?string $old, ?string $state) {
                    if (($get('slug') ?? '') !== Str::slug($old)) {
                        return;
                    }
                    $set('slug', Str::slug($state));
                }),
                TextInput::make('slug')
                    ->required()
                    ->maxLength(255),
                Forms\Components\FileUpload::make('icon')
                    ->disk('public')
                    ->directory('navmenus')
                    ->image()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('slug'),
                Tables\Columns\ImageColumn::make('icon')
            ])
            ->filters([

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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNavMenus::route('/'),
            'create' => Pages\CreateNavMenu::route('/create'),
            'edit' => Pages\EditNavMenu::route('/{record}/edit'),
        ];
    }

    public static function getNavigationLabel(): string
    {
        return __('Nav Menus');
    }
}
