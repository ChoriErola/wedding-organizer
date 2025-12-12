<?php

namespace App\Filament\Resources\Packages\Pages;

use App\Filament\Resources\Packages\PackagesResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePackages extends CreateRecord
{
    protected static string $resource = PackagesResource::class;

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }

}