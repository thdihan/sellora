@extends('layouts.app')

@section('title', 'Data Export')

@section('content')
@if(!auth()->user() || !auth()->user()->hasAnyRole(['Admin', 'Author', 'Manager']))
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="alert alert-danger" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Access Denied:</strong> You don't have permission to access the data export functionality. 
                    Please contact your administrator if you need access.
                </div>
                <a href="{{ route('settings.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Back to Settings
                </a>
            </div>
        </div>
    </div>
@else
<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Data Export</h1>
        <p class="text-gray-600 mt-2">Export your data in various formats for backup or migration</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Export Configuration -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-sm border p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-6">Export Configuration</h2>
                
                <form id="export-form">
                    <!-- Export Scope -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-3">Export Scope</label>
                        <div class="space-y-3">
                            <div class="flex items-center">
                                <input id="scope-full" name="scope" type="radio" value="full" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                                <label for="scope-full" class="ml-3 block text-sm text-gray-700">
                                    <span class="font-medium">Full Export</span>
                                    <span class="block text-gray-500">Export all data from selected modules</span>
                                </label>
                            </div>
                            <div class="flex items-center">
                                <input id="scope-partial" name="scope" type="radio" value="partial" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300" checked>
                                <label for="scope-partial" class="ml-3 block text-sm text-gray-700">
                                    <span class="font-medium">Partial Export</span>
                                    <span class="block text-gray-500">Export data with filters and date ranges</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Module Selection -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-3">Select Modules</label>
                        <div class="grid grid-cols-2 gap-3">
                            <div class="flex items-center">
                                <input id="module-products" name="modules[]" type="checkbox" value="products" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <label for="module-products" class="ml-3 text-sm text-gray-700">Products</label>
                            </div>
                            <div class="flex items-center">
                                <input id="module-customers" name="modules[]" type="checkbox" value="customers" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <label for="module-customers" class="ml-3 text-sm text-gray-700">Customers</label>
                            </div>
                            <div class="flex items-center">
                                <input id="module-orders" name="modules[]" type="checkbox" value="orders" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <label for="module-orders" class="ml-3 text-sm text-gray-700">Orders</label>
                            </div>
                            <div class="flex items-center">
                                <input id="module-inventory" name="modules[]" type="checkbox" value="inventory" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <label for="module-inventory" class="ml-3 text-sm text-gray-700">Inventory</label>
                            </div>
                            <div class="flex items-center">
                                <input id="module-users" name="modules[]" type="checkbox" value="users" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <label for="module-users" class="ml-3 text-sm text-gray-700">Users</label>
                            </div>
                            <div class="flex items-center">
                                <input id="module-settings" name="modules[]" type="checkbox" value="settings" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <label for="module-settings" class="ml-3 text-sm text-gray-700">Settings</label>
                            </div>
                        </div>
                    </div>

                    <!-- Export Format -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-3">Export Format</label>
                        <div class="grid grid-cols-3 gap-3">
                            <div class="flex items-center">
                                <input id="format-sql" name="format" type="radio" value="sql" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300" checked>
                                <label for="format-sql" class="ml-3 text-sm text-gray-700">SQL Dump</label>
                            </div>
                            <div class="flex items-center">
                                <input id="format-csv" name="format" type="radio" value="csv" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                                <label for="format-csv" class="ml-3 text-sm text-gray-700">CSV Files</label>
                            </div>
                            <div class="flex items-center">
                                <input id="format-excel" name="format" type="radio" value="excel" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                                <label for="format-excel" class="ml-3 text-sm text-gray-700">Excel</label>
                            </div>
                        </div>
                    </div>

                    <!-- Date Range (for partial export) -->
                    <div id="date-range-section" class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-3">Date Range</label>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="start-date" class="block text-xs text-gray-500 mb-1">Start Date</label>
                                <input type="date" id="start-date" name="start_date" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label for="end-date" class="block text-xs text-gray-500 mb-1">End Date</label>
                                <input type="date" id="end-date" name="end_date" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>
                    </div>

                    <!-- Advanced Options -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-3">Advanced Options</label>
                        <div class="space-y-3">
                            <div class="flex items-center">
                                <input id="include-dependencies" name="include_dependencies" type="checkbox" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" checked>
                                <label for="include-dependencies" class="ml-3 text-sm text-gray-700">
                                    Include related data and dependencies
                                </label>
                            </div>
                            <div class="flex items-center">
                                <input id="compress-output" name="compress_output" type="checkbox" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <label for="compress-output" class="ml-3 text-sm text-gray-700">
                                    Compress export file (ZIP)
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Export Button -->
                    <div class="flex justify-end">
                        <button type="submit" id="export-btn" class="px-6 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed">
                            Start Export
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Export History -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-sm border p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Recent Exports</h2>
                
                <div id="export-history" class="space-y-4">
                    <!-- Export history will be loaded here -->
                    <div class="text-center py-8 text-gray-500">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <p class="mt-2 text-sm">No exports yet</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Export Progress Modal -->
<div id="export-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-medium text-gray-900">Export in Progress</h3>
            <button id="close-modal" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        
        <div class="mb-4">
            <div class="flex items-center justify-between text-sm text-gray-600 mb-2">
                <span>Processing...</span>
                <span id="progress-percent">0%</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
                <div id="progress-bar" class="bg-blue-600 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
            </div>
        </div>
        
        <div id="export-status" class="text-sm text-gray-600">
            Preparing export...
        </div>
        
        <div id="export-complete" class="hidden">
            <div class="flex items-center text-green-600 mb-4">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                Export completed successfully!
            </div>
            <button id="download-btn" class="w-full px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700">
                Download Export
            </button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const exportForm = document.getElementById('export-form');
    const exportModal = document.getElementById('export-modal');
    const closeModal = document.getElementById('close-modal');
    const scopeRadios = document.querySelectorAll('input[name="scope"]');
    const dateRangeSection = document.getElementById('date-range-section');
    const moduleCheckboxes = document.querySelectorAll('input[name="modules[]"]');
    const exportBtn = document.getElementById('export-btn');
    
    let currentExportJob = null;
    
    // Toggle date range section based on scope
    scopeRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.value === 'full') {
                dateRangeSection.style.display = 'none';
            } else {
                dateRangeSection.style.display = 'block';
            }
        });
    });
    
    // Validate form
    function validateForm() {
        const selectedModules = Array.from(moduleCheckboxes).filter(cb => cb.checked);
        exportBtn.disabled = selectedModules.length === 0;
    }
    
    moduleCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', validateForm);
    });
    
    // Handle form submission
    exportForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const exportData = {
            scope: formData.get('scope'),
            modules: formData.getAll('modules[]'),
            format: formData.get('format'),
            include_dependencies: formData.has('include_dependencies'),
            compress_output: formData.has('compress_output')
        };
        
        if (exportData.scope === 'partial') {
            exportData.filters = {
                start_date: formData.get('start_date'),
                end_date: formData.get('end_date')
            };
        }
        
        startExport(exportData);
    });
    
    // Start export process
    function startExport(data) {
        exportModal.classList.remove('hidden');
        exportModal.classList.add('flex');
        
        fetch('/api/exports', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(result => {
            if (result.id) {
                currentExportJob = result.id;
                pollExportStatus();
            } else {
                throw new Error('Failed to start export');
            }
        })
        .catch(error => {
            console.error('Export error:', error);
            alert('Failed to start export. Please try again.');
            closeExportModal();
        });
    }
    
    // Poll export status
    function pollExportStatus() {
        if (!currentExportJob) return;
        
        fetch(`/api/exports/${currentExportJob}`)
        .then(response => response.json())
        .then(job => {
            updateProgress(job);
            
            if (job.status === 'completed') {
                showExportComplete(job);
            } else if (job.status === 'failed') {
                showExportError(job);
            } else {
                setTimeout(pollExportStatus, 2000);
            }
        })
        .catch(error => {
            console.error('Status check error:', error);
            setTimeout(pollExportStatus, 5000);
        });
    }
    
    // Update progress display
    function updateProgress(job) {
        const progress = job.stats?.progress || 0;
        document.getElementById('progress-bar').style.width = progress + '%';
        document.getElementById('progress-percent').textContent = Math.round(progress) + '%';
        
        let statusText = 'Processing...';
        if (job.status === 'processing') {
            statusText = `Exporting ${job.modules.join(', ')}...`;
        }
        document.getElementById('export-status').textContent = statusText;
    }
    
    // Show export completion
    function showExportComplete(job) {
        document.getElementById('export-status').style.display = 'none';
        document.getElementById('export-complete').classList.remove('hidden');
        
        document.getElementById('download-btn').addEventListener('click', function() {
            window.location.href = `/api/exports/${job.id}/download`;
        });
    }
    
    // Show export error
    function showExportError(job) {
        document.getElementById('export-status').textContent = 'Export failed: ' + (job.error_message || 'Unknown error');
        document.getElementById('export-status').classList.add('text-red-600');
    }
    
    // Close modal
    function closeExportModal() {
        exportModal.classList.add('hidden');
        exportModal.classList.remove('flex');
        currentExportJob = null;
        
        // Reset modal state
        document.getElementById('progress-bar').style.width = '0%';
        document.getElementById('progress-percent').textContent = '0%';
        document.getElementById('export-status').textContent = 'Preparing export...';
        document.getElementById('export-status').classList.remove('text-red-600');
        document.getElementById('export-complete').classList.add('hidden');
        document.getElementById('export-status').style.display = 'block';
        
        loadExportHistory();
    }
    
    closeModal.addEventListener('click', closeExportModal);
    
    // Load export history
    function loadExportHistory() {
        fetch('/api/exports', {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            const historyContainer = document.getElementById('export-history');
            
            if (data.data && data.data.length > 0) {
                historyContainer.innerHTML = data.data.slice(0, 5).map(job => `
                    <div class="border rounded-lg p-3">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-gray-900">${job.modules.join(', ')}</span>
                            <span class="text-xs px-2 py-1 rounded-full ${
                                job.status === 'completed' ? 'bg-green-100 text-green-800' :
                                job.status === 'failed' ? 'bg-red-100 text-red-800' :
                                'bg-yellow-100 text-yellow-800'
                            }">${job.status}</span>
                        </div>
                        <div class="text-xs text-gray-500 mb-2">
                            ${new Date(job.created_at).toLocaleDateString()}
                        </div>
                        ${job.status === 'completed' ? `
                            <button onclick="window.location.href='/api/exports/${job.id}/download'" class="text-xs text-blue-600 hover:text-blue-800">
                                Download
                            </button>
                        ` : ''}
                    </div>
                `).join('');
            } else {
                historyContainer.innerHTML = '<p class="text-gray-500 text-sm">No export history available.</p>';
            }
        })
        .catch(error => {
            console.error('Failed to load export history:', error);
            const historyContainer = document.getElementById('export-history');
            historyContainer.innerHTML = '<p class="text-gray-500 text-sm">Export history temporarily unavailable.</p>';
        });
    }
    
    // Initialize
    validateForm();
    loadExportHistory();
});
</script>
@endif
@endsection