@extends('layouts.app')

@section('title', 'Self Assessment Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-clipboard-check"></i>
                        Self Assessment - {{ $assessment->period }}
                    </h4>
                    <div class="d-flex gap-2">
                        @if($assessment->is_editable)
                            <a href="{{ route('self-assessments.edit', $assessment) }}" class="btn btn-outline-warning">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                        @endif
                        <a href="{{ route('self-assessments.index') }}" class="btn btn-outline-secondary">
                            Back to List
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Assessment Header -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="info-item">
                                <label class="info-label">Period:</label>
                                <span class="info-value">{{ $assessment->period }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item">
                                <label class="info-label">Status:</label>
                                <span class="info-value">{!! $assessment->status_badge !!}</span>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="info-item">
                                <label class="info-label">Created:</label>
                                <span class="info-value">{{ $assessment->created_at->format('M d, Y \a\t g:i A') }}</span>
                            </div>
                        </div>
                        @if($assessment->submitted_at)
                            <div class="col-md-4">
                                <div class="info-item">
                                    <label class="info-label">Submitted:</label>
                                    <span class="info-value">{{ $assessment->submitted_at->format('M d, Y \a\t g:i A') }}</span>
                                </div>
                            </div>
                        @endif
                        @if($assessment->reviewed_at)
                            <div class="col-md-4">
                                <div class="info-item">
                                    <label class="info-label">Reviewed:</label>
                                    <span class="info-value">
                                        {{ $assessment->reviewed_at->format('M d, Y \a\t g:i A') }}
                                        @if($assessment->reviewer)
                                            <br><small class="text-muted">by {{ $assessment->reviewer->name }}</small>
                                        @endif
                                    </span>
                                </div>
                            </div>
                        @endif
                    </div>

                    <hr class="my-4">

                    <!-- Assessment Content -->
                    <div class="assessment-content">
                        <!-- Targets Section -->
                        <div class="content-section mb-4">
                            <h5 class="section-title">
                                <i class="fas fa-bullseye text-primary"></i>
                                Targets & Goals
                            </h5>
                            <div class="content-box">
                                <p class="content-text">{{ $assessment->targets }}</p>
                            </div>
                        </div>

                        <!-- Achievements Section -->
                        <div class="content-section mb-4">
                            <h5 class="section-title">
                                <i class="fas fa-trophy text-success"></i>
                                Achievements & Accomplishments
                            </h5>
                            <div class="content-box">
                                <p class="content-text">{{ $assessment->achievements }}</p>
                            </div>
                        </div>

                        <!-- Problems Section -->
                        <div class="content-section mb-4">
                            <h5 class="section-title">
                                <i class="fas fa-exclamation-triangle text-warning"></i>
                                Challenges & Problems
                            </h5>
                            <div class="content-box">
                                <p class="content-text">{{ $assessment->problems }}</p>
                            </div>
                        </div>

                        <!-- Solutions Section -->
                        <div class="content-section mb-4">
                            <h5 class="section-title">
                                <i class="fas fa-lightbulb text-info"></i>
                                Solutions & Improvements
                            </h5>
                            <div class="content-box">
                                <p class="content-text">{{ $assessment->solutions }}</p>
                            </div>
                        </div>

                        <!-- Market Analysis Section -->
                        <div class="content-section mb-4">
                            <h5 class="section-title">
                                <i class="fas fa-chart-line text-primary"></i>
                                Market Analysis & Insights
                            </h5>
                            <div class="content-box">
                                <p class="content-text">{{ $assessment->market_analysis }}</p>
                            </div>
                        </div>

                        @if($assessment->reviewer_comments)
                            <!-- Reviewer Comments -->
                            <div class="content-section mb-4">
                                <h5 class="section-title">
                                    <i class="fas fa-comments text-info"></i>
                                    Reviewer Comments
                                </h5>
                                <div class="reviewer-comments">
                                    <div class="d-flex align-items-start">
                                        <div class="reviewer-avatar">
                                            <i class="fas fa-user-tie"></i>
                                        </div>
                                        <div class="comment-content">
                                            <div class="comment-header">
                                                <strong>{{ $assessment->reviewer->name ?? 'Reviewer' }}</strong>
                                                <small class="text-muted ms-2">{{ $assessment->reviewed_at->format('M d, Y \a\t g:i A') }}</small>
                                            </div>
                                            <div class="comment-text">{{ $assessment->reviewer_comments }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex justify-content-between mt-4">
                        <div>
                            @if($assessment->status === 'submitted' && $assessment->is_editable)
                                <form method="POST" action="{{ route('self-assessments.revert-to-draft', $assessment) }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-warning"
                                            onclick="return confirm('Are you sure you want to revert this assessment to draft status?')">
                                        <i class="fas fa-undo"></i> Revert to Draft
                                    </button>
                                </form>
                            @endif
                            @if($assessment->status === 'draft')
                                <form method="POST" action="{{ route('self-assessments.submit', $assessment) }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-success"
                                            onclick="return confirm('Are you sure you want to submit this assessment for review?')">
                                        <i class="fas fa-paper-plane"></i> Submit for Review
                                    </button>
                                </form>
                            @endif
                        </div>
                        <div>
                            @if($assessment->is_editable)
                                <a href="{{ route('self-assessments.edit', $assessment) }}" class="btn btn-primary">
                                    <i class="fas fa-edit"></i> Edit Assessment
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.info-item {
    margin-bottom: 0.5rem;
}

.info-label {
    font-weight: 600;
    color: #495057;
    margin-right: 8px;
}

.info-value {
    color: #212529;
}

.section-title {
    color: #495057;
    border-bottom: 2px solid #e9ecef;
    padding-bottom: 8px;
    margin-bottom: 16px;
    font-weight: 600;
}

.section-title i {
    margin-right: 8px;
}

.content-box {
    background-color: #f8f9fa;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 16px;
}

.content-text {
    margin: 0;
    line-height: 1.6;
    color: #495057;
    white-space: pre-wrap;
    word-wrap: break-word;
}

.reviewer-comments {
    background-color: #e3f2fd;
    border: 1px solid #bbdefb;
    border-radius: 8px;
    padding: 20px;
}

.reviewer-avatar {
    width: 40px;
    height: 40px;
    background-color: #2196f3;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    margin-right: 12px;
    flex-shrink: 0;
}

.comment-content {
    flex: 1;
}

.comment-header {
    margin-bottom: 8px;
}

.comment-text {
    color: #495057;
    line-height: 1.6;
    white-space: pre-wrap;
    word-wrap: break-word;
}

.content-section {
    margin-bottom: 2rem;
}

@media (max-width: 768px) {
    .d-flex.justify-content-between {
        flex-direction: column;
        gap: 1rem;
    }
    
    .d-flex.justify-content-between > div {
        text-align: center;
    }
}
</style>
@endpush
@endsection