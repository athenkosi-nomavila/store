@if ($paginator->hasPages())
    <nav class="pagination" aria-label="Pagination">
        @if ($paginator->onFirstPage())
            <span class="page-link is-disabled" aria-disabled="true"><x-icon name="chevron-left" size="18" /><span class="sr-only">Previous</span></span>
        @else
            <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev"><x-icon name="chevron-left" size="18" /><span class="sr-only">Previous</span></a>
        @endif

        @foreach ($elements as $element)
            @if (is_string($element))
                <span class="page-link is-gap" aria-hidden="true">{{ $element }}</span>
            @endif
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="page-link is-current" aria-current="page">{{ $page }}</span>
                    @else
                        <a class="page-link" href="{{ $url }}" aria-label="Page {{ $page }}">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        @if ($paginator->hasMorePages())
            <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next"><x-icon name="chevron-right" size="18" /><span class="sr-only">Next</span></a>
        @else
            <span class="page-link is-disabled" aria-disabled="true"><x-icon name="chevron-right" size="18" /><span class="sr-only">Next</span></span>
        @endif
    </nav>
@endif
