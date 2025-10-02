@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title mb-0">Add New Expense</h3>
                </div>
                
                <form action="{{ route('expenses.store') }}" method="POST" enctype="multipart/form-data" id="expenseForm">
                    @csrf
                    <div class="card-body">
                        <!-- Basic Information -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-primary border-bottom pb-2 mb-3">
                                    <i class="fas fa-info-circle"></i> Basic Information
                                </h5>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="title" class="form-label">Expense Title <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                           id="title" name="title" value="{{ old('title') }}" required>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="category" class="form-label">Category <span class="text-danger">*</span></label>
                                    <select class="form-select @error('category') is-invalid @enderror" 
                                            id="category" name="category" required>
                                        <option value="">Select Category</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category }}" {{ old('category') == $category ? 'selected' : '' }}>
                                                {{ $category }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('category')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" 
                                              id="description" name="description" rows="3" 
                                              placeholder="Provide details about this expense...">{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Financial Details -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-primary border-bottom pb-2 mb-3">
                                    <i class="fas fa-dollar-sign"></i> Financial Details
                                </h5>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="amount" class="form-label">Amount <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('amount') is-invalid @enderror" 
                                           id="amount" name="amount" value="{{ old('amount') }}" 
                                           step="0.01" min="0" required>
                                    @error('amount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="currency" class="form-label">Currency <span class="text-danger">*</span></label>
                                    <select class="form-select @error('currency') is-invalid @enderror" 
                                            id="currency" name="currency" required>
                                        <option value="BDT" {{ old('currency', 'BDT') == 'BDT' ? 'selected' : '' }}>BDT (৳)</option>
                                        <option value="USD" {{ old('currency') == 'USD' ? 'selected' : '' }}>USD</option>
                                        <option value="EUR" {{ old('currency') == 'EUR' ? 'selected' : '' }}>EUR</option>
                                        <option value="GBP" {{ old('currency') == 'GBP' ? 'selected' : '' }}>GBP</option>
                                        <option value="CAD" {{ old('currency') == 'CAD' ? 'selected' : '' }}>CAD</option>
                                    </select>
                                    @error('currency')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="tax_amount" class="form-label">Tax Amount</label>
                                    <input type="number" class="form-control @error('tax_amount') is-invalid @enderror" 
                                           id="tax_amount" name="tax_amount" value="{{ old('tax_amount') }}" 
                                           step="0.01" min="0">
                                    @error('tax_amount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="expense_date" class="form-label">Expense Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('expense_date') is-invalid @enderror" 
                                           id="expense_date" name="expense_date" value="{{ old('expense_date', date('Y-m-d')) }}" required>
                                    @error('expense_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="payment_method" class="form-label">Payment Method</label>
                                    <select class="form-select @error('payment_method') is-invalid @enderror" 
                                            id="payment_method" name="payment_method">
                                        <option value="">Select Payment Method</option>
                                        @foreach($paymentMethods as $method)
                                            <option value="{{ $method }}" {{ old('payment_method') == $method ? 'selected' : '' }}>
                                                {{ $method }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('payment_method')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Vendor & Reference Details -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-primary border-bottom pb-2 mb-3">
                                    <i class="fas fa-building"></i> Vendor & Reference Details
                                </h5>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="vendor" class="form-label">Vendor</label>
                                    <input type="text" class="form-control @error('vendor') is-invalid @enderror" 
                                           id="vendor" name="vendor" value="{{ old('vendor') }}" 
                                           placeholder="Company or person paid">
                                    @error('vendor')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="receipt_number" class="form-label">Receipt/Invoice Number</label>
                                    <input type="text" class="form-control @error('receipt_number') is-invalid @enderror" 
                                           id="receipt_number" name="receipt_number" value="{{ old('receipt_number') }}" 
                                           placeholder="Receipt or invoice number">
                                    @error('receipt_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="reference_number" class="form-label">Reference Number</label>
                                    <input type="text" class="form-control @error('reference_number') is-invalid @enderror" 
                                           id="reference_number" name="reference_number" value="{{ old('reference_number') }}" 
                                           placeholder="Internal reference number">
                                    @error('reference_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="priority" class="form-label">Priority <span class="text-danger">*</span></label>
                                    <select class="form-select @error('priority') is-invalid @enderror" 
                                            id="priority" name="priority" required>
                                        <option value="low" {{ old('priority', 'medium') == 'low' ? 'selected' : '' }}>Low</option>
                                        <option value="medium" {{ old('priority', 'medium') == 'medium' ? 'selected' : '' }}>Medium</option>
                                        <option value="high" {{ old('priority', 'medium') == 'high' ? 'selected' : '' }}>High</option>
                                    </select>
                                    @error('priority')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Additional Options -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-primary border-bottom pb-2 mb-3">
                                    <i class="fas fa-cog"></i> Additional Options
                                </h5>
                            </div>
                            <div class="col-12">
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="is_reimbursable" 
                                               name="is_reimbursable" value="1" {{ old('is_reimbursable', true) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_reimbursable">
                                            This expense is reimbursable
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="notes" class="form-label">Additional Notes</label>
                                    <textarea class="form-control @error('notes') is-invalid @enderror" 
                                              id="notes" name="notes" rows="3" 
                                              placeholder="Any additional notes or comments...">{{ old('notes') }}</textarea>
                                    @error('notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- File Attachments -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-primary border-bottom pb-2 mb-3">
                                    <i class="fas fa-paperclip"></i> Attachments
                                </h5>
                            </div>
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="attachments" class="form-label">Upload Files</label>
                                    <input type="file" class="form-control @error('attachments.*') is-invalid @enderror" 
                                           id="attachments" name="attachments[]" multiple 
                                           accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                                    <div class="form-text">
                                        Supported formats: PDF, JPG, PNG, DOC, DOCX. Max size: 5MB per file.
                                    </div>
                                    @error('attachments.*')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div id="file-preview" class="mt-3"></div>
                            </div>
                        </div>

                        <!-- Summary -->
                        <div class="row">
                            <div class="col-12">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title">Expense Summary</h6>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <p class="mb-1"><strong>Subtotal:</strong> <span id="summary-amount">$0.00</span></p>
                                                <p class="mb-1"><strong>Tax:</strong> <span id="summary-tax">$0.00</span></p>
                                            </div>
                                            <div class="col-md-6">
                                                <p class="mb-0"><strong>Total Amount:</strong> <span id="summary-total" class="text-primary fs-5">$0.00</span></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-footer">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('expenses.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Expenses
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Create Expense
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border: 1px solid rgba(0, 0, 0, 0.125);
}

.form-control:focus, .form-select:focus {
    border-color: #86b7fe;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}

.text-primary {
    color: #0d6efd !important;
}

.border-bottom {
    border-bottom: 2px solid #dee2e6 !important;
}

.file-preview-item {
    display: inline-block;
    margin: 5px;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 5px;
    background-color: #f8f9fa;
}

.file-preview-item .file-name {
    font-size: 0.875rem;
    margin-bottom: 5px;
}

.file-preview-item .file-size {
    font-size: 0.75rem;
    color: #6c757d;
}

.remove-file {
    background: none;
    border: none;
    color: #dc3545;
    font-size: 0.875rem;
    cursor: pointer;
    padding: 0;
    margin-left: 10px;
}

.remove-file:hover {
    color: #b02a37;
}

@media (max-width: 768px) {
    .card-body {
        padding: 1rem;
    }
    
    .row.mb-4 {
        margin-bottom: 1.5rem !important;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const amountInput = document.getElementById('amount');
    const taxInput = document.getElementById('tax_amount');
    const currencySelect = document.getElementById('currency');
    const attachmentsInput = document.getElementById('attachments');
    
    // Update summary when amounts change
    function updateSummary() {
        const amount = parseFloat(amountInput.value) || 0;
        const tax = parseFloat(taxInput.value) || 0;
        const currency = currencySelect.value;
        const total = amount + tax;
        
        const currencySymbol = {
            'USD': '$',
            'EUR': '€',
            'GBP': '£',
            'CAD': 'C$'
        }[currency] || '$';
        
        document.getElementById('summary-amount').textContent = currencySymbol + amount.toFixed(2);
        document.getElementById('summary-tax').textContent = currencySymbol + tax.toFixed(2);
        document.getElementById('summary-total').textContent = currencySymbol + total.toFixed(2);
    }
    
    amountInput.addEventListener('input', updateSummary);
    taxInput.addEventListener('input', updateSummary);
    currencySelect.addEventListener('change', updateSummary);
    
    // File preview functionality
    attachmentsInput.addEventListener('change', function() {
        const preview = document.getElementById('file-preview');
        preview.innerHTML = '';
        
        Array.from(this.files).forEach((file, index) => {
            const fileItem = document.createElement('div');
            fileItem.className = 'file-preview-item';
            fileItem.innerHTML = `
                <div class="file-name">
                    <i class="fas fa-file"></i> ${file.name}
                    <button type="button" class="remove-file" onclick="removeFile(${index})">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="file-size">${(file.size / 1024 / 1024).toFixed(2)} MB</div>
            `;
            preview.appendChild(fileItem);
        });
    });
    
    // Initialize summary
    updateSummary();
});

function removeFile(index) {
    const input = document.getElementById('attachments');
    const dt = new DataTransfer();
    
    Array.from(input.files).forEach((file, i) => {
        if (i !== index) {
            dt.items.add(file);
        }
    });
    
    input.files = dt.files;
    input.dispatchEvent(new Event('change'));
}

// Form validation
document.getElementById('expenseForm').addEventListener('submit', function(e) {
    const amount = document.getElementById('amount').value;
    const title = document.getElementById('title').value;
    const category = document.getElementById('category').value;
    
    if (!title.trim()) {
        e.preventDefault();
        alert('Please enter an expense title.');
        document.getElementById('title').focus();
        return;
    }
    
    if (!category) {
        e.preventDefault();
        alert('Please select a category.');
        document.getElementById('category').focus();
        return;
    }
    
    if (!amount || parseFloat(amount) <= 0) {
        e.preventDefault();
        alert('Please enter a valid amount.');
        document.getElementById('amount').focus();
        return;
    }
});
</script>
@endsection