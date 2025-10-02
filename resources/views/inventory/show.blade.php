@extends('layouts.app')

@section('title', 'Inventory Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Inventory Details: {{ $product->name }}</h4>
                    <div>
                        <a href="{{ route('products.show', $product) }}" class="btn btn-info me-2">
                            <i class="fas fa-eye"></i> View Product
                        </a>
                        <a href="{{ route('inventory.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Inventory
                        </a>
                    </div>
                </div>
                
                <div class="card-body">
                    <div class="row">
                        <!-- Product Information -->
                        <div class="col-lg-4">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="mb-0"><i class="fas fa-info-circle"></i> Product Information</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td class="fw-bold">Product Name:</td>
                                            <td>{{ $product->name }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">SKU:</td>
                                            <td><code>{{ $product->sku }}</code></td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Barcode:</td>
                                            <td>{{ $product->barcode ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Category:</td>
                                            <td>{{ $product->category->name ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Brand:</td>
                                            <td>{{ $product->brand->name ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Unit:</td>
                                            <td>{{ $product->unit->name ?? '-' }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            <!-- Stock Summary -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="mb-0"><i class="fas fa-chart-bar"></i> Stock Summary</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row text-center">
                                        <div class="col-4">
                                            <div class="border-end">
                                                <h4 class="text-primary mb-0">{{ $totalStock }}</h4>
                                                <small class="text-muted">Total Stock</small>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="border-end">
                                                <h4 class="text-warning mb-0">{{ $totalReserved }}</h4>
                                                <small class="text-muted">Reserved</small>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <h4 class="text-success mb-0">{{ $availableStock }}</h4>
                                            <small class="text-muted">Available</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Stock Balance -->
                        <div class="col-lg-8">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="mb-0"><i class="fas fa-boxes"></i> Stock Balance</h5>
                                </div>
                                <div class="card-body">
                                    @if($stockBalances->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Batch</th>
                                                        <th>On Hand</th>
                                                        <th>Reserved</th>
                                                        <th>Available</th>
                                                        <th>Last Updated</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($stockBalances as $balance)
                                                        <tr>
                                                            <td>{{ $balance->batch->batch_no ?? '-' }}</td>
                                                            <td>
                                                                <span class="badge bg-primary">{{ $balance->qty_on_hand }}</span>
                                                            </td>
                                                            <td>
                                                                <span class="badge bg-warning">{{ $balance->qty_reserved }}</span>
                                                            </td>
                                                            <td>
                                                                <span class="badge bg-success">{{ $balance->qty_on_hand - $balance->qty_reserved }}</span>
                                                            </td>
                                                            <td>{{ $balance->updated_at->format('M d, Y H:i') }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="text-center py-4">
                                            <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                                            <p class="text-muted">No stock balances found for this product.</p>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Recent Transactions -->
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0"><i class="fas fa-history"></i> Recent Transactions</h5>
                                    <a href="{{ route('inventory.transactions', ['product_id' => $product->id]) }}" class="btn btn-sm btn-outline-primary">
                                        View All Transactions
                                    </a>
                                </div>
                                <div class="card-body">
                                    @if($recentTransactions->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Date</th>
                                                        <th>Type</th>
                                                        <th>Batch</th>
                                                        <th>Quantity</th>
                                                        <th>Note</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($recentTransactions as $transaction)
                                                        <tr>
                                                            <td>{{ $transaction->created_at->format('M d, Y H:i') }}</td>
                                                            <td>
                                                                @php
                                                                    $typeClasses = [
                                                                        'adjustment_in' => 'bg-success',
                                                                        'adjustment_out' => 'bg-danger',
                                                                        'transfer_in' => 'bg-info',
                                                                        'transfer_out' => 'bg-warning',
                                                                        'sale' => 'bg-primary',
                                                                        'purchase' => 'bg-secondary'
                                                                    ];
                                                                    $class = $typeClasses[$transaction->type] ?? 'bg-secondary';
                                                                @endphp
                                                                <span class="badge {{ $class }}">
                                                                    {{ ucfirst(str_replace('_', ' ', $transaction->type)) }}
                                                                </span>
                                                            </td>
                                                            <td>{{ $transaction->batch->batch_no ?? '-' }}</td>
                                                            <td>
                                                                @if(in_array($transaction->type, ['adjustment_out', 'transfer_out', 'sale']))
                                                                    <span class="text-danger">-{{ $transaction->qty }}</span>
                                                                @else
                                                                    <span class="text-success">+{{ $transaction->qty }}</span>
                                                                @endif
                                                            </td>
                                                            <td>{{ $transaction->note ?? '-' }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="text-center py-4">
                                            <i class="fas fa-history fa-3x text-muted mb-3"></i>
                                            <p class="text-muted">No recent transactions found for this product.</p>
                                        </div>
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
@endsection