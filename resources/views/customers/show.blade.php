@extends('layouts.app')

@section('title', 'Customer Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Customer Details</h5>
                    <div class="btn-group">
                        <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary">
                            Back
                        </a>
                        <a href="{{ route('customers.edit', $customer) }}" class="btn btn-primary">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="row">
                        <!-- Customer Information -->
                        <div class="col-md-8">
                            <div class="card h-100">
                                <div class="card-header">
                                    <h6 class="mb-0">Customer Information</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label text-muted">Customer Name</label>
                                                <p class="fw-bold">{{ $customer->name }}</p>
                                            </div>
                                            
                                            @if($customer->shop_name)
                                                <div class="mb-3">
                                                    <label class="form-label text-muted">Shop/Business Name</label>
                                                    <p class="fw-bold">{{ $customer->shop_name }}</p>
                                                </div>
                                            @endif
                                            
                                            <div class="mb-3">
                                                <label class="form-label text-muted">Phone Number</label>
                                                <p>
                                                    <a href="tel:{{ $customer->phone }}" class="text-decoration-none">
                                                        <i class="fas fa-phone text-success"></i> {{ $customer->phone }}
                                                    </a>
                                                </p>
                                            </div>
                                            
                                            @if($customer->email)
                                                <div class="mb-3">
                                                    <label class="form-label text-muted">Email Address</label>
                                                    <p>
                                                        <a href="mailto:{{ $customer->email }}" class="text-decoration-none">
                                                            <i class="fas fa-envelope text-primary"></i> {{ $customer->email }}
                                                        </a>
                                                    </p>
                                                </div>
                                            @endif
                                        </div>
                                        
                                        <div class="col-md-6">
                                            @if($customer->full_address)
                                                <div class="mb-3">
                                                    <label class="form-label text-muted">Full Address</label>
                                                    <p>{{ $customer->full_address }}</p>
                                                </div>
                                            @endif
                                            
                                            @if($customer->notes)
                                                <div class="mb-3">
                                                    <label class="form-label text-muted">Notes</label>
                                                    <p>{{ $customer->notes }}</p>
                                                </div>
                                            @endif
                                            
                                            <div class="mb-3">
                                                <label class="form-label text-muted">Customer Since</label>
                                                <p>{{ $customer->created_at->format('F d, Y') }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Customer Summary -->
                        <div class="col-md-4">
                            <div class="card h-100">
                                <div class="card-header">
                                    <h6 class="mb-0">Customer Summary</h6>
                                </div>
                                <div class="card-body">
                                    <div class="text-center mb-4">
                                        <div class="mb-3">
                                            <h4 class="text-primary">{{ $customer->orders->count() }}</h4>
                                            <small class="text-muted">Total Orders</small>
                                        </div>
                                        
                                        <div class="mb-3">
                                            @if($outstandingDue > 0)
                                                <h4 class="text-warning">{{ formatBTD($outstandingDue) }}</h4>
                                                <small class="text-muted">Outstanding Due</small>
                                            @else
                                                <h4 class="text-success">{{ formatBTD(0) }}</h4>
                                                <small class="text-muted">No Outstanding Due</small>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <div class="d-grid gap-2">
                                        <a href="{{ route('orders.create', ['customer_id' => $customer->id]) }}" 
                                           class="btn btn-primary btn-sm">
                                            <i class="fas fa-plus"></i> New Order
                                        </a>
                                        <button type="button" class="btn btn-outline-info btn-sm" 
                                                onclick="showCustomerSummary({{ $customer->id }})">
                                            <i class="fas fa-chart-line"></i> View Analytics
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Orders -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0">Recent Orders</h6>
                                    @if($customer->orders->count() > 5)
                                        <a href="{{ route('orders.index', ['customer_id' => $customer->id]) }}" 
                                           class="btn btn-sm btn-outline-primary">
                                            View All Orders
                                        </a>
                                    @endif
                                </div>
                                <div class="card-body">
                                    @if($lastFiveOrders->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-sm table-hover">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Order #</th>
                                                        <th>Date</th>
                                                        <th>Status</th>
                                                        <th>Payment Status</th>
                                                        <th>Total</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($lastFiveOrders as $order)
                                                        <tr>
                                                            <td>
                                                                <a href="{{ route('orders.show', $order) }}" 
                                                                   class="text-decoration-none fw-bold">
                                                                    #{{ $order->order_number }}
                                                                </a>
                                                            </td>
                                                            <td>{{ $order->created_at->format('M d, Y') }}</td>
                                                            <td>
                                                                <span class="badge bg-{{ $order->status_badge }}">
                                                                    {{ ucfirst($order->status) }}
                                                                </span>
                                                            </td>
                                                            <td>
                                                                <span class="badge bg-{{ $order->payment_status_badge }}">
                                                                    {{ ucfirst($order->payment_status) }}
                                                                </span>
                                                            </td>
                                                            <td class="fw-bold">{{ formatBTD($order->total_amount) }}</td>
                                                            <td>
                                                                <a href="{{ route('orders.show', $order) }}" 
                                                                   class="btn btn-sm btn-outline-primary" title="View Order">
                                                                    <i class="fas fa-eye"></i>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="fas fa-shopping-cart fa-3x mb-3"></i>
                                                <p class="mb-0">No orders found for this customer.</p>
                                                <p class="small">
                                                    <a href="{{ route('orders.create', ['customer_id' => $customer->id]) }}" 
                                                       class="text-decoration-none">
                                                        Create the first order
                                                    </a>
                                                </p>
                                            </div>
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

<!-- Customer Analytics Modal -->
<div class="modal fade" id="customerAnalyticsModal" tabindex="-1" aria-labelledby="customerAnalyticsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="customerAnalyticsModalLabel">
                    <i class="fas fa-chart-line"></i> Customer Analytics & Due Summary
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="analyticsContent">
                <div class="text-center">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">Loading customer analytics...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function showCustomerSummary(customerId) {
    // Show the modal
    const modal = new bootstrap.Modal(document.getElementById('customerAnalyticsModal'));
    modal.show();
    
    // Reset modal content to loading state
    document.getElementById('analyticsContent').innerHTML = `
        <div class="text-center">
            <div class="spinner-border" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2">Loading customer analytics...</p>
        </div>
    `;
    
    // Fetch customer summary data
    fetch(`/api/customers/${customerId}/summary`)
        .then(response => response.json())
        .then(data => {
            displayCustomerAnalytics(data);
        })
        .catch(error => {
            console.error('Error fetching customer summary:', error);
            document.getElementById('analyticsContent').innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i>
                    Error loading customer analytics. Please try again.
                </div>
            `;
        });
}

function displayCustomerAnalytics(data) {
    const dueCalc = data.due_calculation;
    const aging = dueCalc.aging_analysis;
    const credit = dueCalc.credit_summary;
    const payment = dueCalc.payment_history;
    
    const content = `
        <div class="row">
            <!-- Outstanding Summary -->
            <div class="col-md-6 mb-4">
                <div class="card border-primary">
                    <div class="card-header bg-primary text-white">
                        <h6 class="mb-0"><i class="fas fa-money-bill-wave"></i> Outstanding Summary</h6>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-6">
                                <h4 class="text-primary">৳${parseFloat(dueCalc.total_outstanding).toLocaleString()}</h4>
                                <small class="text-muted">Total Outstanding</small>
                            </div>
                            <div class="col-6">
                                <h4 class="${credit.is_over_limit ? 'text-danger' : 'text-success'}">৳${parseFloat(credit.credit_available).toLocaleString()}</h4>
                                <small class="text-muted">Credit Available</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Credit Summary -->
            <div class="col-md-6 mb-4">
                <div class="card border-info">
                    <div class="card-header bg-info text-white">
                        <h6 class="mb-0"><i class="fas fa-credit-card"></i> Credit Summary</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-2">
                            <small class="text-muted">Credit Limit:</small>
                            <strong class="float-end">৳${parseFloat(credit.credit_limit).toLocaleString()}</strong>
                        </div>
                        <div class="mb-2">
                            <small class="text-muted">Credit Used:</small>
                            <strong class="float-end">৳${parseFloat(credit.credit_used).toLocaleString()}</strong>
                        </div>
                        <div class="mb-3">
                            <small class="text-muted">Utilization:</small>
                            <strong class="float-end ${credit.credit_utilization_percentage > 80 ? 'text-danger' : 'text-success'}">${credit.credit_utilization_percentage}%</strong>
                        </div>
                        <div class="progress">
                            <div class="progress-bar ${credit.credit_utilization_percentage > 80 ? 'bg-danger' : 'bg-success'}" 
                                 style="width: ${Math.min(credit.credit_utilization_percentage, 100)}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <!-- Aging Analysis -->
            <div class="col-md-8 mb-4">
                <div class="card border-warning">
                    <div class="card-header bg-warning text-dark">
                        <h6 class="mb-0"><i class="fas fa-clock"></i> Aging Analysis</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Period</th>
                                        <th class="text-end">Amount</th>
                                        <th class="text-center">%</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Current (0-30 days)</td>
                                        <td class="text-end">৳${parseFloat(aging.current).toLocaleString()}</td>
                                        <td class="text-center">${dueCalc.total_outstanding > 0 ? Math.round((aging.current / dueCalc.total_outstanding) * 100) : 0}%</td>
                                    </tr>
                                    <tr>
                                        <td>31-60 days</td>
                                        <td class="text-end">৳${parseFloat(aging['30_days']).toLocaleString()}</td>
                                        <td class="text-center">${dueCalc.total_outstanding > 0 ? Math.round((aging['30_days'] / dueCalc.total_outstanding) * 100) : 0}%</td>
                                    </tr>
                                    <tr>
                                        <td>61-90 days</td>
                                        <td class="text-end text-warning">৳${parseFloat(aging['60_days']).toLocaleString()}</td>
                                        <td class="text-center">${dueCalc.total_outstanding > 0 ? Math.round((aging['60_days'] / dueCalc.total_outstanding) * 100) : 0}%</td>
                                    </tr>
                                    <tr>
                                        <td>91-120 days</td>
                                        <td class="text-end text-danger">৳${parseFloat(aging['90_days']).toLocaleString()}</td>
                                        <td class="text-center">${dueCalc.total_outstanding > 0 ? Math.round((aging['90_days'] / dueCalc.total_outstanding) * 100) : 0}%</td>
                                    </tr>
                                    <tr>
                                        <td>Over 120 days</td>
                                        <td class="text-end text-danger fw-bold">৳${parseFloat(aging.over_120).toLocaleString()}</td>
                                        <td class="text-center">${dueCalc.total_outstanding > 0 ? Math.round((aging.over_120 / dueCalc.total_outstanding) * 100) : 0}%</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Payment History -->
            <div class="col-md-4 mb-4">
                <div class="card border-success">
                    <div class="card-header bg-success text-white">
                        <h6 class="mb-0"><i class="fas fa-history"></i> Payment History</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <small class="text-muted">Last 6 Months Paid:</small>
                            <h5 class="text-success">৳${parseFloat(payment.total_paid_6_months).toLocaleString()}</h5>
                        </div>
                        <div class="mb-3">
                            <small class="text-muted">Payment Count:</small>
                            <strong class="float-end">${payment.payment_count_6_months}</strong>
                        </div>
                        <div class="mb-3">
                            <small class="text-muted">Average Payment:</small>
                            <strong class="float-end">৳${parseFloat(payment.average_payment).toLocaleString()}</strong>
                        </div>
                        <div>
                            <small class="text-muted">Last Payment:</small>
                            <strong class="float-end">${payment.last_payment_date ? new Date(payment.last_payment_date).toLocaleDateString() : 'N/A'}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Recent Transactions -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="fas fa-list"></i> Recent Transactions</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Type</th>
                                        <th>Description</th>
                                        <th class="text-end">Amount</th>
                                        <th class="text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${dueCalc.recent_transactions.map(transaction => `
                                        <tr>
                                            <td>${new Date(transaction.date).toLocaleDateString()}</td>
                                            <td><span class="badge bg-${transaction.type === 'order' ? 'primary' : 'info'}">${transaction.type}</span></td>
                                            <td>${transaction.description}</td>
                                            <td class="text-end">৳${parseFloat(transaction.amount).toLocaleString()}</td>
                                            <td class="text-center">
                                                <span class="badge bg-${transaction.status === 'paid' ? 'success' : transaction.status === 'pending' ? 'warning' : 'secondary'}">
                                                    ${transaction.status}
                                                </span>
                                            </td>
                                        </tr>
                                    `).join('')}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    document.getElementById('analyticsContent').innerHTML = content;
}
</script>
@endsection