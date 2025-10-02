@extends('layouts.app')

@section('title', 'Email & Notifications Settings')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3><i class="fas fa-envelope"></i> Email & Notifications Settings</h3>
                    <p class="text-muted mb-0">Configure email settings and notification preferences</p>
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
                    <h5 class="mb-0"><i class="fas fa-server"></i> SMTP Configuration</h5>
                </div>
                <div class="card-body">
                    <form id="emailForm" action="{{ route('settings.email.update') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="mail_driver" class="form-label">Mail Driver</label>
                                <select class="form-control" id="mail_driver" name="mail_driver">
                                    <option value="smtp" {{ ($emailSettings['mail_driver'] ?? 'smtp') == 'smtp' ? 'selected' : '' }}>SMTP</option>
                                    <option value="sendmail" {{ ($emailSettings['mail_driver'] ?? '') == 'sendmail' ? 'selected' : '' }}>Sendmail</option>
                                    <option value="mailgun" {{ ($emailSettings['mail_driver'] ?? '') == 'mailgun' ? 'selected' : '' }}>Mailgun</option>
                                    <option value="ses" {{ ($emailSettings['mail_driver'] ?? '') == 'ses' ? 'selected' : '' }}>Amazon SES</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="mail_host" class="form-label">SMTP Host</label>
                                <input type="text" class="form-control" id="mail_host" name="mail_host" 
                                       value="{{ old('mail_host', $emailSettings['mail_host'] ?? '') }}" 
                                       placeholder="smtp.gmail.com">
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="mail_port" class="form-label">SMTP Port</label>
                                <input type="number" class="form-control" id="mail_port" name="mail_port" 
                                       value="{{ old('mail_port', $emailSettings['mail_port'] ?? 587) }}" 
                                       placeholder="587">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="mail_encryption" class="form-label">Encryption</label>
                                <select class="form-control" id="mail_encryption" name="mail_encryption">
                                    <option value="tls" {{ ($emailSettings['mail_encryption'] ?? 'tls') == 'tls' ? 'selected' : '' }}>TLS</option>
                                    <option value="ssl" {{ ($emailSettings['mail_encryption'] ?? '') == 'ssl' ? 'selected' : '' }}>SSL</option>
                                    <option value="" {{ ($emailSettings['mail_encryption'] ?? '') == '' ? 'selected' : '' }}>None</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="mail_username" class="form-label">SMTP Username</label>
                                <input type="text" class="form-control" id="mail_username" name="mail_username" 
                                       value="{{ old('mail_username', $emailSettings['mail_username'] ?? '') }}" 
                                       placeholder="your-email@gmail.com">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="mail_password" class="form-label">SMTP Password</label>
                                <input type="password" class="form-control" id="mail_password" name="mail_password" 
                                       value="{{ old('mail_password', $emailSettings['mail_password'] ?? '') }}" 
                                       placeholder="Enter SMTP password">
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="mail_from_address" class="form-label">From Email Address</label>
                                <input type="email" class="form-control" id="mail_from_address" name="mail_from_address" 
                                       value="{{ old('mail_from_address', $emailSettings['mail_from_address'] ?? '') }}" 
                                       placeholder="noreply@yourcompany.com">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="mail_from_name" class="form-label">From Name</label>
                                <input type="text" class="form-control" id="mail_from_name" name="mail_from_name" 
                                       value="{{ old('mail_from_name', $emailSettings['mail_from_name'] ?? '') }}" 
                                       placeholder="Your Company Name">
                            </div>
                        </div>
                        
                        <div class="d-flex gap-2 mb-4">
                            <button type="button" class="btn btn-info" id="testEmailBtn">
                                <i class="fas fa-paper-plane"></i> Send Test Email
                            </button>
                        </div>
                        
                        <hr>
                        
                        <h6><i class="fas fa-bell"></i> Notification Settings</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="email_notifications" 
                                           name="email_notifications" value="1" 
                                           {{ ($emailSettings['email_notifications'] ?? true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="email_notifications">
                                        <strong>Email Notifications</strong><br>
                                        <small class="text-muted">Enable email notifications system-wide</small>
                                    </label>
                                </div>
                                
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="user_registration_emails" 
                                           name="user_registration_emails" value="1" 
                                           {{ ($emailSettings['user_registration_emails'] ?? true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="user_registration_emails">
                                        <strong>User Registration Emails</strong><br>
                                        <small class="text-muted">Send welcome emails to new users</small>
                                    </label>
                                </div>
                                
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="password_reset_emails" 
                                           name="password_reset_emails" value="1" 
                                           {{ ($emailSettings['password_reset_emails'] ?? true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="password_reset_emails">
                                        <strong>Password Reset Emails</strong><br>
                                        <small class="text-muted">Send password reset notifications</small>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="admin_notifications" 
                                           name="admin_notifications" value="1" 
                                           {{ ($emailSettings['admin_notifications'] ?? true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="admin_notifications">
                                        <strong>Admin Notifications</strong><br>
                                        <small class="text-muted">Send notifications to administrators</small>
                                    </label>
                                </div>
                                
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="system_alerts" 
                                           name="system_alerts" value="1" 
                                           {{ ($emailSettings['system_alerts'] ?? true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="system_alerts">
                                        <strong>System Alerts</strong><br>
                                        <small class="text-muted">Send system error and warning alerts</small>
                                    </label>
                                </div>
                                
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="marketing_emails" 
                                           name="marketing_emails" value="1" 
                                           {{ ($emailSettings['marketing_emails'] ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="marketing_emails">
                                        <strong>Marketing Emails</strong><br>
                                        <small class="text-muted">Send promotional and marketing emails</small>
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <h6><i class="fas fa-clock"></i> Email Templates & Scheduling</h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="email_template" class="form-label">Default Email Template</label>
                                <select class="form-control" id="email_template" name="email_template">
                                    <option value="default" {{ ($emailSettings['email_template'] ?? 'default') == 'default' ? 'selected' : '' }}>Default</option>
                                    <option value="modern" {{ ($emailSettings['email_template'] ?? '') == 'modern' ? 'selected' : '' }}>Modern</option>
                                    <option value="minimal" {{ ($emailSettings['email_template'] ?? '') == 'minimal' ? 'selected' : '' }}>Minimal</option>
                                    <option value="corporate" {{ ($emailSettings['email_template'] ?? '') == 'corporate' ? 'selected' : '' }}>Corporate</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email_queue" class="form-label">Email Queue</label>
                                <select class="form-control" id="email_queue" name="email_queue">
                                    <option value="sync" {{ ($emailSettings['email_queue'] ?? 'sync') == 'sync' ? 'selected' : '' }}>Send Immediately</option>
                                    <option value="database" {{ ($emailSettings['email_queue'] ?? '') == 'database' ? 'selected' : '' }}>Queue (Database)</option>
                                    <option value="redis" {{ ($emailSettings['email_queue'] ?? '') == 'redis' ? 'selected' : '' }}>Queue (Redis)</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="daily_email_limit" class="form-label">Daily Email Limit</label>
                                <input type="number" class="form-control" id="daily_email_limit" name="daily_email_limit" 
                                       value="{{ old('daily_email_limit', $emailSettings['daily_email_limit'] ?? 1000) }}" 
                                       min="1" max="10000">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email_retry_attempts" class="form-label">Retry Attempts</label>
                                <input type="number" class="form-control" id="email_retry_attempts" name="email_retry_attempts" 
                                       value="{{ old('email_retry_attempts', $emailSettings['email_retry_attempts'] ?? 3) }}" 
                                       min="1" max="10">
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
                    <h5 class="mb-0"><i class="fas fa-info-circle"></i> SMTP Configuration Help</h5>
                </div>
                <div class="card-body">
                    <h6>Common SMTP Settings:</h6>
                    <div class="accordion" id="smtpHelp">
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="gmailHelp">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseGmail">
                                    Gmail
                                </button>
                            </h2>
                            <div id="collapseGmail" class="accordion-collapse collapse" data-bs-parent="#smtpHelp">
                                <div class="accordion-body small">
                                    <strong>Host:</strong> smtp.gmail.com<br>
                                    <strong>Port:</strong> 587<br>
                                    <strong>Encryption:</strong> TLS<br>
                                    <strong>Note:</strong> Use App Password for 2FA accounts
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="outlookHelp">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOutlook">
                                    Outlook/Hotmail
                                </button>
                            </h2>
                            <div id="collapseOutlook" class="accordion-collapse collapse" data-bs-parent="#smtpHelp">
                                <div class="accordion-body small">
                                    <strong>Host:</strong> smtp-mail.outlook.com<br>
                                    <strong>Port:</strong> 587<br>
                                    <strong>Encryption:</strong> TLS
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-chart-bar"></i> Email Statistics</h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled">
                        <li><strong>Emails Sent Today:</strong> <span class="badge bg-primary">{{ $emailStats['today'] ?? 0 }}</span></li>
                        <li><strong>Emails This Week:</strong> <span class="badge bg-info">{{ $emailStats['week'] ?? 0 }}</span></li>
                        <li><strong>Failed Emails:</strong> <span class="badge bg-danger">{{ $emailStats['failed'] ?? 0 }}</span></li>
                        <li><strong>Queue Size:</strong> <span class="badge bg-warning">{{ $emailStats['queued'] ?? 0 }}</span></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('emailForm');
    const testEmailBtn = document.getElementById('testEmailBtn');
    
    // Test email functionality
    testEmailBtn.addEventListener('click', function() {
        const email = prompt('Enter email address to send test email:');
        if (email) {
            this.disabled = true;
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
            
            fetch('{{ route("settings.email.update") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    action: 'test_email',
                    test_email: email
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('success', 'Test email sent successfully!');
                } else {
                    showAlert('danger', data.message || 'Failed to send test email.');
                }
            })
            .catch(error => {
                showAlert('danger', 'An error occurred while sending test email.');
            })
            .finally(() => {
                this.disabled = false;
                this.innerHTML = '<i class="fas fa-paper-plane"></i> Send Test Email';
            });
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
            } else {
                showAlert('danger', data.message);
            }
        })
        .catch(error => {
            showAlert('danger', 'An error occurred while updating email settings.');
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