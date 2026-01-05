<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthenticatedSessionController extends Controller
{
    /**
     * Handle an incoming authentication request.
     */
    public function store(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
       $request->validate([
        'email' => ['required', 'email', 'exists:users,email'],
        'password' => ['required'],
        ]);

        if (! Auth::attempt(
            $request->only('email', 'password'),
            $request->boolean('remember')
        )) {
            throw ValidationException::withMessages([
                'password' => 'Password salah.',
            ]);
        }

        $request->session()->regenerate();

        return $this->authenticated($request, Auth::user());
    }

    /**
     * Redirect user after login based on role.
     */
    protected function authenticated(Request $request, User $user)
    {
        // Admin / Pemilik → Filament Panel
        if (in_array($user->role, [User::ROLE_ADMIN, 'pemilik'])) {
            return redirect()->intended('/panel');
        }

        // Pelanggan → Dashboard Pelanggan
        if ($user->role === User::ROLE_PELANGGAN) {
            return redirect()->intended('/dashboard');
        }

        // Fallback
        return redirect('/');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
