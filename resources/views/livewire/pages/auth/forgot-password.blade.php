<?php

use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.auth')] class extends Component {
    public string $email = '';

    public function submit()
    {
        $this->validate([
            'email' => 'required|email',
        ], [
            'email.required' => 'Email harus diisi',
            'email.email' => 'Email tidak valid',
        ]);

        $status = Password::sendResetLink($this->only('email'));

        if ($status === Password::RESET_LINK_SENT) {
            session()->flash('status', 'Link reset password telah dikirim!');
            return redirect()->route('login');
        } else {
            throw ValidationException::withMessages([
                'email' => __($status),
            ]);
        }
    }
};

?>

<!-- ROOT ELEMENT (WAJIB) -->
    <div class="card bg-gray-500 text-white" style="border-radius: 1rem;">
        <div class="card-body p-5">

            <!-- Back Button -->
            <a
                href="/"
                wire:navigate
                class="start-0 m-3 text-white fs-4"
                title="Kembali ke Dashboard"
            >
                <i class="bi bi-arrow-left"></i>
            </a>

            <form wire:submit="submit">
                <div class="mb-md-4 mt-md-2 pb-3 text-center">
                    <h2 class="fw-bold mb-2 text-uppercase">Forgot Password</h2>
                    <p class="text-white-50 mb-4">
                        Masukkan email Anda untuk reset password
                    </p>
                </div>

                <!-- Email -->
                <div class="form-outline form-white mb-4">
                    <x-input-label
                        for="email"
                        :value="__('Email Address')"
                        class="text-white"
                    />

                    <x-text-input
                        wire:model.live="email"
                        id="email"
                        type="email"
                        class="form-control form-control-lg"
                        required
                        autofocus
                        autocomplete="username"
                    />

                    <x-input-error
                        :messages="$errors->get('email')"
                        class="mt-2 text-danger"
                    />
                </div>

                <!-- Submit -->
                <div class="d-grid mb-4">
                    <button
                        type="submit"
                        class="btn btn-outline-light btn-lg"
                        wire:loading.attr="disabled"
                    >
                        <span wire:loading.remove>Send Reset Link</span>
                        <span wire:loading>Sending...</span>
                    </button>
                </div>

                <!-- Back to Login -->
                <div class="text-center">
                    <a
                        href="{{ route('login') }}"
                        class="text-white fw-bold small"
                    >
                        {{ __('Kembali ke Login') }}
                    </a>
                </div>
            </form>

        </div>
    </div>
