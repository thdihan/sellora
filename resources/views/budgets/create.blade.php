@extends('layouts.app')

@section('title', 'Create Budget')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Create New Budget</h1>
            <p class="mb-0 text-muted">Set up a new budget to track and manage your expenses</p>
        </div>
        <a href="{{ route('budgets.index') }}" class="btn btn-secondary">
            Back to Budgets
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Budget Form -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Budget Information</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('budgets.store') }}" method="POST" id="budgetForm">
                        @csrf
                        
                        <!-- Basic Information -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-primary mb-3">Basic Information</h6>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name" class="form-label">Budget Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name') }}" 
                                           placeholder="e.g., Marketing Q1 2024" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="currency" class="form-label">Currency <span class="text-danger">*</span></label>
                                    <select class="form-control @error('currency') is-invalid @enderror" 
                                            id="currency" name="currency" required>
                                        <option value="BDT" {{ old('currency', 'BDT') == 'BDT' ? 'selected' : '' }}>BDT - Bangladeshi Taka (৳)</option>
                                        <option value="USD" {{ old('currency') == 'USD' ? 'selected' : '' }}>USD - US Dollar</option>
                                        <option value="EUR" {{ old('currency') == 'EUR' ? 'selected' : '' }}>EUR - Euro</option>
                                        <option value="GBP" {{ old('currency') == 'GBP' ? 'selected' : '' }}>GBP - British Pound</option>
                                        <option value="CAD" {{ old('currency') == 'CAD' ? 'selected' : '' }}>CAD - Canadian Dollar</option>
                                        <option value="AUD" {{ old('currency') == 'AUD' ? 'selected' : '' }}>AUD - Australian Dollar</option>
                                    </select>
                                    @error('currency')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" 
                                              id="description" name="description" rows="3" 
                                              placeholder="Brief description of this budget's purpose">{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Period and Amount -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-primary mb-3">Period & Amount</h6>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="period_type" class="form-label">Period Type <span class="text-danger">*</span></label>
                                    <select class="form-control @error('period_type') is-invalid @enderror" 
                                            id="period_type" name="period_type" required onchange="updateDateFields()">
                                        <option value="">Select Period</option>
                                        <option value="monthly" {{ old('period_type') == 'monthly' ? 'selected' : '' }}>Monthly</option>
                                        <option value="quarterly" {{ old('period_type') == 'quarterly' ? 'selected' : '' }}>Quarterly</option>
                                        <option value="half_yearly" {{ old('period_type') == 'half_yearly' ? 'selected' : '' }}>Half Yearly</option>
                                        <option value="yearly" {{ old('period_type') == 'yearly' ? 'selected' : '' }}>Yearly</option>
                                        <option value="custom" {{ old('period_type') == 'custom' ? 'selected' : '' }}>Custom</option>
                                    </select>
                                    @error('period_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="start_date" class="form-label">Start Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('start_date') is-invalid @enderror" 
                                           id="start_date" name="start_date" value="{{ old('start_date') }}" 
                                           required onchange="updateEndDate()">
                                    @error('start_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="end_date" class="form-label">End Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('end_date') is-invalid @enderror" 
                                           id="end_date" name="end_date" value="{{ old('end_date') }}" required>
                                    @error('end_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="total_amount" class="form-label">Total Budget Amount <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">$</span>
                                        </div>
                                        <input type="number" class="form-control @error('total_amount') is-invalid @enderror" 
                                               id="total_amount" name="total_amount" value="{{ old('total_amount') }}" 
                                               step="0.01" min="0" placeholder="0.00" required>
                                        @error('total_amount')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="notification_threshold" class="form-label">Notification Threshold (%) <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="number" class="form-control @error('notification_threshold') is-invalid @enderror" 
                                               id="notification_threshold" name="notification_threshold" 
                                               value="{{ old('notification_threshold', 80) }}" 
                                               min="0" max="100" placeholder="80" required>
                                        <div class="input-group-append">
                                            <span class="input-group-text">%</span>
                                        </div>
                                        @error('notification_threshold')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <small class="form-text text-muted">Get notified when spending reaches this percentage</small>
                                </div>
                            </div>
                        </div>

                        <!-- Categories -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-primary mb-3">Budget Categories</h6>
                                <div class="form-group">
                                    <label class="form-label">Select Categories (Optional)</label>
                                    <div class="row">
                                        @php
                                            $categories = [
                                                'marketing' => 'Marketing & Advertising',
                                                'office_supplies' => 'Office Supplies',
                                                'travel' => 'Travel & Transportation',
                                                'meals' => 'Meals & Entertainment',
                                                'software' => 'Software & Subscriptions',
                                                'equipment' => 'Equipment & Hardware',
                                                'training' => 'Training & Development',
                                                'utilities' => 'Utilities',
                                                'rent' => 'Rent & Facilities',
                                                'insurance' => 'Insurance',
                                                'legal' => 'Legal & Professional',
                                                'other' => 'Other Expenses'
                                            ];
                                        @endphp
                                        @foreach($categories as $key => $label)
                                            <div class="col-md-4 col-sm-6">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" 
                                                           name="categories[]" value="{{ $key }}" 
                                                           id="category_{{ $key }}"
                                                           {{ in_array($key, old('categories', [])) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="category_{{ $key }}">
                                                        {{ $label }}
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Advanced Settings -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-primary mb-3">Advanced Settings</h6>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="auto_approve_limit" class="form-label">Auto-Approve Limit</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">$</span>
                                        </div>
                                        <input type="number" class="form-control @error('auto_approve_limit') is-invalid @enderror" 
                                               id="auto_approve_limit" name="auto_approve_limit" 
                                               value="{{ old('auto_approve_limit') }}" 
                                               step="0.01" min="0" placeholder="0.00">
                                        @error('auto_approve_limit')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <small class="form-text text-muted">Expenses below this amount will be auto-approved</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="form-check mt-4">
                                        <input class="form-check-input" type="checkbox" 
                                               name="is_recurring" value="1" id="is_recurring"
                                               {{ old('is_recurring') ? 'checked' : '' }}
                                               onchange="toggleRecurringOptions()">
                                        <label class="form-check-label" for="is_recurring">
                                            <strong>Recurring Budget</strong>
                                        </label>
                                    </div>
                                    <small class="form-text text-muted">Automatically create new budgets for future periods</small>
                                </div>
                            </div>
                            <div class="col-md-6" id="recurring_frequency_group" style="display: none;">
                                <div class="form-group">
                                    <label for="recurring_frequency" class="form-label">Recurring Frequency</label>
                                    <select class="form-control @error('recurring_frequency') is-invalid @enderror" 
                                            id="recurring_frequency" name="recurring_frequency">
                                        <option value="">Select Frequency</option>
                                        <option value="monthly" {{ old('recurring_frequency') == 'monthly' ? 'selected' : '' }}>Monthly</option>
                                        <option value="quarterly" {{ old('recurring_frequency') == 'quarterly' ? 'selected' : '' }}>Quarterly</option>
                                        <option value="half_yearly" {{ old('recurring_frequency') == 'half_yearly' ? 'selected' : '' }}>Half Yearly</option>
                                        <option value="yearly" {{ old('recurring_frequency') == 'yearly' ? 'selected' : '' }}>Yearly</option>
                                    </select>
                                    @error('recurring_frequency')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Notes -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="notes" class="form-label">Notes</label>
                                    <textarea class="form-control @error('notes') is-invalid @enderror" 
                                              id="notes" name="notes" rows="3" 
                                              placeholder="Additional notes or comments about this budget">{{ old('notes') }}</textarea>
                                    @error('notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('budgets.index') }}" class="btn btn-secondary">
                                        × Cancel
                                    </a>
                                    <div>
                                        <button type="submit" name="action" value="draft" class="btn btn-outline-primary">
                                            💾 Save as Draft
                                        </button>
                                        <button type="submit" name="action" value="submit" class="btn btn-primary">
                                            📤 Create & Submit for Approval
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Budget Preview -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Budget Preview</h6>
                </div>
                <div class="card-body">
                    <div class="budget-preview">
                        <div class="mb-3">
                            <strong>Budget Name:</strong>
                            <div id="preview_name" class="text-muted">Enter budget name</div>
                        </div>
                        <div class="mb-3">
                            <strong>Period:</strong>
                            <div id="preview_period" class="text-muted">Select period type</div>
                        </div>
                        <div class="mb-3">
                            <strong>Duration:</strong>
                            <div id="preview_duration" class="text-muted">Select dates</div>
                        </div>
                        <div class="mb-3">
                            <strong>Total Amount:</strong>
                            <div id="preview_amount" class="text-muted">$0.00</div>
                        </div>
                        <div class="mb-3">
                            <strong>Notification Threshold:</strong>
                            <div id="preview_threshold" class="text-muted">80%</div>
                        </div>
                        <div class="mb-3">
                            <strong>Categories:</strong>
                            <div id="preview_categories" class="text-muted">No categories selected</div>
                        </div>
                        <div class="mb-3">
                            <strong>Recurring:</strong>
                            <div id="preview_recurring" class="text-muted">No</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Help & Tips -->
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-info">Tips & Guidelines</h6>
                </div>
                <div class="card-body">
                    <div class="small">
                        <div class="mb-2">
                            <span class="text-warning">💡</span>
                            <strong>Period Types:</strong> Choose the timeframe that matches your planning cycle.
                        </div>
                        <div class="mb-2">
                            <span class="text-info">🔔</span>
                            <strong>Notifications:</strong> Set threshold to get alerts before overspending.
                        </div>
                        <div class="mb-2">
                            <span class="text-success">🏷️</span>
                            <strong>Categories:</strong> Select relevant categories to track spending patterns.
                        </div>
                        <div class="mb-2">
                            <span class="text-primary">🔄</span>
                            <strong>Recurring:</strong> Enable for budgets that repeat regularly.
                        </div>
                        <div class="mb-0">
                            ✓
                            <strong>Auto-Approve:</strong> Set limit for automatic expense approvals.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Update preview in real-time
document.addEventListener('DOMContentLoaded', function() {
    // Initialize preview
    updatePreview();
    
    // Add event listeners
    document.getElementById('name').addEventListener('input', updatePreview);
    document.getElementById('period_type').addEventListener('change', updatePreview);
    document.getElementById('start_date').addEventListener('change', updatePreview);
    document.getElementById('end_date').addEventListener('change', updatePreview);
    document.getElementById('total_amount').addEventListener('input', updatePreview);
    document.getElementById('notification_threshold').addEventListener('input', updatePreview);
    document.getElementById('is_recurring').addEventListener('change', updatePreview);
    document.getElementById('recurring_frequency').addEventListener('change', updatePreview);
    
    // Category checkboxes
    document.querySelectorAll('input[name="categories[]"]').forEach(checkbox => {
        checkbox.addEventListener('change', updatePreview);
    });
    
    // Initialize recurring options visibility
    toggleRecurringOptions();
});

function updatePreview() {
    // Name
    const name = document.getElementById('name').value || 'Enter budget name';
    document.getElementById('preview_name').textContent = name;
    
    // Period
    const periodType = document.getElementById('period_type').value;
    const periodText = periodType ? periodType.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase()) : 'Select period type';
    document.getElementById('preview_period').textContent = periodText;
    
    // Duration
    const startDate = document.getElementById('start_date').value;
    const endDate = document.getElementById('end_date').value;
    let durationText = 'Select dates';
    if (startDate && endDate) {
        const start = new Date(startDate).toLocaleDateString();
        const end = new Date(endDate).toLocaleDateString();
        durationText = `${start} to ${end}`;
    }
    document.getElementById('preview_duration').textContent = durationText;
    
    // Amount
    const amount = document.getElementById('total_amount').value || '0';
    document.getElementById('preview_amount').textContent = `$${parseFloat(amount).toLocaleString('en-US', {minimumFractionDigits: 2})}`;
    
    // Threshold
    const threshold = document.getElementById('notification_threshold').value || '80';
    document.getElementById('preview_threshold').textContent = `${threshold}%`;
    
    // Categories
    const selectedCategories = Array.from(document.querySelectorAll('input[name="categories[]"]:checked'))
        .map(cb => cb.nextElementSibling.textContent.trim());
    const categoriesText = selectedCategories.length > 0 ? selectedCategories.join(', ') : 'No categories selected';
    document.getElementById('preview_categories').textContent = categoriesText;
    
    // Recurring
    const isRecurring = document.getElementById('is_recurring').checked;
    let recurringText = 'No';
    if (isRecurring) {
        const frequency = document.getElementById('recurring_frequency').value;
        recurringText = frequency ? `Yes (${frequency.replace('_', ' ')})` : 'Yes';
    }
    document.getElementById('preview_recurring').textContent = recurringText;
}

function updateDateFields() {
    const periodType = document.getElementById('period_type').value;
    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');
    
    if (periodType && periodType !== 'custom' && startDateInput.value) {
        updateEndDate();
    }
}

function updateEndDate() {
    const periodType = document.getElementById('period_type').value;
    const startDate = document.getElementById('start_date').value;
    const endDateInput = document.getElementById('end_date');
    
    if (!startDate || periodType === 'custom') return;
    
    const start = new Date(startDate);
    let end = new Date(start);
    
    switch (periodType) {
        case 'monthly':
            end.setMonth(end.getMonth() + 1);
            end.setDate(end.getDate() - 1);
            break;
        case 'quarterly':
            end.setMonth(end.getMonth() + 3);
            end.setDate(end.getDate() - 1);
            break;
        case 'half_yearly':
            end.setMonth(end.getMonth() + 6);
            end.setDate(end.getDate() - 1);
            break;
        case 'yearly':
            end.setFullYear(end.getFullYear() + 1);
            end.setDate(end.getDate() - 1);
            break;
    }
    
    endDateInput.value = end.toISOString().split('T')[0];
    updatePreview();
}

function toggleRecurringOptions() {
    const isRecurring = document.getElementById('is_recurring').checked;
    const frequencyGroup = document.getElementById('recurring_frequency_group');
    
    if (isRecurring) {
        frequencyGroup.style.display = 'block';
    } else {
        frequencyGroup.style.display = 'none';
        document.getElementById('recurring_frequency').value = '';
    }
    
    updatePreview();
}

// Form validation
document.getElementById('budgetForm').addEventListener('submit', function(e) {
    const totalAmount = parseFloat(document.getElementById('total_amount').value);
    const autoApproveLimit = parseFloat(document.getElementById('auto_approve_limit').value);
    
    if (autoApproveLimit && autoApproveLimit > totalAmount) {
        e.preventDefault();
        alert('Auto-approve limit cannot be greater than the total budget amount.');
        return false;
    }
    
    const startDate = new Date(document.getElementById('start_date').value);
    const endDate = new Date(document.getElementById('end_date').value);
    
    if (startDate >= endDate) {
        e.preventDefault();
        alert('End date must be after start date.');
        return false;
    }
});
</script>
@endpush
@endsection