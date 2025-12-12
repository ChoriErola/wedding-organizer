<?php

namespace App\Filament\Resources\Orders\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Illuminate\Support\Str;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->relationship('customer', 'name')
                    ->label('Customer')
                    ->searchable()
                    ->preload()
                    ->reactive()
                    ->required(),
                TextInput::make('order_code')
                    ->default(fn () => 'ORD-' . now()->format('Ymd'). '-' . Str::upper(Str::random(6)))
                    ->disabled()
                    ->dehydrated(),
                DatePicker::make('event_date')
                    ->label('Tanggal Acara')
                    ->required(),
                Select::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'pending' => 'Pending',
                        'confirmed' => 'Confirmed',
                        'paid' => 'Paid',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ])
                    ->required(),
                Textarea::make('alamat')
                    ->label('Alamat Acara')
                    ->required()
                    ->columnSpanFull(),
                Textarea::make('notes')
                    ->label('Catatan')
                    ->columnSpanFull(),
            // choose package and select which services to include (checkboxes only)
            Select::make('package_id')
                ->relationship('package', 'name')
                ->label('Paket')
                ->searchable()
                ->preload()
                ->reactive()
                ->required()
                ->afterStateUpdated(function ($state, $set) {
                    if (! $state) {
                        $set('selected_service_ids', []);
                        $set('base_price', 0);
                        $set('total_price', 0);
                        return;
                    }
                    $package = \App\Models\Package::find($state);
                    if (! $package) {
                        $set('selected_service_ids', []);
                        $set('base_price', 0);
                        $set('total_price', 0);
                        return;
                    }
                    // default select all services from this package
                    $serviceIds = $package->services->pluck('id')->toArray();
                    $set('selected_service_ids', $serviceIds);

                    // Harga paket asli
                    $packagePrice = (float) ($package->price ?? 0);
                    $set('package_price', $packagePrice);

                    // Hitung unselected (awal = none)
                    $unselectedServices = $package->services->whereNotIn('id', $serviceIds);

                    $unselectedTotal = $unselectedServices->sum(function ($service) {
                        $pivotPrice = (float) ($service->pivot->value_price ?? 0);

                        // Jika value_price > 0 → gunakan itu
                        if ($pivotPrice > 0) return $pivotPrice;

                        // Jika value_price = 0 → fallback ke harga_layanan sebenarnya
                        return (float) ($service->harga_layanan ?? 0);
                    });

                    $basePrice = max(0, $packagePrice - $unselectedTotal);

                    $set('base_price', $basePrice);
                    $set('total_price', $basePrice);
                }),

                // show package list price (not stored in DB) so admin can compare
                TextInput::make('package_price')
                    ->label('Harga Paket (daftar)')
                    ->numeric()
                    ->prefix('Rp')
                    ->disabled()
                    ->hidden(fn ($livewire) => 
                        $livewire instanceof \Filament\Resources\Pages\ViewRecord
                        || $livewire instanceof \Filament\Resources\Pages\ListRecords
                    )
                    ->dehydrated(false),

                // Tampilan view
                Textarea::make('package_services_display')
                    ->label('Layanan Paket (Dipilih)')
                    // ->bulleted()
                    ->disabled()
                    ->rows(8)
                    ->dehydrated(false)
                    ->formatStateUsing(function ($record) {
                        // Jika tidak ada record, tampilkan tanda '-' (misal mode create)
                        if (! $record || ! $record->exists) {
                            return '-';
                        }
                        // Ambil layanan yang tersimpan pada order (snapshot di order_services)
                        $services = $record->services()
                            ->where('is_required', true)
                            ->pluck('service_name')
                            ->toArray();

                        return ! empty($services) ? implode("\n", $services) : 'Tidak ada layanan terpilih';
                    })
                    ->hidden(fn ($livewire) => !(
                        $livewire instanceof \Filament\Resources\Pages\ViewRecord
                        || $livewire instanceof \Filament\Resources\Pages\ListRecords
                    ))
                    ->columnSpanFull(),

                CheckboxList::make('selected_service_ids')
                    ->label('Layanan Paket (centang jika ingin dibatalkan ')
                    ->columns(3)
                    ->options(function ($get) {
                        $packageId = $get('package_id');
                        if (! $packageId) return [];

                        $package = \App\Models\Package::find($packageId);
                        if (! $package) return [];

                        // show only service names
                        return $package->services->pluck('name', 'id')->toArray();
                    })
                    ->reactive()
                    ->columnSpanFull()
                    ->hidden(fn ($livewire) => 
                        $livewire instanceof \Filament\Resources\Pages\ViewRecord
                        || $livewire instanceof \Filament\Resources\Pages\ListRecords
                    )
                    ->afterStateUpdated(function ($get, $set) {
                        $packageId = $get('package_id');
                        if (! $packageId) {
                            $set('base_price', 0);
                            $set('total_price', 0);
                            return;
                        }

                        $package = \App\Models\Package::find($packageId);
                        $packagePrice = (float) ($package->price ?? 0);

                        $serviceIds = $get('selected_service_ids') ?? [];
                        $unselectedServices = $package->services->whereNotIn('id', $serviceIds);

                        // FIX: gunakan value_price > 0, jika tidak → fallback harga_layanan
                        $unselectedTotal = $unselectedServices->sum(function ($service) {
                            $pivotPrice = (float) ($service->pivot->value_price ?? 0);

                            if ($pivotPrice > 0) return $pivotPrice;

                            return (float) ($service->harga_layanan ?? 0);
                        });

                        $basePrice = max(0, $packagePrice - $unselectedTotal);

                        // Optional services
                        $optionalIds = $get('optional_service_ids') ?? [];
                        $optionalTotal = 0;

                        if (!empty($optionalIds)) {
                            $optional = \App\Models\Services::whereIn('id', $optionalIds)->get();
                            $optionalTotal = (float) $optional->sum(fn ($s) => (float) ($s->harga_layanan ?? 0));
                        }

                        $set('base_price', $basePrice);
                        $set('total_price', $basePrice + $optionalTotal);
                    }),

                // Optional services (global list) -- admin can add services outside the package
                Textarea::make('optional_services_display')
                    ->label('Layanan Tambahan (Dipilih)')
                    // ->bulleted()
                    ->disabled()
                    ->rows(6)
                    ->dehydrated(false)
                    ->formatStateUsing(function ($record) {
                        if (! $record || ! $record->exists) return '-';

                        // Ambil layanan opsional yang disimpan pada order (order_services snapshot)
                        $services = $record->services()
                            ->where('is_required', false)
                            ->whereNull('package_id')
                            ->pluck('service_name')
                            ->toArray();

                        return ! empty($services) ? implode("\n", $services) : 'Tidak ada layanan tambahan';
                    })
                    ->hidden(fn ($livewire) => !(
                        $livewire instanceof \Filament\Resources\Pages\ViewRecord
                        || $livewire instanceof \Filament\Resources\Pages\ListRecords
                    ))
                    ->columnSpanFull(),

                CheckboxList::make('optional_service_ids')
                    ->label('Layanan Tambahan (Opsional)')
                    ->options(function () {
                        $services = \App\Models\Services::where('is_active', true)->orderBy('name')->get();
                        return $services->mapWithKeys(fn ($s) => [ 
                            $s->id => $s->name . ' — Rp ' . number_format((float)$s->harga_layanan ?? 0, 0, ',', '.') 
                        ])->toArray();
                    })
                    ->reactive()
                    ->live(debounce: '0ms')
                    ->columns(3)
                    ->columnSpanFull()
                    ->hidden(fn ($livewire) => 
                        $livewire instanceof \Filament\Resources\Pages\ViewRecord
                        || $livewire instanceof \Filament\Resources\Pages\ListRecords
                    )
                    ->afterStateUpdated(function ($get, $set) {
                        // recompute total when optional services change
                        $packageId = $get('package_id');
                        $basePrice = 0;

                        if ($packageId) {
                            $package = \App\Models\Package::find($packageId);
                            $packagePrice = (float) ($package->price ?? 0);
                            $serviceIds = $get('selected_service_ids') ?? [];
                            $unselectedServices = $package->services->whereNotIn('id', $serviceIds);
                            
                            // FIX: gunakan value_price > 0, jika tidak → fallback harga_layanan (konsisten dengan selected logic)
                            $unselectedTotal = $unselectedServices->sum(function ($service) {
                                $pivotPrice = (float) ($service->pivot->value_price ?? 0);
                                if ($pivotPrice > 0) return $pivotPrice;
                                return (float) ($service->harga_layanan ?? 0);
                            });

                            $basePrice = max(0, $packagePrice - $unselectedTotal);
                        }

                        $optionalIds = $get('optional_service_ids') ?? [];
                        $optionalTotal = 0;
                        if (! empty($optionalIds)) {
                            $optional = \App\Models\Services::whereIn('id', $optionalIds)->get();
                            $optionalTotal = (float) $optional->sum(fn ($s) => (float) ($s->harga_layanan ?? 0));
                        }

                        $set('base_price', $basePrice);
                        $set('total_price', $basePrice + $optionalTotal);
                    }),
                TextInput::make('base_price')
                    ->label('Harga Paket (Terpilih)')
                    ->numeric()
                    ->prefix('Rp')
                    ->disabled()
                    ->dehydrated()
                    ->columnSpanFull(),

                TextInput::make('total_price')
                    ->label('Total Harga')
                    ->numeric()
                    ->prefix('Rp')
                    ->disabled()
                    ->dehydrated()
                    ->columnSpanFull(),
            ]);
    }
}
