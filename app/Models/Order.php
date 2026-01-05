<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class Order extends Model
{

    protected $casts = [
        'bukti_pembayaran' => 'array',
        'event_date' => 'datetime',
        'payment_approved_at' => 'datetime',
    ];

    protected $fillable = [
        'user_id',
        'package_id',
        'order_code',
        'event_date',
        'acara',
        'base_price',
        'total_price',
        'status',
        'payment_status',
        'payment_note',
        'payment_approved_at',
        'payment_approved_by',
        'bukti_pembayaran',
        'alamat',
        'notes',
    ];

    protected static function booted()
    {
        static::updating(function ($order) {

            if ($order->isDirty('bukti_pembayaran')) {

                $oldFiles = (array) $order->getOriginal('bukti_pembayaran');
                $newFiles = (array) $order->bukti_pembayaran;
                $deletedFiles = array_diff($oldFiles, $newFiles);
                foreach ($deletedFiles as $file) {
                    Storage::disk('public')->delete($file);
                }
            }
        });

        static::deleting(function ($order) {
            foreach ((array) $order->bukti_pembayaran as $file) {
                Storage::disk('public')->delete($file);
            }
        });

        static::updating(function ($order) {
            if ($order->isDirty('status')) {
                OrderHistories::create([
                    'order_id' => $order->id,
                    'old_status' => $order->getOriginal('status'),
                    'new_status' => $order->status,
                    'changed_by' => Auth::id(),
                ]);
            }
            if ($order->isDirty('bukti_pembayaran')) {
                $oldFiles = (array) $order->getOriginal('bukti_pembayaran');
                $newFiles = (array) $order->bukti_pembayaran;
                $deletedFiles = array_diff($oldFiles, $newFiles);

                foreach ($deletedFiles as $file) {
                    Storage::disk('public')->delete($file);
                }
            }
        });
        static::deleting(function ($order) {
            foreach ((array) $order->bukti_pembayaran as $file) {
                Storage::disk('public')->delete($file);
            }
        });

        // // sinkronkan status pembayaran otomatis
        // static::saving(function ($order) {
        //     if ($order->isDirty('status')) {
        //         $order->payment_status = match ($order->status) {
        //             'confirmed' => 'unpaid',
        //             'paid in progress' => 'paid in progress',
        //             'paid completed' => 'paid completed',
        //             default => $order->payment_status,
        //         };
        //     }
        // });
    }

    public function paymentApprovedBy()
    {
        return $this->belongsTo(User::class, 'payment_approved_by');
    }

    public function getPaymentStatusLabel(): string
    {
        return match ($this->status) {
            'confirmed' => 'Unpaid',
            'paid in progress' => 'Paid | In Progress',
            'paid completed' => 'Paid | Completed',
            default => ucfirst($this->status),
        };
    }

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
        $defaultServices = $this->package->services()
            ->get()
            ->keyBy('id');
        $selectedServices = $this->services()->get();
        foreach ($defaultServices as $serviceId => $service) {
            if (!$selectedServices->where('service_id', $serviceId)->first()) {
                $price = (float) ($service->pivot->value_price ?? 0);
                if ($price <= 0) {
                    $price = (float) ($service->harga_layanan ?? 0);
                }
                $total -= $price;
            }
        }
        foreach ($selectedServices as $orderService) {
            if ($orderService->is_custom) {
                $total += (float) ($orderService->price ?? 0);
            }
        }
        $this->total_price = max(0, $total);
        $this->save();
    }

    public function histories()
    {
        return $this->hasMany(OrderHistories::class);
    }
}
