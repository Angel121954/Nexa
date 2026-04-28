@if ($paginator->hasPages())
    <nav class="pagination-wrap" aria-label="Paginación">
        {{-- Anterior --}}
        @if ($paginator->onFirstPage())
            <span class="page-item disabled"><span>&laquo;</span></span>
        @else
            <span class="page-item"><a href="{{ $paginator->previousPageUrl() }}" rel="prev">&laquo;</a></span>
        @endif

        {{-- Números --}}
        @foreach ($elements as $element)
            @if (is_string($element))
                <span class="page-item disabled"><span>{{ $element }}</span></span>
            @endif
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="page-item active"><span>{{ $page }}</span></span>
                    @else
                        <span class="page-item"><a href="{{ $url }}">{{ $page }}</a></span>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Siguiente --}}
        @if ($paginator->hasMorePages())
            <span class="page-item"><a href="{{ $paginator->nextPageUrl() }}" rel="next">&raquo;</a></span>
        @else
            <span class="page-item disabled"><span>&raquo;</span></span>
        @endif
    </nav>
@endif
