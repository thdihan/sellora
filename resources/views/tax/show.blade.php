@extends('layouts.app')

@section('title', 'Tax Code: ' . $taxCode->name)

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                Tax Code: {{ $taxCode->name }}
                <span class="badge bg-{{ $taxCode->is_active ? 'success' : 'secondary' }} ms-2">
                    {{ $taxCode->is_active ? 'Active' : 'Inactive' }}
                </span>
            </h1>
            <p class="mb-0 text-muted">{{ $taxCode->description ?: 'No description provided' }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('tax.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Back to Tax Codes
            </a>
            <a href="{{ route('tax.edit', $taxCode) }}" class="btn btn-primary">
                <i class="fas fa-edit me-1"></i> Edit
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <!-- Tax Code Details -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Tax Code Details</h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
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
                            <td><strong>Created:</strong></td>
                            <td>{{ $taxCode->created_at->format('M d, Y') }}</td>
                        </tr>
                        <tr>
                            <td><strong>Updated:</strong></td>
                            <td>{{ $taxCode->updated_at->format('M d, Y') }}</td>
                        </tr>
                    </table>
                    
                    @if($taxCode->description)
                        <hr>
                        <h6 class="text-primary">Description</h6>
                        <p class="text-muted">{{ $taxCode->description }}</p>
                    @endif
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">Statistics</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="border-right">
                                <h4 class="text-primary">{{ $taxCode->taxRates->count() }}</h4>
                                <small class="text-muted">Tax Rates</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <h4 class="text-success">{{ $taxCode->taxRates->sum(function($rate) { return $rate->taxRules->count(); }) }}</h4>
                            <small class="text-muted">Total Rules</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tax Rates -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Tax Rates</h6>
                    <a href="{{ route('tax.rates.create', ['tax' => $taxCode->id]) }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-plus me-1"></i> Add Rate
                    </a>
                </div>
                <div class="card-body">
                    @if($taxCode->taxRates->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Label</th>
                                        <th>Rate</th>
                                        <th>Region</th>
                                        <th>Effective Period</th>
                                        <th>Default</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($taxCode->taxRates as $rate)
                                        <tr>
                                            <td>
                                                <strong>{{ $rate->label }}</strong>
                                                @if($rate->is_default)
                                                    <i class="fas fa-star text-warning ms-1" title="Default Rate"></i>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-info">{{ $rate->percent }}%</span>
                                            </td>
                                            <td>
                                                {{ $rate->country ?: 'Global' }}
                                                @if($rate->region)
                                                    <br><small class="text-muted">{{ $rate->region }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                <small>
                                                    From: {{ $rate->effective_from ? $rate->effective_from->format('M d, Y') : 'N/A' }}<br>
                                                    To: {{ $rate->effective_to ? $rate->effective_to->format('M d, Y') : 'Ongoing' }}
                                                </small>
                                            </td>
                                            <td>
                                                @if($rate->is_default)
                                                    <span class="badge bg-warning">Yes</span>
                                                @else
                                                    <span class="text-muted">No</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($rate->is_active)
                                                    <span class="badge bg-success">Active</span>
                                                @else
                                                    <span class="badge bg-secondary">Inactive</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('rates.show', $rate) }}" class="btn btn-sm btn-outline-info" title="View">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('rates.edit', $rate) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <form action="{{ route('rates.destroy', $rate) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-percentage fa-3x text-gray-300 mb-3"></i>
                            <h5 class="text-muted">No Tax Rates</h5>
                            <p class="text-muted">Add tax rates to define percentage calculations for this tax code.</p>
                            <a href="{{ route('tax.rates.create', ['tax' => $taxCode->id]) }}" class="btn btn-primary">
                                <i class="fas fa-plus me-1"></i> Add First Rate
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Tax Rules Summary -->
            <div class="card shadow">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Tax Rules Summary</h6>
                    <a href="{{ route('tax.rules.index', ['tax' => $taxCode->id]) }}" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-cogs me-1"></i> Manage Rules
                    </a>
                </div>
                <div class="card-body">
                    @php
                        $allRules = $taxCode->taxRates->flatMap(function($rate) { return $rate->taxRules; });
                        $rulesByType = $allRules->groupBy('applies_to');
                    @endphp
                    
                    @if($allRules->count() > 0)
                        <div class="row">
                            @foreach($rulesByType as $type => $rules)
                                <div class="col-md-6 mb-3">
                                    <div class="border rounded p-3">
                                        <h6 class="text-primary">{{ ucfirst(str_replace('_', ' ', $type)) }}</h6>
                                        <p class="text-muted mb-2">{{ $rules->count() }} rule(s)</p>
                                        <div class="small">
                                            @foreach($rules->take(3) as $rule)
                                                <div class="mb-1">
                                                    <span class="badge bg-light text-dark">{{ ucfirst($rule->price_mode) }}</span>
                                                    @if($rule->reverse_charge)
                                                        <span class="badge bg-warning">Reverse Charge</span>
                                                    @endif
                                                    @if($rule->zero_rated)
                                                        <span class="badge bg-info">Zero Rated</span>
                                                    @endif
                                                    @if($rule->exempt)
                                                        <span class="badge bg-secondary">Exempt</span>
                                                    @endif
                                                </div>
                                            @endforeach
                                            @if($rules->count() > 3)
                                                <small class="text-muted">+{{ $rules->count() - 3 }} more</small>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-3">
                            <i class="fas fa-cogs fa-2x text-gray-300 mb-2"></i>
                            <p class="text-muted">No tax rules configured yet.</p>
                            <a href="{{ route('tax.rules.create', ['tax' => $taxCode->id]) }}" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-plus me-1"></i> Add Rules
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .border-right {
        border-right: 1px solid #e3e6f0;
    }
    .card {
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15) !important;
    }
</style>
@endpush