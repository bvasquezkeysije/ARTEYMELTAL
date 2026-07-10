@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex items-center justify-between gap-3">
        <div class="flex flex-1 justify-between sm:hidden">
            @if ($paginator->onFirstPage())
                <span class="relative inline-flex items-center rounded-lg border border-[#e5dec8] bg-[#faf8f2] px-4 py-2 text-sm text-[#8a7a57]">
                    {{ __('pagination.previous') }}
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="relative inline-flex items-center rounded-lg border border-[#d1be8a] bg-white px-4 py-2 text-sm text-[#5a4314] hover:bg-[#fff5dd]">
                    {{ __('pagination.previous') }}
                </a>
            @endif

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="relative ms-3 inline-flex items-center rounded-lg border border-[#d1be8a] bg-white px-4 py-2 text-sm text-[#5a4314] hover:bg-[#fff5dd]">
                    {{ __('pagination.next') }}
                </a>
            @else
                <span class="relative ms-3 inline-flex items-center rounded-lg border border-[#e5dec8] bg-[#faf8f2] px-4 py-2 text-sm text-[#8a7a57]">
                    {{ __('pagination.next') }}
                </span>
            @endif
        </div>

        <div class="hidden flex-1 sm:block">
            <p class="text-sm text-[#8a6a2e]">
                {!! __('Showing') !!}
                <span class="font-semibold text-[#2a2419]">{{ $paginator->firstItem() }}</span>
                {!! __('to') !!}
                <span class="font-semibold text-[#2a2419]">{{ $paginator->lastItem() }}</span>
                {!! __('of') !!}
                <span class="font-semibold text-[#2a2419]">{{ $paginator->total() }}</span>
                {!! __('results') !!}
            </p>
        </div>

        <div class="hidden sm:flex sm:flex-1 sm:justify-end">
            <span class="relative z-0 inline-flex items-center gap-2 rounded-lg">
                @if ($paginator->onFirstPage())
                    <span aria-disabled="true" aria-label="{{ __('pagination.previous') }}" class="inline-flex h-9 min-w-9 items-center justify-center rounded-lg border border-[#e5dec8] bg-[#faf8f2] px-3 text-sm text-[#8a7a57]">
                        <span aria-hidden="true">&lsaquo;</span>
                    </span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="{{ __('pagination.previous') }}" class="inline-flex h-9 min-w-9 items-center justify-center rounded-lg border border-[#d1be8a] bg-white px-3 text-sm text-[#5a4314] hover:bg-[#fff5dd]">
                        <span aria-hidden="true">&lsaquo;</span>
                    </a>
                @endif

                @foreach ($elements as $element)
                    @if (is_string($element))
                        <span aria-disabled="true" class="inline-flex h-9 min-w-9 items-center justify-center rounded-lg border border-[#e5dec8] bg-[#faf8f2] px-3 text-sm text-[#8a7a57]">{{ $element }}</span>
                    @endif

                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <span aria-current="page" class="inline-flex h-9 min-w-9 items-center justify-center rounded-lg border border-[#d1be8a] bg-[#f4ebd4] px-3 text-sm font-semibold text-[#2a2419]">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}" class="inline-flex h-9 min-w-9 items-center justify-center rounded-lg border border-[#d1be8a] bg-white px-3 text-sm text-[#5a4314] hover:bg-[#fff5dd]" aria-label="{{ __('Go to page :page', ['page' => $page]) }}">
                                    {{ $page }}
                                </a>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="{{ __('pagination.next') }}" class="inline-flex h-9 min-w-9 items-center justify-center rounded-lg border border-[#d1be8a] bg-white px-3 text-sm text-[#5a4314] hover:bg-[#fff5dd]">
                        <span aria-hidden="true">&rsaquo;</span>
                    </a>
                @else
                    <span aria-disabled="true" aria-label="{{ __('pagination.next') }}" class="inline-flex h-9 min-w-9 items-center justify-center rounded-lg border border-[#e5dec8] bg-[#faf8f2] px-3 text-sm text-[#8a7a57]">
                        <span aria-hidden="true">&rsaquo;</span>
                    </span>
                @endif
            </span>
        </div>
    </nav>
@endif

