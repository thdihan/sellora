@extends('layouts.app')

@section('title', 'Edit Customer')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Edit Customer</h5>
                    <div class="btn-group">
                        <a href="{{ route('customers.show', $customer) }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
                        <button type="button" class="btn btn-outline-danger" onclick="confirmDelete()">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <h6 class="alert-heading">Please fix the following errors:</h6>
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route('customers.update', $customer) }}" method="POST" id="customerForm">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <!-- Basic Information -->
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="mb-0">Basic Information</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="name" class="form-label">Customer Name <span class="text-danger">*</span></label>
                                            <input type="text" 
                                                   class="form-control @error('name') is-invalid @enderror" 
                                                   id="name" 
                                                   name="name" 
                                                   value="{{ old('name', $customer->name) }}" 
                                                   required 
                                                   placeholder="Enter customer full name">
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="shop_name" class="form-label">Shop/Business Name</label>
                                            <input type="text" 
                                                   class="form-control @error('shop_name') is-invalid @enderror" 
                                                   id="shop_name" 
                                                   name="shop_name" 
                                                   value="{{ old('shop_name', $customer->shop_name) }}" 
                                                   placeholder="Enter shop or business name (optional)">
                                            @error('shop_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="phone" class="form-label">Phone Number <span class="text-danger">*</span></label>
                                            <input type="tel" 
                                                   class="form-control @error('phone') is-invalid @enderror" 
                                                   id="phone" 
                                                   name="phone" 
                                                   value="{{ old('phone', $customer->phone) }}" 
                                                   required 
                                                   placeholder="Enter phone number">
                                            @error('phone')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <div class="form-text">Format: +880XXXXXXXXX or 01XXXXXXXXX</div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="email" class="form-label">Email Address</label>
                                            <input type="email" 
                                                   class="form-control @error('email') is-invalid @enderror" 
                                                   id="email" 
                                                   name="email" 
                                                   value="{{ old('email', $customer->email) }}" 
                                                   placeholder="Enter email address (optional)">
                                            @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Additional Information -->
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="mb-0">Additional Information</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="full_address" class="form-label">Full Address</label>
                                            <textarea class="form-control @error('full_address') is-invalid @enderror" 
                                                      id="full_address" 
                                                      name="full_address" 
                                                      rows="3" 
                                                      placeholder="Enter complete address (optional)">{{ old('full_address', $customer->full_address) }}</textarea>
                                            @error('full_address')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="notes" class="form-label">Notes</label>
                                            <textarea class="form-control @error('notes') is-invalid @enderror" 
                                                      id="notes" 
                                                      name="notes" 
                                                      rows="4" 
                                                      placeholder="Any additional notes about the customer (optional)">{{ old('notes', $customer->notes) }}</textarea>
                                            @error('notes')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- Customer Statistics (Read-only) -->
                                        <div class="border rounded p-3 bg-light">
                                            <h6 class="text-muted mb-3">Customer Statistics</h6>
                                            <div class="row text-center">
                                                <div class="col-6">
                                                    <div class="mb-2">
                                                        <strong class="text-primary">{{ $customer->orders->count() }}</strong>
                                                    </div>
                                                    <small class="text-muted">Total Orders</small>
                                                </div>
                                                <div class="col-6">
                                                    <div class="mb-2">
                                                        <strong class="text-success">{{ $customer->created_at->format('M Y') }}</strong>
                                                    </div>
                                                    <small class="text-muted">Customer Since</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <a href="{{ route('customers.show', $customer) }}" class="btn btn-outline-secondary">
                                            <i class="fas fa-times"></i> Cancel
                                        </a>
                                    </div>
                                    <div>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save"></i> Update Customer
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- Delete Form (Hidden) -->
                    <form action="{{ route('customers.destroy', $customer) }}" method="POST" id="deleteForm" class="d-none">
                        @csrf
                        @method('DELETE')
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Auto-format phone number
document.getElementById('phone').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    
    if (value.startsWith('880')) {
        value = '+' + value;
    } else if (value.startsWith('01') && value.length === 11) {
        value = '+880' + value.substring(1);
    }
    
    e.target.value = value;
});

// Auto-capitalize name
document.getElementById('name').addEventListener('input', function(e) {
    e.target.value = e.target.value.replace(/\b\w/g, l => l.toUpperCase());
});

// Auto-capitalize shop name
document.getElementById('shop_name').addEventListener('input', function(e) {
    e.target.value = e.target.value.replace(/\b\w/g, l => l.toUpperCase());
});

// Confirm delete
function confirmDelete() {
    if (confirm('Are you sure you want to delete this customer? This action cannot be undone.\n\nNote: You can only delete customers who have no orders.')) {
        document.getElementById('deleteForm').submit();
    }
}

// Form validation
document.getElementById('customerForm').addEventListener('submit', function(e) {
    const name = document.getElementById('name').value.trim();
    const phone = document.getElementById('phone').value.trim();
    
    if (!name) {
        e.preventDefault();
        alert('Customer name is required.');
        document.getElementById('name').focus();
        return;
    }
    
    if (!phone) {
        e.preventDefault();
        alert('Phone number is required.');
        document.getElementById('phone').focus();
        return;
    }
    
    // Validate phone format
    const phoneRegex = /^(\+880|880|01)[0-9]{8,9}$/;
    if (!phoneRegex.test(phone.replace(/\s/g, ''))) {
        e.preventDefault();
        alert('Please enter a valid Bangladeshi phone number.');
        document.getElementById('phone').focus();
        return;
    }
});
</script>
@endsection