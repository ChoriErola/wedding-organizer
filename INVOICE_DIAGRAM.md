# Invoice Feature - Entity Relationship Diagram

## Database Schema Relationships

```
┌─────────────────────────────────────────────────────────────────┐
│                         USERS TABLE                              │
├─────────────────────────────────────────────────────────────────┤
│ id (PK)                                                           │
│ name                                                              │
│ email                                                             │
│ phone                                                             │
│ password                                                          │
└─────────────┬───────────────────────────────────────────────────┘
              │
              │ (1:N) user_id
              │
              ▼
┌─────────────────────────────────────────────────────────────────┐
│                         ORDERS TABLE                              │
├─────────────────────────────────────────────────────────────────┤
│ id (PK)                                                           │
│ user_id (FK) ──────────────> USERS                               │
│ package_id (FK) ──────────────> PACKAGES                         │
│ order_code (UNIQUE)                                               │
│ event_date                                                        │
│ base_price                                                        │
│ total_price                                                       │
│ status (pending, confirmed, completed, cancelled)                │
│ payment_status (pending, approved, rejected)                     │
│ payment_approved_at                                               │
│ payment_approved_by (FK) ──────────> USERS                       │
│ payment_note                                                      │
│ bukti_pembayaran (array)                                          │
│ alamat                                                            │
│ notes                                                             │
│ created_at, updated_at                                            │
└──────┬──────────────────────────┬──────────────────────────────┘
       │                          │
       │ (1:N) order_id           │ (M:1) package_id
       │                          │
       ▼                          ▼
┌─────────────────────────────────────────────────────────────────┐
│                    ORDER_SERVICES TABLE                           │
├─────────────────────────────────────────────────────────────────┤
│ id (PK)                                                           │
│ order_id (FK) ──────────────> ORDERS                             │
│ service_id (FK) ──────────────> SERVICES                         │
│ package_id (FK) ──────────────> PACKAGES                         │
│ service_name (cached name)                                        │
│ price                                                             │
│ is_required (boolean - paket/tambahan)                            │
│ is_custom (boolean - custom/standar)                              │
│ created_at, updated_at                                            │
└─────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────┐
│                       PACKAGES TABLE                              │
├─────────────────────────────────────────────────────────────────┤
│ id (PK)                                                           │
│ name (Paket Premium, Paket Gold, dll)                             │
│ image                                                             │
│ price                                                             │
│ created_at, updated_at                                            │
└──────────────────────┬──────────────────────────────────────────┘
                       │
                       │ (M:N) packages_services
                       │
                       ▼
┌─────────────────────────────────────────────────────────────────┐
│                      SERVICES TABLE                               │
├─────────────────────────────────────────────────────────────────┤
│ id (PK)                                                           │
│ name (Fotografi, Dekorasi, Katering, dll)                         │
│ description                                                       │
│ harga_layanan (base price)                                        │
│ is_active (boolean)                                               │
│ created_at, updated_at                                            │
└─────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────┐
│                   PACKAGES_SERVICES TABLE (Pivot)                 │
├─────────────────────────────────────────────────────────────────┤
│ id (PK)                                                           │
│ package_id (FK) ──────────────> PACKAGES                         │
│ service_id (FK) ──────────────> SERVICES                         │
│ value_price (custom price untuk paket ini)                        │
│ is_required (boolean)                                             │
│ created_at, updated_at                                            │
└─────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────┐
│                   ORDER_HISTORIES TABLE (Audit Trail)             │
├─────────────────────────────────────────────────────────────────┤
│ id (PK)                                                           │
│ order_id (FK) ──────────────> ORDERS                             │
│ old_status                                                        │
│ new_status                                                        │
│ old_payment_status                                                │
│ new_payment_status                                                │
│ note (alasan perubahan)                                           │
│ changed_by (FK) ──────────────> USERS                            │
│ created_at, updated_at                                            │
└─────────────────────────────────────────────────────────────────┘
```

---

## Invoice Data Flow

```
┌─────────────────────────────────────────────────────────────────┐
│                   CUSTOMER / ADMIN REQUEST                        │
│                  GET /invoice/{order_id}                          │
└───────────────────────┬─────────────────────────────────────────┘
                        │
                        ▼
┌─────────────────────────────────────────────────────────────────┐
│              InvoiceController@show()                             │
│  ✓ Authenticate user (middleware auth)                           │
│  ✓ Load Order dengan relationships:                              │
│    - customer (User)                                              │
│    - package (Package)                                            │
│    - services (OrderService dengan Service)                       │
│    - paymentApprovedBy (User)                                     │
└───────────────────────┬─────────────────────────────────────────┘
                        │
                        ▼
┌─────────────────────────────────────────────────────────────────┐
│         resources/views/invoice/show.blade.php                    │
│  ✓ Render invoice dengan:                                        │
│    - Order code, invoice date                                     │
│    - Customer name, email, phone, address                         │
│    - Package name                                                 │
│    - Services dengan price (dari order_services)                  │
│    - Payment status (dari orders.payment_status)                  │
│    - Order status                                                 │
│    - Total price breakdown                                        │
│    - Order notes                                                  │
└─────────────────────────────────────────────────────────────────┘
```

---

## Invoice PDF Generation Flow

```
┌─────────────────────────────────────────────────────────────────┐
│               CUSTOMER REQUEST PDF                                │
│            GET /invoice/{order_id}/pdf                            │
└───────────────────────┬─────────────────────────────────────────┘
                        │
                        ▼
┌─────────────────────────────────────────────────────────────────┐
│           InvoiceController@pdf()                                 │
│  ✓ Load Order dengan relationships (sama seperti show)           │
│  ✓ Load view: resources/views/invoice/pdf.blade.php              │
│  ✓ Generate PDF menggunakan DOMPDF                               │
└───────────────────────┬─────────────────────────────────────────┘
                        │
                        ▼
┌─────────────────────────────────────────────────────────────────┐
│        resources/views/invoice/pdf.blade.php                      │
│  ✓ HTML template dengan CSS inline                               │
│  ✓ Styling kompatibel dengan DOMPDF                              │
└───────────────────────┬─────────────────────────────────────────┘
                        │
                        ▼
┌─────────────────────────────────────────────────────────────────┐
│          Pdf::loadView(...)->download()                           │
│  ✓ Convert HTML ke PDF                                           │
│  ✓ Set paper size: A4                                            │
│  ✓ Filename: Invoice-{order_code}.pdf                            │
└───────────────────────┬─────────────────────────────────────────┘
                        │
                        ▼
┌─────────────────────────────────────────────────────────────────┐
│           Download PDF File to Browser                            │
│  Content-Type: application/pdf                                    │
│  Content-Disposition: attachment; filename="Invoice-xxx.pdf"     │
└─────────────────────────────────────────────────────────────────┘
```

---

## Filament Integration

```
┌─────────────────────────────────────────────────────────────────┐
│            Filament Orders Resource Table                         │
├─────────────────────────────────────────────────────────────────┤
│  Columns:                                                         │
│  ├─ Order Code (searchable, copyable)                             │
│  ├─ Customer Name                                                 │
│  ├─ Package Name                                                  │
│  ├─ Event Date                                                    │
│  ├─ Order Status (badge colored)                                  │
│  ├─ Payment Status (badge colored)                                │
│  ├─ Payment Proof (image)                                         │
│  └─ Total Price (formatted)                                       │
│                                                                   │
│  Actions:                                                         │
│  ├─ View (icon: heroicon-o-eye)                                   │
│  ├─ Edit (icon: heroicon-o-pencil)                                │
│  ├─ View Invoice (icon: heroicon-o-document-text) ◄──┐            │
│  ├─ Add Service (icon: heroicon-o-plus-circle)       │            │
│  └─ Delete (icon: heroicon-o-trash)                   │            │
│                                                       │            │
└───────────────────────────────────────────────────────┼───────────┘
                                                        │
                                                        ▼
                                        route('invoice.show', $order)
                                              /invoice/{order_id}
```

---

## Status Tracking via OrderHistories

```
┌─────────────────────────────────────────────────────────────────┐
│                      ORDER UPDATE EVENT                           │
│           (Observer: OrderObserver@updating)                      │
└───────────────────────┬─────────────────────────────────────────┘
                        │
        ┌───────────────┼───────────────┐
        │               │               │
        ▼               ▼               ▼
   Is dirty         Is dirty        Is dirty
   'status'?   'payment_status'?  'bukti_pembayaran'?
      │               │               │
      ▼               ▼               ▼
   YES             YES              (handle file)
      │               │
      └───────────┬───┘
                  │
                  ▼
┌─────────────────────────────────────────────────────────────────┐
│        Create OrderHistories Record                               │
├─────────────────────────────────────────────────────────────────┤
│ order_id: $order->id                                              │
│ old_status: $order->getOriginal('status')                         │
│ new_status: $order->status                                        │
│ old_payment_status: $order->getOriginal('payment_status')         │
│ new_payment_status: $order->payment_status                        │
│ note: (optional)                                                  │
│ changed_by: Auth::id()                                            │
│ created_at: now()                                                 │
└─────────────────────────────────────────────────────────────────┘
                        │
                        ▼
┌─────────────────────────────────────────────────────────────────┐
│         ORDER_HISTORIES TABLE AUDIT LOG                           │
│                                                                   │
│  Example:                                                         │
│  2024-12-18 10:30 | Order 001 | pending → confirmed | by: Admin  │
│  2024-12-19 14:45 | Order 001 | confirmed → completed | by:Admin │
│  2024-12-18 09:00 | Order 001 | pending → approved (payment)     │
│  2024-12-18 09:30 | Order 001 | pending → rejected (payment)     │
│                                                                   │
│  Invoice dapat menampilkan latest payment status dari tabel ini  │
└─────────────────────────────────────────────────────────────────┘
```

---

## Access Control

```
┌─────────────────────────────────────────────────────────────────┐
│                    ROUTE PROTECTION                               │
├─────────────────────────────────────────────────────────────────┤
│  Route: /invoice/{order_id}                                       │
│  │                                                                │
│  └─ Middleware: auth                                              │
│     ├─ Authenticated? → Allow                                     │
│     └─ Not Authenticated? → Redirect to /login                    │
│                                                                   │
│  OPTIONAL: Policy dapat ditambahkan untuk:                        │
│  ├─ Customer hanya bisa akses invoice mereka                      │
│  └─ Admin bisa akses semua invoice                                │
└─────────────────────────────────────────────────────────────────┘
```

---

## Invoice Content Sources

```
┌───────────────────────────────────────────────────────────────────────┐
│                          INVOICE CONTENT                               │
├───────────────────────────────────────────────────────────────────────┤
│                                                                        │
│  HEADER                                                                │
│  ├─ Order Code ◄────────────────────── from: orders.order_code         │
│  ├─ Invoice Date ◄─────────────────── from: orders.created_at          │
│  └─ Event Date ◄─────────────────── from: orders.event_date           │
│                                                                        │
│  CUSTOMER INFO                                                         │
│  ├─ Name ◄────────────────────────── from: users.name (relation)       │
│  ├─ Email ◄───────────────────────── from: users.email (relation)      │
│  ├─ Phone ◄───────────────────────── from: users.phone (relation)      │
│  └─ Address ◄──────────────────────── from: orders.alamat              │
│                                                                        │
│  ORDER DETAILS                                                         │
│  ├─ Package ◄──────────────────────── from: packages.name (relation)   │
│  └─ Status ◄───────────────────────── from: orders.status              │
│                                                                        │
│  SERVICES                                                              │
│  ├─ Service Name ◄────────────────── from: order_services.service_name │
│  ├─ Service Type ◄────────────────── from: order_services.is_required  │
│  └─ Price ◄────────────────────────── from: order_services.price       │
│                                                                        │
│  PAYMENT STATUS                                                        │
│  ├─ Status ◄────────────────────────── from: orders.payment_status     │
│  ├─ Approval Date ◄────────────────── from: orders.payment_approved_at │
│  ├─ Approved By ◄───────────────────── from: users.name (relation)    │
│  └─ Note ◄────────────────────────── from: orders.payment_note       │
│                                                                        │
│  PRICING                                                               │
│  ├─ Base Price ◄────────────────────── from: orders.base_price        │
│  ├─ Total Price ◄──────────────────── from: orders.total_price        │
│  └─ Additional ◄────────────────────── calculated: total - base        │
│                                                                        │
│  NOTES                                                                 │
│  └─ Notes ◄─────────────────────────── from: orders.notes             │
│                                                                        │
└───────────────────────────────────────────────────────────────────────┘
```

---

**Diagram created**: 18 December 2025  
**Format**: Text-based ERD  
**Status**: Documentation
