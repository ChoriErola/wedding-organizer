<?php

namespace App\Filament\Resources\OrderHistories\Tables;

use Filament\Actions\Action as ActionsAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Tables\Grouping\Group;
use Filament\Schemas\Components\Section;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class OrderHistoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->groups([
                Group::make('order.order_code')
                    ->label('')
                    ->getTitleFromRecordUsing(fn ($record) =>
                        sprintf(
                            '%s • %s',
                            $record->order->order_code,
                            optional($record->order->event_date)->format('d M Y')
                        )
                    ),
            ])
            ->defaultGroup('order.order_code')
            ->groupingSettingsHidden()
            ->columns([
                TextColumn::make('order.order_code')
                    ->label('Order Code')
                    ->searchable()
                    ->copyable()
                    ->sortable(),
                TextColumn::make('order.customer.name')
                    ->label('Nama Pelanggan')
                    ->searchable()
                    ->sortable(),   
                TextColumn::make('old_status')
                    ->label('Status Lama')
                    ->badge()
                    ->searchable(),
                TextColumn::make('new_status')
                    ->label('Status Baru')
                    ->badge()
                    ->color('success')
                    ->searchable(),
                ImageColumn::make('order.bukti_pembayaran')
                    ->label('Bukti Pembayaran')
                    ->disk('public')
                    ->imageHeight(50)
                    ->square()
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->label('Tanggal Perubahan')
                    ->dateTime('d/m/Y')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('order_id')
                    ->label('Kode Order')
                    ->relationship('order', 'order_code'),
                SelectFilter::make('customer_id')
                    ->label('Nama Pelanggan')
                    ->relationship('order.customer', 'name'),
                SelectFilter::make('created_at')
                    ->label('Tanggal Perubahan')
                    ->options(function () {
                        $dates = \App\Models\OrderHistories::query()
                            ->selectRaw('DATE(created_at) as date')
                            ->distinct()
                            ->orderBy('date', 'desc')
                            ->pluck('date')
                            ->toArray();

                        $formattedDates = [];
                        foreach ($dates as $date) {
                            $formattedDates[$date] = date('d/m/Y', strtotime($date));
                        }
                        return $formattedDates;
                    }),
                SelectFilter::make('changed_by')
                    ->label('Diubah Oleh')
                    ->relationship('changer', 'name'),
            ])
            ->recordActions([
                ViewAction::make()
                    ->iconButton()
                    ->modalHeading('Detail Riwayat Pesanan')
                    ->modalWidth('lg') // Mengatur lebar modal
                    ->infolist(static::getViewInfolist()), // Memanggil schema yang kita buat di atas
                
                DeleteAction::make()->iconButton(),
            ])
                ->toolbarActions([
                // BulkActionGroup::make([
                //     DeleteBulkAction::make(),
                // ]),
            ]);      
    }

    // modal view action infolist
    public static function getViewInfolist(): array
    {
        return [
            Section::make('Informasi Pesanan')
                ->schema([
                    TextEntry::make('order.order_code')
                        ->label('Order Code'),
                    TextEntry::make('order.customer.name')
                        ->label('Nama Pelanggan'),
                    TextEntry::make('order.total_price')
                        ->label('Harga Total')
                        ->formatStateUsing(fn ($state) =>
                        'Rp ' . number_format($state, 0, ',', '.')
                        ),
                    TextEntry::make('order.acara')
                        ->label('Acara & Tanggal')
                        ->formatStateUsing(function ($state, $record) {
                            return $state . ' • ' . optional($record->order->event_date)->format('d M Y');
                        }),
                ])->columns(2),
            
            Section::make('Detail Perubahan Status')
                ->schema([
                    TextEntry::make('old_status')
                        ->label('Status Lama')
                        ->badge(),
                    TextEntry::make('new_status')
                        ->label('Status Baru')
                        ->badge()
                        ->color('success'),
                    TextEntry::make('changer.name')
                        ->label('Diubah Oleh'),
                    TextEntry::make('created_at')
                        ->label('Waktu Perubahan')
                        ->dateTime(),
                ])->columns(2),
            
            Section::make('Bukti Pembayaran')
                ->schema([
                    TextEntry::make('order.payment_note')
                        ->label('Catatan Pembayaran')
                        ->columnSpanFull(),
                    ImageEntry::make('order.bukti_pembayaran')
                        ->label('Foto Bukti Pembayaran')
                        ->disk('public')
                        ->columnSpanFull(),
                ])->collapsed()
                ->collapsible(),
        ];
    }
}