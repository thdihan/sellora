@extends('layouts.app')

@section('title', 'Assessment Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">{{ $assessment->title }}</h4>
                    <div class="btn-group">
                        <a href="{{ route('assessments.edit', $assessment) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <a href="{{ route('assessments.take', $assessment) }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-play"></i> Take Assessment
                        </a>
                        <a href="{{ route('assessments.duplicate', $assessment) }}" class="btn btn-info btn-sm">
                            <i class="fas fa-copy"></i> Duplicate
                        </a>
                        <form action="{{ route('assessments.toggle-status', $assessment) }}" method="POST" class="d-inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-{{ $assessment->is_active ? 'secondary' : 'success' }} btn-sm">
                                <i class="fas fa-{{ $assessment->is_active ? 'pause' : 'play' }}"></i>
                                {{ $assessment->is_active ? 'Deactivate' : 'Activate' }}
                            </button>
                        </form>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <!-- Assessment Info -->
                            <div class="mb-4">
                                <h5>Description</h5>
                                <p class="text-muted">{{ $assessment->description ?: 'No description provided.' }}</p>
                            </div>

                            <!-- Assessment Details -->
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <div class="info-item">
                                        <strong>Type:</strong>
                                        <span class="badge badge-primary">{{ ucfirst($assessment->type) }}</span>
                                    </div>
                                    <div class="info-item">
                                        <strong>Category:</strong>
                                        <span class="badge badge-secondary">{{ $assessment->category ?: 'General' }}</span>
                                    </div>
                                    <div class="info-item">
                                        <strong>Difficulty:</strong>
                                        <span class="badge badge-{{ $assessment->difficulty_level === 'easy' ? 'success' : ($assessment->difficulty_level === 'medium' ? 'warning' : 'danger') }}">
                                            {{ ucfirst($assessment->difficulty_level) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-item">
                                        <strong>Time Limit:</strong>
                                        {{ $assessment->time_limit ? $assessment->time_limit . ' minutes' : 'No limit' }}
                                    </div>
                                    <div class="info-item">
                                        <strong>Passing Score:</strong>
                                        {{ $assessment->passing_score }}%
                                    </div>
                                    <div class="info-item">
                                        <strong>Status:</strong>
                                        <span class="badge badge-{{ $assessment->is_active ? 'success' : 'secondary' }}">
                                            {{ $assessment->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Questions -->
                            <div class="mb-4">
                                <h5>Questions ({{ count($assessment->questions) }})</h5>
                                @if(count($assessment->questions) > 0)
                                    <div class="accordion" id="questionsAccordion">
                                        @foreach($assessment->questions as $index => $question)
                                            <div class="card">
                                                <div class="card-header" id="heading{{ $index }}">
                                                    <h6 class="mb-0">
                                                        <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapse{{ $index }}">
                                                            Question {{ $index + 1 }}: {{ Str::limit($question['question'], 60) }}
                                                        </button>
                                                    </h6>
                                                </div>
                                                <div id="collapse{{ $index }}" class="collapse" data-parent="#questionsAccordion">
                                                    <div class="card-body">
                                                        <p><strong>Question:</strong> {{ $question['question'] }}</p>
                                                        <p><strong>Type:</strong> {{ ucfirst($question['type']) }}</p>
                                                        @if(isset($question['options']) && count($question['options']) > 0)
                                                            <p><strong>Options:</strong></p>
                                                            <ul>
                                                                @foreach($question['options'] as $option)
                                                                    <li>{{ $option }}</li>
                                                                @endforeach
                                                            </ul>
                                                        @endif
                                                        <p><strong>Correct Answer:</strong> 
                                                            @if(is_array($question['correct_answer']))
                                                                {{ implode(', ', $question['correct_answer']) }}
                                                            @else
                                                                {{ $question['correct_answer'] }}
                                                            @endif
                                                        </p>
                                                        <p><strong>Points:</strong> {{ $question['points'] ?? 1 }}</p>
                                                        @if(isset($question['explanation']))
                                                            <p><strong>Explanation:</strong> {{ $question['explanation'] }}</p>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-muted">No questions added yet.</p>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-4">
                            <!-- Statistics -->
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">Statistics</h6>
                                </div>
                                <div class="card-body">
                                    <div class="stat-item">
                                        <strong>Total Attempts:</strong>
                                        <span class="float-right">{{ $assessment->attempts_count }}</span>
                                    </div>
                                    <div class="stat-item">
                                        <strong>Completed:</strong>
                                        <span class="float-right">{{ $assessment->completed_attempts_count }}</span>
                                    </div>
                                    <div class="stat-item">
                                        <strong>Average Score:</strong>
                                        <span class="float-right">{{ number_format($assessment->average_score, 1) }}%</span>
                                    </div>
                                    <div class="stat-item">
                                        <strong>Pass Rate:</strong>
                                        <span class="float-right">{{ number_format($assessment->pass_rate, 1) }}%</span>
                                    </div>
                                    <div class="stat-item">
                                        <strong>Created:</strong>
                                        <span class="float-right">{{ $assessment->created_at->format('M d, Y') }}</span>
                                    </div>
                                    <div class="stat-item">
                                        <strong>Updated:</strong>
                                        <span class="float-right">{{ $assessment->updated_at->format('M d, Y') }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Recent Attempts -->
                            @if($assessment->attempts->count() > 0)
                                <div class="card mt-3">
                                    <div class="card-header">
                                        <h6 class="mb-0">Recent Attempts</h6>
                                    </div>
                                    <div class="card-body">
                                        @foreach($assessment->attempts->take(5) as $attempt)
                                            <div class="attempt-item">
                                                <div class="d-flex justify-content-between">
                                                    <span>{{ $attempt->user->name }}</span>
                                                    <span class="badge badge-{{ $attempt->status === 'completed' ? 'success' : ($attempt->status === 'in_progress' ? 'warning' : 'secondary') }}">
                                                        {{ ucfirst($attempt->status) }}
                                                    </span>
                                                </div>
                                                @if($attempt->status === 'completed')
                                                    <small class="text-muted">
                                                        Score: {{ number_format($attempt->score, 1) }}% | 
                                                        {{ $attempt->completed_at->diffForHumans() }}
                                                    </small>
                                                @else
                                                    <small class="text-muted">
                                                        Started {{ $attempt->started_at->diffForHumans() }}
                                                    </small>
                                                @endif
                                            </div>
                                        @endforeach
                                        @if($assessment->attempts->count() > 5)
                                            <a href="{{ route('assessments.analytics', $assessment) }}" class="btn btn-sm btn-outline-primary mt-2">
                                                View All Attempts
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.info-item {
    margin-bottom: 10px;
}

.stat-item {
    padding: 8px 0;
    border-bottom: 1px solid #eee;
}

.stat-item:last-child {
    border-bottom: none;
}

.attempt-item {
    padding: 8px 0;
    border-bottom: 1px solid #eee;
}

.attempt-item:last-child {
    border-bottom: none;
}

.accordion .card {
    margin-bottom: 5px;
}

.accordion .card-header {
    padding: 10px 15px;
}

.accordion .btn-link {
    text-decoration: none;
    color: #333;
    width: 100%;
    text-align: left;
}

.accordion .btn-link:hover {
    text-decoration: none;
}
</style>
@endsection