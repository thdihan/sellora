@extends('layouts.app')

@section('title', 'SQL Import - Products')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">SQL Import</h3>
                    <div class="card-tools">
                        <a href="{{ route('products.import.index') }}" class="btn btn-sm btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    
                    <form action="{{ route('products.import.sql.process') }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="sql_query" class="form-label">SQL Query</label>
                                    <textarea 
                                        class="form-control @error('sql_query') is-invalid @enderror" 
                                        id="sql_query" 
                                        name="sql_query" 
                                        rows="15" 
                                        placeholder="Enter your SQL query here..."
                                        required>{{ old('sql_query') }}</textarea>
                                    @error('sql_query')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="validate_query" name="validate_query" checked>
                                        <label class="form-check-label" for="validate_query">
                                            Validate query before execution
                                        </label>
                                    </div>
                                </div>
                                
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-play"></i> Execute Import
                                    </button>
                                    <button type="button" class="btn btn-secondary" onclick="validateQuery()">
                                        <i class="fas fa-check"></i> Validate Query
                                    </button>
                                    <button type="button" class="btn btn-info" onclick="loadSample()">
                                        <i class="fas fa-file-code"></i> Load Sample
                                    </button>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Query Guidelines</h5>
                                    </div>
                                    <div class="card-body">
                                        <h6>Required Fields:</h6>
                                        <ul class="small">
                                            <li>name (required)</li>
                                            <li>sku (unique)</li>
                                            <li>category_id</li>
                                            <li>brand_id</li>
                                            <li>unit_id</li>
                                            <li>price</li>
                                        </ul>
                                        
                                        <h6>Optional Fields:</h6>
                                        <ul class="small">
                                            <li>description</li>
                                            <li>barcode</li>
                                            <li>tax_rate</li>
                                            <li>is_taxable</li>
                                            <li>status</li>
                                            <li>stock_quantity</li>
                                        </ul>
                                        
                                        <div class="alert alert-warning small mt-3">
                                            <strong>Warning:</strong> SQL import directly modifies the database. Always backup your data before running imports.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function validateQuery() {
    // TODO: Implement query validation logic
    alert('Query validation feature coming soon!');
}

function loadSample() {
    const sampleQuery = `INSERT INTO products (name, sku, category_id, brand_id, unit_id, price, description, status, created_at, updated_at)
VALUES 
('Sample Product 1', 'SKU001', 1, 1, 1, 15.00, 'Sample product description', 1, NOW(), NOW()),
('Sample Product 2', 'SKU002', 1, 1, 1, 30.00, 'Another sample product', 1, NOW(), NOW());`;
    
    document.getElementById('sql_query').value = sampleQuery;
}

// TODO: Add syntax highlighting for SQL queries
console.log('SQL Import page loaded');
</script>
@endpush