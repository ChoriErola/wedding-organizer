<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Filament\Resources\Orders\OrderResource;
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

        $packageId = $order->package_id;

        $selected = $state['selected_service_ids'] ?? [];
        $optional = $state['optional_service_ids'] ?? [];

        // snapshot ALL package services (selected and unselected) to track what was removed
        if (! empty($packageId)) {
            $package = \App\Models\Package::find($packageId);
            if ($package) {
                foreach ($package->services as $service) {
                    $isSelected = in_array($service->id, $selected);
                    $price = (float) ($service->pivot?->value_price ?? $service->harga_layanan ?? 0);

                    $order->services()->create([
                        'service_id' => $service->id,
                        'package_id' => $packageId,
                        'service_name' => $service->name,
                        'price' => $price,
                        'is_required' => $isSelected ? 1 : 0,
                        'is_custom' => 0,
                    ]);
                }
            }
        }

        // optional global services
        if (! empty($optional)) {
            foreach ($optional as $serviceId) {
                $service = \App\Models\Services::find($serviceId);
                if (! $service) continue;

                $order->services()->create([
                    'service_id' => $service->id,
                    'package_id' => null,
                    'service_name' => $service->name,
                    'price' => $service->harga_layanan ?? 0,
                    'is_required' => false,
                    'is_custom' => true,
                ]);
            }
        }

        // Use the prices calculated client-side in the form (same as EditOrder)
        $order->update([
            'base_price' => $state['base_price'] ?? 0,
            'total_price' => $state['total_price'] ?? 0,
        ]);
    }
}
