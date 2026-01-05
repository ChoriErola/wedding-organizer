# 📋 Invoice Pelanggan - Dokumentasi Implementasi

## Overview
Fitur Invoice Pelanggan telah berhasil dibuat dengan konsep yang sama seperti admin. Pelanggan kini dapat melihat dan mengunduh invoice dari pesanan mereka.

## Komponen yang Dibuat

### 1. **Livewire Component: InvoiceShow**
📁 File: [app/Livewire/Pelanggan/InvoiceShow.php](app/Livewire/Pelanggan/InvoiceShow.php)

```php
class InvoiceShow extends Component
{
    public Order $order;

    public function mount(Order $order)
    {
        // Validasi: user hanya bisa melihat invoice miliknya sendiri
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }
    }

    public function downloadPdf()      // Download invoice sebagai PDF
    public function viewPdf()          // Lihat invoice secara inline di browser
}
```

**Fitur:**
- ✅ Keamanan: Verifikasi bahwa user hanya bisa melihat invoice miliknya
- ✅ Download PDF: Menggunakan `OrderInvoiceService` yang sama dengan admin
- ✅ View PDF: Tampilkan invoice langsung di browser

### 2. **Blade View: invoice-show.blade.php**
📁 File: [resources/views/livewire/pelanggan/invoice-show.blade.php](resources/views/livewire/pelanggan/invoice-show.blade.php)

**Menampilkan:**
- 📌 Header Invoice (Nomor, Tanggal Invoice, Tanggal Acara)
- 👤 Informasi Pelanggan (Dari dan Untuk)
- 📦 Detail Pesanan (Paket, Status)
- 📊 Tabel Layanan dengan Harga
- 💳 Status Pembayaran
- 📝 Catatan (Order, Layanan Tambahan)
- 🔘 Tombol Aksi (Lihat, Unduh, Kembali)

### 3. **Route**
📁 File: [routes/web.php](routes/web.php)

```php
Route::get('/pelanggan/pesanan/{order}/invoice', InvoiceShow::class)
    ->name('pelanggan.pesanan.invoice');
```

**Akses:**
- URL: `/pelanggan/pesanan/{order_id}/invoice`
- Route Name: `pelanggan.pesanan.invoice`
- Middleware: `auth` (Protected)

### 4. **Update Halaman Orders**
📁 File: [resources/views/livewire/pelanggan/orders.blade.php](resources/views/livewire/pelanggan/orders.blade.php)

**Perubahan:**
- ✅ Tambah tombol "Lihat Invoice" di footer card pesanan
- ✅ Styling match dengan desain aplikasi (warna #a8729a)
- ✅ Icon PDF untuk visual yang lebih baik

## 🔐 Keamanan

1. **Middleware Auth**: Route dilindungi middleware `auth`
2. **User Validation**: Component memverifikasi `Auth::id()` == `Order->user_id`
3. **Unauthorized Handling**: Mengembalikan HTTP 403 jika user tidak authorized

## 📊 Data yang Ditampilkan

### Header Invoice
- Nomor Invoice (Order Code)
- Tanggal Invoice (Created At)
- Tanggal Acara (Event Date)

### Detail Pelanggan
- Nama Pelanggan
- Email
- Nomor Telepon (jika ada)
- Alamat Acara

### Detail Layanan
1. **Paket Utama**: Harga base_price
2. **Layanan Tambahan**: Semua service yang tidak termasuk dalam paket
   - Ditampilkan dengan harga individual
   - Catatan layanan (jika ada)

### Ringkasan Pembayaran
- Total Harga (total_price)
- Status Pembayaran (payment_note)
- Catatan Pembayaran (jika ada)

## 🎨 Styling & UX

- **Responsive Design**: Menggunakan Bootstrap Grid System
- **Color Scheme**: 
  - Primary: #a8729a (Purple - Match dengan existing)
  - Secondary: #ff9800 (Orange - CTA)
  - Neutral: Gray palette
- **Interactive Buttons**: Hover effects, Icon integration
- **Print-Friendly**: PDF format yang rapi dan profesional

## 🔗 Integration dengan OrderInvoiceService

Komponen ini menggunakan `OrderInvoiceService::generate()` yang sudah ada:

```php
public function viewPdf()
{
    return response(OrderInvoiceService::generate($this->order), 200)
        ->header('Content-Type', 'application/pdf')
        ->header('Content-Disposition', 'inline; filename="invoice-' . $this->order->order_code . '.pdf"');
}
```

Service ini menghandle:
- ✅ Data cleaning (ASCII validation)
- ✅ Item assembly (Paket + Services)
- ✅ Payment notes parsing
- ✅ HTML to PDF rendering (DomPDF)

## 📱 User Flow

```
1. Pelanggan login
2. Akses /pelanggan/pesanan
3. Lihat daftar pesanan dengan tombol "Lihat Invoice"
4. Klik tombol → Route ke /pelanggan/pesanan/{id}/invoice
5. Component mount, validasi ownership
6. Render view dengan data pesanan
7. Opsi:
   - Lihat Invoice (inline PDF di browser)
   - Unduh Invoice (download file PDF)
   - Kembali (ke daftar pesanan)
```

## ✅ Testing Checklist

- [ ] Login sebagai pelanggan
- [ ] Buka halaman Pesanan (/pelanggan/pesanan)
- [ ] Verify tombol "Lihat Invoice" muncul di setiap pesanan
- [ ] Klik tombol → Verify redirect ke /pelanggan/pesanan/{id}/invoice
- [ ] Verify data invoice ditampilkan dengan benar
- [ ] Test "Lihat Invoice" button → PDF muncul inline
- [ ] Test "Unduh Invoice" button → PDF terdownload
- [ ] Test "Kembali" button → Kembali ke pesanan
- [ ] Login user lain, coba akses invoice user lain → Verify 403 Unauthorized
- [ ] Print from browser → Verify layout tetap rapi

## 🚀 Features yang Bisa Dikembangkan

1. **Email Invoice**: Kirim PDF ke email pelanggan saat status berubah
2. **Invoice History**: Archive semua invoice dengan pencarian
3. **Payment Tracker**: Visual tracking pembayaran cicilan
4. **Multi-Language**: Support Invoice dalam bahasa lain
5. **Digital Signature**: Tanda tangan digital untuk invoice resmi

## 📝 Notes

- Invoice menggunakan template yang sama dengan admin: `vendor/invoices/templates/order_invoice.blade.php`
- PDF generation menggunakan DomPDF dengan file support enabled
- Semua data pelanggan ditampilkan sesuai JSON stored di database
- Service tambahan yang tidak di-bundle paket tampil terpisah

---

**Status**: ✅ Ready to Use  
**Version**: 1.0  
**Last Updated**: 2026-01-01
