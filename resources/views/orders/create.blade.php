@extends('layouts.app')

@section('title', 'Create Order')

@push('styles')
<style>
/* Typeahead Styles */
.typeahead-container {
    position: relative;
}

.typeahead-dropdown {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: white;
    border: 1px solid #e5e7eb;
    border-top: none;
    border-radius: 0 0 0.5rem 0.5rem;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    max-height: 200px;
    overflow-y: auto;
    z-index: 1000;
    display: none;
}

.typeahead-item {
    padding: 0.75rem;
    cursor: pointer;
    border-bottom: 1px solid #f3f4f6;
    transition: background-color 0.15s ease-in-out;
}

.typeahead-item:hover {
    background-color: #f9fafb;
}

.typeahead-item:last-child {
    border-bottom: none;
}

.typeahead-item .fw-bold {
    font-weight: 600;
    color: #111827;
}

.typeahead-item .text-muted {
    color: #6b7280;
    font-size: 0.875rem;
}
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <!-- Header -->
            <div class="border-b border-gray-200 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Create New Order</h1>
                        <p class="mt-1 text-sm text-gray-600">Fill in the details below to create a new order</p>
                    </div>
                    <div class="flex items-center space-x-3">
                        <a href="{{ route('orders.index') }}" 
                           class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Back to Orders
                        </a>
                    </div>
                </div>
            </div>

            <!-- Form Content -->
            <div class="p-6">
                <form method="POST" action="{{ route('orders.store') }}" id="order-form">
                    @csrf
                    
                    <!-- Customer Information -->
                    <div class="space-y-6">
                        <div class="border-b border-gray-200 pb-4">
                            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                Customer Information
                            </h3>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="typeahead-container">
                                <label for="customer_search" class="block text-sm font-medium text-gray-700 mb-2">
                                    Search Customer <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 @error('customer_id') border-red-500 @enderror" 
                                       id="customer_search" 
                                       placeholder="Type customer name or phone..."
                                       autocomplete="off">
                                <div id="customer_dropdown" class="typeahead-dropdown"></div>
                                <input type="hidden" id="customer_id" name="customer_id" value="{{ old('customer_id') }}">
                                @error('customer_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="customer_name" class="block text-sm font-medium text-gray-700 mb-2">
                                    Customer Name <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 @error('customer_name') border-red-500 @enderror" 
                                       id="customer_name" 
                                       name="customer_name" 
                                       value="{{ old('customer_name') }}" 
                                       required
                                       placeholder="Enter customer name">
                                @error('customer_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <!-- Order Details -->
                    <div class="mt-8 space-y-6">
                        <div class="border-b border-gray-200 pb-4">
                            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Order Details
                            </h3>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label for="order_date" class="block text-sm font-medium text-gray-700 mb-2">
                                    Order Date <span class="text-red-500">*</span>
                                </label>
                                <input type="date" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 @error('order_date') border-red-500 @enderror" 
                                       id="order_date" 
                                       name="order_date" 
                                       value="{{ old('order_date', date('Y-m-d')) }}" 
                                       required>
                                @error('order_date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="delivery_date" class="block text-sm font-medium text-gray-700 mb-2">Delivery Date</label>
                                <input type="date" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 @error('delivery_date') border-red-500 @enderror" 
                                       id="delivery_date" 
                                       name="delivery_date" 
                                       value="{{ old('delivery_date') }}"
                                       min="{{ date('Y-m-d') }}">
                                @error('delivery_date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                                    Status <span class="text-red-500">*</span>
                                </label>
                                <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 @error('status') border-red-500 @enderror" 
                                        id="status" 
                                        name="status" 
                                        required>
                                    <option value="pending" {{ old('status', 'pending') == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="confirmed" {{ old('status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                                    <option value="processing" {{ old('status') == 'processing' ? 'selected' : '' }}>Processing</option>
                                    <option value="shipped" {{ old('status') == 'shipped' ? 'selected' : '' }}>Shipped</option>
                                    <option value="delivered" {{ old('status') == 'delivered' ? 'selected' : '' }}>Delivered</option>
                                    <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                                @error('status')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <!-- Order Items -->
                    <div class="mt-8 space-y-6">
                        <div class="border-b border-gray-200 pb-4">
                            <div class="flex items-center justify-between">
                                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                    </svg>
                                    Order Items
                                </h3>
                                <button type="button" 
                                        id="add-item-btn" 
                                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 transition-colors duration-200">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    Add Item
                                </button>
                            </div>
                        </div>
                        
                        <div id="order-items-container" class="space-y-4">
                            <!-- Dynamic order items will be added here -->
                        </div>
                    </div>
                    
                    <!-- Pricing & Payment Section -->
                    <div class="mt-8 border-t border-gray-200 pt-8">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                            <!-- Pricing Information -->
                            <div class="space-y-6">
                                <div class="border-b border-gray-200 pb-4">
                                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                        </svg>
                                        Pricing & Payment
                                    </h3>
                                </div>
                                
                                <div>
                                    <label for="discount" class="block text-sm font-medium text-gray-700 mb-2">Discount ($)</label>
                                    <input type="number" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 @error('discount') border-red-500 @enderror" 
                                           id="discount" 
                                           name="discount" 
                                           value="{{ old('discount', 0) }}" 
                                           step="0.01" 
                                           min="0"
                                           placeholder="0.00">
                                    @error('discount')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <!-- VAT & TAX Configuration -->
                                <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                                    <div class="flex items-center mb-4">
                                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                        </svg>
                                        <h4 class="text-lg font-semibold text-blue-900">VAT & TAX Configuration</h4>
                                    </div>
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                        <div>
                                            <label for="vat_condition" class="block text-sm font-medium text-gray-700 mb-2">
                                                VAT Condition <span class="text-red-500">*</span>
                                            </label>
                                            <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 @error('vat_condition') border-red-500 @enderror" 
                                                    id="vat_condition" 
                                                    name="vat_condition" 
                                                    required>
                                                <option value="client_bears" {{ old('vat_condition', 'client_bears') == 'client_bears' ? 'selected' : '' }}>Client bears</option>
                                                <option value="company_bears" {{ old('vat_condition') == 'company_bears' ? 'selected' : '' }}>Company bears</option>
                                            </select>
                                            @error('vat_condition')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div>
                                            <label for="tax_condition" class="block text-sm font-medium text-gray-700 mb-2">
                                                TAX Condition <span class="text-red-500">*</span>
                                            </label>
                                            <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 @error('tax_condition') border-red-500 @enderror" 
                                                    id="tax_condition" 
                                                    name="tax_condition" 
                                                    required>
                                                <option value="client_bears" {{ old('tax_condition', 'client_bears') == 'client_bears' ? 'selected' : '' }}>Client bears</option>
                                                <option value="company_bears" {{ old('tax_condition') == 'company_bears' ? 'selected' : '' }}>Company bears</option>
                                            </select>
                                            @error('tax_condition')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                    
                                    <div class="bg-white rounded-lg p-4 space-y-2">
                                        <div class="flex justify-between text-sm text-gray-600">
                                            <span>VAT Amount:</span>
                                            <span id="vat-amount-display" class="font-medium">৳0.00</span>
                                        </div>
                                        <div class="flex justify-between text-sm text-gray-600">
                                            <span>TAX Amount:</span>
                                            <span id="tax-amount-display" class="font-medium">৳0.00</span>
                                        </div>
                                        <div class="flex justify-between text-sm font-semibold text-gray-900 border-t border-gray-200 pt-2">
                                            <span>Net Revenue (Company):</span>
                                            <span id="net-revenue-display">৳0.00</span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div>
                                    <label for="payment_method" class="block text-sm font-medium text-gray-700 mb-2">Payment Method</label>
                                    <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 @error('payment_method') border-red-500 @enderror" 
                                            id="payment_method" 
                                            name="payment_method">
                                        <option value="">Select Payment Method</option>
                                        <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>Cash</option>
                                        <option value="credit_card" {{ old('payment_method') == 'credit_card' ? 'selected' : '' }}>Credit Card</option>
                                        <option value="debit_card" {{ old('payment_method') == 'debit_card' ? 'selected' : '' }}>Debit Card</option>
                                        <option value="bank_transfer" {{ old('payment_method') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                        <option value="check" {{ old('payment_method') == 'check' ? 'selected' : '' }}>Check</option>
                                        <option value="other" {{ old('payment_method') == 'other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                    @error('payment_method')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            
                            <!-- Order Summary -->
                            <div class="space-y-6">
                                <div class="border-b border-gray-200 pb-4">
                                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                        </svg>
                                        Order Summary
                                    </h3>
                                </div>
                                
                                <div class="bg-gray-50 rounded-lg p-6 space-y-4">
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm font-medium text-gray-600">Subtotal:</span>
                                        <span id="subtotal_display" class="text-sm font-semibold text-gray-900">৳0.00</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm font-medium text-gray-600">Discount:</span>
                                        <span id="discount_display" class="text-sm font-semibold text-green-600">-৳0.00</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm font-medium text-gray-600">VAT:</span>
                                        <span id="vat_amount_display" class="text-sm font-semibold text-gray-900">৳0.00</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm font-medium text-gray-600">TAX:</span>
                                        <span id="tax_amount_display" class="text-sm font-semibold text-gray-900">৳0.00</span>
                                    </div>
                                    <div class="border-t border-gray-300 pt-4">
                                        <div class="flex justify-between items-center">
                                            <span class="text-lg font-bold text-gray-900">Total:</span>
                                            <span id="total_display" class="text-lg font-bold text-blue-600">৳0.00</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Hidden inputs for tax data -->
                    <input type="hidden" id="tax_data" name="tax_data" value="">
                    <input type="hidden" id="total_amount" name="total_amount" value="">
                    
                    <!-- Form Actions -->
                    <div class="mt-8 border-t border-gray-200 pt-6">
                        <div class="flex flex-col sm:flex-row gap-4 justify-end">
                            <a href="{{ route('orders.index') }}" 
                               class="inline-flex items-center justify-center px-6 py-3 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors duration-200">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Cancel
                            </a>
                            <button type="submit" 
                                    class="inline-flex items-center justify-center px-6 py-3 border border-transparent rounded-lg text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors duration-200" 
                                    onclick="prepareFormSubmission()">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                                </svg>
                                Create Order
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// VAT and TAX rates (will be fetched from settings)
let vatRate = 15; // Default 15%
let taxRate = 5;  // Default 5%

// Fetch VAT and TAX rates from settings on page load
fetch('/api/settings/tax-rates')
    .then(response => response.json())
    .then(data => {
        vatRate = parseFloat(data.vat_rate) || 15;
        taxRate = parseFloat(data.tax_rate) || 5;
        updateGrandTotal(); // Recalculate with correct rates
    })
    .catch(error => {
        console.warn('Could not fetch tax rates, using defaults:', error);
        updateGrandTotal(); // Still calculate with defaults
    });

// Prepare form submission
function prepareFormSubmission() {
    updateGrandTotal();
    return true;
}

// Customer Typeahead Implementation
let customerSearchTimeout;
function initCustomerTypeahead() {
    const searchInput = document.getElementById('customer_search');
    const dropdown = document.getElementById('customer_dropdown');
    
    searchInput.addEventListener('input', function() {
        const query = this.value.trim();
        
        clearTimeout(customerSearchTimeout);
        
        if (query.length < 3) {
            dropdown.style.display = 'none';
            return;
        }
        
        customerSearchTimeout = setTimeout(() => {
            searchCustomers(query);
        }, 250);
    });
    
    searchInput.addEventListener('blur', function() {
        setTimeout(() => dropdown.style.display = 'none', 200);
    });
    
    searchInput.addEventListener('focus', function() {
        if (this.value.length >= 3) {
            searchCustomers(this.value.trim());
        }
    });
}

function searchCustomers(query) {
    fetch(`/api/customers/search?q=${encodeURIComponent(query)}`, {
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        credentials: 'same-origin'
    })
    .then(response => response.json())
    .then(customers => {
        renderCustomerDropdown(customers);
    })
    .catch(error => console.error('Error searching customers:', error));
}

function renderCustomerDropdown(customers) {
    const dropdown = document.getElementById('customer_dropdown');
    
    if (customers.length === 0) {
        dropdown.innerHTML = '<div class="typeahead-item">No customers found</div>';
        dropdown.style.display = 'block';
        return;
    }
    
    dropdown.innerHTML = customers.map(customer => `
        <div class="typeahead-item" onclick="selectCustomer(${customer.id}, '${customer.name.replace(/'/g, "\\'")}')" 
            data-phone="${customer.phone || ''}" data-shop-name="${customer.shop_name || ''}" data-email="${customer.email || ''}">
            <div class="fw-bold">${customer.display_name || customer.name}</div>
            <div class="text-muted small">${customer.phone || ''}</div>
        </div>
    `).join('');
    
    dropdown.style.display = 'block';
}

function selectCustomer(id, name) {
    const searchInput = document.getElementById('customer_search');
    const dropdown = document.getElementById('customer_dropdown');
    const selectedItem = dropdown.querySelector(`[onclick*="${id}"]`);
    
    // Set values
    document.getElementById('customer_id').value = id;
    document.getElementById('customer_name').value = name;
    searchInput.value = selectedItem ? (selectedItem.querySelector('.fw-bold').textContent || name) : name;
    
    // Auto-fill other fields if they exist
    if (selectedItem) {
        const phoneField = document.getElementById('customer_phone');
        const emailField = document.getElementById('customer_email');
        const shopNameField = document.getElementById('customer_shop_name');
        
        if (phoneField) phoneField.value = selectedItem.dataset.phone || '';
        if (emailField) emailField.value = selectedItem.dataset.email || '';
        if (shopNameField) shopNameField.value = selectedItem.dataset.shopName || '';
    }
    
    dropdown.style.display = 'none';
}

// Global function to update grand total
function updateGrandTotal() {
    let subtotal = 0;
    const container = document.getElementById('order-items-container');
    if (container) {
        const totalDisplays = container.querySelectorAll('.total-display');
        
        totalDisplays.forEach(display => {
            const value = parseFloat(display.value) || 0;
            subtotal += value;
        });
    }
    
    // Get discount
    const discountElement = document.getElementById('discount');
    const discount = discountElement ? parseFloat(discountElement.value) || 0 : 0;
    const baseAmount = subtotal - discount;
    
    // Get VAT and TAX conditions
    const vatConditionElement = document.getElementById('vat_condition');
    const taxConditionElement = document.getElementById('tax_condition');
    const vatCondition = vatConditionElement ? vatConditionElement.value : 'company_bears';
    const taxCondition = taxConditionElement ? taxConditionElement.value : 'company_bears';
    
    // Calculate VAT and TAX amounts
    const vatAmount = (baseAmount * vatRate) / 100;
    const taxAmount = (baseAmount * taxRate) / 100;
    
    // Calculate total based on conditions
    let totalAmount = baseAmount;
    
    if (vatCondition === 'client_bears') {
        totalAmount += vatAmount;
    }
    
    if (taxCondition === 'client_bears') {
        totalAmount += taxAmount;
    }
    
    // Update order summary displays
    const subtotalDisplay = document.getElementById('subtotal_display');
    const discountDisplay = document.getElementById('discount_display');
    const vatAmountDisplay = document.getElementById('vat_amount_display');
    const taxAmountDisplay = document.getElementById('tax_amount_display');
    const totalDisplay = document.getElementById('total_display');
    
    // Also update the VAT/TAX configuration section displays
    const vatAmountConfigDisplay = document.getElementById('vat-amount-display');
    const taxAmountConfigDisplay = document.getElementById('tax-amount-display');
    const netRevenueDisplay = document.getElementById('net-revenue-display');
    
    if (subtotalDisplay) subtotalDisplay.textContent = '৳' + subtotal.toFixed(2);
    if (discountDisplay) discountDisplay.textContent = '৳' + discount.toFixed(2);
    if (vatAmountDisplay) vatAmountDisplay.textContent = '৳' + vatAmount.toFixed(2);
    if (taxAmountDisplay) taxAmountDisplay.textContent = '৳' + taxAmount.toFixed(2);
    if (totalDisplay) totalDisplay.textContent = '৳' + totalAmount.toFixed(2);
    
    // Update configuration section displays
    if (vatAmountConfigDisplay) vatAmountConfigDisplay.textContent = '৳' + vatAmount.toFixed(2);
    if (taxAmountConfigDisplay) taxAmountConfigDisplay.textContent = '৳' + taxAmount.toFixed(2);
    
    // Calculate and display net revenue
    let netRevenue = baseAmount;
    if (vatCondition === 'company_bears') netRevenue -= vatAmount;
    if (taxCondition === 'company_bears') netRevenue -= taxAmount;
    if (netRevenueDisplay) netRevenueDisplay.textContent = '৳' + netRevenue.toFixed(2);
    
    // Update hidden total amount
    const totalAmountInput = document.getElementById('total_amount');
    if (totalAmountInput) {
        totalAmountInput.value = totalAmount.toFixed(2);
    }
}

// Order Items Management
document.addEventListener('DOMContentLoaded', function() {
    let itemIndex = 0;
    const container = document.getElementById('order-items-container');
    const addBtn = document.getElementById('add-item-btn');
    
    // Add first item on page load
    addOrderItem();
    
    addBtn.addEventListener('click', function() {
        addOrderItem();
    });
    
    function addOrderItem() {
        const itemHtml = `
            <div class="order-item bg-white border border-gray-200 rounded-lg p-6" data-index="${itemIndex}">
                <div class="flex items-center justify-between mb-4">
                    <h4 class="text-lg font-medium text-gray-900">Item #${itemIndex + 1}</h4>
                    <button type="button" class="remove-item-btn inline-flex items-center px-3 py-2 border border-red-300 rounded-lg text-sm font-medium text-red-700 bg-white hover:bg-red-50 transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        Remove
                    </button>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Product <span class="text-red-500">*</span></label>
                        <select class="product-select w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200" name="items[${itemIndex}][product_id]" required>
                            <option value="">Select Product</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Quantity <span class="text-red-500">*</span></label>
                        <input type="number" class="quantity-input w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200" name="items[${itemIndex}][quantity]" min="1" value="1" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Unit Price <span class="text-red-500">*</span></label>
                        <input type="number" class="price-input w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200" name="items[${itemIndex}][unit_price]" step="0.01" min="0" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Total</label>
                        <input type="number" class="total-display w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50" readonly>
                    </div>
                </div>
            </div>
        `;
        
        container.insertAdjacentHTML('beforeend', itemHtml);
        
        // Load products for the new select
        loadProductsForSelect(container.querySelector(`[data-index="${itemIndex}"] .product-select`));
        
        // Add event listeners for the new item
        const newItem = container.querySelector(`[data-index="${itemIndex}"]`);
        const quantityInput = newItem.querySelector('.quantity-input');
        const priceInput = newItem.querySelector('.price-input');
        const totalDisplay = newItem.querySelector('.total-display');
        const removeBtn = newItem.querySelector('.remove-item-btn');
        
        // Calculate total for this item
        function calculateItemTotal() {
            const quantity = parseFloat(quantityInput.value) || 0;
            const price = parseFloat(priceInput.value) || 0;
            const total = quantity * price;
            totalDisplay.value = total.toFixed(2);
            updateGrandTotal();
        }
        
        quantityInput.addEventListener('input', calculateItemTotal);
        priceInput.addEventListener('input', calculateItemTotal);
        
        // Remove item functionality
        removeBtn.addEventListener('click', function() {
            newItem.remove();
            updateGrandTotal();
            updateRemoveButtons();
        });
        
        itemIndex++;
        updateRemoveButtons();
    }
    
    function loadProductsForSelect(selectElement) {
        fetch('/api/products', {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            credentials: 'same-origin'
        })
        .then(response => response.json())
        .then(products => {
            selectElement.innerHTML = '<option value="">Select Product</option>';
            products.forEach(product => {
                const option = document.createElement('option');
                option.value = product.id;
                option.textContent = `${product.name} - ৳${product.price}`;
                option.dataset.price = product.price;
                selectElement.appendChild(option);
            });
            
            // Auto-fill price when product is selected
            selectElement.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                if (selectedOption.dataset.price) {
                    const priceInput = this.closest('.order-item').querySelector('.price-input');
                    priceInput.value = selectedOption.dataset.price;
                    priceInput.dispatchEvent(new Event('input'));
                }
            });
        })
        .catch(error => console.error('Error loading products:', error));
    }
    
    function updateRemoveButtons() {
        const items = container.querySelectorAll('.order-item');
        items.forEach((item, index) => {
            const removeBtn = item.querySelector('.remove-item-btn');
            if (items.length === 1) {
                removeBtn.style.display = 'none';
            } else {
                removeBtn.style.display = 'inline-flex';
            }
        });
    }
    
    // Add listeners for discount and VAT/TAX condition changes
    const discountInput = document.getElementById('discount');
    const vatConditionSelect = document.getElementById('vat_condition');
    const taxConditionSelect = document.getElementById('tax_condition');
    
    if (discountInput) {
        discountInput.addEventListener('input', updateGrandTotal);
        discountInput.addEventListener('change', updateGrandTotal);
    }
    
    if (vatConditionSelect) {
        vatConditionSelect.addEventListener('change', updateGrandTotal);
    }
    
    if (taxConditionSelect) {
        taxConditionSelect.addEventListener('change', updateGrandTotal);
    }
    
    // Initialize typeahead components
    initCustomerTypeahead();
    
    // Initial calculation
    updateGrandTotal();
    
    // Set minimum delivery date to order date
    const orderDateInput = document.getElementById('order_date');
    if (orderDateInput) {
        orderDateInput.addEventListener('change', function() {
            const deliveryDateInput = document.getElementById('delivery_date');
            if (deliveryDateInput) {
                deliveryDateInput.min = this.value;
            }
        });
    }
});
</script>
@endpush