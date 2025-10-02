@props([
    'name' => 'typeahead',
    'placeholder' => 'Type to search...',
    'minChars' => 3,
    'displayKey' => 'name',
    'valueKey' => 'id',
    'fetchUrl' => '',
    'searchParam' => 'search',
    'value' => '',
    'displayValue' => '',
    'required' => false,
    'class' => '',
    'debounceMs' => 250
])

<div 
    x-data="typeahead({
        name: '{{ $name }}',
        minChars: {{ $minChars }},
        displayKey: '{{ $displayKey }}',
        valueKey: '{{ $valueKey }}',
        fetchUrl: '{{ $fetchUrl }}',
        searchParam: '{{ $searchParam }}',
        debounceMs: {{ $debounceMs }},
        initialValue: '{{ $value }}',
        initialDisplayValue: '{{ $displayValue }}'
    })"
    class="relative"
>
    <!-- Hidden input for form submission -->
    <input 
        type="hidden" 
        name="{{ $name }}" 
        x-model="selectedValue"
    >
    
    <!-- Search input -->
    <div class="relative">
        <input 
            type="text"
            x-model="query"
            x-on:input.debounce.{{ $debounceMs }}ms="search()"
            x-on:focus="showDropdown = true"
            x-on:keydown.arrow-down.prevent="highlightNext()"
            x-on:keydown.arrow-up.prevent="highlightPrevious()"
            x-on:keydown.enter.prevent="selectHighlighted()"
            x-on:keydown.escape="hideDropdown()"
            placeholder="{{ $placeholder }}"
            class="form-control {{ $class }}"
            autocomplete="off"
            {{ $required ? 'required' : '' }}
        >
        
        <!-- Loading spinner -->
        <div 
            x-show="loading" 
            class="absolute right-3 top-1/2 transform -translate-y-1/2"
        >
            <div class="spinner-border spinner-border-sm text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
        
        <!-- Clear button -->
        <button 
            type="button"
            x-show="query && !loading"
            x-on:click="clear()"
            class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600"
        >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>
    
    <!-- Dropdown results -->
    <div 
        x-show="showDropdown && (results.length > 0 || (query.length >= minChars && !loading))"
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="transform opacity-0 scale-95"
        x-transition:enter-end="transform opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="transform opacity-100 scale-100"
        x-transition:leave-end="transform opacity-0 scale-95"
        class="absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg max-h-60 overflow-auto"
        x-on:click.away="hideDropdown()"
    >
        <!-- Results -->
        <template x-for="(item, index) in results" :key="item[valueKey]">
            <div 
                x-on:click="selectItem(item)"
                x-on:mouseenter="highlightedIndex = index"
                :class="{
                    'bg-blue-50 text-blue-700': highlightedIndex === index,
                    'text-gray-900': highlightedIndex !== index
                }"
                class="px-4 py-2 cursor-pointer hover:bg-gray-50 border-b border-gray-100 last:border-b-0"
            >
                <div x-text="item[displayKey]"></div>
                <div x-show="item.description" x-text="item.description" class="text-sm text-gray-500"></div>
            </div>
        </template>
        
        <!-- No results -->
        <div 
            x-show="query.length >= minChars && results.length === 0 && !loading"
            class="px-4 py-2 text-gray-500 text-center"
        >
            No results found
        </div>
        
        <!-- Minimum characters message -->
        <div 
            x-show="query.length > 0 && query.length < minChars"
            class="px-4 py-2 text-gray-500 text-center"
        >
            Type at least {{ $minChars }} characters to search
        </div>
    </div>
</div>

<script>
    function typeahead(config) {
        return {
            // Configuration
            name: config.name,
            minChars: config.minChars,
            displayKey: config.displayKey,
            valueKey: config.valueKey,
            fetchUrl: config.fetchUrl,
            searchParam: config.searchParam,
            debounceMs: config.debounceMs,
            
            // State
            query: config.initialDisplayValue || '',
            selectedValue: config.initialValue || '',
            results: [],
            showDropdown: false,
            loading: false,
            highlightedIndex: -1,
            
            // Methods
            async search() {
                if (this.query.length < this.minChars) {
                    this.results = [];
                    this.showDropdown = false;
                    return;
                }
                
                this.loading = true;
                this.showDropdown = true;
                
                try {
                    const url = new URL(this.fetchUrl, window.location.origin);
                    url.searchParams.set(this.searchParam, this.query);
                    
                    const response = await fetch(url);
                    const data = await response.json();
                    
                    this.results = data.data || data || [];
                    this.highlightedIndex = -1;
                } catch (error) {
                    console.error('Typeahead search error:', error);
                    this.results = [];
                } finally {
                    this.loading = false;
                }
            },
            
            selectItem(item) {
                this.query = item[this.displayKey];
                this.selectedValue = item[this.valueKey];
                this.showDropdown = false;
                this.highlightedIndex = -1;
                
                // Dispatch custom event for parent components
                this.$dispatch('typeahead-selected', {
                    name: this.name,
                    item: item,
                    value: item[this.valueKey],
                    display: item[this.displayKey]
                });
            },
            
            selectHighlighted() {
                if (this.highlightedIndex >= 0 && this.highlightedIndex < this.results.length) {
                    this.selectItem(this.results[this.highlightedIndex]);
                }
            },
            
            highlightNext() {
                if (this.results.length === 0) return;
                this.highlightedIndex = Math.min(this.highlightedIndex + 1, this.results.length - 1);
            },
            
            highlightPrevious() {
                if (this.results.length === 0) return;
                this.highlightedIndex = Math.max(this.highlightedIndex - 1, -1);
            },
            
            hideDropdown() {
                this.showDropdown = false;
                this.highlightedIndex = -1;
            },
            
            clear() {
                this.query = '';
                this.selectedValue = '';
                this.results = [];
                this.showDropdown = false;
                this.highlightedIndex = -1;
                
                // Dispatch clear event
                this.$dispatch('typeahead-cleared', {
                    name: this.name
                });
            }
        }
    }
</script>