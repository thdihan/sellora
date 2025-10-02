@extends('layouts.app')

@section('title', 'Stock Adjustments')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">📦 Stock Adjustments</h1>
            <p class="text-muted mb-0">Adjust inventory levels for products in your main warehouse</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('inventory.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Back to Inventory
            </a>
        </div>
    </div>

    <!-- Adjustment Form -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">🔧 Create Stock Adjustment</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('inventory.adjustments.store') }}" method="POST" id="adjustmentForm">
                        @csrf
                        
                        <!-- Adjustment Date -->
                        <div class="mb-3">
                            <div class="col-md-6">
                                <label for="adjustment_date" class="form-label">Adjustment Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="adjustment_date" name="adjustment_date" value="{{ date('Y-m-d') }}" required>
                            </div>
                        </div>

                        <!-- Reason -->
                        <div class="mb-3">
                            <label for="reason" class="form-label">Reason for Adjustment</label>
                            <textarea class="form-control" id="reason" name="reason" rows="2" placeholder="Enter reason for stock adjustment..."></textarea>
                        </div>

                        <!-- Product Selection -->
                        <div class="mb-3">
                            <label class="form-label">Products to Adjust</label>
                            <div class="border rounded p-3">
                                <div class="row mb-2">
                                    <div class="col-md-4">
                                        <input type="text" class="form-control" id="productSearch" placeholder="Search products...">
                                    </div>
                                    <div class="col-md-8 text-end">
                                        <button type="button" class="btn btn-primary btn-sm" onclick="addProductRow()">
                                            <i class="fas fa-plus me-1"></i> Add Product
                                        </button>
                                    </div>
                                </div>
                                
                                <div id="productRows">
                                    <!-- Product rows will be added here dynamically -->
                                </div>
                                
                                <div id="noProductsMessage" class="text-center text-muted py-4">
                                    <span style="font-size: 2rem;" class="mb-2 d-block">📦</span>
                                    <p class="mb-0">No products added yet. Click "Add Product" to start.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="d-flex justify-content-end gap-2">
                            <button type="button" class="btn btn-secondary" onclick="resetForm()">Reset</button>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save me-1"></i> Save Adjustments
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Summary Panel -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">📊 Adjustment Summary</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted d-block">Total Products</small>
                        <h4 class="mb-0" id="totalProducts">0</h4>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted d-block">Positive Adjustments</small>
                        <h4 class="mb-0 text-success" id="positiveAdjustments">0</h4>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted d-block">Negative Adjustments</small>
                        <h4 class="mb-0 text-danger" id="negativeAdjustments">0</h4>
                    </div>
                    <hr>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Note:</strong> Stock adjustments will immediately update your inventory levels. Please ensure all quantities are correct before saving.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Product Row Template -->
<template id="productRowTemplate">
    <div class="product-row border rounded p-3 mb-2">
        <div class="row align-items-center">
            <div class="col-md-4">
                <select class="form-select product-select" name="products[INDEX][product_id]" required>
                    <option value="">Select Product</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}" data-current-stock="{{ $product->stockBalances->sum('quantity') }}">
                            {{ $product->name }} ({{ $product->sku }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <input type="number" class="form-control current-stock" placeholder="Current" readonly>
            </div>
            <div class="col-md-2">
                <input type="number" class="form-control adjustment-qty" name="products[INDEX][adjustment_quantity]" placeholder="±Qty" required>
            </div>
            <div class="col-md-2">
                <input type="number" class="form-control new-stock" placeholder="New" readonly>
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-danger btn-sm" onclick="removeProductRow(this)">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
    </div>
</template>

<script>
let productIndex = 0;

function addProductRow() {
    const template = document.getElementById('productRowTemplate');
    const clone = template.content.cloneNode(true);
    
    // Replace INDEX with actual index
    clone.innerHTML = clone.innerHTML.replace(/INDEX/g, productIndex);
    
    document.getElementById('productRows').appendChild(clone);
    document.getElementById('noProductsMessage').style.display = 'none';
    
    // Add event listeners to the new row
    const newRow = document.getElementById('productRows').lastElementChild;
    const productSelect = newRow.querySelector('.product-select');
    const adjustmentQty = newRow.querySelector('.adjustment-qty');
    
    productSelect.addEventListener('change', updateCurrentStock);
    adjustmentQty.addEventListener('input', calculateNewStock);
    
    productIndex++;
    updateSummary();
}

function removeProductRow(button) {
    button.closest('.product-row').remove();
    
    if (document.getElementById('productRows').children.length === 0) {
        document.getElementById('noProductsMessage').style.display = 'block';
    }
    
    updateSummary();
}

function updateCurrentStock(event) {
    const select = event.target;
    const row = select.closest('.product-row');
    const currentStockInput = row.querySelector('.current-stock');
    const selectedOption = select.options[select.selectedIndex];
    
    if (selectedOption.value) {
        const currentStock = selectedOption.getAttribute('data-current-stock') || 0;
        currentStockInput.value = currentStock;
        calculateNewStock.call(row.querySelector('.adjustment-qty'));
    } else {
        currentStockInput.value = '';
        row.querySelector('.new-stock').value = '';
    }
}

function calculateNewStock() {
    const row = this.closest('.product-row');
    const currentStock = parseFloat(row.querySelector('.current-stock').value) || 0;
    const adjustmentQty = parseFloat(this.value) || 0;
    const newStockInput = row.querySelector('.new-stock');
    
    newStockInput.value = currentStock + adjustmentQty;
    updateSummary();
}

function updateSummary() {
    const rows = document.querySelectorAll('.product-row');
    let totalProducts = rows.length;
    let positiveAdjustments = 0;
    let negativeAdjustments = 0;
    
    rows.forEach(row => {
        const adjustmentQty = parseFloat(row.querySelector('.adjustment-qty').value) || 0;
        if (adjustmentQty > 0) {
            positiveAdjustments++;
        } else if (adjustmentQty < 0) {
            negativeAdjustments++;
        }
    });
    
    document.getElementById('totalProducts').textContent = totalProducts;
    document.getElementById('positiveAdjustments').textContent = positiveAdjustments;
    document.getElementById('negativeAdjustments').textContent = negativeAdjustments;
}

function resetForm() {
    document.getElementById('adjustmentForm').reset();
    document.getElementById('productRows').innerHTML = '';
    document.getElementById('noProductsMessage').style.display = 'block';
    productIndex = 0;
    updateSummary();
}

// Product search functionality
document.getElementById('productSearch').addEventListener('input', function() {
    const searchTerm = this.value.toLowerCase();
    const productSelects = document.querySelectorAll('.product-select');
    
    productSelects.forEach(select => {
        const options = select.querySelectorAll('option');
        options.forEach(option => {
            if (option.value === '') return; // Skip empty option
            
            const text = option.textContent.toLowerCase();
            if (text.includes(searchTerm)) {
                option.style.display = 'block';
            } else {
                option.style.display = 'none';
            }
        });
    });
});
</script>
@endsection