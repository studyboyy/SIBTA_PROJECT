@props([
    'message' => 'Menu ini dikunci.',
    'show' => false,
])

@if ($show)
    <span class="relative inline-flex items-center">
        <span tabindex="0" class="peer inline-flex items-center text-amber-600">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="size-4">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 0h10.5A2.25 2.25 0 0 1 19.5 12.75v6A2.25 2.25 0 0 1 17.25 21h-10.5A2.25 2.25 0 0 1 4.5 18.75v-6a2.25 2.25 0 0 1 2.25-2.25Z" />
            </svg>
        </span>
        <span role="tooltip"
            class="pointer-events-none absolute bottom-full left-1/2 z-20 mb-2 hidden w-56 -translate-x-1/2 rounded-lg bg-slate-900 px-2.5 py-1.5 text-[11px] font-medium leading-tight text-white shadow-lg peer-hover:block peer-focus:block">
            {{ $message }}
        </span>
    </span>
@endif
