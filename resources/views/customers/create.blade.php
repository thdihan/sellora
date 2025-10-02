@extends('layouts.app')

@section('title', 'Add New Customer')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Add New Customer</h5>
                    <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary">
                        Back to Customers
                    </a>
                </div>
                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <h6>Please fix the following errors:</h6>
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('customers.store') }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <!-- Basic Information -->
                            <div class="col-md-6">
                                <h6 class="text-muted mb-3">Basic Information</h6>
                                
                                <div class="mb-3">
                                    <label for="name" class="form-label">Customer Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name') }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="shop_name" class="form-label">Shop/Business Name</label>
                                    <input type="text" class="form-control @error('shop_name') is-invalid @enderror" 
                                           id="shop_name" name="shop_name" value="{{ old('shop_name') }}">
                                    @error('shop_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="phone" class="form-label">Phone Number <span class="text-danger">*</span></label>
                                    <input type="tel" class="form-control @error('phone') is-invalid @enderror" 
                                           id="phone" name="phone" value="{{ old('phone') }}" required>
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="email" class="form-label">Email Address</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                           id="email" name="email" value="{{ old('email') }}">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Additional Information -->
                            <div class="col-md-6">
                                <h6 class="text-muted mb-3">Additional Information</h6>
                                
                                <div class="mb-3">
                                    <label for="full_address" class="form-label">Full Address</label>
                                    <textarea class="form-control @error('full_address') is-invalid @enderror" 
                                              id="full_address" name="full_address" rows="4" 
                                              placeholder="Enter complete address including area, city, postal code...">{{ old('full_address') }}</textarea>
                                    @error('full_address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="notes" class="form-label">Notes</label>
                                    <textarea class="form-control @error('notes') is-invalid @enderror" 
                                              id="notes" name="notes" rows="4" 
                                              placeholder="Any additional notes about the customer...">{{ old('notes') }}</textarea>
                                    @error('notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-times"></i> Cancel
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Save Customer
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Auto-format phone number as user types
document.getElementById('phone').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    if (value.length >= 11) {
        // Format as: 01XXX-XXXXXX
        value = value.substring(0, 5) + '-' + value.substring(5, 11);
    }
    e.target.value = value;
});

// Auto-capitalize name fields
document.getElementById('name').addEventListener('input', function(e) {
    e.target.value = e.target.value.replace(/\b\w/g, l => l.toUpperCase());
});

document.getElementById('shop_name').addEventListener('input', function(e) {
    e.target.value = e.target.value.replace(/\b\w/g, l => l.toUpperCase());
});
</script>
@endsection