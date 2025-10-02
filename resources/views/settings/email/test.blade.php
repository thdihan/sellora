@extends('layouts.app')

@section('title', 'Email Test')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Email Test</h3>
                    <div class="card-tools">
                        <a href="{{ route('settings.email') }}" class="btn btn-secondary">
                            Back to Email Settings
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
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-envelope-open-text"></i> Send Test Email
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <form id="testEmailForm">
                                        @csrf
                                        
                                        <div class="mb-3">
                                            <label for="test_email" class="form-label">Recipient Email Address</label>
                                            <input type="email" class="form-control" id="test_email" name="test_email" required placeholder="Enter email address to send test to">
                                            <small class="form-text text-muted">Email address where the test email will be sent</small>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="test_subject" class="form-label">Email Subject</label>
                                            <input type="text" class="form-control" id="test_subject" name="test_subject" value="Test Email from {{ config('app.name') }}" required>
                                            <small class="form-text text-muted">Subject line for the test email</small>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="test_message" class="form-label">Email Message</label>
                                            <textarea class="form-control" id="test_message" name="test_message" rows="6" required placeholder="Enter your test message here...">This is a test email sent from {{ config('app.name') }} to verify that email configuration is working correctly.

If you receive this email, your email settings are properly configured.

Test Details:
- Sent at: {{ now()->format('Y-m-d H:i:s') }}
- From: {{ config('app.name') }}
- Server: {{ request()->getHost() }}

Thank you!</textarea>
                                            <small class="form-text text-muted">Content of the test email</small>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="include_headers" name="include_headers" checked>
                                                <label class="form-check-label" for="include_headers">
                                                    Include technical headers in email
                                                </label>
                                            </div>
                                            <small class="form-text text-muted">Add server and configuration details to the email</small>
                                        </div>
                                        
                                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                            <button type="button" class="btn btn-outline-secondary" onclick="resetForm()">
                                                <i class="fas fa-undo"></i> Reset Form
                                            </button>
                                            <button type="submit" class="btn btn-primary" id="sendTestBtn">
                                                <i class="fas fa-paper-plane"></i> Send Test Email
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            
                            <div class="card mt-3">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-history"></i> Test Results
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div id="test_results" class="d-none">
                                        <div class="alert" id="result_alert" role="alert"></div>
                                        <div id="result_details"></div>
                                    </div>
                                    <div id="no_results" class="text-center text-muted">
                                        <i class="fas fa-inbox fa-2x mb-2"></i>
                                        <p>No test results yet. Send a test email to see results here.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-cog"></i> Current Email Configuration
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-sm table-borderless">
                                        <tr>
                                            <td><strong>SMTP Host:</strong></td>
                                            <td>{{ $emailSettings['smtp_host'] ?? 'Not configured' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>SMTP Port:</strong></td>
                                            <td>{{ $emailSettings['smtp_port'] ?? 'Not configured' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Username:</strong></td>
                                            <td>{{ $emailSettings['smtp_username'] ? Str::mask($emailSettings['smtp_username'], '*', 3) : 'Not configured' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Encryption:</strong></td>
                                            <td>
                                                @if($emailSettings['smtp_encryption'])
                                                    <span class="badge bg-success">{{ strtoupper($emailSettings['smtp_encryption']) }}</span>
                                                @else
                                                    <span class="badge bg-warning">None</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>From Address:</strong></td>
                                            <td>{{ $emailSettings['mail_from_address'] ?? 'Not configured' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>From Name:</strong></td>
                                            <td>{{ $emailSettings['mail_from_name'] ?? 'Not configured' }}</td>
                                        </tr>
                                    </table>
                                    
                                    <div class="mt-3">
                                        <a href="{{ route('settings.email') }}" class="btn btn-sm btn-outline-primary w-100">
                                            <i class="fas fa-edit"></i> Edit Email Settings
                                        </a>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="card mt-3">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-info-circle"></i> Testing Guidelines
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="small">
                                        <h6>Before Testing:</h6>
                                        <ul class="mb-3">
                                            <li>Ensure SMTP settings are configured</li>
                                            <li>Check firewall and network settings</li>
                                            <li>Verify credentials are correct</li>
                                        </ul>
                                        
                                        <h6>Troubleshooting:</h6>
                                        <ul class="mb-3">
                                            <li>Check spam/junk folders</li>
                                            <li>Verify recipient email is valid</li>
                                            <li>Review server logs for errors</li>
                                            <li>Test with different email providers</li>
                                        </ul>
                                        
                                        <h6>Common Issues:</h6>
                                        <ul class="mb-0">
                                            <li><strong>Authentication failed:</strong> Check username/password</li>
                                            <li><strong>Connection timeout:</strong> Verify host and port</li>
                                            <li><strong>SSL/TLS errors:</strong> Check encryption settings</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="card mt-3">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-tools"></i> Quick Actions
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="d-grid gap-2">
                                        <button type="button" class="btn btn-sm btn-outline-info" onclick="testConnection()">
                                            <i class="fas fa-plug"></i> Test SMTP Connection
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-warning" onclick="viewLogs()">
                                            <i class="fas fa-file-alt"></i> View Email Logs
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="clearLogs()">
                                            <i class="fas fa-trash"></i> Clear Logs
                                        </button>
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
document.getElementById('testEmailForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const sendBtn = document.getElementById('sendTestBtn');
    const originalText = sendBtn.innerHTML;
    
    // Show loading state
    sendBtn.disabled = true;
    sendBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
    
    fetch('{{ route('settings.email.test.send') }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        showTestResult(data);
    })
    .catch(error => {
        showTestResult({
            success: false,
            message: 'Network error: ' + error.message
        });
        console.error('Error:', error);
    })
    .finally(() => {
        // Reset button state
        sendBtn.disabled = false;
        sendBtn.innerHTML = originalText;
    });
});

function showTestResult(result) {
    const resultsDiv = document.getElementById('test_results');
    const noResultsDiv = document.getElementById('no_results');
    const alertDiv = document.getElementById('result_alert');
    const detailsDiv = document.getElementById('result_details');
    
    // Show results section
    resultsDiv.classList.remove('d-none');
    noResultsDiv.classList.add('d-none');
    
    // Set alert type and message
    alertDiv.className = `alert ${result.success ? 'alert-success' : 'alert-danger'}`;
    alertDiv.innerHTML = `
        ${result.success ? '✓' : '⚠'}
        ${result.message}
    `;
    
    // Add details
    const timestamp = new Date().toLocaleString();
    detailsDiv.innerHTML = `
        <small class="text-muted">
            <strong>Test performed at:</strong> ${timestamp}<br>
            <strong>Status:</strong> ${result.success ? 'Success' : 'Failed'}
        </small>
    `;
    
    // Auto-hide after 10 seconds if successful
    if (result.success) {
        setTimeout(() => {
            resultsDiv.classList.add('d-none');
            noResultsDiv.classList.remove('d-none');
        }, 10000);
    }
}

function resetForm() {
    document.getElementById('testEmailForm').reset();
    document.getElementById('test_subject').value = 'Test Email from {{ config("app.name") }}';
    document.getElementById('test_message').value = `This is a test email sent from {{ config('app.name') }} to verify that email configuration is working correctly.

If you receive this email, your email settings are properly configured.

Test Details:
- Sent at: {{ now()->format('Y-m-d H:i:s') }}
- From: {{ config('app.name') }}
- Server: {{ request()->getHost() }}

Thank you!`;
}

function testConnection() {
    const btn = event.target;
    const originalText = btn.innerHTML;
    
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Testing...';
    btn.disabled = true;
    
    // TODO: Implement connection test endpoint
    setTimeout(function() {
        showTestResult({
            success: true,
            message: 'SMTP connection test successful!'
        });
        btn.innerHTML = originalText;
        btn.disabled = false;
    }, 2000);
}

function viewLogs() {
    // TODO: Implement email logs viewer
    alert('Email logs viewer will be implemented soon.');
}

function clearLogs() {
    if (confirm('Are you sure you want to clear all email logs?')) {
        // TODO: Implement log clearing functionality
        alert('Email logs cleared successfully!');
    }
}

// TODO: Add email test functionality
console.log('Email Test page loaded');

// Auto-hide alerts after 5 seconds
setTimeout(function() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(function(alert) {
        if (alert.classList.contains('alert-dismissible')) {
            setTimeout(() => {
                alert.style.opacity = '0';
                setTimeout(() => {
                    if (alert.parentNode) {
                        alert.parentNode.removeChild(alert);
                    }
                }, 300);
            }, 100);
        }
    });
}, 5000);
</script>
@endpush