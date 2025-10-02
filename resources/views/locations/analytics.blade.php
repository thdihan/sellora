@extends('layouts.app')

@section('title', 'Location Analytics')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">
                            📊
                            Location Analytics
                        </h4>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('locations.index') }}">Locations</a></li>
                                <li class="breadcrumb-item active">Analytics</li>
                            </ol>
                        </nav>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Time Period Selector -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <label for="timePeriod" class="form-label">Time Period</label>
                            <select class="form-select" id="timePeriod" onchange="updateAnalytics()">
                                <option value="week">Last 7 Days</option>
                                <option value="month" selected>Last 30 Days</option>
                                <option value="quarter">Last 3 Months</option>
                                <option value="year">Last 12 Months</option>
                                <option value="custom">Custom Range</option>
                            </select>
                        </div>
                        <div class="col-md-4" id="customRangeStart" style="display: none;">
                            <label for="startDate" class="form-label">Start Date</label>
                            <input type="date" class="form-control" id="startDate" onchange="updateAnalytics()">
                        </div>
                        <div class="col-md-4" id="customRangeEnd" style="display: none;">
                            <label for="endDate" class="form-label">End Date</label>
                            <input type="date" class="form-control" id="endDate" onchange="updateAnalytics()">
                        </div>
                    </div>

                    <!-- Key Metrics -->
                    <div class="row mb-4">
                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="card bg-primary text-white h-100">
                                <div class="card-body text-center">
                                    <span style="font-size: 2rem;" class="mb-2">📍</span>
                                    <h3 class="mb-1" id="totalLocations">{{ $analytics['total_locations'] ?? 0 }}</h3>
                                    <p class="mb-0">Total Locations</p>
                                    <small class="opacity-75">+{{ $analytics['new_locations'] ?? 0 }} this period</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="card bg-success text-white h-100">
                                <div class="card-body text-center">
                                    <span style="font-size: 2rem;" class="mb-2">📜</span>
                                    <h3 class="mb-1" id="totalVisits">{{ $analytics['total_visits'] ?? 0 }}</h3>
                                    <p class="mb-0">Total Visits</p>
                                    <small class="opacity-75">{{ $analytics['avg_visits_per_day'] ?? 0 }} per day avg</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="card bg-info text-white h-100">
                                <div class="card-body text-center">
                                    <span style="font-size: 2rem;" class="mb-2">🕒</span>
                                    <h3 class="mb-1" id="totalTime">{{ $analytics['total_time_hours'] ?? 0 }}h</h3>
                                    <p class="mb-0">Total Time</p>
                                    <small class="opacity-75">{{ $analytics['avg_duration'] ?? 0 }}m avg visit</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="card bg-warning text-white h-100">
                                <div class="card-body text-center">
                                    <span style="font-size: 2rem;" class="mb-2">⭐</span>
                                    <h3 class="mb-1" id="favoriteLocations">{{ $analytics['favorite_locations'] ?? 0 }}</h3>
                                    <p class="mb-0">Favorite Locations</p>
                                    <small class="opacity-75">{{ $analytics['most_visited'] ?? 'N/A' }} most visited</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Charts Row -->
                    <div class="row mb-4">
                        <!-- Visit Trends Chart -->
                        <div class="col-lg-8 mb-4">
                            <div class="card h-100">
                                <div class="card-header">
                                    <h6 class="card-title mb-0">
                                        📈
                                        Visit Trends Over Time
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <canvas id="visitTrendsChart" height="300"></canvas>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Location Types Distribution -->
                        <div class="col-lg-4 mb-4">
                            <div class="card h-100">
                                <div class="card-header">
                                    <h6 class="card-title mb-0">
                                        📊
                                        Location Types
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <canvas id="locationTypesChart" height="300"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Detailed Analytics Row -->
                    <div class="row mb-4">
                        <!-- Top Locations -->
                        <div class="col-lg-6 mb-4">
                            <div class="card h-100">
                                <div class="card-header">
                                    <h6 class="card-title mb-0">
                                        🏆
                                        Most Visited Locations
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Rank</th>
                                                    <th>Location</th>
                                                    <th>Visits</th>
                                                    <th>Total Time</th>
                                                </tr>
                                            </thead>
                                            <tbody id="topLocationsTable">
                                                @foreach($analytics['top_locations'] ?? [] as $index => $location)
                                                    <tr>
                                                        <td>
                                                            @if($index === 0)
                                                                <span class="text-warning">🏆</span>
                                                            @elseif($index === 1)
                                                                <span class="text-secondary">🥈</span>
                                                            @elseif($index === 2)
                                                                <span class="text-warning">🥉</span>
                                                            @else
                                                                {{ $index + 1 }}
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                @switch($location['type'])
                                                                    @case('home')
                                                                        <span class="text-primary me-2">🏠</span>
                                                                        @break
                                                                    @case('office')
                                                                        <span class="text-info me-2">🏢</span>
                                                                        @break
                                                                    @case('client')
                                                                        <span class="text-success me-2">🤝</span>
                                                                        @break
                                                                    @default
                                                                        <span class="text-secondary me-2">📍</span>
                                                                @endswitch
                                                                <span>{{ $location['name'] }}</span>
                                                            </div>
                                                        </td>
                                                        <td><span class="badge bg-primary">{{ $location['visits'] }}</span></td>
                                                        <td><span class="text-muted">{{ $location['total_time'] }}h</span></td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Time Patterns -->
                        <div class="col-lg-6 mb-4">
                            <div class="card h-100">
                                <div class="card-header">
                                    <h6 class="card-title mb-0">
                                        🕒
                                        Visit Patterns by Hour
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <canvas id="hourlyPatternsChart" height="300"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Weekly Heatmap -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="card-title mb-0">
                                        📅
                                        Weekly Activity Heatmap
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div id="weeklyHeatmap" class="text-center">
                                        <div class="row">
                                            <div class="col-1 text-end pe-0">
                                                <div class="heatmap-hour">6 AM</div>
                                                <div class="heatmap-hour">9 AM</div>
                                                <div class="heatmap-hour">12 PM</div>
                                                <div class="heatmap-hour">3 PM</div>
                                                <div class="heatmap-hour">6 PM</div>
                                                <div class="heatmap-hour">9 PM</div>
                                            </div>
                                            <div class="col-11">
                                                <div class="heatmap-days">
                                                    <div class="heatmap-day-label">Mon</div>
                                                    <div class="heatmap-day-label">Tue</div>
                                                    <div class="heatmap-day-label">Wed</div>
                                                    <div class="heatmap-day-label">Thu</div>
                                                    <div class="heatmap-day-label">Fri</div>
                                                    <div class="heatmap-day-label">Sat</div>
                                                    <div class="heatmap-day-label">Sun</div>
                                                </div>
                                                <div class="heatmap-grid" id="heatmapGrid">
                                                    <!-- Heatmap cells will be generated by JavaScript -->
                                                </div>
                                            </div>
                                        </div>
                                        <div class="heatmap-legend mt-3">
                                            <span class="me-2">Less</span>
                                            <div class="heatmap-legend-scale">
                                                <div class="heatmap-cell level-0"></div>
                                                <div class="heatmap-cell level-1"></div>
                                                <div class="heatmap-cell level-2"></div>
                                                <div class="heatmap-cell level-3"></div>
                                                <div class="heatmap-cell level-4"></div>
                                            </div>
                                            <span class="ms-2">More</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Productivity and Mood Analytics -->
                    <div class="row mb-4">
                        <div class="col-lg-6 mb-4">
                            <div class="card h-100">
                                <div class="card-header">
                                    <h6 class="card-title mb-0">
                                        📈
                                        Productivity by Location Type
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <canvas id="productivityChart" height="300"></canvas>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-lg-6 mb-4">
                            <div class="card h-100">
                                <div class="card-header">
                                    <h6 class="card-title mb-0">
                                        😊
                                        Mood Trends
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <canvas id="moodChart" height="300"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Export and Actions -->
                    <div class="d-flex justify-content-between">
                        <div>
                            <a href="{{ route('locations.index') }}" class="btn btn-secondary">
                                ← Back to Locations
                            </a>
                        </div>
                        <div>
                            <button class="btn btn-outline-primary" onclick="exportAnalytics()">
                                📥 Export Report
                            </button>
                            <button class="btn btn-outline-info" onclick="refreshAnalytics()">
                                🔄 Refresh Data
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.heatmap-days {
    display: flex;
    justify-content: space-between;
    margin-bottom: 5px;
}

.heatmap-day-label {
    font-size: 12px;
    font-weight: 600;
    color: #6c757d;
    width: 14.28%;
    text-align: center;
}

.heatmap-hour {
    font-size: 10px;
    color: #6c757d;
    height: 20px;
    line-height: 20px;
    margin-bottom: 2px;
}

.heatmap-grid {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    grid-template-rows: repeat(6, 1fr);
    gap: 2px;
    max-width: 300px;
    margin: 0 auto;
}

.heatmap-cell {
    width: 20px;
    height: 20px;
    border-radius: 2px;
    cursor: pointer;
    transition: all 0.2s;
}

.heatmap-cell:hover {
    transform: scale(1.1);
    border: 1px solid #007bff;
}

.heatmap-cell.level-0 {
    background-color: #ebedf0;
}

.heatmap-cell.level-1 {
    background-color: #c6e48b;
}

.heatmap-cell.level-2 {
    background-color: #7bc96f;
}

.heatmap-cell.level-3 {
    background-color: #239a3b;
}

.heatmap-cell.level-4 {
    background-color: #196127;
}

.heatmap-legend {
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    color: #6c757d;
}

.heatmap-legend-scale {
    display: flex;
    gap: 2px;
    margin: 0 10px;
}

.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border: 1px solid rgba(0, 0, 0, 0.125);
}

.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid rgba(0, 0, 0, 0.125);
}
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
let charts = {};

// Initialize analytics
document.addEventListener('DOMContentLoaded', function() {
    initializeCharts();
    generateHeatmap();
    
    // Handle time period changes
    document.getElementById('timePeriod').addEventListener('change', function() {
        const customRangeStart = document.getElementById('customRangeStart');
        const customRangeEnd = document.getElementById('customRangeEnd');
        
        if (this.value === 'custom') {
            customRangeStart.style.display = 'block';
            customRangeEnd.style.display = 'block';
        } else {
            customRangeStart.style.display = 'none';
            customRangeEnd.style.display = 'none';
        }
    });
});

// Initialize all charts
function initializeCharts() {
    initVisitTrendsChart();
    initLocationTypesChart();
    initHourlyPatternsChart();
    initProductivityChart();
    initMoodChart();
}

// Visit trends chart
function initVisitTrendsChart() {
    const ctx = document.getElementById('visitTrendsChart').getContext('2d');
    charts.visitTrends = new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
            datasets: [{
                label: 'Visits',
                data: [12, 19, 15, 25],
                borderColor: '#007bff',
                backgroundColor: 'rgba(0, 123, 255, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
}

// Location types chart
function initLocationTypesChart() {
    const ctx = document.getElementById('locationTypesChart').getContext('2d');
    charts.locationTypes = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Office', 'Home', 'Client', 'Meeting', 'Other'],
            datasets: [{
                data: [35, 25, 20, 15, 5],
                backgroundColor: [
                    '#007bff',
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
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
}

// Hourly patterns chart
function initHourlyPatternsChart() {
    const ctx = document.getElementById('hourlyPatternsChart').getContext('2d');
    charts.hourlyPatterns = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['6AM', '9AM', '12PM', '3PM', '6PM', '9PM'],
            datasets: [{
                label: 'Check-ins',
                data: [5, 15, 8, 12, 6, 3],
                backgroundColor: '#17a2b8'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
}

// Productivity chart
function initProductivityChart() {
    const ctx = document.getElementById('productivityChart').getContext('2d');
    charts.productivity = new Chart(ctx, {
        type: 'radar',
        data: {
            labels: ['Office', 'Home', 'Client', 'Meeting', 'Other'],
            datasets: [{
                label: 'Productivity Rating',
                data: [4.2, 3.8, 4.5, 3.9, 3.5],
                borderColor: '#28a745',
                backgroundColor: 'rgba(40, 167, 69, 0.2)',
                pointBackgroundColor: '#28a745'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                r: {
                    beginAtZero: true,
                    max: 5
                }
            }
        }
    });
}

// Mood chart
function initMoodChart() {
    const ctx = document.getElementById('moodChart').getContext('2d');
    charts.mood = new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
            datasets: [{
                label: 'Mood Rating',
                data: [4.1, 4.3, 3.8, 4.0, 4.2, 4.5, 4.4],
                borderColor: '#ffc107',
                backgroundColor: 'rgba(255, 193, 7, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 5
                }
            }
        }
    });
}

// Generate heatmap
function generateHeatmap() {
    const grid = document.getElementById('heatmapGrid');
    grid.innerHTML = '';
    
    // Generate 42 cells (6 hours × 7 days)
    for (let i = 0; i < 42; i++) {
        const cell = document.createElement('div');
        cell.className = 'heatmap-cell';
        
        // Random activity level for demo
        const level = Math.floor(Math.random() * 5);
        cell.classList.add(`level-${level}`);
        
        // Add tooltip
        const day = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'][i % 7];
        const hour = [6, 9, 12, 15, 18, 21][Math.floor(i / 7)];
        cell.title = `${day} ${hour}:00 - ${level} visits`;
        
        grid.appendChild(cell);
    }
}

// Update analytics based on time period
function updateAnalytics() {
    const timePeriod = document.getElementById('timePeriod').value;
    
    // Show loading state
    showLoadingState();
    
    // Simulate API call
    setTimeout(() => {
        // Update charts with new data
        updateChartsData(timePeriod);
        hideLoadingState();
        showAlert('success', 'Analytics updated successfully!');
    }, 1000);
}

// Update charts with new data
function updateChartsData(period) {
    // This would typically fetch new data from the server
    // For demo purposes, we'll just update with random data
    
    // Update visit trends
    const newVisitData = generateRandomData(4, 10, 30);
    charts.visitTrends.data.datasets[0].data = newVisitData;
    charts.visitTrends.update();
    
    // Update location types
    const newTypeData = generateRandomData(5, 5, 40);
    charts.locationTypes.data.datasets[0].data = newTypeData;
    charts.locationTypes.update();
    
    // Update hourly patterns
    const newHourlyData = generateRandomData(6, 1, 20);
    charts.hourlyPatterns.data.datasets[0].data = newHourlyData;
    charts.hourlyPatterns.update();
    
    // Regenerate heatmap
    generateHeatmap();
}

// Generate random data for demo
function generateRandomData(count, min, max) {
    return Array.from({length: count}, () => Math.floor(Math.random() * (max - min + 1)) + min);
}

// Show loading state
function showLoadingState() {
    const cards = document.querySelectorAll('.card-body canvas');
    cards.forEach(card => {
        card.style.opacity = '0.5';
    });
}

// Hide loading state
function hideLoadingState() {
    const cards = document.querySelectorAll('.card-body canvas');
    cards.forEach(card => {
        card.style.opacity = '1';
    });
}

// Export analytics
function exportAnalytics() {
    const timePeriod = document.getElementById('timePeriod').value;
    const startDate = document.getElementById('startDate').value;
    const endDate = document.getElementById('endDate').value;
    
    const params = new URLSearchParams({
        period: timePeriod,
        start_date: startDate,
        end_date: endDate
    });
    
    window.open(`/locations/analytics/export?${params.toString()}`, '_blank');
}

// Refresh analytics
function refreshAnalytics() {
    updateAnalytics();
}

// Utility function to show alerts
function showAlert(type, message) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    const container = document.querySelector('.card-body');
    container.insertBefore(alertDiv, container.firstChild);
    
    // Auto-dismiss after 3 seconds
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 3000);
}
</script>
@endsection