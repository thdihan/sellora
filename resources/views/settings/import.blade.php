@extends('layouts.app')

@section('title', 'Data Import')

@section('content')
@if(!auth()->user() || !auth()->user()->hasAnyRole(['Admin', 'Author', 'Manager']))
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="alert alert-danger" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Access Denied:</strong> You don't have permission to access the data import functionality. 
                    Please contact your administrator if you need access.
                </div>
                <a href="{{ route('settings.index') }}" class="btn btn-secondary">
                    Back to Settings
                </a>
            </div>
        </div>
    </div>
@else
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title mb-0">Data Import</h3>
                    <p class="text-muted mb-0">Import data from CSV, Excel, or SQL files into your system</p>
                </div>
                <div class="card-body">
                    <!-- Import Options -->
                    <div class="row g-4 mb-4">
                        <div class="col-12 col-md-6 col-lg-4">
                            <div class="card h-100 border-2 source-option" data-source="csv" style="cursor: pointer;">
                                <div class="card-body text-center">
                                    <div class="mb-3">
                                        <i class="fas fa-file-csv fa-3x text-success"></i>
                                    </div>
                                    <h5 class="card-title">CSV File</h5>
                                    <p class="card-text text-muted">Import from comma-separated values file</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-12 col-md-6 col-lg-4">
                            <div class="card h-100 border-2 source-option" data-source="excel" style="cursor: pointer;">
                                <div class="card-body text-center">
                                    <div class="mb-3">
                                        <i class="fas fa-file-excel fa-3x text-primary"></i>
                                    </div>
                                    <h5 class="card-title">Excel File</h5>
                                    <p class="card-text text-muted">Import from Excel spreadsheet (.xlsx, .xls)</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-12 col-md-6 col-lg-4">
                            <div class="card h-100 border-2 source-option" data-source="sql" style="cursor: pointer;">
                                <div class="card-body text-center">
                                    <div class="mb-3">
                                        <i class="fas fa-database fa-3x text-info"></i>
                                    </div>
                                    <h5 class="card-title">SQL Dump</h5>
                                    <p class="card-text text-muted">Import from SQL database dump file</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Target Module Selection -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Select Target Module</h5>
                                </div>
                                <div class="card-body">
                                    <select id="module" class="form-select">
                                        <option value="">Choose module to import data into...</option>
                                        <option value="products">Products</option>
                                        <option value="customers">Customers</option>
                                        <option value="orders">Orders</option>
                                        <option value="bills">Bills</option>
                                        <option value="users">Users</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- File Upload Section -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Upload Your File</h5>
                                </div>
                                <div class="card-body">
                                    <div id="upload-area" class="border border-2 border-dashed rounded p-4 text-center" style="cursor: pointer; min-height: 200px; display: flex; align-items: center; justify-content: center;">
                                        <input type="file" id="file-input" class="d-none" accept=".csv,.xlsx,.xls,.sql">
                                        <div>
                                            <h5 class="mb-2">Drop your file here or click to browse</h5>
                                            <p class="text-muted mb-1">
                                                Supported formats: <strong>CSV</strong>, <strong>Excel (.xlsx, .xls)</strong>, <strong>SQL</strong>
                                            </p>
                                            <p class="text-muted small">CSV, Excel, or SQL files up to 10MB</p>
                                        </div>
                                    </div>
                                    <div id="file-info" class="d-none mt-3">
                                        <div class="d-flex align-items-center justify-content-center">
                                            <span class="text-success me-3" style="font-size: 2rem;">✓</span>
                                            <div>
                                                <p class="mb-0 fw-medium" id="file-name"></p>
                                                <p class="mb-0 text-muted small" id="file-size"></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('settings.index') }}" class="btn btn-secondary">
                                    Back to Settings
                                </a>
                                <button id="start-import" class="btn btn-primary" disabled>
                            Start Import
                        </button>
                            </div>
                        </div>
                    </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let selectedSource = null;
    let selectedModule = null;
    let uploadedFile = null;
    
    const sourceOptions = document.querySelectorAll('.source-option');
    const moduleSelect = document.getElementById('module');
    const fileInput = document.getElementById('file-input');
    const uploadArea = document.getElementById('upload-area');
    const fileInfo = document.getElementById('file-info');
    const startImportBtn = document.getElementById('start-import');
    
    // Source selection
    sourceOptions.forEach(option => {
        option.addEventListener('click', function() {
            sourceOptions.forEach(opt => {
                opt.classList.remove('border-primary', 'bg-light');
            });
            this.classList.add('border-primary', 'bg-light');
            selectedSource = this.dataset.source;
            updateImportButton();
        });
    });
    
    // Module selection
    moduleSelect.addEventListener('change', function() {
        selectedModule = this.value;
        updateImportButton();
    });
    
    // File upload
    uploadArea.addEventListener('click', () => fileInput.click());
    
    fileInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            uploadedFile = file;
            document.getElementById('file-name').textContent = file.name;
            document.getElementById('file-size').textContent = formatFileSize(file.size);
            uploadArea.style.display = 'none';
            fileInfo.classList.remove('d-none');
            updateImportButton();
        }
    });
    
    // Start import
    startImportBtn.addEventListener('click', function() {
        if (selectedSource && selectedModule && uploadedFile) {
            // Handle import logic here
            alert('Import functionality would be implemented here');
        }
    });
    
    function updateImportButton() {
        const canImport = selectedSource && selectedModule && uploadedFile;
        startImportBtn.disabled = !canImport;
    }
    
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }
});
</script>
@endif
@endsection