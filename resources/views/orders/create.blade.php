@extends('layouts.app')

@section('title', 'Create Order')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Create New Order</h5>
                    <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary">
                        Back to Orders
                    </a>
                </div>
                <div class="card-body">
                    <form action="{{ route('orders.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="row">
                            <!-- Customer Information -->
                            <div class="col-md-6">
                                <h6 class="text-primary mb-3">Customer Information</h6>
                                
                                <div class="mb-3">
                                    <label for="customer_search" class="form-label">Customer <span class="text-danger">*</span></label>
                                    <div class="typeahead-container">
                                        <input type="text" class="form-control @error('customer_name') is-invalid @enderror" 
                                               id="customer_search" placeholder="Search customers..." autocomplete="off">
                                        <input type="hidden" id="customer_id" name="customer_id" value="{{ old('customer_id') }}">
                                        <input type="hidden" id="customer_name" name="customer_name" value="{{ old('customer_name') }}">
                                        <div class="typeahead-dropdown" id="customer_dropdown"></div>
                                    </div>
                                    @error('customer_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="customer_email" class="form-label">Customer Email</label>
                                    <input type="email" class="form-control @error('customer_email') is-invalid @enderror" 
                                           id="customer_email" name="customer_email" value="{{ old('customer_email') }}">
                                    @error('customer_email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="customer_phone" class="form-label">Customer Phone</label>
                                    <input type="tel" class="form-control @error('customer_phone') is-invalid @enderror" 
                                           id="customer_phone" name="customer_phone" value="{{ old('customer_phone') }}">
                                    @error('customer_phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="customer_address" class="form-label">Customer Address</label>
                                    <textarea class="form-control @error('customer_address') is-invalid @enderror" 
                                              id="customer_address" name="customer_address" rows="3">{{ old('customer_address') }}</textarea>
                                    @error('customer_address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <!-- Order Items -->
                            <div class="col-md-12">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="text-primary mb-0">Order Items</h6>
                                    <button type="button" class="btn btn-sm btn-outline-primary" id="add-item-btn">
                                        <i class="fas fa-plus"></i> Add Item
                                    </button>
                                </div>
                                
                                <div id="order-items-container">
                                    <!-- Order items will be added here dynamically -->
                                </div>
                                
                                @error('items')
                                    <div class="text-danger small mt-2">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <hr>
                        
                        <div class="row">
                            <!-- Pricing Information -->
                            <div class="col-md-6">
                                <h6 class="text-primary mb-3">Pricing & Payment</h6>
                                
                                <div class="mb-3">
                                    <label for="discount" class="form-label">Discount ($)</label>
                                    <input type="number" class="form-control @error('discount') is-invalid @enderror" 
                                           id="discount" name="discount" value="{{ old('discount', 0) }}" step="0.01" min="0">
                                    @error('discount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <!-- VAT & TAX Configuration -->
                                <div class="card border-primary mb-3">
                                    <div class="card-header bg-primary text-white">
                                        <h6 class="mb-0">🧮 VAT & TAX Configuration</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="vat_condition" class="form-label">VAT Condition <span class="text-danger">*</span></label>
                                                    <select class="form-select @error('vat_condition') is-invalid @enderror" id="vat_condition" name="vat_condition" required>
                                                        <option value="client_bears" {{ old('vat_condition', 'client_bears') == 'client_bears' ? 'selected' : '' }}>Client bears</option>
                                                        <option value="company_bears" {{ old('vat_condition') == 'company_bears' ? 'selected' : '' }}>Company bears</option>
                                                    </select>
                                                    @error('vat_condition')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="tax_condition" class="form-label">TAX Condition <span class="text-danger">*</span></label>
                                                    <select class="form-select @error('tax_condition') is-invalid @enderror" id="tax_condition" name="tax_condition" required>
                                                        <option value="client_bears" {{ old('tax_condition', 'client_bears') == 'client_bears' ? 'selected' : '' }}>Client bears</option>
                                                        <option value="company_bears" {{ old('tax_condition') == 'company_bears' ? 'selected' : '' }}>Company bears</option>
                                                    </select>
                                                    @error('tax_condition')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="mt-3">
                                            <div class="d-flex justify-content-between text-muted">
                                                <span>VAT Amount:</span>
                                                <span id="vat-amount-display">৳0.00</span>
                                            </div>
                                            <div class="d-flex justify-content-between text-muted">
                                                <span>TAX Amount:</span>
                                                <span id="tax-amount-display">৳0.00</span>
                                            </div>
                                            <div class="d-flex justify-content-between text-muted">
                                                <span>Net Revenue (Company):</span>
                                                <span id="net-revenue-display">৳0.00</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="payment_method" class="form-label">Payment Method</label>
                                    <select class="form-select @error('payment_method') is-invalid @enderror" id="payment_method" name="payment_method">
                                        <option value="">Select Payment Method</option>
                                        <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>Cash</option>
                                        <option value="credit_card" {{ old('payment_method') == 'credit_card' ? 'selected' : '' }}>Credit Card</option>
                                        <option value="debit_card" {{ old('payment_method') == 'debit_card' ? 'selected' : '' }}>Debit Card</option>
                                        <option value="bank_transfer" {{ old('payment_method') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                        <option value="check" {{ old('payment_method') == 'check' ? 'selected' : '' }}>Check</option>
                                        <option value="other" {{ old('payment_method') == 'other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                    @error('payment_method')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <!-- Total Calculation Display -->
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title">Order Summary</h6>
                                        <div class="d-flex justify-content-between">
                                            <span>Subtotal:</span>
                                            <span id="subtotal_display">$0.00</span>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <span>Discount:</span>
                                            <span id="discount_display" class="text-success">-$0.00</span>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <span>VAT:</span>
                                            <span id="vat_amount_display">$0.00</span>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <span>TAX:</span>
                                            <span id="tax_amount_display">$0.00</span>
                                        </div>
                                        <hr>
                                        <div class="d-flex justify-content-between fw-bold">
                                            <span>Total:</span>
                                            <span id="total_display">$0.00</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Order Details -->
                            <div class="col-md-6">
                                <h6 class="text-primary mb-3">Order Details</h6>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="order_date" class="form-label">Order Date <span class="text-danger">*</span></label>
                                            <input type="date" class="form-control @error('order_date') is-invalid @enderror" 
                                                   id="order_date" name="order_date" value="{{ old('order_date', date('Y-m-d')) }}" required>
                                            @error('order_date')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="delivery_date" class="form-label">Delivery Date</label>
                                            <input type="date" class="form-control @error('delivery_date') is-invalid @enderror" 
                                                   id="delivery_date" name="delivery_date" value="{{ old('delivery_date') }}">
                                            @error('delivery_date')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="notes" class="form-label">Notes</label>
                                    <textarea class="form-control @error('notes') is-invalid @enderror" 
                                              id="notes" name="notes" rows="3" placeholder="Additional notes or instructions...">{{ old('notes') }}</textarea>
                                    @error('notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="attachments" class="form-label">Attachments</label>
                                    <input type="file" class="form-control @error('attachments.*') is-invalid @enderror" 
                                           id="attachments" name="attachments[]" multiple 
                                           accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                    <div class="form-text">Allowed formats: PDF, DOC, DOCX, JPG, PNG. Max size: 5MB per file.</div>
                                    @error('attachments.*')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <!-- Hidden inputs for tax data -->
                        <input type="hidden" id="tax_data" name="tax_data" value="">
                        <input type="hidden" id="total_amount" name="total_amount" value="">
                        
                        <hr>
                        
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('orders.index') }}" class="btn btn-secondary">
                                × Cancel
                            </a>
                            <button type="submit" class="btn btn-primary" onclick="prepareFormSubmission()">
                                💾 Create Order
                            </button>
                        </div>
                    </form>
                </div>
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

// Product selection is handled by dropdown in order items

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

// Add event listeners for real-time calculation
document.addEventListener('DOMContentLoaded', function() {
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

@push('styles')
<style>
.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border: 1px solid rgba(0, 0, 0, 0.125);
}

.text-primary {
    color: #0d6efd !important;
}

.form-label {
    font-weight: 500;
    margin-bottom: 0.5rem;
}

.required {
    color: #dc3545;
}

.bg-light {
    background-color: #f8f9fa !important;
}

.gap-2 {
    gap: 0.5rem !important;
}

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
    border: 1px solid #dee2e6;
    border-top: none;
    border-radius: 0 0 0.375rem 0.375rem;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    max-height: 200px;
    overflow-y: auto;
    z-index: 1000;
    display: none;
}

.typeahead-item {
    padding: 0.75rem;
    cursor: pointer;
    border-bottom: 1px solid #f8f9fa;
    transition: background-color 0.15s ease-in-out;
}

.typeahead-item:hover {
    background-color: #f8f9fa;
}

.typeahead-item:last-child {
    border-bottom: none;
}

.typeahead-item .fw-bold {
    font-weight: 600;
    color: #212529;
}

.typeahead-item .text-muted {
    color: #6c757d !important;
    font-size: 0.875rem;
}
</style>

<script>
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
            <div class="order-item border rounded p-3 mb-3" data-index="${itemIndex}">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="mb-0">Item #${itemIndex + 1}</h6>
                    <button type="button" class="btn btn-sm btn-outline-danger remove-item-btn">
                        <i class="fas fa-trash"></i> Remove
                    </button>
                </div>
                
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Product <span class="text-danger">*</span></label>
                            <select class="form-control product-select" name="items[${itemIndex}][product_id]" required>
                                <option value="">Select Product</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="mb-3">
                            <label class="form-label">Quantity <span class="text-danger">*</span></label>
                            <input type="number" class="form-control quantity-input" name="items[${itemIndex}][quantity]" min="1" value="1" required>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="mb-3">
                            <label class="form-label">Unit Price <span class="text-danger">*</span></label>
                            <input type="number" class="form-control price-input" name="items[${itemIndex}][unit_price]" step="0.01" min="0" required>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="mb-3">
                            <label class="form-label">Total</label>
                            <input type="text" class="form-control total-display" readonly>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="mb-3">
                            <label class="form-label">Available Stock</label>
                            <input type="text" class="form-control stock-display" readonly>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label class="form-label">Notes</label>
                            <textarea class="form-control" name="items[${itemIndex}][notes]" rows="2" placeholder="Optional notes for this item"></textarea>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        container.insertAdjacentHTML('beforeend', itemHtml);
        
        // Add event listeners for the new item
        const newItem = container.lastElementChild;
        populateProductDropdown(newItem.querySelector('.product-select'));
        setupItemEventListeners(newItem);
        
        itemIndex++;
        updateRemoveButtons();
    }
    
    function populateProductDropdown(selectElement) {
        fetch('/api/order-products', {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            credentials: 'same-origin'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.products) {
                data.products.forEach(product => {
                    const option = document.createElement('option');
                    option.value = product.id;
                    option.textContent = product.name;
                    option.dataset.price = product.selling_price || product.price || '';
                    selectElement.appendChild(option);
                });
            }
        })
        .catch(error => {
            console.error('Error fetching products:', error);
        });
    }
    
    function setupItemEventListeners(item) {
        const productSelect = item.querySelector('.product-select');
        const quantityInput = item.querySelector('.quantity-input');
        const priceInput = item.querySelector('.price-input');
        const totalDisplay = item.querySelector('.total-display');
        const stockDisplay = item.querySelector('.stock-display');
        const removeBtn = item.querySelector('.remove-item-btn');
        
        // Product selection change
        productSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            if (selectedOption.value) {
                priceInput.value = selectedOption.dataset.price || '';
                // Fetch and display available stock
                fetchAvailableStock(selectedOption.value, stockDisplay);
            } else {
                priceInput.value = '';
                stockDisplay.value = '';
            }
            calculateTotal();
        });
        
        // Quantity and price change
        quantityInput.addEventListener('input', function() {
            calculateTotal();
            validateStockQuantity(item);
        });
        priceInput.addEventListener('input', calculateTotal);
        
        // Remove item
        removeBtn.addEventListener('click', function() {
            item.remove();
            updateRemoveButtons();
            updateGrandTotal();
        });
        
        function calculateTotal() {
            const quantity = parseFloat(quantityInput.value) || 0;
            const price = parseFloat(priceInput.value) || 0;
            const total = quantity * price;
            totalDisplay.value = total.toFixed(2);
            
            // Update stock display to show remaining after quantity selection
            const availableStock = parseInt(stockDisplay.dataset.stock) || 0;
            const remainingStock = Math.max(0, availableStock - quantity);
            if (availableStock > 0) {
                stockDisplay.value = `${remainingStock} remaining (${availableStock} available)`;
            }
            
            updateGrandTotal();
        }
    }
    
    function fetchAvailableStock(productId, stockDisplay) {
        stockDisplay.value = 'Loading...';
        stockDisplay.className = 'form-control stock-display';
        
        fetch(`/api/order-products/${productId}/info`, {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            credentials: 'same-origin'
        })
            .then(response => response.json())
            .then(data => {
                const availableStock = data.available_stock || 0;
                const price = data.price || 0;
                const expirationDate = data.expiration_date;
                
                // Update stock display
                stockDisplay.value = `${availableStock} available`;
                stockDisplay.dataset.stock = availableStock;
                stockDisplay.dataset.productId = productId;
                
                // Update price input if it exists
                const priceInput = stockDisplay.closest('.order-item').querySelector('.price-input');
                if (priceInput) {
                    priceInput.value = price;
                    // Trigger change event to update calculations
                    priceInput.dispatchEvent(new Event('input'));
                }
                
                // Display expiration date if available
                const orderItem = stockDisplay.closest('.order-item');
                let expirationInfo = orderItem.querySelector('.expiration-info');
                if (!expirationInfo) {
                    expirationInfo = document.createElement('small');
                    expirationInfo.className = 'expiration-info text-muted d-block mt-1';
                    stockDisplay.parentNode.appendChild(expirationInfo);
                }
                
                if (expirationDate) {
                    const expDate = new Date(expirationDate);
                    const today = new Date();
                    const daysUntilExpiry = Math.ceil((expDate - today) / (1000 * 60 * 60 * 24));
                    
                    if (daysUntilExpiry <= 0) {
                        expirationInfo.innerHTML = `<span class="text-danger">⚠️ Expired on ${expDate.toLocaleDateString()}</span>`;
                    } else if (daysUntilExpiry <= 30) {
                        expirationInfo.innerHTML = `<span class="text-warning">⚠️ Expires in ${daysUntilExpiry} days (${expDate.toLocaleDateString()})</span>`;
                    } else {
                        expirationInfo.innerHTML = `<span class="text-success">✓ Expires on ${expDate.toLocaleDateString()}</span>`;
                    }
                } else {
                    expirationInfo.innerHTML = '<span class="text-muted">No expiration date</span>';
                }
                
                // Remove previous classes
                stockDisplay.classList.remove('text-danger', 'text-warning', 'text-success');
                
                // Add appropriate class based on stock level
                if (availableStock <= 0) {
                    stockDisplay.classList.add('text-danger');
                    stockDisplay.value = 'Out of Stock';
                } else if (availableStock <= 10) {
                    stockDisplay.classList.add('text-warning');
                } else {
                    stockDisplay.classList.add('text-success');
                }
                
                // Validate current quantity against available stock
                validateStockQuantity(stockDisplay.closest('.order-item'));
            })
            .catch(error => {
                console.error('Error fetching stock:', error);
                stockDisplay.value = 'Error loading stock';
                stockDisplay.classList.add('text-danger');
                stockDisplay.dataset.stock = 0;
            });
    }
    
    function validateStockQuantity(orderItem) {
        const quantityInput = orderItem.querySelector('.quantity-input');
        const stockDisplay = orderItem.querySelector('.stock-display');
        const availableStock = parseInt(stockDisplay.dataset.stock) || 0;
        const requestedQuantity = parseInt(quantityInput.value) || 0;
        
        // Remove previous validation classes
        quantityInput.classList.remove('is-invalid');
        let existingFeedback = orderItem.querySelector('.invalid-feedback');
        if (existingFeedback) {
            existingFeedback.remove();
        }
        
        if (requestedQuantity > availableStock) {
            quantityInput.classList.add('is-invalid');
            const feedback = document.createElement('div');
            feedback.className = 'invalid-feedback';
            feedback.textContent = `Only ${availableStock} items available in stock`;
            quantityInput.parentNode.appendChild(feedback);
            
            // Optionally adjust quantity to maximum available
            if (availableStock > 0) {
                quantityInput.value = availableStock;
            }
        }
    }
    
    function updateRemoveButtons() {
        const items = container.querySelectorAll('.order-item');
        items.forEach((item, index) => {
            const removeBtn = item.querySelector('.remove-item-btn');
            const title = item.querySelector('h6');
            title.textContent = `Item #${index + 1}`;
            
            // Hide remove button if only one item
            if (items.length <= 1) {
                removeBtn.style.display = 'none';
            } else {
                removeBtn.style.display = 'inline-block';
            }
        });
    }
    
    // updateGrandTotal function is now defined globally above
});
</script>

@endpush