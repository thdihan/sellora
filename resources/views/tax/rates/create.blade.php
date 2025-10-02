@extends('layouts.app')

@section('title', 'Create Tax Rate')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">Create Tax Rate</h3>
                    <a href="{{ route('tax.rates.index', ['tax' => request()->route('tax')]) }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Tax Rates
                    </a>
                </div>
                <div class="card-body">
                    <form action="{{ route('tax.rates.store', ['tax' => request()->route('tax')]) }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="tax_code_id" class="form-label">Tax Code <span class="text-danger">*</span></label>
                                    <select class="form-select @error('tax_code_id') is-invalid @enderror" id="tax_code_id" name="tax_code_id" required>
                                        <option value="">Select Tax Code</option>
                                        @foreach($taxCodes as $taxCode)
                                            <option value="{{ $taxCode->id }}" {{ old('tax_code_id') == $taxCode->id ? 'selected' : '' }}>
                                                {{ $taxCode->code }} - {{ $taxCode->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('tax_code_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="label" class="form-label">Label <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('label') is-invalid @enderror" 
                                           id="label" name="label" value="{{ old('label') }}" required 
                                           placeholder="e.g., Standard Rate, Reduced Rate">
                                    @error('label')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="percent" class="form-label">Tax Rate (%) <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('percent') is-invalid @enderror" 
                                           id="percent" name="percent" value="{{ old('percent') }}" required 
                                           min="0" max="100" step="0.01" placeholder="e.g., 10.00">
                                    @error('percent')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="country" class="form-label">Country Code</label>
                                    <input type="text" class="form-control @error('country') is-invalid @enderror" 
                                           id="country" name="country" value="{{ old('country') }}" 
                                           maxlength="2" placeholder="e.g., US, CA, GB">
                                    @error('country')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">2-letter country code (optional)</div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="region" class="form-label">Region/State</label>
                                    <input type="text" class="form-control @error('region') is-invalid @enderror" 
                                           id="region" name="region" value="{{ old('region') }}" 
                                           placeholder="e.g., California, Ontario">
                                    @error('region')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="effective_from" class="form-label">Effective From <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('effective_from') is-invalid @enderror" 
                                           id="effective_from" name="effective_from" value="{{ old('effective_from') }}" required>
                                    @error('effective_from')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="effective_to" class="form-label">Effective To</label>
                                    <input type="date" class="form-control @error('effective_to') is-invalid @enderror" 
                                           id="effective_to" name="effective_to" value="{{ old('effective_to') }}">
                                    @error('effective_to')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Leave empty for ongoing rate</div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="is_default" name="is_default" value="1" {{ old('is_default') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_default">
                                            Set as Default Rate
                                        </label>
                                    </div>
                                    <div class="form-text">Default rates are used when no specific rate is found</div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">
                                            Active
                                        </label>
                                    </div>
                                    <div class="form-text">Only active rates can be used in calculations</div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-end gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Create Tax Rate
                                    </button>
                                    <a href="{{ route('tax.rates.index', ['tax' => request()->route('tax')]) }}" class="btn btn-secondary">
                                        Cancel
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection