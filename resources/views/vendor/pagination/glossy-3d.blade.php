@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="custom-pagination-wrapper">
        <ul id="custom-pagination-menu">
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
                            <li><span class="active" aria-current="page">{{ $page }}</span></li>
                        @else
                            <li><a href="{{ $url }}" aria-label="{{ __('Go to page :page', ['page' => $page]) }}">{{ $page }}</a></li>
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

        {{-- Results Info --}}
        <div class="pagination-info">
            <p class="text-sm text-gray-700">
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
    
    .custom-pagination-wrapper {
        display: flex !important;
        flex-direction: column !important;
        align-items: center !important;
        gap: 1rem !important;
        margin: 2rem 0 !important;
        text-align: center !important;
    }

    /* Override global pagination styles */
    .custom-pagination-wrapper .pagination,
    .custom-pagination-wrapper .page-item,
    .custom-pagination-wrapper .page-link {
        all: unset !important;
    }

    #custom-pagination-menu {
        list-style: none !important;
        padding: 0 12px !important;
        margin: 0 !important;
        background: #0d6efd !important;
        display: inline-block !important;
        height: 50px !important;
        overflow: hidden !important;
        border-radius: 5px !important;
        box-shadow: 0px 4px #0a58ca, 0px 4px 6px rgba(0, 0, 0, 0.3) !important;
    }

    #custom-pagination-menu li {
        margin-left: 10px !important;
        display: inline-block !important;
        position: relative !important;
        bottom: -11px !important;
    }

    #custom-pagination-menu li:first-child {
        margin: 0 !important;
    }

    #custom-pagination-menu li a {
        background: #4dabf7 !important;
        display: block !important;
        border-radius: 3px !important;
        padding: 0 12px !important;
        color: white !important;
        position: relative !important;
        text-decoration: none !important;
        height: 27px !important;
        font: 12px / 27px "PT Sans", Arial, sans-serif !important;
        box-shadow: 0px 3px #339af0, 0px 4px 5px rgba(0, 0, 0, 0.3) !important;
        transition: all 0.3s ease !important;
    }

    #custom-pagination-menu li a:hover {
        background: #74c0fc !important;
        color: white !important;
        text-decoration: none !important;
    }

    #custom-pagination-menu li a:active {
        background: #74c0fc !important;
        bottom: -3px !important;
        box-shadow: 0px 0px #339af0, 0px 1px 3px rgba(0, 0, 0, 0.3) !important;
    }

    #custom-pagination-menu li span.active {
        background: #1c7ed6 !important;
        display: block !important;
        border-radius: 3px !important;
        padding: 0 12px !important;
        color: white !important;
        position: relative !important;
        text-decoration: none !important;
        height: 27px !important;
        font: 12px / 27px "PT Sans", Arial, sans-serif !important;
        box-shadow: 0px 3px #1864ab, 0px 4px 5px rgba(0, 0, 0, 0.3) !important;
        bottom: -3px !important;
        font-weight: bold !important;
    }

    #custom-pagination-menu li span.disabled {
        background: #adb5bd !important;
        display: block !important;
        border-radius: 3px !important;
        padding: 0 12px !important;
        color: #6c757d !important;
        position: relative !important;
        text-decoration: none !important;
        height: 27px !important;
        font: 12px / 27px "PT Sans", Arial, sans-serif !important;
        box-shadow: 0px 3px #868e96, 0px 4px 5px rgba(0, 0, 0, 0.3) !important;
        cursor: not-allowed !important;
    }

    #custom-pagination-menu li span.dots {
        background: transparent !important;
        display: block !important;
        padding: 0 12px !important;
        color: white !important;
        position: relative !important;
        height: 27px !important;
        font: 12px / 27px "PT Sans", Arial, sans-serif !important;
        box-shadow: none !important;
        cursor: default !important;
    }

    .pagination-info {
        text-align: center !important;
        margin-top: 1rem !important;
    }

    .pagination-info p {
        margin: 0 !important;
        color: #6b7280 !important;
        font-size: 0.875rem !important;
    }

    .pagination-info .font-medium {
        font-weight: 600 !important;
        color: #374151 !important;
    }

    @media (max-width: 640px) {
        #custom-pagination-menu {
            padding: 0 8px !important;
            height: 45px !important;
        }
        
        #custom-pagination-menu li {
            margin-left: 5px !important;
        }
        
        #custom-pagination-menu li a,
        #custom-pagination-menu li span {
            padding: 0 8px !important;
            height: 24px !important;
            font-size: 11px !important;
            line-height: 24px !important;
        }
    }
    </style>
@endif