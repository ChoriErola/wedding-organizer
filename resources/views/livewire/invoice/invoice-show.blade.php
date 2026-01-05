<div class="invoice-container bg-white p-8 rounded-lg shadow-lg max-w-4xl mx-auto">
    <!-- Invoice Header -->
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

    <!-- Customer Information -->
    <div class="grid grid-cols-2 gap-8 mb-8">
        <div>
            <h3 class="font-semibold text-gray-800 mb-3 text-lg">Dari:</h3>
            <div class="text-gray-700">
                <p class="font-semibold">Wedding Organizer</p>
                <p>Layanan Pernikahan Profesional</p>
            </div>
        </div>
        <div>
            <h3 class="font-semibold text-gray-800 mb-3 text-lg">Untuk:</h3>
            <div class="text-gray-700">
                <p class="font-semibold">{{ $order->customer->name }}</p>
                <p>{{ $order->customer->email }}</p>
                <p>{{ $order->customer->phone ?? 'N/A' }}</p>
                @if($order->alamat)
                    <p class="mt-2">{{ $order->alamat }}</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Order Details -->
    <div class="bg-gray-50 p-6 rounded-lg mb-8">
        <h3 class="font-semibold text-gray-800 mb-4 text-lg">Detail Pesanan</h3>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <p class="text-gray-600">Paket:</p>
                <p class="font-semibold text-gray-800">{{ $order->package->name ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-gray-600">Status Pesanan:</p>
                <p class="font-semibold text-gray-800">
                    <span class="px-3 py-1 rounded-full text-sm
                        @if($order->status == 'pending') bg-yellow-100 text-yellow-800
                        @elseif($order->status == 'confirmed') bg-blue-100 text-blue-800
                        @elseif($order->status == 'completed') bg-green-100 text-green-800
                        @elseif($order->status == 'cancelled') bg-red-100 text-red-800
                        @else bg-gray-100 text-gray-800 @endif">
                        {{ ucfirst($order->status) }}
                    </span>
                </p>
            </div>
        </div>
    </div>

    <!-- Services Table -->
    <div class="mb-8">
        <h3 class="font-semibold text-gray-800 mb-4 text-lg">Layanan yang Dipilih</h3>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-200 border-b-2 border-gray-300">
                        <th class="px-4 py-3 text-left text-gray-800 font-semibold">Nama Layanan</th>
                        <th class="px-4 py-3 text-left text-gray-800 font-semibold">Tipe</th>
                        <th class="px-4 py-3 text-right text-gray-800 font-semibold">Harga</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($order->services as $orderService)
                        <tr class="border-b border-gray-200 hover:bg-gray-50">
                            <td class="px-4 py-3 text-gray-700">{{ $orderService->service_name }}</td>
                            <td class="px-4 py-3 text-gray-600">
                                <span class="text-sm
                                    @if($orderService->is_required) 
                                        inline-block px-2 py-1 bg-blue-100 text-blue-800 rounded
                                    @else 
                                        inline-block px-2 py-1 bg-gray-100 text-gray-800 rounded
                                    @endif">
                                    {{ $orderService->is_required ? 'Paket' : 'Tambahan' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right text-gray-800 font-semibold">
                                Rp {{ number_format($orderService->price, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-4 py-3 text-center text-gray-500">Tidak ada layanan yang dipilih</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Payment Status -->
    <div class="bg-blue-50 p-6 rounded-lg mb-8 border border-blue-200">
        <h3 class="font-semibold text-gray-800 mb-4 text-lg">Status Pembayaran</h3>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <p class="text-gray-600">Status Pembayaran:</p>
                <p class="font-semibold text-lg">
                    <span class="px-3 py-1 rounded-full text-sm
                        @if($order->payment_status == 'pending') bg-yellow-100 text-yellow-800
                        @elseif($order->payment_status == 'approved') bg-green-100 text-green-800
                        @elseif($order->payment_status == 'rejected') bg-red-100 text-red-800
                        @else bg-gray-100 text-gray-800 @endif">
                        {{ ucfirst(str_replace('_', ' ', $order->payment_status)) }}
                    </span>
                </p>
            </div>
            <div>
                <p class="text-gray-600">Tanggal Verifikasi:</p>
                <p class="font-semibold">{{ $order->payment_approved_at ? $order->payment_approved_at->translatedFormat('d F Y H:i') : '-' }}</p>
            </div>
            @if($order->payment_note)
                <div class="col-span-2">
                    <p class="text-gray-600">Catatan Pembayaran:</p>
                    <p class="font-semibold text-gray-800">{{ $order->payment_note }}</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Price Summary -->
    <div class="bg-gray-50 p-6 rounded-lg mb-8 border border-gray-300">
        <div class="space-y-3">
            <div class="flex justify-between text-gray-700">
                <span>Harga Paket:</span>
                <span>Rp {{ number_format($order->base_price, 0, ',', '.') }}</span>
            </div>
            @if($order->total_price > $order->base_price)
                <div class="flex justify-between text-gray-700">
                    <span>Layanan Tambahan:</span>
                    <span>Rp {{ number_format($order->total_price - $order->base_price, 0, ',', '.') }}</span>
                </div>
            @endif
            <div class="border-t-2 border-gray-300 pt-3 flex justify-between text-xl font-bold text-gray-900">
                <span>Total Harga:</span>
                <span>Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>

    <!-- Notes -->
    @if($order->notes)
        <div class="bg-yellow-50 p-4 rounded-lg mb-8 border border-yellow-200">
            <p class="text-gray-600 font-semibold mb-2">Catatan Tambahan:</p>
            <p class="text-gray-700">{{ $order->notes }}</p>
        </div>
    @endif

    <!-- Footer -->
    <div class="border-t-2 border-gray-300 pt-6 text-center text-gray-600 text-sm">
        <p>Terima kasih atas kepercayaan Anda. Kami berkomitmen memberikan layanan terbaik untuk acara spesial Anda.</p>
        <p class="mt-2">Invoice ini dibuat pada {{ now()->translatedFormat('d F Y H:i') }}</p>
    </div>

    <!-- Print Button -->
    <div class="mt-8 flex justify-center gap-4">
        <button onclick="window.print()" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg transition">
            🖨️ Cetak Invoice
        </button>
        <button onclick="window.history.back()" class="bg-gray-600 hover:bg-gray-700 text-white font-semibold py-2 px-6 rounded-lg transition">
            Kembali
        </button>
    </div>
</div>

<style>
    @media print {
        body {
            margin: 0;
            padding: 0;
        }
        
        .invoice-container {
            max-width: 100%;
            box-shadow: none;
            border: none;
        }
        
        button {
            display: none;
        }
    }
</style>
