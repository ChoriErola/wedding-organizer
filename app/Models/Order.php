<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'package_id',
        'order_code',
        'event_date',
        'base_price',
        'total_price',
        'status',
        'alamat',
        'notes',
    ];

    public function services()
    {
        return $this->hasMany(OrderService::class);
    }
    
    public function customer()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function package()
    {
        return $this->belongsTo(Package::class, 'package_id');
    }

    public function updateTotalPrice()
    {
        $basePrice = (float) ($this->package->price ?? 0);  
        $total = $basePrice;

        // Ambil service default paket dengan pivot data
        $defaultServices = $this->package->services()
            ->get()
            ->keyBy('id');

        // Ambil pilihan user dari order_services (snapshot)
        $selectedServices = $this->services()->get();

        // 1. Kurangi layanan default yang dihapus user
        foreach ($defaultServices as $serviceId => $service) {
            if (!$selectedServices->where('service_id', $serviceId)->first()) {
                // Gunakan pivot value_price, fallback ke harga_layanan jika 0
                $price = (float) ($service->pivot->value_price ?? 0);
                if ($price <= 0) {
                    $price = (float) ($service->harga_layanan ?? 0);
                }
                $total -= $price;
            }
        }

        // 2. Tambah layanan tambahan yang ditambahkan user (is_custom=true)
        foreach ($selectedServices as $orderService) {
            if ($orderService->is_custom) {
                $total += (float) ($orderService->price ?? 0);
            }
        }

        $this->total_price = max(0, $total);
    }
}
