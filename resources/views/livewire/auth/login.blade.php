{{-- <div>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />


    <form wire:submit="login">
        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email Address')" />
            <x-text-input 
                wire:model="form.email" 
                id="email" 
                class="block mt-1 w-full" 
                type="email" 
                name="email" 
                required 
                autofocus 
                autocomplete="username" 
            />
            <x-input-error :messages="$errors->get('form.email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input 
                wire:model="form.password" 
                id="password" 
                class="block mt-1 w-full"
                type="password"
                name="password"
                required 
                autocomplete="current-password" 
            />
            <x-input-error :messages="$errors->get('form.password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember" class="inline-flex items-center">
                <input 
                    wire:model="form.remember" 
                    id="remember" 
                    type="checkbox" 
                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" 
                    name="remember"
                >
                <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
            </label>
        </div>

        <!-- Forgot Password & Register Links -->
        <div class="flex items-center justify-between mt-4">
            @if (Route::has('password.request'))
                <a 
                    class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" 
                    href="{{ route('password.request') }}" 
                    wire:navigate
                >
                    {{ __('Forgot your password?') }}
                </a>
            @endif
        </div>

        <!-- Submit & Register Button -->
        <div class="flex items-center justify-between mt-4">
            @if (Route::has('register'))
                <a 
                    class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" 
                    href="{{ route('register') }}" 
                    wire:navigate
                >
                    {{ __('Belum Registrasi?') }}
                </a>
            @endif
            <x-primary-button>
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>
</div> --}}

<div class="card bg-gray-500 text-white" style="border-radius: 1rem;">
    <div class="card-body p-5">
        <a
            href="/"
            wire:navigate
            class="position-absolute top-0 start-0 m-3 text-white fs-4"
            title="Kembali ke Dashboard"
        >
            <i class="bi bi-arrow-left"></i>
        </a>
        <!-- Session Status -->
        <x-auth-session-status
            class="mb-4 text-center"
            :status="session('status')"
        />

        <form wire:submit="login">
            <div class="mb-md-4 mt-md-2 pb-3 text-center">
                <h2 class="fw-bold mb-2 text-uppercase">Login</h2>
                <p class="text-white-50 mb-4">
                    Silahkan masukkan email dan password
                </p>
            </div>
            <!-- Email -->
            <div class="form-outline form-white mb-4">
                <x-input-label
                    for="email"
                    :value="__('Masukkan Email')"
                    class="text-white"
                />
                <x-text-input
                    wire:model.live="form.email"
                    id="email"
                    type="email"
                    class="form-control form-control-lg"
                    required
                    autofocus
                    autocomplete="username"
                />
                <x-input-error
                    :messages="$errors->get('form.email')"
                    class="mt-2 text-danger"
                />
            </div>
            <!-- Password -->
            <div class="form-outline form-white mb-4">
                <x-input-label
                    for="password"
                    :value="__('Password')"
                    class="text-white"
                />
                <x-text-input
                    wire:model.live="form.password"
                    id="password"
                    type="password"
                    class="form-control form-control-lg"
                    required
                    autocomplete="current-password"
                />
                <x-input-error
                    :messages="$errors->get('form.password')"
                    class="mt-2 text-danger"
                />
            </div>
            <!-- Remember -->
            <div class="form-check mb-4">
                <input
                    wire:model="form.remember"
                    class="form-check-input"
                    type="checkbox"
                    id="remember"
                >
                <label class="form-check-label text-white" for="remember">
                    {{ __('Remember me') }}
                </label>
            </div>
            <!-- Submit -->
            <div class="d-grid mb-4 bg-dark rounded">
                <button
                    type="submit"
                    class="btn btn-outline-light btn-lg"
                    wire:loading.attr="disabled"
                >
                    {{ __('Log in') }}
                </button>
            </div>
            <!-- Links -->
            <div class="d-flex justify-content-between">
                @if (Route::has('password.request'))
                    <a
                        href="{{ route('password.request') }}"
                        class="text-white"
                    >
                        {{ __('Forgot your password?') }}
                    </a>
                @endif
                @if (Route::has('register'))
                    <a
                        href="{{ route('register') }}"
                        class="text-white fw-bold"
                        wire:navigate
                    >
                        {{ __('Belum Registrasi?') }}
                    </a>
                @endif
            </div>
        </form>

    </div>
</div>