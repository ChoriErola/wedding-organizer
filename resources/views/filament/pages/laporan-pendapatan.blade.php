<x-filament-panels::page>
        <x-filament::section class="mt-6">
        {{-- HEADER --}}
        <x-slot name="heading">
            <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between w-full">
                {{-- Judul --}}
                <span class="text-lg font-semibold">
                    Detail Order
                </span>

                {{-- Action --}}
                <div class="flex gap-2">
                    <x-filament::button
                        color="primary"
                        wire:click="generate"
                        size="sm"
                    >
                        Tampilkan Laporan
                    </x-filament::button>

                    <x-filament::button
                        color="success"
                        wire:click="exportPdf"
                        size="sm"
                    >
                        Download
                    </x-filament::button>
                </div>
            </div>
        </x-slot>

        {{-- FILTER --}}
        <div class="mb-4">
            {{ $this->form }}
        </div>
        <br>
        {{-- TABLE --}}
        {{ $this->table }}
    </x-filament::section>

</x-filament-panels::page>
