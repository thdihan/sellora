@extends('layouts.app')

@section('title', 'Product Batches')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">📦 Product Batches</h4>
                    <a href="{{ route('inventory.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Inventory
                    </a>
                </div>
                
                <div class="card-body">
                    <!-- Search and Filters -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <form method="GET" action="{{ route('inventory.batches') }}" class="d-flex">
                                <input type="text" name="search" class="form-control" 
                                       placeholder="Search by batch number or product..." 
                                       value="{{ request('search') }}">
                                <button type="submit" class="btn btn-primary ms-2">
                                    <i class="fas fa-search"></i>
                                </button>
                            </form>
                        </div>
                        <div class="col-md-6">
                            <div class="btn-group" role="group">
                                <a href="{{ route('inventory.batches') }}" 
                                   class="btn {{ !request()->hasAny(['expired', 'near_expiry']) ? 'btn-primary' : 'btn-outline-primary' }}">
                                    All Batches
                                </a>
                                <a href="{{ route('inventory.batches', ['near_expiry' => 'true']) }}" 
                                   class="btn {{ request('near_expiry') ? 'btn-warning' : 'btn-outline-warning' }}">
                                    Near Expiry
                                </a>
                                <a href="{{ route('inventory.batches', ['expired' => 'true']) }}" 
                                   class="btn {{ request('expired') ? 'btn-danger' : 'btn-outline-danger' }}">
                                    Expired
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Batches Table -->
                    @if($batches->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Batch Number</th>
                                        <th>Product</th>
                                        <th>Manufacturing Date</th>
                                        <th>Expiry Date</th>
                                        <th>Status</th>
                                        <th>Stock Balance</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($batches as $batch)
                                        <tr>
                                            <td>
                                                <strong>{{ $batch->batch_no }}</strong>
                                            </td>
                                            <td>
                                                <div>
                                                    <strong>{{ $batch->product->name }}</strong><br>
                                                    <small class="text-muted">SKU: {{ $batch->product->sku }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                {{ $batch->mfg_date ? $batch->mfg_date->format('M d, Y') : 'N/A' }}
                                            </td>
                                            <td>
                                                @if($batch->exp_date)
                                                    <span class="@if($batch->exp_date < now()) text-danger @elseif($batch->exp_date <= now()->addDays(30)) text-warning @endif">
                                                        {{ $batch->exp_date->format('M d, Y') }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">No expiry</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($batch->exp_date && $batch->exp_date < now())
                                                    <span class="badge bg-danger">Expired</span>
                                                @elseif($batch->exp_date && $batch->exp_date <= now()->addDays(30))
                                                    <span class="badge bg-warning">Expiring Soon</span>
                                                @else
                                                    <span class="badge bg-success">Active</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($batch->stockBalances->count() > 0)
                                                    @foreach($batch->stockBalances as $balance)
                                                        <div class="small">
                                                            <strong>Stock:</strong> 
                                                            {{ number_format($balance->qty_on_hand) }}
                                                        </div>
                                                    @endforeach
                                                @else
                                                    <span class="text-muted">No stock</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('products.show', $batch->product_id) }}" 
                                                   class="btn btn-sm btn-outline-primary" title="View Product">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center">
                            {{ $batches->appends(request()->query())->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No batches found</h5>
                            <p class="text-muted">No product batches match your current filters.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection