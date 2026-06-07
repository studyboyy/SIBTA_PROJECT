@props(['name'])

<div>
    <div x-data="{ show: false }"
        x-on:open-modal.window="if ($event.detail.name === '{{ $name }}') show = true"
        x-on:close-modal.window="if ($event.detail.name === '{{ $name }}') show = false"
        x-show="show" wire:cloak
        x-transition:opacity
        class="modal fixed inset-0 z-50 flex items-end justify-center p-4 sm:items-center">

        {{-- Backdrop --}}
        <div x-show="show"
            x-transition:enter="ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm"></div>

        {{-- Panel --}}
        <div x-show="show"
            x-transition:enter="ease-out duration-200"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            class="relative z-50 w-full max-w-lg rounded-2xl bg-white shadow-2xl ring-1 ring-slate-900/5">
            <div class="p-6">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>
