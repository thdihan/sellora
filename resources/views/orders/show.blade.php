@extends('layouts.app')

@section('title', 'Order Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Order Details - {{ $order->order_number }}</h5>
                    <div class="btn-group">
                        <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary">
                            ← Back to Orders
                        </a>
                        <a href="{{ route('orders.edit', $order) }}" class="btn btn-outline-primary">
                            ✏️ Edit
                        </a>
                        @if($order->status === 'pending' && !$order->approved_by)
                            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#approveModal">
                                ✓ Approve Order
                            </button>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Order Information -->
                        <div class="col-md-8">
                            <div class="row">
                                <!-- Customer Information -->
                                <div class="col-md-6">
                                    <h6 class="text-primary mb-3">Customer Information</h6>
                                    <table class="table table-borderless table-sm">
                                        <tr>
                                            <td class="fw-bold">Name:</td>
                                            <td>{{ $order->customer_name }}</td>
                                        </tr>
                                        @if($order->customer_email)
                                        <tr>
                                            <td class="fw-bold">Email:</td>
                                            <td><a href="mailto:{{ $order->customer_email }}">{{ $order->customer_email }}</a></td>
                                        </tr>
                                        @endif
                                        @if($order->customer_phone)
                                        <tr>
                                            <td class="fw-bold">Phone:</td>
                                            <td><a href="tel:{{ $order->customer_phone }}">{{ $order->customer_phone }}</a></td>
                                        </tr>
                                        @endif
                                        @if($order->customer_address)
                                        <tr>
                                            <td class="fw-bold">Address:</td>
                                            <td>{{ $order->customer_address }}</td>
                                        </tr>
                                        @endif
                                    </table>
                                </div>
                                
                                <!-- Product Information -->
                                <div class="col-md-6">
                                    <h6 class="text-primary mb-3">Product Information</h6>
                                    <table class="table table-borderless table-sm">
                                        <tr>
                                            <td class="fw-bold">Product:</td>
                                            <td>{{ $order->product_name }}</td>
                                        </tr>
                                        @if($order->product_description)
                                        <tr>
                                            <td class="fw-bold">Description:</td>
                                            <td>{{ $order->product_description }}</td>
                                        </tr>
                                        @endif
                                        <tr>
                                            <td class="fw-bold">Quantity:</td>
                                            <td>{{ $order->quantity }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Unit Price:</td>
                                            <td>৳{{ number_format($order->unit_price, 2) }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            
                            <hr>
                            
                            <!-- Order Details -->
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="text-primary mb-3">Order Details</h6>
                                    <table class="table table-borderless table-sm">
                                        <tr>
                                            <td class="fw-bold">Order Date:</td>
                                            <td>{{ $order->created_at->format('M d, Y') }}</td>
                                        </tr>
                                        @if(isset($order->delivery_date) && $order->delivery_date)
                                        <tr>
                                            <td class="fw-bold">Delivery Date:</td>
                                            <td>{{ $order->delivery_date->format('M d, Y') }}</td>
                                        </tr>
                                        @endif
                                        @if($order->payment_method)
                                        <tr>
                                            <td class="fw-bold">Payment Method:</td>
                                            <td>{{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}</td>
                                        </tr>
                                        @endif
                                        <tr>
                                            <td class="fw-bold">Created By:</td>
                                            <td>{{ $order->user->name }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Created At:</td>
                                            <td>{{ $order->created_at->format('M d, Y H:i') }}</td>
                                        </tr>
                                    </table>
                                </div>
                                
                                <div class="col-md-6">
                                    <h6 class="text-primary mb-3">Status & Approval</h6>
                                    <table class="table table-borderless table-sm">
                                        <tr>
                                            <td class="fw-bold">Status:</td>
                                            <td><span class="badge {{ $order->status_badge }}">{{ ucfirst($order->status) }}</span></td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Payment Status:</td>
                                            <td><span class="badge {{ $order->payment_status_badge }}">{{ ucfirst(str_replace('_', ' ', $order->payment_status)) }}</span></td>
                                        </tr>
                                        @if($order->approved_by)
                                        <tr>
                                            <td class="fw-bold">Approved By:</td>
                                            <td>{{ $order->approver->name }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Approved At:</td>
                                            <td>{{ $order->approved_at->format('M d, Y H:i') }}</td>
                                        </tr>
                                        @if($order->approval_notes)
                                        <tr>
                                            <td class="fw-bold">Approval Notes:</td>
                                            <td>{{ $order->approval_notes }}</td>
                                        </tr>
                                        @endif
                                        @endif
                                    </table>
                                </div>
                            </div>
                            
                            @if($order->notes)
                            <hr>
                            <h6 class="text-primary mb-3">Notes</h6>
                            <div class="bg-light p-3 rounded">
                                {{ $order->notes }}
                            </div>
                            @endif
                            
                            @if($order->attachments && count($order->attachments) > 0)
                            <hr>
                            <h6 class="text-primary mb-3">Attachments</h6>
                            <div class="row">
                                @foreach($order->attachments as $index => $attachment)
                                <div class="col-md-4 mb-3">
                                    <div class="card">
                                        <div class="card-body text-center">
                                            <span style="font-size: 2rem; color: #6c757d;" class="mb-2 d-block">📄</span>
                                            <h6 class="card-title">{{ $attachment['name'] }}</h6>
                                            <p class="card-text text-muted small">{{ number_format($attachment['size'] / 1024, 2) }} KB</p>
                                            <a href="{{ route('orders.download-attachment', [$order, $index]) }}" class="btn btn-sm btn-outline-primary">
                                                📥 Download
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            @endif
                        </div>
                        
                        <!-- Order Summary -->
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-header">
                                    <h6 class="mb-0">Order Summary</h6>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Base Amount:</span>
                                        <span>৳{{ number_format($order->amount, 2) }}</span>
                                    </div>
                                    
                                    @if($order->vat_amount > 0)
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>VAT ({{ ucfirst(str_replace('_', ' ', $order->vat_condition)) }}):</span>
                                        <span>৳{{ number_format($order->vat_amount, 2) }}</span>
                                    </div>
                                    @endif
                                    
                                    @if($order->tax_amount > 0)
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>TAX ({{ ucfirst(str_replace('_', ' ', $order->tax_condition)) }}):</span>
                                        <span>৳{{ number_format($order->tax_amount, 2) }}</span>
                                    </div>
                                    @endif
                                    
                                    <hr>
                                    <div class="d-flex justify-content-between fw-bold mb-2">
                                        <span>Total Amount:</span>
                                        <span class="text-primary">৳{{ number_format($order->total_amount, 2) }}</span>
                                    </div>
                                    
                                    <div class="d-flex justify-content-between fw-bold fs-5">
                                        <span>Net Revenue:</span>
                                        <span class="text-success">৳{{ number_format($order->net_revenue, 2) }}</span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Quick Actions -->
                            <div class="card mt-3">
                                <div class="card-header">
                                    <h6 class="mb-0">Quick Actions</h6>
                                </div>
                                <div class="card-body">
                                    <div class="d-grid gap-2">
                                        @if($order->status !== 'cancelled')
                                        <button class="btn btn-outline-warning btn-sm" onclick="updateStatus('processing')">
                                            ⚙️ Mark as Processing
                                        </button>
                                        <button class="btn btn-outline-info btn-sm" onclick="updateStatus('shipped')">
                                            🚚 Mark as Shipped
                                        </button>
                                        <button class="btn btn-outline-success btn-sm" onclick="updateStatus('delivered')">
                                            <span style="color: #28a745;">✓</span> Mark as Delivered
                                        </button>
                                        <hr>
                                        <button class="btn btn-outline-danger btn-sm" onclick="updateStatus('cancelled')">
                                            <span style="color: #dc3545;">×</span> Cancel Order
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

<!-- Approval Modal -->
@if($order->status === 'pending' && !$order->approved_by)
<div class="modal fade" id="approveModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('orders.approve', $order) }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-header">
                    <h5 class="modal-title">Approve Order</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to approve this order?</p>
                    <div class="mb-3">
                        <label for="approval_notes" class="form-label">Approval Notes (Optional)</label>
                        <textarea class="form-control" id="approval_notes" name="approval_notes" rows="3" placeholder="Add any notes about the approval..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Approve Order</button>
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
    if (confirm('Are you sure you want to update the order status to ' + status + '?')) {
        document.getElementById('newStatus').value = status;
        document.getElementById('statusUpdateForm').submit();
    }
}
</script>
@endpush

@push('styles')
<style>
.table-borderless td {
    border: none;
    padding: 0.25rem 0.5rem;
}

.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border: 1px solid rgba(0, 0, 0, 0.125);
}

.text-primary {
    color: #0d6efd !important;
}

.badge {
    font-size: 0.75em;
}

.bg-light {
    background-color: #f8f9fa !important;
}

.btn-group .btn {
    margin-right: 0.25rem;
}

.d-grid {
    display: grid !important;
}

.gap-2 {
    gap: 0.5rem !important;
}
</style>
@endpush