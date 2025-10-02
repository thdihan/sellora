@extends('layouts.app')

@section('title', 'Inventory Dashboard')

@section('content')
<div class="container-fluid">
    <!-- KPI Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-0">Total Products</h6>
                            <h3 class="mb-0">{{ $stats['total_products'] }}</h3>
                        </div>
                        <div class="text-white-50">
                            <span style="font-size: 2rem;">📦</span>
                        </div>
                    </div>
                    <small class="text-white-50">
                        ↑ {{ $stats['active_products'] }} active
                    </small>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-0">Total Stock Value</h6>
                            <h3 class="mb-0">৳{{ number_format($stats['total_stock_value'], 0) }}</h3>
                        </div>
                        <div class="text-white-50">
                            <span style="font-size: 2rem;">💰</span>
                        </div>
                    </div>
                    <small class="text-white-50">
                        Based on purchase price
                    </small>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-0">Low Stock Items</h6>
                            <h3 class="mb-0">{{ $stats['low_stock_count'] }}</h3>
                        </div>
                        <div class="text-white-50">
                            <span style="font-size: 2rem;">⚠️</span>
                        </div>
                    </div>
                    <small class="text-white-50">
                        Need attention
                    </small>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-0">Out of Stock</h6>
                            <h3 class="mb-0">{{ $stats['out_of_stock_count'] }}</h3>
                        </div>
                        <div class="text-white-50">
                            <span style="font-size: 2rem;">❌</span>
                        </div>
                    </div>
                    <small class="text-white-50">
                        Immediate action required
                    </small>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <!-- Stock Overview Chart -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">📊 Stock Overview by Category</h5>
                    <div class="btn-group" role="group">
                        <input type="radio" class="btn-check" name="chartType" id="quantity" autocomplete="off" checked>
                        <label class="btn btn-outline-primary btn-sm" for="quantity">Quantity</label>
                        
                        <input type="radio" class="btn-check" name="chartType" id="value" autocomplete="off">
                        <label class="btn btn-outline-primary btn-sm" for="value">Value</label>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="stockChart" height="300"></canvas>
                </div>
            </div>
        </div>
        
        <!-- Recent Transactions -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">🔄 Recent Transactions</h5>
                    <a href="{{ route('inventory.transactions') }}" class="btn btn-sm btn-outline-primary">
                        View All
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($recentTransactions as $transaction)
                            <div class="list-group-item">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">{{ $transaction->product->name }}</h6>
    
                                        <small class="text-muted">{{ $transaction->created_at->diffForHumans() }}</small>
                                    </div>
                                    <div class="text-end">
                                        @if($transaction->type === 'inbound')
                                            <span class="badge bg-success">+{{ $transaction->quantity }}</span>
                                        @else
                                            <span class="badge bg-danger">-{{ $transaction->quantity }}</span>
                                        @endif
                                        <div class="small text-muted">{{ ucfirst($transaction->type) }}</div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="list-group-item text-center text-muted">
                                <span style="font-size: 2rem;" class="mb-2 d-block">📥</span>
                                <p class="mb-0">No recent transactions</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mt-4">
        <!-- Low Stock Alert -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-warning">⚠️ Low Stock Alert</h5>
                    <a href="{{ route('products.index', ['status' => 'low_stock']) }}" class="btn btn-sm btn-outline-warning">
                        View All
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Product</th>
                                    <th>Current Stock</th>
                                    <th>Min Level</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($lowStockProducts as $product)
                                    <tr>
                                        <td>
                                            <div class="fw-bold">{{ $product->name }}</div>
                                            <small class="text-muted">{{ $product->sku }}</small>
                                        </td>
                                        <td>
                                            <span class="badge bg-warning">{{ $product->total_stock ?? 0 }}</span>
                                        </td>
                                        <td>{{ $product->min_stock_level }}</td>
                                        <td>
                                            <a href="{{ route('products.show', $product) }}" class="btn btn-sm btn-outline-primary">
                                                👁️
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">
                                            <span class="text-success mb-2" style="font-size: 2rem;">✓</span>
                                            <p class="mb-0">All products have sufficient stock</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Expiring Products -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-danger">📅 Expiring Soon</h5>
                    <a href="{{ route('inventory.batches', ['expiring' => 'soon']) }}" class="btn btn-sm btn-outline-danger">
                        View All
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Product</th>
                                    <th>Batch</th>
                                    <th>Expiry Date</th>
                                    <th>Quantity</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($expiringBatches as $batch)
                                    <tr>
                                        <td>
                                            <div class="fw-bold">{{ $batch->product->name }}</div>
                                            <small class="text-muted">{{ $batch->product->sku }}</small>
                                        </td>
                                        <td><code>{{ $batch->batch_number }}</code></td>
                                        <td>
                                            <span class="badge {{ $batch->is_expired ? 'bg-danger' : ($batch->is_expiring_soon ? 'bg-warning' : 'bg-success') }}">
                                                {{ $batch->expiry_date ? $batch->expiry_date->format('M d, Y') : '-' }}
                                            </span>
                                        </td>
                                        <td>{{ $batch->stockBalances->sum('quantity') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">
                                            <span class="text-success mb-2" style="font-size: 2rem;">✓</span>
                                            <p class="mb-0">No products expiring soon</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Product Inventory Listing -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">📦 Product Inventory</h5>
                    <div class="d-flex gap-2">
                        <a href="{{ route('products.create') }}" class="btn btn-primary btn-sm">
                            ➕ Add Product
                        </a>
                        <a href="{{ route('products.index') }}" class="btn btn-outline-secondary btn-sm">
                            📋 Manage Products
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Search and Filter -->
                    <form method="GET" action="{{ route('inventory.index') }}" class="mb-4">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <input type="text" name="search" class="form-control" 
                                       placeholder="Search products..." value="{{ request('search') }}">
                            </div>

                            <div class="col-md-2">
                                <select name="stock_status" class="form-select">
                                    <option value="">All Stock</option>
                                    <option value="in_stock" {{ request('stock_status') == 'in_stock' ? 'selected' : '' }}>In Stock</option>
                                    <option value="low_stock" {{ request('stock_status') == 'low_stock' ? 'selected' : '' }}>Low Stock</option>
                                    <option value="out_of_stock" {{ request('stock_status') == 'out_of_stock' ? 'selected' : '' }}>Out of Stock</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-outline-primary">
                                        🔍 Filter
                                    </button>
                                    <a href="{{ route('inventory.index') }}" class="btn btn-outline-secondary">
                                        ✖️ Clear
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- Products Table -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>SKU</th>
                                    <th>Product Name</th>
                                    <th>Category</th>
                                    <th>Purchase Price</th>
                                    <th>Selling Price</th>
                                    <th>Current Stock</th>
                                    <th>Stock Value</th>
                                    
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($stockBalances as $stockBalance)
                                    <tr>
                                        <td><code>{{ $stockBalance->product->sku }}</code></td>
                                        <td>
                                            <div class="fw-bold">{{ $stockBalance->product->name }}</div>
                                            @if($stockBalance->product->barcode)
                                                <small class="text-muted">Barcode: {{ $stockBalance->product->barcode }}</small>
                                            @endif
                                        </td>
                                        <td>{{ $stockBalance->product->category->name ?? '-' }}</td>
                                        <td>৳{{ number_format($stockBalance->product->purchase_price, 2) }}</td>
                                        <td>৳{{ number_format($stockBalance->product->selling_price, 2) }}</td>
                                        <td>
                                            @php
                                                $stockQty = $stockBalance->qty_on_hand;
                                                $minLevel = $stockBalance->product->min_stock_level ?? 0;
                                            @endphp
                                            <span class="badge {{ $stockQty <= 0 ? 'bg-danger' : ($stockQty <= $minLevel ? 'bg-warning' : 'bg-success') }}">
                                                {{ $stockQty }}
                                            </span>
                                            @if($stockQty <= $minLevel && $stockQty > 0)
                                                <small class="text-warning d-block">Low Stock</small>
                                            @elseif($stockQty <= 0)
                                                <small class="text-danger d-block">Out of Stock</small>
                                            @endif
                                        </td>
                                        <td>৳{{ number_format($stockBalance->qty_on_hand * $stockBalance->product->purchase_price, 2) }}</td>

                                        <td>
                                            @if($stockBalance->product->status)
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-secondary">Inactive</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('products.show', $stockBalance->product) }}" 
                                                   class="btn btn-sm btn-outline-info" title="View Product">
                                                    👁️
                                                </a>
                                                <a href="{{ route('products.edit', $stockBalance->product) }}" 
                                                   class="btn btn-sm btn-outline-primary" title="Edit Product">
                                                    ✏️
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="fas fa-box-open fa-3x mb-3"></i>
                                                <p>No inventory records found.</p>
                                                <a href="{{ route('products.create') }}" class="btn btn-primary">
                                                    Add First Product
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    @if($stockBalances->hasPages())
                        <div class="d-flex justify-content-center mt-4">
                            {{ $stockBalances->appends(request()->query())->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">⚡ Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-3 col-md-6 mb-3">
                            <a href="{{ route('products.create') }}" class="btn btn-primary w-100">
                                ➕ Add New Product
                            </a>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <a href="{{ route('inventory.adjustments') }}" class="btn btn-warning w-100">
                                ⚖️ Stock Adjustment
                            </a>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <a href="{{ route('inventory.transfers') }}" class="btn btn-info w-100">
                                🔄 Stock Transfer
                            </a>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <a href="{{ route('inventory.transactions') }}" class="btn btn-secondary w-100">
                                📜 View Transactions
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Stock Chart
const ctx = document.getElementById('stockChart').getContext('2d');
const chartData = @json($chartData);

let stockChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: chartData.categories,
        datasets: [{
            label: 'Stock Quantity',
            data: chartData.quantities,
            backgroundColor: 'rgba(54, 162, 235, 0.8)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true
            }
        },
        plugins: {
            legend: {
                display: false
            }
        }
    }
});

// Chart type toggle
document.querySelectorAll('input[name="chartType"]').forEach(radio => {
    radio.addEventListener('change', function() {
        if (this.id === 'quantity') {
            stockChart.data.datasets[0].label = 'Stock Quantity';
            stockChart.data.datasets[0].data = chartData.quantities;
            stockChart.data.datasets[0].backgroundColor = 'rgba(54, 162, 235, 0.8)';
            stockChart.data.datasets[0].borderColor = 'rgba(54, 162, 235, 1)';
        } else {
            stockChart.data.datasets[0].label = 'Stock Value (৳)';
            stockChart.data.datasets[0].data = chartData.values;
            stockChart.data.datasets[0].backgroundColor = 'rgba(75, 192, 192, 0.8)';
            stockChart.data.datasets[0].borderColor = 'rgba(75, 192, 192, 1)';
        }
        stockChart.update();
    });
});
</script>
@endpush
@endsection