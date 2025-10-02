@extends('layouts.app')

@section('title', 'Order Details')

@push('styles')
<style>
    /* Order Show Page Specific Styles */
    .orders-header {
        background: transparent;
        color: #495057;
        padding: 1.5rem 0;
        margin-bottom: 2rem;
    }
    
    .orders-title {
        font-size: 2.2rem !important;
        font-weight: 700 !important;
        margin: 0 !important;
        color: #495057 !important;
    }
    
    .orders-subtitle {
        font-size: 1rem !important;
        color: #6c757d !important;
        margin-top: 0.5rem !important;
        margin-bottom: 0 !important;
    }
    
    .action-btn {
        padding: 10px 20px !important;
        border-radius: 8px !important;
        font-weight: 600 !important;
        font-size: 0.9rem !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 6px !important;
        transition: all 0.3s ease !important;
        text-decoration: none !important;
        margin-left: 8px !important;
        border: 2px solid transparent !important;
    }
    
    .action-btn.btn-primary {
        background: #007bff !important;
        color: white !important;
        border-color: #007bff !important;
    }
    
    .action-btn.btn-primary:hover {
        background: #0056b3 !important;
        border-color: #0056b3 !important;
        color: white !important;
    }
    
    .action-btn.btn-outline-secondary {
        background: transparent !important;
        color: #6c757d !important;
        border-color: #6c757d !important;
    }
    
    .action-btn.btn-outline-secondary:hover {
        background: #6c757d !important;
        color: white !important;
        border-color: #6c757d !important;
    }
    
    .action-btn.btn-success {
        background: #28a745 !important;
        color: white !important;
        border-color: #28a745 !important;
    }
    
    .action-btn.btn-success:hover {
        background: #1e7e34 !important;
        border-color: #1e7e34 !important;
        color: white !important;
    }
    
    .info-card {
        background: white;
        border-radius: 15px;
        box-shadow: 0 8px 25px rgba(0,0,0,0.08);
        border: none;
        margin-bottom: 2rem;
        overflow: hidden;
        transition: all 0.3s ease;
    }
    
    .info-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 35px rgba(0,0,0,0.12);
    }
    
    .card-header-custom {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-bottom: 2px solid #dee2e6;
        padding: 1.5rem;
    }
    
    .card-title-custom {
        font-size: 1.3rem !important;
        font-weight: 700 !important;
        color: #495057 !important;
        margin: 0 !important;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .card-body-custom {
        padding: 2rem;
    }
    
    .info-table {
        margin: 0;
    }
    
    .info-table td {
        border: none;
        padding: 0.8rem 0;
        vertical-align: middle;
        border-bottom: 1px solid #f8f9fa;
    }
    
    .info-table td:first-child {
        font-weight: 600;
        color: #6c757d;
        width: 35%;
    }
    
    .info-table td:last-child {
        font-weight: 500;
        color: #495057;
    }
    
    .status-badge {
        font-size: 0.85rem !important;
        padding: 0.5rem 1rem !important;
        border-radius: 25px !important;
        font-weight: 600 !important;
        text-transform: uppercase !important;
        letter-spacing: 0.5px !important;
    }
    
    .card {
        border: 1px solid #e5e7eb;
        border-radius: 0.5rem;
        background: white;
    }
    
    .card-header {
        background: white !important;
        border-bottom: 1px solid #e5e7eb;
        padding: 1rem 1.5rem;
    }
    
    .card-body {
        padding: 1.5rem;
    }
    
    .card-title {
        font-size: 1.125rem;
        font-weight: 600;
        color: #111827;
    }
    
    .table {
        margin-bottom: 0;
    }
    
    .table th {
        background-color: #f9fafb;
        border-bottom: 1px solid #e5e7eb;
        font-weight: 600;
        color: #374151;
        padding: 0.75rem;
    }
    
    .table td {
        padding: 0.75rem;
        border-bottom: 1px solid #f3f4f6;
    }
    
    .badge {
        font-size: 0.75rem;
        font-weight: 500;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
    }
    
    .badge.bg-warning {
        background-color: #f59e0b !important;
        color: white;
    }
    
    .badge.bg-success {
        background-color: #10b981 !important;
        color: white;
    }
    
    .badge.bg-info {
        background-color: #3b82f6 !important;
        color: white;
    }
    
    .badge.bg-danger {
        background-color: #ef4444 !important;
        color: white;
    }
    
    .summary-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.5rem 0;
    }
    
    .summary-item.total {
        font-weight: 600;
        font-size: 1.125rem;
        border-top: 1px solid #e5e7eb;
        padding-top: 1rem;
        margin-top: 0.5rem;
    }
    
    .summary-label {
        color: #6b7280;
    }
    
    .summary-value {
        font-weight: 600;
        color: #111827;
    }
    
    .btn {
        border-radius: 0.5rem;
        font-weight: 500;
        transition: all 0.2s;
    }
    
    .btn-sm {
        padding: 0.375rem 0.75rem;
        font-size: 0.875rem;
    }
    
    .quick-actions-row {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-bottom: 0;
    }
    
    .quick-action-btn {
        flex: 1;
        min-width: 120px;
        padding: 12px 16px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.9rem;
        transition: all 0.3s ease;
        border: 2px solid transparent;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }
    
    .quick-action-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0,0,0,0.15);
    }
    
    .notes-content {
        background: #f8f9fa;
        padding: 1.5rem;
        border-radius: 10px;
        border-left: 4px solid #007bff;
        font-style: italic;
        color: #495057;
    }
    
    .attachment-card {
        border: 2px dashed #dee2e6;
        border-radius: 10px;
        transition: all 0.3s ease;
        background: #f8f9fa;
    }
    
    .attachment-card:hover {
        border-color: #007bff;
        background: white;
        transform: translateY(-2px);
    }
    
    .download-btn {
        background: linear-gradient(135deg, #007bff, #0056b3);
        color: white;
        border: none;
        padding: 8px 16px;
        border-radius: 6px;
        font-size: 0.8rem;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .download-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 123, 255, 0.3);
        color: white;
    }
    
    .modal-content {
        border-radius: 15px;
        border: none;
        box-shadow: 0 20px 40px rgba(0,0,0,0.15);
    }
    
    .modal-header {
        background: linear-gradient(135deg, #28a745, #20c997);
        color: white;
        border-radius: 15px 15px 0 0;
        border-bottom: none;
        padding: 1.5rem;
    }
    
    .modal-title {
        font-weight: 700;
        font-size: 1.2rem;
    }
    
    .btn-close {
        filter: invert(1);
    }
    
    .container-fluid {
        max-width: 1400px;
        margin: 0 auto;
        padding: 2rem;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="border-b border-gray-200 px-6 py-4">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Order Details</h1>
                <p class="mt-1 text-sm text-gray-600">Order #{{ $order->order_number }} • Created {{ $order->created_at->format('M d, Y') }}</p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('orders.index') }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to Orders
                </a>
                <a href="{{ route('orders.edit', $order) }}" 
                   class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Edit Order
                </a>
                @if($order->status === 'pending' && !$order->approved_by)
                    <button type="button" class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors duration-200" data-bs-toggle="modal" data-bs-target="#approveModal">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Approve Order
                    </button>
                @endif
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Customer Information Card -->
            <div class="info-card">
                <div class="card-header-custom">
                    <h3 class="card-title-custom">
                        <i class="fas fa-user text-primary"></i>
                        Customer Information
                    </h3>
                </div>
                <div class="card-body-custom">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="info-table table">
                                <tr>
                                    <td>Name:</td>
                                    <td>{{ $order->customer_name }}</td>
                                </tr>
                                @if($order->customer_email)
                                <tr>
                                    <td>Email:</td>
                                    <td><a href="mailto:{{ $order->customer_email }}" class="text-decoration-none text-primary">{{ $order->customer_email }}</a></td>
                                </tr>
                                @endif
                                @if($order->customer_phone)
                                <tr>
                                    <td>Phone:</td>
                                    <td><a href="tel:{{ $order->customer_phone }}" class="text-decoration-none text-primary">{{ $order->customer_phone }}</a></td>
                                </tr>
                                @endif
                            </table>
                        </div>
                        <div class="col-md-6">
                            @if($order->customer_address)
                            <table class="info-table table">
                                <tr>
                                    <td>Address:</td>
                                    <td>{{ $order->customer_address }}</td>
                                </tr>
                            </table>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Product Information Card -->
            <div class="info-card">
                <div class="card-header-custom">
                    <h3 class="card-title-custom">
                        <i class="fas fa-box text-success"></i>
                        Product Information
                    </h3>
                </div>
                <div class="card-body-custom">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="info-table table">
                                <tr>
                                    <td>Product:</td>
                                    <td>{{ $order->product_name }}</td>
                                </tr>
                                @if($order->product_description)
                                <tr>
                                    <td>Description:</td>
                                    <td>{{ $order->product_description }}</td>
                                </tr>
                                @endif
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="info-table table">
                                <tr>
                                    <td>Quantity:</td>
                                    <td><span class="badge bg-info">{{ $order->quantity }}</span></td>
                                </tr>
                                <tr>
                                    <td>Unit Price:</td>
                                    <td><span class="text-success fw-bold">৳{{ number_format($order->unit_price, 2) }}</span></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Details Card -->
            <div class="info-card">
                <div class="card-header-custom">
                    <h3 class="card-title-custom">
                        <i class="fas fa-info-circle text-info"></i>
                        Order Details
                    </h3>
                </div>
                <div class="card-body-custom">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="info-table table">
                                <tr>
                                    <td>Order Date:</td>
                                    <td>{{ $order->created_at->format('M d, Y') }}</td>
                                </tr>
                                @if(isset($order->delivery_date) && $order->delivery_date)
                                <tr>
                                    <td>Delivery Date:</td>
                                    <td>{{ $order->delivery_date->format('M d, Y') }}</td>
                                </tr>
                                @endif
                                @if($order->payment_method)
                                <tr>
                                    <td>Payment Method:</td>
                                    <td>{{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}</td>
                                </tr>
                                @endif
                                <tr>
                                    <td>Created By:</td>
                                    <td>{{ $order->user->name }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="info-table table">
                                <tr>
                                    <td>Status:</td>
                                    <td><span class="status-badge {{ $order->status_badge }}">{{ ucfirst($order->status) }}</span></td>
                                </tr>
                                <tr>
                                    <td>Payment Status:</td>
                                    <td><span class="status-badge {{ $order->payment_status_badge }}">{{ ucfirst(str_replace('_', ' ', $order->payment_status)) }}</span></td>
                                </tr>
                                @if($order->approved_by)
                                <tr>
                                    <td>Approved By:</td>
                                    <td>{{ $order->approver->name }}</td>
                                </tr>
                                <tr>
                                    <td>Approved At:</td>
                                    <td>{{ $order->approved_at->format('M d, Y H:i') }}</td>
                                </tr>
                                @endif
                            </table>
                        </div>
                    </div>
                    
                    @if($order->approval_notes)
                    <div class="mt-4">
                        <h5 class="fw-bold text-muted mb-3">Approval Notes:</h5>
                        <div class="notes-content">
                            {{ $order->approval_notes }}
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            @if($order->notes)
            <!-- Notes Card -->
            <div class="info-card">
                <div class="card-header-custom">
                    <h3 class="card-title-custom">
                        <i class="fas fa-sticky-note text-warning"></i>
                        Notes
                    </h3>
                </div>
                <div class="card-body-custom">
                    <div class="notes-content">
                        {{ $order->notes }}
                    </div>
                </div>
            </div>
            @endif

            @if($order->attachments && count($order->attachments) > 0)
            <!-- Attachments Card -->
            <div class="info-card">
                <div class="card-header-custom">
                    <h3 class="card-title-custom">
                        <i class="fas fa-paperclip text-secondary"></i>
                        Attachments
                    </h3>
                </div>
                <div class="card-body-custom">
                    <div class="row">
                        @foreach($order->attachments as $index => $attachment)
                        <div class="col-md-4 mb-3">
                            <div class="attachment-card card">
                                <div class="card-body text-center py-4">
                                    <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                                    <h6 class="card-title mb-2 fw-bold">{{ $attachment['name'] }}</h6>
                                    <p class="card-text text-muted mb-3">{{ number_format($attachment['size'] / 1024, 2) }} KB</p>
                                    <a href="{{ route('orders.download-attachment', [$order, $index]) }}" class="download-btn">
                                        <i class="fas fa-download me-2"></i>Download
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>
        
        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Order Summary Card -->
            <div class="info-card">
                <div class="card-header-custom">
                    <h3 class="card-title-custom">
                        <i class="fas fa-calculator text-primary"></i>
                        Order Summary
                    </h3>
                </div>
                <div class="card-body-custom">
                    <div class="summary-item">
                        <span class="summary-label">Subtotal:</span>
                        <span class="summary-value">${{ number_format($order->base_amount, 2) }}</span>
                    </div>
                    <div class="summary-item">
                        <span class="summary-label">VAT ({{ $order->vat_percentage }}%):</span>
                        <span class="summary-value">${{ number_format($order->vat_amount, 2) }}</span>
                    </div>
                    <div class="summary-item">
                        <span class="summary-label">Tax ({{ $order->tax_percentage }}%):</span>
                        <span class="summary-value">${{ number_format($order->tax_amount, 2) }}</span>
                    </div>
                    <hr class="my-3">
                    <div class="summary-item total">
                        <span class="summary-label">Total:</span>
                        <span class="summary-value">${{ number_format($order->total_amount, 2) }}</span>
                    </div>
                    <div class="summary-item">
                        <span class="summary-label">Net Revenue:</span>
                        <span class="summary-value text-success">${{ number_format($order->net_revenue, 2) }}</span>
                    </div>
                </div>
            </div>
            
            <!-- Quick Actions Card -->
            @if($order->status !== 'cancelled')
            <div class="info-card">
                <div class="card-header-custom">
                    <h3 class="card-title-custom">
                        <i class="fas fa-bolt text-primary"></i>
                        Quick Actions
                    </h3>
                </div>
                <div class="card-body-custom">
                    <div class="d-flex flex-wrap gap-2">
                        <button class="btn btn-warning btn-sm" onclick="updateStatus('processing')">
                            <i class="fas fa-cog me-1"></i>Processing
                        </button>
                        <button class="btn btn-info btn-sm" onclick="updateStatus('shipped')">
                            <i class="fas fa-shipping-fast me-1"></i>Shipped
                        </button>
                        <button class="btn btn-success btn-sm" onclick="updateStatus('delivered')">
                            <i class="fas fa-check-circle me-1"></i>Delivered
                        </button>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Approval Modal -->
@if($order->status === 'pending' && !$order->approved_by)
<div class="modal fade" id="approveModal" tabindex="-1" aria-labelledby="approveModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('orders.approve', $order) }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-header">
                    <h5 class="modal-title" id="approveModalLabel">
                        <i class="fas fa-check-circle me-2"></i>Approve Order
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="alert alert-info border-0 rounded-3">
                        <i class="fas fa-info-circle me-2"></i>
                        Are you sure you want to approve this order?
                    </div>
                    <div class="mb-3">
                        <label for="approval_notes" class="form-label fw-bold">Approval Notes (Optional)</label>
                        <textarea class="form-control border-2 rounded-3" id="approval_notes" name="approval_notes" rows="4" placeholder="Add any notes about the approval..."></textarea>
                    </div>
                </div>
                <div class="modal-footer p-4">
                    <button type="button" class="btn btn-secondary rounded-3 px-4" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-success rounded-3 px-4">
                        <i class="fas fa-check me-2"></i>Approve Order
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<!-- Status Update Form (Hidden) -->
<form id="statusUpdateForm" action="{{ route('orders.update', $order) }}" method="POST" style="display: none;">
    @csrf
    @method('PATCH')
    <input type="hidden" name="status" id="newStatus">
    <input type="hidden" name="customer_name" value="{{ $order->customer_name }}">
    <input type="hidden" name="product_name" value="{{ $order->product_name }}">
    <input type="hidden" name="quantity" value="{{ $order->quantity }}">
    <input type="hidden" name="unit_price" value="{{ $order->unit_price }}">
    <input type="hidden" name="order_date" value="{{ $order->created_at->format('Y-m-d') }}">
    <input type="hidden" name="payment_status" value="{{ $order->payment_status }}">
</form>
@endsection

@push('scripts')
<script>
function updateStatus(status) {
    const statusText = status.charAt(0).toUpperCase() + status.slice(1);
    if (confirm(`Are you sure you want to update the order status to ${statusText}?`)) {
        document.getElementById('newStatus').value = status;
        document.getElementById('statusUpdateForm').submit();
    }
}
</script>
@endpush