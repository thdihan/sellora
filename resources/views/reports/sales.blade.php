@extends('layouts.app')

@section('title', 'Sales Reports')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Sales Reports</h1>
            <p class="text-muted">Comprehensive sales analytics and insights</p>
        </div>
        <div class="btn-group" role="group">
            <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary">
                Back to Reports
            </a>
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown">
                    Export
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#" onclick="exportSalesReport('pdf')">PDF Report</a></li>
                    <li><a class="dropdown-item" href="#" onclick="exportSalesReport('excel')">Excel Report</a></li>
                    <li><a class="dropdown-item" href="#" onclick="exportSalesReport('csv')">CSV Data</a></li>
                    @php
                        $nsmPlusRoles = ['NSM', 'NSM+', 'RSM', 'ASM', 'Author'];
                    @endphp
                    @if(in_array(auth()->user()->role, $nsmPlusRoles))
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="#" onclick="generatePresentationFromReport('sales')">📊 Generate Presentation</a></li>
                    @endif
                </ul>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filters & Options</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('reports.sales') }}" id="filtersForm">
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
                            <option value="processing" {{ request('status') === 'processing' ? 'selected' : '' }}>Processing</option>
                            <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="payment_status" class="form-label">Payment</label>
                        <select class="form-control" id="payment_status" name="payment_status">
                            <option value="">All Payments</option>
                            <option value="pending" {{ request('payment_status') === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="paid" {{ request('payment_status') === 'paid' ? 'selected' : '' }}>Paid</option>
                            <option value="failed" {{ request('payment_status') === 'failed' ? 'selected' : '' }}>Failed</option>
                            <option value="refunded" {{ request('payment_status') === 'refunded' ? 'selected' : '' }}>Refunded</option>
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
                        <button type="submit" class="btn btn-primary">
                            🔍 Apply Filters
                        </button>
                        <a href="{{ route('reports.sales') }}" class="btn btn-outline-secondary">
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
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Orders</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($salesData['stats']['total_orders']) }}</div>
                        </div>
                        <div class="col-auto">
                            <span style="font-size: 2rem; color: #6c757d;">🛒</span>
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
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Revenue</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">৳{{ number_format($salesData['stats']['total_amount'], 2) }}</div>
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
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Average Order</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">৳{{ number_format($salesData['stats']['average_order'], 2) }}</div>
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
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Pending Orders</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($salesData['stats']['pending_orders']) }}</div>
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
        <!-- Sales Trend Chart -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Sales Trend</h6>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
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
                    <canvas id="salesTrendChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>

        <!-- Status Breakdown -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Order Status Breakdown</h6>
                </div>
                <div class="card-body">
                    <canvas id="statusChart" width="400" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Status & Top Customers -->
    <div class="row mb-4">
        <!-- Payment Status -->
        <div class="col-xl-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Payment Status Distribution</h6>
                </div>
                <div class="card-body">
                    <canvas id="paymentChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>

        <!-- Top Customers -->
        <div class="col-xl-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Top Customers</h6>
                </div>
                <div class="card-body">
                    @if(count($salesData['top_customers']) > 0)
                        <div class="table-responsive">
                            <table class="table table-borderless">
                                <thead>
                                    <tr>
                                        <th>Customer</th>
                                        <th class="text-center">Orders</th>
                                        <th class="text-right">Total Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($salesData['top_customers'] as $customer)
                                        <tr>
                                            <td class="font-weight-bold">{{ $customer['name'] }}</td>
                                            <td class="text-center">
                                                <span class="badge badge-primary">{{ $customer['orders_count'] }}</span>
                                            </td>
                                            <td class="text-right font-weight-bold">৳{{ number_format($customer['total_amount'], 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <span style="font-size: 3rem; color: #6c757d;" class="mb-3 d-block">👥</span>
                            <p class="text-muted">No customer data available</p>
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
                    <h6 class="m-0 font-weight-bold text-primary">Detailed Analytics</h6>
                </div>
                <div class="card-body">
                    <ul class="nav nav-tabs" id="analyticsTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="status-tab" data-bs-toggle="tab" data-bs-target="#status" type="button" role="tab">
                                Status Analysis
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="payment-tab" data-bs-toggle="tab" data-bs-target="#payment" type="button" role="tab">
                                Payment Analysis
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="timeline-tab" data-bs-toggle="tab" data-bs-target="#timeline" type="button" role="tab">
                                Timeline Data
                            </button>
                        </li>
                    </ul>
                    <div class="tab-content" id="analyticsTabContent">
                        <div class="tab-pane fade show active" id="status" role="tabpanel">
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
                                        @foreach($salesData['status_breakdown'] as $status)
                                            <tr>
                                                <td>
                                                    <span class="badge badge-{{ $status->status === 'completed' ? 'success' : ($status->status === 'pending' ? 'warning' : 'secondary') }}">
                                                        {{ ucfirst($status->status) }}
                                                    </span>
                                                </td>
                                                <td class="text-center">{{ number_format($status->count) }}</td>
                                                <td class="text-right">৳{{ number_format($status->amount, 2) }}</td>
                                                <td class="text-center">
                                                    {{ $salesData['stats']['total_orders'] > 0 ? number_format(($status->count / $salesData['stats']['total_orders']) * 100, 1) : 0 }}%
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="payment" role="tabpanel">
                            <div class="table-responsive mt-3">
                                <table class="table table-bordered">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Payment Status</th>
                                            <th class="text-center">Count</th>
                                            <th class="text-right">Total Amount</th>
                                            <th class="text-center">Percentage</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($salesData['payment_breakdown'] as $payment)
                                            <tr>
                                                <td>
                                                    <span class="badge badge-{{ $payment->payment_status === 'paid' ? 'success' : ($payment->payment_status === 'pending' ? 'warning' : 'danger') }}">
                                                        {{ ucfirst($payment->payment_status) }}
                                                    </span>
                                                </td>
                                                <td class="text-center">{{ number_format($payment->count) }}</td>
                                                <td class="text-right">৳{{ number_format($payment->amount, 2) }}</td>
                                                <td class="text-center">
                                                    {{ $salesData['stats']['total_orders'] > 0 ? number_format(($payment->count / $salesData['stats']['total_orders']) * 100, 1) : 0 }}%
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
                                            <th class="text-right">Revenue</th>
                                            <th class="text-center">Growth</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $previousValue = 0; @endphp
                                        @foreach($salesData['time_series'] as $index => $period)
                                            <tr>
                                                <td class="font-weight-bold">{{ $period['period'] }}</td>
                                                <td class="text-right">৳{{ number_format($period['value'], 2) }}</td>
                                                <td class="text-center">
                                                    @if($index > 0 && $previousValue > 0)
                                                        @php
                                                            $growth = (($period['value'] - $previousValue) / $previousValue) * 100;
                                                        @endphp
                                                        <span class="text-{{ $growth >= 0 ? 'success' : 'danger' }}">
                                                            {{ $growth >= 0 ? '↑' : '↓' }}
                                                            {{ number_format(abs($growth), 1) }}%
                                                        </span>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            @php $previousValue = $period['value']; @endphp
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
const timeSeriesData = @json($salesData['time_series']);
const statusData = @json($salesData['status_breakdown']);
const paymentData = @json($salesData['payment_breakdown']);

// Sales Trend Chart
const salesCtx = document.getElementById('salesTrendChart').getContext('2d');
const salesChart = new Chart(salesCtx, {
    type: 'line',
    data: {
        labels: timeSeriesData.map(item => item.period),
        datasets: [{
            label: 'Revenue',
            data: timeSeriesData.map(item => item.value),
            borderColor: 'rgb(75, 192, 192)',
            backgroundColor: 'rgba(75, 192, 192, 0.1)',
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
                        return 'Revenue: ৳' + context.parsed.y.toLocaleString();
                    }
                }
            }
        }
    }
});

// Status Chart
const statusCtx = document.getElementById('statusChart').getContext('2d');
const statusChart = new Chart(statusCtx, {
    type: 'doughnut',
    data: {
        labels: statusData.map(item => item.status.charAt(0).toUpperCase() + item.status.slice(1)),
        datasets: [{
            data: statusData.map(item => item.count),
            backgroundColor: [
                '#28a745',
                '#ffc107',
                '#6c757d',
                '#dc3545'
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});

// Payment Chart
const paymentCtx = document.getElementById('paymentChart').getContext('2d');
const paymentChart = new Chart(paymentCtx, {
    type: 'bar',
    data: {
        labels: paymentData.map(item => item.payment_status.charAt(0).toUpperCase() + item.payment_status.slice(1)),
        datasets: [{
            label: 'Amount',
            data: paymentData.map(item => item.amount),
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
function exportSalesReport(format) {
    const params = new URLSearchParams(window.location.search);
    params.set('type', 'sales');
    params.set('format', format);
    const url = `{{ route('reports.export') }}?${params.toString()}`;
    window.open(url, '_blank');
}

function generatePresentationFromReport(reportType) {
    if (confirm('Generate a PowerPoint presentation from this report?')) {
        // Create a form to submit the request
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/presentations/generate-from-report/${reportType}`;
        
        // Add CSRF token
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);
        
        // Submit the form
        document.body.appendChild(form);
        form.submit();
        document.body.removeChild(form);
    }
}

function changeChartType(type) {
    salesChart.config.type = type;
    salesChart.update();
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