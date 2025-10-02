@extends('layouts.app')

@section('title', 'Orders Management')

@push('styles')
<style>
    /* Orders Page Specific Styles */
    .orders-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 2rem;
        border-radius: 12px;
        margin-bottom: 2rem;
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    }
    
    .orders-title {
        font-size: 2.5rem !important;
        font-weight: 700 !important;
        margin: 0 !important;
        text-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .orders-subtitle {
        font-size: 1.1rem !important;
        opacity: 0.9;
        margin-top: 0.5rem !important;
        margin-bottom: 0 !important;
    }
    
    .create-order-btn {
        background: linear-gradient(135deg, #ff6b6b, #ee5a24) !important;
        color: white !important;
        border: none !important;
        padding: 12px 24px !important;
        border-radius: 8px !important;
        font-weight: 600 !important;
        font-size: 1rem !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 8px !important;
        transition: all 0.3s ease !important;
        text-decoration: none !important;
        box-shadow: 0 4px 15px rgba(255, 107, 107, 0.3) !important;
    }
    
    .create-order-btn:hover {
        transform: translateY(-2px) !important;
        box-shadow: 0 6px 20px rgba(255, 107, 107, 0.4) !important;
        color: white !important;
    }
    
    .filters-card {
        background: white !important;
        border-radius: 12px !important;
        padding: 1.5rem !important;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05) !important;
        margin-bottom: 2rem !important;
        border: 1px solid #e2e8f0 !important;
    }
    
    .search-input {
        border: 2px solid #e2e8f0 !important;
        border-radius: 8px !important;
        padding: 12px 16px !important;
        font-size: 1rem !important;
        transition: all 0.3s ease !important;
        background: #f8fafc !important;
    }
    
    .search-input:focus {
        border-color: #667eea !important;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1) !important;
        outline: none !important;
        background: white !important;
    }
    
    .filter-select {
        border: 2px solid #e2e8f0 !important;
        border-radius: 8px !important;
        padding: 12px 16px !important;
        font-size: 1rem !important;
        background: #f8fafc !important;
        transition: all 0.3s ease !important;
    }
    
    .filter-select:focus {
        border-color: #667eea !important;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1) !important;
        outline: none !important;
        background: white !important;
    }
    
    .filter-btn {
        background: linear-gradient(135deg, #667eea, #764ba2) !important;
        color: white !important;
        border: none !important;
        padding: 12px 24px !important;
        border-radius: 8px !important;
        font-weight: 600 !important;
        transition: all 0.3s ease !important;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3) !important;
    }
    
    .filter-btn:hover {
        transform: translateY(-2px) !important;
        box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4) !important;
    }
    
    .orders-table-card {
        background: white !important;
        border-radius: 12px !important;
        overflow: hidden !important;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05) !important;
        border: 1px solid #e2e8f0 !important;
    }
    
    .table {
        margin: 0 !important;
        border-collapse: separate !important;
        border-spacing: 0 !important;
    }
    
    .table thead th {
        background: linear-gradient(135deg, #f8fafc, #e2e8f0) !important;
        color: #374151 !important;
        font-weight: 600 !important;
        padding: 1rem !important;
        border: none !important;
        font-size: 0.875rem !important;
        text-transform: uppercase !important;
        letter-spacing: 0.5px !important;
    }
    
    .table tbody td {
        padding: 1rem !important;
        border-bottom: 1px solid #f1f5f9 !important;
        vertical-align: middle !important;
        font-size: 0.875rem !important;
    }
    
    .table tbody tr:hover {
        background-color: #f8fafc !important;
    }
    
    .container-fluid {
        background-color: #f8fafc !important;
        min-height: 100vh !important;
        padding: 2rem !important;
    }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gray-50 px-4 py-8">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div class="mb-4 sm:mb-0">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Orders Management</h1>
                <p class="text-gray-600">Manage and track all your orders</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('orders.create') }}" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white font-semibold rounded-lg shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200">
                    <i class="fas fa-plus mr-2"></i>Create Order
                </a>
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <form method="GET" action="{{ route('orders.index') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-7 gap-4">
                <div class="lg:col-span-2">
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Search Orders</label>
                    <input type="text" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" id="search" name="search" 
                           value="{{ request('search') }}" placeholder="Order number, customer...">
                </div>
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" id="status" name="status">
                        <option value="">All Status</option>
                        @foreach($statuses as $status)
                            <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>{{ $status }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="payment_status" class="block text-sm font-medium text-gray-700 mb-2">Payment</label>
                    <select class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" id="payment_status" name="payment_status">
                        <option value="">All Payments</option>
                        <option value="pending" {{ request('payment_status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Paid</option>
                        <option value="partial" {{ request('payment_status') == 'partial' ? 'selected' : '' }}>Partial</option>
                        <option value="refunded" {{ request('payment_status') == 'refunded' ? 'selected' : '' }}>Refunded</option>
                    </select>
                </div>
                <div>
                    <label for="date_from" class="block text-sm font-medium text-gray-700 mb-2">From Date</label>
                    <input type="date" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" id="date_from" name="date_from" 
                           value="{{ request('date_from') }}">
                </div>
                <div>
                    <label for="date_to" class="block text-sm font-medium text-gray-700 mb-2">To Date</label>
                    <input type="date" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" id="date_to" name="date_to" 
                           value="{{ request('date_to') }}">
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white font-semibold rounded-lg shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200">
                        <i class="fas fa-search mr-2"></i>Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                    <i class="fas fa-shopping-cart mr-2 text-blue-600"></i>Orders List
                </h2>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                    {{ $orders->total() }} total orders
                </span>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order #</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Items</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">VAT/TAX</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Revenue</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-4 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($orders as $order)
                    <tr class="hover:bg-gray-50 transition-colors duration-150">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex flex-col">
                                <span class="text-sm font-bold text-blue-600">#{{ $order->order_number }}</span>
                                @if($order->approved_by)
                                    <span class="inline-flex items-center text-xs text-green-600">
                                        <i class="fas fa-check-circle mr-1"></i>Approved
                                    </span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex flex-col">
                                <span class="text-sm font-medium text-gray-900">{{ $order->customer_name }}</span>
                                @if($order->user)
                                    <span class="text-xs text-gray-500">Created by: {{ $order->user->name }}</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                {{ $order->orderItems->count() }} items
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm font-bold text-gray-900">৳{{ number_format($order->total_amount ?? $order->amount, 2) }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($order->vat_amount > 0 || $order->tax_amount > 0)
                                <div class="flex flex-col space-y-1">
                                    @if($order->vat_amount > 0)
                                        <span class="text-xs text-blue-600">VAT: ৳{{ number_format($order->vat_amount, 2) }}</span>
                                    @endif
                                    @if($order->tax_amount > 0)
                                        <span class="text-xs text-amber-600">TAX: ৳{{ number_format($order->tax_amount, 2) }}</span>
                                    @endif
                                </div>
                            @else
                                <span class="text-xs text-gray-500">No VAT/TAX</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm font-bold text-green-600">৳{{ number_format($order->net_revenue ?? 0, 2) }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($order->status === 'Pending') bg-yellow-100 text-yellow-800
                                @elseif($order->status === 'Approved') bg-blue-100 text-blue-800
                                @elseif($order->status === 'Forwarded') bg-purple-100 text-purple-800
                                @elseif($order->status === 'Completed') bg-green-100 text-green-800
                                @elseif($order->status === 'Cancelled') bg-red-100 text-red-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ $order->status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                N/A
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex flex-col">
                                <span class="text-sm font-medium text-gray-900">{{ $order->created_at->format('M d, Y') }}</span>
                                <span class="text-xs text-gray-500">{{ $order->created_at->format('h:i A') }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <div class="flex items-center justify-center space-x-2">
                                <a href="{{ route('orders.show', $order) }}" class="inline-flex items-center p-2 text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded-lg transition-colors" title="View">
                                    <i class="fas fa-eye text-sm"></i>
                                </a>
                                <a href="{{ route('orders.edit', $order) }}" class="inline-flex items-center p-2 text-gray-600 hover:text-gray-800 hover:bg-gray-50 rounded-lg transition-colors" title="Edit">
                                    <i class="fas fa-edit text-sm"></i>
                                </a>
                                @if($order->status === 'pending' && !$order->approved_by)
                                    <form action="{{ route('orders.approve', $order) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="inline-flex items-center p-2 text-green-600 hover:text-green-800 hover:bg-green-50 rounded-lg transition-colors" title="Approve" 
                                                onclick="return confirm('Are you sure you want to approve this order?')">
                                            <i class="fas fa-check text-sm"></i>
                                        </button>
                                    </form>
                                @endif
                                <button type="button" class="inline-flex items-center p-2 text-red-600 hover:text-red-800 hover:bg-red-50 rounded-lg transition-colors delete-order-btn" title="Delete" 
                                        data-order-id="{{ $order->id }}" data-order-number="{{ $order->order_number }}">
                                    <i class="fas fa-trash text-sm"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center">
                                <i class="fas fa-inbox text-6xl text-gray-300 mb-4"></i>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">No orders found</h3>
                                <p class="text-gray-500 mb-6">Get started by creating your first order</p>
                                <a href="{{ route('orders.create') }}" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white font-semibold rounded-lg shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200">
                                    <i class="fas fa-plus mr-2"></i>Create First Order
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
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                <div class="flex justify-center">
                    {{ $orders->appends(request()->query())->links('vendor.pagination.custom-3d') }}
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

@section('styles')
<style>
/* Enhanced Orders Page Styling */
.container-fluid {
    background-color: #f8f9fa !important;
    min-height: 100vh;
}

/* Header Section */
.text-gray-800 {
    color: #5a5c69 !important;
}

/* Cards */
.card {
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
    border: 1px solid #e3e6f0;
    border-radius: 0.75rem;
}

.card-header {
    background-color: #fff;
    border-bottom: 1px solid #e3e6f0;
}

/* Form Controls */
.form-control, .form-select {
    border: 1px solid #d1d3e2;
    border-radius: 0.5rem;
    padding: 0.75rem 1rem;
    font-size: 0.875rem;
    transition: all 0.15s ease-in-out;
}

.form-control:focus, .form-select:focus {
    border-color: #4e73df;
    box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
}

.form-label {
    font-weight: 600;
    margin-bottom: 0.5rem;
}

/* Buttons */
.btn {
    border-radius: 0.5rem;
    font-weight: 600;
    transition: all 0.15s ease-in-out;
}

.btn-primary {
    background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
    border: none;
    box-shadow: 0 0.125rem 0.25rem rgba(78, 115, 223, 0.4);
}

.btn-primary:hover {
    background: linear-gradient(135deg, #224abe 0%, #1e3a8a 100%);
    transform: translateY(-1px);
    box-shadow: 0 0.25rem 0.5rem rgba(78, 115, 223, 0.5);
}

.btn-outline-primary {
    border-color: #4e73df;
    color: #4e73df;
}

.btn-outline-primary:hover {
    background-color: #4e73df;
    border-color: #4e73df;
}

/* Table Styling */
.table {
    font-size: 0.875rem;
}

.table th {
    border-top: none;
    border-bottom: 2px solid #e3e6f0;
    font-weight: 700;
    color: #5a5c69;
    background-color: #f8f9fc;
    padding: 1rem 0.75rem;
    letter-spacing: 0.5px;
}

.table td {
    padding: 1rem 0.75rem;
    vertical-align: middle;
    border-bottom: 1px solid #e3e6f0;
}

.table-hover tbody tr:hover {
    background-color: rgba(78, 115, 223, 0.05);
    transition: background-color 0.15s ease-in-out;
}

/* Badge Styling */
.badge {
    font-size: 0.75rem;
    font-weight: 600;
    padding: 0.5rem 0.75rem;
    border-radius: 0.5rem;
    letter-spacing: 0.5px;
}

.bg-light {
    background-color: #f8f9fc !important;
    border: 1px solid #e3e6f0;
}

/* Button Group */
.btn-group .btn {
    border-radius: 0.375rem;
    margin-right: 0.25rem;
    padding: 0.375rem 0.75rem;
}

.btn-group .btn:last-child {
    margin-right: 0;
}

.btn-sm {
    font-size: 0.75rem;
    padding: 0.375rem 0.75rem;
}

/* Empty State */
.text-light {
    color: #d1d3e2 !important;
}

/* Responsive Design */
@media (max-width: 768px) {
    .container-fluid {
        padding-left: 1rem;
        padding-right: 1rem;
    }
    
    .btn-lg {
        font-size: 1rem;
        padding: 0.75rem 1.5rem;
    }
    
    .table-responsive {
        font-size: 0.8rem;
    }
    
    .btn-group .btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.7rem;
    }
}

/* Animation for cards */
.card {
    animation: fadeInUp 0.5s ease-out;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Status Badge Colors */
.badge.bg-warning {
    background-color: #f6c23e !important;
    color: #1a1a1a;
}

.badge.bg-success {
    background-color: #1cc88a !important;
}

.badge.bg-danger {
    background-color: #e74a3b !important;
}

.badge.bg-info {
    background-color: #36b9cc !important;
}

.badge.bg-secondary {
    background-color: #858796 !important;
}

/* Pagination in card footer */
.card-footer {
    background-color: #f8f9fc;
    border-top: 1px solid #e3e6f0;
    padding: 1.25rem;
}

/* Search and filter section */
.card-body.py-3 {
    background-color: #fff;
}

/* Icon styling */
.fas {
    width: 1rem;
    text-align: center;
}

/* Text colors */
.text-gray-700 {
    color: #6e707e !important;
}

/* Shadow utilities */
.shadow-sm {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075) !important;
}
</style>
@endsection

@section('scripts')
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
@endsection