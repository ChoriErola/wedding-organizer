<form wire:submit.prevent="save" style="background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
    <!-- Form Content -->
    <div style="padding: 24px; display: grid; gap: 24px;">
        
        <!-- Row 1: Event Date & Event Type -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <!-- Order Code -->
            <div>
                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #2c3e50; font-size: 0.95rem;">Nomor Pesanan</label>
                <input type="text" value="{{ $order_code }}" readonly style="width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 0.95rem; font-family: monospace; background-color: #f8f9fa; color: #2c3e50;">
            </div>

            <!-- Nama Pelanggan -->
            <div>
                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #2c3e50; font-size: 0.95rem;">Nama Pelanggan</label>
                <input type="text" value="{{ $customer_name }}" readonly style="width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 0.95rem; font-family: inherit; background-color: #f8f9fa; color: #2c3e50;">
            </div>
        </div>

        <!-- Row 2: Tanggal Acara & Jenis Acara -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <!-- Tanggal Acara -->
            <div>
                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #2c3e50; font-size: 0.95rem;">Tanggal Acara</label>
                <input type="date" wire:model="event_date" style="width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 0.95rem; font-family: inherit; transition: border-color 0.2s;">
                @error('event_date') <span style="color: #e74c3c; font-size: 0.85rem; display: block; margin-top: 4px;">{{ $message }}</span> @enderror
            </div>

            <!-- Jenis Acara -->
            <div>
                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #2c3e50; font-size: 0.95rem;">Jenis Acara</label>
                <input type="text" wire:model="acara" placeholder="Wedding, Engagement, dll" style="width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 0.95rem; font-family: inherit; transition: border-color 0.2s;">
                @error('acara') <span style="color: #e74c3c; font-size: 0.85rem; display: block; margin-top: 4px;">{{ $message }}</span> @enderror
            </div>
        </div>

        <!-- Row 2: Paket Catering -->
        <div>
            <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #2c3e50; font-size: 0.95rem;">Paket Catering</label>
            <select wire:model.live="package_id" style="width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 0.95rem; font-family: inherit; transition: border-color 0.2s;">
                <option value="">-- Pilih Paket --</option>
                @foreach($packages as $package)
                    <option value="{{ $package->id }}">{{ $package->name }} — Rp {{ number_format($package->price, 0, ',', '.') }}</option>
                @endforeach
            </select>
            @error('package_id') <span style="color: #e74c3c; font-size: 0.85rem; display: block; margin-top: 4px;">{{ $message }}</span> @enderror
        </div>

        <!-- Row 3: Layanan Paket -->
        @if($selected_package && !empty($package_services))
        <div style="padding: 16px; background-color: #f8f9fa; border: 1px solid #e9ecef; border-radius: 6px;">
            <label style="display: block; margin-bottom: 12px; font-weight: 600; color: #2c3e50; font-size: 0.95rem;">Layanan Paket</label>
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 10px;">
                @foreach($selected_package->services as $service)
                    <label style="display: flex; align-items: flex-start; padding: 10px; background: white; border: 1px solid #ddd; border-radius: 6px; cursor: pointer; transition: all 0.2s;">
                        <input type="checkbox" wire:model.live="selected_service_ids" value="{{ $service->id }}" style="margin-right: 10px; margin-top: 2px; cursor: pointer; width: 18px; height: 18px; flex-shrink: 0;">
                        <div style="flex: 1;">
                            <span style="font-weight: 500; color: #2c3e50; display: block; font-size: 0.95rem;">{{ $service->name }}</span>
                            <small style="color: #7f8c8d; display: block; margin-top: 2px;">Rp {{ number_format($service->harga_layanan, 0, ',', '.') }}</small>
                        </div>
                    </label>
                @endforeach
            </div>
            @error('selected_service_ids') <span style="color: #e74c3c; font-size: 0.85rem; display: block; margin-top: 8px;">{{ $message }}</span> @enderror
        </div>
        @endif

        <!-- Row 4: Layanan Tambahan -->
        <div style="padding: 16px; background-color: #f8f9fa; border: 1px solid #e9ecef; border-radius: 6px;">
            <label style="display: block; margin-bottom: 12px; font-weight: 600; color: #2c3e50; font-size: 0.95rem;">Layanan Tambahan (Opsional)</label>
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 10px;">
                @foreach($services as $service)
                    <label style="display: flex; align-items: flex-start; padding: 10px; background: white; border: 1px solid #ddd; border-radius: 6px; cursor: pointer; transition: all 0.2s;">
                        <input type="checkbox" wire:model.live="optional_service_ids" value="{{ $service->id }}" style="margin-right: 10px; margin-top: 2px; cursor: pointer; width: 18px; height: 18px; flex-shrink: 0;">
                        <div style="flex: 1;">
                            <span style="font-weight: 500; color: #2c3e50; display: block; font-size: 0.95rem;">{{ $service->name }}</span>
                            <small style="color: #7f8c8d; display: block; margin-top: 2px;">Rp {{ number_format($service->harga_layanan, 0, ',', '.') }}</small>
                        </div>
                    </label>
                @endforeach
            </div>
            @error('optional_service_ids') <span style="color: #e74c3c; font-size: 0.85rem; display: block; margin-top: 8px;">{{ $message }}</span> @enderror
        </div>

        <!-- Row 5: Price Fields -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div>
                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #2c3e50; font-size: 0.95rem;">Harga Paket (Terpilih)</label>
                <input type="number" wire:model="base_price" readonly style="width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 6px; background-color: #f8f9fa; font-size: 0.95rem; font-family: inherit; display: none;">
                <div style="padding: 12px 16px; background-color: #fff3e0; border: 1px solid #ffe0b2; border-radius: 6px; font-weight: 600; color: #ff4a17; font-size: 1rem; text-align: center;">
                    Rp {{ number_format($base_price, 0, ',', '.') }}
                </div>
            </div>

            <div>
                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #2c3e50; font-size: 0.95rem;">Total Harga</label>
                <input type="number" wire:model="total_price" readonly style="width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 6px; background-color: #f8f9fa; font-size: 0.95rem; font-family: inherit; display: none;">
                <div style="padding: 12px 16px; background-color: #fff3e0; border: 1px solid #ffe0b2; border-radius: 6px; font-weight: 700; color: #ff4a17; font-size: 1.1rem; text-align: center;">
                    Rp {{ number_format($total_price, 0, ',', '.') }}
                </div>
            </div>
        </div>

        <!-- Row 6: Alamat Acara -->
        <div>
            <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #2c3e50; font-size: 0.95rem;">Alamat Acara</label>
            <textarea wire:model="alamat" placeholder="Masukkan alamat lengkap acara" style="width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 0.95rem; font-family: inherit; min-height: 100px; resize: vertical; transition: border-color 0.2s;"></textarea>
            @error('alamat') <span style="color: #e74c3c; font-size: 0.85rem; display: block; margin-top: 4px;">{{ $message }}</span> @enderror
        </div>

        <!-- Row 7: Catatan -->
        <div>
            <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #2c3e50; font-size: 0.95rem;">Catatan Tambahan</label>
            <textarea wire:model="notes" placeholder="Catatan khusus untuk pesanan Anda (opsional)" style="width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 0.95rem; font-family: inherit; min-height: 80px; resize: vertical; transition: border-color 0.2s;"></textarea>
            @error('notes') <span style="color: #e74c3c; font-size: 0.85rem; display: block; margin-top: 4px;">{{ $message }}</span> @enderror
        </div>

        <!-- Row 8: Bukti Pembayaran -->
        {{-- <div>
            <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #2c3e50; font-size: 0.95rem;">Bukti Pembayaran (Opsional)</label>
            <input type="file" wire:model="bukti_pembayaran" multiple accept="image/*" style="width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 0.95rem; cursor: pointer;">
            <small style="color: #7f8c8d; margin-top: 6px; display: block;">Format: JPG, PNG. Ukuran maksimal 2MB per file</small>
            @error('bukti_pembayaran.*') <span style="color: #e74c3c; font-size: 0.85rem; display: block; margin-top: 4px;">{{ $message }}</span> @enderror
        </div> --}}

    </div>

    <!-- Form Footer / Submit Button -->
    <div style="padding: 16px 24px; background-color: #f8f9fa; border-top: 1px solid #e9ecef; display: flex; gap: 12px; justify-content: flex-end;">
        <button type="submit" style="background: linear-gradient(135deg, #ff9800 0%, #f57c00 100%); color: white; padding: 10px 24px; border: none; border-radius: 6px; font-weight: 600; font-size: 0.95rem; cursor: pointer; transition: all 0.2s; box-shadow: 0 1px 3px rgba(255, 152, 0, 0.3);">
            Simpan Pesanan
        </button>
    </div>
</form>

