@extends('layouts.app')

@section('title', 'Self Assessment History')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-history"></i>
                        Self Assessment History Timeline
                    </h4>
                    <div class="d-flex gap-2">
                        <a href="{{ route('self-assessments.create') }}" class="btn btn-outline-primary">
                            <i class="fas fa-plus"></i> New Assessment
                        </a>
                        <a href="{{ route('self-assessments.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-list"></i> List View
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if($assessments->count() > 0)
                        <!-- Timeline -->
                        <div class="timeline">
                            @foreach($assessments as $assessment)
                                <div class="timeline-item {{ $assessment->status }}">
                                    <div class="timeline-marker">
                                        @if($assessment->status === 'draft')
                                            <i class="fas fa-edit"></i>
                                        @elseif($assessment->status === 'submitted')
                                            <i class="fas fa-paper-plane"></i>
                                        @elseif($assessment->status === 'reviewed')
                                            ✓
                                        @endif
                                    </div>
                                    <div class="timeline-content">
                                        <div class="timeline-header">
                                            <h5 class="timeline-title">
                                                <a href="{{ route('self-assessments.show', $assessment) }}" class="text-decoration-none">
                                                    {{ $assessment->period }}
                                                </a>
                                                {!! $assessment->status_badge !!}
                                            </h5>
                                            <small class="timeline-date">
                                                Created: {{ $assessment->created_at->format('M d, Y') }}
                                            </small>
                                        </div>
                                        
                                        <div class="timeline-body">
                                            <!-- Key Highlights -->
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="highlight-box">
                                                        <h6><i class="fas fa-bullseye text-primary"></i> Key Targets</h6>
                                                        <p class="text-truncate-3">{{ $assessment->targets }}</p>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="highlight-box">
                                                        <h6><i class="fas fa-trophy text-success"></i> Main Achievements</h6>
                                                        <p class="text-truncate-3">{{ $assessment->achievements }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <!-- Status Timeline -->
                                            <div class="status-timeline mt-3">
                                                <div class="status-step completed">
                                                    <i class="fas fa-plus-circle"></i>
                                                    <span>Created</span>
                                                    <small>{{ $assessment->created_at->format('M d') }}</small>
                                                </div>
                                                @if($assessment->submitted_at)
                                                    <div class="status-step completed">
                                                        <i class="fas fa-paper-plane"></i>
                                                        <span>Submitted</span>
                                                        <small>{{ $assessment->submitted_at->format('M d') }}</small>
                                                    </div>
                                                @endif
                                                @if($assessment->reviewed_at)
                                                    <div class="status-step completed">
                                                        ✓
                                                        <span>Reviewed</span>
                                                        <small>{{ $assessment->reviewed_at->format('M d') }}</small>
                                                    </div>
                                                @endif
                                            </div>
                                            
                                            @if($assessment->reviewer_comments)
                                                <div class="reviewer-note mt-3">
                                                    <i class="fas fa-comment text-info"></i>
                                                    <strong>Reviewer Note:</strong>
                                                    <span class="text-truncate-2">{{ $assessment->reviewer_comments }}</span>
                                                </div>
                                            @endif
                                        </div>
                                        
                                        <div class="timeline-actions">
                                            <a href="{{ route('self-assessments.show', $assessment) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i> View Details
                                            </a>
                                            @if($assessment->is_editable)
                                                <a href="{{ route('self-assessments.edit', $assessment) }}" class="btn btn-sm btn-outline-warning">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <!-- Pagination -->
                    @if($assessments->hasPages())
                        <div class="d-flex justify-content-center mt-4">
                            {{ $assessments->links('vendor.pagination.custom-3d') }}
                        </div>
                    @endif
                        
                        <!-- Summary Stats -->
                        <div class="row mt-5">
                            <div class="col-12">
                                <h5 class="mb-3">Assessment Summary</h5>
                            </div>
                            <div class="col-md-3">
                                <div class="stat-card">
                                    <div class="stat-icon">
                                        <i class="fas fa-clipboard-list"></i>
                                    </div>
                                    <div class="stat-content">
                                        <h3>{{ $totalAssessments }}</h3>
                                        <p>Total Assessments</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="stat-card">
                                    <div class="stat-icon draft">
                                        <i class="fas fa-edit"></i>
                                    </div>
                                    <div class="stat-content">
                                        <h3>{{ $draftCount }}</h3>
                                        <p>Draft</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="stat-card">
                                    <div class="stat-icon submitted">
                                        <i class="fas fa-paper-plane"></i>
                                    </div>
                                    <div class="stat-content">
                                        <h3>{{ $submittedCount }}</h3>
                                        <p>Submitted</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="stat-card">
                                    <div class="stat-icon reviewed">
                                        ✓
                                    </div>
                                    <div class="stat-content">
                                        <h3>{{ $reviewedCount }}</h3>
                                        <p>Reviewed</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <!-- Empty State -->
                        <div class="text-center py-5">
                            <i class="fas fa-history fa-3x text-gray-300 mb-3"></i>
                            <h5 class="text-gray-600">No assessment history found</h5>
                            <p class="text-gray-500">Create your first self assessment to start building your history timeline.</p>
                            <a href="{{ route('self-assessments.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Create First Assessment
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
/* Timeline Styles */
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e9ecef;
}

.timeline-item {
    position: relative;
    margin-bottom: 30px;
    padding-left: 40px;
}

.timeline-marker {
    position: absolute;
    left: -40px;
    top: 0;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 14px;
    z-index: 1;
}

.timeline-item.draft .timeline-marker {
    background-color: #6c757d;
}

.timeline-item.submitted .timeline-marker {
    background-color: #ffc107;
}

.timeline-item.reviewed .timeline-marker {
    background-color: #28a745;
}

.timeline-content {
    background: white;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 20px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.timeline-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 15px;
    flex-wrap: wrap;
    gap: 10px;
}

.timeline-title {
    margin: 0;
    color: #495057;
    display: flex;
    align-items: center;
    gap: 10px;
}

.timeline-date {
    color: #6c757d;
    white-space: nowrap;
}

.highlight-box {
    background-color: #f8f9fa;
    border-radius: 6px;
    padding: 15px;
    margin-bottom: 15px;
    height: 100%;
}

.highlight-box h6 {
    margin-bottom: 8px;
    font-size: 0.9rem;
    color: #495057;
}

.highlight-box h6 i {
    margin-right: 5px;
}

.text-truncate-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    line-height: 1.4;
}

.text-truncate-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
    line-height: 1.4;
}

/* Status Timeline */
.status-timeline {
    display: flex;
    align-items: center;
    gap: 20px;
    padding: 15px;
    background-color: #f8f9fa;
    border-radius: 6px;
    flex-wrap: wrap;
}

.status-step {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 4px;
    opacity: 0.5;
    transition: opacity 0.3s;
}

.status-step.completed {
    opacity: 1;
}

.status-step i {
    font-size: 18px;
    color: #28a745;
}

.status-step span {
    font-size: 0.8rem;
    font-weight: 500;
    color: #495057;
}

.status-step small {
    font-size: 0.7rem;
    color: #6c757d;
}

/* Reviewer Note */
.reviewer-note {
    background-color: #e3f2fd;
    border-left: 4px solid #2196f3;
    padding: 10px 15px;
    border-radius: 4px;
}

.reviewer-note i {
    margin-right: 8px;
}

/* Timeline Actions */
.timeline-actions {
    margin-top: 15px;
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

/* Stats Cards */
.stat-card {
    background: white;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 20px;
    text-align: center;
    margin-bottom: 20px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.stat-icon {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 15px;
    background-color: #007bff;
    color: white;
    font-size: 20px;
}

.stat-icon.draft {
    background-color: #6c757d;
}

.stat-icon.submitted {
    background-color: #ffc107;
}

.stat-icon.reviewed {
    background-color: #28a745;
}

.stat-content h3 {
    margin: 0 0 5px 0;
    color: #495057;
    font-size: 2rem;
    font-weight: 600;
}

.stat-content p {
    margin: 0;
    color: #6c757d;
    font-size: 0.9rem;
}

/* Responsive */
@media (max-width: 768px) {
    .timeline {
        padding-left: 20px;
    }
    
    .timeline-item {
        padding-left: 30px;
    }
    
    .timeline-marker {
        left: -30px;
        width: 25px;
        height: 25px;
        font-size: 12px;
    }
    
    .timeline-header {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .status-timeline {
        gap: 15px;
    }
    
    .timeline-actions {
        justify-content: center;
    }
}
</style>
@endpush
@endsection