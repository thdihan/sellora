@extends('layouts.app')

@section('title', 'Security Settings')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3><i class="fas fa-shield-alt"></i> Security Settings</h3>
                    <p class="text-muted mb-0">Configure authentication, session, and security policies</p>
                </div>
                <a href="{{ route('settings.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Settings
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-lock"></i> Authentication Settings</h5>
                </div>
                <div class="card-body">
                    <form id="securityForm" action="{{ route('settings.security.update') }}" method="POST">
                        @csrf
                        
                        <h6><i class="fas fa-key"></i> Password Policy</h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="min_password_length" class="form-label">Minimum Password Length</label>
                                <input type="number" class="form-control" id="min_password_length" name="min_password_length" 
                                       value="{{ old('min_password_length', $securitySettings['min_password_length'] ?? 8) }}" 
                                       min="6" max="50">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="password_expiry_days" class="form-label">Password Expiry (Days)</label>
                                <input type="number" class="form-control" id="password_expiry_days" name="password_expiry_days" 
                                       value="{{ old('password_expiry_days', $securitySettings['password_expiry_days'] ?? 90) }}" 
                                       min="0" max="365" placeholder="0 = Never expires">
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="require_uppercase" 
                                           name="require_uppercase" value="1" 
                                           {{ ($securitySettings['require_uppercase'] ?? true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="require_uppercase">
                                        <strong>Require Uppercase Letters</strong><br>
                                        <small class="text-muted">Password must contain at least one uppercase letter</small>
                                    </label>
                                </div>
                                
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="require_lowercase" 
                                           name="require_lowercase" value="1" 
                                           {{ ($securitySettings['require_lowercase'] ?? true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="require_lowercase">
                                        <strong>Require Lowercase Letters</strong><br>
                                        <small class="text-muted">Password must contain at least one lowercase letter</small>
                                    </label>
                                </div>
                                
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="require_numbers" 
                                           name="require_numbers" value="1" 
                                           {{ ($securitySettings['require_numbers'] ?? true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="require_numbers">
                                        <strong>Require Numbers</strong><br>
                                        <small class="text-muted">Password must contain at least one number</small>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="require_special_chars" 
                                           name="require_special_chars" value="1" 
                                           {{ ($securitySettings['require_special_chars'] ?? true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="require_special_chars">
                                        <strong>Require Special Characters</strong><br>
                                        <small class="text-muted">Password must contain at least one special character</small>
                                    </label>
                                </div>
                                
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="prevent_password_reuse" 
                                           name="prevent_password_reuse" value="1" 
                                           {{ ($securitySettings['prevent_password_reuse'] ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="prevent_password_reuse">
                                        <strong>Prevent Password Reuse</strong><br>
                                        <small class="text-muted">Users cannot reuse their last 5 passwords</small>
                                    </label>
                                </div>
                                
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="force_password_change" 
                                           name="force_password_change" value="1" 
                                           {{ ($securitySettings['force_password_change'] ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="force_password_change">
                                        <strong>Force Password Change on First Login</strong><br>
                                        <small class="text-muted">New users must change their password on first login</small>
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <h6><i class="fas fa-clock"></i> Session Management</h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="session_lifetime" class="form-label">Session Lifetime (Minutes)</label>
                                <input type="number" class="form-control" id="session_lifetime" name="session_lifetime" 
                                       value="{{ old('session_lifetime', $securitySettings['session_lifetime'] ?? 120) }}" 
                                       min="5" max="1440">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="max_login_attempts" class="form-label">Max Login Attempts</label>
                                <input type="number" class="form-control" id="max_login_attempts" name="max_login_attempts" 
                                       value="{{ old('max_login_attempts', $securitySettings['max_login_attempts'] ?? 5) }}" 
                                       min="1" max="20">
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="lockout_duration" class="form-label">Lockout Duration (Minutes)</label>
                                <input type="number" class="form-control" id="lockout_duration" name="lockout_duration" 
                                       value="{{ old('lockout_duration', $securitySettings['lockout_duration'] ?? 15) }}" 
                                       min="1" max="1440">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="concurrent_sessions" class="form-label">Max Concurrent Sessions</label>
                                <input type="number" class="form-control" id="concurrent_sessions" name="concurrent_sessions" 
                                       value="{{ old('concurrent_sessions', $securitySettings['concurrent_sessions'] ?? 3) }}" 
                                       min="1" max="10">
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="remember_me_enabled" 
                                           name="remember_me_enabled" value="1" 
                                           {{ ($securitySettings['remember_me_enabled'] ?? true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="remember_me_enabled">
                                        <strong>Enable "Remember Me"</strong><br>
                                        <small class="text-muted">Allow users to stay logged in for extended periods</small>
                                    </label>
                                </div>
                                
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="auto_logout_inactive" 
                                           name="auto_logout_inactive" value="1" 
                                           {{ ($securitySettings['auto_logout_inactive'] ?? true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="auto_logout_inactive">
                                        <strong>Auto Logout Inactive Users</strong><br>
                                        <small class="text-muted">Automatically logout users after inactivity</small>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="session_regeneration" 
                                           name="session_regeneration" value="1" 
                                           {{ ($securitySettings['session_regeneration'] ?? true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="session_regeneration">
                                        <strong>Session ID Regeneration</strong><br>
                                        <small class="text-muted">Regenerate session ID on login for security</small>
                                    </label>
                                </div>
                                
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="secure_cookies" 
                                           name="secure_cookies" value="1" 
                                           {{ ($securitySettings['secure_cookies'] ?? true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="secure_cookies">
                                        <strong>Secure Cookies</strong><br>
                                        <small class="text-muted">Use secure cookies (HTTPS required)</small>
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <h6><i class="fas fa-shield-alt"></i> Security Features</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="two_factor_auth" 
                                           name="two_factor_auth" value="1" 
                                           {{ ($securitySettings['two_factor_auth'] ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="two_factor_auth">
                                        <strong>Two-Factor Authentication</strong><br>
                                        <small class="text-muted">Enable 2FA for enhanced security</small>
                                    </label>
                                </div>
                                
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="email_verification" 
                                           name="email_verification" value="1" 
                                           {{ ($securitySettings['email_verification'] ?? true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="email_verification">
                                        <strong>Email Verification Required</strong><br>
                                        <small class="text-muted">Users must verify their email address</small>
                                    </label>
                                </div>
                                
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="login_notifications" 
                                           name="login_notifications" value="1" 
                                           {{ ($securitySettings['login_notifications'] ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="login_notifications">
                                        <strong>Login Notifications</strong><br>
                                        <small class="text-muted">Send email notifications for new logins</small>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="ip_whitelist_enabled" 
                                           name="ip_whitelist_enabled" value="1" 
                                           {{ ($securitySettings['ip_whitelist_enabled'] ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="ip_whitelist_enabled">
                                        <strong>IP Whitelist</strong><br>
                                        <small class="text-muted">Restrict access to specific IP addresses</small>
                                    </label>
                                </div>
                                
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="suspicious_activity_detection" 
                                           name="suspicious_activity_detection" value="1" 
                                           {{ ($securitySettings['suspicious_activity_detection'] ?? true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="suspicious_activity_detection">
                                        <strong>Suspicious Activity Detection</strong><br>
                                        <small class="text-muted">Monitor and alert on suspicious activities</small>
                                    </label>
                                </div>
                                
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="audit_logging" 
                                           name="audit_logging" value="1" 
                                           {{ ($securitySettings['audit_logging'] ?? true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="audit_logging">
                                        <strong>Audit Logging</strong><br>
                                        <small class="text-muted">Log all security-related activities</small>
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row" id="ipWhitelistSection" style="display: none;">
                            <div class="col-12 mb-3">
                                <label for="allowed_ips" class="form-label">Allowed IP Addresses</label>
                                <textarea class="form-control" id="allowed_ips" name="allowed_ips" rows="3" 
                                          placeholder="Enter IP addresses, one per line (e.g., 192.168.1.1, 10.0.0.0/24)">{{ old('allowed_ips', $securitySettings['allowed_ips'] ?? '') }}</textarea>
                                <small class="text-muted">Enter IP addresses or CIDR ranges, one per line</small>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <h6><i class="fas fa-globe"></i> API Security</h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="api_rate_limit" class="form-label">API Rate Limit (per minute)</label>
                                <input type="number" class="form-control" id="api_rate_limit" name="api_rate_limit" 
                                       value="{{ old('api_rate_limit', $securitySettings['api_rate_limit'] ?? 60) }}" 
                                       min="1" max="1000">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="api_token_expiry" class="form-label">API Token Expiry (Hours)</label>
                                <input type="number" class="form-control" id="api_token_expiry" name="api_token_expiry" 
                                       value="{{ old('api_token_expiry', $securitySettings['api_token_expiry'] ?? 24) }}" 
                                       min="1" max="8760">
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="api_authentication_required" 
                                           name="api_authentication_required" value="1" 
                                           {{ ($securitySettings['api_authentication_required'] ?? true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="api_authentication_required">
                                        <strong>API Authentication Required</strong><br>
                                        <small class="text-muted">Require authentication for all API endpoints</small>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="api_cors_enabled" 
                                           name="api_cors_enabled" value="1" 
                                           {{ ($securitySettings['api_cors_enabled'] ?? true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="api_cors_enabled">
                                        <strong>Enable CORS</strong><br>
                                        <small class="text-muted">Allow cross-origin requests to API</small>
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Save Security Settings
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
                    <h5 class="mb-0"><i class="fas fa-exclamation-triangle"></i> Security Status</h5>
                </div>
                <div class="card-body">
                    <div class="security-status">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>Password Policy</span>
                            <span class="badge bg-success">Strong</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>Session Security</span>
                            <span class="badge bg-success">Secure</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>Two-Factor Auth</span>
                            <span class="badge bg-{{ ($securitySettings['two_factor_auth'] ?? false) ? 'success' : 'warning' }}">
                                {{ ($securitySettings['two_factor_auth'] ?? false) ? 'Enabled' : 'Disabled' }}
                            </span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>Email Verification</span>
                            <span class="badge bg-{{ ($securitySettings['email_verification'] ?? true) ? 'success' : 'danger' }}">
                                {{ ($securitySettings['email_verification'] ?? true) ? 'Required' : 'Optional' }}
                            </span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span>Audit Logging</span>
                            <span class="badge bg-{{ ($securitySettings['audit_logging'] ?? true) ? 'success' : 'warning' }}">
                                {{ ($securitySettings['audit_logging'] ?? true) ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-chart-line"></i> Security Metrics</h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled">
                        <li><strong>Failed Logins Today:</strong> <span class="badge bg-danger">{{ $securityMetrics['failed_logins'] ?? 0 }}</span></li>
                        <li><strong>Active Sessions:</strong> <span class="badge bg-info">{{ $securityMetrics['active_sessions'] ?? 0 }}</span></li>
                        <li><strong>Locked Accounts:</strong> <span class="badge bg-warning">{{ $securityMetrics['locked_accounts'] ?? 0 }}</span></li>
                        <li><strong>Security Alerts:</strong> <span class="badge bg-danger">{{ $securityMetrics['security_alerts'] ?? 0 }}</span></li>
                    </ul>
                </div>
            </div>
            
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-lightbulb"></i> Security Tips</h5>
                </div>
                <div class="card-body">
                    <ul class="small">
                        <li>Enable two-factor authentication for enhanced security</li>
                        <li>Use strong password policies to prevent brute force attacks</li>
                        <li>Monitor failed login attempts regularly</li>
                        <li>Keep session timeouts reasonable for your use case</li>
                        <li>Enable audit logging to track security events</li>
                        <li>Consider IP whitelisting for admin accounts</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('securityForm');
    const ipWhitelistCheckbox = document.getElementById('ip_whitelist_enabled');
    const ipWhitelistSection = document.getElementById('ipWhitelistSection');
    
    // Toggle IP whitelist section
    function toggleIpWhitelist() {
        if (ipWhitelistCheckbox.checked) {
            ipWhitelistSection.style.display = 'block';
        } else {
            ipWhitelistSection.style.display = 'none';
        }
    }
    
    ipWhitelistCheckbox.addEventListener('change', toggleIpWhitelist);
    toggleIpWhitelist(); // Initial state
    
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
                // Update security status badges if needed
                updateSecurityStatus(data.settings);
            } else {
                showAlert('danger', data.message);
            }
        })
        .catch(error => {
            showAlert('danger', 'An error occurred while updating security settings.');
        });
    });
    
    function updateSecurityStatus(settings) {
        // Update security status badges based on new settings
        const badges = document.querySelectorAll('.security-status .badge');
        // Implementation would update badges based on settings
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
});
</script>
@endsection