<?php

namespace App\Livewire\Pelanggan;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class Orders extends Component
{
    use WithFileUploads;

    public $orders;
    public $selectedOrder;
    public $bukti_pembayaran = [];

    public function mount()
    {
        $this->loadOrders();
    }

    public function loadOrders()
    {
        $this->orders = Order::with(['package', 'services'])
            ->where('user_id', Auth::id())
            ->latest()
            ->get();
    }

    public function selectOrder($orderId)
    {
        $this->selectedOrder = Order::with(['package', 'services'])
            ->where('user_id', Auth::id())
            ->findOrFail($orderId);
    }

    public function uploadBukti()
    {
        $this->validate([
            'bukti_pembayaran.*' => 'image|max:2048',
        ]);

        $files = [];
        foreach ($this->bukti_pembayaran as $file) {
            $files[] = $file->store('bukti-pembayaran', 'public');
        }

        $this->selectedOrder->update([
            'bukti_pembayaran' => $files,
            'payment_status' => 'paid in progress',
        ]);

        $this->reset('bukti_pembayaran');
        $this->loadOrders();

        session()->flash('success', 'Bukti pembayaran berhasil dikirim.');
    }

    public function render()
    {
        return view('livewire.pelanggan.orders');
    }
}
