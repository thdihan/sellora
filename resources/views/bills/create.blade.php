@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title mb-0">Add New Bill</h3>
                </div>
                
                <form action="{{ route('bills.store') }}" method="POST" enctype="multipart/form-data" id="billForm">
                    @csrf
                    <div class="card-body">
                        <!-- Basic Information -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-primary border-bottom pb-2 mb-3">
                                    ℹ️ Bill Information
                                </h5>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="amount" class="form-label">Amount <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">৳</span>
                                        <input type="number" class="form-control @error('amount') is-invalid @enderror" 
                                               id="amount" name="amount" value="{{ old('amount') }}" 
                                               step="0.01" min="0" required>
                                    </div>
                                    @error('amount')
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
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="purpose" class="form-label">Purpose <span class="text-danger">*</span></label>
                                    <textarea class="form-control @error('purpose') is-invalid @enderror" 
                                              id="purpose" name="purpose" rows="4" required 
                                              placeholder="Describe the purpose of this bill...">{{ old('purpose') }}</textarea>
                                    @error('purpose')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Additional Information -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-primary border-bottom pb-2 mb-3">
                                    📋 Additional Details
                                </h5>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="category" class="form-label">Category <span class="text-danger">*</span></label>
                                    <select class="form-select @error('category') is-invalid @enderror" 
                                            id="category" name="category" required>
                                        <option value="">Select Category</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category }}" {{ old('category') == $category ? 'selected' : '' }}>{{ $category }}</option>
                                        @endforeach
                                    </select>
                                    @error('category')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="payment_method" class="form-label">Payment Method <span class="text-danger">*</span></label>
                                    <select class="form-select @error('payment_method') is-invalid @enderror" 
                                            id="payment_method" name="payment_method" required>
                                        <option value="">Select Payment Method</option>
                                        @foreach($paymentMethods as $method)
                                            <option value="{{ $method }}" {{ old('payment_method') == $method ? 'selected' : '' }}>{{ $method }}</option>
                                        @endforeach
                                    </select>
                                    @error('payment_method')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="priority" class="form-label">Priority <span class="text-danger">*</span></label>
                                    <select class="form-select @error('priority') is-invalid @enderror" 
                                            id="priority" name="priority" required>
                                        <option value="">Select Priority</option>
                                        @foreach($priorities as $priority)
                                            <option value="{{ $priority }}" {{ old('priority') == $priority ? 'selected' : '' }}>{{ $priority }}</option>
                                        @endforeach
                                    </select>
                                    @error('priority')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="vendor" class="form-label">Vendor</label>
                                    <input type="text" class="form-control @error('vendor') is-invalid @enderror" 
                                           id="vendor" name="vendor" value="{{ old('vendor') }}" 
                                           placeholder="Enter vendor name">
                                    @error('vendor')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="receipt_number" class="form-label">Receipt Number</label>
                                    <input type="text" class="form-control @error('receipt_number') is-invalid @enderror" 
                                           id="receipt_number" name="receipt_number" value="{{ old('receipt_number') }}" 
                                           placeholder="Enter receipt number">
                                    @error('receipt_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" 
                                              id="description" name="description" rows="3" 
                                              placeholder="Additional description...">{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="notes" class="form-label">Notes</label>
                                    <textarea class="form-control @error('notes') is-invalid @enderror" 
                                              id="notes" name="notes" rows="3" 
                                              placeholder="Internal notes...">{{ old('notes') }}</textarea>
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
                                    📎 Attachments
                                </h5>
                            </div>
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="attachments" class="form-label">Upload Files</label>
                                    <input type="file" class="form-control @error('attachments.*') is-invalid @enderror" 
                                           id="attachments" name="attachments[]" multiple 
                                           accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.gif,.xlsx,.xls">
                                    <div class="form-text">
                                        Supported formats: PDF, DOC, DOCX, JPG, JPEG, PNG, GIF, XLSX, XLS. Max size: 10MB per file.
                                    </div>
                                    @error('attachments.*')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <!-- File Preview -->
                                <div id="file-preview" class="file-preview-container"></div>
                            </div>
                        </div>

                        <!-- Summary -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title">Bill Summary</h6>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <p class="mb-1"><strong>Amount:</strong> <span id="summary-amount">৳0.00</span></p>
                                            </div>
                                            <div class="col-md-6">
                                                <p class="mb-1"><strong>Category:</strong> <span id="summary-category">Not selected</span></p>
                                            </div>
                                            <div class="col-md-6">
                                                <p class="mb-1"><strong>Priority:</strong> <span id="summary-priority">Not selected</span></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-footer">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('bills.index') }}" class="btn btn-secondary">
                                ← Back to Bills
                            </a>
                            <button type="submit" class="btn btn-primary">
                                💾 Create Bill
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.file-preview-container {
    border: 2px dashed #dee2e6;
    border-radius: 0.375rem;
    padding: 1rem;
    min-height: 100px;
    background-color: #f8f9fa;
}

.file-preview-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.5rem;
    margin-bottom: 0.5rem;
    background-color: white;
    border: 1px solid #dee2e6;
    border-radius: 0.25rem;
}

.file-name {
    display: flex;
    align-items: center;
    flex-grow: 1;
}

.file-name i {
    margin-right: 0.5rem;
    color: #6c757d;
}

.remove-file {
    background: none;
    border: none;
    color: #dc3545;
    cursor: pointer;
    margin-left: 0.5rem;
    padding: 0.25rem;
}

.remove-file:hover {
    color: #c82333;
}

.file-size {
    font-size: 0.875rem;
    color: #6c757d;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const amountInput = document.getElementById('amount');
    const categorySelect = document.getElementById('category');
    const prioritySelect = document.getElementById('priority');
    const attachmentsInput = document.getElementById('attachments');
    
    // Update summary when amount changes
    function updateSummary() {
        const amount = parseFloat(amountInput.value) || 0;
        
        document.getElementById('summary-amount').textContent = '৳' + amount.toFixed(2);
        document.getElementById('summary-category').textContent = document.getElementById('category').options[document.getElementById('category').selectedIndex].text || 'Not selected';
        document.getElementById('summary-priority').textContent = document.getElementById('priority').value || 'Not selected';
    }
    
    amountInput.addEventListener('input', updateSummary);
    categorySelect.addEventListener('change', updateSummary);
    prioritySelect.addEventListener('change', updateSummary);
    
    // File preview functionality
    attachmentsInput.addEventListener('change', function() {
        const preview = document.getElementById('file-preview');
        preview.innerHTML = '';
        
        if (this.files.length === 0) {
            preview.innerHTML = '<p class="text-muted text-center mb-0">No files selected</p>';
            return;
        }
        
        Array.from(this.files).forEach((file, index) => {
            const fileItem = document.createElement('div');
            fileItem.className = 'file-preview-item';
            fileItem.innerHTML = `
                <div class="file-name">
                    📄 ${file.name}
                    <button type="button" class="remove-file" onclick="removeFile(${index})">
                        ❌
                    </button>
                </div>
                <div class="file-size">${(file.size / 1024 / 1024).toFixed(2)} MB</div>
            `;
            preview.appendChild(fileItem);
        });
    });
    
    // Initialize summary
    updateSummary();
    
    // Initialize file preview
    const preview = document.getElementById('file-preview');
    preview.innerHTML = '<p class="text-muted text-center mb-0">No files selected</p>';
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
document.getElementById('billForm').addEventListener('submit', function(e) {
    const amount = document.getElementById('amount').value;
    const purpose = document.getElementById('purpose').value;
    
    if (!purpose.trim()) {
        e.preventDefault();
        alert('Please enter the purpose of this bill.');
        document.getElementById('purpose').focus();
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
@endpush