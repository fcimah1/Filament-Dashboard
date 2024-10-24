<?php

namespace App\Filament\Resources\NavMenuResource\Pages;

use App\Filament\Resources\NavMenuResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditNavMenu extends EditRecord
{
    protected static string $resource = NavMenuResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
    public function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    
}
