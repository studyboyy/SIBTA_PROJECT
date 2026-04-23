@if ($paginator->hasPages())
    <nav class="flex items-center justify-between border-t border-gray-200 px-4 sm:px-0">

        <!-- PREVIOUS -->
        <div class="-mt-px flex w-0 flex-1">
            @if ($paginator->onFirstPage())
                <span class="inline-flex items-center pt-4 pr-1 text-sm text-gray-400">
                    Previous
                </span>
            @else
                <button wire:click="previousPage('{{ $paginator->getPageName() }}')"
                    class="inline-flex items-center border-t-2 border-transparent pt-4 pr-1 text-sm text-gray-500 hover:border-gray-300 hover:text-gray-700">

                    <svg class="mr-3 size-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path
                            d="M18 10a.75.75 0 0 1-.75.75H4.66l2.1 1.95a.75.75 0 1 1-1.02 1.1l-3.5-3.25a.75.75 0 0 1 0-1.1l3.5-3.25a.75.75 0 1 1 1.02 1.1l-2.1 1.95h12.59A.75.75 0 0 1 18 10Z" />
                    </svg>

                    Previous
                </button>
            @endif
        </div>

        <!-- PAGE NUMBERS -->
        <div class="hidden md:-mt-px md:flex">

            @foreach ($elements as $element)
                <!-- DOTS -->
                @if (is_string($element))
                    <span class="px-4 pt-4 text-sm text-gray-500">{{ $element }}</span>
                @endif

                <!-- NUMBERS -->
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span
                                class="inline-flex items-center border-t-2 border-indigo-500 px-4 pt-4 text-sm font-medium text-indigo-600">
                                {{ $page }}
                            </span>
                        @else
                            <button wire:click="gotoPage({{ $page }}, '{{ $paginator->getPageName() }}')"
                                class="inline-flex items-center border-t-2 border-transparent px-4 pt-4 text-sm text-gray-500 hover:border-gray-300 hover:text-gray-700">
                                {{ $page }}
                            </button>
                        @endif
                    @endforeach
                @endif
            @endforeach

        </div>

        <!-- NEXT -->
        <div class="-mt-px flex w-0 flex-1 justify-end">
            @if ($paginator->hasMorePages())
                <button wire:click="nextPage('{{ $paginator->getPageName() }}')"
                    class="inline-flex items-center border-t-2 border-transparent pt-4 pl-1 text-sm text-gray-500 hover:border-gray-300 hover:text-gray-700">

                    Next

                    <svg class="ml-3 size-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path
                            d="M2 10a.75.75 0 0 1 .75-.75h12.59l-2.1-1.95a.75.75 0 1 1 1.02-1.1l3.5 3.25a.75.75 0 0 1 0 1.1l-3.5 3.25a.75.75 0 1 1-1.02-1.1l2.1-1.95H2.75A.75.75 0 0 1 2 10Z" />
                    </svg>
                </button>
            @else
                <span class="inline-flex items-center pt-4 pl-1 text-sm text-gray-400">
                    Next
                </span>
            @endif
        </div>

    </nav>
@endif
