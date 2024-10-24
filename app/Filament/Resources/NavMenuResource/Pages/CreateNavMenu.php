<?php

namespace App\Filament\Resources\NavMenuResource\Pages;

use App\Filament\Resources\NavMenuResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateNavMenu extends CreateRecord
{
    protected static string $resource = NavMenuResource::class;

    public function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
