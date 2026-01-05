<?php

namespace App\Filament\Resources\ContactUs\Pages;

use App\Filament\Resources\ContactUs\ContactUsResource;
use Filament\Resources\Pages\CreateRecord;

class CreateContactUs extends CreateRecord
{
    protected static string $resource = ContactUsResource::class;
    
    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }

}
