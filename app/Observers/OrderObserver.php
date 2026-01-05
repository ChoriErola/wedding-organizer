<?php

namespace App\Observers;

use App\Models\Order;
use App\Models\OrderHistories;
use Illuminate\Support\Facades\Auth;

// class OrderObserver
// {
//     /**
//      * Handle the Order "created" event.
//      */
//     public function created(Order $order): void
//     {
//         //
//     }

//     /**
//      * Handle the Order "updated" event.
//      */
//     public function updated(Order $order): void
//     {
//         // Ambil kolom apa saja yang berubah di database
//         $changes = $order->getDirty();

//         // Jika tidak ada perubahan data, jangan buat riwayat
//         if (empty($changes)) {
//             return;
//         }

//         $notes = [];

//         // 1. Deteksi Perubahan Harga (Akibat Tambah Layanan)
//         if (isset($changes['total_price'])) {
//             $oldPrice = number_format($order->getOriginal('total_price'), 0, ',', '.');
//             $newPrice = number_format($order->total_price, 0, ',', '.');
//             $notes[] = "Total harga berubah dari Rp$oldPrice menjadi Rp$newPrice";
//         }

//         // 2. Deteksi Perubahan Status
//         if (isset($changes['status'])) {
//             $oldStatus = strtoupper($order->getOriginal('status'));
//             $newStatus = strtoupper($order->status);
//             $notes[] = "Status berubah dari $oldStatus menjadi $newStatus";
//         }

//         // Jika ada catatan perubahan, simpan ke tabel order_histories
//         if (!empty($notes)) {
//             OrderHistories::create([
//                 'order_id'           => $order->id,
//                 'old_status'         => $order->getOriginal('status'),
//                 'new_status'         => $order->status,
//                 'note'               => implode(" | ", $notes),
//                 'changed_by'         => Auth::id() ?? $order->user_id,
//             ]);
//         }
//     }

//     /**
//      * Handle the Order "deleted" event.
//      */
//     public function deleted(Order $order): void
//     {
//         //
//     }

//     /**
//      * Handle the Order "restored" event.
//      */
//     public function restored(Order $order): void
//     {
//         //
//     }

//     /**
//      * Handle the Order "force deleted" event.
//      */
//     public function forceDeleted(Order $order): void
//     {
//         //
//     }
// }
