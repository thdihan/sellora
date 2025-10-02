@extends('layouts.app')

@section('title', 'Company Settings')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3><i class="fas fa-building"></i> Company Settings</h3>
                    <p class="text-muted mb-0">Configure company information and branding</p>
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
                    <h5 class="mb-0"><i class="fas fa-info-circle"></i> Company Information</h5>
                </div>
                <div class="card-body">
                    <form id="companyForm" action="{{ route('settings.company.update') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="company_name" class="form-label">Company Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="company_name" name="company_name" 
                                       value="{{ old('company_name', $companySettings['company_name'] ?? '') }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="company_email" class="form-label">Company Email</label>
                                <input type="email" class="form-control" id="company_email" name="company_email" 
                                       value="{{ old('company_email', $companySettings['company_email'] ?? '') }}">
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="company_phone" class="form-label">Company Phone</label>
                                <input type="tel" class="form-control" id="company_phone" name="company_phone" 
                                       value="{{ old('company_phone', $companySettings['company_phone'] ?? '') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="company_website" class="form-label">Website</label>
                                <input type="url" class="form-control" id="company_website" name="company_website" 
                                       value="{{ old('company_website', $companySettings['company_website'] ?? '') }}" 
                                       placeholder="https://example.com">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="company_address" class="form-label">Company Address</label>
                            <textarea class="form-control" id="company_address" name="company_address" rows="3" 
                                      placeholder="Enter complete company address...">{{ old('company_address', $companySettings['company_address'] ?? '') }}</textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="company_city" class="form-label">City</label>
                                <input type="text" class="form-control" id="company_city" name="company_city" 
                                       value="{{ old('company_city', $companySettings['company_city'] ?? '') }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="company_state" class="form-label">State/Province</label>
                                <input type="text" class="form-control" id="company_state" name="company_state" 
                                       value="{{ old('company_state', $companySettings['company_state'] ?? '') }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="company_zip" class="form-label">ZIP/Postal Code</label>
                                <input type="text" class="form-control" id="company_zip" name="company_zip" 
                                       value="{{ old('company_zip', $companySettings['company_zip'] ?? '') }}">
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="company_country" class="form-label">Country</label>
                                <select class="form-control" id="company_country" name="company_country">
                                    <option value="">Select Country</option>
                                    <option value="US" {{ ($companySettings['company_country'] ?? '') == 'US' ? 'selected' : '' }}>United States</option>
                                    <option value="CA" {{ ($companySettings['company_country'] ?? '') == 'CA' ? 'selected' : '' }}>Canada</option>
                                    <option value="GB" {{ ($companySettings['company_country'] ?? '') == 'GB' ? 'selected' : '' }}>United Kingdom</option>
                                    <option value="AU" {{ ($companySettings['company_country'] ?? '') == 'AU' ? 'selected' : '' }}>Australia</option>
                                    <option value="DE" {{ ($companySettings['company_country'] ?? '') == 'DE' ? 'selected' : '' }}>Germany</option>
                                    <option value="FR" {{ ($companySettings['company_country'] ?? '') == 'FR' ? 'selected' : '' }}>France</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="company_timezone" class="form-label">Company Timezone</label>
                                <select class="form-control" id="company_timezone" name="company_timezone">
                                    <option value="UTC" {{ ($companySettings['company_timezone'] ?? 'UTC') == 'UTC' ? 'selected' : '' }}>UTC</option>
                                    <option value="America/New_York" {{ ($companySettings['company_timezone'] ?? '') == 'America/New_York' ? 'selected' : '' }}>Eastern Time</option>
                                    <option value="America/Chicago" {{ ($companySettings['company_timezone'] ?? '') == 'America/Chicago' ? 'selected' : '' }}>Central Time</option>
                                    <option value="America/Denver" {{ ($companySettings['company_timezone'] ?? '') == 'America/Denver' ? 'selected' : '' }}>Mountain Time</option>
                                    <option value="America/Los_Angeles" {{ ($companySettings['company_timezone'] ?? '') == 'America/Los_Angeles' ? 'selected' : '' }}>Pacific Time</option>
                                </select>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <h6><i class="fas fa-palette"></i> Branding</h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="brand_color" class="form-label">Primary Brand Color</label>
                                <div class="input-group">
                                    <input type="color" class="form-control form-control-color" id="brand_color" 
                                           name="brand_color" value="{{ old('brand_color', $companySettings['brand_color'] ?? '#007bff') }}">
                                    <input type="text" class="form-control" id="brand_color_hex" 
                                           value="{{ old('brand_color', $companySettings['brand_color'] ?? '#007bff') }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="secondary_color" class="form-label">Secondary Color</label>
                                <div class="input-group">
                                    <input type="color" class="form-control form-control-color" id="secondary_color" 
                                           name="secondary_color" value="{{ old('secondary_color', $companySettings['secondary_color'] ?? '#6c757d') }}">
                                    <input type="text" class="form-control" id="secondary_color_hex" 
                                           value="{{ old('secondary_color', $companySettings['secondary_color'] ?? '#6c757d') }}" readonly>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="footer_brand_html" class="form-label">Footer Brand HTML</label>
                            <textarea class="form-control" id="footer_brand_html" name="footer_brand_html" rows="4" 
                                      placeholder="Enter footer brand HTML content...">{{ old('footer_brand_html', $companySettings['footer_brand_html'] ?? '') }}</textarea>
                            <div class="form-text">HTML content for the footer brand section. Supports links and basic formatting.</div>
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
                    <h5 class="mb-0"><i class="fas fa-eye"></i> Brand Preview</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h6>Primary Color</h6>
                        <div id="primary-preview" class="border rounded p-3 text-center text-white" 
                             style="background-color: {{ $companySettings['brand_color'] ?? '#007bff' }}">
                            Sample Text
                        </div>
                    </div>
                    <div class="mb-3">
                        <h6>Secondary Color</h6>
                        <div id="secondary-preview" class="border rounded p-3 text-center text-white" 
                             style="background-color: {{ $companySettings['secondary_color'] ?? '#6c757d' }}">
                            Sample Text
                        </div>
                    </div>
                    <div class="mb-3">
                        <h6>Footer Brand Preview</h6>
                        <div id="footer-preview" class="border rounded p-2 bg-light">
                            {!! $companySettings['footer_brand_html'] ?? '<em class="text-muted">No content set</em>' !!}
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-info-circle"></i> Information</h5>
                </div>
                <div class="card-body">
                    <p class="small text-muted">
                        Company settings affect the entire application branding and contact information. 
                        Changes here will be reflected across all user interfaces.
                    </p>
                    <ul class="small text-muted">
                        <li>Company name appears in headers and emails</li>
                        <li>Brand colors customize the application theme</li>
                        <li>Footer brand HTML appears on all pages</li>
                        <li>Contact information is used for support</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('companyForm');
    const brandColorInput = document.getElementById('brand_color');
    const brandColorHex = document.getElementById('brand_color_hex');
    const secondaryColorInput = document.getElementById('secondary_color');
    const secondaryColorHex = document.getElementById('secondary_color_hex');
    const footerHtmlInput = document.getElementById('footer_brand_html');
    const primaryPreview = document.getElementById('primary-preview');
    const secondaryPreview = document.getElementById('secondary-preview');
    const footerPreview = document.getElementById('footer-preview');
    
    // Color input handlers
    brandColorInput.addEventListener('input', function() {
        brandColorHex.value = this.value;
        primaryPreview.style.backgroundColor = this.value;
    });
    
    secondaryColorInput.addEventListener('input', function() {
        secondaryColorHex.value = this.value;
        secondaryPreview.style.backgroundColor = this.value;
    });
    
    // Footer HTML preview
    footerHtmlInput.addEventListener('input', function() {
        const content = this.value.trim();
        footerPreview.innerHTML = content || '<em class="text-muted">No content set</em>';
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
            showAlert('danger', 'An error occurred while updating company settings.');
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