<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class Register extends Component
{
    public function render()
    {
        return view('livewire.auth.register');
    }

    public $name;
    public $email;
    public $password;
    public $password_confirmation;

    public function register()
    {
    $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'role' => 'pelanggan', // default
        ]);

        Auth::login($user);

        return redirect('/dashboard');
    }
}
