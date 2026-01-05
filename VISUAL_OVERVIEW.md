# 📊 Visual Overview - Invoice Pelanggan Feature

## System Architecture Diagram

```
┌─────────────────────────────────────────────────────────────┐
│           CUSTOMER INVOICE SYSTEM ARCHITECTURE              │
└─────────────────────────────────────────────────────────────┘

                      WEB BROWSER
                          │
                          │ /pelanggan/pesanan
                          ▼
            ┌──────────────────────────────┐
            │  Orders List Page            │
            │  (Livewire Orders Component) │
            │                              │
            │  [Order 1] [Lihat Invoice]   │
            │  [Order 2] [Lihat Invoice]   │
            │  [Order 3] [Lihat Invoice]   │
            └──────────────┬───────────────┘
                           │
                Click "Lihat Invoice"
                           │
                           ▼
            ┌──────────────────────────────┐
            │ Route: /pelanggan/pesanan    │
            │        /{order}/invoice      │
            │                              │
            │ Middleware: auth             │
            └──────────────┬───────────────┘
                           │
                           ▼
            ┌──────────────────────────────┐
            │  InvoiceShow Component       │
            │  (Livewire)                  │
            │                              │
            │ 1. Mount & Validate          │
            │ 2. Load Order & Relations    │
            │ 3. Render Blade Template     │
            └──────────────┬───────────────┘
                           │
                ┌──────────┴──────────┐
                │                     │
                ▼                     ▼
        [View PDF]              [Download PDF]
        (Inline)                (Attachment)
            │                         │
            │                         ▼
            │              OrderInvoiceService
            │              ::generate()
            │                         │
            │                         ▼
            │              DomPDF Rendering
            │                         │
            └─────────────┬───────────┘
                          │
                          ▼
                    [PDF Output]
```

---

## Component Interaction Flow

```
User Action Timeline:
═══════════════════════════════════════════════════════════════

1. USER LOGIN
   ├─ URL: /login
   ├─ Action: Enter credentials
   └─ Result: Authenticated session

2. NAVIGATE TO ORDERS
   ├─ URL: /pelanggan/pesanan
   ├─ Component: Livewire Orders
   └─ Display: List of orders with "Lihat Invoice" buttons

3. CLICK "LIHAT INVOICE" BUTTON
   ├─ Route: /pelanggan/pesanan/{order_id}/invoice
   ├─ Component: Livewire InvoiceShow
   └─ Action: mount() method called

4. VALIDATION CHECK
   ├─ Check: Auth::id() == $order->user_id
   ├─ If TRUE: Continue to render
   ├─ If FALSE: Return HTTP 403
   └─ Load: Order with relations

5. RENDER INVOICE
   ├─ Template: invoice-show.blade.php
   ├─ Display: Complete invoice details
   └─ Show: Action buttons

6. USER CHOOSES ACTION
   │
   ├─ Option A: Click "👁️ Lihat Invoice"
   │  ├─ Method: viewPdf()
   │  ├─ Header: Content-Disposition: inline
   │  └─ Display: PDF in browser
   │
   ├─ Option B: Click "⬇️ Unduh Invoice"
   │  ├─ Method: downloadPdf()
   │  ├─ Header: Content-Disposition: attachment
   │  └─ Action: Download PDF file
   │
   └─ Option C: Click "← Kembali"
      ├─ URL: /pelanggan/pesanan
      └─ Display: Return to orders list
```

---

## Data Structure & Relationships

```
Order Model
├─ id: integer
├─ user_id: integer (Foreign Key → Users)
├─ package_id: integer (Foreign Key → Packages)
├─ order_code: string
├─ event_date: date
├─ base_price: decimal
├─ total_price: decimal
├─ status: string
├─ customer: json
│  ├─ name: string
│  ├─ email: string
│  └─ phone: string
├─ alamat: string
├─ notes: string
├─ payment_note: string
└─ Relationships:
   ├─ User (hasOne)
   ├─ Package (belongsTo)
   ├─ Services (hasMany OrderService)
   └─ Invoices (hasMany)

Package Model
├─ id: integer
├─ name: string
├─ image: string
├─ description: string
└─ Relationships:
   ├─ Services (belongsToMany)
   └─ Orders (hasMany)

Services Model
├─ id: integer
├─ name: string
├─ is_active: boolean
└─ Relationships:
   ├─ Packages (belongsToMany)
   └─ Orders (belongsToMany as OrderService)

OrderService (Pivot)
├─ id: integer
├─ order_id: integer
├─ service_id: integer
├─ service_name: string
├─ price: decimal
└─ notes: string
```

---

## File Organization

```
Project Root
├── app/
│   └── Livewire/
│       └── Pelanggan/
│           ├── Orders.php                    (existing)
│           ├── OrdersCreate.php              (existing)
│           └── InvoiceShow.php               ✨ NEW
│
├── resources/
│   └── views/
│       └── livewire/
│           ├── invoice/
│           │   └── invoice-show.blade.php    (existing - admin)
│           └── pelanggan/
│               ├── orders.blade.php          🔄 MODIFIED
│               ├── orders-create.blade.php   (existing)
│               └── invoice-show.blade.php    ✨ NEW
│
├── routes/
│   └── web.php                              🔄 MODIFIED
│
├── app/Services/
│   └── OrderInvoiceService.php              (existing)
│
└── Documentation/
    ├── CUSTOMER_INVOICE_IMPLEMENTATION.md   ✨ NEW
    ├── INVOICE_PELANGGAN_SUMMARY.md         ✨ NEW
    ├── INVOICE_ARCHITECTURE.md              ✨ NEW
    ├── INVOICE_QUICK_REFERENCE.md           ✨ NEW
    ├── INVOICE_IMPLEMENTATION_CHECKLIST.md  ✨ NEW
    └── INVOICE_PELANGGAN_FINAL_REPORT.md    ✨ NEW
```

---

## Route Definition Diagram

```
┌─ Auth Routes
│  │
│  ├─ GET /login
│  │  └─ Login::class
│  │
│  ├─ POST /logout
│  │  └─ Logout action
│  │
│  └─ [Authenticated Group]
│     │
│     ├─ GET /dashboard
│     │  └─ Dashboard view
│     │
│     ├─ GET /pelanggan/pesanan
│     │  └─ Orders::class
│     │     (List all orders with "Lihat Invoice" buttons)
│     │
│     ├─ GET /pelanggan/pesanan/buat
│     │  └─ OrdersCreate::class
│     │     (Create new order)
│     │
│     └─ GET /pelanggan/pesanan/{order}/invoice  ✨ NEW
│        └─ InvoiceShow::class
│           (View/Download invoice)
│
└─ Public Routes
   │
   ├─ GET /
   │  └─ Homepage
   │
   ├─ GET /order/create
   │  └─ OrderController::create()
   │
   └─ POST /order
      └─ OrderController::store()
```

---

## Security Validation Flow

```
Request Arrives
       │
       ▼
┌─────────────────────────────┐
│ Check: Is User Authenticated?
├─────────────────────────────┤
│ Via: auth middleware        │
└──────┬──────────────────────┘
       │
       ├─ NO: Redirect to /login
       │
       └─ YES: Continue
             │
             ▼
    ┌────────────────────────────────┐
    │ Load Order from Database       │
    ├────────────────────────────────┤
    │ Using: Route Model Binding     │
    └──────┬─────────────────────────┘
           │
           ▼
    ┌──────────────────────────────────────┐
    │ Component Mount: Validate Ownership  │
    ├──────────────────────────────────────┤
    │ Check: Auth::id() == Order->user_id  │
    └──────┬───────────────────────────────┘
           │
           ├─ MATCH: User owns order
           │  └─ Load relations
           │  └─ Render template
           │  └─ Display invoice
           │
           └─ NO MATCH: User doesn't own order
              └─ abort(403, 'Unauthorized')
              └─ Display error page
```

---

## Invoice Display Components

```
┌─────────────────────────────────────────────┐
│           INVOICE DISPLAY PAGE              │
└─────────────────────────────────────────────┘

┌─ Header
│  ├─ INVOICE (title)
│  ├─ Nomor Invoice: ORD-20260101-ABC123
│  ├─ Tanggal Invoice: 01 Januari 2026
│  └─ Tanggal Acara: 15 Februari 2026
│
├─ Action Buttons
│  ├─ [👁️ Lihat Invoice]
│  ├─ [⬇️ Unduh Invoice]
│  └─ [← Kembali ke Pesanan]
│
├─ Dari (Seller)
│  └─ Wedding Organizer
│
├─ Untuk (Buyer)
│  ├─ Nama Pelanggan
│  ├─ Email
│  ├─ Alamat
│  └─ Nomor Telepon
│
├─ Detail Pesanan
│  ├─ Paket: Wedding Gold
│  └─ Status: Confirmed
│
├─ Detail Layanan
│  ├─ Table Header
│  │  ├─ Deskripsi
│  │  ├─ Harga Satuan
│  │  ├─ Qty
│  │  └─ Total
│  │
│  ├─ Row: Paket Wedding Gold
│  │  ├─ Rp 50.000.000
│  │  ├─ 1
│  │  └─ Rp 50.000.000
│  │
│  ├─ Row: Dekorasi Tambahan (if any)
│  │  ├─ Rp 5.000.000
│  │  ├─ 1
│  │  └─ Rp 5.000.000
│  │
│  └─ Footer: Total Rp 55.000.000
│
├─ Status Pembayaran
│  └─ Catatan Pembayaran: ...
│
└─ Catatan Tambahan
   ├─ Catatan Order: ...
   └─ Catatan Layanan: ...
```

---

## PDF Generation Process

```
User clicks PDF action
        │
        ▼
┌─────────────────────────────┐
│ Livewire Method Triggered   │
├─────────────────────────────┤
│ viewPdf() or downloadPdf()  │
└──────┬──────────────────────┘
       │
       ▼
┌──────────────────────────────────────┐
│ Call OrderInvoiceService::generate() │
├──────────────────────────────────────┤
│                                      │
│ 1. Clean data (ASCII validation)     │
│ 2. Setup seller (CompanySeller)      │
│ 3. Setup buyer (Customer info)       │
│ 4. Build items array                 │
│ 5. Parse payment notes               │
│ 6. Render HTML template              │
│ 7. Convert HTML to PDF               │
│                                      │
└──────┬───────────────────────────────┘
       │
       ▼
┌──────────────────────────────┐
│ DomPDF Library               │
├──────────────────────────────┤
│ loadHTML()                   │
│ setOption()                  │
│ output()                     │
└──────┬───────────────────────┘
       │
       ▼
┌──────────────────────────────────────┐
│ Return PDF Response                  │
├──────────────────────────────────────┤
│ Header: Content-Type: application/pdf│
│ Header: Content-Disposition:         │
│   - inline (viewPdf)                 │
│   - attachment (downloadPdf)         │
│ Body: PDF binary data                │
└──────┬───────────────────────────────┘
       │
       └─ viewPdf: Display in browser
       │
       └─ downloadPdf: Download file
```

---

## Comparison with Admin Invoice

```
┌─────────────────┬──────────────────┬──────────────────┐
│    Feature      │   Admin Invoice  │ Customer Invoice │
├─────────────────┼──────────────────┼──────────────────┤
│ Access          │ /panel/orders    │ /pelanggan/      │
│                 │                  │ pesanan          │
│                 │                  │                  │
│ Security        │ Auth + Filament  │ Auth + User ID   │
│                 │ authorization    │ validation       │
│                 │                  │                  │
│ PDF Generation  │ OrderInvoice     │ OrderInvoice     │
│                 │ Service          │ Service          │
│                 │ (same)           │ (same)           │
│                 │                  │                  │
│ Template        │ vendor/invoices/ │ vendor/invoices/ │
│                 │ order_invoice    │ order_invoice    │
│                 │ (same)           │ (same)           │
│                 │                  │                  │
│ Display Format  │ Blade            │ Blade            │
│                 │ (similar)        │ (similar)        │
│                 │                  │                  │
│ Features        │ View + Download  │ View + Download  │
│                 │ (same)           │ (same)           │
│                 │                  │                  │
│ Data Access     │ All orders       │ Own orders only  │
│                 │                  │                  │
│ User Type       │ Admin            │ Customer         │
└─────────────────┴──────────────────┴──────────────────┘
```

---

## Implementation Status

```
✅ COMPLETED TASKS:

1. Component Creation (InvoiceShow.php)
   ├─ Class defined
   ├─ Mount method with validation
   ├─ Download PDF method
   ├─ View PDF method
   └─ Render method

2. Template Creation (invoice-show.blade.php)
   ├─ Invoice header
   ├─ Customer info
   ├─ Order details
   ├─ Services table
   ├─ Payment status
   ├─ Notes section
   └─ Action buttons

3. Route Configuration (web.php)
   ├─ Component import
   ├─ Route definition
   ├─ Middleware attached
   └─ Route naming

4. UI Integration (orders.blade.php)
   ├─ Button added
   ├─ Link configured
   ├─ Styling applied
   └─ Icon added

5. Documentation
   ├─ Implementation guide
   ├─ Architecture diagram
   ├─ Quick reference
   ├─ Checklist
   └─ Final report

═══════════════════════════════════════════════════════════════
STATUS: ✅ READY FOR PRODUCTION
═══════════════════════════════════════════════════════════════
```

---

## Key Files Summary

| File | Type | Status | Purpose |
|------|------|--------|---------|
| InvoiceShow.php | Component | ✨ NEW | Handle invoice display/PDF |
| invoice-show.blade.php | Template | ✨ NEW | Invoice layout |
| web.php | Route | 🔄 UPDATED | Add invoice route |
| orders.blade.php | Template | 🔄 UPDATED | Add invoice button |
| DOCUMENTATION (x5) | Docs | ✨ NEW | Reference materials |

---

**Version**: 1.0  
**Status**: ✅ Production Ready  
**Last Updated**: 2026-01-01
