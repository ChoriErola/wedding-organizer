<?php

namespace App\Filament\Resources\Orders\Schemas;

use Filament\Schemas\Schema;
use App\Models\Package;
use App\Models\Services;
use Filament\Forms\Components\Select;
use Illuminate\Support\Str;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;

class EditOrderForm
{
    public static function configure(Schema $schema): Schema
    {
        $calculatePrice = function ($get, $set, $livewire) {
            $packageId = $get('package_id');
            if (! $packageId) {
                $set('base_price', 0);
                $set('total_price', 0);
                return;
            }
            
            $package = Package::find($packageId);
            if (! $package) {
                $set('base_price', 0);
                $set('total_price', 0);
                return;
            }

            $packagePrice = (float) ($package->price ?? 0);
            $serviceIds = $get('selected_service_ids') ?? [];
            
            // Tentukan apakah menggunakan snapshot logic atau new package logic
            $order = $livewire->record ?? null;
            $isNewOrder = !$order || !$order->exists;
            $isPackageChanged = $order && $order->exists && (string) $packageId !== (string) $order->package_id;
            
            $unselectedTotal = 0;
            
            if (!$isNewOrder && !$isPackageChanged) {
                // SNAPSHOT LOGIC: Gunakan harga dari snapshot order yang tersimpan
                $snapshotServices = $order->services()
                    ->where('package_id', $packageId)
                    ->get()
                    ->keyBy('service_id');
                
                foreach ($snapshotServices as $serviceId => $snap) {
                    if (!in_array((string)$serviceId, $serviceIds)) {
                        $unselectedTotal += (float)$snap->price;
                    }
                }
            } else {
                // NEW PACKAGE LOGIC: Gunakan harga dari package master
                $pkgWithServices = Package::with('services')->find($packageId);
                if ($pkgWithServices) {
                    $unselectedServices = $pkgWithServices->services->whereNotIn('id', $serviceIds);
                    $unselectedTotal = (float) $unselectedServices->sum(function ($service) {
                        $pivotPrice = (float) ($service->pivot->value_price ?? 0);
                        if ($pivotPrice > 0) return $pivotPrice;
                        return (float) ($service->harga_layanan ?? 0);
                    });
                }
            }
            
            $basePrice = max(0, $packagePrice - $unselectedTotal);
            
            $optionalIds = $get('optional_service_ids') ?? [];
            $optionalTotal = 0;
            if (!empty($optionalIds)) {
                $optional = Services::whereIn('id', $optionalIds)->get();
                $optionalTotal = (float) $optional->sum(fn ($s) => (float) ($s->harga_layanan ?? 0));
            }
            
            $set('base_price', $basePrice);
            $set('total_price', $basePrice + $optionalTotal);
        };
        
        return $schema
            ->components([
                Select::make('user_id')
                    ->relationship('customer', 'name')
                    ->label('Customer')
                    ->searchable()
                    ->preload()
                    ->reactive()
                    ->disabled(fn ($livewire) => isset($livewire->record) && $livewire->record->status !== 'draft')
                    ->required(),
                TextInput::make('order_code')
                    ->default(fn () => 'ORD-' . now()->format('Ymd') . '-' . Str::upper(Str::random(8)))
                    ->disabled()
                    ->dehydrated(),
                DatePicker::make('event_date')
                    ->label('Tanggal Acara')
                    ->disabled(fn ($livewire) => isset($livewire->record) && !in_array($livewire->record->status, ['draft', 'pending']))
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
                    ->disabled(fn ($livewire) => isset($livewire->record) && $livewire->record->status !== 'draft')
                    ->required(),
                Textarea::make('alamat')
                    ->label('Alamat Acara')
                    ->required()
                    ->disabled(fn ($livewire) => isset($livewire->record) && !in_array($livewire->record->status, ['draft', 'pending']))
                    ->columnSpanFull(),
                Textarea::make('notes')
                    ->label('Catatan')
                    ->disabled(fn ($livewire) => isset($livewire->record) && !in_array($livewire->record->status, ['draft', 'pending']))
                    ->columnSpanFull(),
                Select::make('package_id')
                    ->relationship('package', 'name')
                    ->label('Paket')
                    ->searchable()
                    ->preload()
                    ->reactive()
                    ->disabled(fn ($livewire) => isset($livewire->record) && $livewire->record->status !== 'draft')
                    ->required()
                    ->afterStateUpdated(function ($state, $set, $get, $livewire) use ($calculatePrice) {
                        $packageId = $state;
                        $record = $livewire->record ?? null;
                        
                        // Cek apakah ada paket, jika tidak, reset semua
                        if (! $packageId) {
                            $set('selected_service_ids', []);
                            $set('base_price', 0);
                            $set('total_price', 0);
                            return;
                        }
                        $package = Package::find($packageId);
                        if (! $package) {
                            $set('selected_service_ids', []);
                            $set('base_price', 0);
                            $set('total_price', 0);
                            return;
                        }
                        $isPackageChanged = $record && $record->exists && (string) $packageId !== (string) $record->package_id;
                        if (! $record || $record->status === 'draft' || $isPackageChanged) {
                            $serviceIds = $package->services
                                ->pluck('id')
                                ->map(fn ($id) => (string) $id)
                                ->toArray();
                            // set semua service id ke checkbox (centang semua untuk paket baru/draft)
                            $set('selected_service_ids', $serviceIds);
                            // $set('selected_service_ids', []);
                        } 
                        // perhitungan harga
                        $set('package_price', (float) ($package->price ?? 0));
                        // $set('optional_service_ids', $get('optional_service_ids')); // Trigger price calc
                        $calculatePrice($get, $set, $livewire);
                    }),
                TextInput::make('package_price')
                    ->label('Harga Paket')
                    ->numeric()
                    ->prefix('Rp')
                    ->disabled()
                    ->hidden(fn ($livewire) => 
                        $livewire instanceof \Filament\Resources\Pages\ViewRecord
                        || $livewire instanceof \Filament\Resources\Pages\ListRecords
                    )
                    ->dehydrated(false),
                Textarea::make('package_services_display')
                    ->label('Layanan Paket (Dipilih)')
                    // ->bulleted()
                    ->disabled()
                    ->rows(8)
                    ->dehydrated(false)
                    ->formatStateUsing(function ($record) {
                        if (! $record || ! $record->exists) {
                            return '-';
                        }
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
                ->label('Layanan Paket & Tambahan (Tersimpan)')
                ->columns(3)
                ->options(function ($get, $livewire) {
                    $formPackageId = $get('package_id');
                    if (!$formPackageId) return [];
                    
                    $package = Package::find($formPackageId);
                    if (!$package) return [];
                    
                    // Selalu kembalikan services dari paket yang dipilih saat ini
                    $options = $package->services->pluck('name', 'id')->toArray();
                    
                    // Jika ada order existing, tambah optional services yang sudah disimpan
                    $record = $livewire->record ?? null;
                    if ($record && $record->exists) {
                        $optionalServices = $record->services()
                            ->where('is_required', false)
                            ->whereNull('package_id')
                            ->pluck('service_name', 'service_id')
                            ->toArray();
                        $options = array_merge($options, $optionalServices);
                    }
                    
                    return $options;
                })
                ->afterStateHydrated(function ($component, $livewire) {
                    $record = $livewire->record ?? null;
                    if (! $record || ! $record->exists) return;

                    // Ambil semua saved service IDs
                    $savedIds = $record->services()
                        ->pluck('service_id')
                        ->map(fn($x) => (string)$x)
                        ->toArray();
                    
                    // Filter hanya yang ada di opsi saat ini (paket + optional)
                    $validOptions = array_map('strval', array_keys($component->getOptions() ?? []));
                    $filtered = array_values(array_intersect($savedIds, $validOptions));
                    
                    $component->state($filtered);
                })
                ->afterStateUpdated(function ($get, $set, $livewire) use ($calculatePrice) {
                    $calculatePrice($get, $set, $livewire);
                })
                ->reactive()
                ->dehydrated(true)
                ->validationAttribute('Layanan Paket & Tambahan (Tersimpan)')
                ->disabled(fn ($livewire) => isset($livewire->record) && $livewire->record->status !== 'draft')
                ->columnSpanFull()
                ->hidden(fn ($livewire) =>
                    $livewire instanceof \Filament\Resources\Pages\ViewRecord
                    || $livewire instanceof \Filament\Resources\Pages\ListRecords
                ),
                Textarea::make('optional_services_display')
                    ->label('Layanan Tambahan (Dipilih)')
                    ->disabled()
                    ->rows(6)
                    ->dehydrated(false)
                    ->formatStateUsing(function ($record) {
                        if (! $record || ! $record->exists) return '-';
                        $services = $record->services()
                            ->where('is_required', false)
                            ->whereNull('package_id')
                            ->pluck('service_name')
                            ->toArray();
                        return ! empty($services) ? implode("\n", $services) : 'Tidak ada layanan tambahan';
                    })
                    ->visible(fn ($livewire) => (
                        $livewire instanceof \Filament\Resources\Pages\ViewRecord
                        || $livewire instanceof \Filament\Resources\Pages\ListRecords
                    ))
                    ->columnSpanFull(),
                CheckboxList::make('optional_service_ids')
                    ->label('Layanan Tambahan (Opsional)')
                    ->options(function () {
                        $services = Services::where('is_active', true)->orderBy('name')->get();
                        return $services->mapWithKeys(fn ($s) => [ 
                            $s->id => $s->name . ' — Rp ' . number_format((float)$s->harga_layanan ?? 0, 0, ',', '.') 
                        ])->toArray();
                    })
                    ->reactive()
                    ->live(debounce: '0ms')
                    ->columns(3)
                    ->disabled(fn ($livewire) =>
                        isset($livewire->record)
                        && ! in_array($livewire->record->status, ['draft', 'pending'])
                    )
                    ->visible(fn ($livewire) => (
                        $livewire instanceof \Filament\Resources\Pages\EditRecord
                        && in_array($livewire->record?->status, ['draft', 'pending'])
                    ))
                    ->columnSpanFull()
                    ->afterStateUpdated(function ($get, $set, $livewire) use ($calculatePrice) {
                        $calculatePrice($get, $set, $livewire);
                    }),
                TextInput::make('base_price')
                    ->label('Harga Paket (Terpilih)')
                    ->prefix('Rp')
                    ->numeric()
                    ->disabled(),
                TextInput::make('total_price')
                    ->label('Total Harga')
                    ->prefix('Rp')
                    ->numeric()
                    ->disabled(),
            ]);
    }
}
