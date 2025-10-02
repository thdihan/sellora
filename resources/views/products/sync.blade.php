@extends('layouts.app')

@section('title', 'Product API Sync')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Product API Sync</h3>
                    <div class="card-tools">
                        <a href="{{ route('products.index') }}" class="btn btn-sm btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Products
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
                                    <h5 class="card-title">API Configuration</h5>
                                </div>
                                <div class="card-body">
                                    <form action="{{ route('products.sync.process') }}" method="POST" id="syncForm">
                                        @csrf
                                        
                                        <div class="mb-3">
                                            <label for="api_provider" class="form-label">API Provider</label>
                                            <select class="form-select" id="api_provider" name="api_provider" required>
                                                <option value="">Select API provider...</option>
                                                <option value="shopify">Shopify</option>
                                                <option value="woocommerce">WooCommerce</option>
                                                <option value="magento">Magento</option>
                                                <option value="prestashop">PrestaShop</option>
                                                <option value="bigcommerce">BigCommerce</option>
                                                <option value="custom">Custom API</option>
                                            </select>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="api_url" class="form-label">API URL</label>
                                            <input 
                                                type="url" 
                                                class="form-control @error('api_url') is-invalid @enderror" 
                                                id="api_url" 
                                                name="api_url" 
                                                placeholder="https://your-store.myshopify.com/admin/api/2023-10/products.json"
                                                required>
                                            @error('api_url')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="api_key" class="form-label">API Key</label>
                                            <input 
                                                type="password" 
                                                class="form-control @error('api_key') is-invalid @enderror" 
                                                id="api_key" 
                                                name="api_key" 
                                                placeholder="Enter your API key"
                                                required>
                                            @error('api_key')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="api_secret" class="form-label">API Secret (if required)</label>
                                            <input 
                                                type="password" 
                                                class="form-control" 
                                                id="api_secret" 
                                                name="api_secret" 
                                                placeholder="Enter your API secret">
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="sync_direction" class="form-label">Sync Direction</label>
                                            <select class="form-select" id="sync_direction" name="sync_direction" required>
                                                <option value="import">Import from API (API → Sellora)</option>
                                                <option value="export">Export to API (Sellora → API)</option>
                                                <option value="bidirectional">Bidirectional Sync</option>
                                            </select>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="sync_fields" class="form-label">Fields to Sync</label>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" id="sync_name" name="sync_fields[]" value="name" checked>
                                                        <label class="form-check-label" for="sync_name">Product Name</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" id="sync_description" name="sync_fields[]" value="description" checked>
                                                        <label class="form-check-label" for="sync_description">Description</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" id="sync_price" name="sync_fields[]" value="price" checked>
                                                        <label class="form-check-label" for="sync_price">Price</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" id="sync_stock" name="sync_fields[]" value="stock" checked>
                                                        <label class="form-check-label" for="sync_stock">Stock Quantity</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" id="sync_sku" name="sync_fields[]" value="sku" checked>
                                                        <label class="form-check-label" for="sync_sku">SKU</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" id="sync_categories" name="sync_fields[]" value="categories">
                                                        <label class="form-check-label" for="sync_categories">Categories</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" id="sync_images" name="sync_fields[]" value="images">
                                                        <label class="form-check-label" for="sync_images">Images</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" id="sync_variants" name="sync_fields[]" value="variants">
                                                        <label class="form-check-label" for="sync_variants">Variants</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="test_connection" name="test_connection" checked>
                                                <label class="form-check-label" for="test_connection">
                                                    Test connection before sync
                                                </label>
                                            </div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="backup_before_sync" name="backup_before_sync" checked>
                                                <label class="form-check-label" for="backup_before_sync">
                                                    Create backup before sync
                                                </label>
                                            </div>
                                        </div>
                                        
                                        <div class="d-flex gap-2">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-sync"></i> Start Sync
                                            </button>
                                            <button type="button" class="btn btn-info" onclick="testConnection()">
                                                <i class="fas fa-plug"></i> Test Connection
                                            </button>
                                            <button type="button" class="btn btn-secondary" onclick="saveConfig()">
                                                <i class="fas fa-save"></i> Save Config
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Sync Status</h5>
                                </div>
                                <div class="card-body">
                                    <div id="sync-status">
                                        <div class="mb-2">
                                            <strong>Last Sync:</strong> <span id="last-sync">Never</span>
                                        </div>
                                        <div class="mb-2">
                                            <strong>Status:</strong> <span id="sync-state" class="badge bg-secondary">Idle</span>
                                        </div>
                                        <div class="mb-2">
                                            <strong>Products Synced:</strong> <span id="products-synced">0</span>
                                        </div>
                                        <div class="mb-2">
                                            <strong>Errors:</strong> <span id="sync-errors">0</span>
                                        </div>
                                    </div>
                                    
                                    <div id="sync-progress" class="d-none mt-3">
                                        <div class="progress mb-2">
                                            <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%"></div>
                                        </div>
                                        <small class="text-muted">Syncing products...</small>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="card mt-3">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">API Guidelines</h5>
                                </div>
                                <div class="card-body">
                                    <h6>Supported Platforms:</h6>
                                    <ul class="small">
                                        <li><strong>Shopify:</strong> REST Admin API</li>
                                        <li><strong>WooCommerce:</strong> REST API</li>
                                        <li><strong>Magento:</strong> REST API</li>
                                        <li><strong>PrestaShop:</strong> Web Service API</li>
                                        <li><strong>BigCommerce:</strong> Store API</li>
                                        <li><strong>Custom:</strong> JSON REST API</li>
                                    </ul>
                                    
                                    <div class="alert alert-info small mt-3">
                                        <strong>Tip:</strong> Always test the connection before running a full sync.
                                    </div>
                                </div>
                            </div>
                            
                            <div class="card mt-3">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Sync History</h5>
                                </div>
                                <div class="card-body">
                                    <div id="sync-history">
                                        <div class="text-muted small">No sync history available</div>
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
function testConnection() {
    const apiUrl = document.getElementById('api_url').value;
    const apiKey = document.getElementById('api_key').value;
    
    if (!apiUrl || !apiKey) {
        alert('Please enter API URL and API Key first.');
        return;
    }
    
    // TODO: Implement API connection test
    alert('Connection test feature coming soon!');
}

function saveConfig() {
    // TODO: Implement configuration saving
    alert('Configuration save feature coming soon!');
}

// API provider change handler
document.getElementById('api_provider').addEventListener('change', function(e) {
    const provider = e.target.value;
    const apiUrlField = document.getElementById('api_url');
    
    // Set placeholder based on provider
    switch(provider) {
        case 'shopify':
            apiUrlField.placeholder = 'https://your-store.myshopify.com/admin/api/2023-10/products.json';
            break;
        case 'woocommerce':
            apiUrlField.placeholder = 'https://your-store.com/wp-json/wc/v3/products';
            break;
        case 'magento':
            apiUrlField.placeholder = 'https://your-store.com/rest/V1/products';
            break;
        case 'prestashop':
            apiUrlField.placeholder = 'https://your-store.com/api/products';
            break;
        case 'bigcommerce':
            apiUrlField.placeholder = 'https://api.bigcommerce.com/stores/{store_hash}/v3/catalog/products';
            break;
        default:
            apiUrlField.placeholder = 'https://your-api-endpoint.com/products';
    }
});

// TODO: Add real-time sync progress and status updates
console.log('Product API Sync page loaded');
</script>
@endpush