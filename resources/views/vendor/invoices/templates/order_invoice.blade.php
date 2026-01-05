<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice Wedding Organizer</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #333;
        }

        /* ===== HEADER ===== */
        .header {
            margin-bottom: 30px;
            border-bottom: 3px solid #0f4c63;
            padding-bottom: 15px;
        }
        
        .logo {   
            margin-top: -60px;
            position: absolute;
            width: 200px;
            height: auto;
        }

        .logo-kanan {
            right: 0;
            top: 0;
        }

        .company {
            flex: 1;
        }

        .company h2 {
            margin: 0;
            font-size: 16px;
            text-transform: uppercase;
        }

        .status {
            margin: 8px 0;
            font-weight: bold;
            font-size: 13px;
            color: #0f4c63;
            text-transform: uppercase;
        }

        /* ===== SECTION ===== */
        .section {
            margin: 20px 0;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 30px;
        }

        .section-customer {
            flex: 1;
            float: left;
        }

        .section .invoice-info {
            text-align: right;
        }

        /* ===== TABLE ===== */
        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: #0f4c63;
            padding: 8px;
            border: 1px solid #ddd;
            text-align: center;
            color: #fff;
        }

        td {
            padding: 8px;
            border: 1px solid #ddd;
            text-align: center;
        }

        /* ===== PAYMENT SUMMARY ===== */
        .payment-summary {
            margin-top: 30px;
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .payment-summary td {
            padding: 12px 15px;
            border: none;
            text-align: right;
        }

        .payment-summary tr:first-child td {
            border-top: 2px solid #0f4c63;
            border-bottom: 2px solid #0f4c63;
            background: #f3f4f6;
            font-weight: bold;
            font-size: 13px;
            padding-top: 15px;
            padding-bottom: 15px;
        }

        .payment-summary tr:last-child td {
            background: #0f4c63;
            color: #fff;
            font-weight: bold;
            font-size: 15px;
            border-bottom: 2px solid #0f4c63;
            padding-top: 15px;
            padding-bottom: 15px;
        }

        .payment-summary tr:last-child td:first-child {
            text-align: left;
        }

        /* ===== PAYMENT NOTES ===== */
        .payment-notes {
            font-size: 13px;
            line-height: 1;
        }

        .payment-notes strong {
            color: #000000;
            display: block;
            margin-bottom: 10px;
        }

        /* ===== PAYMENT SECTION ===== */
        .payment-section {
            display: flex;
            justify-content: space-between;
        }

        .payment-section .payment-info {
            flex: 1;
            font-size: 13px;
            margin: 0 auto 0 0;
            float: left;
        }

        .payment-section .payment-notes {
            flex: 1;
            margin-top: 0;
            text-align: right;
        }

        /* ===== NOTES & PAYMENT ===== */
        .notes, .payment-info {
            font-size: 11px;
        }

        /* ===== SIGNATURE ===== */
        .signature {
            margin-top: 150px;
            text-align: right;
            font-size: 13px;
        }
    </style>
</head>
<body>

    {{-- HEADER --}}
<div class="header">
    <div class="company">
        <h2>{{ $seller->name }}</h2>
        <div>{{ $seller->address }}</div>
        <div>{{ $seller->custom_fields['Phone'] ?? '-' }}</div>
        <div>{{ $seller->custom_fields['Email'] ?? '-' }}</div>
    </div>
    <img 
        src="{{ public_path('images/p-projectindonesia.png') }}"
        alt="Logo"
        class="logo logo-kanan"
    >
</div>

{{-- CUSTOMER --}}
<div class="section">
    <div class="section-customer">
        <strong>DITAGIH KEPADA:</strong><br>
        {{ $buyer->name }}<br>
        {{ $buyer->address }}<br><br>

        <strong>Acara:</strong> {{ $order->acara ?? '-' }}<br>
        <strong>Tanggal Acara:</strong>
        {{ $order->event_date ? \Carbon\Carbon::parse($order->event_date)->format('d F Y') : '-' }}<br>
        <strong>Lokasi:</strong> {{ $order->alamat ?? '-' }}
    </div>
    <div class="invoice-info">
        <h1>INVOICE</h1>

        @if($order->status)
            <div class="status">
                {{ strtoupper($order->status) }}
            </div>
        @endif

        <div><strong>No:</strong> WO-{{ $order->order_code }}</div>
        <div>
            <strong>Tanggal:</strong>
            {{ \Carbon\Carbon::parse($order->created_at)->format('d F Y') }}
        </div>
    </div>
</div>

{{-- ITEMS --}}
    <table>
        <thead>
            <tr>
                <th>Deskripsi</th>
                <th>Harga</th>
                <th>Qty</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $item)
            <tr>
                <td>{{ $item->title }}</td>
                <td>Rp {{ number_format($item->price_per_unit, 0, ',', '.') }}</td>
                <td>{{ $item->quantity }}</td>
                <td>
                    Rp {{ number_format($item->price_per_unit * $item->quantity, 0, ',', '.') }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

{{-- TOTAL --}}
<table class="payment-summary">
    <tr>
        <td style="text-align: right;">Total</td>
        <td style="text-align: right;">
            Rp {{ number_format($order->total_price, 0, ',', '.') }}
        </td>
    </tr>
</table>

{{-- NOTES --}}
@if(!empty($order->notes))
<div class="notes">
    <strong>Catatan</strong><br><br>
    {!! nl2br(e($order->notes)) !!}
</div>
@endif

{{-- PAYMENT INFO & PAYMENT NOTES --}}
<div class="payment-section">
    {{-- PAYMENT INFO --}}
    <div class="payment-info">
        <strong>INSTRUKSI PEMBAYARAN</strong><br><br>
        Bank BCA<br>
        No Rek: <strong>1192094903</strong><br>
        A/N Hendri Purnama Putra<br><br>

        Pelunasan maksimal H+3 setelah
        {{ $order->event_date ? \Carbon\Carbon::parse($order->event_date)->format('d F Y') : '-' }}
    </div>

    {{-- PAYMENT NOTES --}}
    @if($order->payment_note)
    <div class="payment-notes">
        <strong>Catatan Pembayaran</strong><br><br>
        @foreach(explode("\n", $order->payment_note) as $note)
            @if(trim($note))
            {{ trim($note) }}<br>
            @endif
        @endforeach
    </div>
    @endif
</div>

{{-- SIGNATURE --}}
<div class="signature">
    Hormat Kami,<br><br><br>
    <strong>Hendri Purnama Putra</strong><br>
    Company CEO
</div>

</body>
</html>
