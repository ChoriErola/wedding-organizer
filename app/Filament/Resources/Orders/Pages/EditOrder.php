<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Filament\Resources\Orders\OrderResource;
use App\Filament\Resources\Orders\Schemas\EditOrderForm;
use App\Models\Order;
use App\Models\Services;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class EditOrder extends EditRecord
{
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
            Action::make('approve_payment')
                ->label('Konfirmasi Pembayaran')
                ->color('success')
                ->icon('heroicon-o-check-circle')
                ->visible(fn ($record) => $record->status === 'paid in progress')
                ->requiresConfirmation()
                ->action(function ($record) {
                    $record->update([
                        'status' => 'paid completed',
                        'payment_approved_by' => Auth::id(),
                        'payment_approved_at' => now(),
                    ]);
                })
                ->after(fn () => redirect($this->getResource()::getUrl('index'))),
            Action::make('reject_payment')
                ->label('Tolak Pembayaran')
                ->color('danger')
                ->icon('heroicon-o-x-circle')
                ->form([
                    Textarea::make('payment_note')
                        ->label('Alasan Penolakan')
                        ->required(),
                ])
                ->visible(fn ($record) => $record->status === 'paid in progress')
                ->action(function ($record, $data) {
                    $record->update([
                        'status' => 'paid in progress',
                        'payment_note' => $data['payment_note'],
                    ]);
                })
                ->after(fn () => redirect($this->getResource()::getUrl('index'))),
        ];

    }

    /**
     * Menyiapkan data sebelum ditampilkan di form Edit
     */
    protected function mutateFormDataToFill(array $data): array
    {
        $order = $this->record;
        
        $allServiceIds = $order->services()
            ->where(function ($query) {
                $query->where('is_required', true)
                      ->orWhere('is_custom', true);
            })
            ->pluck('service_id')
            ->map(fn ($id) => (string) $id)
            ->toArray();
        
        $data['all_service_ids'] = $allServiceIds;
        
        return $data;
    }

    /**
     * Membersihkan data "palsu" sebelum disimpan ke database tabel Orders
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        unset(
            $data['all_service_ids'],
            $data['package_price']
        );
        return $data;
    }

    /**
     * Logika yang dijalankan SETELAH data utama Order berhasil diupdate
     */
    protected function afterSave(): void
    {
        // $order = $this->record;
        $state = $this->form->getState();
        
        // Jalankan sinkronisasi tabel pivot
        if (
            array_key_exists('selected_service_ids', $state) ||
            array_key_exists('optional_service_ids', $state)
        ) {
            $this->syncServices($this->record, $state);
        }

        // update status otomatis
        if (
            ! empty($this->record->bukti_pembayaran) &&
            $this->record->status === 'confirmed'
        ) {
            $this->record->updateQuietly([
                'status' => 'paid in progress',
            ]);
        }
    }

    /**
     * Fungsi Helper untuk Sinkronisasi Layanan
     */
    protected function syncServices(Order $order, array $state): void
    {
        if (
        empty($state['selected_service_ids']) &&
        empty($state['optional_service_ids'])
        ) {
            return; // jangan hapus apa pun
        }
        // Hapus data lama dulu agar tidak duplikat saat save berkali-kali
        $order->services()->delete();
        
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
