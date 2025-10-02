@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Edit Expense</h4>
                    <a href="{{ route('expenses.show', $expense) }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Details
                    </a>
                </div>
                <div class="card-body">
                    <form action="{{ route('expenses.update', $expense) }}" method="POST" enctype="multipart/form-data" id="expenseForm">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-8">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                                   id="title" name="title" value="{{ old('title', $expense->title) }}" required>
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
                                                <option value="travel" {{ old('category', $expense->category) === 'travel' ? 'selected' : '' }}>Travel</option>
                                                <option value="meals" {{ old('category', $expense->category) === 'meals' ? 'selected' : '' }}>Meals</option>
                                                <option value="office" {{ old('category', $expense->category) === 'office' ? 'selected' : '' }}>Office Supplies</option>
                                                <option value="marketing" {{ old('category', $expense->category) === 'marketing' ? 'selected' : '' }}>Marketing</option>
                                                <option value="training" {{ old('category', $expense->category) === 'training' ? 'selected' : '' }}>Training</option>
                                                <option value="utilities" {{ old('category', $expense->category) === 'utilities' ? 'selected' : '' }}>Utilities</option>
                                                <option value="maintenance" {{ old('category', $expense->category) === 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                                                <option value="other" {{ old('category', $expense->category) === 'other' ? 'selected' : '' }}>Other</option>
                                            </select>
                                            @error('category')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" 
                                              id="description" name="description" rows="3" 
                                              placeholder="Provide details about this expense...">{{ old('description', $expense->description) }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="amount" class="form-label">Amount <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control @error('amount') is-invalid @enderror" 
                                                   id="amount" name="amount" step="0.01" min="0" 
                                                   value="{{ old('amount', $expense->amount) }}" required>
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
                                                <option value="BDT" {{ old('currency', $expense->currency) === 'BDT' ? 'selected' : '' }}>BDT (৳)</option>
                                                <option value="USD" {{ old('currency', $expense->currency) === 'USD' ? 'selected' : '' }}>USD</option>
                                                <option value="EUR" {{ old('currency', $expense->currency) === 'EUR' ? 'selected' : '' }}>EUR</option>
                                                <option value="GBP" {{ old('currency', $expense->currency) === 'GBP' ? 'selected' : '' }}>GBP</option>
                                                <option value="CAD" {{ old('currency', $expense->currency) === 'CAD' ? 'selected' : '' }}>CAD</option>
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
                                                   id="tax_amount" name="tax_amount" step="0.01" min="0" 
                                                   value="{{ old('tax_amount', $expense->tax_amount) }}">
                                            @error('tax_amount')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="expense_date" class="form-label">Expense Date <span class="text-danger">*</span></label>
                                            <input type="date" class="form-control @error('expense_date') is-invalid @enderror" 
                                                   id="expense_date" name="expense_date" 
                                                   value="{{ old('expense_date', $expense->expense_date->format('Y-m-d')) }}" required>
                                            @error('expense_date')
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
                                                <option value="cash" {{ old('payment_method', $expense->payment_method) === 'cash' ? 'selected' : '' }}>Cash</option>
                                                <option value="credit_card" {{ old('payment_method', $expense->payment_method) === 'credit_card' ? 'selected' : '' }}>Credit Card</option>
                                                <option value="debit_card" {{ old('payment_method', $expense->payment_method) === 'debit_card' ? 'selected' : '' }}>Debit Card</option>
                                                <option value="bank_transfer" {{ old('payment_method', $expense->payment_method) === 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                                <option value="check" {{ old('payment_method', $expense->payment_method) === 'check' ? 'selected' : '' }}>Check</option>
                                                <option value="other" {{ old('payment_method', $expense->payment_method) === 'other' ? 'selected' : '' }}>Other</option>
                                            </select>
                                            @error('payment_method')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="reference_number" class="form-label">Reference Number</label>
                                            <input type="text" class="form-control @error('reference_number') is-invalid @enderror" 
                                                   id="reference_number" name="reference_number" 
                                                   value="{{ old('reference_number', $expense->reference_number) }}" 
                                                   placeholder="Receipt/Invoice number">
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
                                                <option value="low" {{ old('priority', $expense->priority) === 'low' ? 'selected' : '' }}>Low</option>
                                                <option value="medium" {{ old('priority', $expense->priority) === 'medium' ? 'selected' : '' }}>Medium</option>
                                                <option value="high" {{ old('priority', $expense->priority) === 'high' ? 'selected' : '' }}>High</option>
                                                <option value="urgent" {{ old('priority', $expense->priority) === 'urgent' ? 'selected' : '' }}>Urgent</option>
                                            </select>
                                            @error('priority')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="is_reimbursable" 
                                               name="is_reimbursable" value="1" 
                                               {{ old('is_reimbursable', $expense->is_reimbursable) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_reimbursable">
                                            This expense is reimbursable
                                        </label>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="attachments" class="form-label">Add New Attachments</label>
                                    <input type="file" class="form-control @error('attachments.*') is-invalid @enderror" 
                                           id="attachments" name="attachments[]" multiple 
                                           accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx">
                                    <div class="form-text">Supported formats: PDF, JPG, PNG, DOC, DOCX, XLS, XLSX. Max 5MB per file.</div>
                                    @error('attachments.*')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                @if($expense->attachments && count($expense->attachments) > 0)
                                <div class="mb-3">
                                    <label class="form-label">Current Attachments</label>
                                    <div class="row">
                                        @foreach($expense->attachments as $index => $attachment)
                                        <div class="col-md-4 mb-2">
                                            <div class="card">
                                                <div class="card-body p-2">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <small class="text-muted">{{ $attachment['name'] }}</small>
                                                        <div class="btn-group btn-group-sm">
                                                            <a href="{{ route('expenses.download-attachment', [$expense, $index]) }}" 
                                                               class="btn btn-outline-primary btn-sm">
                                                                <i class="fas fa-download"></i>
                                                            </a>
                                                            <button type="button" class="btn btn-outline-danger btn-sm" 
                                                                    onclick="removeAttachment({{ $index }})">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                                @endif
                            </div>

                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="mb-0">Expense Summary</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-2">
                                            <strong>Amount:</strong>
                                            <span id="summaryAmount" class="float-end">$0.00</span>
                                        </div>
                                        <div class="mb-2">
                                            <strong>Tax:</strong>
                                            <span id="summaryTax" class="float-end">$0.00</span>
                                        </div>
                                        <hr>
                                        <div class="mb-0">
                                            <strong>Total:</strong>
                                            <span id="summaryTotal" class="float-end h6 text-primary">$0.00</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="card mt-3">
                                    <div class="card-header">
                                        <h6 class="mb-0">Current Status</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-2">
                                            <strong>Status:</strong><br>
                                            <span class="badge bg-{{ $expense->status_color }}">
                                                {{ ucfirst($expense->status) }}
                                            </span>
                                        </div>
                                        <div class="mb-2">
                                            <strong>Submitted:</strong><br>
                                            <small class="text-muted">{{ $expense->created_at->format('M d, Y H:i') }}</small>
                                        </div>
                                        @if($expense->updated_at != $expense->created_at)
                                        <div class="mb-0">
                                            <strong>Last Updated:</strong><br>
                                            <small class="text-muted">{{ $expense->updated_at->format('M d, Y H:i') }}</small>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('expenses.show', $expense) }}" class="btn btn-secondary">
                                        <i class="fas fa-times"></i> Cancel
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Update Expense
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function updateSummary() {
    const amount = parseFloat(document.getElementById('amount').value) || 0;
    const taxAmount = parseFloat(document.getElementById('tax_amount').value) || 0;
    const currency = document.getElementById('currency').value || 'USD';
    
    const currencySymbol = {
        'USD': '$',
        'EUR': '€',
        'GBP': '£',
        'CAD': 'C$'
    }[currency] || '$';
    
    document.getElementById('summaryAmount').textContent = currencySymbol + amount.toFixed(2);
    document.getElementById('summaryTax').textContent = currencySymbol + taxAmount.toFixed(2);
    document.getElementById('summaryTotal').textContent = currencySymbol + (amount + taxAmount).toFixed(2);
}

function removeAttachment(index) {
    if (confirm('Are you sure you want to remove this attachment?')) {
        fetch(`{{ route('expenses.remove-attachment', [$expense, '__INDEX__']) }}`.replace('__INDEX__', index), {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error removing attachment');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error removing attachment');
        });
    }
}

// Update summary on page load and when values change
document.addEventListener('DOMContentLoaded', function() {
    updateSummary();
    
    document.getElementById('amount').addEventListener('input', updateSummary);
    document.getElementById('tax_amount').addEventListener('input', updateSummary);
    document.getElementById('currency').addEventListener('change', updateSummary);
});
</script>
@endsection