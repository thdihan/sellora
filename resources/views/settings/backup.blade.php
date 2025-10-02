@extends('layouts.app')

@section('title', 'Backup & Integrations Settings')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3><i class="fas fa-database"></i> Backup & Integrations Settings</h3>
                    <p class="text-muted mb-0">Configure data backups and third-party integrations</p>
                </div>
                <a href="{{ route('settings.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Settings
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Backup Settings -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-shield-alt"></i> Backup Configuration</h5>
                </div>
                <div class="card-body">
                    <form id="backupForm" action="{{ route('settings.backup.update') }}" method="POST">
                        @csrf
                        
                        <h6><i class="fas fa-clock"></i> Automatic Backups</h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="backup_frequency" class="form-label">Backup Frequency</label>
                                <select class="form-control" id="backup_frequency" name="backup_frequency">
                                    <option value="disabled" {{ ($backupSettings['backup_frequency'] ?? 'daily') == 'disabled' ? 'selected' : '' }}>Disabled</option>
                                    <option value="hourly" {{ ($backupSettings['backup_frequency'] ?? 'daily') == 'hourly' ? 'selected' : '' }}>Hourly</option>
                                    <option value="daily" {{ ($backupSettings['backup_frequency'] ?? 'daily') == 'daily' ? 'selected' : '' }}>Daily</option>
                                    <option value="weekly" {{ ($backupSettings['backup_frequency'] ?? 'daily') == 'weekly' ? 'selected' : '' }}>Weekly</option>
                                    <option value="monthly" {{ ($backupSettings['backup_frequency'] ?? 'daily') == 'monthly' ? 'selected' : '' }}>Monthly</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="backup_time" class="form-label">Backup Time</label>
                                <input type="time" class="form-control" id="backup_time" name="backup_time" 
                                       value="{{ old('backup_time', $backupSettings['backup_time'] ?? '02:00') }}">
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="backup_retention_days" class="form-label">Retention Period (Days)</label>
                                <input type="number" class="form-control" id="backup_retention_days" name="backup_retention_days" 
                                       value="{{ old('backup_retention_days', $backupSettings['backup_retention_days'] ?? 30) }}" 
                                       min="1" max="365">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="max_backup_size" class="form-label">Max Backup Size (MB)</label>
                                <input type="number" class="form-control" id="max_backup_size" name="max_backup_size" 
                                       value="{{ old('max_backup_size', $backupSettings['max_backup_size'] ?? 1024) }}" 
                                       min="100" max="10240">
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="backup_database" 
                                           name="backup_database" value="1" 
                                           {{ ($backupSettings['backup_database'] ?? true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="backup_database">
                                        <strong>Include Database</strong><br>
                                        <small class="text-muted">Backup database tables and data</small>
                                    </label>
                                </div>
                                
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="backup_files" 
                                           name="backup_files" value="1" 
                                           {{ ($backupSettings['backup_files'] ?? true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="backup_files">
                                        <strong>Include Files</strong><br>
                                        <small class="text-muted">Backup uploaded files and assets</small>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="backup_compression" 
                                           name="backup_compression" value="1" 
                                           {{ ($backupSettings['backup_compression'] ?? true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="backup_compression">
                                        <strong>Enable Compression</strong><br>
                                        <small class="text-muted">Compress backups to save space</small>
                                    </label>
                                </div>
                                
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="backup_notifications" 
                                           name="backup_notifications" value="1" 
                                           {{ ($backupSettings['backup_notifications'] ?? true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="backup_notifications">
                                        <strong>Email Notifications</strong><br>
                                        <small class="text-muted">Send email notifications for backup status</small>
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <h6><i class="fas fa-cloud"></i> Storage Configuration</h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="backup_storage" class="form-label">Storage Location</label>
                                <select class="form-control" id="backup_storage" name="backup_storage">
                                    <option value="local" {{ ($backupSettings['backup_storage'] ?? 'local') == 'local' ? 'selected' : '' }}>Local Storage</option>
                                    <option value="s3" {{ ($backupSettings['backup_storage'] ?? 'local') == 's3' ? 'selected' : '' }}>Amazon S3</option>
                                    <option value="dropbox" {{ ($backupSettings['backup_storage'] ?? 'local') == 'dropbox' ? 'selected' : '' }}>Dropbox</option>
                                    <option value="google_drive" {{ ($backupSettings['backup_storage'] ?? 'local') == 'google_drive' ? 'selected' : '' }}>Google Drive</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="backup_path" class="form-label">Backup Path</label>
                                <input type="text" class="form-control" id="backup_path" name="backup_path" 
                                       value="{{ old('backup_path', $backupSettings['backup_path'] ?? '/backups') }}" 
                                       placeholder="/path/to/backups">
                            </div>
                        </div>
                        
                        <div class="d-flex gap-2 mb-4">
                            <button type="button" class="btn btn-success" id="createBackupBtn">
                                <i class="fas fa-play"></i> Create Backup Now
                            </button>
                            <button type="button" class="btn btn-info" id="testBackupBtn">
                                <i class="fas fa-vial"></i> Test Backup Configuration
                            </button>
                        </div>
                        
                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Save Backup Settings
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Integrations Settings -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-plug"></i> Third-Party Integrations</h5>
                </div>
                <div class="card-body">
                    <form id="integrationsForm" action="{{ route('settings.backup.update') }}" method="POST">
                        @csrf
                        <input type="hidden" name="section" value="integrations">
                        
                        <h6><i class="fab fa-google"></i> Google Services</h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="google_analytics_id" class="form-label">Google Analytics ID</label>
                                <input type="text" class="form-control" id="google_analytics_id" name="google_analytics_id" 
                                       value="{{ old('google_analytics_id', $integrationSettings['google_analytics_id'] ?? '') }}" 
                                       placeholder="G-XXXXXXXXXX">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="google_maps_api_key" class="form-label">Google Maps API Key</label>
                                <input type="text" class="form-control" id="google_maps_api_key" name="google_maps_api_key" 
                                       value="{{ old('google_maps_api_key', $integrationSettings['google_maps_api_key'] ?? '') }}" 
                                       placeholder="AIzaSyXXXXXXXXXXXXXXXXXXXXXXXXXX">
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="google_recaptcha_site_key" class="form-label">reCAPTCHA Site Key</label>
                                <input type="text" class="form-control" id="google_recaptcha_site_key" name="google_recaptcha_site_key" 
                                       value="{{ old('google_recaptcha_site_key', $integrationSettings['google_recaptcha_site_key'] ?? '') }}" 
                                       placeholder="6LeXXXXXXXXXXXXXXXXXXXXXXXXXXXXX">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="google_recaptcha_secret_key" class="form-label">reCAPTCHA Secret Key</label>
                                <input type="password" class="form-control" id="google_recaptcha_secret_key" name="google_recaptcha_secret_key" 
                                       value="{{ old('google_recaptcha_secret_key', $integrationSettings['google_recaptcha_secret_key'] ?? '') }}" 
                                       placeholder="6LeXXXXXXXXXXXXXXXXXXXXXXXXXXXXX">
                            </div>
                        </div>
                        
                        <h6><i class="fab fa-aws"></i> Amazon Web Services</h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="aws_access_key_id" class="form-label">AWS Access Key ID</label>
                                <input type="text" class="form-control" id="aws_access_key_id" name="aws_access_key_id" 
                                       value="{{ old('aws_access_key_id', $integrationSettings['aws_access_key_id'] ?? '') }}" 
                                       placeholder="AKIAIOSFODNN7EXAMPLE">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="aws_secret_access_key" class="form-label">AWS Secret Access Key</label>
                                <input type="password" class="form-control" id="aws_secret_access_key" name="aws_secret_access_key" 
                                       value="{{ old('aws_secret_access_key', $integrationSettings['aws_secret_access_key'] ?? '') }}" 
                                       placeholder="wJalrXUtnFEMI/K7MDENG/bPxRfiCYEXAMPLEKEY">
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="aws_default_region" class="form-label">AWS Default Region</label>
                                <select class="form-control" id="aws_default_region" name="aws_default_region">
                                    <option value="us-east-1" {{ ($integrationSettings['aws_default_region'] ?? 'us-east-1') == 'us-east-1' ? 'selected' : '' }}>US East (N. Virginia)</option>
                                    <option value="us-west-2" {{ ($integrationSettings['aws_default_region'] ?? '') == 'us-west-2' ? 'selected' : '' }}>US West (Oregon)</option>
                                    <option value="eu-west-1" {{ ($integrationSettings['aws_default_region'] ?? '') == 'eu-west-1' ? 'selected' : '' }}>Europe (Ireland)</option>
                                    <option value="ap-southeast-1" {{ ($integrationSettings['aws_default_region'] ?? '') == 'ap-southeast-1' ? 'selected' : '' }}>Asia Pacific (Singapore)</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="aws_bucket" class="form-label">S3 Bucket Name</label>
                                <input type="text" class="form-control" id="aws_bucket" name="aws_bucket" 
                                       value="{{ old('aws_bucket', $integrationSettings['aws_bucket'] ?? '') }}" 
                                       placeholder="my-app-bucket">
                            </div>
                        </div>
                        
                        <h6><i class="fas fa-credit-card"></i> Payment Gateways</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="stripe_enabled" 
                                           name="stripe_enabled" value="1" 
                                           {{ ($integrationSettings['stripe_enabled'] ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="stripe_enabled">
                                        <strong>Enable Stripe</strong><br>
                                        <small class="text-muted">Accept payments via Stripe</small>
                                    </label>
                                </div>
                                
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="paypal_enabled" 
                                           name="paypal_enabled" value="1" 
                                           {{ ($integrationSettings['paypal_enabled'] ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="paypal_enabled">
                                        <strong>Enable PayPal</strong><br>
                                        <small class="text-muted">Accept payments via PayPal</small>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="razorpay_enabled" 
                                           name="razorpay_enabled" value="1" 
                                           {{ ($integrationSettings['razorpay_enabled'] ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="razorpay_enabled">
                                        <strong>Enable Razorpay</strong><br>
                                        <small class="text-muted">Accept payments via Razorpay</small>
                                    </label>
                                </div>
                                
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="square_enabled" 
                                           name="square_enabled" value="1" 
                                           {{ ($integrationSettings['square_enabled'] ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="square_enabled">
                                        <strong>Enable Square</strong><br>
                                        <small class="text-muted">Accept payments via Square</small>
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <h6><i class="fas fa-share-alt"></i> Social Media</h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="facebook_app_id" class="form-label">Facebook App ID</label>
                                <input type="text" class="form-control" id="facebook_app_id" name="facebook_app_id" 
                                       value="{{ old('facebook_app_id', $integrationSettings['facebook_app_id'] ?? '') }}" 
                                       placeholder="123456789012345">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="twitter_api_key" class="form-label">Twitter API Key</label>
                                <input type="text" class="form-control" id="twitter_api_key" name="twitter_api_key" 
                                       value="{{ old('twitter_api_key', $integrationSettings['twitter_api_key'] ?? '') }}" 
                                       placeholder="xxxxxxxxxxxxxxxxxxxxxxxxxxx">
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Save Integration Settings
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-history"></i> Recent Backups</h5>
                </div>
                <div class="card-body">
                    <div class="backup-list">
                        @forelse($recentBackups ?? [] as $backup)
                        <div class="d-flex justify-content-between align-items-center mb-2 p-2 border rounded">
                            <div>
                                <small class="fw-bold">{{ $backup['name'] }}</small><br>
                                <small class="text-muted">{{ $backup['date'] }}</small>
                            </div>
                            <div>
                                <span class="badge bg-{{ $backup['status'] == 'success' ? 'success' : 'danger' }}">{{ $backup['status'] }}</span>
                                <button class="btn btn-sm btn-outline-primary ms-1" onclick="downloadBackup('{{ $backup['id'] }}')">
                                    Download
                                </button>
                            </div>
                        </div>
                        @empty
                        <p class="text-muted">No backups found</p>
                        @endforelse
                    </div>
                </div>
            </div>
            
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-info-circle"></i> Backup Information</h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled small">
                        <li><strong>Last Backup:</strong> {{ $backupInfo['last_backup'] ?? 'Never' }}</li>
                        <li><strong>Next Backup:</strong> {{ $backupInfo['next_backup'] ?? 'Not scheduled' }}</li>
                        <li><strong>Total Backups:</strong> {{ $backupInfo['total_backups'] ?? 0 }}</li>
                        <li><strong>Storage Used:</strong> {{ $backupInfo['storage_used'] ?? '0 MB' }}</li>
                        <li><strong>Average Size:</strong> {{ $backupInfo['average_size'] ?? '0 MB' }}</li>
                    </ul>
                </div>
            </div>
            
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-lightbulb"></i> Integration Tips</h5>
                </div>
                <div class="card-body">
                    <ul class="small">
                        <li>Test your backup configuration regularly</li>
                        <li>Store backups in multiple locations for redundancy</li>
                        <li>Keep API keys secure and rotate them periodically</li>
                        <li>Monitor integration usage and limits</li>
                        <li>Enable only the integrations you actually use</li>
                        <li>Review backup retention policies based on your needs</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const backupForm = document.getElementById('backupForm');
    const integrationsForm = document.getElementById('integrationsForm');
    const createBackupBtn = document.getElementById('createBackupBtn');
    const testBackupBtn = document.getElementById('testBackupBtn');
    
    // Create backup now
    createBackupBtn.addEventListener('click', function() {
        this.disabled = true;
        this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creating Backup...';
        
        fetch('{{ route("settings.backup.update") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ action: 'create_backup' })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('success', 'Backup created successfully!');
                // Refresh backup list
                location.reload();
            } else {
                showAlert('danger', data.message || 'Failed to create backup.');
            }
        })
        .catch(error => {
            showAlert('danger', 'An error occurred while creating backup.');
        })
        .finally(() => {
            this.disabled = false;
            this.innerHTML = '<i class="fas fa-play"></i> Create Backup Now';
        });
    });
    
    // Test backup configuration
    testBackupBtn.addEventListener('click', function() {
        this.disabled = true;
        this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Testing...';
        
        fetch('{{ route("settings.backup.update") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ action: 'test_backup' })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('success', 'Backup configuration test passed!');
            } else {
                showAlert('danger', data.message || 'Backup configuration test failed.');
            }
        })
        .catch(error => {
            showAlert('danger', 'An error occurred while testing backup configuration.');
        })
        .finally(() => {
            this.disabled = false;
            this.innerHTML = '<i class="fas fa-vial"></i> Test Backup Configuration';
        });
    });
    
    // Form submissions
    [backupForm, integrationsForm].forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
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

function downloadBackup(backupId) {
    window.location.href = `/settings/backup/download/${backupId}`;
}
</script>
@endsection