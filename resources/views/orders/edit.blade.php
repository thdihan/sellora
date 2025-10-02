@extends('layouts.app')

@section('title', 'Edit Order')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h1 class="text-xl font-semibold text-gray-900">Edit Order - {{ $order->order_number }}</h1>
            <a href="{{ route('orders.show', $order) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                ← Back to Order
            </a>
        </div>
        <div class="p-6">
            @if ($errors->any())
                <div class="mb-6 bg-red-50 border border-red-200 rounded-md p-4">
                    <ul class="list-disc list-inside text-sm text-red-600 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('orders.update', $order) }}" method="POST" enctype="multipart/form-data" id="orderForm">
                @csrf
                @method('PATCH')
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Customer Information -->
                    <div class="bg-gray-50 rounded-lg border border-gray-200">
                        <div class="px-4 py-3 border-b border-gray-200 bg-gray-100 rounded-t-lg">
                            <h3 class="text-sm font-medium text-gray-900">Customer Information</h3>
                        </div>
                        <div class="p-4 space-y-4">
                            <div>
                                <label for="customer_name" class="block text-sm font-medium text-gray-700 mb-1">Customer Name <span class="text-red-500">*</span></label>
                                <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="customer_name" name="customer_name" 
                                       value="{{ old('customer_name', $order->customer_name) }}" required>
                            </div>
                            
                            <div>
                                <label for="customer_email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                <input type="email" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="customer_email" name="customer_email" 
                                       value="{{ old('customer_email', $order->customer_email) }}">
                            </div>
                            
                            <div>
                                <label for="customer_phone" class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                                <input type="tel" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="customer_phone" name="customer_phone" 
                                       value="{{ old('customer_phone', $order->customer_phone) }}">
                            </div>
                            
                            <div>
                                <label for="customer_address" class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                                <textarea class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="customer_address" name="customer_address" rows="3">{{ old('customer_address', $order->customer_address) }}</textarea>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Product Information -->
                    <div class="bg-gray-50 rounded-lg border border-gray-200">
                        <div class="px-4 py-3 border-b border-gray-200 bg-gray-100 rounded-t-lg">
                            <h3 class="text-sm font-medium text-gray-900">Product Information</h3>
                        </div>
                        <div class="p-4 space-y-4">
                            <div>
                                <label for="product_name" class="block text-sm font-medium text-gray-700 mb-1">Product Name <span class="text-red-500">*</span></label>
                                <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="product_name" name="product_name" 
                                       value="{{ old('product_name', $order->product_name) }}" required>
                            </div>
                            
                            <div>
                                <label for="product_description" class="block text-sm font-medium text-gray-700 mb-1">Product Description</label>
                                <textarea class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="product_description" name="product_description" rows="3">{{ old('product_description', $order->product_description) }}</textarea>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="quantity" class="block text-sm font-medium text-gray-700 mb-1">Quantity <span class="text-red-500">*</span></label>
                                    <input type="number" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="quantity" name="quantity" 
                                           value="{{ old('quantity', $order->quantity) }}" min="1" required>
                                </div>
                                <div>
                                    <label for="unit_price" class="block text-sm font-medium text-gray-700 mb-1">Unit Price <span class="text-red-500">*</span></label>
                                    <input type="number" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="unit_price" name="unit_price" 
                                           value="{{ old('unit_price', $order->unit_price) }}" step="0.01" min="0" required>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
                    <!-- Pricing & Payment -->
                    <div class="bg-gray-50 rounded-lg border border-gray-200">
                        <div class="px-4 py-3 border-b border-gray-200 bg-gray-100 rounded-t-lg">
                            <h3 class="text-sm font-medium text-gray-900">Pricing & Payment</h3>
                        </div>
                        <div class="p-4 space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="discount" class="block text-sm font-medium text-gray-700 mb-1">Discount ($)</label>
                                    <input type="number" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="discount" name="discount" 
                                           value="{{ old('discount', $order->discount) }}" step="0.01" min="0">
                                </div>
                                <div>
                                    <label for="tax" class="block text-sm font-medium text-gray-700 mb-1">Tax ($)</label>
                                    <input type="number" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="tax" name="tax" 
                                           value="{{ old('tax', $order->tax) }}" step="0.01" min="0">
                                </div>
                            </div>
                            
                            <div>
                                <label for="payment_method" class="block text-sm font-medium text-gray-700 mb-1">Payment Method</label>
                                <select class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="payment_method" name="payment_method">
                                    <option value="">Select Payment Method</option>
                                    <option value="cash" {{ old('payment_method', $order->payment_method) == 'cash' ? 'selected' : '' }}>Cash</option>
                                    <option value="credit_card" {{ old('payment_method', $order->payment_method) == 'credit_card' ? 'selected' : '' }}>Credit Card</option>
                                    <option value="debit_card" {{ old('payment_method', $order->payment_method) == 'debit_card' ? 'selected' : '' }}>Debit Card</option>
                                    <option value="bank_transfer" {{ old('payment_method', $order->payment_method) == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                    <option value="check" {{ old('payment_method', $order->payment_method) == 'check' ? 'selected' : '' }}>Check</option>
                                    <option value="other" {{ old('payment_method', $order->payment_method) == 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                            </div>
                            
                            <div>
                                <label for="payment_status" class="block text-sm font-medium text-gray-700 mb-1">Payment Status</label>
                                <select class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="payment_status" name="payment_status">
                                    <option value="pending" {{ old('payment_status', $order->payment_status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="partial" {{ old('payment_status', $order->payment_status) == 'partial' ? 'selected' : '' }}>Partial</option>
                                    <option value="paid" {{ old('payment_status', $order->payment_status) == 'paid' ? 'selected' : '' }}>Paid</option>
                                    <option value="refunded" {{ old('payment_status', $order->payment_status) == 'refunded' ? 'selected' : '' }}>Refunded</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Order Details -->
                    <div class="bg-gray-50 rounded-lg border border-gray-200">
                        <div class="px-4 py-3 border-b border-gray-200 bg-gray-100 rounded-t-lg">
                            <h3 class="text-sm font-medium text-gray-900">Order Details</h3>
                        </div>
                        <div class="p-4 space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="order_date" class="block text-sm font-medium text-gray-700 mb-1">Order Date <span class="text-red-500">*</span></label>
                                    <input type="date" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="order_date" name="order_date" 
                                           value="{{ old('order_date', $order->created_at->format('Y-m-d')) }}" required>
                                </div>
                                <div>
                                    <label for="delivery_date" class="block text-sm font-medium text-gray-700 mb-1">Delivery Date</label>
                                    <input type="date" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="delivery_date" name="delivery_date" 
                                           value="{{ old('delivery_date', (isset($order->delivery_date) && $order->delivery_date) ? $order->delivery_date->format('Y-m-d') : '') }}">
                                </div>
                            </div>
                            
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Order Status</label>
                                <select class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="status" name="status">
                                    <option value="pending" {{ old('status', $order->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="processing" {{ old('status', $order->status) == 'processing' ? 'selected' : '' }}>Processing</option>
                                    <option value="shipped" {{ old('status', $order->status) == 'shipped' ? 'selected' : '' }}>Shipped</option>
                                    <option value="delivered" {{ old('status', $order->status) == 'delivered' ? 'selected' : '' }}>Delivered</option>
                                    <option value="cancelled" {{ old('status', $order->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                            </div>
                            
                            <div>
                                <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                                <textarea class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="notes" name="notes" rows="4" placeholder="Add any additional notes about this order...">{{ old('notes', $order->notes) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- File Attachments -->
                <div class="bg-gray-50 rounded-lg border border-gray-200 mt-6">
                    <div class="px-4 py-3 border-b border-gray-200 bg-gray-100 rounded-t-lg">
                        <h3 class="text-sm font-medium text-gray-900">File Attachments</h3>
                    </div>
                    <div class="p-4">
                        @if($order->attachments && count($order->attachments) > 0)
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Current Attachments</label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                                @foreach($order->attachments as $index => $attachment)
                                <div class="bg-white rounded-lg border border-gray-200 p-3 text-center hover:shadow-md transition-shadow">
                                    <div class="text-gray-400 mb-2">
                                        <svg class="w-8 h-8 mx-auto" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                    <h6 class="text-sm font-medium text-gray-900 mb-1 truncate">{{ $attachment['name'] }}</h6>
                                    <p class="text-xs text-gray-500 mb-2">{{ number_format($attachment['size'] / 1024, 2) }} KB</p>
                                    <a href="{{ route('orders.download-attachment', [$order, $index]) }}" class="inline-flex items-center px-2 py-1 border border-blue-300 rounded text-xs font-medium text-blue-700 bg-blue-50 hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                        </svg>
                                        Download
                                    </a>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                        
                        <div class="mb-4">
                            <label for="attachments" class="block text-sm font-medium text-gray-700 mb-1">Add New Attachments</label>
                            <input type="file" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" id="attachments" name="attachments[]" multiple 
                                   accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.gif">
                            <p class="mt-1 text-sm text-gray-500">You can upload multiple files. Supported formats: PDF, DOC, DOCX, XLS, XLSX, JPG, JPEG, PNG, GIF. Max size: 10MB per file.</p>
                        </div>
                        
                        <div id="file-preview" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4"></div>
                    </div>
                </div>
                
                <!-- Order Summary -->
                <div class="bg-gray-50 rounded-lg border border-gray-200 mt-6">
                    <div class="px-4 py-3 border-b border-gray-200 bg-gray-100 rounded-t-lg">
                        <h3 class="text-sm font-medium text-gray-900">Order Summary</h3>
                    </div>
                    <div class="p-4">
                        <div class="max-w-md">
                            <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                <span class="text-sm text-gray-600">Subtotal:</span>
                                <span id="subtotal" class="text-sm font-medium text-gray-900">$0.00</span>
                            </div>
                            <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                <span class="text-sm text-gray-600">Discount:</span>
                                <span id="discount-amount" class="text-sm font-medium text-green-600">-$0.00</span>
                            </div>
                            <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                <span class="text-sm text-gray-600">Tax:</span>
                                <span id="tax-amount" class="text-sm font-medium text-gray-900">$0.00</span>
                            </div>
                            <div class="flex justify-between items-center py-3 border-t-2 border-gray-300 mt-2">
                                <span class="text-base font-semibold text-gray-900">Total:</span>
                                <span id="total" class="text-lg font-bold text-blue-600">$0.00</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Form Actions -->
                <div class="flex justify-end space-x-3 mt-6 pt-6 border-t border-gray-200">
                    <a href="{{ route('orders.show', $order) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                        × Cancel
                    </a>
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                        💾 Update Order
                    </button>
                </div>
            </form>
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
            filePreview.className = 'bg-white rounded-lg border border-gray-200 p-3 text-center hover:shadow-md transition-shadow';
            filePreview.innerHTML = `
                <div class="text-blue-500 mb-2">
                    <svg class="w-8 h-8 mx-auto" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <h6 class="text-sm font-medium text-gray-900 mb-1 truncate">${file.name}</h6>
                <p class="text-xs text-gray-500 mb-2">${fileSize} KB</p>
                <button type="button" class="inline-flex items-center px-2 py-1 border border-red-300 rounded text-xs font-medium text-red-700 bg-red-50 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors" onclick="removeFile(${index})">
                    ×
                </button>
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