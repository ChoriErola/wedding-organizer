<?php

namespace App\Filament\Resources\OrderHistories\Pages;

use App\Filament\Resources\OrderHistories\OrderHistoriesResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditOrderHistories extends EditRecord
{
    protected static string $resource = OrderHistoriesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // DeleteAction::make(),
        ];
    }

}
