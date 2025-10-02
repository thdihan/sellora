@extends('layouts.app')

@section('title', 'Edit Tax Rule')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">Edit Tax Rule</h3>
                    <div>
                        <a href="{{ route('rules.show', $taxRule) }}" class="btn btn-info">
                            <i class="fas fa-eye"></i> View
                        </a>
                        <a href="{{ route('tax.rules.index', ['tax' => request()->route('tax')]) }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Tax Rules
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('tax.rules.update', $taxRule) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name" class="form-label">Rule Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name', $taxRule->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="priority" class="form-label">Priority <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('priority') is-invalid @enderror" 
                                           id="priority" name="priority" value="{{ old('priority', $taxRule->priority) }}" min="1" required>
                                    @error('priority')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Lower numbers have higher priority</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="tax_rate_id" class="form-label">Tax Rate <span class="text-danger">*</span></label>
                                    <select class="form-control @error('tax_rate_id') is-invalid @enderror" 
                                            id="tax_rate_id" name="tax_rate_id" required>
                                        <option value="">Select Tax Rate</option>
                                        @foreach($taxRates as $taxRate)
                                            <option value="{{ $taxRate->id }}" 
                                                {{ old('tax_rate_id', $taxRule->tax_rate_id) == $taxRate->id ? 'selected' : '' }}>
                                                {{ $taxRate->name }} ({{ $taxRate->rate }}%)
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('tax_rate_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="is_active" class="form-label">Status</label>
                                    <select class="form-control @error('is_active') is-invalid @enderror" 
                                            id="is_active" name="is_active">
                                        <option value="1" {{ old('is_active', $taxRule->is_active) == 1 ? 'selected' : '' }}>Active</option>
                                        <option value="0" {{ old('is_active', $taxRule->is_active) == 0 ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                    @error('is_active')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3">{{ old('description', $taxRule->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <h5>Rule Conditions</h5>
                            <p class="text-muted">Define when this tax rule should be applied</p>
                            
                            <div id="conditions-container">
                                @php
                                    $existingConditions = json_decode($taxRule->conditions, true) ?? [];
                                @endphp
                                @foreach($existingConditions as $index => $condition)
                                    <div class="condition-row border p-3 mb-3 rounded" data-index="{{ $index }}">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <label class="form-label">Field</label>
                                                <select class="form-control" name="conditions[{{ $index }}][field]">
                                                    <option value="product_category" {{ ($condition['field'] ?? '') == 'product_category' ? 'selected' : '' }}>Product Category</option>
                                                    <option value="product_id" {{ ($condition['field'] ?? '') == 'product_id' ? 'selected' : '' }}>Specific Product</option>
                                                    <option value="customer_type" {{ ($condition['field'] ?? '') == 'customer_type' ? 'selected' : '' }}>Customer Type</option>
                                                    <option value="order_total" {{ ($condition['field'] ?? '') == 'order_total' ? 'selected' : '' }}>Order Total</option>
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">Operator</label>
                                                <select class="form-control" name="conditions[{{ $index }}][operator]">
                                                    <option value="equals" {{ ($condition['operator'] ?? '') == 'equals' ? 'selected' : '' }}>Equals</option>
                                                    <option value="not_equals" {{ ($condition['operator'] ?? '') == 'not_equals' ? 'selected' : '' }}>Not Equals</option>
                                                    <option value="greater_than" {{ ($condition['operator'] ?? '') == 'greater_than' ? 'selected' : '' }}>Greater Than</option>
                                                    <option value="less_than" {{ ($condition['operator'] ?? '') == 'less_than' ? 'selected' : '' }}>Less Than</option>
                                                    <option value="contains" {{ ($condition['operator'] ?? '') == 'contains' ? 'selected' : '' }}>Contains</option>
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">Value</label>
                                                <input type="text" class="form-control" name="conditions[{{ $index }}][value]" 
                                                       value="{{ $condition['value'] ?? '' }}" placeholder="Enter value">
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label">&nbsp;</label>
                                                <button type="button" class="btn btn-danger btn-sm d-block remove-condition">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            
                            <button type="button" class="btn btn-outline-primary btn-sm" id="add-condition">
                                <i class="fas fa-plus"></i> Add Condition
                            </button>
                        </div>
                        
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Tax Rule
                            </button>
                            <a href="{{ route('rules.show', $taxRule) }}" class="btn btn-secondary">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let conditionIndex = {{ count($existingConditions) }};
    
    document.getElementById('add-condition').addEventListener('click', function() {
        const container = document.getElementById('conditions-container');
        const conditionHtml = `
            <div class="condition-row border p-3 mb-3 rounded" data-index="${conditionIndex}">
                <div class="row">
                    <div class="col-md-3">
                        <label class="form-label">Field</label>
                        <select class="form-control" name="conditions[${conditionIndex}][field]">
                            <option value="product_category">Product Category</option>
                            <option value="product_id">Specific Product</option>
                            <option value="customer_type">Customer Type</option>
                            <option value="order_total">Order Total</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Operator</label>
                        <select class="form-control" name="conditions[${conditionIndex}][operator]">
                            <option value="equals">Equals</option>
                            <option value="not_equals">Not Equals</option>
                            <option value="greater_than">Greater Than</option>
                            <option value="less_than">Less Than</option>
                            <option value="contains">Contains</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Value</label>
                        <input type="text" class="form-control" name="conditions[${conditionIndex}][value]" placeholder="Enter value">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <button type="button" class="btn btn-danger btn-sm d-block remove-condition">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', conditionHtml);
        conditionIndex++;
    });
    
    document.addEventListener('click', function(e) {
        if (e.target.closest('.remove-condition')) {
            e.target.closest('.condition-row').remove();
        }
    });
});
</script>
@endsection