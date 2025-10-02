@extends('layouts.app')

@section('title', 'Brand Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">Brand Details</h3>
                    <div>
                        <a href="{{ route('product-brands.edit', $brand) }}" class="btn btn-warning me-2">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <a href="{{ route('product-brands.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Brands
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <h4>{{ $brand->name }}</h4>
                            <p class="text-muted mb-3">{{ $brand->description ?: 'No description available.' }}</p>
                            
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <strong>Status:</strong>
                                    @if($brand->status)
                                        <span class="badge bg-success ms-2">Active</span>
                                    @else
                                        <span class="badge bg-danger ms-2">Inactive</span>
                                    @endif
                                </div>
                                <div class="col-md-6">
                                    <strong>Created:</strong> {{ $brand->created_at->format('M d, Y \\a\\t g:i A') }}
                                </div>
                            </div>
                            
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <strong>Last Updated:</strong> {{ $brand->updated_at->format('M d, Y \\a\\t g:i A') }}
                                </div>
                                <div class="col-md-6">
                                    <strong>Total Products:</strong> {{ $brand->products->count() }}
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h5 class="card-title">Quick Stats</h5>
                                    <div class="row">
                                        <div class="col-12 mb-2">
                                            <div class="h3 text-primary">{{ $brand->products->count() }}</div>
                                            <small class="text-muted">Total Products</small>
                                        </div>
                                        <div class="col-12 mb-2">
                                            <div class="h3 text-success">{{ $brand->products->where('status', 1)->count() }}</div>
                                            <small class="text-muted">Active Products</small>
                                        </div>
                                        <div class="col-12">
                                            <div class="h3 text-warning">{{ $brand->products->where('stock_quantity', '>', 0)->count() }}</div>
                                            <small class="text-muted">In Stock</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    @if($brand->products->count() > 0)
                        <hr>
                        <h5>Products in this Brand</h5>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Product Name</th>
                                        <th>SKU</th>
                                        <th>Price</th>
                                        <th>Stock</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($brand->products->take(10) as $product)
                                        <tr>
                                            <td>{{ $product->name }}</td>
                                            <td>{{ $product->sku }}</td>
                                            <td>${{ number_format($product->selling_price, 2) }}</td>
                                            <td>{{ $product->stock_quantity }}</td>
                                            <td>
                                                @if($product->status)
                                                    <span class="badge bg-success">Active</span>
                                                @else
                                                    <span class="badge bg-danger">Inactive</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('products.show', $product) }}" class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            @if($brand->products->count() > 10)
                                <p class="text-muted">Showing 10 of {{ $brand->products->count() }} products. <a href="{{ route('products.index', ['brand' => $brand->id]) }}">View all products</a></p>
                            @endif
                        </div>
                    @else
                        <hr>
                        <div class="text-center py-4">
                            <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No products found for this brand.</p>
                            <a href="{{ route('products.create', ['brand' => $brand->id]) }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Add Product
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection