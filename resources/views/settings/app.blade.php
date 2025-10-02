@extends('layouts.app')

@section('title', 'Application Settings')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3><i class="fas fa-cogs"></i> Application Settings</h3>
                    <p class="text-muted mb-0">Configure application behavior and features</p>
                </div>
                <a href="{{ route('settings.index') }}" class="btn btn-secondary">
                    Back to Settings
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-sliders-h"></i> General Settings</h5>
                </div>
                <div class="card-body">
                    <form id="appForm" action="{{ route('settings.app.update') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="app_name" class="form-label">Application Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="app_name" name="app_name" 
                                       value="{{ old('app_name', $appSettings['app_name'] ?? config('app.name')) }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="app_version" class="form-label">Application Version</label>
                                <input type="text" class="form-control" id="app_version" name="app_version" 
                                       value="{{ old('app_version', $appSettings['app_version'] ?? '1.0.0') }}" readonly>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="app_environment" class="form-label">Environment</label>
                                <select class="form-control" id="app_environment" name="app_environment">
                                    <option value="production" {{ ($appSettings['app_environment'] ?? config('app.env')) == 'production' ? 'selected' : '' }}>Production</option>
                                    <option value="staging" {{ ($appSettings['app_environment'] ?? config('app.env')) == 'staging' ? 'selected' : '' }}>Staging</option>
                                    <option value="development" {{ ($appSettings['app_environment'] ?? config('app.env')) == 'development' ? 'selected' : '' }}>Development</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="app_timezone" class="form-label">Default Timezone</label>
                                <select class="form-control" id="app_timezone" name="app_timezone">
                                    <option value="UTC" {{ ($appSettings['app_timezone'] ?? config('app.timezone')) == 'UTC' ? 'selected' : '' }}>UTC</option>
                                    <option value="America/New_York" {{ ($appSettings['app_timezone'] ?? '') == 'America/New_York' ? 'selected' : '' }}>Eastern Time</option>
                                    <option value="America/Chicago" {{ ($appSettings['app_timezone'] ?? '') == 'America/Chicago' ? 'selected' : '' }}>Central Time</option>
                                    <option value="America/Denver" {{ ($appSettings['app_timezone'] ?? '') == 'America/Denver' ? 'selected' : '' }}>Mountain Time</option>
                                    <option value="America/Los_Angeles" {{ ($appSettings['app_timezone'] ?? '') == 'America/Los_Angeles' ? 'selected' : '' }}>Pacific Time</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="app_locale" class="form-label">Default Language</label>
                                <select class="form-control" id="app_locale" name="app_locale">
                                    <option value="en" {{ ($appSettings['app_locale'] ?? 'en') == 'en' ? 'selected' : '' }}>English</option>
                                    <option value="es" {{ ($appSettings['app_locale'] ?? '') == 'es' ? 'selected' : '' }}>Spanish</option>
                                    <option value="fr" {{ ($appSettings['app_locale'] ?? '') == 'fr' ? 'selected' : '' }}>French</option>
                                    <option value="de" {{ ($appSettings['app_locale'] ?? '') == 'de' ? 'selected' : '' }}>German</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="date_format" class="form-label">Date Format</label>
                                <select class="form-control" id="date_format" name="date_format">
                                    <option value="Y-m-d" {{ ($appSettings['date_format'] ?? 'Y-m-d') == 'Y-m-d' ? 'selected' : '' }}>YYYY-MM-DD</option>
                                    <option value="m/d/Y" {{ ($appSettings['date_format'] ?? '') == 'm/d/Y' ? 'selected' : '' }}>MM/DD/YYYY</option>
                                    <option value="d/m/Y" {{ ($appSettings['date_format'] ?? '') == 'd/m/Y' ? 'selected' : '' }}>DD/MM/YYYY</option>
                                    <option value="M j, Y" {{ ($appSettings['date_format'] ?? '') == 'M j, Y' ? 'selected' : '' }}>Mon DD, YYYY</option>
                                </select>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <h6><i class="fas fa-toggle-on"></i> Feature Toggles</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="maintenance_mode" 
                                           name="maintenance_mode" value="1" 
                                           {{ ($appSettings['maintenance_mode'] ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="maintenance_mode">
                                        <strong>Maintenance Mode</strong><br>
                                        <small class="text-muted">Put application in maintenance mode</small>
                                    </label>
                                </div>
                                
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="user_registration" 
                                           name="user_registration" value="1" 
                                           {{ ($appSettings['user_registration'] ?? true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="user_registration">
                                        <strong>User Registration</strong><br>
                                        <small class="text-muted">Allow new user registrations</small>
                                    </label>
                                </div>
                                
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="email_verification" 
                                           name="email_verification" value="1" 
                                           {{ ($appSettings['email_verification'] ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="email_verification">
                                        <strong>Email Verification</strong><br>
                                        <small class="text-muted">Require email verification for new users</small>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="debug_mode" 
                                           name="debug_mode" value="1" 
                                           {{ ($appSettings['debug_mode'] ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="debug_mode">
                                        <strong>Debug Mode</strong><br>
                                        <small class="text-muted">Enable detailed error reporting</small>
                                    </label>
                                </div>
                                
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="api_enabled" 
                                           name="api_enabled" value="1" 
                                           {{ ($appSettings['api_enabled'] ?? true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="api_enabled">
                                        <strong>API Access</strong><br>
                                        <small class="text-muted">Enable API endpoints</small>
                                    </label>
                                </div>
                                
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="logging_enabled" 
                                           name="logging_enabled" value="1" 
                                           {{ ($appSettings['logging_enabled'] ?? true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="logging_enabled">
                                        <strong>Application Logging</strong><br>
                                        <small class="text-muted">Enable application activity logging</small>
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <h6><i class="fas fa-chart-line"></i> Performance Settings</h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="cache_driver" class="form-label">Cache Driver</label>
                                <select class="form-control" id="cache_driver" name="cache_driver">
                                    <option value="file" {{ ($appSettings['cache_driver'] ?? 'file') == 'file' ? 'selected' : '' }}>File</option>
                                    <option value="redis" {{ ($appSettings['cache_driver'] ?? '') == 'redis' ? 'selected' : '' }}>Redis</option>
                                    <option value="memcached" {{ ($appSettings['cache_driver'] ?? '') == 'memcached' ? 'selected' : '' }}>Memcached</option>
                                    <option value="database" {{ ($appSettings['cache_driver'] ?? '') == 'database' ? 'selected' : '' }}>Database</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="session_lifetime" class="form-label">Session Lifetime (minutes)</label>
                                <input type="number" class="form-control" id="session_lifetime" name="session_lifetime" 
                                       value="{{ old('session_lifetime', $appSettings['session_lifetime'] ?? 120) }}" min="5" max="1440">
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="max_upload_size" class="form-label">Max Upload Size (MB)</label>
                                <input type="number" class="form-control" id="max_upload_size" name="max_upload_size" 
                                       value="{{ old('max_upload_size', $appSettings['max_upload_size'] ?? 10) }}" min="1" max="100">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="pagination_limit" class="form-label">Default Pagination Limit</label>
                                <input type="number" class="form-control" id="pagination_limit" name="pagination_limit" 
                                       value="{{ old('pagination_limit', $appSettings['pagination_limit'] ?? 15) }}" min="5" max="100">
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Save Changes
                            </button>
                            <button type="reset" class="btn btn-secondary">
                                <i class="fas fa-undo"></i> Reset
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-info-circle"></i> System Information</h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled">
                        <li><strong>PHP Version:</strong> {{ PHP_VERSION }}</li>
                        <li><strong>Laravel Version:</strong> {{ app()->version() }}</li>
                        <li><strong>Environment:</strong> {{ config('app.env') }}</li>
                        <li><strong>Debug Mode:</strong> {{ config('app.debug') ? 'Enabled' : 'Disabled' }}</li>
                        <li><strong>Cache Driver:</strong> {{ config('cache.default') }}</li>
                        <li><strong>Session Driver:</strong> {{ config('session.driver') }}</li>
                    </ul>
                </div>
            </div>
            
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-exclamation-triangle"></i> Important Notes</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning" role="alert">
                        <small>
                            <strong>Maintenance Mode:</strong> When enabled, only administrators can access the application.
                        </small>
                    </div>
                    <div class="alert alert-info" role="alert">
                        <small>
                            <strong>Debug Mode:</strong> Should be disabled in production environments for security.
                        </small>
                    </div>
                    <div class="alert alert-success" role="alert">
                        <small>
                            <strong>Cache Settings:</strong> Changes to cache driver may require application restart.
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('appForm');
    const maintenanceToggle = document.getElementById('maintenance_mode');
    const debugToggle = document.getElementById('debug_mode');
    
    // Warning for maintenance mode
    maintenanceToggle.addEventListener('change', function() {
        if (this.checked) {
            if (!confirm('Are you sure you want to enable maintenance mode? This will make the application inaccessible to regular users.')) {
                this.checked = false;
            }
        }
    });
    
    // Warning for debug mode
    debugToggle.addEventListener('change', function() {
        if (this.checked) {
            const env = document.getElementById('app_environment').value;
            if (env === 'production') {
                if (!confirm('Warning: Debug mode should not be enabled in production environments. Continue anyway?')) {
                    this.checked = false;
                }
            }
        }
    });
    
    // Form submission
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
                if (data.reload) {
                    setTimeout(() => location.reload(), 2000);
                }
            } else {
                showAlert('danger', data.message);
            }
        })
        .catch(error => {
            showAlert('danger', 'An error occurred while updating application settings.');
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
</script>
@endsection