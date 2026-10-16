@php
    $base = 'inline-flex h-11 min-w-11 items-center justify-center rounded-full border px-3';
@endphp
@if ($paginator->hasPages())
    <nav class="mt-10 flex flex-wrap justify-center gap-2" aria-label="Pagination">
        @if ($paginator->onFirstPage())
            <span class="{{ $base }} border-zinc-200 bg-white text-zinc-300" aria-disabled="true"><x-icon name="chevron-left" size="18" /><span class="sr-only">Previous</span></span>
        @else
            <a class="{{ $base }} border-zinc-200 bg-white hover:border-brand-600 hover:text-brand-600" href="{{ $paginator->previousPageUrl() }}" rel="prev"><x-icon name="chevron-left" size="18" /><span class="sr-only">Previous</span></a>
        @endif

        @foreach ($elements as $element)
            @if (is_string($element))
                <span class="{{ $base }} border-transparent" aria-hidden="true">{{ $element }}</span>
            @endif
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="{{ $base }} border-brand-600 bg-brand-600 text-white" aria-current="page">{{ $page }}</span>
                    @else
                        <a class="{{ $base }} border-zinc-200 bg-white hover:border-brand-600 hover:text-brand-600" href="{{ $url }}" aria-label="Page {{ $page }}">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        @if ($paginator->hasMorePages())
            <a class="{{ $base }} border-zinc-200 bg-white hover:border-brand-600 hover:text-brand-600" href="{{ $paginator->nextPageUrl() }}" rel="next"><x-icon name="chevron-right" size="18" /><span class="sr-only">Next</span></a>
        @else
            <span class="{{ $base }} border-zinc-200 bg-white text-zinc-300" aria-disabled="true"><x-icon name="chevron-right" size="18" /><span class="sr-only">Next</span></span>
        @endif
    </nav>
@endif
