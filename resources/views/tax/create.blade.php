@extends('layouts.app')

@section('title', 'Create Tax Code')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Create Tax Code</h1>
            <p class="mb-0 text-muted">Add a new tax code to your system</p>
        </div>
        <a href="{{ route('tax.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back to Tax Codes
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Tax Code Information</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('tax.store') }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Tax Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name') }}" 
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
                                           id="code" name="code" value="{{ old('code') }}" 
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
                                      placeholder="Optional description of this tax code">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                                       value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    Active
                                </label>
                                <small class="form-text text-muted d-block">Inactive tax codes cannot be used in calculations</small>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('tax.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-1"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Create Tax Code
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Help Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-info">Help & Tips</h6>
                </div>
                <div class="card-body">
                    <h6 class="text-primary">Common Tax Codes:</h6>
                    <ul class="list-unstyled">
                        <li><strong>VAT</strong> - Value Added Tax</li>
                        <li><strong>GST</strong> - Goods and Services Tax</li>
                        <li><strong>PST</strong> - Provincial Sales Tax</li>
                        <li><strong>HST</strong> - Harmonized Sales Tax</li>
                        <li><strong>SALES</strong> - Sales Tax</li>
                        <li><strong>EXCISE</strong> - Excise Tax</li>
                    </ul>
                    
                    <hr>
                    
                    <h6 class="text-primary">Best Practices:</h6>
                    <ul class="small text-muted">
                        <li>Use short, descriptive codes</li>
                        <li>Keep codes consistent across your system</li>
                        <li>Add clear descriptions for complex tax types</li>
                        <li>Set up rates after creating the code</li>
                    </ul>
                </div>
            </div>

            <!-- Next Steps Card -->
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">Next Steps</h6>
                </div>
                <div class="card-body">
                    <p class="small text-muted">After creating this tax code, you can:</p>
                    <ol class="small">
                        <li>Add tax rates with percentages</li>
                        <li>Configure calculation rules</li>
                        <li>Set regional variations</li>
                        <li>Test with sample calculations</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Auto-generate code from name
        $('#name').on('input', function() {
            if ($('#code').val() === '') {
                let code = $(this).val()
                    .toUpperCase()
                    .replace(/[^A-Z0-9]/g, '')
                    .substring(0, 20);
                $('#code').val(code);
            }
        });
        
        // Validate code format
        $('#code').on('input', function() {
            let value = $(this).val().toUpperCase().replace(/[^A-Z0-9]/g, '');
            $(this).val(value);
        });
    });
</script>
@endpush