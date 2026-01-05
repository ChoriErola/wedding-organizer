<?php

namespace App\Services;

use App\Invoices\CompanySeller;
use App\Models\ContactUs;
use App\Models\Order;
use LaravelDaily\Invoices\Invoice;
use LaravelDaily\Invoices\Classes\Buyer;
use LaravelDaily\Invoices\Classes\InvoiceItem;
use LaravelDaily\Invoices\Classes\Seller;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\View;

class OrderInvoiceService
{
    public static function generate(Order $order)
    {

        // Gunakan filter_var untuk memastikan string benar-benar bersih dari karakter non-ASCII
        $clean = fn($str) => preg_replace('/[[:^print:]]/', '', (string) $str);

        $logoPath = public_path('images/p_project_indonesia.jpg');

        $seller = CompanySeller::dynamic();

        // 3. Setup Data Buyer (Pelanggan)
        $customer = new Buyer([
            'name'          => $clean($order->customer['name'] ?? 'Pelanggan'), 
            'address'       => $clean($order->alamat),
            'custom_fields' => [
                'Email' => $clean($order->customer['email'] ?? '-'),
            ],
        ]);

        $items = [];
        // Menambahkan item utama dari paket
        $items[] = (new InvoiceItem())
            ->title($clean($order->package['name'] ?? 'Paket Wedding'))
            ->pricePerUnit((float) $order->base_price)
            ->quantity(1);

        // Menambahkan hanya layanan tambahan (is_custom = 1) jika ada
        $serviceNotes = '';
        $packageServiceIds = [];
        if ($order->package) {
            $packageServiceIds = $order->package
                ->services
                ->pluck('id')
                ->toArray();
        }

        foreach ($order->services as $service) {
            // layanan tambahan = tidak ada di paket
            if (! in_array($service->service_id, $packageServiceIds)) {
                $items[] = (new InvoiceItem())
                    ->title($clean($service->service_name))
                    ->pricePerUnit((float) $service->price)
                    ->quantity(1);

                if (! empty($service->notes)) {
                    $serviceNotes .= ($serviceNotes ? "\n\n" : "") . $clean($service->notes);
                }
            }
        }
        
        // Gabungkan order notes dan service notes dengan keterangan
        $notes = [];
        // Catatan Order
        if (!empty($order->notes)) {
            $notes[] = "<strong>Catatan Order:</strong><br> " . nl2br($clean($order->notes));
        }
        // Catatan Layanan Tambahan
        $paymentRows = [];

        if (! empty($order->payment_note)) {
            $paymentRows = collect(explode("\n", $order->payment_note))
                ->map(function ($line) {
                    [$label, $date, $amount] = array_pad(
                        array_map('trim', explode('|', $line)),
                        3,
                        null
                    );

                    return [
                        'label' => $label,
                        'date' => $date,
                        'amount' => (float) $amount,
                    ];
                })
                ->filter(fn ($row) => $row['label'])
                ->values()
                ->toArray();
        }

        // Catatan Pembayaran (TAMPIL JIKA ADA)
        if (! empty($order->payment_note)) {
            $notes[] = "<strong>Catatan Pembayaran:</strong><br>" 
                . nl2br($clean($order->payment_note));
        }
        $allNotes = implode('<br><br>', $notes);

        // Render view with order data
        $html = View::make('vendor.invoices.templates.order_invoice', [
            'invoice' => new \stdClass(),
            'order' => $order,
            'seller' => $seller,
            'buyer' => $customer,
            'items' => $items,
            'allNotes' => $allNotes,
            'logoPath' => $logoPath,
            'paymentRows' => $paymentRows,
        ])->render();

        return Pdf::loadHTML($html)
            ->setOption('enable-local-file-access', true)
            ->output();
    }
}
