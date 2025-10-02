@extends('layouts.app')

@section('title', 'Sales Target Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Sales Target Details</h3>
                    <div>
                        @if($salesTarget->assigned_by_user_id === auth()->id())
                            <a href="{{ route('sales-targets.edit', $salesTarget) }}" class="btn btn-warning me-2">
                                <i class="fas fa-edit"></i> Edit Target
                            </a>
                        @endif
                        <a href="{{ route('sales-targets.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Target Assignment Info -->
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Target Assignment Information</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row mb-3">
                                        <div class="col-sm-4"><strong>Assigned To:</strong></div>
                                        <div class="col-sm-8">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar avatar-sm me-2">
                                                    <span class="avatar-initial rounded-circle bg-primary">
                                                        {{ strtoupper(substr($salesTarget->assignedTo->name, 0, 1)) }}
                                                    </span>
                                                </div>
                                                <div>
                                                    <strong>{{ $salesTarget->assignedTo->name }}</strong><br>
                                    <small class="text-muted">{{ $salesTarget->assignedTo->role->name }}</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-sm-4"><strong>Assigned By:</strong></div>
                                        <div class="col-sm-8">
                                            {{ $salesTarget->assignedBy->name }}<br>
                                            <small class="text-muted">{{ $salesTarget->assignedBy->role->name }}</small>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-sm-4"><strong>Target Year:</strong></div>
                                        <div class="col-sm-8">{{ $salesTarget->target_year }}</div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-sm-4"><strong>Status:</strong></div>
                                        <div class="col-sm-8">
                                            @if($salesTarget->status === 'active')
                                                <span class="badge bg-success">Active</span>
                                            @elseif($salesTarget->status === 'inactive')
                                                <span class="badge bg-warning">Inactive</span>
                                            @else
                                                <span class="badge bg-secondary">Completed</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-sm-4"><strong>Created:</strong></div>
                                        <div class="col-sm-8">{{ $salesTarget->created_at->format('M d, Y \\a\\t h:i A') }}</div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-4"><strong>Last Updated:</strong></div>
                                        <div class="col-sm-8">{{ $salesTarget->updated_at->format('M d, Y \\a\\t h:i A') }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Achievement Overview -->
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Achievement Overview</h5>
                                </div>
                                <div class="card-body">
                                    @php
                                        $achievementPercentage = $achievement;
                                        $targetAmount = $salesTarget->total_yearly_target;
                                        $achievedAmount = ($targetAmount * $achievementPercentage) / 100;
                                        $achievementData = [
                                            'percentage' => $achievementPercentage,
                                            'achieved' => $achievedAmount,
                                            'details' => null
                                        ];
                                    @endphp
                                    <div class="text-center mb-4">
                                        <div class="achievement-circle">
                                            <div class="achievement-percentage">
                                                {{ number_format($achievementData['percentage'], 1) }}%
                                            </div>
                                            <div class="achievement-label">Achievement</div>
                                        </div>
                                    </div>
                                    <div class="row text-center">
                                        <div class="col-6">
                                            <div class="achievement-stat">
                                                <div class="stat-value text-success">
                                                    ৳{{ number_format($achievementData['achieved'], 2) }}
                                                </div>
                                                <div class="stat-label">Achieved</div>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="achievement-stat">
                                                <div class="stat-value text-primary">
                                                    ৳{{ number_format($salesTarget->total_yearly_target, 2) }}
                                                </div>
                                                <div class="stat-label">Target</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="progress mt-3" style="height: 10px;">
                                        <div class="progress-bar bg-success" 
                                             style="width: {{ min($achievementData['percentage'], 100) }}%"
                                             role="progressbar">
                                        </div>
                                    </div>
                                    <div class="text-center mt-2">
                                        <small class="text-muted">
                                            Remaining: ৳{{ number_format($salesTarget->total_yearly_target - $achievementData['achieved'], 2) }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Weekly Targets -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Weekly Sales Targets</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @for($week = 1; $week <= 4; $week++)
                                    <div class="col-md-3 col-sm-6 mb-3">
                                        <div class="target-card">
                                            <div class="target-header">Week {{ $week }}</div>
                                            <div class="target-amount">
                                                ৳{{ number_format($salesTarget->{'week_' . $week . '_target'} ?? 0, 2) }}
                                            </div>
                                        </div>
                                    </div>
                                @endfor
                            </div>
                        </div>
                    </div>

                    <!-- Monthly Targets -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Monthly Sales Targets</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @php
                                    $months = [
                                        'january' => 'January', 'february' => 'February', 'march' => 'March',
                                        'april' => 'April', 'may' => 'May', 'june' => 'June',
                                        'july' => 'July', 'august' => 'August', 'september' => 'September',
                                        'october' => 'October', 'november' => 'November', 'december' => 'December'
                                    ];
                                @endphp
                                @foreach($months as $key => $month)
                                    <div class="col-md-2 col-sm-4 col-6 mb-3">
                                        <div class="target-card">
                                            <div class="target-header">{{ $month }}</div>
                                            <div class="target-amount">
                                                ৳{{ number_format($salesTarget->{$key . '_target'} ?? 0, 2) }}
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Total Yearly Target -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Total Yearly Sales Target</h5>
                        </div>
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <div class="yearly-target">
                                        <div class="yearly-amount">
                                            ৳{{ number_format($salesTarget->total_yearly_target, 2) }}
                                        </div>
                                        <div class="yearly-label">Total Yearly Target</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="target-breakdown">
                                        <h6>Target Breakdown:</h6>
                                        <ul class="list-unstyled mb-0">
                                            <li><strong>Monthly Average:</strong> ৳{{ number_format($salesTarget->total_yearly_target / 12, 2) }}</li>
                                            <li><strong>Weekly Average:</strong> ৳{{ number_format($salesTarget->total_yearly_target / 52, 2) }}</li>
                                            <li><strong>Daily Average:</strong> ৳{{ number_format($salesTarget->total_yearly_target / 365, 2) }}</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Achievement Calculation Details -->
                    @if($achievementData['details'])
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Achievement Calculation Details</h5>
                            </div>
                            <div class="card-body">
                                <div class="achievement-details">
                                    @foreach($achievementData['details'] as $detail)
                                        <div class="detail-item">
                                            <div class="detail-label">{{ $detail['label'] }}:</div>
                                            <div class="detail-value">৳{{ number_format($detail['amount'], 2) }}</div>
                                        </div>
                                    @endforeach
                                    <hr>
                                    <div class="detail-item total">
                                        <div class="detail-label"><strong>Total Achievement:</strong></div>
                                        <div class="detail-value"><strong>৳{{ number_format($achievementData['achieved'], 2) }}</strong></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.avatar {
    width: 32px;
    height: 32px;
}

.avatar-initial {
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    font-weight: 600;
}

.achievement-circle {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    background: conic-gradient(#28a745 {{ min($achievementData['percentage'] ?? 0, 100) * 3.6 }}deg, #e9ecef 0deg);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
    position: relative;
}

.achievement-circle::before {
    content: '';
    position: absolute;
    width: 90px;
    height: 90px;
    background: white;
    border-radius: 50%;
    z-index: 1;
}

.achievement-percentage {
    font-size: 24px;
    font-weight: bold;
    color: #28a745;
    z-index: 2;
}

.achievement-label {
    font-size: 12px;
    color: #6c757d;
    z-index: 2;
}

.achievement-stat {
    padding: 1rem;
}

.stat-value {
    font-size: 18px;
    font-weight: bold;
    margin-bottom: 0.25rem;
}

.stat-label {
    font-size: 12px;
    color: #6c757d;
    text-transform: uppercase;
}

.target-card {
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
    padding: 1rem;
    text-align: center;
    height: 100%;
}

.target-header {
    font-size: 14px;
    font-weight: 600;
    color: #495057;
    margin-bottom: 0.5rem;
}

.target-amount {
    font-size: 16px;
    font-weight: bold;
    color: #007bff;
}

.yearly-target {
    text-align: center;
    padding: 2rem;
}

.yearly-amount {
    font-size: 36px;
    font-weight: bold;
    color: #007bff;
    margin-bottom: 0.5rem;
}

.yearly-label {
    font-size: 16px;
    color: #6c757d;
}

.target-breakdown {
    background: #f8f9fa;
    padding: 1.5rem;
    border-radius: 0.375rem;
}

.achievement-details {
    background: #f8f9fa;
    padding: 1.5rem;
    border-radius: 0.375rem;
}

.detail-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.5rem 0;
}

.detail-item.total {
    border-top: 2px solid #dee2e6;
    margin-top: 1rem;
    padding-top: 1rem;
}

.detail-label {
    color: #495057;
}

.detail-value {
    color: #007bff;
    font-weight: 600;
}

.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
}

.me-2 {
    margin-right: 0.5rem;
}
</style>
@endpush