# Invoice Feature - Quick Start Guide

## ✅ Checklist Implementasi

### 1. Files yang Sudah Dibuat
- [x] `app/Http/Controllers/InvoiceController.php` - Controller untuk invoice
- [x] `resources/views/invoice/show.blade.php` - View HTML invoice
- [x] `resources/views/invoice/pdf.blade.php` - Template PDF invoice
- [x] `routes/web.php` - Routes configuration (updated)
- [x] `app/Filament/Resources/Orders/Tables/OrdersTable.php` - Filament integration (updated)
- [x] `tests/Feature/InvoiceTest.php` - Feature tests
- [x] `INVOICE_FEATURE.md` - Dokumentasi fitur lengkap
- [x] `INVOICE_DIAGRAM.md` - Entity relationship diagram

---

## 🚀 Cara Menggunakan

### Untuk Customer/User
1. **Login** ke aplikasi
2. Buka dashboard atau halaman pesanan
3. Klik tombol **"Invoice"** atau **"📄 Invoice"**
4. Pilih aksi:
   - **Lihat Invoice** - Tampilkan di browser
   - **Download PDF** - Download sebagai file PDF
   - **Cetak** - Cetak langsung dari browser

### URL Direct
```
# Lihat invoice HTML
https://localhost:8000/invoice/{order_id}

# Download invoice PDF
https://localhost:8000/invoice/{order_id}/pdf
```

---

## 🔧 Konfigurasi

### 1. Environment Variables (jika diperlukan)
Tidak ada konfigurasi khusus yang diperlukan. DOMPDF sudah include dalam project.

### 2. Dependencies Check
```bash
# Pastikan barryvdh/laravel-dompdf sudah installed
composer require barryvdh/laravel-dompdf:^3.1

# Jika belum, run:
composer install
```

### 3. Clear Cache
```bash
# Clear route cache
php artisan route:cache

# Clear view cache
php artisan view:clear

# Clear all cache
php artisan cache:clear
```

---

## 📋 Fitur Invoice

### ✓ Informasi yang Ditampilkan
- ✓ Order Code (Nomor Invoice)
- ✓ Invoice Date (Tanggal Invoice)
- ✓ Event Date (Tanggal Acara)
- ✓ Customer Name, Email, Phone
- ✓ Customer Address
- ✓ Package Name
- ✓ Order Status
- ✓ Services (Nama, Tipe, Harga)
- ✓ Payment Status
- ✓ Payment Approval Date
- ✓ Payment Notes
- ✓ Base Price
- ✓ Additional Services Cost
- ✓ Total Price
- ✓ Order Notes

### ✓ Fitur Tambahan
- ✓ Print Invoice (dari browser)
- ✓ Download PDF (automatic naming)
- ✓ Responsive Design (mobile-friendly)
- ✓ Print CSS Media Query
- ✓ Currency Formatting (Rp)
- ✓ Date Localization (Indonesian)
- ✓ Status Badges (dengan warna)

---

## 🔐 Keamanan

### Authentication
```php
// Hanya authenticated user yang bisa akses
Route::middleware('auth')->group(function () {
    Route::get('/invoice/{order}', ...);
    Route::get('/invoice/{order}/pdf', ...);
});
```

### Authorization (Optional - bisa ditambahkan)
```php
// Tambahkan policy untuk memastikan user hanya akses invoice mereka
public function view(User $user, Order $order)
{
    return $user->id === $order->user_id || $user->is_admin;
}
```

---

## 📊 Testing

### Run Tests
```bash
# Jalankan invoice tests
php artisan test tests/Feature/InvoiceTest.php

# Jalankan tests dengan verbose
php artisan test tests/Feature/InvoiceTest.php --verbose

# Jalankan specific test
php artisan test tests/Feature/InvoiceTest.php --filter=test_authenticated_user_can_view_invoice
```

### Test Cases yang Tersedia
1. ✅ Unauthenticated user cannot access invoice
2. ✅ Authenticated user can view invoice
3. ✅ Invoice displays order code
4. ✅ Invoice displays customer name
5. ✅ Invoice displays customer email
6. ✅ Invoice displays package name
7. ✅ Invoice displays event date
8. ✅ Invoice displays total price
9. ✅ Invoice displays payment status
10. ✅ Invoice displays order address
11. ✅ Invoice displays order notes
12. ✅ Unauthenticated user cannot download PDF
13. ✅ Authenticated user can download PDF
14. ✅ PDF filename contains order code
15. ✅ Invoice shows payment status variations
16. ✅ Invoice shows order status
17. ✅ Invoice displays pricing breakdown

---

## 🎨 Customization

### 1. Mengubah Style Invoice
Edit file `resources/views/invoice/show.blade.php` atau `resources/views/invoice/pdf.blade.php`

```blade
<!-- Ubah warna badge -->
<span class="px-3 py-1 rounded-full text-sm bg-blue-100 text-blue-800">
    {{ $order->status }}
</span>
```

### 2. Menambah Informasi ke Invoice
Tambahkan kolom baru ke database Orders:
```php
// Migration
Schema::table('orders', function (Blueprint $table) {
    $table->string('invoice_notes')->nullable();
});
```

### 3. Mengubah Logo/Branding
Dalam template invoice:
```blade
<!-- Ubah nama organisasi -->
<p class="font-semibold">Wedding Organizer</p>
<!-- Atau tambahkan logo -->
<img src="{{ asset('logo.png') }}" alt="Logo">
```

### 4. Mengubah Format Harga
Edit formatter di controller:
```php
// Dari
number_format($price, 0, ',', '.')

// Menjadi
number_format($price, 2, ',', '.') // dengan 2 decimal places
```

---

## 🐛 Troubleshooting

### Problem: "Route not found"
**Solution**: 
```bash
php artisan route:cache
php artisan route:clear
```

### Problem: "PDF tidak tergenerate"
**Solution**:
```bash
# Check dependencies
composer require barryvdh/laravel-dompdf:^3.1

# Check permissions
chmod -R 775 storage/
```

### Problem: "View not found"
**Solution**:
```bash
# Clear view cache
php artisan view:clear

# Verify file paths
ls resources/views/invoice/
```

### Problem: "Database relation error"
**Solution**:
```bash
# Run migrations
php artisan migrate

# Check Order model relationships
# Ensure all relations exist in app/Models/Order.php
```

### Problem: "PDF styling tidak sesuai"
**Solution**:
- DOMPDF tidak support semua CSS properties
- Gunakan CSS inline atau limited CSS features
- Hindari CSS Grid, Flex kompleks
- Gunakan table-based layout jika perlu

---

## 📈 Performance Tips

### 1. Query Optimization
Invoice controller sudah menggunakan eager loading:
```php
$order->load([
    'customer',
    'package',
    'services.service',
    'paymentApprovedBy'
]);
```

### 2. Cache Invoice
Untuk invoice yang tidak sering berubah:
```php
$invoice = Cache::remember(
    'invoice-' . $order->id,
    now()->addDay(),
    function () use ($order) {
        return view('invoice.show', ['order' => $order]);
    }
);
```

### 3. Batch Generate PDF
Jika perlu generate banyak invoice:
```php
foreach ($orders as $order) {
    $pdf = Pdf::loadView('invoice.pdf', ['order' => $order]);
    $pdf->save(storage_path('invoices/Invoice-' . $order->order_code . '.pdf'));
}
```

---

## 🔄 Integration Points

### 1. Di Filament Admin Panel
- Tombol "View Invoice" ada di Orders table
- Membuka di tab baru
- Icon: document-text

### 2. Di Customer Dashboard
Tambahkan link ke dashboard:
```blade
<!-- resources/views/pelanggan/dashboard.blade.php -->
<a href="{{ route('invoice.show', $order) }}" class="btn btn-primary">
    View Invoice
</a>
```

### 3. Di Email Notification
Kirim invoice via email:
```php
Mail::send('invoice.email', ['order' => $order], function ($m) use ($order) {
    $m->to($order->customer->email)
      ->subject('Invoice: ' . $order->order_code);
});
```

---

## 📞 Support & Additional Features

### Fitur yang Bisa Ditambahkan
1. **Invoice Numbering System** - Auto-increment dengan prefix
2. **Digital Signature** - Tanda tangan digital untuk approved invoices
3. **Email Invoice** - Auto-send invoice via email
4. **Invoice History** - Track perubahan invoice
5. **Multi-currency** - Support multiple currency
6. **Custom Templates** - Admin bisa customize template
7. **Bulk Generate** - Generate multiple invoices sekaligus
8. **Archive** - Arsip invoice lama
9. **Search/Filter** - Cari invoice by date, customer, status
10. **Analytics** - Report invoice by status, amount, etc

### Extensions
- **QR Code** - Tambahkan QR code untuk tracking
- **Barcode** - Invoice barcode untuk inventory
- **Watermark** - Add watermark untuk draft/unsent invoices
- **Encryption** - Enkripsi PDF invoice

---

## 📞 Contact & Support

Untuk pertanyaan atau issue:
1. Check dokumentasi: `INVOICE_FEATURE.md`
2. Check diagram: `INVOICE_DIAGRAM.md`
3. Run tests: `php artisan test tests/Feature/InvoiceTest.php`
4. Check logs: `storage/logs/laravel.log`

---

## Version Info

- **Created**: 18 December 2025
- **Version**: 1.0
- **Status**: Production Ready
- **Laravel Version**: 11.x
- **PHP Version**: 8.1+
- **Dependencies**: barryvdh/laravel-dompdf ^3.1

---

## Changelog

### v1.0 (2025-12-18)
- ✨ Initial release
- ✨ Invoice HTML view with print support
- ✨ Invoice PDF generation with DOMPDF
- ✨ Filament integration with action button
- ✨ Comprehensive test suite
- ✨ Full documentation

---

**Last Updated**: 18 December 2025
