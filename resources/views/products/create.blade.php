@extends('layouts.app')

@section('title', 'Add Product')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Add New Product</h4>
                    <a href="{{ route('products.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Products
                    </a>
                </div>
                
                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <h6><i class="fas fa-exclamation-triangle"></i> Please fix the following errors:</h6>
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    
                    <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data" id="productForm">
                        @csrf
                        
                        <!-- Hidden warehouse_id for single warehouse mode -->
                        <input type="hidden" name="warehouse_id" value="{{ $warehouses->first()->id ?? 1 }}">
                        
                        <!-- Product Information Section -->
                        <div class="mb-4">
                            <h5 class="mb-3">
                                <i class="fas fa-info-circle text-primary"></i> Product Information
                            </h5>
                            
                            <!-- Basic Information Section -->
                            <div class="mb-4">
                                <h6 class="mb-3 text-secondary">
                                    <i class="fas fa-tag"></i> Basic Details
                                </h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="name" class="form-label">Product Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                                   id="name" name="name" value="{{ old('name') }}" required>
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="sku" class="form-label">SKU</label>
                                            <input type="text" class="form-control @error('sku') is-invalid @enderror" 
                                                   id="sku" name="sku" value="{{ old('sku') }}">
                                            @error('sku')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label for="category_id" class="form-label">Category</label>
                                            <select class="form-select @error('category_id') is-invalid @enderror" 
                                                    id="category_id" name="category_id">
                                                <option value="">Select Category</option>
                                                @foreach($categories as $category)
                                                    <option value="{{ $category->id }}" 
                                                            {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                                        {{ $category->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('category_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label for="brand_id" class="form-label">Brand</label>
                                            <select class="form-select @error('brand_id') is-invalid @enderror" 
                                                    id="brand_id" name="brand_id">
                                                <option value="">Select Brand</option>
                                                @foreach($brands as $brand)
                                                    <option value="{{ $brand->id }}" 
                                                            {{ old('brand_id') == $brand->id ? 'selected' : '' }}>
                                                        {{ $brand->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('brand_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label for="unit_id" class="form-label">Unit</label>
                                            <select class="form-select @error('unit_id') is-invalid @enderror" 
                                                    id="unit_id" name="unit_id">
                                                <option value="">Select Unit</option>
                                                @foreach($units as $unit)
                                                    <option value="{{ $unit->id }}" 
                                                            {{ old('unit_id') == $unit->id ? 'selected' : '' }}>
                                                        {{ $unit->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('unit_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="barcode" class="form-label">Barcode</label>
                                            <input type="text" class="form-control @error('barcode') is-invalid @enderror" 
                                                   id="barcode" name="barcode" value="{{ old('barcode') }}">
                                            @error('barcode')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="status" class="form-label">Status</label>
                                            <select class="form-select @error('status') is-invalid @enderror" 
                                                    id="status" name="status">
                                                <option value="1" {{ old('status', '1') == '1' ? 'selected' : '' }}>Active</option>
                                                <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Inactive</option>
                                            </select>
                                            @error('status')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" 
                                              id="description" name="description" rows="4">{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <!-- Pricing & Inventory Section -->
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="selling_price" class="form-label">Selling Price <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-text">৳</span>
                                                <input type="number" class="form-control @error('selling_price') is-invalid @enderror" 
                                                       id="selling_price" name="selling_price" value="{{ old('selling_price') }}" 
                                                       step="0.01" min="0" required>
                                            </div>
                                            @error('selling_price')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="purchase_price" class="form-label">Purchase Price</label>
                                            <div class="input-group">
                                                <span class="input-group-text">৳</span>
                                                <input type="number" class="form-control @error('purchase_price') is-invalid @enderror" 
                                                       id="purchase_price" name="purchase_price" value="{{ old('purchase_price') }}" 
                                                       step="0.01" min="0">
                                            </div>
                                            @error('purchase_price')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="stock" class="form-label">Current Stock <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control @error('stock') is-invalid @enderror" 
                                                   id="stock" name="stock" value="{{ old('stock', 0) }}" 
                                                   min="0" required>
                                            @error('stock')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="expiration_date" class="form-label">Expiration Date</label>
                                            <input type="date" class="form-control @error('expiration_date') is-invalid @enderror" 
                                                   id="expiration_date" name="expiration_date" value="{{ old('expiration_date') }}">
                                            @error('expiration_date')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>


                            </div>

                            <!-- Images & Files Section -->
                            <div class="mb-4">
                                <h6 class="mb-3 text-secondary">
                                    <i class="fas fa-images"></i> Images & Files
                                </h6>
                                <div class="mb-4">
                                    <label class="form-label">Product Images</label>
                                    <div class="border rounded p-3">
                                        <input type="file" class="form-control mb-3" 
                                               id="images" name="images[]" multiple accept="image/*">
                                        <small class="text-muted">
                                            Select multiple images. Supported formats: JPG, PNG, GIF. Max size: 2MB per image.
                                        </small>
                                        <div id="imagePreview" class="mt-3 row"></div>
                                    </div>
                                </div>
                                
                                <div class="mb-4">
                                    <label class="form-label">Product Documents</label>
                                    <div class="border rounded p-3">
                                        <input type="file" class="form-control mb-3" 
                                               id="documents" name="documents[]" multiple 
                                               accept=".pdf,.doc,.docx,.xls,.xlsx">
                                        <small class="text-muted">
                                            Select documents. Supported formats: PDF, DOC, DOCX, XLS, XLSX. Max size: 5MB per file.
                                        </small>
                                        <div id="documentPreview" class="mt-3"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Form Actions -->
                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('products.index') }}" class="btn btn-secondary">
                                × Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Save Product
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Form validation and submission handling
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('productForm');
    const submitBtn = form.querySelector('button[type="submit"]');
    
    // Add real-time validation feedback
    const requiredFields = form.querySelectorAll('[required]');
    requiredFields.forEach(field => {
        field.addEventListener('blur', function() {
            validateField(this);
        });
        
        field.addEventListener('input', function() {
            if (this.classList.contains('is-invalid')) {
                validateField(this);
            }
        });
    });
    
    function validateField(field) {
        const value = field.value.trim();
        const isValid = value !== '';
        
        if (isValid) {
            field.classList.remove('is-invalid');
            field.classList.add('is-valid');
            // Remove any custom error message
            const errorDiv = field.parentNode.querySelector('.custom-invalid-feedback');
            if (errorDiv) errorDiv.remove();
        } else {
            field.classList.remove('is-valid');
            field.classList.add('is-invalid');
            // Add custom error message if not exists
            if (!field.parentNode.querySelector('.custom-invalid-feedback')) {
                const errorDiv = document.createElement('div');
                errorDiv.className = 'custom-invalid-feedback text-danger small mt-1';
                errorDiv.textContent = 'This field is required.';
                field.parentNode.appendChild(errorDiv);
            }
        }
    }
    
    // Form submission handling
    form.addEventListener('submit', function(e) {
        console.log('Form submission started');
        
        // Validate all required fields before submission
        let allValid = true;
        requiredFields.forEach(field => {
            validateField(field);
            if (!field.value.trim()) {
                allValid = false;
            }
        });
        
        if (!allValid) {
            e.preventDefault();
            console.log('Form submission prevented - validation failed');
            
            // Show alert to user
            const alertDiv = document.createElement('div');
            alertDiv.className = 'alert alert-warning alert-dismissible fade show';
            alertDiv.innerHTML = `
                <i class="fas fa-exclamation-triangle"></i> Please fill in all required fields before submitting.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            
            // Insert alert at top of form
            form.insertBefore(alertDiv, form.firstChild);
            
            // Scroll to first invalid field
            const firstInvalid = form.querySelector('.is-invalid');
            if (firstInvalid) {
                firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                firstInvalid.focus();
            }
            
            return false;
        }
        
        // Disable submit button to prevent double submission
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
        
        console.log('Form validation passed, submitting to server');
    });
});

// Image preview functionality
document.getElementById('images').addEventListener('change', function(e) {
    const preview = document.getElementById('imagePreview');
    preview.innerHTML = '';
    
    Array.from(e.target.files).forEach((file, index) => {
        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const col = document.createElement('div');
                col.className = 'col-md-3 mb-3';
                col.innerHTML = `
                    <div class="card">
                        <img src="${e.target.result}" class="card-img-top" style="height: 150px; object-fit: cover;">
                        <div class="card-body p-2">
                            <small class="text-muted">${file.name}</small>
                        </div>
                    </div>
                `;
                preview.appendChild(col);
            };
            reader.readAsDataURL(file);
        }
    });
});

// Document preview functionality
document.getElementById('documents').addEventListener('change', function(e) {
    const preview = document.getElementById('documentPreview');
    preview.innerHTML = '';
    
    Array.from(e.target.files).forEach((file, index) => {
        const item = document.createElement('div');
        item.className = 'alert alert-info d-flex align-items-center mb-2';
        item.innerHTML = `
            <i class="fas fa-file-alt me-2"></i>
            <span>${file.name} (${(file.size / 1024 / 1024).toFixed(2)} MB)</span>
        `;
        preview.appendChild(item);
    });
});


</script>
@endpush
@endsection