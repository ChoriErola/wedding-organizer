<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use Illuminate\Support\Facades\Auth;

Route::get('/', function() {
    return view('pengunjung');
});

use App\Http\Controllers\OrderController;

// Frontend order creation (public view)
Route::get('/order/create', [OrderController::class, 'create'])->name('orders.create');

// Submit order (must be authenticated)
Route::post('/order', [OrderController::class, 'store'])->middleware('auth')->name('orders.store');

Route::get('/order/{order}/thankyou', [OrderController::class, 'thankyou'])->name('orders.thankyou');

// Route::view('dashboard', 'dashboard')
//     ->middleware(['auth', 'verified'])
//     ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('pelanggan.dashboard');
    })->name('dashboard');
});

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::get('/login', Login::class)->name('login');
Route::get('/register', Register::class)->name('register');

require __DIR__.'/auth.php';


// Central logout route: invalidate session then redirect to welcome page.
Route::post('/logout', function (\Illuminate\Http\Request $request) {
    Auth::guard('web')->logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/');
})->middleware('auth')->name('logout');
// Filament panel logout (panel path is '/panel')
Route::post('/panel/logout', function (\Illuminate\Http\Request $request) {
    Auth::guard('web')->logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/');
})->middleware('auth')->name('filament.panel.auth.logout');
