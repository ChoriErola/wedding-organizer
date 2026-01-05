<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Filament\Resources\Orders\OrderResource;
use App\Models\Services;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreateOrder extends CreateRecord
{
    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }

    protected static string $resource = OrderResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Remove UI-only fields that should not be mass assigned directly
        unset($data['selected_service_ids'], $data['optional_service_ids']);

        // ensure order_code is unique: if a generated default collides, regenerate
        if (! empty($data['order_code']) && \App\Models\Order::where('order_code', $data['order_code'])->exists()) {
            $data['order_code'] = 'ORD-' . now()->format('Ymd') . '-' . Str::upper(Str::random(6));
        }

        // base_price and total_price are already computed client-side in the form — leave them
        return $data;
    }

    protected function afterCreate(): void
    {
        $state = $this->form->getState();

        $this->syncServices($this->record, $state);
    }

    protected function syncServices(\App\Models\Order $order, array $state): void
    {
        // remove any existing snapshots
        $order->services()->delete();

        // ambil HANYA yang dipilih
        $selectedServiceIds = array_unique(array_merge(
            $state['selected_service_ids'] ?? [],
            $state['optional_service_ids'] ?? []
        ));

        if (empty($selectedServiceIds)) return;

        $services = Services::whereIn('id', $selectedServiceIds)->get();

        foreach ($services as $service) {
            $order->services()->create([
                'service_id'   => $service->id,
                'service_name' => $service->name,
                'price'        => $service->harga_layanan ?? 0,
            ]);
        }

        $order->update([
            'base_price' => $state['base_price'] ?? 0,
            'total_price' => $state['total_price'] ?? 0,
        ]);
        
    }
}
