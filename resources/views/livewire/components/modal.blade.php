@props(['name'])

<div>
    <div x-data="{ show: false}"
    x-on:open-modal.window="
        if ($event.detail.name === '{{ $name }}') show = true
    "
    x-on:close-modal.window="
        if ($event.detail.name === '{{ $name}}') show = false
    " 
    x-show="show" wire:cloak
    x-transition-opacity class="modal fixed z-50 inset-0 flex items-center justify-center">
        <div x-show="show"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="absolute  inset-0 bg-gray-500/75 transition-opacity"></div>
        <div x-show="show"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            class="bg-white w-125 z-50 relative rounded-lg shadow-xl p-6">
            {{ $slot }}
        </div>

    </div>
</div>
