<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Filament\Resources\Orders\OrderResource;
use App\Models\Order;
use App\Models\Package;
use App\Models\Services;
use App\Filament\Resources\Orders\Schemas\EditOrderForm;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Schema;

class EditOrder extends EditRecord
{
    protected ?array $temporary_selected_service_ids = null;
    protected ?array $temporary_optional_service_ids = null;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    
    protected static string $resource = OrderResource::class;

    public function form(Schema $schema): Schema
    {
        return EditOrderForm::configure($schema);
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function prepareFormData(array $data): array
    {
        // Pastikan selected_service_ids adalah array
        if (isset($data['selected_service_ids']) && !is_array($data['selected_service_ids'])) {
            $data['selected_service_ids'] = [];
        }
        
        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->temporary_selected_service_ids = $data['selected_service_ids'] ?? null;
        $this->temporary_optional_service_ids = $data['optional_service_ids'] ?? null;
        // Remove UI-only fields before saving
        unset(
            $data['selected_service_ids'], 
            $data['optional_service_ids'], 
            $data['package_services_list'], 
            $data['optional_services_list'], 
            $data['package_price'], 
            $data['package_services_display'], 
            $data['optional_services_display']);
        return $data;
    }
    
    protected function mutateFormDataToFill(array $data): array
    {
        $order = $this->record;
        if (empty($data['package_id']) && ! empty($order->package_id)) {
            $data['package_id'] = $order->package_id;
        }
        
        // Semua service IDs (paket + optional) masuk ke selected_service_ids
        $savedServiceIds = $order->services()
            ->pluck('service_id')
            ->map(fn ($id) => (string) $id) 
            ->toArray();

        // Identifikasi optional service IDs (adalah services dengan is_required=false dan package_id=null)
        $optionalIds = $order->services()
            ->where('is_required', false)
            ->whereNull('package_id')
            ->pluck('service_id')
            ->map(fn ($id) => (string) $id)
            ->toArray();

        $data['selected_service_ids'] = $savedServiceIds;
        $data['optional_service_ids'] = $optionalIds;

        $data['base_price'] = $order->base_price;
        $data['total_price'] = $order->total_price;
        
        if ($order->package) {
            $data['package_price'] = $order->package->price;
        }

        return $data;
    }

    protected function afterSave(): void
    {
        $state = $this->form->getState();
        if ($this->temporary_selected_service_ids !== null) {
        $state['selected_service_ids'] = $this->temporary_selected_service_ids;
        }
        if ($this->temporary_optional_service_ids !== null) {
            $state['optional_service_ids'] = $this->temporary_optional_service_ids;
        }

        $this->temporary_selected_service_ids = null;
        $this->temporary_optional_service_ids = null;

        $this->syncServices($this->record, $state);
    }

    protected function syncServices(Order $order, array $state): void
    {
        $packageId = $order->package_id;
        $order->services()->delete();
        
        // Saat edit, semua services ada di selected_service_ids
        $selectedIds = $state['selected_service_ids'] ?? [];
        
        $masterPackageServiceIds = [];
        $package = null;
        
        if ($packageId) {
            $package = Package::find($packageId);
            if ($package) {
                $masterPackageServiceIds = $package->services->pluck('id')->toArray();
            }
        }

        foreach ($selectedIds as $serviceId) {
            $serviceMaster = Services::find($serviceId);
            if (! $serviceMaster) continue;
            
            if (in_array($serviceId, $masterPackageServiceIds)) {
                // Layanan dari master package → simpan sebagai is_required=true
                $pivotPrice = 0;
                if ($package) {
                    $pivotItem = $package->services->where('id', $serviceId)->first();
                    if ($pivotItem) {
                        $pivotPrice = (float) ($pivotItem->pivot->value_price ?? 0);
                        if ($pivotPrice <= 0) {
                            $pivotPrice = (float) ($serviceMaster->harga_layanan ?? 0);
                        }
                    }
                }

                $order->services()->create([
                    'service_id'   => $serviceMaster->id,
                    'package_id'   => $packageId,
                    'service_name' => $serviceMaster->name,
                    'price'        => $pivotPrice,
                    'is_required'  => true,
                    'is_custom'    => false,
                ]);
            } else {
                // Layanan optional → simpan sebagai optional
                $order->services()->create([
                    'service_id'   => $serviceMaster->id,
                    'package_id'   => null,
                    'service_name' => $serviceMaster->name,
                    'price'        => $serviceMaster->harga_layanan ?? 0,
                    'is_required'  => false,
                    'is_custom'    => false,
                ]);
            }
        }
        
        $order->update([
            'base_price'  => $state['base_price'] ?? 0,
            'total_price' => $state['total_price'] ?? 0,
        ]);
    }
}
