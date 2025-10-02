@extends('layouts.app')

@section('title', 'Theme Settings')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Theme Settings</h3>
                    <div class="card-tools">
                        <a href="{{ route('settings.index') }}" class="btn btn-secondary">
                            Back to Settings
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
                    
                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    
                    <form id="themeForm" action="{{ route('settings.theme.update') }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Color Scheme</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="primary_color" class="form-label">Primary Color</label>
                                            <div class="input-group">
                                                <input type="color" class="form-control form-control-color" id="primary_color" name="primary_color" value="{{ $themeSettings['primary_color'] }}" title="Choose primary color">
                                                <input type="text" class="form-control" id="primary_color_text" value="{{ $themeSettings['primary_color'] }}" readonly>
                                            </div>
                                            <small class="form-text text-muted">Main brand color used for buttons, links, and highlights</small>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="secondary_color" class="form-label">Secondary Color</label>
                                            <div class="input-group">
                                                <input type="color" class="form-control form-control-color" id="secondary_color" name="secondary_color" value="{{ $themeSettings['secondary_color'] }}" title="Choose secondary color">
                                                <input type="text" class="form-control" id="secondary_color_text" value="{{ $themeSettings['secondary_color'] }}" readonly>
                                            </div>
                                            <small class="form-text text-muted">Secondary color for borders, backgrounds, and subtle elements</small>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="dark_mode" name="dark_mode" value="1" {{ $themeSettings['dark_mode'] ? 'checked' : '' }}>
                                                <label class="form-check-label" for="dark_mode">
                                                    Enable Dark Mode
                                                </label>
                                            </div>
                                            <small class="form-text text-muted">Switch between light and dark theme</small>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="sidebar_style" class="form-label">Sidebar Style</label>
                                            <select class="form-select" id="sidebar_style" name="sidebar_style">
                                                <option value="default" {{ $themeSettings['sidebar_style'] === 'default' ? 'selected' : '' }}>Default</option>
                                                <option value="compact" {{ $themeSettings['sidebar_style'] === 'compact' ? 'selected' : '' }}>Compact</option>
                                                <option value="mini" {{ $themeSettings['sidebar_style'] === 'mini' ? 'selected' : '' }}>Mini</option>
                                            </select>
                                            <small class="form-text text-muted">Choose sidebar layout style</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Typography</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="font_family" class="form-label">Font Family</label>
                                            <select class="form-select" id="font_family" name="font_family">
                                                <option value="Inter" {{ $themeSettings['font_family'] === 'Inter' ? 'selected' : '' }}>Inter</option>
                                                <option value="Roboto" {{ $themeSettings['font_family'] === 'Roboto' ? 'selected' : '' }}>Roboto</option>
                                                <option value="Open Sans" {{ $themeSettings['font_family'] === 'Open Sans' ? 'selected' : '' }}>Open Sans</option>
                                                <option value="Lato" {{ $themeSettings['font_family'] === 'Lato' ? 'selected' : '' }}>Lato</option>
                                                <option value="Poppins" {{ $themeSettings['font_family'] === 'Poppins' ? 'selected' : '' }}>Poppins</option>
                                                <option value="Nunito" {{ $themeSettings['font_family'] === 'Nunito' ? 'selected' : '' }}>Nunito</option>
                                            </select>
                                            <small class="form-text text-muted">Choose the main font for the application</small>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="font_size" class="form-label">Base Font Size</label>
                                            <select class="form-select" id="font_size" name="font_size">
                                                <option value="12px" {{ $themeSettings['font_size'] === '12px' ? 'selected' : '' }}>Small (12px)</option>
                                                <option value="14px" {{ $themeSettings['font_size'] === '14px' ? 'selected' : '' }}>Medium (14px)</option>
                                                <option value="16px" {{ $themeSettings['font_size'] === '16px' ? 'selected' : '' }}>Large (16px)</option>
                                                <option value="18px" {{ $themeSettings['font_size'] === '18px' ? 'selected' : '' }}>Extra Large (18px)</option>
                                            </select>
                                            <small class="form-text text-muted">Base font size for the application</small>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-label">Font Preview</label>
                                            <div class="border rounded p-3" id="font_preview" style="font-family: {{ $themeSettings['font_family'] }}; font-size: {{ $themeSettings['font_size'] }};">
                                                <h5>Sample Heading</h5>
                                                <p>This is a sample paragraph to preview the selected font family and size. Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
                                                <small class="text-muted">Small text example</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="card mt-3">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Theme Preview</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="theme-preview" id="theme_preview">
                                            <div class="preview-header" style="background-color: {{ $themeSettings['primary_color'] }}; color: white; padding: 10px; border-radius: 4px 4px 0 0;">
                                                <strong>Header Preview</strong>
                                            </div>
                                            <div class="preview-content" style="border: 1px solid {{ $themeSettings['secondary_color'] }}; padding: 15px; border-radius: 0 0 4px 4px;">
                                                <button type="button" class="btn btn-sm mb-2" style="background-color: {{ $themeSettings['primary_color'] }}; color: white; border: none;">Primary Button</button>
                                                <button type="button" class="btn btn-sm btn-outline-secondary mb-2">Secondary Button</button>
                                                <p class="mb-2" style="font-family: {{ $themeSettings['font_family'] }}; font-size: {{ $themeSettings['font_size'] }};">Sample content with selected typography</p>
                                                <small class="text-muted">Preview of theme colors and fonts</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <button type="button" class="btn btn-outline-secondary" onclick="resetToDefaults()">
                                            <i class="fas fa-undo"></i> Reset to Defaults
                                        </button>
                                    </div>
                                    <div>
                                        <button type="button" class="btn btn-outline-primary me-2" onclick="previewChanges()">
                                            <i class="fas fa-eye"></i> Preview Changes
                                        </button>
                                        <button type="submit" class="btn btn-success">
                                            <i class="fas fa-save"></i> Save Theme Settings
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Update color text inputs when color picker changes
document.getElementById('primary_color').addEventListener('change', function() {
    document.getElementById('primary_color_text').value = this.value;
    updatePreview();
});

document.getElementById('secondary_color').addEventListener('change', function() {
    document.getElementById('secondary_color_text').value = this.value;
    updatePreview();
});

// Update font preview when selections change
document.getElementById('font_family').addEventListener('change', updateFontPreview);
document.getElementById('font_size').addEventListener('change', updateFontPreview);

function updateFontPreview() {
    const fontFamily = document.getElementById('font_family').value;
    const fontSize = document.getElementById('font_size').value;
    const preview = document.getElementById('font_preview');
    
    preview.style.fontFamily = fontFamily;
    preview.style.fontSize = fontSize;
    
    updatePreview();
}

function updatePreview() {
    const primaryColor = document.getElementById('primary_color').value;
    const secondaryColor = document.getElementById('secondary_color').value;
    const fontFamily = document.getElementById('font_family').value;
    const fontSize = document.getElementById('font_size').value;
    
    const preview = document.getElementById('theme_preview');
    const header = preview.querySelector('.preview-header');
    const content = preview.querySelector('.preview-content');
    const button = preview.querySelector('.btn');
    const text = preview.querySelector('p');
    
    header.style.backgroundColor = primaryColor;
    content.style.borderColor = secondaryColor;
    button.style.backgroundColor = primaryColor;
    text.style.fontFamily = fontFamily;
    text.style.fontSize = fontSize;
}

function resetToDefaults() {
    if (confirm('Are you sure you want to reset all theme settings to defaults?')) {
        document.getElementById('primary_color').value = '#007bff';
        document.getElementById('secondary_color').value = '#6c757d';
        document.getElementById('dark_mode').checked = false;
        document.getElementById('sidebar_style').value = 'default';
        document.getElementById('font_family').value = 'Inter';
        document.getElementById('font_size').value = '14px';
        
        // Update text inputs
        document.getElementById('primary_color_text').value = '#007bff';
        document.getElementById('secondary_color_text').value = '#6c757d';
        
        updateFontPreview();
        updatePreview();
    }
}

function previewChanges() {
    // TODO: Implement live preview functionality
    alert('Live preview functionality coming soon!');
}

// TODO: Add theme settings functionality
console.log('Theme Settings page loaded');

// Auto-hide alerts after 5 seconds
setTimeout(function() {
    $('.alert').fadeOut('slow');
}, 5000);

// Initialize preview on page load
updatePreview();
</script>
@endpush