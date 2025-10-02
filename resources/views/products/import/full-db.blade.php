@extends('layouts.app')

@section('title', 'Full Database Import - Products')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Full Database Import</h3>
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
                    
                    <div class="alert alert-danger">
                        <h5><i class="fas fa-exclamation-triangle"></i> Warning</h5>
                        <p class="mb-0">This operation will completely replace your current product database. <strong>All existing product data will be lost.</strong> Please ensure you have a backup before proceeding.</p>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-8">
                            <form action="{{ route('products.import.full-db.process') }}" method="POST" enctype="multipart/form-data" onsubmit="return confirmImport()">
                                @csrf
                                
                                <div class="mb-3">
                                    <label for="db_file" class="form-label">Select Database File</label>
                                    <input 
                                        type="file" 
                                        class="form-control @error('db_file') is-invalid @enderror" 
                                        id="db_file" 
                                        name="db_file" 
                                        accept=".sql,.db,.dump"
                                        required>
                                    @error('db_file')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Supported formats: SQL, DB, DUMP files</div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="import_type" class="form-label">Import Type</label>
                                    <select class="form-select" id="import_type" name="import_type" required>
                                        <option value="">Select import type...</option>
                                        <option value="replace">Replace All Data (Complete Restore)</option>
                                        <option value="merge">Merge with Existing Data</option>
                                        <option value="products_only">Products Table Only</option>
                                    </select>
                                    <div class="form-text">Choose how to handle existing data</div>
                                </div>
                                
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="backup_current" name="backup_current" checked>
                                        <label class="form-check-label" for="backup_current">
                                            Create backup of current data before import
                                        </label>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="validate_structure" name="validate_structure" checked>
                                        <label class="form-check-label" for="validate_structure">
                                            Validate database structure before import
                                        </label>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="confirm_understanding" name="confirm_understanding" required>
                                        <label class="form-check-label" for="confirm_understanding">
                                            <strong>I understand this will replace my current product data</strong>
                                        </label>
                                    </div>
                                </div>
                                
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-danger">
                                        <i class="fas fa-database"></i> Start Full Import
                                    </button>
                                    <button type="button" class="btn btn-info" onclick="validateFile()">
                                        <i class="fas fa-check"></i> Validate File
                                    </button>
                                    <button type="button" class="btn btn-secondary" onclick="createBackup()">
                                        <i class="fas fa-save"></i> Create Backup First
                                    </button>
                                </div>
                            </form>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Import Guidelines</h5>
                                </div>
                                <div class="card-body">
                                    <h6>Supported File Types:</h6>
                                    <ul class="small">
                                        <li><strong>SQL:</strong> MySQL dump files</li>
                                        <li><strong>DB:</strong> SQLite database files</li>
                                        <li><strong>DUMP:</strong> Database backup files</li>
                                    </ul>
                                    
                                    <h6>Import Types:</h6>
                                    <ul class="small">
                                        <li><strong>Replace All:</strong> Complete database restore</li>
                                        <li><strong>Merge:</strong> Add to existing data</li>
                                        <li><strong>Products Only:</strong> Import products table only</li>
                                    </ul>
                                    
                                    <div class="alert alert-info small mt-3">
                                        <strong>Tip:</strong> Always create a backup before performing full database imports.
                                    </div>
                                </div>
                            </div>
                            
                            <div class="card mt-3">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">File Information</h5>
                                </div>
                                <div class="card-body">
                                    <div id="file-info" class="d-none">
                                        <div class="mb-2">
                                            <strong>File Size:</strong> <span id="file-size">-</span>
                                        </div>
                                        <div class="mb-2">
                                            <strong>File Type:</strong> <span id="file-type">-</span>
                                        </div>
                                        <div class="mb-2">
                                            <strong>Last Modified:</strong> <span id="file-modified">-</span>
                                        </div>
                                    </div>
                                    <div id="file-status" class="text-muted small">
                                        Select a file to view information
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
                                            <div class="progress-bar progress-bar-striped progress-bar-animated bg-danger" role="progressbar" style="width: 0%"></div>
                                        </div>
                                        <small class="text-muted">Importing database...</small>
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
function confirmImport() {
    const confirmation = confirm('Are you absolutely sure you want to proceed with the full database import? This action cannot be undone.');
    if (confirmation) {
        return confirm('This will permanently replace your current product data. Type "CONFIRM" to proceed.');
    }
    return false;
}

function validateFile() {
    const fileInput = document.getElementById('db_file');
    if (!fileInput.files[0]) {
        alert('Please select a file first.');
        return;
    }
    
    // TODO: Implement database file validation
    alert('File validation feature coming soon!');
}

function createBackup() {
    // TODO: Implement backup creation
    alert('Backup creation feature coming soon!');
}

// File info display
document.getElementById('db_file').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        document.getElementById('file-size').textContent = (file.size / 1024 / 1024).toFixed(2) + ' MB';
        document.getElementById('file-type').textContent = file.type || 'Unknown';
        document.getElementById('file-modified').textContent = new Date(file.lastModified).toLocaleString();
        document.getElementById('file-info').classList.remove('d-none');
        document.getElementById('file-status').classList.add('d-none');
    }
});

// TODO: Add real-time import progress and validation
console.log('Full Database Import page loaded');
</script>
@endpush