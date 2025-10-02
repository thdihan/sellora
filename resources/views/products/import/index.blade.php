@extends('layouts.app')

@section('title', 'Product Import')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Product Import Options</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 col-lg-3 mb-4">
                            <div class="card h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-database fa-3x text-primary mb-3"></i>
                                    <h5 class="card-title">SQL Import</h5>
                                    <p class="card-text">Import products using custom SQL queries</p>
                                    <a href="{{ route('products.import.sql') }}" class="btn btn-primary">Start SQL Import</a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6 col-lg-3 mb-4">
                            <div class="card h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-file-csv fa-3x text-success mb-3"></i>
                                    <h5 class="card-title">CSV Import</h5>
                                    <p class="card-text">Import products from CSV files</p>
                                    <a href="{{ route('products.import.csv') }}" class="btn btn-success">Start CSV Import</a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6 col-lg-3 mb-4">
                            <div class="card h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-file-excel fa-3x text-warning mb-3"></i>
                                    <h5 class="card-title">Excel Import</h5>
                                    <p class="card-text">Import products from Excel files</p>
                                    <a href="{{ route('products.import.excel') }}" class="btn btn-warning">Start Excel Import</a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6 col-lg-3 mb-4">
                            <div class="card h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-server fa-3x text-danger mb-3"></i>
                                    <h5 class="card-title">Full DB Import</h5>
                                    <p class="card-text">Import complete database backup</p>
                                    <a href="{{ route('products.import.full-db') }}" class="btn btn-danger">Start Full Import</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="alert alert-info">
                                <h5><i class="fas fa-info-circle"></i> Import Guidelines</h5>
                                <ul class="mb-0">
                                    <li><strong>SQL Import:</strong> Use for custom database queries and complex data transformations</li>
                                    <li><strong>CSV Import:</strong> Best for simple product lists with standard fields</li>
                                    <li><strong>Excel Import:</strong> Supports multiple sheets and complex formatting</li>
                                    <li><strong>Full DB Import:</strong> Complete database restoration from backup files</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-12">
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('products.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Back to Products
                                </a>
                                <a href="{{ route('products.sync.index') }}" class="btn btn-info">
                                    <i class="fas fa-sync"></i> API Sync
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// TODO: Add import progress tracking and validation
console.log('Product Import Index loaded');
</script>
@endpush