@extends('layouts.app')

@section('title', 'Orders Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Orders Management</h5>
                    <a href="{{ route('orders.create') }}" class="btn btn-primary">
                        Create Order
                    </a>
                </div>
                <div class="card-body">
                    <!-- Search and Filter Form -->
                    <form method="GET" action="{{ route('orders.index') }}" class="mb-4">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <input type="text" name="search" class="form-control" 
                                       placeholder="Search orders..." value="{{ request('search') }}">
                            </div>
                            <div class="col-md-3">
                                <select name="status" class="form-select">
                                    <option value="">All Status</option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                                    <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Processing</option>
                                    <option value="shipped" {{ request('status') == 'shipped' ? 'selected' : '' }}>Shipped</option>
                                    <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Delivered</option>
                                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    <option value="refunded" {{ request('status') == 'refunded' ? 'selected' : '' }}>Refunded</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select name="payment_status" class="form-select">
                                    <option value="">All Payment Status</option>
                                    <option value="pending" {{ request('payment_status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Paid</option>
                                    <option value="partial" {{ request('payment_status') == 'partial' ? 'selected' : '' }}>Partial</option>
                                    <option value="failed" {{ request('payment_status') == 'failed' ? 'selected' : '' }}>Failed</option>
                                    <option value="refunded" {{ request('payment_status') == 'refunded' ? 'selected' : '' }}>Refunded</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-outline-primary w-100">
                                    <i class="fas fa-search"></i> Filter
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Orders Table -->
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Order #</th>
                                    <th>Customer</th>
                                    <th>Product</th>
                                    <th>Total Amount</th>
                                    <th>VAT/TAX</th>
                                    <th>Net Revenue</th>
                                    <th>Status</th>
                                    <th>Payment</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($orders as $order)
                                <tr>
                                    <td>
                                        <strong>{{ $order->order_number }}</strong>
                                        @if($order->attachments && count($order->attachments) > 0)
                                            <i class="fas fa-paperclip text-muted ms-1" title="Has attachments"></i>
                                        @endif
                                    </td>
                                    <td>
                                        <div>
                                            <strong>{{ $order->customer_name }}</strong>
                                            @if($order->customer_email)
                                                <br><small class="text-muted">{{ $order->customer_email }}</small>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            {{ $order->product_name }}
                                            <br><small class="text-muted">Qty: {{ $order->quantity }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <strong>৳{{ number_format($order->total_amount, 2) }}</strong>
                                        <br><small class="text-muted">Base: ৳{{ number_format($order->amount, 2) }}</small>
                                    </td>
                                    <td>
                                        @if($order->vat_amount > 0 || $order->tax_amount > 0)
                                            @if($order->vat_amount > 0)
                                                <small class="text-info">VAT: ৳{{ number_format($order->vat_amount, 2) }}</small><br>
                                            @endif
                                            @if($order->tax_amount > 0)
                                                <small class="text-warning">TAX: ৳{{ number_format($order->tax_amount, 2) }}</small>
                                            @endif
                                        @else
                                            <small class="text-muted">No VAT/TAX</small>
                                        @endif
                                    </td>
                                    <td>
                                        <strong class="text-success">৳{{ number_format($order->net_revenue, 2) }}</strong>
                                    </td>
                                    <td>
                                        <span class="badge {{ $order->status_badge }}">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge {{ $order->payment_status_badge }}">
                                            {{ ucfirst(str_replace('_', ' ', $order->payment_status)) }}
                                        </span>
                                    </td>
                                    <td>
                                        {{ $order->order_date ? $order->order_date->format('M d, Y') : 'N/A' }}
                                        @if($order->delivery_date)
                                            <br><small class="text-muted">Delivery: {{ $order->delivery_date->format('M d') }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('orders.show', $order) }}" class="btn btn-sm btn-outline-primary" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('orders.edit', $order) }}" class="btn btn-sm btn-outline-secondary" title="Edit">
                                                Edit
                                            </a>
                                            @if($order->status === 'pending' && !$order->approved_by)
                                                <form action="{{ route('orders.approve', $order) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-sm btn-outline-success" title="Approve" 
                                                            onclick="return confirm('Are you sure you want to approve this order?')">
                                                        ✓
                                                    </button>
                                                </form>
                                            @endif
                                            <button type="button" class="btn btn-sm btn-outline-danger delete-order-btn" title="Delete" 
                                                    data-order-id="{{ $order->id }}" data-order-number="{{ $order->order_number }}">
                                                Delete
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="10" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="fas fa-inbox fa-3x mb-3"></i>
                                            <p>No orders found.</p>
                                            <a href="{{ route('orders.create') }}" class="btn btn-primary">
                                                Create First Order
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($orders->hasPages())
                        <div class="d-flex justify-content-center mt-4">
                            {{ $orders->appends(request()->query())->links('vendor.pagination.custom-3d') }}
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
.table th {
    border-top: none;
    font-weight: 600;
    color: #495057;
}

.btn-group .btn {
    border-radius: 0.25rem;
    margin-right: 2px;
}

.badge {
    font-size: 0.75em;
}

.table-responsive {
    border-radius: 0.5rem;
}

.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border: 1px solid rgba(0, 0, 0, 0.125);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle order deletion
    document.querySelectorAll('.delete-order-btn').forEach(button => {
        button.addEventListener('click', function() {
            const orderId = this.dataset.orderId;
            const orderNumber = this.dataset.orderNumber;
            
            if (confirm(`Are you sure you want to delete order #${orderNumber}?`)) {
                fetch(`/orders/${orderId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Remove the table row
                        this.closest('tr').remove();
                        
                        // Show success message
                        const alertDiv = document.createElement('div');
                        alertDiv.className = 'alert alert-success alert-dismissible fade show';
                        alertDiv.innerHTML = `
                            <strong>Success!</strong> Order deleted successfully.
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        `;
                        document.querySelector('.container-fluid').insertBefore(alertDiv, document.querySelector('.row'));
                        
                        // Auto-hide alert after 3 seconds
                        setTimeout(() => {
                            alertDiv.remove();
                        }, 3000);
                    } else {
                        alert('Error deleting order: ' + (data.message || 'Unknown error'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error deleting order. Please try again.');
                });
            }
        });
    });
});
</script>
@endpush