<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\OrderController;
use App\Models\Order;
use App\Models\AboutUs;
use App\Models\PortfolioImage;
use App\Models\Package;
use App\Models\Services;
use App\Services\OrderInvoiceService;
use App\Livewire\Auth\Login;
use App\Livewire\Pelanggan\Orders;
use App\Livewire\Pelanggan\OrdersCreate;
use App\Models\ContactUs;

Route::get('/login', Login::class)
    ->middleware('guest')
    ->name('login');

// logout pelanggan
Route::post('/logout', function (\Illuminate\Http\Request $request) {
    Auth::guard('web')->logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/');
})->middleware('auth')->name('logout');
// logout admin dan pemilik
Route::post('/panel/logout', function (\Illuminate\Http\Request $request) {
    Auth::guard('web')->logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/');
})->middleware('auth')->name('filament.panel.auth.logout');

// view profile pelanggan
Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::get('/', function() {
    $aboutUs = AboutUs::first();
    $portfolios = PortfolioImage::all(); 
    $contactUs = ContactUs::first();
    $packages = Package::all();
    $services = Services::where('is_active', true)->get();
    return view('pengunjung', compact('aboutUs', 'portfolios', 'contactUs', 'packages', 'services'));
});

// dashboard pelanggan
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        $aboutUs = AboutUs::first();
        $portfolios = PortfolioImage::all();
        $contactUs = ContactUs::first();
        $packages = Package::with('services')->get();
        $services = Services::where('is_active', true)->get();
        return view('pelanggan.dashboard', compact('aboutUs', 'portfolios', 'contactUs', 'packages', 'services'));
    })->name('dashboard');
    Route::get('/pelanggan/pesanan', Orders::class)
        ->name('pelanggan.pesanan');
    Route::get('/pelanggan/pesanan/buat', OrdersCreate::class)
        ->name('pelanggan.pesanan.create');
    Route::get('/pelanggan/pesanan/{order}/invoice', function (Order $order) {
        return \App\Livewire\Pelanggan\InvoiceShow::generate($order);
    })->name('pelanggan.pesanan.invoice');
});

// Frontend order creation (public view)
Route::get('/order/create', [OrderController::class, 'create'])->name('orders.create');
Route::post('/order', [OrderController::class, 'store'])->middleware('auth')->name('orders.store');
Route::get('/order/{order}/thankyou', [OrderController::class, 'thankyou'])->name('orders.thankyou');

Route::get('/print-invoice/{order}', function (Order $order) {
    // Pastikan relasi diload agar tidak error di service
    $order->load(['customer', 'services']); 
    
    return response(OrderInvoiceService::generate($order), 200)
        ->header('Content-Type', 'application/pdf')
        ->header('Content-Disposition', 'inline; filename="invoice-' . $order->order_code . '.pdf"');
})->name('order_invoice')->middleware(['auth']);

require __DIR__.'/auth.php';

