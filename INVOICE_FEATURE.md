# Invoice Feature Documentation

## Fitur Invoice Wedding Organizer

Fitur ini memungkinkan pembuat pesanan (customer) dan admin untuk melihat, mencetak, dan mengunduh invoice dalam format PDF.

### Komponen yang Dibuat

#### 1. Controller
- **File**: `app/Http/Controllers/InvoiceController.php`
- **Methods**:
  - `show(Order $order)` - Menampilkan invoice dalam format HTML
  - `pdf(Order $order)` - Mengunduh invoice dalam format PDF

#### 2. Views
- **`resources/views/invoice/show.blade.php`** - Tampilan invoice HTML dengan opsi cetak dan download PDF
- **`resources/views/invoice/pdf.blade.php`** - Template PDF invoice (khusus untuk DOMPDF)

#### 3. Livewire Component (Opsional)
- **File**: `app/Livewire/Invoice/InvoiceShow.php`
- **View**: `resources/views/livewire/invoice/invoice-show.blade.php`

#### 4. Routes
- `GET /invoice/{order}` - Tampilkan invoice (nama route: `invoice.show`)
- `GET /invoice/{order}/pdf` - Download PDF invoice (nama route: `invoice.pdf`)

#### 5. Integrasi Filament
- Tombol "Lihat Invoice" ditambahkan di table Orders dengan ikon document
- Link membuka invoice di tab baru

---

## Informasi yang Ditampilkan

### Header Invoice
- Nomor Invoice (Order Code)
- Tanggal Invoice (dibuat)
- Tanggal Acara (event_date)

### Informasi Pelanggan
- **Dari**: Identitas Wedding Organizer
- **Untuk**: 
  - Nama Customer
  - Email
  - Nomor Telepon
  - Alamat

### Detail Pesanan
- Nama Paket
- Status Pesanan (pending, confirmed, completed, cancelled)

### Layanan yang Dipilih
Tabel dengan kolom:
- Nama Layanan
- Tipe (Paket/Tambahan) - berdasarkan `is_required`
- Harga

### Status Pembayaran
- Status Pembayaran (pending, approved, rejected)
- Tanggal Verifikasi Pembayaran
- Catatan Pembayaran (jika ada)
- Data dari tabel `order_histories` dengan observer

### Ringkasan Harga
- Harga Paket (base_price)
- Layanan Tambahan (total_price - base_price)
- **Total Harga** (total_price)

### Catatan Tambahan
- Menampilkan notes dari order jika ada

---

## Model Relationships

```
Order
├── customer() → User
├── package() → Package
├── services() → OrderService (hasMany)
│   └── service() → Services
└── paymentApprovedBy() → User
```

---

## Database Columns

### Orders Table
- `order_code` - Kode unik order
- `user_id` - Foreign Key ke User (customer)
- `package_id` - Foreign Key ke Package
- `event_date` - Tanggal acara (format: datetime)
- `base_price` - Harga paket dasar
- `total_price` - Total harga termasuk layanan tambahan
- `status` - Status order
- `payment_status` - Status pembayaran
- `payment_approved_at` - Tanggal approval pembayaran
- `payment_approved_by` - ID user yang approve
- `payment_note` - Catatan pembayaran
- `bukti_pembayaran` - File bukti pembayaran (array)
- `alamat` - Alamat acara
- `notes` - Catatan pesanan

### OrderService Table
- `order_id` - Foreign Key ke Order
- `service_id` - Foreign Key ke Services
- `service_name` - Nama layanan (cache)
- `price` - Harga layanan
- `is_required` - Boolean (paket wajib atau tambahan)
- `is_custom` - Boolean (layanan custom/tambahan)

### OrderHistories Table
- `order_id` - Foreign Key ke Order
- `old_status` - Status lama
- `new_status` - Status baru
- `old_payment_status` - Status pembayaran lama
- `new_payment_status` - Status pembayaran baru
- `note` - Catatan perubahan
- `changed_by` - ID user yang melakukan perubahan

---

## Cara Penggunaan

### 1. Akses Invoice untuk Customer
```php
// URL: /invoice/{order_id}
// Hanya untuk authenticated user
Route::middleware('auth')->get('/invoice/{order}', [InvoiceController::class, 'show'])->name('invoice.show');
```

### 2. Download PDF Invoice
```php
// URL: /invoice/{order_id}/pdf
// Hanya untuk authenticated user
Route::middleware('auth')->get('/invoice/{order}/pdf', [InvoiceController::class, 'pdf'])->name('invoice.pdf');
```

### 3. Di Filament Admin
Tombol "Lihat Invoice" tersedia di table Orders dengan:
- Icon: document-text
- Warna: info (biru)
- Target: Membuka di tab baru

---

## Styling dan Layout

### Invoice HTML
- **Framework CSS**: Tailwind CSS
- **Responsive**: Mobile-friendly design
- **Print-friendly**: Tombol print dan CSS media query untuk print

### Invoice PDF
- **Engine**: DOMPDF (barryvdh/laravel-dompdf)
- **Format**: A4 size
- **Styling**: CSS inline (kompatibel dengan DOMPDF)

---

## Format Tampilan

### Badge Status
```
Status Pending    → Yellow badge
Status Confirmed  → Blue badge
Status Completed  → Green badge
Status Cancelled  → Red badge

Payment Pending   → Yellow badge
Payment Approved  → Green badge
Payment Rejected  → Red badge
```

### Format Harga
- Currency: Rupiah (Rp)
- Format: `Rp X.XXX.XXX` (dengan separator ribuan)
- Decimal: 0 digit (hanya untuk integer)

### Format Tanggal
- Format: `d F Y` (misal: 25 December 2024)
- Locale: Menggunakan Carbon localized format
- Timezone: Sesuai dengan server timezone

---

## Fitur Tambahan

### 1. Print Invoice
- Tombol print di halaman invoice HTML
- CSS media query untuk print-friendly layout
- Tombol navigasi disembunyikan saat print

### 2. Download PDF
- Nama file: `Invoice-{order_code}.pdf`
- Format: A4
- Styling profesional dengan DOMPDF

### 3. Keamanan
- Route dilindungi middleware `auth`
- Hanya authenticated user yang bisa akses invoice
- Policy bisa ditambahkan untuk memastikan user hanya bisa melihat invoice mereka

---

## Observer dan Audit Trail

Invoice menggunakan data dari OrderHistories yang dicatat melalui Observer:

```php
// Di OrderObserver atau Model Boot
if ($order->isDirty('status') || $order->isDirty('payment_status')) {
    OrderHistories::create([
        'order_id' => $order->id,
        'old_status' => $order->getOriginal('status'),
        'new_status' => $order->status,
        'changed_by' => Auth::id(),
    ]);
}
```

---

## Troubleshooting

### 1. PDF tidak tergenerate
- Pastikan `barryvdh/laravel-dompdf` sudah terinstall: `composer require barryvdh/laravel-dompdf`
- Check file path di controller
- Pastikan view file ada di `resources/views/invoice/pdf.blade.php`

### 2. Route tidak ditemukan
- Pastikan routes sudah di-register di `routes/web.php`
- Run `php artisan route:cache` jika menggunakan route caching
- Check import controller di routes

### 3. Relationship error
- Pastikan `Order` model memiliki semua relationship yang diperlukan
- Pastikan foreign keys ada di database
- Run migration jika perlu

### 4. Tampilan tidak sesuai
- Clear browser cache: Ctrl+Shift+Delete atau Cmd+Shift+Delete
- Run `php artisan view:clear`
- Pastikan Tailwind CSS sudah compiled

---

## Ekspansi Fitur

### 1. Email Invoice
```php
// Mengirim invoice ke email customer
Mail::send(new InvoiceMail($order));
```

### 2. Invoice History/Versioning
Simpan history versi invoice jika ada perubahan order.

### 3. Digital Signature
Tambahkan tanda tangan digital untuk invoice yang sudah approved.

### 4. Multi-language Support
Invoice sudah mendukung lokalisasi tanggal, perlu ditambah untuk teks lainnya.

### 5. Custom Branding
Tambahkan logo, warna brand, dan styling custom di konfigurasi.

---

## File Checklist

✅ `app/Http/Controllers/InvoiceController.php` - Controller
✅ `resources/views/invoice/show.blade.php` - View HTML
✅ `resources/views/invoice/pdf.blade.php` - Template PDF
✅ `routes/web.php` - Routes (updated)
✅ `app/Filament/Resources/Orders/Tables/OrdersTable.php` - Filament Integration (updated)

---

## Testing

### Test Akses Invoice
```php
// Test untuk authenticated user
$this->actingAs($user)->get('/invoice/' . $order->id)->assertOk();

// Test PDF download
$this->actingAs($user)->get('/invoice/' . $order->id . '/pdf')->assertOk();

// Test untuk unauthenticated user
$this->get('/invoice/' . $order->id)->assertRedirect('/login');
```

---

**Dibuat pada**: 18 Desember 2025  
**Versi**: 1.0  
**Status**: Production Ready
