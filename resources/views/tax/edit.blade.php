@extends('layouts.app')

@section('title', 'Edit Tax Code: ' . $taxCode->name)

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Edit Tax Code</h1>
            <p class="mb-0 text-muted">Update tax code information</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('tax.show', $taxCode) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Back to Details
            </a>
            <a href="{{ route('tax.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-list me-1"></i> All Tax Codes
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Tax Code Information</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('tax.update', $taxCode) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Tax Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name', $taxCode->name) }}" 
                                           placeholder="e.g., Value Added Tax" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Full name of the tax type</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="code" class="form-label">Tax Code <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('code') is-invalid @enderror" 
                                           id="code" name="code" value="{{ old('code', $taxCode->code) }}" 
                                           placeholder="e.g., VAT" required maxlength="20">
                                    @error('code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Short code for identification (max 20 characters)</small>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3" 
                                      placeholder="Optional description of this tax code">{{ old('description', $taxCode->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                                       value="1" {{ old('is_active', $taxCode->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    Active
                                </label>
                                <small class="form-text text-muted d-block">Inactive tax codes cannot be used in calculations</small>
                            </div>
                        </div>

                        @if($taxCode->taxRates->count() > 0)
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Note:</strong> This tax code has {{ $taxCode->taxRates->count() }} associated rate(s). 
                                Deactivating this code will affect existing calculations.
                            </div>
                        @endif

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('tax.show', $taxCode) }}" class="btn btn-secondary">
                                <i class="fas fa-times me-1"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Update Tax Code
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Current Details -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-info">Current Details</h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless table-sm">
                        <tr>
                            <td><strong>Code:</strong></td>
                            <td><span class="badge bg-primary">{{ $taxCode->code }}</span></td>
                        </tr>
                        <tr>
                            <td><strong>Name:</strong></td>
                            <td>{{ $taxCode->name }}</td>
                        </tr>
                        <tr>
                            <td><strong>Status:</strong></td>
                            <td>
                                <span class="badge bg-{{ $taxCode->is_active ? 'success' : 'secondary' }}">
                                    {{ $taxCode->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Rates:</strong></td>
                            <td>{{ $taxCode->taxRates->count() }}</td>
                        </tr>
                        <tr>
                            <td><strong>Created:</strong></td>
                            <td>{{ $taxCode->created_at->format('M d, Y') }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Associated Rates -->
            @if($taxCode->taxRates->count() > 0)
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-success">Associated Rates</h6>
                    </div>
                    <div class="card-body">
                        @foreach($taxCode->taxRates->take(5) as $rate)
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div>
                                    <strong>{{ $rate->label }}</strong>
                                    @if($rate->is_default)
                                        <i class="fas fa-star text-warning" title="Default"></i>
                                    @endif
                                    <br>
                                    <small class="text-muted">{{ $rate->country ?: 'Global' }}</small>
                                </div>
                                <span class="badge bg-info">{{ $rate->percent }}%</span>
                            </div>
                        @endforeach
                        
                        @if($taxCode->taxRates->count() > 5)
                            <small class="text-muted">+{{ $taxCode->taxRates->count() - 5 }} more rates</small>
                        @endif
                        
                        <hr>
                        <a href="{{ route('tax.rates.index', ['tax' => $taxCode->id]) }}" class="btn btn-sm btn-outline-success">
                            <i class="fas fa-percentage me-1"></i> Manage Rates
                        </a>
                    </div>
                </div>
            @endif

            <!-- Help Card -->
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-warning">Important Notes</h6>
                </div>
                <div class="card-body">
                    <ul class="small text-muted mb-0">
                        <li>Changing the code will affect all references</li>
                        <li>Deactivating will prevent new calculations</li>
                        <li>Existing rates and rules will remain unchanged</li>
                        <li>Consider creating a new code for major changes</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Validate code format
        $('#code').on('input', function() {
            let value = $(this).val().toUpperCase().replace(/[^A-Z0-9]/g, '');
            $(this).val(value);
        });
        
        // Warn about deactivation
        $('#is_active').on('change', function() {
            if (!$(this).is(':checked') && {{ $taxCode->taxRates->count() }} > 0) {
                if (!confirm('This tax code has associated rates. Deactivating it may affect existing calculations. Continue?')) {
                    $(this).prop('checked', true);
                }
            }
        });
    });
</script>
@endpush