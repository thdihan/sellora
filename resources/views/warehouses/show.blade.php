@extends('layouts.app')

@section('title', 'Warehouse Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Warehouse Details: {{ $warehouse->name }}</h4>
                    <div>
                        <a href="{{ route('warehouses.edit', $warehouse) }}" class="btn btn-primary me-2">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <a href="{{ route('warehouses.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Warehouses
                        </a>
                    </div>
                </div>
                
                <div class="card-body">
                    <div class="row">
                        <!-- Warehouse Information -->
                        <div class="col-lg-8">
                            <!-- Basic Information -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="mb-0"><i class="fas fa-info-circle"></i> Basic Information</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <table class="table table-borderless">
                                                <tr>
                                                    <td class="fw-bold">Warehouse Name:</td>
                                                    <td>{{ $warehouse->name }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-bold">Code:</td>
                                                    <td><span class="badge bg-secondary">{{ $warehouse->code }}</span></td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-bold">Status:</td>
                                                    <td>
                                                        @if($warehouse->status)
                                                            <span class="badge bg-success">Active</span>
                                                        @else
                                                            <span class="badge bg-danger">Inactive</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-bold">Address:</td>
                                                    <td>{{ $warehouse->address ?: 'No address provided' }}</td>
                                                </tr>
                                            </table>
                                        </div>
                                        <div class="col-md-6">
                                            <table class="table table-borderless">
                                                <tr>
                                                    <td class="fw-bold">Phone:</td>
                                                    <td>
                                                        @if($warehouse->phone)
                                                            <i class="fas fa-phone"></i> {{ $warehouse->phone }}
                                                        @else
                                                            No phone provided
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-bold">Email:</td>
                                                    <td>
                                                        @if($warehouse->email)
                                                            <i class="fas fa-envelope"></i> {{ $warehouse->email }}
                                                        @else
                                                            No email provided
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-bold">Created:</td>
                                                    <td>{{ $warehouse->created_at->format('M d, Y') }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-bold">Last Updated:</td>
                                                    <td>{{ $warehouse->updated_at->format('M d, Y') }}</td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Recent Stock Transactions -->
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0"><i class="fas fa-exchange-alt"></i> Recent Stock Transactions</h5>
                                </div>
                                <div class="card-body">
                                    @if($recentTransactions->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>Date</th>
                                                        <th>Product</th>
                                                        <th>Type</th>
                                                        <th>Quantity</th>
                                                        <th>Note</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($recentTransactions as $transaction)
                                                        <tr>
                                                            <td>{{ $transaction->created_at->format('M d, Y') }}</td>
                                                            <td>
                                                                @if($transaction->product)
                                                                    <strong>{{ $transaction->product->name }}</strong><br>
                                                                    <small class="text-muted">SKU: {{ $transaction->product->sku }}</small>
                                                                @else
                                                                    <span class="text-muted">Product not found</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                @php
                                                                    $typeColors = [
                                                                        'purchase_in' => 'success',
                                                                        'sale_out' => 'danger',
                                                                        'transfer_in' => 'info',
                                                                        'transfer_out' => 'warning',
                                                                        'adjustment_in' => 'primary',
                                                                        'adjustment_out' => 'secondary',
                                                                        'opening' => 'dark'
                                                                    ];
                                                                    $color = $typeColors[$transaction->type] ?? 'secondary';
                                                                @endphp
                                                                <span class="badge bg-{{ $color }}">{{ ucfirst(str_replace('_', ' ', $transaction->type)) }}</span>
                                                            </td>
                                                            <td>
                                                                @if(in_array($transaction->type, ['purchase_in', 'transfer_in', 'adjustment_in', 'opening']))
                                                                    <span class="text-success">+{{ $transaction->qty }}</span>
                                                                @else
                                                                    <span class="text-danger">-{{ $transaction->qty }}</span>
                                                                @endif
                                                            </td>
                                                            <td>{{ $transaction->note ?: '-' }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="text-center py-4">
                                            <i class="fas fa-exchange-alt fa-3x text-muted mb-3"></i>
                                            <h6 class="text-muted">No recent transactions</h6>
                                            <p class="text-muted">Stock transactions will appear here once inventory activities begin.</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Statistics Sidebar -->
                        <div class="col-lg-4">
                            <!-- Stock Statistics -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="mb-0"><i class="fas fa-chart-bar"></i> Stock Statistics</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row text-center">
                                        <div class="col-12 mb-3">
                                            <div class="bg-primary text-white rounded p-3">
                                                <h3 class="mb-0">{{ $totalProducts }}</h3>
                                                <small>Unique Products</small>
                                            </div>
                                        </div>
                                        <div class="col-12 mb-3">
                                            <div class="bg-success text-white rounded p-3">
                                                <h3 class="mb-0">{{ number_format($totalStock) }}</h3>
                                                <small>Total Stock Units</small>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="bg-info text-white rounded p-3">
                                                <h3 class="mb-0">{{ $warehouse->stockBalances->count() }}</h3>
                                                <small>Stock Balance Records</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Quick Actions -->
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0"><i class="fas fa-tools"></i> Quick Actions</h5>
                                </div>
                                <div class="card-body">
                                    <div class="d-grid gap-2">
                                        <a href="{{ route('inventory.index') }}" class="btn btn-outline-primary">
                                            <i class="fas fa-boxes"></i> View Inventory
                                        </a>
                                        <a href="{{ route('warehouses.edit', $warehouse) }}" class="btn btn-outline-warning">
                                            <i class="fas fa-edit"></i> Edit Warehouse
                                        </a>
                                        @if($warehouse->status)
                                            <button class="btn btn-outline-secondary" onclick="toggleStatus(false)">
                                                <i class="fas fa-pause"></i> Deactivate
                                            </button>
                                        @else
                                            <button class="btn btn-outline-success" onclick="toggleStatus(true)">
                                                <i class="fas fa-play"></i> Activate
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Status Toggle Form -->
<form id="statusForm" method="POST" action="{{ route('warehouses.update', $warehouse) }}" style="display: none;">
    @csrf
    @method('PUT')
    <input type="hidden" name="status" id="statusInput">
    <input type="hidden" name="name" value="{{ $warehouse->name }}">
    <input type="hidden" name="code" value="{{ $warehouse->code }}">
    <input type="hidden" name="address" value="{{ $warehouse->address }}">
    <input type="hidden" name="phone" value="{{ $warehouse->phone }}">
    <input type="hidden" name="email" value="{{ $warehouse->email }}">
</form>
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

.card {
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
    border: none;
}

.card-header {
    background-color: #f8f9fc;
    border-bottom: 1px solid #e3e6f0;
}

.table-borderless td {
    border: none;
    padding: 0.5rem 0;
}

.bg-primary, .bg-success, .bg-info {
    border-radius: 0.5rem !important;
}
</style>
@endpush

@push('scripts')
<script>
function toggleStatus(newStatus) {
    if (confirm('Are you sure you want to ' + (newStatus ? 'activate' : 'deactivate') + ' this warehouse?')) {
        document.getElementById('statusInput').value = newStatus ? '1' : '0';
        document.getElementById('statusForm').submit();
    }
}
</script>
@endpush