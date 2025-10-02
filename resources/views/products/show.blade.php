@extends('layouts.app')

@section('title', 'Product Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Product Details: {{ $product->name }}</h4>
                    <div>
                        <a href="{{ route('products.edit', $product) }}" class="btn btn-primary me-2">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <a href="{{ route('products.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Products
                        </a>
                    </div>
                </div>
                
                <div class="card-body">
                    <div class="row">
                        <!-- Product Information -->
                        <div class="col-lg-8">
                            <!-- Basic Information -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="mb-0"><i class="fas fa-info-circle"></i> Basic Information</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <table class="table table-borderless">
                                                <tr>
                                                    <td class="fw-bold">Product Name:</td>
                                                    <td>{{ $product->name }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-bold">SKU:</td>
                                                    <td><code>{{ $product->sku }}</code></td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-bold">Barcode:</td>
                                                    <td>{{ $product->barcode ?? '-' }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-bold">Category:</td>
                                                    <td>{{ $product->category->name ?? '-' }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-bold">Brand:</td>
                                                    <td>{{ $product->brand->name ?? '-' }}</td>
                                                </tr>
                                            </table>
                                        </div>
                                        <div class="col-md-6">
                                            <table class="table table-borderless">
                                                <tr>
                                                    <td class="fw-bold">Unit:</td>
                                                    <td>{{ $product->unit->name ?? '-' }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-bold">Status:</td>
                                                    <td>
                                                        @if($product->status)
                                                            <span class="badge bg-success">Active</span>
                                                        @else
                                                            <span class="badge bg-secondary">Inactive</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-bold">Track Quantity:</td>
                                                    <td>
                                                        @if($product->track_quantity)
                                                            <span class="badge bg-success">Yes</span>
                                                        @else
                                                            <span class="badge bg-secondary">No</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-bold">Has Expiry:</td>
                                                    <td>
                                                        @if($product->has_expiry)
                                                            <span class="badge bg-warning">Yes</span>
                                                        @else
                                                            <span class="badge bg-secondary">No</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-bold">Created:</td>
                                                    <td>{{ $product->created_at->format('M d, Y') }}</td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                    
                                    @if($product->description)
                                        <div class="mt-3">
                                            <h6>Description:</h6>
                                            <p class="text-muted">{{ $product->description }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- Pricing Information -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="mb-0"><i class="fas fa-dollar-sign"></i> Pricing Information</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="text-center">
                                                <h6 class="text-muted">Purchase Price</h6>
                                                <h4 class="text-primary">৳{{ number_format($product->purchase_price, 2) }}</h4>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="text-center">
                                                <h6 class="text-muted">Selling Price</h6>
                                                <h4 class="text-success">৳{{ number_format($product->selling_price, 2) }}</h4>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="text-center">
                                                <h6 class="text-muted">Profit Margin</h6>
                                                <h4 class="text-info">
                                                    ৳{{ number_format($product->selling_price - $product->purchase_price, 2) }}
                                                </h4>
                                                <small class="text-muted">
                                                    ({{ number_format((($product->selling_price - $product->purchase_price) / $product->purchase_price) * 100, 1) }}%)
                                                </small>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="text-center">
                                                <h6 class="text-muted">Stock Value</h6>
                                                <h4 class="text-warning">
                                                    ৳{{ number_format(($product->total_stock ?? 0) * $product->purchase_price, 2) }}
                                                </h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Stock Information -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="mb-0"><i class="fas fa-boxes"></i> Stock Information</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="text-center">
                                                <h6 class="text-muted">Total Stock</h6>
                                                <h4 class="text-primary">{{ $product->total_stock ?? 0 }}</h4>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="text-center">
                                                <h6 class="text-muted">Available Stock</h6>
                                                <h4 class="text-success">{{ $product->available_stock ?? 0 }}</h4>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="text-center">
                                                <h6 class="text-muted">Reserved Stock</h6>
                                                <h4 class="text-warning">{{ ($product->total_stock ?? 0) - ($product->available_stock ?? 0) }}</h4>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="text-center">
                                                <h6 class="text-muted">Stock Status</h6>
                                                @php
                                                    $stockLevel = $product->total_stock ?? 0;
                                                @endphp
                                                @if($stockLevel <= 0)
                                                    <h4 class="text-danger">Out of Stock</h4>
                                                @else
                                                    <h4 class="text-success">In Stock</h4>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    

                                </div>
                            </div>
                        </div>
                        
                        <!-- Product Images -->
                        <div class="col-lg-4">
                            @if($product->files->where('type', 'image')->count() > 0)
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h5 class="mb-0"><i class="fas fa-images"></i> Product Images</h5>
                                    </div>
                                    <div class="card-body">
                                        <div id="productCarousel" class="carousel slide" data-bs-ride="carousel">
                                            <div class="carousel-inner">
                                                @foreach($product->files->where('type', 'image') as $index => $file)
                                                    <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                                                        <img src="{{ $file->url }}" class="d-block w-100" 
                                                             style="height: 300px; object-fit: cover;" 
                                                             alt="{{ $file->original_name }}">
                                                    </div>
                                                @endforeach
                                            </div>
                                            @if($product->files->where('type', 'image')->count() > 1)
                                                <button class="carousel-control-prev" type="button" data-bs-target="#productCarousel" data-bs-slide="prev">
                                                    <span class="carousel-control-prev-icon"></span>
                                                </button>
                                                <button class="carousel-control-next" type="button" data-bs-target="#productCarousel" data-bs-slide="next">
                                                    <span class="carousel-control-next-icon"></span>
                                                </button>
                                            @endif
                                        </div>
                                        
                                        <!-- Thumbnail Navigation -->
                                        @if($product->files->where('type', 'image')->count() > 1)
                                            <div class="row mt-3">
                                                @foreach($product->files->where('type', 'image') as $index => $file)
                                                    <div class="col-3">
                                                        <img src="{{ $file->url }}" class="img-thumbnail" 
                                                             style="height: 60px; object-fit: cover; cursor: pointer;" 
                                                             onclick="$('#productCarousel').carousel({{ $index }})">
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif
                            
                            <!-- Product Documents -->
                            @if($product->files->where('type', 'document')->count() > 0)
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h5 class="mb-0"><i class="fas fa-file-alt"></i> Documents</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="list-group list-group-flush">
                                            @foreach($product->files->where('type', 'document') as $file)
                                                <a href="{{ $file->url }}" target="_blank" class="list-group-item list-group-item-action">
                                                    <div class="d-flex align-items-center">
                                                        <i class="fas fa-file-alt me-3 text-primary"></i>
                                                        <div>
                                                            <div class="fw-bold">{{ $file->original_name }}</div>
                                                            <small class="text-muted">{{ $file->formatted_size }}</small>
                                                        </div>
                                                    </div>
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endif
                            
                            <!-- Quick Actions -->
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0"><i class="fas fa-cogs"></i> Quick Actions</h5>
                                </div>
                                <div class="card-body">
                                    <div class="d-grid gap-2">
                                        <a href="{{ route('products.edit', $product) }}" class="btn btn-primary">
                                            <i class="fas fa-edit"></i> Edit Product
                                        </a>
                                        <a href="{{ route('inventory.batches', ['product' => $product->id]) }}" class="btn btn-info">
                                            <i class="fas fa-layer-group"></i> View Batches
                                        </a>
                                        <a href="{{ route('inventory.transactions', ['product' => $product->id]) }}" class="btn btn-secondary">
                                            <i class="fas fa-exchange-alt"></i> Stock Transactions
                                        </a>
                                        <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#adjustStockModal">
                                            <i class="fas fa-adjust"></i> Adjust Stock
                                        </button>
                                        <form action="{{ route('products.destroy', $product) }}" method="POST" 
                                              onsubmit="return confirm('Are you sure you want to delete this product? This action cannot be undone.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger w-100">
                                                <i class="fas fa-trash"></i> Delete Product
                                            </button>
                                        </form>
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

<!-- Stock Adjustment Modal -->
<div class="modal fade" id="adjustStockModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('inventory.adjustments') }}" method="POST">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                
                <div class="modal-header">
                    <h5 class="modal-title">Adjust Stock for {{ $product->name }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                
                <div class="modal-body">

                    
                    <div class="mb-3">
                        <label for="adjustment_type" class="form-label">Adjustment Type <span class="text-danger">*</span></label>
                        <select class="form-select" id="adjustment_type" name="adjustment_type" required>
                            <option value="increase">Increase Stock</option>
                            <option value="decrease">Decrease Stock</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="quantity" class="form-label">Quantity <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="quantity" name="quantity" min="1" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="reason" class="form-label">Reason</label>
                        <textarea class="form-control" id="reason" name="reason" rows="3" placeholder="Reason for stock adjustment..."></textarea>
                    </div>
                    
                    @if($product->has_expiry)
                        <div class="mb-3">
                            <label for="expiry_date" class="form-label">Expiry Date</label>
                            <input type="date" class="form-control" id="expiry_date" name="expiry_date">
                        </div>
                    @endif
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Adjust Stock</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection