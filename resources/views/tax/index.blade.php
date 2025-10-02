@extends('layouts.app')

@section('title', 'VAT/TAX Settings')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">VAT/TAX Settings</h1>
            <p class="mb-0 text-muted">Manage tax codes, rates, and calculation rules</p>
        </div>
        <div class="d-flex gap-2">
            @if($taxCodes->count() > 0)
                <a href="{{ route('tax.rates.index', ['tax' => $taxCodes->first()->id]) }}" class="btn btn-outline-primary">
                    <i class="fas fa-percentage me-1"></i> Manage Rates
                </a>
                <a href="{{ route('tax.rules.index', $taxCodes->first()) }}" class="btn btn-outline-secondary">
                    Tax Rules
                </a>
            @else
                <span class="btn btn-outline-primary disabled">
                    <i class="fas fa-percentage me-1"></i> Manage Rates
                </span>
                <span class="btn btn-outline-secondary disabled">
                    Tax Rules
                </span>
            @endif
            <a href="{{ route('tax.create') }}" class="btn btn-primary">
                Add Tax Code
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Tax Codes Overview -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Tax Codes
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $taxCodes->total() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-tags fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Active Rates
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $taxCodes->sum(function($code) { return $code->taxRates->count(); }) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-percentage fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Tax Rules
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ \App\Models\TaxRule::count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-cogs fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Default Rate
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                @php
                                    $defaultRate = \App\Models\TaxRate::where('is_default', true)->first();
                                @endphp
                                {{ $defaultRate ? $defaultRate->percent . '%' : 'Not Set' }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-star fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tax Codes Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Tax Codes</h6>
        </div>
        <div class="card-body">
            @if($taxCodes->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered" id="taxCodesTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Active Rates</th>
                                <th>Default Rate</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($taxCodes as $taxCode)
                                <tr>
                                    <td>
                                        <span class="badge bg-primary">{{ $taxCode->code }}</span>
                                    </td>
                                    <td>
                                        <strong>{{ $taxCode->name }}</strong>
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $taxCode->description ?: 'No description' }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $taxCode->taxRates->count() }} rates</span>
                                        @if($taxCode->taxRates->count() > 0)
                                            <div class="mt-1">
                                                @foreach($taxCode->taxRates->take(3) as $rate)
                                                    <small class="text-muted d-block">
                                                        {{ $rate->label }}: {{ $rate->percent }}%
                                                        @if($rate->is_default)
                                                            <i class="fas fa-star text-warning" title="Default"></i>
                                                        @endif
                                                    </small>
                                                @endforeach
                                                @if($taxCode->taxRates->count() > 3)
                                                    <small class="text-muted">+{{ $taxCode->taxRates->count() - 3 }} more</small>
                                                @endif
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $defaultRate = $taxCode->taxRates->where('is_default', true)->first();
                                        @endphp
                                        @if($defaultRate)
                                            <span class="badge bg-success">{{ $defaultRate->percent }}%</span>
                                        @else
                                            <span class="text-muted">None</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($taxCode->is_active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-secondary">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('tax.show', $taxCode) }}" class="btn btn-sm btn-outline-info" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('tax.edit', $taxCode) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                                Edit
                                            </a>
                                            <a href="{{ route('tax.rates.create', ['tax' => $taxCode->id]) }}" class="btn btn-sm btn-outline-success" title="Add Rate">
                                                Add
                                            </a>
                                            <form action="{{ route('tax.destroy', $taxCode) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this tax code?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                    Delete
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center">
                    @if($taxCodes->hasPages())
                        {{ $taxCodes->links('vendor.pagination.custom-3d') }}
                    @endif
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-tags fa-3x text-gray-300 mb-3"></i>
                    <h5 class="text-muted">No Tax Codes Found</h5>
                    <p class="text-muted">Get started by creating your first tax code.</p>
                    <a href="{{ route('tax.create') }}" class="btn btn-primary">
                        Create Tax Code
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Quick Setup Guide -->
    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Quick Setup Guide</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="text-center">
                        <div class="mb-3">
                            <i class="fas fa-tags fa-3x text-primary"></i>
                        </div>
                        <h5>1. Create Tax Codes</h5>
                        <p class="text-muted">Define tax types like VAT, GST, Sales Tax, etc.</p>
                        <a href="{{ route('tax.create') }}" class="btn btn-outline-primary btn-sm">Add Tax Code</a>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="text-center">
                        <div class="mb-3">
                            <i class="fas fa-percentage fa-3x text-success"></i>
                        </div>
                        <h5>2. Set Tax Rates</h5>
                        <p class="text-muted">Configure percentage rates for different regions or periods.</p>
                        @if($taxCodes->count() > 0)
                            <a href="{{ route('tax.rates.index', ['tax' => $taxCodes->first()->id]) }}" class="btn btn-outline-success btn-sm">Manage Rates</a>
                        @else
                            <span class="btn btn-outline-success btn-sm disabled">Manage Rates</span>
                        @endif
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="text-center">
                        <div class="mb-3">
                            <i class="fas fa-cogs fa-3x text-info"></i>
                        </div>
                        <h5>3. Configure Rules</h5>
                        <p class="text-muted">Set up calculation rules and application logic.</p>
                        <a href="{{ $taxCodes->isNotEmpty() ? route('tax.rules.index', ['tax' => $taxCodes->first()->id]) : '#' }}" class="btn btn-outline-info btn-sm" {{ $taxCodes->isEmpty() ? 'disabled' : '' }}>Setup Rules</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .border-left-primary {
        border-left: 0.25rem solid #4e73df !important;
    }
    .border-left-success {
        border-left: 0.25rem solid #1cc88a !important;
    }
    .border-left-info {
        border-left: 0.25rem solid #36b9cc !important;
    }
    .border-left-warning {
        border-left: 0.25rem solid #f6c23e !important;
    }
    .card {
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15) !important;
    }
</style>
@endpush

@push('scripts')
<script>
    $(document).ready(function() {
        $('#taxCodesTable').DataTable({
            "pageLength": 10,
            "ordering": true,
            "searching": true,
            "columnDefs": [
                { "orderable": false, "targets": [6] } // Actions column
            ]
        });
    });
</script>
@endpush