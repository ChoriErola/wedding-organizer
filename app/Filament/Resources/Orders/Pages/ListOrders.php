<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Filament\Resources\Orders\OrderResource;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\ListRecords;

class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
    
    protected function getTableActions(): array
    {
        return [
            ViewAction::make()
                ->label('Lihat')
                ->modalHeading(fn($record) => "Lihat {$record->order_code}")
                ->modalWidth('7xl')
                ->modal() // open as modal (no custom form reference)
                ->modalSubmitAction(false) // karena ini hanya view
                ->modalCancelActionLabel('Tutup'),
            
            EditAction::make(),
        ];
    }

}
