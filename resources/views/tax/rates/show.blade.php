@extends('layouts.app')

@section('title', 'Tax Rate Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">Tax Rate Details</h3>
                    <div class="btn-group">
                        <a href="{{ route('tax.rates.index', ['tax' => $taxRate->tax_code_id]) }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Tax Rates
                        </a>
                        <a href="{{ route('rates.edit', $taxRate) }}" class="btn btn-primary">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <form action="{{ route('rates.destroy', $taxRate) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this tax rate?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        </form>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Tax Code:</label>
                                <p class="mb-0">{{ $taxRate->taxCode->code }} - {{ $taxRate->taxCode->name }}</p>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Label:</label>
                                <p class="mb-0">{{ $taxRate->label }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Tax Rate:</label>
                                <p class="mb-0">
                                    <span class="badge bg-primary fs-6">{{ number_format($taxRate->percent, 2) }}%</span>
                                </p>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Country:</label>
                                <p class="mb-0">{{ $taxRate->country ?: 'Not specified' }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Region/State:</label>
                                <p class="mb-0">{{ $taxRate->region ?: 'Not specified' }}</p>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Status:</label>
                                <p class="mb-0">
                                    @if($taxRate->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                    
                                    @if($taxRate->is_default)
                                        <span class="badge bg-info ms-1">Default</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Effective From:</label>
                                <p class="mb-0">{{ $taxRate->effective_from ? $taxRate->effective_from->format('M d, Y') : 'Not specified' }}</p>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Effective To:</label>
                                <p class="mb-0">{{ $taxRate->effective_to ? $taxRate->effective_to->format('M d, Y') : 'Ongoing' }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Created:</label>
                                <p class="mb-0">{{ $taxRate->created_at->format('M d, Y \\a\\t g:i A') }}</p>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Last Updated:</label>
                                <p class="mb-0">{{ $taxRate->updated_at->format('M d, Y \\a\\t g:i A') }}</p>
                            </div>
                        </div>
                    </div>

                    @if($taxRate->effective_from && $taxRate->effective_to)
                        <div class="row">
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i>
                                    <strong>Validity Period:</strong> This tax rate is valid from 
                                    {{ $taxRate->effective_from->format('M d, Y') }} to 
                                    {{ $taxRate->effective_to->format('M d, Y') }}
                                    @if($taxRate->effective_to->isPast())
                                        <span class="badge bg-warning ms-2">Expired</span>
                                    @elseif($taxRate->effective_from->isFuture())
                                        <span class="badge bg-info ms-2">Future</span>
                                    @else
                                        <span class="badge bg-success ms-2">Current</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection