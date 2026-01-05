<?php

namespace App\Livewire\Pelanggan;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithFileUploads;
use App\Models\Order;
use App\Models\Package;
use App\Models\Services;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

#[Layout('layouts.app')]
class OrdersCreate extends Component
{
    use WithFileUploads;

    public $event_date;
    public $acara;
    public $package_id;
    public $selected_service_ids = [];
    public $optional_service_ids = [];
    public $alamat;
    public $notes;
    public $bukti_pembayaran = [];
    public $base_price = 0;
    public $total_price = 0;
    public $selected_package = null;
    public $package_services = [];
    public $order_code = '';
    public $customer_name = '';

    /**
     * Update base_price dan total_price ketika paket dipilih
     * Logic sama seperti admin form dengan pivot value_price support
     */
    public function updatedPackageId($value)
    {
        if ($value) {
            $package = Package::find($value);
            if ($package) {
                $this->selected_package = $package;
                // Ambil services dari paket dengan column yang spesifik
                $this->package_services = $package->services()->pluck('services.id')->toArray();
                // Set services dari paket ke selected_service_ids (user bisa customize)
                $this->selected_service_ids = $this->package_services;
                // Reset optional services ketika paket berubah
                $this->optional_service_ids = [];
                // Calculate base_price with all services selected
                $this->calculateBasePriceFromPackage($package, $this->selected_service_ids);
            }
        } else {
            $this->selected_package = null;
            $this->package_services = [];
            $this->base_price = 0;
            $this->selected_service_ids = [];
            $this->optional_service_ids = [];
            $this->total_price = 0;
        }
    }

    /**
     * Update base_price dan total_price ketika selected services berubah
     */
    public function updatedSelectedServiceIds()
    {
        if ($this->selected_package && $this->package_id) {
            $this->calculateBasePriceFromPackage(
                $this->selected_package,
                $this->selected_service_ids
            );
        }
    }

    /**
     * Update total_price ketika optional services berubah
     */
    public function updatedOptionalServiceIds()
    {
        $this->calculateTotalPrice();
    }

    /**
     * Calculate base_price dari package price minus unselected services
     * Menggunakan pivot value_price jika tersedia, fallback ke harga_layanan
     * Logic sama seperti admin OrderForm
     */
    protected function calculateBasePriceFromPackage(Package $package, array $selectedServiceIds): void
    {
        $packagePrice = (float) ($package->price ?? 0);
        
        // Hitung harga service yang NOT dipilih (unselected)
        $unselectedServices = $package->services->whereNotIn('id', $selectedServiceIds);
        
        $unselectedTotal = $unselectedServices->sum(function ($service) {
            // Cek pivot value_price untuk discount
            $pivotPrice = (float) ($service->pivot->value_price ?? 0);
            
            // Jika value_price > 0 → gunakan itu (discounted price)
            if ($pivotPrice > 0) return $pivotPrice;
            
            // Jika value_price = 0 → fallback ke harga_layanan sebenarnya
            return (float) ($service->harga_layanan ?? 0);
        });
        
        // Base price = original package price - unselected services total
        $basePrice = max(0, $packagePrice - $unselectedTotal);
        
        $this->base_price = $basePrice;
        
        // Recalculate total dengan base price yang baru
        $this->calculateTotalPrice();
    }

    /**
     * Hitung total_price dari base_price + hanya optional services yang dipilih
     */
    protected function calculateTotalPrice(): void
    {
        // Total = base_price + only optional services
        $total = $this->base_price;

        // Tambahkan hanya optional services
        if (!empty($this->optional_service_ids)) {
            $optionalServices = Services::whereIn('id', $this->optional_service_ids)->get();
            foreach ($optionalServices as $service) {
                $total += $service->harga_layanan ?? 0;
            }
        }

        $this->total_price = $total;
    }

    public function save()
    {
        $this->validate([
            'event_date' => 'required|date',
            'acara' => 'required|string|max:255',
            'package_id' => 'required|exists:packages,id',
            'selected_service_ids' => 'array',
            'selected_service_ids.*' => 'exists:services,id',
            'optional_service_ids' => 'array',
            'optional_service_ids.*' => 'exists:services,id',
            'alamat' => 'required|string',
            'bukti_pembayaran.*' => 'image|max:2048',
            'base_price' => 'numeric|min:0',
            'total_price' => 'numeric|min:0',
        ]);

        $files = [];
        foreach ($this->bukti_pembayaran as $file) {
            $files[] = $file->store('bukti-pembayaran', 'public');
        }

        // Generate unique order code
        $orderCode = $this->order_code;
        while (Order::where('order_code', $orderCode)->exists()) {
            $orderCode = 'ORD-' . now()->format('Ymd') . '-' . Str::upper(Str::random(6));
        }

        $order = Order::create([
            'user_id' => Auth::id(),
            'package_id' => $this->package_id,
            'order_code' => $orderCode,
            'event_date' => $this->event_date,
            'acara' => $this->acara,
            'alamat' => $this->alamat,
            'notes' => $this->notes,
            'status' => 'pending',
            'payment_status' => 'unpaid',
            'bukti_pembayaran' => $files,
            'base_price' => $this->base_price,
            'total_price' => $this->total_price,
        ]);

        // Sync services sama seperti admin
        $this->syncServices($order);

        session()->flash('success', 'Pesanan berhasil dibuat');
        return redirect()->route('pelanggan.pesanan');
    }

    protected function syncServices(Order $order): void
    {
        // Remove any existing service snapshots
        $order->services()->delete();

        // Combine both selected and optional service IDs
        $selectedServiceIds = array_unique(array_merge(
            $this->selected_service_ids ?? [],
            $this->optional_service_ids ?? []
        ));

        if (empty($selectedServiceIds)) {
            return;
        }

        $services = Services::whereIn('id', $selectedServiceIds)->get();

        foreach ($services as $service) {
            $order->services()->create([
                'service_id' => $service->id,
                'service_name' => $service->name,
                'price' => $service->harga_layanan ?? 0,
            ]);
        }
    }

    public function render()
    {
        // Generate order code jika belum ada
        if (empty($this->order_code)) {
            $this->order_code = 'ORD-' . now()->format('Ymd') . '-' . Str::upper(Str::random(6));
        }

        // Get customer name
        if (empty($this->customer_name) && Auth::check()) {
            $this->customer_name = Auth::user()->name;
        }

        return view('livewire.pelanggan.orders-create', [
            'packages' => Package::all(),
            'services' => Services::all(),
            'selected_package' => $this->selected_package,
            'package_services' => $this->package_services
        ]);
    }
}
