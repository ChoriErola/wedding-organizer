# 📌 Invoice Pelanggan - Quick Reference

## 1. Component Code (InvoiceShow.php)

```php
<?php

namespace App\Livewire\Pelanggan;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Order;
use App\Services\OrderInvoiceService;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.app')]
class InvoiceShow extends Component
{
    public Order $order;

    public function mount(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }
        $this->order = $order->load(['customer', 'services', 'package']);
    }

    public function downloadPdf()
    {
        return response(OrderInvoiceService::generate($this->order), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="invoice-' . $this->order->order_code . '.pdf"');
    }

    public function viewPdf()
    {
        return response(OrderInvoiceService::generate($this->order), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="invoice-' . $this->order->order_code . '.pdf"');
    }

    public function render()
    {
        return view('livewire.pelanggan.invoice-show');
    }
}
```

## 2. Route Definition (web.php)

```php
// Import at the top
use App\Livewire\Pelanggan\InvoiceShow;

// In the authenticated routes group
Route::middleware('auth')->group(function () {
    // ... existing routes ...
    
    // NEW: Invoice route
    Route::get('/pelanggan/pesanan/{order}/invoice', InvoiceShow::class)
        ->name('pelanggan.pesanan.invoice');
});
```

## 3. Orders List Button (orders.blade.php)

```blade
<!-- In card footer section -->
<div class="card-footer border-0 px-4 py-4"
    style="background-color: #a8729a; border-radius: 0 0 10px 10px;">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h6 class="text-white mb-0">
                Total Pembayaran :
                <span class="fw-bold">
                    Rp {{ number_format($order->total_price) }}
                </span>
            </h6>
        </div>
        <div>
            <a href="{{ route('pelanggan.pesanan.invoice', $order->id) }}" 
               class="btn btn-sm btn-light"
               style="background-color: white; color: #a8729a; border: none; padding: 8px 16px; font-weight: 600; border-radius: 5px; text-decoration: none; transition: all 0.3s;">
                <i class="bi bi-file-pdf" style="margin-right: 6px;"></i>Lihat Invoice
            </a>
        </div>
    </div>
</div>
```

## 4. Invoice Display Template Key Sections

### Header
```blade
<div class="border-b-2 border-gray-300 pb-6 mb-6">
    <div class="flex justify-between items-start mb-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">INVOICE</h1>
            <p class="text-gray-600 mt-2">Nomor Invoice: <strong>{{ $order->order_code }}</strong></p>
        </div>
        <div class="text-right">
            <p class="text-gray-600">Tanggal Invoice: <strong>{{ $order->created_at->translatedFormat('d F Y') }}</strong></p>
            <p class="text-gray-600">Tanggal Acara: <strong>{{ \Carbon\Carbon::parse($order->event_date)->translatedFormat('d F Y') }}</strong></p>
        </div>
    </div>
</div>
```

### Action Buttons
```blade
<div class="flex gap-4 mb-6">
    <button wire:click="viewPdf" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
        👁️ Lihat Invoice
    </button>
    <button wire:click="downloadPdf" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
        ⬇️ Unduh Invoice
    </button>
    <a href="{{ route('pelanggan.pesanan') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
        ← Kembali ke Pesanan
    </a>
</div>
```

### Services Table
```blade
<div class="overflow-x-auto">
    <table class="w-full">
        <thead>
            <tr class="bg-gray-200 border-b-2 border-gray-300">
                <th class="px-4 py-3 text-left font-semibold text-gray-800">Deskripsi</th>
                <th class="px-4 py-3 text-right font-semibold text-gray-800">Harga Satuan</th>
                <th class="px-4 py-3 text-center font-semibold text-gray-800">Qty</th>
                <th class="px-4 py-3 text-right font-semibold text-gray-800">Total</th>
            </tr>
        </thead>
        <tbody>
            <!-- Package -->
            <tr class="border-b border-gray-200 hover:bg-gray-50">
                <td class="px-4 py-3 text-gray-700">{{ $order->package->name ?? 'Paket Wedding' }}</td>
                <td class="px-4 py-3 text-right text-gray-700">Rp {{ number_format($order->base_price, 0, ',', '.') }}</td>
                <td class="px-4 py-3 text-center text-gray-700">1</td>
                <td class="px-4 py-3 text-right font-semibold text-gray-800">Rp {{ number_format($order->base_price, 0, ',', '.') }}</td>
            </tr>

            <!-- Additional Services -->
            @php
                $packageServiceIds = $order->package ? $order->package->services->pluck('id')->toArray() : [];
                $additionalServices = $order->services->filter(function($service) use ($packageServiceIds) {
                    return !in_array($service->service_id, $packageServiceIds);
                });
            @endphp

            @forelse($additionalServices as $service)
                <tr class="border-b border-gray-200 hover:bg-gray-50">
                    <td class="px-4 py-3 text-gray-700">{{ $service->service_name }}</td>
                    <td class="px-4 py-3 text-right text-gray-700">Rp {{ number_format($service->price, 0, ',', '.') }}</td>
                    <td class="px-4 py-3 text-center text-gray-700">1</td>
                    <td class="px-4 py-3 text-right font-semibold text-gray-800">Rp {{ number_format($service->price, 0, ',', '.') }}</td>
                </tr>
            @empty
            @endforelse
        </tbody>
        <tfoot>
            <tr class="bg-gray-100 border-t-2 border-gray-300">
                <td colspan="3" class="px-4 py-3 text-right font-semibold text-gray-800">Total:</td>
                <td class="px-4 py-3 text-right font-semibold text-lg text-gray-800">Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>
</div>
```

## 5. Key Features Implemented

✅ **Security**
- User authentication required (middleware 'auth')
- Order ownership validation in mount()
- HTTP 403 error for unauthorized access

✅ **Functionality**
- View PDF inline in browser
- Download PDF to computer
- Display complete invoice details
- Navigate back to orders list

✅ **UI/UX**
- Responsive design
- Professional layout
- Clear information hierarchy
- Accessible buttons and links
- Color scheme matching app theme

✅ **Data Integration**
- Uses existing OrderInvoiceService
- Works with existing Order model
- Supports all stored order data
- Handles JSON customer data
- Supports order notes and service notes

## 6. URL Patterns

```
// Access invoice
GET /pelanggan/pesanan/{id}/invoice
Route name: pelanggan.pesanan.invoice

// Example URLs
/pelanggan/pesanan/1/invoice
/pelanggan/pesanan/42/invoice
/pelanggan/pesanan/100/invoice
```

## 7. Testing Curl Commands

```bash
# Login first to get session
curl -X POST http://localhost/login \
  -d "email=customer@example.com&password=password" \
  -c cookies.txt

# Access invoice
curl http://localhost/pelanggan/pesanan/1/invoice \
  -b cookies.txt

# View PDF
curl http://localhost/pelanggan/pesanan/1/invoice \
  -b cookies.txt \
  --header "Accept: application/pdf"

# Try another user's invoice (should get 403)
curl http://localhost/pelanggan/pesanan/999/invoice \
  -b cookies.txt
```

## 8. Troubleshooting

### Invoice not showing?
1. Check if user is authenticated
2. Verify order ID exists
3. Check if order belongs to current user
4. Check browser console for errors

### PDF not generating?
1. Verify DomPDF is installed
2. Check file permissions in storage/
3. Ensure order data is loaded correctly
4. Check OrderInvoiceService error logs

### Styling issues?
1. Verify Tailwind/Bootstrap is loaded
2. Clear browser cache
3. Check CSS file links
4. Verify view layout is correct

### Button not working?
1. Check route name is correct
2. Verify order ID is passed
3. Check Livewire is loaded
4. Check browser console

---

**Version**: 1.0  
**Status**: Ready for Production  
**Last Updated**: 2026-01-01
