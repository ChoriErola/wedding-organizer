<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laporan Pendapatan</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
        }
        h1 {
            margin-bottom: 0;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            border: 1px solid #000;
            padding: 6px;
        }
        th {
            background: #f3f3f3;
        }
    </style>
    
</head>
<body>
    <h1>Wedding Organizer</h1>
    <p>Laporan Pendapatan</p>

    <p>
        <strong>Periode:</strong>
        {{ $start }} s/d {{ $end }}
    </p>

    {{-- SUMMARY --}}
    <table>
        <tr>
            <th>Total Order</th>
            <th>Total Dibayar</th>
            <th>Belum Lunas</th>
        </tr>
        <tr>
            <td class="text-center">{{ $summary['total_orders'] }}</td>
            <td class="text-right">
                Rp {{ number_format($summary['total_paid'], 0, ',', '.') }}
            </td>
            <td class="text-right">
                Rp {{ number_format($summary['total_unpaid'], 0, ',', '.') }}
            </td>
        </tr>
    </table>

    {{-- DETAIL PER ORDER --}}
    <h3>Detail Order</h3>

    <table>
        <thead>
            <tr>
                <th>Kode Order</th>
                <th>Tanggal Order</th>
                <th>Tanggal Acara</th>
                <th>Customer</th>
                <th>Total</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($orders as $order)
                <tr>
                    <td>{{ $order->order_code }}</td>
                    <td>{{ $order->created_at->format('d-m-Y') }}</td>
                    <td>{{ optional($order->event_date)->format('d-m-Y') }}</td>
                    <td>{{ $order->customer->name ?? '-' }}</td>
                    <td class="text-right">
                        Rp {{ number_format($order->total_price, 0, ',', '.') }}
                    </td>
                    <td>{{ ucfirst($order->status) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <br>
    <p>
        Dicetak pada {{ now()->format('d M Y') }}
    </p>
</body>
</html>