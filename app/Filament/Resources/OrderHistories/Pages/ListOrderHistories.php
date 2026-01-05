<?php

namespace App\Filament\Resources\OrderHistories\Pages;

use App\Filament\Resources\OrderHistories\OrderHistoriesResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListOrderHistories extends ListRecords
{
    protected static string $resource = OrderHistoriesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // CreateAction::make(),
        ];
    }

    
}
