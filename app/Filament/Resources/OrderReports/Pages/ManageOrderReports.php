<?php

namespace App\Filament\Resources\OrderReports\Pages;

use App\Filament\Resources\OrderReports\OrderReportResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageOrderReports extends ManageRecords
{
    protected static string $resource = OrderReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //CreateAction::make(),
        ];
    }
}
