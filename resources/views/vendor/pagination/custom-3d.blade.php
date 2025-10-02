@if ($paginator->hasPages())
<nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="custom-3d-pagination-wrapper">
    <ul id="menu">
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
</nav>

<style>
@import url(https://fonts.googleapis.com/css?family=PT+Sans);

.custom-3d-pagination-wrapper {
    text-align: center;
    margin: 20px 0;
}

/* Override global pagination styles */
.custom-3d-pagination-wrapper .pagination,
.custom-3d-pagination-wrapper .page-item,
.custom-3d-pagination-wrapper .page-link {
    all: unset;
}

#menu {
    list-style: none;
    padding: 0 12px;
    margin: 0;
    background: #5c8a97;
    margin: 20px auto;
    display: inline-block;
    height: 50px;
    overflow: hidden;
    border-radius: 5px;
    box-shadow: 0px 4px #3b636e, 0px 4px 6px rgba(0, 0, 0, 0.3);
}

#menu li {
    margin-left: 10px;
    display: inline-block;
    position: relative;
    bottom: -11px;
}

#menu li:first-child {
    margin: 0;
}

#menu li a {
    background: #a1d0dd;
    display: block;
    border-radius: 3px;
    padding: 0 12px;
    color: white;
    position: relative;
    text-decoration: none;
    height: 27px;
    font: 12px / 27px "PT Sans", Arial, sans-serif;
    box-shadow: 0px 3px #7fafbc, 0px 4px 5px rgba(0, 0, 0, 0.3);
    transition: all 0.3s ease;
}

#menu li a:hover {
    background: #bae0ea;
}

#menu li a:active {
    background: #bae0ea;
    bottom: -3px;
    box-shadow: 0px 0px #7fafbc, 0px 1px 3px rgba(0, 0, 0, 0.3);
}

#menu li span.active {
    background: #7fafbc;
    display: block;
    border-radius: 3px;
    padding: 0 12px;
    color: white;
    position: relative;
    text-decoration: none;
    height: 27px;
    font: 12px / 27px "PT Sans", Arial, sans-serif;
    box-shadow: 0px 1px #5c8a97, 0px 2px 3px rgba(0, 0, 0, 0.3);
    bottom: -3px;
}

#menu li span.disabled {
    background: #6b9aa7;
    display: block;
    border-radius: 3px;
    padding: 0 12px;
    color: #ccc;
    position: relative;
    text-decoration: none;
    height: 27px;
    font: 12px / 27px "PT Sans", Arial, sans-serif;
    box-shadow: 0px 2px #4a7580, 0px 3px 4px rgba(0, 0, 0, 0.2);
    cursor: not-allowed;
}

#menu li span.dots {
    background: transparent;
    display: block;
    border-radius: 3px;
    padding: 0 8px;
    color: white;
    position: relative;
    text-decoration: none;
    height: 27px;
    font: 12px / 27px "PT Sans", Arial, sans-serif;
    cursor: default;
}

/* Responsive design */
@media (max-width: 768px) {
    #menu {
        margin: 20px 10px;
        padding: 0 8px;
    }
    
    #menu li {
        margin-left: 5px;
    }
    
    #menu li a,
    #menu li span {
        padding: 0 8px;
        font-size: 11px;
    }
}
</style>
@endif