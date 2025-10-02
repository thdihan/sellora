@extends('layouts.app')

@section('title', 'Expense Reports')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Expense Reports</h1>
            <p class="text-muted">Comprehensive expense analytics and cost analysis</p>
        </div>
        <div class="btn-group" role="group">
            <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary">
                Back to Reports
            </a>
            <div class="btn-group" role="group">
                <button class="btn btn-success dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    Export
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#" onclick="exportExpenseReport('pdf')">PDF Report</a></li>
                    <li><a class="dropdown-item" href="#" onclick="exportExpenseReport('excel')">Excel Report</a></li>
                    <li><a class="dropdown-item" href="#" onclick="exportExpenseReport('csv')">CSV Data</a></li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-success">Filters & Options</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('reports.expenses') }}" id="filtersForm">
                <div class="row">
                    <div class="col-md-3">
                        <label for="date_from" class="form-label">From Date</label>
                        <input type="date" class="form-control" id="date_from" name="date_from" 
                               value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="date_to" class="form-label">To Date</label>
                        <input type="date" class="form-control" id="date_to" name="date_to" 
                               value="{{ request('date_to') }}">
                    </div>
                    <div class="col-md-2">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-control" id="status" name="status">
                            <option value="">All Statuses</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                            <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Paid</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="category" class="form-label">Category</label>
                        <select class="form-control" id="category" name="category">
                            <option value="">All Categories</option>
                            <option value="travel" {{ request('category') === 'travel' ? 'selected' : '' }}>Travel</option>
                            <option value="meals" {{ request('category') === 'meals' ? 'selected' : '' }}>Meals</option>
                            <option value="office supplies" {{ request('category') === 'office supplies' ? 'selected' : '' }}>Office Supplies</option>
                            <option value="software" {{ request('category') === 'software' ? 'selected' : '' }}>Software</option>
                            <option value="marketing" {{ request('category') === 'marketing' ? 'selected' : '' }}>Marketing</option>
                            <option value="training" {{ request('category') === 'training' ? 'selected' : '' }}>Training</option>
                            <option value="equipment" {{ request('category') === 'equipment' ? 'selected' : '' }}>Equipment</option>
                            <option value="sales commission" {{ request('category') === 'sales commission' ? 'selected' : '' }}>Sales Commission</option>
                            <option value="sales incentives" {{ request('category') === 'sales incentives' ? 'selected' : '' }}>Sales Incentives</option>
                            <option value="client entertainment" {{ request('category') === 'client entertainment' ? 'selected' : '' }}>Client Entertainment</option>
                            <option value="sales materials" {{ request('category') === 'sales materials' ? 'selected' : '' }}>Sales Materials</option>
                            <option value="trade shows" {{ request('category') === 'trade shows' ? 'selected' : '' }}>Trade Shows</option>
                            <option value="sales training" {{ request('category') === 'sales training' ? 'selected' : '' }}>Sales Training</option>
                            <option value="crm software" {{ request('category') === 'crm software' ? 'selected' : '' }}>CRM Software</option>
                            <option value="lead generation" {{ request('category') === 'lead generation' ? 'selected' : '' }}>Lead Generation</option>
                            <option value="sales tools" {{ request('category') === 'sales tools' ? 'selected' : '' }}>Sales Tools</option>
                            <option value="team building" {{ request('category') === 'team building' ? 'selected' : '' }}>Team Building</option>
                            <option value="sales meetings" {{ request('category') === 'sales meetings' ? 'selected' : '' }}>Sales Meetings</option>
                            <option value="customer visits" {{ request('category') === 'customer visits' ? 'selected' : '' }}>Customer Visits</option>
                            <option value="sales conferences" {{ request('category') === 'sales conferences' ? 'selected' : '' }}>Sales Conferences</option>
                            <option value="promotional items" {{ request('category') === 'promotional items' ? 'selected' : '' }}>Promotional Items</option>
                            <option value="sales literature" {{ request('category') === 'sales literature' ? 'selected' : '' }}>Sales Literature</option>
                            <option value="other" {{ request('category') === 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="period" class="form-label">Period</label>
                        <select class="form-control" id="period" name="period">
                            <option value="day" {{ request('period') === 'day' ? 'selected' : '' }}>Daily</option>
                            <option value="week" {{ request('period') === 'week' ? 'selected' : '' }}>Weekly</option>
                            <option value="month" {{ request('period', 'month') === 'month' ? 'selected' : '' }}>Monthly</option>
                            <option value="quarter" {{ request('period') === 'quarter' ? 'selected' : '' }}>Quarterly</option>
                            <option value="year" {{ request('period') === 'year' ? 'selected' : '' }}>Yearly</option>
                        </select>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <button type="submit" class="btn btn-success">
                            🔍 Apply Filters
                        </button>
                        <a href="{{ route('reports.expenses') }}" class="btn btn-outline-secondary">
                            × Clear Filters
                        </a>
                        <button type="button" class="btn btn-info" onclick="refreshData()">
                            🔄 Refresh
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Expenses</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($expenseData['stats']['total_expenses']) }}</div>
                        </div>
                        <div class="col-auto">
                            <span style="font-size: 2rem; color: #6c757d;">🧾</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Total Amount</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">৳{{ number_format($expenseData['stats']['total_amount'], 2) }}</div>
                        </div>
                        <div class="col-auto">
                            <span style="font-size: 2rem; color: #6c757d;">💰</span>
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
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Average Expense</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">৳{{ number_format($expenseData['stats']['average_expense'], 2) }}</div>
                        </div>
                        <div class="col-auto">
                            <span style="font-size: 2rem; color: #6c757d;">📊</span>
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
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Pending Approval</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($expenseData['stats']['pending_expenses']) }}</div>
                        </div>
                        <div class="col-auto">
                            <span style="font-size: 2rem; color: #6c757d;">🕒</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row mb-4">
        <!-- Expense Trend Chart -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-success">Expense Trend</h6>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-success dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            Chart Options
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#" onclick="changeChartType('line')">Line Chart</a></li>
                            <li><a class="dropdown-item" href="#" onclick="changeChartType('bar')">Bar Chart</a></li>
                            <li><a class="dropdown-item" href="#" onclick="changeChartType('area')">Area Chart</a></li>
                        </ul>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="expenseTrendChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>

        <!-- Category Breakdown -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">Category Breakdown</h6>
                </div>
                <div class="card-body">
                    <canvas id="categoryChart" width="400" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Distribution & Category Details -->
    <div class="row mb-4">
        <!-- Status Distribution -->
        <div class="col-xl-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">Status Distribution</h6>
                </div>
                <div class="card-body">
                    <canvas id="statusChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>

        <!-- Category Details -->
        <div class="col-xl-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">Category Analysis</h6>
                </div>
                <div class="card-body">
                    @if(count($expenseData['category_breakdown']) > 0)
                        <div class="table-responsive">
                            <table class="table table-borderless">
                                <thead>
                                    <tr>
                                        <th>Category</th>
                                        <th class="text-center">Count</th>
                                        <th class="text-right">Total Amount</th>
                                        <th class="text-center">Avg Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($expenseData['category_breakdown'] as $category)
                                        <tr>
                                            <td>
                                                <span class="badge badge-success">{{ ucfirst($category->category) }}</span>
                                            </td>
                                            <td class="text-center">{{ $category->count }}</td>
                                            <td class="text-right font-weight-bold">৳{{ number_format($category->amount, 2) }}</td>
                                            <td class="text-center">৳{{ number_format($category->amount / $category->count, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <span style="font-size: 3rem; color: #6c757d;" class="mb-3 d-block">📊</span>
                            <p class="text-muted">No category data available</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Breakdown Tables -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">Detailed Analytics</h6>
                </div>
                <div class="card-body">
                    <ul class="nav nav-tabs" id="analyticsTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="category-tab" data-bs-toggle="tab" data-bs-target="#category" type="button" role="tab">
                                Category Analysis
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="status-tab" data-bs-toggle="tab" data-bs-target="#status" type="button" role="tab">
                                Status Analysis
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="timeline-tab" data-bs-toggle="tab" data-bs-target="#timeline" type="button" role="tab">
                                Timeline Data
                            </button>
                        </li>
                    </ul>
                    <div class="tab-content" id="analyticsTabContent">
                        <div class="tab-pane fade show active" id="category" role="tabpanel">
                            <div class="table-responsive mt-3">
                                <table class="table table-bordered">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Category</th>
                                            <th class="text-center">Count</th>
                                            <th class="text-right">Total Amount</th>
                                            <th class="text-center">Percentage</th>
                                            <th class="text-center">Avg Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                        @foreach($expenseData['category_breakdown'] as $category)
                            <tr>
                                <td>
                                    <span class="badge badge-success">{{ ucfirst($category->category) }}</span>
                                </td>
                                <td class="text-center">{{ number_format($category->count) }}</td>
                                <td class="text-right">৳{{ number_format($category->amount, 2) }}</td>
                                <td class="text-center">
                                    {{ $expenseData['stats']['total_amount'] > 0 ? number_format(($category->amount / $expenseData['stats']['total_amount']) * 100, 1) : 0 }}%
                                </td>
                                <td class="text-center">৳{{ number_format($category->amount / $category->count, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="status" role="tabpanel">
                            <div class="table-responsive mt-3">
                                <table class="table table-bordered">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Status</th>
                                            <th class="text-center">Count</th>
                                            <th class="text-right">Total Amount</th>
                                            <th class="text-center">Percentage</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($expenseData['status_breakdown'] as $status)
                                            <tr>
                                                <td>
                                                    <span class="badge badge-{{ $status->status === 'approved' || $status->status === 'paid' ? 'success' : ($status->status === 'pending' ? 'warning' : 'danger') }}">
                                                        {{ ucfirst($status->status) }}
                                                    </span>
                                                </td>
                                                <td class="text-center">{{ number_format($status->count) }}</td>
                                                <td class="text-right">৳{{ number_format($status->amount, 2) }}</td>
                                                <td class="text-center">
                                                    {{ $expenseData['stats']['total_expenses'] > 0 ? number_format(($status->count / $expenseData['stats']['total_expenses']) * 100, 1) : 0 }}%
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="timeline" role="tabpanel">
                            <div class="table-responsive mt-3">
                                <table class="table table-bordered">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Period</th>
                                            <th class="text-right">Total Expenses</th>
                                            <th class="text-center">Change</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $previousValue = 0; @endphp
                                        @foreach($expenseData['time_series'] as $index => $period)
                                            <tr>
                                                <td class="font-weight-bold">{{ $period->period }}</td>
                                                <td class="text-right">৳{{ number_format($period->value, 2) }}</td>
                                                <td class="text-center">
                                                    @if($index > 0 && $previousValue > 0)
                                                        @php
                                                            $change = (($period->value - $previousValue) / $previousValue) * 100;
                                                        @endphp
                                                        <span class="text-{{ $change <= 0 ? 'success' : 'danger' }}">
                                                            {{ $change <= 0 ? '↓' : '↑' }}
                                                            {{ number_format(abs($change), 1) }}%
                                                        </span>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            @php $previousValue = $period->value; @endphp
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Chart data from backend
const timeSeriesData = @json($expenseData['time_series']);
const categoryData = @json($expenseData['category_breakdown']);
const statusData = @json($expenseData['status_breakdown']);

// Expense Trend Chart
const expenseCtx = document.getElementById('expenseTrendChart').getContext('2d');
const expenseChart = new Chart(expenseCtx, {
    type: 'line',
    data: {
        labels: timeSeriesData.map(item => item.period),
        datasets: [{
            label: 'Expenses',
            data: timeSeriesData.map(item => item.value),
            borderColor: 'rgb(220, 53, 69)',
            backgroundColor: 'rgba(220, 53, 69, 0.1)',
            tension: 0.1,
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                        callback: function(value) {
                            return '৳' + value.toLocaleString();
                        }
                    }
            }
        },
        plugins: {
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return 'Expenses: ৳' + context.parsed.y.toLocaleString();
                    }
                }
            }
        }
    }
});

// Category Chart
const categoryCtx = document.getElementById('categoryChart').getContext('2d');
const categoryChart = new Chart(categoryCtx, {
    type: 'doughnut',
    data: {
        labels: categoryData.map(item => item.category.charAt(0).toUpperCase() + item.category.slice(1)),
        datasets: [{
            data: categoryData.map(item => item.amount),
            backgroundColor: [
                '#28a745',
                '#17a2b8',
                '#ffc107',
                '#dc3545',
                '#6f42c1',
                '#fd7e14'
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom'
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                    return context.label + ': ৳' + context.parsed.toLocaleString();
                }
                }
            }
        }
    }
});

// Status Chart
const statusCtx = document.getElementById('statusChart').getContext('2d');
const statusChart = new Chart(statusCtx, {
    type: 'bar',
    data: {
        labels: statusData.map(item => item.status.charAt(0).toUpperCase() + item.status.slice(1)),
        datasets: [{
            label: 'Amount',
            data: statusData.map(item => item.amount),
            backgroundColor: [
                '#28a745',
                '#ffc107',
                '#dc3545',
                '#6c757d'
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return '৳' + value.toLocaleString();
                    }
                }
            }
        },
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
});

// Functions
function exportExpenseReport(format) {
    const params = new URLSearchParams(window.location.search);
    params.set('type', 'expenses');
    params.set('format', format);
    const url = `{{ route('reports.export') }}?${params.toString()}`;
    window.open(url, '_blank');
}

function changeChartType(type) {
    expenseChart.config.type = type;
    expenseChart.update();
}

function refreshData() {
    location.reload();
}

// Auto-submit form on filter change
document.querySelectorAll('#filtersForm select, #filtersForm input').forEach(element => {
    element.addEventListener('change', function() {
        if (this.type !== 'date' || (document.getElementById('date_from').value && document.getElementById('date_to').value)) {
            document.getElementById('filtersForm').submit();
        }
    });
});
</script>
@endsection