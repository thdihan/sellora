@php
    $footerBrandHtml = App\Models\Setting::get('footer_brand_html', '');
    $isLocked = App\Models\Setting::get('footer_brand_locked', 'false') === 'true';
    $canModify = !$isLocked || (auth()->user() && auth()->user()->hasRole('Author'));
@endphp

@if($footerBrandHtml)
<footer class="footer-brand" role="contentinfo" aria-label="Footer brand information">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="footer-brand-content text-center py-3">
                    <div class="brand-logo mb-2">
                        <img src="{{ asset('assets/brand/logo.svg') }}" alt="Sellora Logo" class="footer-logo" width="120" height="40">
                    </div>
                    <div class="brand-text">
                        {!! $footerBrandHtml !!}
                    </div>
                    @if(!$canModify)
                        <div class="brand-edit-controls mt-3">
                            <div class="alert alert-info border-0 shadow-sm" role="alert">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-shield-alt text-info fa-lg" aria-hidden="true"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="alert-heading mb-1 text-info">
                                            <i class="fas fa-lock me-1" aria-hidden="true"></i>
                                            Protected Content
                                        </h6>
                                        <p class="mb-0 small text-muted">
                                            This content is restricted and requires <strong>Author</strong> level access to modify.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</footer>

<style>
.footer-brand {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-top: 1px solid #dee2e6;
    margin-top: auto;
    box-shadow: 0 -2px 4px rgba(0,0,0,0.1);
}

.footer-brand-content {
    color: #6c757d;
    font-size: 0.875rem;
    line-height: 1.5;
}

.footer-logo {
    max-width: 120px;
    height: auto;
    filter: drop-shadow(0 1px 2px rgba(0,0,0,0.1));
}

.brand-text a {
    color: #007bff;
    text-decoration: none;
    font-weight: 500;
    transition: color 0.2s ease;
}

.brand-text a:hover {
    color: #0056b3;
    text-decoration: underline;
}

.brand-text a:focus {
    outline: 2px solid #007bff;
    outline-offset: 2px;
    border-radius: 2px;
}

.brand-edit-controls {
    max-width: 500px;
    margin: 0 auto;
}

.brand-edit-controls .alert {
    background: linear-gradient(135deg, #e3f2fd 0%, #f3e5f5 100%);
    border-left: 4px solid #17a2b8;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.brand-edit-controls .alert:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(23, 162, 184, 0.15) !important;
}

.brand-edit-controls .alert-heading {
    font-weight: 600;
    font-size: 0.9rem;
}

.brand-edit-controls .alert p {
    font-size: 0.8rem;
    line-height: 1.4;
}

@media (max-width: 768px) {
    .footer-brand-content {
        font-size: 0.8rem;
    }
    
    .footer-logo {
        max-width: 100px;
    }
}
</style>
@endif