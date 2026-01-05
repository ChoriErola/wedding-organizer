<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Package;
use App\Models\Services;
use App\Models\Order;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class OrderController
{
    public function create()
    {
        $packages = Package::with('services')->get();
        $services = Services::all();

        return view('orders.checkout', compact('packages', 'services'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'package_id' => 'required|exists:packages,id',
            'event_date' => 'required|date',
            'services' => 'array',
            'services.*' => 'exists:services,id',
        ]);

        $user = Auth::user();
        if (! $user) {
            return redirect()->route('login');
        }

        $package = Package::findOrFail($request->input('package_id'));

        // create empty order record - pesanan service will snapshot services and update totals
        $order = Order::create([
            'user_id' => $user->id,
            'package_id' => $package->id,
            'order_code' => 'ORD-' . now()->format('Ymd') . '-' . Str::upper(Str::random(6)),
            'event_date' => $request->input('event_date'),
            'base_price' => 0,
            'total_price' => 0,
            'status' => 'confirmed',
            'notes' => $request->input('notes'),
        ]);

        // attach package's services & compute totals
        Order::handle($order, $package);

        // Optionally attach any extra services chosen (if you want to support adding custom picks)
        // For now we assume package services only.

        return redirect()->route('orders.thankyou', ['order' => $order->id]);
    }

    public function thankyou(Order $order)
    {
        return view('orders.thankyou', compact('order'));
    }
}
