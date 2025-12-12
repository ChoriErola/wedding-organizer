<?php

namespace App\Livewire\Auth;
use Illuminate\Support\Facades\Auth;
use Filament\Facades\Filament;
use Livewire\Component;

class Login extends Component
{
    public $email;
    public $password;
    public $remember = false;

    public function login()
    {
        $this->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // GANTI BAGIAN INI
        // Dari: if (Filament::auth()->attempt(...))
        // Menjadi:
        if (Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {

            // GANTI BAGIAN INI JUGA
            // Dari: $user = Filament::auth()->user();
            // Menjadi:
            $user = Auth::user();

            // Logika di bawah ini sudah SEMPURNA, tidak perlu diubah.
            if (in_array($user->role, ['admin', 'pemilik'])) {
                // Gunakan Livewire redirect untuk pengalaman terbaik
                return $this->redirect(route('filament.panel.pages.dashboard'), navigate: true);
            }

            // Arahkan ke dashboard pelanggan
            return $this->redirect('/dashboard', navigate: true);
        }

        $this->addError('email', 'Email atau password salah.');
    }
    
    
    public function render()
    {
        return view('livewire.auth.login');
    }

}
