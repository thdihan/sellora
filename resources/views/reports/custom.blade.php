@extends('layouts.app')

@section('title', 'Custom Report Builder')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Custom Report Builder</h1>
            <p class="text-muted">Create advanced custom reports with flexible data selection</p>
        </div>
        <div class="btn-group" role="group">
            <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary">
                ← Back to Reports
            </a>
            <button type="button" class="btn btn-primary" onclick="saveReport()">
                💾 Save Report
            </button>
        </div>
    </div>

    <div class="row">
        <!-- Report Builder Form -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Report Configuration</h6>
                </div>
                <div class="card-body">
                    <form id="customReportForm">
                        <!-- Report Basic Info -->
                        <div class="mb-3">
                            <label for="report_name" class="form-label">Report Name</label>
                            <input type="text" class="form-control" id="report_name" name="report_name" 
                                   placeholder="Enter report name" required>
                        </div>

                        <div class="mb-3">
                            <label for="report_description" class="form-label">Description</label>
                            <textarea class="form-control" id="report_description" name="report_description" 
                                      rows="2" placeholder="Brief description of the report"></textarea>
                        </div>

                        <!-- Data Sources -->
                        <div class="mb-3">
                            <label class="form-label">Data Sources</label>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="source_orders" name="data_sources[]" value="orders">
                                <label class="form-check-label" for="source_orders">
                                    <i class="fas fa-shopping-cart text-primary"></i> Orders/Sales
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="source_expenses" name="data_sources[]" value="expenses">
                                <label class="form-check-label" for="source_expenses">
                                    <i class="fas fa-receipt text-danger"></i> Expenses
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="source_visits" name="data_sources[]" value="visits">
                                <label class="form-check-label" for="source_visits">
                                    <i class="fas fa-map-marker-alt text-info"></i> Visits
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="source_budgets" name="data_sources[]" value="budgets">
                                <label class="form-check-label" for="source_budgets">
                                    <i class="fas fa-dollar-sign text-success"></i> Budgets
                                </label>
                            </div>
                        </div>

                        <!-- Date Range -->
                        <div class="mb-3">
                            <label class="form-label">Date Range</label>
                            <div class="row">
                                <div class="col-6">
                                    <input type="date" class="form-control" id="date_from" name="date_from">
                                    <small class="text-muted">From</small>
                                </div>
                                <div class="col-6">
                                    <input type="date" class="form-control" id="date_to" name="date_to">
                                    <small class="text-muted">To</small>
                                </div>
                            </div>
                        </div>

                        <!-- Grouping -->
                        <div class="mb-3">
                            <label for="group_by" class="form-label">Group By</label>
                            <select class="form-control" id="group_by" name="group_by">
                                <option value="">No Grouping</option>
                                <option value="day">Daily</option>
                                <option value="week">Weekly</option>
                                <option value="month">Monthly</option>
                                <option value="quarter">Quarterly</option>
                                <option value="year">Yearly</option>
                                <option value="category">By Category</option>
                                <option value="status">By Status</option>
                                <option value="type">By Type</option>
                            </select>
                        </div>

                        <!-- Metrics -->
                        <div class="mb-3">
                            <label class="form-label">Metrics to Include</label>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="metric_count" name="metrics[]" value="count" checked>
                                <label class="form-check-label" for="metric_count">Count</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="metric_sum" name="metrics[]" value="sum">
                                <label class="form-check-label" for="metric_sum">Sum/Total</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="metric_avg" name="metrics[]" value="average">
                                <label class="form-check-label" for="metric_avg">Average</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="metric_min" name="metrics[]" value="min">
                                <label class="form-check-label" for="metric_min">Minimum</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="metric_max" name="metrics[]" value="max">
                                <label class="form-check-label" for="metric_max">Maximum</label>
                            </div>
                        </div>

                        <!-- Filters -->
                        <div class="mb-3">
                            <label class="form-label">Additional Filters</label>
                            <div id="filters-container">
                                <!-- Dynamic filters will be added here -->
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="addFilter()">
                                <i class="fas fa-plus"></i> Add Filter
                            </button>
                        </div>

                        <!-- Chart Type -->
                        <div class="mb-3">
                            <label for="chart_type" class="form-label">Chart Type</label>
                            <select class="form-control" id="chart_type" name="chart_type">
                                <option value="table">Table Only</option>
                                <option value="line">Line Chart</option>
                                <option value="bar">Bar Chart</option>
                                <option value="pie">Pie Chart</option>
                                <option value="doughnut">Doughnut Chart</option>
                                <option value="area">Area Chart</option>
                            </select>
                        </div>

                        <!-- Actions -->
                        <div class="d-grid gap-2">
                            <button type="button" class="btn btn-primary" onclick="generateReport()">
                                <i class="fas fa-chart-bar"></i> Generate Report
                            </button>
                            <button type="button" class="btn btn-outline-secondary" onclick="resetForm()">
                                <i class="fas fa-undo"></i> Reset Form
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Saved Reports -->
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Saved Reports</h6>
                </div>
                <div class="card-body">
                    <div id="saved-reports-list">
                        <div class="text-center py-3">
                            <i class="fas fa-file-alt fa-2x text-gray-300 mb-2"></i>
                            <p class="text-muted small">No saved reports yet</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Report Preview -->
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Report Preview</h6>
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-sm btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown">
                            Export
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#" onclick="exportCustomReport('pdf')">PDF Report</a></li>
                            <li><a class="dropdown-item" href="#" onclick="exportCustomReport('excel')">Excel Report</a></li>
                            <li><a class="dropdown-item" href="#" onclick="exportCustomReport('csv')">CSV Data</a></li>
                        </ul>
                    </div>
                </div>
                <div class="card-body">
                    <div id="report-preview">
                        <div class="text-center py-5">
                            <i class="fas fa-chart-line fa-4x text-gray-300 mb-3"></i>
                            <h5 class="text-gray-600">Custom Report Preview</h5>
                            <p class="text-muted">Configure your report settings and click "Generate Report" to see the preview</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filter Modal -->
<div class="modal fade" id="filterModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Filter</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="filterForm">
                    <div class="mb-3">
                        <label for="filter_field" class="form-label">Field</label>
                        <select class="form-control" id="filter_field" name="filter_field">
                            <option value="">Select Field</option>
                            <option value="status">Status</option>
                            <option value="category">Category</option>
                            <option value="type">Type</option>
                            <option value="amount">Amount</option>
                            <option value="date">Date</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="filter_operator" class="form-label">Operator</label>
                        <select class="form-control" id="filter_operator" name="filter_operator">
                            <option value="equals">Equals</option>
                            <option value="not_equals">Not Equals</option>
                            <option value="contains">Contains</option>
                            <option value="greater_than">Greater Than</option>
                            <option value="less_than">Less Than</option>
                            <option value="between">Between</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="filter_value" class="form-label">Value</label>
                        <input type="text" class="form-control" id="filter_value" name="filter_value" placeholder="Enter filter value">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="applyFilter()">Add Filter</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
let filterCount = 0;
let currentChart = null;
let reportData = null;

// Add filter function
function addFilter() {
    const modal = new bootstrap.Modal(document.getElementById('filterModal'));
    modal.show();
}

// Apply filter function
function applyFilter() {
    const field = document.getElementById('filter_field').value;
    const operator = document.getElementById('filter_operator').value;
    const value = document.getElementById('filter_value').value;
    
    if (!field || !operator || !value) {
        alert('Please fill all filter fields');
        return;
    }
    
    filterCount++;
    const filterHtml = `
        <div class="filter-item mb-2 p-2 border rounded" id="filter-${filterCount}">
            <div class="d-flex justify-content-between align-items-center">
                <small class="text-muted">
                    <strong>${field}</strong> ${operator.replace('_', ' ')} <strong>${value}</strong>
                </small>
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeFilter(${filterCount})">
                    ×
                </button>
            </div>
            <input type="hidden" name="filters[${filterCount}][field]" value="${field}">
            <input type="hidden" name="filters[${filterCount}][operator]" value="${operator}">
            <input type="hidden" name="filters[${filterCount}][value]" value="${value}">
        </div>
    `;
    
    document.getElementById('filters-container').insertAdjacentHTML('beforeend', filterHtml);
    
    // Reset form and close modal
    document.getElementById('filterForm').reset();
    bootstrap.Modal.getInstance(document.getElementById('filterModal')).hide();
}

// Remove filter function
function removeFilter(filterId) {
    document.getElementById(`filter-${filterId}`).remove();
}

// Generate report function
function generateReport() {
    const formData = new FormData(document.getElementById('customReportForm'));
    
    // Validate required fields
    if (!formData.get('report_name')) {
        alert('Please enter a report name');
        return;
    }
    
    const dataSources = formData.getAll('data_sources[]');
    if (dataSources.length === 0) {
        alert('Please select at least one data source');
        return;
    }
    
    // Show loading
    document.getElementById('report-preview').innerHTML = `
        <div class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-3 text-muted">Generating report...</p>
        </div>
    `;
    
    // Simulate API call
    setTimeout(() => {
        generateMockReport(formData);
    }, 2000);
}

// Generate mock report
function generateMockReport(formData) {
    const reportName = formData.get('report_name');
    const chartType = formData.get('chart_type');
    const groupBy = formData.get('group_by');
    
    // Mock data generation
    const mockData = generateMockData(groupBy);
    reportData = mockData;
    
    let previewHtml = `
        <div class="mb-3">
            <h5 class="text-primary">${reportName}</h5>
            <p class="text-muted">${formData.get('report_description') || 'Custom generated report'}</p>
        </div>
    `;
    
    // Add chart if selected
    if (chartType !== 'table') {
        previewHtml += `
            <div class="mb-4">
                <canvas id="customChart" width="400" height="200"></canvas>
            </div>
        `;
    }
    
    // Add data table
    previewHtml += `
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="thead-light">
                    <tr>
                        <th>Period/Category</th>
                        <th class="text-center">Count</th>
                        <th class="text-center">Total Amount</th>
                        <th class="text-center">Average</th>
                    </tr>
                </thead>
                <tbody>
    `;
    
    mockData.forEach(item => {
        previewHtml += `
            <tr>
                <td>${item.label}</td>
                <td class="text-center">${item.count}</td>
                <td class="text-center">৳${item.total.toLocaleString()}</td>
                                <td class="text-center">৳${item.average.toLocaleString()}</td>
            </tr>
        `;
    });
    
    previewHtml += `
                </tbody>
            </table>
        </div>
    `;
    
    document.getElementById('report-preview').innerHTML = previewHtml;
    
    // Generate chart if needed
    if (chartType !== 'table') {
        generateCustomChart(chartType, mockData);
    }
}

// Generate mock data
function generateMockData(groupBy) {
    const labels = {
        'day': ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
        'week': ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
        'month': ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
        'category': ['Marketing', 'Operations', 'Travel', 'Equipment'],
        'status': ['Active', 'Completed', 'Pending', 'Cancelled'],
        'type': ['Type A', 'Type B', 'Type C', 'Type D']
    };
    
    const selectedLabels = labels[groupBy] || ['Item 1', 'Item 2', 'Item 3', 'Item 4'];
    
    return selectedLabels.map(label => ({
        label: label,
        count: Math.floor(Math.random() * 100) + 10,
        total: Math.floor(Math.random() * 10000) + 1000,
        average: Math.floor(Math.random() * 500) + 100
    }));
}

// Generate custom chart
function generateCustomChart(type, data) {
    const ctx = document.getElementById('customChart').getContext('2d');
    
    if (currentChart) {
        currentChart.destroy();
    }
    
    const chartConfig = {
        type: type,
        data: {
            labels: data.map(item => item.label),
            datasets: [{
                label: 'Total Amount',
                data: data.map(item => item.total),
                backgroundColor: [
                    '#007bff',
                    '#28a745',
                    '#ffc107',
                    '#dc3545',
                    '#6f42c1',
                    '#fd7e14',
                    '#20c997'
                ],
                borderColor: '#007bff',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Amount: ৳' + context.parsed.y.toLocaleString();
                        }
                    }
                }
            }
        }
    };
    
    if (type === 'line' || type === 'area') {
        chartConfig.data.datasets[0].fill = type === 'area';
        chartConfig.data.datasets[0].tension = 0.1;
    }
    
    currentChart = new Chart(ctx, chartConfig);
}

// Save report function
function saveReport() {
    const reportName = document.getElementById('report_name').value;
    if (!reportName) {
        alert('Please enter a report name before saving');
        return;
    }
    
    // Mock save functionality
    const savedReport = {
        name: reportName,
        description: document.getElementById('report_description').value,
        created_at: new Date().toLocaleDateString()
    };
    
    addSavedReport(savedReport);
    alert('Report saved successfully!');
}

// Add saved report to list
function addSavedReport(report) {
    const listContainer = document.getElementById('saved-reports-list');
    
    // Remove "no reports" message if it exists
    if (listContainer.querySelector('.text-center')) {
        listContainer.innerHTML = '';
    }
    
    const reportHtml = `
        <div class="saved-report-item mb-2 p-2 border rounded">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h6 class="mb-1">${report.name}</h6>
                    <small class="text-muted">${report.description || 'No description'}</small>
                    <br>
                    <small class="text-muted">Created: ${report.created_at}</small>
                </div>
                <div class="btn-group-vertical btn-group-sm">
                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="loadSavedReport('${report.name}')">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button type="button" class="btn btn-outline-danger btn-sm" onclick="deleteSavedReport(this)">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>
    `;
    
    listContainer.insertAdjacentHTML('beforeend', reportHtml);
}

// Load saved report
function loadSavedReport(reportName) {
    alert(`Loading report: ${reportName}`);
    // Implementation would load the saved report configuration
}

// Delete saved report
function deleteSavedReport(button) {
    if (confirm('Are you sure you want to delete this report?')) {
        button.closest('.saved-report-item').remove();
        
        // Show "no reports" message if list is empty
        const listContainer = document.getElementById('saved-reports-list');
        if (listContainer.children.length === 0) {
            listContainer.innerHTML = `
                <div class="text-center py-3">
                    <i class="fas fa-file-alt fa-2x text-gray-300 mb-2"></i>
                    <p class="text-muted small">No saved reports yet</p>
                </div>
            `;
        }
    }
}

// Export custom report
function exportCustomReport(format) {
    if (!reportData) {
        alert('Please generate a report first');
        return;
    }
    
    const reportName = document.getElementById('report_name').value || 'custom_report';
    alert(`Exporting ${reportName} as ${format.toUpperCase()}`);
    // Implementation would handle actual export
}

// Reset form
function resetForm() {
    document.getElementById('customReportForm').reset();
    document.getElementById('filters-container').innerHTML = '';
    document.getElementById('report-preview').innerHTML = `
        <div class="text-center py-5">
            <i class="fas fa-chart-line fa-4x text-gray-300 mb-3"></i>
            <h5 class="text-gray-600">Custom Report Preview</h5>
            <p class="text-muted">Configure your report settings and click "Generate Report" to see the preview</p>
        </div>
    `;
    
    if (currentChart) {
        currentChart.destroy();
        currentChart = null;
    }
    
    filterCount = 0;
    reportData = null;
}

// Initialize date inputs with current month
document.addEventListener('DOMContentLoaded', function() {
    const today = new Date();
    const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
    const lastDay = new Date(today.getFullYear(), today.getMonth() + 1, 0);
    
    document.getElementById('date_from').value = firstDay.toISOString().split('T')[0];
    document.getElementById('date_to').value = lastDay.toISOString().split('T')[0];
});
</script>
@endsection