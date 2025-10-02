@extends('layouts.app')

@section('title', 'Excel Import - Products')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Excel Import</h3>
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
                    
                    <div class="row">
                        <div class="col-md-8">
                            <form action="{{ route('products.import.excel.process') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="import_type" value="excel">
                                
                                <div class="mb-3">
                                    <label for="excel_file" class="form-label">Select Excel File</label>
                                    <input 
                                        type="file" 
                                        class="form-control @error('excel_file') is-invalid @enderror" 
                                        id="excel_file" 
                                        name="file" 
                                        accept=".xlsx,.xls"
                                        required>
                                    @error('excel_file')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Supported formats: XLSX, XLS</div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="sheet_name" class="form-label">Worksheet Name</label>
                                    <input 
                                        type="text" 
                                        class="form-control" 
                                        id="sheet_name" 
                                        name="sheet_name" 
                                        placeholder="Leave empty to use first sheet"
                                        value="{{ old('sheet_name') }}">
                                    <div class="form-text">Specify which worksheet to import (optional)</div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="start_row" class="form-label">Start Row</label>
                                    <input 
                                        type="number" 
                                        class="form-control" 
                                        id="start_row" 
                                        name="start_row" 
                                        value="2" 
                                        min="1">
                                    <div class="form-text">Row number to start importing from (default: 2)</div>
                                </div>
                                
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="has_header" name="has_header" checked>
                                        <label class="form-check-label" for="has_header">
                                            File has header row
                                        </label>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="update_existing" name="update_existing">
                                        <label class="form-check-label" for="update_existing">
                                            Update existing products (match by SKU)
                                        </label>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="skip_errors" name="skip_errors" checked>
                                        <label class="form-check-label" for="skip_errors">
                                            Skip rows with errors and continue
                                        </label>
                                    </div>
                                </div>
                                
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        Import Excel
                                    </button>
                                    <button type="button" class="btn btn-info" onclick="downloadTemplate()">
                                        <i class="fas fa-download"></i> Download Template
                                    </button>
                                    <button type="button" class="btn btn-secondary" onclick="analyzeFile()">
                                        <i class="fas fa-search"></i> Analyze File
                                    </button>
                                </div>
                            </form>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Excel Format Guide</h5>
                                </div>
                                <div class="card-body">
                                    <h6>Column Headers (Row 1):</h6>
                                    <ul class="small">
                                        <li>A: name</li>
                                        <li>B: sku</li>
                                        <li>C: category_id</li>
                                        <li>D: brand_id</li>
                                        <li>E: unit_id</li>
                                        <li>F: price</li>
                                        <li>G: description</li>
                                        <li>H: barcode</li>
                                        <li>I: tax_rate</li>
                                        <li>J: is_taxable</li>
                                        <li>K: status</li>
                                        <li>L: stock_quantity</li>
                                    </ul>
                                    
                                    <div class="alert alert-warning small mt-3">
                                        <strong>Note:</strong> Excel files support multiple sheets. Specify the sheet name if needed.
                                    </div>
                                </div>
                            </div>
                            
                            <div class="card mt-3">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">File Analysis</h5>
                                </div>
                                <div class="card-body">
                                    <div id="file-analysis" class="d-none">
                                        <div class="mb-2">
                                            <strong>Sheets:</strong> <span id="sheet-count">-</span>
                                        </div>
                                        <div class="mb-2">
                                            <strong>Rows:</strong> <span id="row-count">-</span>
                                        </div>
                                        <div class="mb-2">
                                            <strong>Columns:</strong> <span id="column-count">-</span>
                                        </div>
                                        <div id="sheet-list"></div>
                                    </div>
                                    <div id="analysis-status" class="text-muted small">
                                        Select a file to analyze
                                    </div>
                                </div>
                            </div>
                            
                            <div class="card mt-3">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Import Progress</h5>
                                </div>
                                <div class="card-body">
                                    <div id="import-progress" class="d-none">
                                        <div class="progress mb-2">
                                            <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%"></div>
                                        </div>
                                        <small class="text-muted">Processing Excel file...</small>
                                    </div>
                                    <div id="import-status" class="text-muted small">
                                        No import in progress
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
    // TODO: Implement Excel template download
    alert('Excel template download feature coming soon!');
}

function analyzeFile() {
    const fileInput = document.getElementById('excel_file');
    if (!fileInput.files[0]) {
        alert('Please select a file first.');
        return;
    }
    
    // TODO: Implement Excel file analysis
    alert('File analysis feature coming soon!');
}

// TODO: Add Excel file preview and sheet selection
console.log('Excel Import page loaded');
</script>
@endpush