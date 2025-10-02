@extends('layouts.app')

@section('title', 'Product Category Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">Category: {{ $productCategory->name }}</h3>
                    <div class="btn-group">
                        <a href="{{ route('product-categories.edit', $productCategory) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <a href="{{ route('product-categories.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Categories
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Category Information</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>ID:</strong></td>
                                    <td>{{ $productCategory->id }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Name:</strong></td>
                                    <td>{{ $productCategory->name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Status:</strong></td>
                                    <td>
                                        @if($productCategory->status)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-danger">Inactive</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Description:</strong></td>
                                    <td>{{ $productCategory->description ?: 'No description provided' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Created At:</strong></td>
                                    <td>{{ $productCategory->created_at->format('M d, Y H:i A') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Updated At:</strong></td>
                                    <td>{{ $productCategory->updated_at->format('M d, Y H:i A') }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5>Statistics</h5>
                            <div class="row">
                                <div class="col-6">
                                    <div class="card bg-primary text-white">
                                        <div class="card-body text-center">
                                            <h3>{{ $productCategory->products->count() }}</h3>
                                            <p class="mb-0">Total Products</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="card bg-success text-white">
                                        <div class="card-body text-center">
                                            <h3>{{ $productCategory->products->where('status', true)->count() }}</h3>
                                            <p class="mb-0">Active Products</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    @if($productCategory->products->count() > 0)
                        <hr>
                        <h5>Products in this Category</h5>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead class="table-dark">
                                    <tr>
                                        <th>SKU</th>
                                        <th>Name</th>
                                        <th>Brand</th>
                                        <th>Selling Price</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($productCategory->products->take(10) as $product)
                                        <tr>
                                            <td>{{ $product->sku }}</td>
                                            <td>{{ $product->name }}</td>
                                            <td>{{ $product->brand->name ?? 'N/A' }}</td>
                                            <td>${{ number_format($product->selling_price, 2) }}</td>
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
                            @if($productCategory->products->count() > 10)
                                <p class="text-muted">Showing first 10 products. <a href="{{ route('products.index', ['category_id' => $productCategory->id]) }}">View all products in this category</a></p>
                            @endif
                        </div>
                    @else
                        <hr>
                        <div class="text-center py-4">
                            <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No products in this category yet.</p>
                            <a href="{{ route('products.create') }}" class="btn btn-primary">Add First Product</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection