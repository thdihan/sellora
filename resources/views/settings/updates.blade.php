@extends('layouts.app')

@section('title', 'Application Updates')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3><i class="fas fa-sync-alt"></i> Application Updates</h3>
                    <p class="text-muted mb-0">Manage application updates, version control, and rollback functionality</p>
                </div>
                <a href="{{ route('settings.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Settings
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Current Version Info -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-info-circle"></i> Current Version Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Application Details</h6>
                            <ul class="list-unstyled">
                                <li><strong>Current Version:</strong> <span class="badge bg-primary">{{ $currentVersion['version'] ?? '1.0.0' }}</span></li>
                                <li><strong>Release Date:</strong> {{ $currentVersion['release_date'] ?? 'Unknown' }}</li>
                                <li><strong>Build Number:</strong> {{ $currentVersion['build'] ?? 'N/A' }}</li>
                                <li><strong>Environment:</strong> <span class="badge bg-{{ app()->environment() == 'production' ? 'success' : 'warning' }}">{{ ucfirst(app()->environment()) }}</span></li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6>System Information</h6>
                            <ul class="list-unstyled">
                                <li><strong>PHP Version:</strong> {{ PHP_VERSION }}</li>
                                <li><strong>Laravel Version:</strong> {{ app()->version() }}</li>
                                <li><strong>Database:</strong> {{ config('database.default') }}</li>
                                <li><strong>Last Updated:</strong> {{ $currentVersion['last_updated'] ?? 'Never' }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Upload Update -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Upload New Update</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning" role="alert">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Important:</strong> Always create a backup before applying updates. Updates cannot be undone without a backup.
                    </div>
                    
                    <form id="updateForm" action="{{ route('settings.updates.upload') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label for="update_file" class="form-label">Update Package (ZIP file)</label>
                                <input type="file" class="form-control" id="update_file" name="update_file" 
                                       accept=".zip" required>
                                <small class="text-muted">Upload a ZIP file containing the application update</small>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="version_number" class="form-label">Version Number</label>
                                <input type="text" class="form-control" id="version_number" name="version_number" 
                                       placeholder="e.g., 1.1.0" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="release_notes" class="form-label">Release Notes</label>
                            <textarea class="form-control" id="release_notes" name="release_notes" rows="4" 
                                      placeholder="Describe what's new in this version..."></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="create_backup" 
                                           name="create_backup" value="1" checked>
                                    <label class="form-check-label" for="create_backup">
                                        <strong>Create Backup Before Update</strong><br>
                                        <small class="text-muted">Automatically create a backup before applying the update</small>
                                    </label>
                                </div>
                                
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="run_migrations" 
                                           name="run_migrations" value="1" checked>
                                    <label class="form-check-label" for="run_migrations">
                                        <strong>Run Database Migrations</strong><br>
                                        <small class="text-muted">Execute database migrations if included in the update</small>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="clear_cache" 
                                           name="clear_cache" value="1" checked>
                                    <label class="form-check-label" for="clear_cache">
                                        <strong>Clear Application Cache</strong><br>
                                        <small class="text-muted">Clear all caches after applying the update</small>
                                    </label>
                                </div>
                                
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="maintenance_mode" 
                                           name="maintenance_mode" value="1" checked>
                                    <label class="form-check-label" for="maintenance_mode">
                                        <strong>Enable Maintenance Mode</strong><br>
                                        <small class="text-muted">Put application in maintenance mode during update</small>
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary" id="uploadBtn">
                            Upload and Apply Update
                        </button>
                            <button type="button" class="btn btn-info" id="validateBtn">
                                <i class="fas fa-check-circle"></i> Validate Package Only
                            </button>
                        </div>
                    </form>
                    
                    <!-- Progress Bar -->
                    <div id="updateProgress" class="mt-4" style="display: none;">
                        <h6>Update Progress</h6>
                        <div class="progress mb-2">
                            <div class="progress-bar progress-bar-striped progress-bar-animated" 
                                 role="progressbar" style="width: 0%" id="progressBar"></div>
                        </div>
                        <div id="progressText" class="text-muted">Preparing update...</div>
                    </div>
                </div>
            </div>
            
            <!-- Update Settings -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Update Settings</h5>
                </div>
                <div class="card-body">
                    <form id="updateSettingsForm" action="{{ route('settings.updates.settings') }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="auto_backup" 
                                           name="auto_backup" value="1" 
                                           {{ ($updateSettings['auto_backup'] ?? true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="auto_backup">
                                        <strong>Automatic Backup</strong><br>
                                        <small class="text-muted">Always create backup before updates</small>
                                    </label>
                                </div>
                                
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="update_notifications" 
                                           name="update_notifications" value="1" 
                                           {{ ($updateSettings['update_notifications'] ?? true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="update_notifications">
                                        <strong>Update Notifications</strong><br>
                                        <small class="text-muted">Send email notifications for updates</small>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="rollback_enabled" 
                                           name="rollback_enabled" value="1" 
                                           {{ ($updateSettings['rollback_enabled'] ?? true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="rollback_enabled">
                                        <strong>Enable Rollback</strong><br>
                                        <small class="text-muted">Allow rolling back to previous versions</small>
                                    </label>
                                </div>
                                
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="version_logging" 
                                           name="version_logging" value="1" 
                                           {{ ($updateSettings['version_logging'] ?? true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="version_logging">
                                        <strong>Version Logging</strong><br>
                                        <small class="text-muted">Log all version changes and updates</small>
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="max_rollback_versions" class="form-label">Max Rollback Versions</label>
                                <input type="number" class="form-control" id="max_rollback_versions" name="max_rollback_versions" 
                                       value="{{ old('max_rollback_versions', $updateSettings['max_rollback_versions'] ?? 5) }}" 
                                       min="1" max="20">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="update_timeout" class="form-label">Update Timeout (Minutes)</label>
                                <input type="number" class="form-control" id="update_timeout" name="update_timeout" 
                                       value="{{ old('update_timeout', $updateSettings['update_timeout'] ?? 30) }}" 
                                       min="5" max="120">
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Save Update Settings
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <!-- Version History -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-history"></i> Version History</h5>
                </div>
                <div class="card-body">
                    <div class="version-history">
                        @forelse($versionHistory ?? [] as $version)
                        <div class="d-flex justify-content-between align-items-start mb-3 p-2 border rounded {{ $version['is_current'] ? 'bg-light' : '' }}">
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center mb-1">
                                    <strong>v{{ $version['version'] }}</strong>
                                    @if($version['is_current'])
                                        <span class="badge bg-success ms-2">Current</span>
                                    @endif
                                </div>
                                <small class="text-muted d-block">{{ $version['date'] }}</small>
                                @if($version['notes'])
                                    <small class="text-muted">{{ Str::limit($version['notes'], 50) }}</small>
                                @endif
                            </div>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    @if(!$version['is_current'] && ($updateSettings['rollback_enabled'] ?? true))
                                        <li><a class="dropdown-item" href="#" onclick="rollbackToVersion('{{ $version['id'] }}')">Rollback to this version</a></li>
                                    @endif
                                    <li><a class="dropdown-item" href="#" onclick="viewVersionDetails('{{ $version['id'] }}')">View details</a></li>
                                    @if($version['backup_available'])
                                        <li><a class="dropdown-item" href="#" onclick="downloadBackup('{{ $version['backup_id'] }}')">Download backup</a></li>
                                    @endif
                                </ul>
                            </div>
                        </div>
                        @empty
                        <p class="text-muted">No version history available</p>
                        @endforelse
                    </div>
                </div>
            </div>
            
            <!-- Update Statistics -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-chart-bar"></i> Update Statistics</h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled">
                        <li><strong>Total Updates:</strong> <span class="badge bg-primary">{{ $updateStats['total_updates'] ?? 0 }}</span></li>
                        <li><strong>Successful Updates:</strong> <span class="badge bg-success">{{ $updateStats['successful'] ?? 0 }}</span></li>
                        <li><strong>Failed Updates:</strong> <span class="badge bg-danger">{{ $updateStats['failed'] ?? 0 }}</span></li>
                        <li><strong>Rollbacks:</strong> <span class="badge bg-warning">{{ $updateStats['rollbacks'] ?? 0 }}</span></li>
                        <li><strong>Last Update:</strong> {{ $updateStats['last_update'] ?? 'Never' }}</li>
                    </ul>
                </div>
            </div>
            
            <!-- Quick Actions -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-bolt"></i> Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button class="btn btn-outline-primary" onclick="checkForUpdates()">
                            <i class="fas fa-search"></i> Check for Updates
                        </button>
                        <button class="btn btn-outline-info" onclick="createBackup()">
                            <i class="fas fa-shield-alt"></i> Create Backup
                        </button>
                        <button class="btn btn-outline-warning" onclick="clearCache()">
                            <i class="fas fa-broom"></i> Clear Cache
                        </button>
                        <button class="btn btn-outline-secondary" onclick="viewLogs()">
                            <i class="fas fa-file-alt"></i> View Update Logs
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Rollback Confirmation Modal -->
<div class="modal fade" id="rollbackModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Rollback</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>Warning:</strong> Rolling back will revert your application to a previous version. This action cannot be undone.
                </div>
                <p>Are you sure you want to rollback to version <strong id="rollbackVersion"></strong>?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-warning" id="confirmRollback">Rollback</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const updateForm = document.getElementById('updateForm');
    const updateSettingsForm = document.getElementById('updateSettingsForm');
    const validateBtn = document.getElementById('validateBtn');
    const uploadBtn = document.getElementById('uploadBtn');
    const updateProgress = document.getElementById('updateProgress');
    const progressBar = document.getElementById('progressBar');
    const progressText = document.getElementById('progressText');
    
    // Validate package
    validateBtn.addEventListener('click', function() {
        const fileInput = document.getElementById('update_file');
        if (!fileInput.files[0]) {
            showAlert('warning', 'Please select an update package first.');
            return;
        }
        
        const formData = new FormData();
        formData.append('update_file', fileInput.files[0]);
        formData.append('action', 'validate');
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
        
        this.disabled = true;
        this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Validating...';
        
        fetch('{{ route("settings.updates.upload") }}', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('success', 'Package validation successful!');
            } else {
                showAlert('danger', data.message || 'Package validation failed.');
            }
        })
        .catch(error => {
            showAlert('danger', 'An error occurred during validation.');
        })
        .finally(() => {
            this.disabled = false;
            this.innerHTML = '<i class="fas fa-check-circle"></i> Validate Package Only';
        });
    });
    
    // Update form submission
    updateForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        if (!confirm('Are you sure you want to apply this update? This action cannot be undone without a backup.')) {
            return;
        }
        
        const formData = new FormData(this);
        
        uploadBtn.disabled = true;
        uploadBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Uploading...';
        updateProgress.style.display = 'block';
        
        // Simulate progress updates
        let progress = 0;
        const progressInterval = setInterval(() => {
            progress += Math.random() * 10;
            if (progress > 90) progress = 90;
            progressBar.style.width = progress + '%';
            
            if (progress < 30) {
                progressText.textContent = 'Uploading update package...';
            } else if (progress < 60) {
                progressText.textContent = 'Extracting files...';
            } else {
                progressText.textContent = 'Applying update...';
            }
        }, 500);
        
        fetch(this.action, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            clearInterval(progressInterval);
            progressBar.style.width = '100%';
            progressText.textContent = 'Update completed!';
            
            if (data.success) {
                showAlert('success', data.message);
                setTimeout(() => {
                    location.reload();
                }, 2000);
            } else {
                showAlert('danger', data.message || 'Update failed.');
            }
        })
        .catch(error => {
            clearInterval(progressInterval);
            showAlert('danger', 'An error occurred during the update.');
        })
        .finally(() => {
            uploadBtn.disabled = false;
            uploadBtn.innerHTML = '<i class="fas fa-upload"></i> Upload and Apply Update';
        });
    });
    
    // Update settings form
    updateSettingsForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        fetch(this.action, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('success', data.message);
            } else {
                showAlert('danger', data.message);
            }
        })
        .catch(error => {
            showAlert('danger', 'An error occurred while updating settings.');
        });
    });
    
    function showAlert(type, message) {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        const container = document.querySelector('.container-fluid');
        container.insertBefore(alertDiv, container.firstChild);
        
        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.remove();
            }
        }, 5000);
    }
});

// Quick action functions
function checkForUpdates() {
    showAlert('info', 'Checking for updates...');
    // Implementation would check for available updates
}

function createBackup() {
    showAlert('info', 'Creating backup...');
    // Implementation would create a backup
}

function clearCache() {
    fetch('/settings/updates/clear-cache', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('success', 'Cache cleared successfully!');
        } else {
            showAlert('danger', 'Failed to clear cache.');
        }
    });
}

function viewLogs() {
    window.open('/settings/updates/logs', '_blank');
}

function rollbackToVersion(versionId) {
    const modal = new bootstrap.Modal(document.getElementById('rollbackModal'));
    document.getElementById('rollbackVersion').textContent = versionId;
    document.getElementById('confirmRollback').onclick = function() {
        performRollback(versionId);
        modal.hide();
    };
    modal.show();
}

function performRollback(versionId) {
    fetch(`{{ route('settings.updates.rollback', '') }}/${versionId}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('success', 'Rollback completed successfully!');
            setTimeout(() => {
                location.reload();
            }, 2000);
        } else {
            showAlert('danger', data.message || 'Rollback failed.');
        }
    });
}

function viewVersionDetails(versionId) {
    // Implementation would show version details in a modal
    showAlert('info', 'Version details feature coming soon.');
}

function downloadBackup(backupId) {
    window.location.href = `/settings/backup/download/${backupId}`;
}

function showAlert(type, message) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    const container = document.querySelector('.container-fluid');
    container.insertBefore(alertDiv, container.firstChild);
    
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 5000);
}
</script>
@endsection