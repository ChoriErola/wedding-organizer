<?php

namespace App\Livewire\Pelanggan;

use App\Models\Order;
use App\Services\OrderInvoiceService;
use Illuminate\Support\Facades\Auth;

class InvoiceShow
{
    public static function generate(Order $order)
    {
        // Pastikan user hanya bisa melihat invoice miliknya sendiri
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        // Load relasi
        $order->load(['customer', 'services', 'package']);

        // Generate dan return PDF langsung
        return response(OrderInvoiceService::generate($order), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="invoice-' . $order->order_code . '.pdf"');
    }
}
