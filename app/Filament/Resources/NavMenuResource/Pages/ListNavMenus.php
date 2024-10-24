<?php

namespace App\Filament\Resources\NavMenuResource\Pages;

use App\Filament\Resources\NavMenuResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListNavMenus extends ListRecords
{
    protected static string $resource = NavMenuResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
