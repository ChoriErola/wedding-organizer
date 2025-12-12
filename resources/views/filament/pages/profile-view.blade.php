<x-filament-panels::page>
    {{ $this->profileInfolist }}

    @if (Auth::user()->role === 'admin')
        <div class="mt-6">
            <x-filament::button tag="a" href="{{ route('filament.panel.resources.users.edit', Auth::id()) }}">
                Edit Profil
            </x-filament::button>
        </div>
    @endif
</x-filament-panels::page>
