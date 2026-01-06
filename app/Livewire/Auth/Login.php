<?php

namespace App\Livewire\Auth;

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.guest')]
class Login extends Component
{
    public LoginForm $form;

    public function login()
    {
        $this->form->validate();

        if (! Auth::attempt(
            $this->form->only('email', 'password'),
            $this->form->remember
        )) {
            $this->addError('form.email', 'Email atau password salah.');
            return;
        }

        session()->regenerate();

        $user = Auth::user();

        if (in_array($user->role, ['admin', 'pemilik'])) {
            $this->redirect('/panel');
        } else {
            $this->redirect('/dashboard');
        }
    }

    public function render()
    {
        return view('livewire.auth.login');
    }
}
