@extends('layouts.app')

@section('title', 'CSV Import - Products')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">CSV Import</h3>
                    <div class="card-tools">
                        <a href="{{ route('products.import.index') }}" class="btn btn-sm btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle"></i> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <h6><i class="fas fa-exclamation-triangle"></i> Validation Errors:</h6>
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    
                    <!-- File Upload Section -->
                    <div class="row">
                        <div class="col-md-8">
                            <form action="{{ route('products.import.csv.process') }}" method="POST" enctype="multipart/form-data" id="csvImportForm">
                                @csrf
                                <input type="hidden" name="import_type" value="csv">
                                
                                <div class="row">
                                    <div class="col-12">
                                        <div class="mb-4">
                                            <label for="csvFile" class="form-label">
                                                <i class="fas fa-file-csv text-success"></i> Select CSV File
                                            </label>
                                            <input type="file" class="form-control" id="csvFile" name="file" accept=".csv" required>
                                            <div class="form-text">
                                                Maximum file size: 10MB. Only CSV files are allowed.
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Import Options -->
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="hasHeaders" class="form-label">CSV Options</label>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="hasHeaders" name="has_headers" value="1" checked>
                                                <label class="form-check-label" for="hasHeaders">
                                                    First row contains headers
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="delimiter" class="form-label">Delimiter</label>
                                            <select class="form-select" id="delimiter" name="delimiter">
                                                <option value="," selected>Comma (,)</option>
                                                <option value=";">Semicolon (;)</option>
                                                <option value="|">Pipe (|)</option>
                                                <option value="	">Tab</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <!-- Import Mode -->
                                <div class="row">
                                    <div class="col-12">
                                        <div class="mb-3">
                                            <label class="form-label">Import Mode</label>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="import_mode" id="modeInsert" value="insert" checked>
                                                        <label class="form-check-label" for="modeInsert">
                                                            <i class="fas fa-plus text-success"></i> Insert Only
                                                            <small class="d-block text-muted">Add new products only</small>
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="import_mode" id="modeUpdate" value="update">
                                                        <label class="form-check-label" for="modeUpdate">
                                                            <i class="fas fa-edit text-warning"></i> Update Only
                                                            <small class="d-block text-muted">Update existing products</small>
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="import_mode" id="modeUpsert" value="upsert">
                                                        <label class="form-check-label" for="modeUpsert">
                                                            <i class="fas fa-sync text-info"></i> Insert + Update
                                                            <small class="d-block text-muted">Insert new, update existing</small>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Submit Button -->
                                <div class="row">
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-success" id="importBtn">
                                            <i class="fas fa-upload"></i> Import Products
                                        </button>
                                        <button type="button" class="btn btn-secondary ms-2" onclick="resetForm()">
                                            <i class="fas fa-redo"></i> Reset
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        
                        <!-- Help Section -->
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-header">
                                    <h6 class="card-title mb-0">
                                        <i class="fas fa-info-circle text-info"></i> CSV Format Guide
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <h6>Required Columns:</h6>
                                    <ul class="small">
                                        <li><strong>name</strong> - Product name</li>
                                        <li><strong>price</strong> - Product price</li>
                                        <li><strong>category</strong> - Product category</li>
                                    </ul>
                                    
                                    <h6>Optional Columns:</h6>
                                    <ul class="small">
                                        <li><strong>sku</strong> - Product SKU</li>
                                        <li><strong>description</strong> - Product description</li>
                                        <li><strong>stock_quantity</strong> - Inventory count</li>
                                        <li><strong>brand</strong> - Product brand</li>
                                        <li><strong>weight</strong> - Product weight</li>
                                        <li><strong>image_url</strong> - Product image URL</li>
                                        <li><strong>status</strong> - active/inactive</li>
                                    </ul>
                                    
                                    <div class="mt-3">
                                        <a href="#" class="btn btn-sm btn-outline-primary" onclick="downloadSampleCSV()">
                                            <i class="fas fa-download"></i> Download Sample CSV
                                        </a>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Import Progress -->
                            <div class="card mt-3" id="progressCard" style="display: none;">
                                <div class="card-header">
                                    <h6 class="card-title mb-0">
                                        <i class="fas fa-spinner fa-spin text-primary"></i> Import Progress
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="progress mb-2">
                                        <div class="progress-bar progress-bar-striped progress-bar-animated" 
                                             role="progressbar" style="width: 0%" id="progressBar">
                                        </div>
                                    </div>
                                    <div class="text-center">
                                        <small id="progressText">Preparing import...</small>
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
@endsection

@push('scripts')
<script>
function downloadTemplate() {
    // TODO: Implement CSV template download
    alert('CSV template download feature coming soon!');
}

function previewFile() {
    const fileInput = document.getElementById('csv_file');
    if (!fileInput.files[0]) {
        alert('Please select a file first.');
        return;
    }
    
    // TODO: Implement file preview functionality
    alert('File preview feature coming soon!');
}

// TODO: Add real-time import progress tracking
console.log('CSV Import page loaded');
</script>
@endpush