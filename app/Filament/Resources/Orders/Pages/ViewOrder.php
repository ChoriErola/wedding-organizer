<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Filament\Resources\Orders\OrderResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Forms;
use Filament\Schemas\Components\Section;

class ViewOrder extends ViewRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }

    protected function getFormSchema(): array
    {
        return [
            Section::make('Detail Order')
                ->schema([
                    Forms\Components\Select::make('user_id')
                        ->label('Customer')
                        ->relationship('customer', 'name')
                        ->disabled(),

                    Forms\Components\TextInput::make('order_code')
                        ->label('Order Code')
                        ->disabled(),

                    Forms\Components\DatePicker::make('event_date')
                        ->label('Tanggal Acara')
                        ->disabled(),

                    Forms\Components\Select::make('status')
                        ->label('Status')
                        ->options([
                            'draft' => 'Draft',
                            'confirmed' => 'Confirmed',
                            'paid' => 'Paid',
                            'completed' => 'Completed',
                            'cancelled' => 'Cancelled',
                        ])
                        ->disabled(),

                    Forms\Components\Textarea::make('notes')
                        ->label('Catatan')
                        ->disabled()
                        ->columnSpanFull(),
                ])
                ->columns(2),

            Section::make('Paket & Harga (Terpilih)')
                ->schema([
                    Forms\Components\Select::make('package_id')
                        ->label('Paket')
                        ->relationship('package', 'name')
                        ->disabled(),

                    Forms\Components\TextInput::make('base_price')
                        ->label('Harga Paket (Terpilih)')
                        ->numeric()
                        ->prefix('Rp')
                        ->disabled()
                        ->formatStateUsing(fn ($state) =>
                            $state ? number_format($state, 0, ',', '.') : '0'
                        ),
                ])
                ->columns(2),

            Section::make('Layanan Paket (Dipilih)')
                ->schema([
                    Forms\Components\CheckboxList::make('selected_services')
                        ->label('')
                        ->options(function ($record) {
                            if (!$record) return [];
                            return $record->services()
                                ->where('is_required', true)
                                ->pluck('service_name', 'id')
                                ->toArray();
                        })
                        ->disabled()
                        ->columnSpanFull(),
                ])
                ->columnSpanFull(),

            Section::make('Layanan Tambahan (Dipilih)')
                ->schema([
                    Forms\Components\CheckboxList::make('optional_services')
                        ->label('')
                        ->options(function ($record) {
                            if (!$record) return [];
                            return $record->services()
                                ->where('is_required', false)
                                ->whereNull('package_id')
                                ->pluck('service_name', 'id')
                                ->toArray();
                        })
                        ->disabled()
                        ->columnSpanFull(),
                ])
                ->columnSpanFull(),

            Section::make('Total Harga')
                ->schema([
                    Forms\Components\TextInput::make('total_price')
                        ->label('Total')
                        ->numeric()
                        ->prefix('Rp')
                        ->disabled()
                        ->formatStateUsing(fn ($state) =>
                            $state ? number_format($state, 0, ',', '.') : '0'
                        ),
                ])
                ->columns(1),
        ];
    }
}
