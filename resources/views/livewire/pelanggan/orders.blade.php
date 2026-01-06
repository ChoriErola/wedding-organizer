<div>
    <div style="margin-top: 80px; padding: 20px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 10px; margin: 20px;">
        <div class="container-lg">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 style="color: white; margin: 0; font-weight: 600;">Pesanan Anda</h4>
                    <p style="color: rgba(255,255,255,0.8); margin: 5px 0 0 0; font-size: 0.9rem;">Kelola dan buat pesanan catering baru</p>
                </div>
                <a href="{{ route('pelanggan.pesanan.create') }}" class="btn" style="background-color: #ff9800; color: white; border: none; padding: 10px 25px; font-weight: 600; border-radius: 5px; text-decoration: none; transition: all 0.3s;">
                    <i class="bi bi-plus-circle" style="margin-right: 8px;"></i>Buat Pesanan Baru
                </a>
            </div>
        </div>
    </div>
    <div class="orders-content" style="margin-top: 20px;">
        <section class="h-100 gradient-custom">
            <div class="container py-5 h-100">

        @forelse ($orders as $order)
        <div class="row d-flex justify-content-center mb-5">
            <div class="col-lg-10 col-xl-8">

                <div class="card" style="border-radius: 10px;">
                    {{-- HEADER --}}
                    <div class="card-header">   
                </div>

                {{-- BODY --}}
                <div class="card-body p-4">

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <p class="lead fw-normal mb-0" style="color: #a8729a;">Detail Pesanan</p>
                        <p class="small text-muted mb-0">
                            Kode Order : {{ $order->order_code }}
                        </p>
                    </div>

                    {{-- ITEM / PAKET --}}
                    <div class="card shadow-0 border mb-4">
                        <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-2">
                                @if($order->package?->image)
                                    <img src="{{ asset('storage/' . $order->package->image) }}"
                                        class="img-fluid" alt="{{ $order->package->name }}" style="border-radius: 6px;">
                                @else
                                    <img src="{{ asset('assets/img/package.png') }}"
                                        class="img-fluid" alt="Package" style="border-radius: 6px;">
                                @endif
                            </div>

                            <div class="col-md-4">
                                <p class="text-muted mb-0 fw-bold">
                                    {{ $order->package->name ?? '-' }}
                                </p>
                                <p class="text-muted mb-0 small">
                                    Acara: {{ $order->acara }}
                                </p>
                                <p class="text-muted mb-0 small">
                                    Tanggal: {{ $order->event_date?->format('d M Y') }}
                                </p>
                            </div>

                            <div class="col-md-3 text-center">
                                <p class="text-muted mb-0 small">Status</p>
                                <span class="badge bg-secondary">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </div>

                            <div class="col-md-3 text-center">
                                <p class="text-muted mb-0 small">Total</p>
                                <p class="fw-bold mb-0">
                                    Rp {{ number_format($order->total_price, 0, ',', '.') }}
                                </p>
                            </div>
                        </div>

                        {{-- PROGRESS --}}
                        <hr class="mb-4">

                        <div class="row align-items-center mb-4">
                            <div class="col-md-2">
                                <p class="text-muted mb-0 small">Progress</p>
                            </div>

                            <div class="col-md-10">
                                @php
                                $progress = match($order->status) {
                                    'pending' => 25,
                                    'confirmed' => 50,
                                    'paid in progress' => 75,
                                    'paid completed' => 75,
                                    'completed' => 100,
                                    'cancelled' => 0,
                                    default => 10,
                                };
                                @endphp

                                <div class="progress" style="height: 6px;">
                                    <div class="progress-bar"
                                        role="progressbar"
                                        style="width: {{ $progress }}%; background-color: #a8729a;">
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between mt-1">
                                    <small>Cancel</small>
                                    <small>Pending</small>
                                    <small>Confirmed</small>
                                    <small>Paid in progress</small>
                                    <small>Paid completed</small>
                                    <small>Completed</small>
                                </div>
                            </div>
                        </div>

                        {{-- LAYANAN TAMBAHAN (OPTIONAL) --}}
                        @php
                            $optionalServices = $order->services()
                                ->whereNotIn('service_id', $order->package->services->pluck('id')->toArray())
                                ->get();
                        @endphp
                        
                        @if($optionalServices->isNotEmpty())
                        <div class="mb-4">
                            <hr class="mb-3">
                            <p class="text-muted mb-3 fw-bold">Layanan Tambahan (Opsional)</p>
                            <div class="row">
                                @foreach($optionalServices as $service)
                                <div class="col-md-6 mb-2">
                                    <div class="p-2" style="background-color: #f8f9fa; border-radius: 6px; border-left: 3px solid #ff9800;">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="small fw-bold text-dark">{{ $service->service_name }}</span>
                                            <span class="small text-muted">Rp {{ number_format($service->price, 0, ',', '.') }}</span>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                        {{-- INFO TAMBAHAN --}}
                        <div class="d-flex justify-content-between pt-2">
                            <p class="text-muted mb-0">
                                Alamat Acara
                            </p>
                            <p class="text-muted mb-0 text-end">
                                {{ $order->alamat }}
                            </p>
                        </div>
    
                        <div class="d-flex justify-content-between">
                            <p class="text-muted mb-0">Catatan</p>
                            <p class="text-muted mb-0">
                                {{ $order->notes ?? '-' }}
                            </p>
                        </div>
                        <div class="d-flex align-items-center">
                            <p class="text-muted mb-0">Lihat Pembayaran</p>
                            @if($order->payment_note || (isset($order->bukti_pembayaran) && count($order->bukti_pembayaran ?? []) > 0))
                                <button type="button" 
                                        class="btn btn-sm btn-link p-0 ms-2" 
                                        style="color: #a8729a; text-decoration: none; font-size: 18px;"
                                        data-bs-toggle="modal" 
                                        data-bs-target="#paymentModal{{ $order->id }}">
                                    <i class="bi bi-eye" style="cursor: pointer;"></i>
                                </button>
                            @endif
                        </div>

                        {{-- PAYMENT MODAL --}}
                        @if($order->payment_note || (isset($order->bukti_pembayaran) && count($order->bukti_pembayaran ?? []) > 0))
                        <div class="modal fade" id="paymentModal{{ $order->id }}" tabindex="-1" aria-labelledby="paymentModalLabel{{ $order->id }}" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header" style="background-color: #a8729a; border: none;">
                                        <h5 class="modal-title" id="paymentModalLabel{{ $order->id }}" style="color: white; font-weight: 600;">Bukti Pembayaran - {{ $order->order_code }}</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        @if($order->payment_note)
                                            <div class="mb-4">
                                                <h6 style="font-weight: 600; color: #333; margin-bottom: 12px;">Catatan Pembayaran:</h6>
                                                <div style="background-color: #f8f9fa; padding: 12px; border-radius: 6px; border-left: 4px solid #a8729a;">
                                                    {!! nl2br(e($order->payment_note)) !!}
                                                </div>
                                            </div>
                                        @endif

                                        @if(isset($order->bukti_pembayaran) && count($order->bukti_pembayaran ?? []) > 0)
                                            <div>
                                                <h6 style="font-weight: 600; color: #333; margin-bottom: 12px;">Bukti Pembayaran:</h6>
                                                
                                                <!-- Preview Images -->
                                                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(120px, 1fr)); gap: 12px; margin-bottom: 20px;">
                                                    @foreach($order->bukti_pembayaran as $index => $bukti)
                                                        <div style="position: relative; border-radius: 8px; overflow: hidden; border: 2px solid #ddd; cursor: pointer;">
                                                            <img src="{{ asset('storage/' . $bukti) }}" 
                                                                 alt="Bukti Pembayaran" 
                                                                 style="width: 100%; height: 120px; object-fit: cover;"
                                                                 onclick="showPaymentImage('{{ asset('storage/' . $bukti) }}', '{{ $order->order_code }}')">
                                                            <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; background: rgba(0,0,0,0.5); opacity: 0; transition: opacity 0.3s;"
                                                                 onmouseover="this.style.opacity='1'"
                                                                 onmouseout="this.style.opacity='0'"
                                                                 onclick="showPaymentImage('{{ asset('storage/' . $bukti) }}', '{{ $order->order_code }}')">
                                                                <i class="bi bi-zoom-in" style="color: white; font-size: 20px;"></i>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>

                                                <!-- Full Image View -->
                                                <div id="fullImageContainer{{ $order->id }}" style="display: none; text-align: center; margin-top: 20px; border-top: 1px solid #ddd; padding-top: 20px;">
                                                    <img id="fullImage{{ $order->id }}" src="" alt="Bukti Pembayaran" style="max-width: 100%; max-height: 500px; border-radius: 8px;">
                                                    <div style="margin-top: 12px;">
                                                        <button type="button" class="btn btn-sm btn-secondary" onclick="hidePaymentImage('{{ $order->id }}')">Sembunyikan Gambar</button>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <script>
                            function showPaymentImage(imageSrc, orderCode) {
                                // Find the container within the current order's modal
                                const orderId = '{{ $order->id }}';
                                const container = document.getElementById('fullImageContainer' + orderId);
                                const img = document.getElementById('fullImage' + orderId);
                                
                                if (container && img) {
                                    img.src = imageSrc;
                                    container.style.display = 'block';
                                    // Scroll to the image
                                    container.scrollIntoView({ behavior: 'smooth' });
                                }
                            }

                            function hidePaymentImage(orderId) {
                                const container = document.getElementById('fullImageContainer' + orderId);
                                if (container) {
                                    container.style.display = 'none';
                                }
                            }
                        </script>
                        @endif
                    </div>

                </div>

                {{-- FOOTER --}}
                <div class="card-footer border-0 px-4 py-4"
                    style="background-color: #a8729a; border-radius: 0 0 10px 10px;">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="text-white mb-0">
                            Total Pembayaran :
                            <span class="fw-bold">
                                Rp {{ number_format($order->total_price) }}
                            </span>
                        </h6>
                        <a href="{{ route('pelanggan.pesanan.invoice', $order->id) }}" 
                           target="_blank"
                           class="btn btn-sm"
                           style="background-color: white; color: #a8729a; border: none; padding: 8px 16px; font-weight: 600; border-radius: 5px; text-decoration: none; transition: all 0.3s;">
                            <i class="bi bi-file-pdf" style="margin-right: 6px;"></i>Invoice PDF
                        </a>
                    </div>
                </div>

                </div>
            </div>
        </div>
        @empty
        @endforelse

            </div>
        </section>
    </div>
