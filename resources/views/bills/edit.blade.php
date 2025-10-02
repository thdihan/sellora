@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">Edit Bill #{{ $bill->id }}</h3>
                    <span class="badge bg-{{ $bill->status_color }} fs-6">
                        {{ ucfirst($bill->status) }}
                    </span>
                </div>
                
                @if(!$bill->canBeEdited())
                    <div class="alert alert-warning mx-3 mt-3">
                        ⚠️
                        This bill cannot be edited because it has been {{ $bill->status }}.
                    </div>
                @endif
                
                <form action="{{ route('bills.update', $bill) }}" method="POST" enctype="multipart/form-data" id="billForm">
                    @csrf
                    @method('PUT')
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
                                        <span class="input-group-text">$</span>
                                        <input type="number" class="form-control @error('amount') is-invalid @enderror" 
                                               id="amount" name="amount" value="{{ old('amount', $bill->amount) }}" 
                                               step="0.01" min="0" required {{ !$bill->canBeEdited() ? 'readonly' : '' }}>
                                    </div>
                                    @error('amount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="status" class="form-label">Status</label>
                                    <select class="form-select @error('status') is-invalid @enderror" 
                                            id="status" name="status" {{ !$bill->canBeEdited() ? 'disabled' : '' }}>
                                        <option value="pending" {{ old('status', $bill->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="approved" {{ old('status', $bill->status) == 'approved' ? 'selected' : '' }}>Approved</option>
                                        <option value="rejected" {{ old('status', $bill->status) == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                        <option value="paid" {{ old('status', $bill->status) == 'paid' ? 'selected' : '' }}>Paid</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="purpose" class="form-label">Purpose <span class="text-danger">*</span></label>
                                    <textarea class="form-control @error('purpose') is-invalid @enderror" 
                                              id="purpose" name="purpose" rows="4" required 
                                              placeholder="Describe the purpose of this bill..." 
                                              {{ !$bill->canBeEdited() ? 'readonly' : '' }}>{{ old('purpose', $bill->purpose) }}</textarea>
                                    @error('purpose')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Existing Attachments -->
                        @if($bill->files->count() > 0)
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h5 class="text-primary border-bottom pb-2 mb-3">
                                        📎 Current Attachments
                                    </h5>
                                </div>
                                <div class="col-12">
                                    <div class="row">
                                        @foreach($bill->files as $index => $file)
                                            <div class="col-md-6 mb-3">
                                                <div class="card">
                                                    <div class="card-body p-3">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <div>
                                                                <h6 class="card-title mb-1">
                                                                    📄 {{ $file->original_name }}
                                                                </h6>
                                                                <small class="text-muted">{{ $file->formatted_size }}</small>
                                                            </div>
                                                            <div class="btn-group">
                                                                <a href="{{ route('bills.download-attachment', [$bill, $index]) }}" 
                                                                   class="btn btn-sm btn-outline-primary" title="Download">
                                                                    📥
                                                                </a>
                                                                @if($bill->canBeEdited())
                                                                    <button type="button" class="btn btn-sm btn-outline-danger" 
                                                                            title="Remove" onclick="removeAttachment({{ $index }})">
                                                                        🗑️
                                                                    </button>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- New File Attachments -->
                        @if($bill->canBeEdited())
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h5 class="text-primary border-bottom pb-2 mb-3">
                                        ➕ Add New Attachments
                                    </h5>
                                </div>
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label for="attachments" class="form-label">Upload Additional Files</label>
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
                        @endif

                        <!-- Bill Details -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title">Bill Details</h6>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <p class="mb-1"><strong>Amount:</strong> <span id="summary-amount">${{ number_format($bill->amount, 2) }}</span></p>
                                            </div>
                                            <div class="col-md-4">
                                                <p class="mb-1"><strong>Status:</strong> <span id="summary-status">{{ ucfirst($bill->status) }}</span></p>
                                            </div>
                                            <div class="col-md-4">
                                                <p class="mb-1"><strong>Created:</strong> {{ $bill->created_at->format('M d, Y') }}</p>
                                            </div>
                                        </div>
                                        @if($bill->approvals->count() > 0)
                                            <hr>
                                            <h6 class="mb-2">Approval History</h6>
                                            @foreach($bill->approvals as $approval)
                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                    <span>
                                                        <span class="badge bg-{{ $approval->action_badge_color }}">{{ ucfirst($approval->action) }}</span>
                                                        by {{ $approval->actor->name }}
                                                    </span>
                                                    <small class="text-muted">{{ $approval->created_at->format('M d, Y H:i') }}</small>
                                                </div>
                                                @if($approval->notes)
                                                    <p class="text-muted small mb-2">{{ $approval->notes }}</p>
                                                @endif
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-footer">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('bills.index') }}" class="btn btn-secondary">
                                Back to Bills
                            </a>
                            @if($bill->canBeEdited())
                                <button type="submit" class="btn btn-primary">
                                    💾 Update Bill
                                </button>
                            @endif
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
function removeAttachment(index) {
    if (confirm('Are you sure you want to remove this attachment?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/bills/{{ $bill->id }}/attachments/${index}`;
        form.innerHTML = `
            @csrf
            @method('DELETE')
        `;
        document.body.appendChild(form);
        form.submit();
    }
}

@if($bill->canBeEdited())
document.addEventListener('DOMContentLoaded', function() {
    const amountInput = document.getElementById('amount');
    const statusSelect = document.getElementById('status');
    const attachmentsInput = document.getElementById('attachments');
    
    // Update summary when amount changes
    function updateSummary() {
        const amount = parseFloat(amountInput.value) || 0;
        const status = statusSelect.value;
        
        document.getElementById('summary-amount').textContent = '$' + amount.toFixed(2);
        document.getElementById('summary-status').textContent = status.charAt(0).toUpperCase() + status.slice(1);
    }
    
    amountInput.addEventListener('input', updateSummary);
    statusSelect.addEventListener('change', updateSummary);
    
    // File preview functionality
    attachmentsInput.addEventListener('change', function() {
        const preview = document.getElementById('file-preview');
        preview.innerHTML = '';
        
        if (this.files.length === 0) {
            preview.innerHTML = '<p class="text-muted text-center mb-0">No new files selected</p>';
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
    
    // Initialize file preview
    const preview = document.getElementById('file-preview');
    preview.innerHTML = '<p class="text-muted text-center mb-0">No new files selected</p>';
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
@endif
</script>
@endpush