@extends('layouts.app')

@section('title', 'Edit Order')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Edit Order - {{ $order->order_number }}</h5>
                    <a href="{{ route('orders.show', $order) }}" class="btn btn-outline-secondary">
                        ← Back to Order
                    </a>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('orders.update', $order) }}" method="POST" enctype="multipart/form-data" id="orderForm">
                        @csrf
                        @method('PATCH')
                        
                        <div class="row">
                            <!-- Customer Information -->
                            <div class="col-md-6">
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h6 class="mb-0">Customer Information</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="customer_name" class="form-label">Customer Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="customer_name" name="customer_name" 
                                                   value="{{ old('customer_name', $order->customer_name) }}" required>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="customer_email" class="form-label">Email</label>
                                            <input type="email" class="form-control" id="customer_email" name="customer_email" 
                                                   value="{{ old('customer_email', $order->customer_email) }}">
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="customer_phone" class="form-label">Phone</label>
                                            <input type="tel" class="form-control" id="customer_phone" name="customer_phone" 
                                                   value="{{ old('customer_phone', $order->customer_phone) }}">
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="customer_address" class="form-label">Address</label>
                                            <textarea class="form-control" id="customer_address" name="customer_address" rows="3">{{ old('customer_address', $order->customer_address) }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Product Information -->
                            <div class="col-md-6">
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h6 class="mb-0">Product Information</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="product_name" class="form-label">Product Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="product_name" name="product_name" 
                                                   value="{{ old('product_name', $order->product_name) }}" required>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="product_description" class="form-label">Product Description</label>
                                            <textarea class="form-control" id="product_description" name="product_description" rows="3">{{ old('product_description', $order->product_description) }}</textarea>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="quantity" class="form-label">Quantity <span class="text-danger">*</span></label>
                                                    <input type="number" class="form-control" id="quantity" name="quantity" 
                                                           value="{{ old('quantity', $order->quantity) }}" min="1" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="unit_price" class="form-label">Unit Price <span class="text-danger">*</span></label>
                                                    <input type="number" class="form-control" id="unit_price" name="unit_price" 
                                                           value="{{ old('unit_price', $order->unit_price) }}" step="0.01" min="0" required>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <!-- Pricing & Payment -->
                            <div class="col-md-6">
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h6 class="mb-0">Pricing & Payment</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="discount" class="form-label">Discount ($)</label>
                                                    <input type="number" class="form-control" id="discount" name="discount" 
                                                           value="{{ old('discount', $order->discount) }}" step="0.01" min="0">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="tax" class="form-label">Tax ($)</label>
                                                    <input type="number" class="form-control" id="tax" name="tax" 
                                                           value="{{ old('tax', $order->tax) }}" step="0.01" min="0">
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="payment_method" class="form-label">Payment Method</label>
                                            <select class="form-select" id="payment_method" name="payment_method">
                                                <option value="">Select Payment Method</option>
                                                <option value="cash" {{ old('payment_method', $order->payment_method) == 'cash' ? 'selected' : '' }}>Cash</option>
                                                <option value="credit_card" {{ old('payment_method', $order->payment_method) == 'credit_card' ? 'selected' : '' }}>Credit Card</option>
                                                <option value="debit_card" {{ old('payment_method', $order->payment_method) == 'debit_card' ? 'selected' : '' }}>Debit Card</option>
                                                <option value="bank_transfer" {{ old('payment_method', $order->payment_method) == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                                <option value="check" {{ old('payment_method', $order->payment_method) == 'check' ? 'selected' : '' }}>Check</option>
                                                <option value="other" {{ old('payment_method', $order->payment_method) == 'other' ? 'selected' : '' }}>Other</option>
                                            </select>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="payment_status" class="form-label">Payment Status</label>
                                            <select class="form-select" id="payment_status" name="payment_status">
                                                <option value="pending" {{ old('payment_status', $order->payment_status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                                <option value="partial" {{ old('payment_status', $order->payment_status) == 'partial' ? 'selected' : '' }}>Partial</option>
                                                <option value="paid" {{ old('payment_status', $order->payment_status) == 'paid' ? 'selected' : '' }}>Paid</option>
                                                <option value="refunded" {{ old('payment_status', $order->payment_status) == 'refunded' ? 'selected' : '' }}>Refunded</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Order Details -->
                            <div class="col-md-6">
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h6 class="mb-0">Order Details</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="order_date" class="form-label">Order Date <span class="text-danger">*</span></label>
                                                    <input type="date" class="form-control" id="order_date" name="order_date" 
                                                           value="{{ old('order_date', $order->created_at->format('Y-m-d')) }}" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="delivery_date" class="form-label">Delivery Date</label>
                                                    <input type="date" class="form-control" id="delivery_date" name="delivery_date" 
                                                           value="{{ old('delivery_date', (isset($order->delivery_date) && $order->delivery_date) ? $order->delivery_date->format('Y-m-d') : '') }}">
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="status" class="form-label">Order Status</label>
                                            <select class="form-select" id="status" name="status">
                                                <option value="pending" {{ old('status', $order->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                                <option value="processing" {{ old('status', $order->status) == 'processing' ? 'selected' : '' }}>Processing</option>
                                                <option value="shipped" {{ old('status', $order->status) == 'shipped' ? 'selected' : '' }}>Shipped</option>
                                                <option value="delivered" {{ old('status', $order->status) == 'delivered' ? 'selected' : '' }}>Delivered</option>
                                                <option value="cancelled" {{ old('status', $order->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                            </select>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="notes" class="form-label">Notes</label>
                                            <textarea class="form-control" id="notes" name="notes" rows="4" placeholder="Add any additional notes about this order...">{{ old('notes', $order->notes) }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- File Attachments -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="mb-0">File Attachments</h6>
                            </div>
                            <div class="card-body">
                                @if($order->attachments && count($order->attachments) > 0)
                                <div class="mb-3">
                                    <label class="form-label">Current Attachments</label>
                                    <div class="row">
                                        @foreach($order->attachments as $index => $attachment)
                                        <div class="col-md-3 mb-2">
                                            <div class="card">
                                                <div class="card-body text-center p-2">
                                                    <i class="fas fa-file fa-lg text-muted mb-1"></i>
                                                    <h6 class="card-title small">{{ $attachment['name'] }}</h6>
                                                    <p class="card-text text-muted small">{{ number_format($attachment['size'] / 1024, 2) }} KB</p>
                                                    <a href="{{ route('orders.download-attachment', [$order, $index]) }}" class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-download"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                                @endif
                                
                                <div class="mb-3">
                                    <label for="attachments" class="form-label">Add New Attachments</label>
                                    <input type="file" class="form-control" id="attachments" name="attachments[]" multiple 
                                           accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.gif">
                                    <div class="form-text">You can upload multiple files. Supported formats: PDF, DOC, DOCX, XLS, XLSX, JPG, JPEG, PNG, GIF. Max size: 10MB per file.</div>
                                </div>
                                
                                <div id="file-preview" class="row"></div>
                            </div>
                        </div>
                        
                        <!-- Order Summary -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="mb-0">Order Summary</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>Subtotal:</span>
                                            <span id="subtotal">$0.00</span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>Discount:</span>
                                            <span id="discount-amount" class="text-success">-$0.00</span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>Tax:</span>
                                            <span id="tax-amount">$0.00</span>
                                        </div>
                                        <hr>
                                        <div class="d-flex justify-content-between fw-bold fs-5">
                                            <span>Total:</span>
                                            <span id="total" class="text-primary">$0.00</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Form Actions -->
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('orders.show', $order) }}" class="btn btn-secondary">
                                × Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                💾 Update Order
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
// Calculate order totals
function calculateTotals() {
    const quantity = parseFloat(document.getElementById('quantity').value) || 0;
    const unitPrice = parseFloat(document.getElementById('unit_price').value) || 0;
    const discount = parseFloat(document.getElementById('discount').value) || 0;
    const tax = parseFloat(document.getElementById('tax').value) || 0;
    
    const subtotal = quantity * unitPrice;
    const total = subtotal - discount + tax;
    
    document.getElementById('subtotal').textContent = '$' + subtotal.toFixed(2);
    document.getElementById('discount-amount').textContent = '-$' + discount.toFixed(2);
    document.getElementById('tax-amount').textContent = '$' + tax.toFixed(2);
    document.getElementById('total').textContent = '$' + total.toFixed(2);
}

// File preview functionality
function previewFiles() {
    const fileInput = document.getElementById('attachments');
    const preview = document.getElementById('file-preview');
    preview.innerHTML = '';
    
    if (fileInput.files) {
        Array.from(fileInput.files).forEach((file, index) => {
            const fileSize = (file.size / 1024).toFixed(2);
            const filePreview = document.createElement('div');
            filePreview.className = 'col-md-3 mb-2';
            filePreview.innerHTML = `
                <div class="card">
                    <div class="card-body text-center p-2">
                        <i class="fas fa-file fa-lg text-primary mb-1"></i>
                        <h6 class="card-title small">${file.name}</h6>
                        <p class="card-text text-muted small">${fileSize} KB</p>
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeFile(${index})">
                            ×
                        </button>
                    </div>
                </div>
            `;
            preview.appendChild(filePreview);
        });
    }
}

// Remove file from selection
function removeFile(index) {
    const fileInput = document.getElementById('attachments');
    const dt = new DataTransfer();
    
    Array.from(fileInput.files).forEach((file, i) => {
        if (i !== index) {
            dt.items.add(file);
        }
    });
    
    fileInput.files = dt.files;
    previewFiles();
}

// Event listeners
document.addEventListener('DOMContentLoaded', function() {
    // Calculate initial totals
    calculateTotals();
    
    // Add event listeners for calculation
    ['quantity', 'unit_price', 'discount', 'tax'].forEach(id => {
        document.getElementById(id).addEventListener('input', calculateTotals);
    });
    
    // File input change event
    document.getElementById('attachments').addEventListener('change', previewFiles);
    
    // Form validation
    document.getElementById('orderForm').addEventListener('submit', function(e) {
        const customerName = document.getElementById('customer_name').value.trim();
        const productName = document.getElementById('product_name').value.trim();
        const quantity = document.getElementById('quantity').value;
        const unitPrice = document.getElementById('unit_price').value;
        const orderDate = document.getElementById('order_date').value;
        
        if (!customerName || !productName || !quantity || !unitPrice || !orderDate) {
            e.preventDefault();
            alert('Please fill in all required fields.');
            return false;
        }
        
        if (parseFloat(quantity) <= 0) {
            e.preventDefault();
            alert('Quantity must be greater than 0.');
            return false;
        }
        
        if (parseFloat(unitPrice) < 0) {
            e.preventDefault();
            alert('Unit price cannot be negative.');
            return false;
        }
    });
});
</script>
@endpush

@push('styles')
<style>
.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border: 1px solid rgba(0, 0, 0, 0.125);
}

.text-danger {
    color: #dc3545 !important;
}

.text-primary {
    color: #0d6efd !important;
}

.text-success {
    color: #198754 !important;
}

.form-label {
    font-weight: 500;
}

.btn {
    border-radius: 0.375rem;
}

.gap-2 {
    gap: 0.5rem !important;
}

#file-preview .card {
    transition: transform 0.2s;
}

#file-preview .card:hover {
    transform: translateY(-2px);
}

.alert {
    border-radius: 0.375rem;
}
</style>
@endpush