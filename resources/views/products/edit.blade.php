@extends('layouts.app')

@section('title', 'Edit Product')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Edit Product: {{ $product->name }}</h4>
                    <div>
                        <a href="{{ route('products.show', $product) }}" class="btn btn-info me-2">
                            <i class="fas fa-eye"></i> View
                        </a>
                        <a href="{{ route('products.index') }}" class="btn btn-secondary">
                            Back to Products
                        </a>
                    </div>
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
                    
                    <form action="{{ route('products.update', $product) }}" method="POST" enctype="multipart/form-data" id="productForm">
                        @csrf
                        @method('PUT')
                        
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
                                                   id="name" name="name" value="{{ old('name', $product->name) }}" required>
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="sku" class="form-label">SKU <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('sku') is-invalid @enderror" 
                                                   id="sku" name="sku" value="{{ old('sku', $product->sku) }}" required>
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
                                                            {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
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
                                                            {{ old('brand_id', $product->brand_id) == $brand->id ? 'selected' : '' }}>
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
                                                            {{ old('unit_id', $product->unit_id) == $unit->id ? 'selected' : '' }}>
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
                                                   id="barcode" name="barcode" value="{{ old('barcode', $product->barcode) }}">
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
                                                <option value="1" {{ old('status', $product->status) == '1' ? 'selected' : '' }}>Active</option>
                                                <option value="0" {{ old('status', $product->status) == '0' ? 'selected' : '' }}>Inactive</option>
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
                                              id="description" name="description" rows="4">{{ old('description', $product->description) }}</textarea>
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
                                                       id="selling_price" name="selling_price" value="{{ old('selling_price', $product->selling_price) }}" 
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
                                                       id="purchase_price" name="purchase_price" value="{{ old('purchase_price', $product->purchase_price) }}" 
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
                                                   id="stock" name="stock" value="{{ old('stock', $product->stock) }}" 
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
                                                   id="expiration_date" name="expiration_date" value="{{ old('expiration_date', $product->expiration_date) }}">
                                            @error('expiration_date')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>


                            
                            <!-- Images & Documents Section -->
                            <div class="mb-4">
                                <h6 class="mb-3 text-secondary">
                                    <i class="fas fa-images"></i> Images & Documents
                                </h6>
                                <!-- Existing Images -->
                                @if($product->files->where('type', 'image')->count() > 0)
                                    <div class="mb-4">
                                        <label class="form-label">Current Images</label>
                                        <div class="row">
                                            @foreach($product->files->where('type', 'image') as $file)
                                                <div class="col-md-3 mb-3">
                                                    <div class="card">
                                                        <img src="{{ $file->url }}" class="card-img-top" style="height: 150px; object-fit: cover;">
                                                        <div class="card-body p-2">
                                                            <small class="text-muted">{{ $file->original_name }}</small>
                                                            <button type="button" class="btn btn-sm btn-danger w-100 mt-1" 
                                                                    onclick="removeFile({{ $file->id }})">
                                                                <i class="fas fa-trash"></i> Remove
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                                
                                <div class="mb-4">
                                    <label class="form-label">Add New Images</label>
                                    <div class="border rounded p-3">
                                        <input type="file" class="form-control mb-3" 
                                               id="images" name="images[]" multiple accept="image/*">
                                        <small class="text-muted">
                                            Select multiple images. Supported formats: JPG, PNG, GIF. Max size: 2MB per image.
                                        </small>
                                        <div id="imagePreview" class="mt-3 row"></div>
                                    </div>
                                </div>
                                
                                <!-- Existing Documents -->
                                @if($product->files->where('type', 'document')->count() > 0)
                                    <div class="mb-4">
                                        <label class="form-label">Current Documents</label>
                                        <div class="list-group">
                                            @foreach($product->files->where('type', 'document') as $file)
                                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <i class="fas fa-file-alt me-2"></i>
                                                        <a href="{{ $file->url }}" target="_blank">{{ $file->original_name }}</a>
                                                        <small class="text-muted d-block">{{ $file->formatted_size }}</small>
                                                    </div>
                                                    <button type="button" class="btn btn-sm btn-danger" 
                                                            onclick="removeFile({{ $file->id }})">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                                
                                <div class="mb-4">
                                    <label class="form-label">Add New Documents</label>
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
                                <i class="fas fa-arrow-left"></i> Back to Products
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Product
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
// Image preview functionality
function previewImages(input, previewContainer) {
    const files = input.files;
    previewContainer.innerHTML = '';
    
    for (let i = 0; i < files.length; i++) {
        const file = files[i];
        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const div = document.createElement('div');
                div.className = 'col-md-3 mb-2';
                div.innerHTML = `
                    <div class="card">
                        <img src="${e.target.result}" class="card-img-top" style="height: 100px; object-fit: cover;">
                        <div class="card-body p-2">
                            <small class="text-muted">${file.name}</small>
                        </div>
                    </div>
                `;
                previewContainer.appendChild(div);
            };
            reader.readAsDataURL(file);
        }
    }
}

// Document preview functionality
function previewDocuments(input, previewContainer) {
    const files = input.files;
    previewContainer.innerHTML = '';
    
    for (let i = 0; i < files.length; i++) {
        const file = files[i];
        const div = document.createElement('div');
        div.className = 'col-md-4 mb-2';
        div.innerHTML = `
            <div class="card">
                <div class="card-body p-2 text-center">
                    <i class="fas fa-file-alt fa-2x text-secondary mb-2"></i>
                    <div>
                        <small class="text-muted d-block">${file.name}</small>
                        <small class="text-muted">${(file.size / 1024).toFixed(1)} KB</small>
                    </div>
                </div>
            </div>
        `;
        previewContainer.appendChild(div);
    }
}

// Attach event listeners
const imageInput = document.getElementById('images');
const imagePreview = document.getElementById('imagePreview');
const documentInput = document.getElementById('documents');
const documentPreview = document.getElementById('documentPreview');

if (imageInput && imagePreview) {
    imageInput.addEventListener('change', function() {
        previewImages(this, imagePreview);
    });
}

if (documentInput && documentPreview) {
    documentInput.addEventListener('change', function() {
        previewDocuments(this, documentPreview);
    });
}



// Remove file functionality
function removeFile(fileId) {
    if (confirm('Are you sure you want to remove this file?')) {
        fetch(`{{ route('products.remove-file', $product) }}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ file_id: fileId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                ScrollPreserver.preserveAndReload();
            } else {
                alert('Error removing file: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error removing file');
        });
    }
}
</script>
@endpush
@endsection