@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">Customer Details</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('customers.index') }}">Customers</a></li>
                            <li class="breadcrumb-item active" aria-current="page">{{ $customer->name }}</li>
                        </ol>
                    </nav>
                </div>
                <div class="btn-group" role="group">
                    <a href="{{ route('customers.edit', $customer->id) }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-edit"></i> Edit Customer
                    </a>
                    <a href="{{ route('customers.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left"></i> Back to List
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Customer Information Card -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white border-bottom">
                    <h5 class="card-title mb-0 text-primary">
                        <i class="fas fa-user me-2"></i>Customer Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <label class="form-label text-muted small">Full Name</label>
                            <p class="fw-semibold mb-0">{{ $customer->name }}</p>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label text-muted small">Email Address</label>
                            <p class="mb-0">{{ $customer->email }}</p>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label text-muted small">Phone Number</label>
                            <p class="mb-0">{{ $customer->phone ?? 'Not provided' }}</p>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label text-muted small">Customer Since</label>
                            <p class="mb-0">{{ $customer->created_at->format('M d, Y') }}</p>
                        </div>
                        <div class="col-12">
                            <label class="form-label text-muted small">Address</label>
                            <p class="mb-0">{{ $customer->address ?? 'No address provided' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Customer Summary Card -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white border-bottom">
                    <h5 class="card-title mb-0 text-success">
                        <i class="fas fa-chart-line me-2"></i>Customer Summary
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="text-center p-3 bg-light rounded">
                                <h4 class="text-primary mb-1">{{ $customer->orders->count() }}</h4>
                                <small class="text-muted">Total Orders</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center p-3 bg-light rounded">
                                <h4 class="text-success mb-1">${{ number_format($customer->orders->sum('total_amount'), 2) }}</h4>
                                <small class="text-muted">Total Spent</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center p-3 bg-light rounded">
                                <h4 class="text-warning mb-1">${{ number_format($customer->orders->where('status', 'pending')->sum('total_amount'), 2) }}</h4>
                                <small class="text-muted">Outstanding Due</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center p-3 bg-light rounded">
                                <h4 class="text-info mb-1">{{ $customer->orders->where('created_at', '>=', now()->subDays(30))->count() }}</h4>
                                <small class="text-muted">Orders (30 days)</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions Card -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom">
                    <h5 class="card-title mb-0 text-info">
                        <i class="fas fa-bolt me-2"></i>Quick Actions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-wrap gap-2">
                        <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#analyticsModal">
                            <i class="fas fa-chart-bar me-1"></i>View Analytics
                        </button>
                        <a href="{{ route('orders.create', ['customer_id' => $customer->id]) }}" class="btn btn-outline-success btn-sm">
                            <i class="fas fa-plus me-1"></i>Create Order
                        </a>
                        <button type="button" class="btn btn-outline-info btn-sm">
                            <i class="fas fa-envelope me-1"></i>Send Email
                        </button>
                        <button type="button" class="btn btn-outline-warning btn-sm">
                            <i class="fas fa-file-invoice me-1"></i>Generate Report
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Orders Card -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0 text-dark">
                        <i class="fas fa-shopping-cart me-2"></i>Recent Orders
                    </h5>
                    <a href="{{ route('orders.index', ['customer_id' => $customer->id]) }}" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-eye me-1"></i>View All Orders
                    </a>
                </div>
                <div class="card-body p-0">
                    @if($customer->orders->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="border-0 fw-semibold">Order ID</th>
                                        <th class="border-0 fw-semibold">Date</th>
                                        <th class="border-0 fw-semibold">Items</th>
                                        <th class="border-0 fw-semibold">Total</th>
                                        <th class="border-0 fw-semibold">Status</th>
                                        <th class="border-0 fw-semibold text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($customer->orders->take(10) as $order)
                                    <tr>
                                        <td class="fw-semibold text-primary">#{{ $order->id }}</td>
                                        <td>{{ $order->created_at->format('M d, Y') }}</td>
                                        <td>{{ $order->order_items->count() }} items</td>
                                        <td class="fw-semibold">${{ number_format($order->total_amount, 2) }}</td>
                                        <td>
                                            @switch($order->status)
                                                @case('completed')
                                                    <span class="badge bg-success">Completed</span>
                                                    @break
                                                @case('pending')
                                                    <span class="badge bg-warning">Pending</span>
                                                    @break
                                                @case('cancelled')
                                                    <span class="badge bg-danger">Cancelled</span>
                                                    @break
                                                @default
                                                    <span class="badge bg-secondary">{{ ucfirst($order->status) }}</span>
                                            @endswitch
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="{{ route('orders.show', $order->id) }}" class="btn btn-outline-primary btn-sm" title="View Order">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('orders.edit', $order->id) }}" class="btn btn-outline-secondary btn-sm" title="Edit Order">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No Orders Found</h5>
                            <p class="text-muted mb-3">This customer hasn't placed any orders yet.</p>
                            <a href="{{ route('orders.create', ['customer_id' => $customer->id]) }}" class="btn btn-primary">
                                <i class="fas fa-plus me-1"></i>Create First Order
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Customer Analytics Modal -->
<div class="modal fade" id="analyticsModal" tabindex="-1" aria-labelledby="analyticsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="analyticsModalLabel">
                    <i class="fas fa-chart-bar me-2"></i>Customer Analytics - {{ $customer->name }}
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="analyticsContent">
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2 text-muted">Loading analytics data...</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="exportAnalytics()">
                    <i class="fas fa-download me-1"></i>Export Report
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Load analytics when modal is shown
    const analyticsModal = document.getElementById('analyticsModal');
    if (analyticsModal) {
        analyticsModal.addEventListener('show.bs.modal', function() {
            loadCustomerAnalytics();
        });
    }
});

function loadCustomerAnalytics() {
    const customerId = {{ $customer->id }};
    const analyticsContent = document.getElementById('analyticsContent');
    
    fetch(`/customers/${customerId}/analytics`)
        .then(response => response.json())
        .then(data => {
            analyticsContent.innerHTML = `
                <div class="row g-4">
                    <!-- Order Trends -->
                    <div class="col-md-6">
                        <div class="card border-0 bg-light">
                            <div class="card-body">
                                <h6 class="card-title text-primary">
                                    <i class="fas fa-chart-line me-1"></i>Order Trends
                                </h6>
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h4 class="mb-0">${data.monthly_orders || 0}</h4>
                                        <small class="text-muted">This Month</small>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge ${data.trend_direction === 'up' ? 'bg-success' : 'bg-danger'}">
                                            <i class="fas fa-arrow-${data.trend_direction === 'up' ? 'up' : 'down'}"></i>
                                            ${data.trend_percentage || 0}%
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Average Order Value -->
                    <div class="col-md-6">
                        <div class="card border-0 bg-light">
                            <div class="card-body">
                                <h6 class="card-title text-success">
                                    <i class="fas fa-dollar-sign me-1"></i>Average Order Value
                                </h6>
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h4 class="mb-0">$${data.avg_order_value || '0.00'}</h4>
                                        <small class="text-muted">Per Order</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Aging Analysis -->
                    <div class="col-12">
                        <div class="card border-0">
                            <div class="card-header bg-white">
                                <h6 class="mb-0 text-warning">
                                    <i class="fas fa-clock me-1"></i>Aging Analysis
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row text-center">
                                    <div class="col-3">
                                        <h5 class="text-success">$${data.aging_0_30 || '0.00'}</h5>
                                        <small class="text-muted">0-30 Days</small>
                                    </div>
                                    <div class="col-3">
                                        <h5 class="text-warning">$${data.aging_31_60 || '0.00'}</h5>
                                        <small class="text-muted">31-60 Days</small>
                                    </div>
                                    <div class="col-3">
                                        <h5 class="text-danger">$${data.aging_61_90 || '0.00'}</h5>
                                        <small class="text-muted">61-90 Days</small>
                                    </div>
                                    <div class="col-3">
                                        <h5 class="text-dark">$${data.aging_90_plus || '0.00'}</h5>
                                        <small class="text-muted">90+ Days</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment History -->
                    <div class="col-md-6">
                        <div class="card border-0">
                            <div class="card-header bg-white">
                                <h6 class="mb-0 text-info">
                                    <i class="fas fa-credit-card me-1"></i>Payment History
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-2">
                                    <small class="text-muted">On-time Payments</small>
                                    <div class="progress" style="height: 8px;">
                                        <div class="progress-bar bg-success" style="width: ${data.ontime_payment_rate || 0}%"></div>
                                    </div>
                                    <small class="text-success">${data.ontime_payment_rate || 0}%</small>
                                </div>
                                <div class="mb-2">
                                    <small class="text-muted">Late Payments</small>
                                    <div class="progress" style="height: 8px;">
                                        <div class="progress-bar bg-warning" style="width: ${data.late_payment_rate || 0}%"></div>
                                    </div>
                                    <small class="text-warning">${data.late_payment_rate || 0}%</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Transactions -->
                    <div class="col-md-6">
                        <div class="card border-0">
                            <div class="card-header bg-white">
                                <h6 class="mb-0 text-dark">
                                    <i class="fas fa-exchange-alt me-1"></i>Recent Transactions
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="list-group list-group-flush">
                                    ${data.recent_transactions ? data.recent_transactions.map(transaction => `
                                        <div class="list-group-item border-0 px-0 py-2">
                                            <div class="d-flex justify-content-between">
                                                <small class="text-muted">${transaction.date}</small>
                                                <small class="fw-semibold ${transaction.amount > 0 ? 'text-success' : 'text-danger'}">
                                                    ${transaction.amount > 0 ? '+' : ''}$${Math.abs(transaction.amount).toFixed(2)}
                                                </small>
                                            </div>
                                            <small class="text-dark">${transaction.description}</small>
                                        </div>
                                    `).join('') : '<p class="text-muted mb-0">No recent transactions</p>'}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        })
        .catch(error => {
            console.error('Error loading analytics:', error);
            analyticsContent.innerHTML = `
                <div class="alert alert-warning" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Unable to load analytics data. Please try again later.
                </div>
            `;
        });
}

function exportAnalytics() {
    const customerId = {{ $customer->id }};
    window.open(`/customers/${customerId}/analytics/export`, '_blank');
}
</script>

<style>
.card {
    transition: all 0.3s ease;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}

.btn-group-sm > .btn, .btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
}

.table th {
    font-weight: 600;
    color: #495057;
    font-size: 0.875rem;
}

.badge {
    font-size: 0.75rem;
    font-weight: 500;
}

.progress {
    background-color: #e9ecef;
}

.modal-header.bg-primary {
    border-bottom: none;
}

.breadcrumb {
    background: none;
    padding: 0;
    margin: 0;
}

.breadcrumb-item + .breadcrumb-item::before {
    content: ">";
    color: #6c757d;
}
</style>
@endsection