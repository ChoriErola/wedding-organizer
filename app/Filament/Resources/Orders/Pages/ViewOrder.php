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
                            'confirmed' => 'Confirmed',
                            'paid in progress' => 'Paid | In Progress',
                            'paid completed' => 'Paid | Completed',
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

                    Forms\Components\Textarea::make('all_services_display')
                        ->label('Layanan (Dipilih)')
                        ->disabled()
                        ->rows(8)
                        ->dehydrated(false)
                        ->formatStateUsing(function ($record) {
                            if (! $record || ! $record->exists) return '-';
                            $services = $record->services()->pluck('service_name')->toArray();
                            return ! empty($services) ? implode("\n", $services) : 'Tidak ada layanan terpilih';
                        })
                        ->columnSpanFull(),
                ])
                ->columns(2),

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
