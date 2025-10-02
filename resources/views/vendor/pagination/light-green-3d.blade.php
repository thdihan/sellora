@if ($paginator->hasPages())
<nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="light-green-pagination-wrapper">
    <ul id="light-green-pagination-menu">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <li><span class="disabled">Previous</span></li>
        @else
            <li><a href="{{ $paginator->previousPageUrl() }}" rel="prev">Previous</a></li>
        @endif

        {{-- Pagination Elements --}}
        @foreach ($elements as $element)
            {{-- "Three Dots" Separator --}}
            @if (is_string($element))
                <li><span class="dots">{{ $element }}</span></li>
            @endif

            {{-- Array Of Links --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <li><span class="active">{{ $page }}</span></li>
                    @else
                        <li><a href="{{ $url }}">{{ $page }}</a></li>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <li><a href="{{ $paginator->nextPageUrl() }}" rel="next">Next</a></li>
        @else
            <li><span class="disabled">Next</span></li>
        @endif
    </ul>

    {{-- Pagination Info --}}
    <div class="pagination-info">
        <p class="text-sm text-gray-700 leading-5">
            Showing
            @if ($paginator->firstItem())
                <span class="font-medium">{{ $paginator->firstItem() }}</span>
                to
                <span class="font-medium">{{ $paginator->lastItem() }}</span>
            @else
                {{ $paginator->count() }}
            @endif
            of
            <span class="font-medium">{{ $paginator->total() }}</span>
            results
        </p>
    </div>
</nav>

<style>
@import url(https://fonts.googleapis.com/css?family=PT+Sans);

.light-green-pagination-wrapper {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1rem;
    margin: 2rem 0;
}

/* Override global pagination styles */
.light-green-pagination-wrapper .pagination,
.light-green-pagination-wrapper .page-item,
.light-green-pagination-wrapper .page-link {
    all: unset;
}

#light-green-pagination-menu {
    list-style: none;
    padding: 0 12px;
    margin: 0;
    background: linear-gradient(135deg, #4ade80, #22c55e); /* Light green gradient */
    display: inline-block;
    height: 50px;
    overflow: hidden;
    border-radius: 8px;
    box-shadow: 0px 4px #16a34a, 0px 6px 12px rgba(0, 0, 0, 0.15);
    transition: all 0.3s ease;
}

#light-green-pagination-menu:hover {
    transform: translateY(-2px);
    box-shadow: 0px 6px #16a34a, 0px 8px 16px rgba(0, 0, 0, 0.2);
}

#light-green-pagination-menu li {
    margin-left: 10px;
    display: inline-block;
    position: relative;
    bottom: -11px;
}

#light-green-pagination-menu li:first-child {
    margin: 0;
}

#light-green-pagination-menu li a {
    background: linear-gradient(135deg, #86efac, #6ee7b7); /* Lighter green gradient */
    display: block;
    border-radius: 6px;
    padding: 0 14px;
    color: #065f46; /* Dark green text */
    position: relative;
    text-decoration: none;
    height: 27px;
    font: 13px / 27px "PT Sans", Arial, sans-serif;
    font-weight: 500;
    box-shadow: 0px 3px #22c55e, 0px 4px 8px rgba(0, 0, 0, 0.15);
    transition: all 0.3s ease;
    border: 1px solid rgba(255, 255, 255, 0.3);
}

#light-green-pagination-menu li a:hover {
    background: linear-gradient(135deg, #bbf7d0, #a7f3d0);
    color: #047857;
    transform: translateY(-1px);
    box-shadow: 0px 4px #22c55e, 0px 6px 10px rgba(0, 0, 0, 0.2);
}

#light-green-pagination-menu li a:active {
    background: linear-gradient(135deg, #bbf7d0, #a7f3d0);
    bottom: -3px;
    box-shadow: 0px 0px #22c55e, 0px 2px 4px rgba(0, 0, 0, 0.15);
}

#light-green-pagination-menu li span.active {
    background: linear-gradient(135deg, #059669, #047857); /* Darker green for active */
    display: block;
    border-radius: 6px;
    padding: 0 14px;
    color: white;
    position: relative;
    height: 27px;
    font: 13px / 27px "PT Sans", Arial, sans-serif;
    font-weight: 600;
    box-shadow: inset 0px 2px 4px rgba(0, 0, 0, 0.2), 0px 3px #065f46, 0px 4px 8px rgba(0, 0, 0, 0.15);
    border: 1px solid rgba(255, 255, 255, 0.2);
    bottom: -11px;
}

#light-green-pagination-menu li span.disabled {
    background: linear-gradient(135deg, #d1fae5, #a7f3d0); /* Very light green */
    display: block;
    border-radius: 6px;
    padding: 0 14px;
    color: #6b7280; /* Gray text */
    position: relative;
    height: 27px;
    font: 13px / 27px "PT Sans", Arial, sans-serif;
    box-shadow: 0px 1px #22c55e, 0px 2px 4px rgba(0, 0, 0, 0.1);
    bottom: -11px;
    opacity: 0.6;
    cursor: not-allowed;
}

#light-green-pagination-menu li span.dots {
    background: transparent;
    display: block;
    padding: 0 8px;
    color: #065f46;
    position: relative;
    height: 27px;
    font: 13px / 27px "PT Sans", Arial, sans-serif;
    bottom: -11px;
}

.pagination-info {
    text-align: center;
    margin-top: 0.5rem;
}

.pagination-info p {
    margin: 0;
    color: #374151;
    font-size: 0.875rem;
}

.pagination-info .font-medium {
    font-weight: 600;
    color: #059669;
}

/* Responsive design */
@media (max-width: 768px) {
    #light-green-pagination-menu {
        padding: 0 8px;
        height: 45px;
    }
    
    #light-green-pagination-menu li {
        margin-left: 6px;
    }
    
    #light-green-pagination-menu li a,
    #light-green-pagination-menu li span {
        padding: 0 10px;
        font-size: 12px;
        height: 24px;
        line-height: 24px;
    }
}
</style>
@endif