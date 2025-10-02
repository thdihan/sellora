@extends('layouts.app')

@section('title', 'API Connector Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">API Connector Dashboard</h1>
            <p class="text-muted">Manage external system integrations and synchronization</p>
        </div>
        <div>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#syncModal">
                <i class="fas fa-sync-alt me-2"></i>Start Sync
            </button>
            <button type="button" class="btn btn-outline-secondary" onclick="refreshDashboard()">
                <i class="fas fa-refresh me-2"></i>Refresh
            </button>
        </div>
    </div>

    <!-- System Status Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Active Connections
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="activeConnections">-</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-plug fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Successful Syncs (24h)
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="successfulSyncs">-</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Failed Syncs (24h)
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="failedSyncs">-</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Items Synced (24h)
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="itemsSynced">-</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-database fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- External Systems Configuration -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">External Systems</h6>
                    <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addSystemModal">
                        <i class="fas fa-plus me-1"></i>Add System
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="systemsTable">
                            <thead>
                                <tr>
                                    <th>System</th>
                                    <th>Status</th>
                                    <th>Last Sync</th>
                                    <th>Items</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Systems will be loaded via AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Sync Activity</h6>
                </div>
                <div class="card-body">
                    <div id="recentActivity">
                        <!-- Recent activity will be loaded via AJAX -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sync Logs -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Sync Logs</h6>
            <div>
                <select class="form-select form-select-sm" id="logFilter">
                    <option value="all">All Logs</option>
                    <option value="completed">Completed</option>
                    <option value="failed">Failed</option>
                    <option value="processing">Processing</option>
                </select>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="logsTable">
                    <thead>
                        <tr>
                            <th>Timestamp</th>
                            <th>System</th>
                            <th>Type</th>
                            <th>Direction</th>
                            <th>Status</th>
                            <th>Items</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Logs will be loaded via AJAX -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Start Sync Modal -->
<div class="modal fade" id="syncModal" tabindex="-1" aria-labelledby="syncModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="syncModalLabel">Start Synchronization</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="syncForm">
                    <div class="mb-3">
                        <label for="externalSystem" class="form-label">External System</label>
                        <select class="form-select" id="externalSystem" required>
                            <option value="">Select System</option>
                            <option value="shopify">Shopify</option>
                            <option value="woocommerce">WooCommerce</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="syncType" class="form-label">Sync Type</label>
                        <select class="form-select" id="syncType" required>
                            <option value="">Select Type</option>
                            <option value="products">Products</option>
                            <option value="orders">Orders</option>
                            <option value="customers">Customers</option>
                            <option value="inventory">Inventory</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="syncDirection" class="form-label">Direction</label>
                        <select class="form-select" id="syncDirection" required>
                            <option value="">Select Direction</option>
                            <option value="pull">Pull (Import from External)</option>
                            <option value="push">Push (Export to External)</option>
                            <option value="bidirectional">Bidirectional</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="batchSize" class="form-label">Batch Size</label>
                        <input type="number" class="form-control" id="batchSize" value="100" min="1" max="1000">
                        <div class="form-text">Number of items to process per batch (1-1000)</div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="startSync()">Start Sync</button>
            </div>
        </div>
    </div>
</div>

<!-- Add System Modal -->
<div class="modal fade" id="addSystemModal" tabindex="-1" aria-labelledby="addSystemModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addSystemModalLabel">Add External System</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addSystemForm">
                    <div class="mb-3">
                        <label for="systemType" class="form-label">System Type</label>
                        <select class="form-select" id="systemType" required onchange="showSystemConfig()">
                            <option value="">Select System Type</option>
                            <option value="shopify">Shopify</option>
                            <option value="woocommerce">WooCommerce</option>
                        </select>
                    </div>
                    
                    <!-- Shopify Configuration -->
                    <div id="shopifyConfig" class="system-config" style="display: none;">
                        <h6 class="text-primary mb-3">Shopify Configuration</h6>
                        <div class="mb-3">
                            <label for="shopifyUrl" class="form-label">Shop URL</label>
                            <input type="url" class="form-control" id="shopifyUrl" placeholder="https://your-shop.myshopify.com">
                        </div>
                        <div class="mb-3">
                            <label for="shopifyAccessToken" class="form-label">Access Token</label>
                            <input type="password" class="form-control" id="shopifyAccessToken">
                        </div>
                        <div class="mb-3">
                            <label for="shopifyApiVersion" class="form-label">API Version</label>
                            <input type="text" class="form-control" id="shopifyApiVersion" value="2023-10">
                        </div>
                    </div>
                    
                    <!-- WooCommerce Configuration -->
                    <div id="woocommerceConfig" class="system-config" style="display: none;">
                        <h6 class="text-primary mb-3">WooCommerce Configuration</h6>
                        <div class="mb-3">
                            <label for="wooUrl" class="form-label">Store URL</label>
                            <input type="url" class="form-control" id="wooUrl" placeholder="https://your-store.com">
                        </div>
                        <div class="mb-3">
                            <label for="wooConsumerKey" class="form-label">Consumer Key</label>
                            <input type="text" class="form-control" id="wooConsumerKey">
                        </div>
                        <div class="mb-3">
                            <label for="wooConsumerSecret" class="form-label">Consumer Secret</label>
                            <input type="password" class="form-control" id="wooConsumerSecret">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" onclick="testConnection()">Test Connection</button>
                <button type="button" class="btn btn-primary" onclick="saveSystem()">Save System</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Dashboard data refresh interval
let refreshInterval;

// Initialize dashboard
$(document).ready(function() {
    loadDashboardData();
    loadSystems();
    loadRecentActivity();
    loadSyncLogs();
    
    // Set up auto-refresh
    refreshInterval = setInterval(loadDashboardData, 30000); // Refresh every 30 seconds
    
    // Log filter change handler
    $('#logFilter').change(function() {
        loadSyncLogs();
    });
});

// Load dashboard statistics
function loadDashboardData() {
    $.get('/api/connector/dashboard')
        .done(function(data) {
            $('#activeConnections').text(data.active_connections || 0);
            $('#successfulSyncs').text(data.successful_syncs_24h || 0);
            $('#failedSyncs').text(data.failed_syncs_24h || 0);
            $('#itemsSynced').text(data.items_synced_24h || 0);
        })
        .fail(function() {
            console.error('Failed to load dashboard data');
        });
}

// Load external systems
function loadSystems() {
    const tbody = $('#systemsTable tbody');
    tbody.html('<tr><td colspan="5" class="text-center">Loading...</td></tr>');
    
    // This would typically load from an API endpoint
    // For now, showing sample data
    const sampleSystems = [
        {
            name: 'Shopify Store',
            type: 'shopify',
            status: 'connected',
            last_sync: '2024-01-15 10:30:00',
            items: 1250
        },
        {
            name: 'WooCommerce Store',
            type: 'woocommerce',
            status: 'disconnected',
            last_sync: '2024-01-14 15:45:00',
            items: 890
        }
    ];
    
    tbody.empty();
    sampleSystems.forEach(function(system) {
        const statusBadge = system.status === 'connected' 
            ? '<span class="badge bg-success">Connected</span>'
            : '<span class="badge bg-danger">Disconnected</span>';
            
        const row = `
            <tr>
                <td>
                    <div class="d-flex align-items-center">
                        <i class="fab fa-${system.type} me-2"></i>
                        ${system.name}
                    </div>
                </td>
                <td>${statusBadge}</td>
                <td>${system.last_sync}</td>
                <td>${system.items}</td>
                <td>
                    <button class="btn btn-sm btn-outline-primary me-1" onclick="configureSystem('${system.type}')">
                        <i class="fas fa-cog"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-success me-1" onclick="syncSystem('${system.type}')">
                        <i class="fas fa-sync"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-danger" onclick="removeSystem('${system.type}')">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;
        tbody.append(row);
    });
}

// Load recent activity
function loadRecentActivity() {
    const container = $('#recentActivity');
    container.html('<div class="text-center">Loading...</div>');
    
    // Sample recent activity data
    const activities = [
        {
            type: 'sync_completed',
            system: 'Shopify',
            message: 'Product sync completed',
            time: '5 minutes ago',
            status: 'success'
        },
        {
            type: 'sync_failed',
            system: 'WooCommerce',
            message: 'Order sync failed',
            time: '15 minutes ago',
            status: 'error'
        },
        {
            type: 'webhook_received',
            system: 'Shopify',
            message: 'Product updated webhook',
            time: '1 hour ago',
            status: 'info'
        }
    ];
    
    container.empty();
    activities.forEach(function(activity) {
        const iconClass = activity.status === 'success' ? 'text-success fas fa-check-circle'
            : activity.status === 'error' ? 'text-danger fas fa-exclamation-circle'
            : 'text-info fas fa-info-circle';
            
        const item = `
            <div class="d-flex align-items-center mb-3">
                <div class="me-3">
                    <i class="${iconClass}"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="small font-weight-bold">${activity.system}</div>
                    <div class="small text-muted">${activity.message}</div>
                    <div class="small text-muted">${activity.time}</div>
                </div>
            </div>
        `;
        container.append(item);
    });
}

// Load sync logs
function loadSyncLogs() {
    const tbody = $('#logsTable tbody');
    const filter = $('#logFilter').val();
    
    tbody.html('<tr><td colspan="7" class="text-center">Loading...</td></tr>');
    
    $.get('/api/connector/logs', { status: filter })
        .done(function(data) {
            tbody.empty();
            if (data.logs && data.logs.length > 0) {
                data.logs.forEach(function(log) {
                    const statusBadge = getStatusBadge(log.status);
                    const row = `
                        <tr>
                            <td>${log.created_at}</td>
                            <td>${log.external_system}</td>
                            <td>${log.sync_type}</td>
                            <td>${log.operation}</td>
                            <td>${statusBadge}</td>
                            <td>${log.processed_items || '-'}</td>
                            <td>
                                <button class="btn btn-sm btn-outline-info me-1" onclick="viewLogDetails(${log.id})">
                                    <i class="fas fa-eye"></i>
                                </button>
                                ${log.status === 'failed' ? `<button class="btn btn-sm btn-outline-warning" onclick="retrySync(${log.id})"><i class="fas fa-redo"></i></button>` : ''}
                            </td>
                        </tr>
                    `;
                    tbody.append(row);
                });
            } else {
                tbody.html('<tr><td colspan="7" class="text-center text-muted">No logs found</td></tr>');
            }
        })
        .fail(function() {
            tbody.html('<tr><td colspan="7" class="text-center text-danger">Failed to load logs</td></tr>');
        });
}

// Get status badge HTML
function getStatusBadge(status) {
    switch (status) {
        case 'completed':
            return '<span class="badge bg-success">Completed</span>';
        case 'failed':
            return '<span class="badge bg-danger">Failed</span>';
        case 'processing':
            return '<span class="badge bg-warning">Processing</span>';
        default:
            return '<span class="badge bg-secondary">Unknown</span>';
    }
}

// Start sync operation
function startSync() {
    const formData = {
        external_system: $('#externalSystem').val(),
        sync_type: $('#syncType').val(),
        sync_direction: $('#syncDirection').val(),
        batch_size: $('#batchSize').val()
    };
    
    if (!formData.external_system || !formData.sync_type || !formData.sync_direction) {
        alert('Please fill in all required fields');
        return;
    }
    
    $.post('/api/connector/sync', formData)
        .done(function(response) {
            $('#syncModal').modal('hide');
            alert('Sync started successfully! Batch ID: ' + response.batch_id);
            loadDashboardData();
            loadSyncLogs();
        })
        .fail(function(xhr) {
            alert('Failed to start sync: ' + (xhr.responseJSON?.message || 'Unknown error'));
        });
}

// Show system configuration based on type
function showSystemConfig() {
    const systemType = $('#systemType').val();
    $('.system-config').hide();
    if (systemType) {
        $(`#${systemType}Config`).show();
    }
}

// Test API connection
function testConnection() {
    const systemType = $('#systemType').val();
    if (!systemType) {
        alert('Please select a system type first');
        return;
    }
    
    // Collect configuration data based on system type
    let configData = { system: systemType };
    
    if (systemType === 'shopify') {
        configData.base_url = $('#shopifyUrl').val();
        configData.access_token = $('#shopifyAccessToken').val();
        configData.version = $('#shopifyApiVersion').val();
    } else if (systemType === 'woocommerce') {
        configData.base_url = $('#wooUrl').val();
        configData.consumer_key = $('#wooConsumerKey').val();
        configData.consumer_secret = $('#wooConsumerSecret').val();
    }
    
    $.post(`/api/connector/test/${systemType}`, configData)
        .done(function(response) {
            if (response.success) {
                alert('Connection test successful!');
            } else {
                alert('Connection test failed: ' + response.message);
            }
        })
        .fail(function(xhr) {
            alert('Connection test failed: ' + (xhr.responseJSON?.message || 'Unknown error'));
        });
}

// Save system configuration
function saveSystem() {
    const systemType = $('#systemType').val();
    if (!systemType) {
        alert('Please select a system type first');
        return;
    }
    
    // Collect and save configuration
    let configData = { system: systemType };
    
    if (systemType === 'shopify') {
        configData.base_url = $('#shopifyUrl').val();
        configData.access_token = $('#shopifyAccessToken').val();
        configData.version = $('#shopifyApiVersion').val();
    } else if (systemType === 'woocommerce') {
        configData.base_url = $('#wooUrl').val();
        configData.consumer_key = $('#wooConsumerKey').val();
        configData.consumer_secret = $('#wooConsumerSecret').val();
    }
    
    $.ajax({
        url: `/api/connector/config/${systemType}`,
        method: 'PUT',
        data: configData
    })
    .done(function(response) {
        $('#addSystemModal').modal('hide');
        alert('System configuration saved successfully!');
        loadSystems();
    })
    .fail(function(xhr) {
        alert('Failed to save configuration: ' + (xhr.responseJSON?.message || 'Unknown error'));
    });
}

// Refresh dashboard
function refreshDashboard() {
    loadDashboardData();
    loadSystems();
    loadRecentActivity();
    loadSyncLogs();
}

// Configure system
function configureSystem(systemType) {
    // Load existing configuration and show modal
    $('#systemType').val(systemType).trigger('change');
    
    $.get(`/api/connector/config/${systemType}`)
        .done(function(config) {
            if (systemType === 'shopify') {
                $('#shopifyUrl').val(config.base_url || '');
                $('#shopifyAccessToken').val(config.access_token || '');
                $('#shopifyApiVersion').val(config.version || '2023-10');
            } else if (systemType === 'woocommerce') {
                $('#wooUrl').val(config.base_url || '');
                $('#wooConsumerKey').val(config.consumer_key || '');
                $('#wooConsumerSecret').val(config.consumer_secret || '');
            }
        })
        .always(function() {
            $('#addSystemModal').modal('show');
        });
}

// Sync specific system
function syncSystem(systemType) {
    $('#externalSystem').val(systemType);
    $('#syncModal').modal('show');
}

// Remove system
function removeSystem(systemType) {
    if (confirm(`Are you sure you want to remove the ${systemType} system configuration?`)) {
        // Implementation for removing system configuration
        alert('System removal functionality would be implemented here');
    }
}

// Retry failed sync
function retrySync(syncLogId) {
    if (confirm('Are you sure you want to retry this sync operation?')) {
        $.post(`/api/connector/retry/${syncLogId}`)
            .done(function(response) {
                alert('Sync retry initiated successfully!');
                loadSyncLogs();
            })
            .fail(function(xhr) {
                alert('Failed to retry sync: ' + (xhr.responseJSON?.message || 'Unknown error'));
            });
    }
}

// View log details
function viewLogDetails(logId) {
    // Implementation for viewing detailed log information
    alert('Log details view would be implemented here');
}

// Clean up interval on page unload
$(window).on('beforeunload', function() {
    if (refreshInterval) {
        clearInterval(refreshInterval);
    }
});
</script>
@endpush

@push('styles')
<style>
.border-left-primary {
    border-left: 0.25rem solid #4e73df !important;
}

.border-left-success {
    border-left: 0.25rem solid #1cc88a !important;
}

.border-left-warning {
    border-left: 0.25rem solid #f6c23e !important;
}

.border-left-info {
    border-left: 0.25rem solid #36b9cc !important;
}

.system-config {
    border: 1px solid #e3e6f0;
    border-radius: 0.35rem;
    padding: 1rem;
    margin-top: 1rem;
}

.card {
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15) !important;
}

.table th {
    border-top: none;
    font-weight: 600;
    color: #5a5c69;
}

.badge {
    font-size: 0.75em;
}

.btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.75rem;
}

#recentActivity {
    max-height: 400px;
    overflow-y: auto;
}

.text-xs {
    font-size: 0.7rem;
}

.font-weight-bold {
    font-weight: 700 !important;
}

.text-gray-800 {
    color: #5a5c69 !important;
}

.text-gray-300 {
    color: #dddfeb !important;
}
</style>
@endpush