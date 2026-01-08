{{-- <div>
    <form wire:submit="register">
        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input wire:model="name" id="name" class="block mt-1 w-full" type="text" name="name" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input wire:model="email" id="email" class="block mt-1 w-full" type="email" name="email" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input wire:model="password" id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

            <x-text-input wire:model="password_confirmation" id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}" wire:navigate>
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>
</div> --}}
<div class="card bg-gray-500 text-white" style="border-radius: 1rem;">
    <div class="card-body p-5">
        <a
            href="/"
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

        <form wire:submit="register">
            <div class="mb-md-4 mt-md-2 pb-3 text-center">
                <h2 class="fw-bold mb-2 text-uppercase">Register</h2>
                <p class="text-white-50 mb-4">
                    Silahkan masukkan data diri Anda
                </p>
            </div>
            <!-- Name -->
            <div class="form-outline form-white mb-4">
                <x-input-label for="name" :value="__('Name')" class="text-white"/>
                <x-text-input wire:model="name" id="name" class="form-control form-control-lg" type="text" name="name" required autofocus autocomplete="name" />
                <x-input-error :messages="$errors->get('name')" class="mt-2 text-danger"/>
            </div>

            <!-- Email Address -->
            <div class="form-outline form-white mb-4">
                <x-input-label for="email" :value="__('Email')" class="text-white"/>
                <x-text-input wire:model="email" id="email" class="form-control form-control-lg" type="email" name="email" required autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-2 text-danger"/>
            </div>

            <!-- Password -->
            <div class="form-outline form-white mb-4">
                <x-input-label for="password" :value="__('Password')" class="text-white"/>

                <x-text-input wire:model="password" id="password" class="form-control form-control-lg"
                                type="password"
                                name="password"
                                required autocomplete="new-password" />

                <x-input-error :messages="$errors->get('password')" class="mt-2 text-danger" />
            </div>

            <!-- Confirm Password -->
            <div class="form-outline form-white mb-4">
                <x-input-label for="password_confirmation" :value="__('Confirm Password')" class="text-white"/>

                <x-text-input wire:model="password_confirmation" id="password_confirmation" class="form-control form-control-lg"
                                type="password"
                                name="password_confirmation" required autocomplete="new-password" />

                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-danger" />
            </div>

            <div class="flex items-center justify-end mt-4">
                <a class="underline text-sm text-white hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}" wire:navigate>
                    {{ __('Sudah Daftar?') }}
                </a>

                <x-primary-button class="ms-4">
                    {{ __('Register') }}
                </x-primary-button>
            </div>
        </form>
    </div>  
</div>