@extends('layouts.app')

@section('title', 'Tax Rates')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">Tax Rates</h3>
                    <a href="{{ route('tax.rates.create', ['tax' => request()->route('tax')]) }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add Tax Rate
                    </a>
                </div>
                <div class="card-body">
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

                    @if($taxRates->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Tax Code</th>
                                        <th>Label</th>
                                        <th>Rate (%)</th>
                                        <th>Country</th>
                                        <th>Region</th>
                                        <th>Effective From</th>
                                        <th>Effective To</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($taxRates as $rate)
                                        <tr>
                                            <td>
                                                <span class="badge bg-info">{{ $rate->taxCode->code ?? 'N/A' }}</span>
                                            </td>
                                            <td>{{ $rate->label }}</td>
                                            <td>
                                                <span class="fw-bold text-primary">{{ number_format($rate->percent, 2) }}%</span>
                                            </td>
                                            <td>{{ $rate->country ?? '-' }}</td>
                                            <td>{{ $rate->region ?? '-' }}</td>
                                            <td>{{ $rate->effective_from ? $rate->effective_from->format('M d, Y') : '-' }}</td>
                                            <td>{{ $rate->effective_to ? $rate->effective_to->format('M d, Y') : 'Ongoing' }}</td>
                                            <td>
                                                @if($rate->is_active)
                                                    <span class="badge bg-success">Active</span>
                                                @else
                                                    <span class="badge bg-secondary">Inactive</span>
                                                @endif
                                                @if($rate->is_default)
                                                    <span class="badge bg-warning text-dark">Default</span>
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
                                                    <form action="{{ route('rates.destroy', $rate) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this tax rate?')">
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

                        <div class="d-flex justify-content-center mt-4">
                            {{ $taxRates->links() }}
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-percentage fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No Tax Rates Found</h5>
                            <p class="text-muted">Create your first tax rate to get started.</p>
                            <a href="{{ route('tax.rates.create', ['tax' => request()->route('tax')]) }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Add Tax Rate
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection