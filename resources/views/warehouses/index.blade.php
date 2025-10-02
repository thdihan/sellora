@extends('layouts.app')

@section('title', 'Warehouses Management')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Main Warehouse</h1>
            <p class="text-muted">View your main warehouse information</p>
        </div>
    </div>



    <!-- Warehouses Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Warehouses List</h5>
        </div>
        <div class="card-body">
            @if($warehouses->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Name</th>
                                <th>Address</th>
                                <th>Contact</th>
                                <th>Status</th>
                                <th>Stock Items</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($warehouses as $warehouse)
                                <tr>
                                    <td>
                                        <span class="badge bg-secondary">{{ $warehouse->code }}</span>
                                    </td>
                                    <td>
                                        <strong>{{ $warehouse->name }}</strong>
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $warehouse->address ?: 'No address' }}</small>
                                    </td>
                                    <td>
                                        @if($warehouse->phone || $warehouse->email)
                                            <div>
                                                @if($warehouse->phone)
                                                    <small><i class="fas fa-phone"></i> {{ $warehouse->phone }}</small><br>
                                                @endif
                                                @if($warehouse->email)
                                                    <small><i class="fas fa-envelope"></i> {{ $warehouse->email }}</small>
                                                @endif
                                            </div>
                                        @else
                                            <small class="text-muted">No contact info</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($warehouse->status)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-danger">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $warehouse->stockBalances->count() ?? 0 }}</span>
                                    </td>
                                    <td>
                                        <a href="{{ route('warehouses.show', $warehouse) }}" 
                                           class="btn btn-sm btn-outline-primary" title="View Details">
                                            <i class="fas fa-eye"></i> View Details
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($warehouses->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        <div class="pagination-wrapper">
                            <p class="text-muted mb-2">
                                Showing {{ $warehouses->firstItem() }} to {{ $warehouses->lastItem() }} of {{ $warehouses->total() }} results
                            </p>
                            {{ $warehouses->links('vendor.pagination.custom-3d') }}
                        </div>
                    </div>
                @endif
            @else
                <div class="text-center py-4">
                    <i class="fas fa-building fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No warehouses found</h5>
                    <p class="text-muted">Start by adding your first warehouse location.</p>
                    <a href="{{ route('warehouses.create') }}" class="btn btn-primary">
                        Add Warehouse
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.table th {
    border-top: none;
    font-weight: 600;
    color: #5a5c69;
}

.badge {
    font-size: 0.75em;
}

.btn-group .btn {
    border-radius: 0.25rem;
    margin-right: 2px;
}

.card {
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
    border: none;
}

.card-header {
    background-color: #f8f9fc;
    border-bottom: 1px solid #e3e6f0;
}
</style>
@endpush