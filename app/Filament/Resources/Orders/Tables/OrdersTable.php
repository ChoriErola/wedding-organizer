<?php

namespace App\Filament\Resources\Orders\Tables;

use App\Models\Order;
use App\Models\Services;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Facades\Auth;

use function Laravel\Prompts\textarea;

class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order_code')
                    ->label('Kode Order')
                    ->searchable()
                    ->copyable(),
                TextColumn::make('customer.name')
                    ->label('Customer')
                    ->searchable(),
                TextColumn::make('acara')
                    ->label('Acara')
                    ->searchable(),
                TextColumn::make('package.name')
                    ->label('Paket')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('event_date')
                    ->label('Tanggal Acara')
                    ->dateTime('d M Y'),
                TextColumn::make('status')
                    ->label('Status Order')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'pending' => 'secondary',
                        'confirmed' => 'warning',
                        'paid in progress' => 'info',
                        'paid completed' => 'success',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        default => 'secondary',
                    }),
                TextColumn::make('status')
                    ->label('Status Pembayaran')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'secondary',
                        'confirmed' => 'warning',
                        'paid in progress' => 'info',
                        'paid completed' => 'success',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                    })
                    ->disabled(fn ($record) =>
                        in_array($record?->status, [
                            'paid completed',
                            'completed',
                        ])
                    ),
                ImageColumn::make('bukti_pembayaran')
                    ->label('Bukti Pembayaran')
                    ->disk('public')
                    ->imageHeight(50)
                    ->square()
                    ->toggleable(),
                TextColumn::make('total_price')
                    ->label('Total Harga')
                    ->formatStateUsing(fn ($state) =>
                        'Rp ' . number_format($state, 0, ',', '.')
                    )
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'confirmed' => 'Confirmed',
                        'on progress' => 'Paid / Proses',
                        'paid' => 'Paid / Selesai',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ])
                    ->label('Filter by Status'),
                SelectFilter::make('event_date')
                    ->label('Filter by Event Date')
                    ->options(function () {
                        $dates = \App\Models\Order::query()
                            ->selectRaw('DATE(event_date) as date')
                            ->distinct()
                            ->orderBy('date', 'desc')
                            ->pluck('date')
                            ->toArray();
                        $formattedDates = [];
                        foreach ($dates as $date) {
                            $formattedDates[$date] = date('d M Y', strtotime($date));
                        }
                        return $formattedDates;
                    }),
                SelectFilter::make('customer_id')
                    ->label('Filter by Customer')
                    ->relationship('customer', 'name'),
            ])
            ->actions([
                ViewAction::make()->iconButton(),
                EditAction::make()->iconButton(),
                
                // Invoice Action
                Action::make('viewInvoice')
                    ->icon('heroicon-o-clipboard-document')
                    ->iconButton()
                    ->color('info')
                    ->tooltip('Lihat Invoice')
                    ->url(fn (Order $record) => route('order_invoice', $record))
                    ->openUrlInNewTab(),
                
                // Custom Action: Tambah Layanan
                Action::make('tambahLayanan')
                    ->icon('heroicon-o-plus-circle')
                    ->iconButton()
                    ->color('success')
                    ->tooltip('Tambah Layanan')
                    ->modalHeading(fn (Order $record) => "Tambah Layanan: " . $record->order_code)
                    ->modalDescription('Layanan yang ditambahkan akan otomatis memperbarui total harga order.')
                    ->modalWidth('md')
                    ->form([
                        Select::make('service_id')
                            ->label('Pilih Layanan')
                            ->options(
                                Services::query()
                                    ->whereNotNull('name')
                                    ->pluck('name', 'id')
                            )
                            ->searchable()
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, $set) {
                                $service = Services::find($state);
                                $set('price', $service?->harga_layanan ?? 0);
                            }),

                        TextInput::make('price')
                            ->label('Harga Layanan')
                            ->numeric()
                            ->prefix('Rp')
                            ->required(),
                        Textarea::make('notes')
                            ->label('Catatan Tambahan')
                            ->rows(3)
                            ->placeholder('Misalnya: Bunga Warna Putih.'),
                    ])
                    ->action(function (Order $record, array $data): void {
                        $service = \App\Models\Services::find($data['service_id']);
                        $nama = $service?->name ?? 'Layanan Tambahan';
                        $harga = (float) ($service?->harga_layanan ?? 0);

                        $record->services()->create([
                            'service_id'   => $data['service_id'],
                            'service_name' => $nama,
                            'price'        => $harga,
                            'notes'        => $data['notes'] ?? null,
                            'is_custom'    => true,
                        ]);

                        $record->histories()->create([
                            'old_status' => $record->status,
                            'new_status' => $record->status,
                            'notes' => "Menambahkan layanan: " . $nama,
                            'changed_by' => Auth::id(),
                        ]);
                        
                        $record->total_price = (float) $record->total_price + $harga;
                        $record->save();

                        \Filament\Notifications\Notification::make()
                            ->title('Berhasil Menambahkan Layanan')
                            ->body($nama . ' - Total harga diperbarui: Rp ' . number_format($record->total_price, 0, ',', '.'))
                            ->success()
                            ->send();
                    }),
                    
                DeleteAction::make()->iconButton(),
                
            ]);
    }
}
