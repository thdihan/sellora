@extends('layouts.app')

@section('title', 'Assessment Results')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Assessment Results: {{ $attempt->assessment->title }}</h4>
                    <div class="btn-group">
                        <a href="{{ route('assessments.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to Assessments
                        </a>
                        @if($attempt->assessment->allow_retake)
                            <a href="{{ route('assessments.take', $attempt->assessment) }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-redo"></i> Retake Assessment
                            </a>
                        @endif
                        <button onclick="window.print()" class="btn btn-info btn-sm">
                            <i class="fas fa-print"></i> Print Results
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Results Summary -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="result-card text-center">
                                <div class="result-score {{ $attempt->score >= $attempt->assessment->passing_score ? 'passed' : 'failed' }}">
                                    {{ number_format($attempt->score, 1) }}%
                                </div>
                                <div class="result-status">
                                    @if($attempt->score >= $attempt->assessment->passing_score)
                                        <span class="badge badge-success">PASSED</span>
                                    @else
                                        <span class="badge badge-danger">FAILED</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-9">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="info-item">
                                        <strong>Correct Answers:</strong>
                                        {{ $attempt->results->where('is_correct', true)->count() }} / {{ $attempt->results->count() }}
                                    </div>
                                    <div class="info-item">
                                        <strong>Points Earned:</strong>
                                        {{ $attempt->results->sum('points_earned') }} / {{ $attempt->results->sum('max_points') }}
                                    </div>
                                    <div class="info-item">
                                        <strong>Passing Score:</strong>
                                        {{ $attempt->assessment->passing_score }}%
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-item">
                                        <strong>Time Taken:</strong>
                                        {{ $attempt->getFormattedDuration() }}
                                    </div>
                                    <div class="info-item">
                                        <strong>Started:</strong>
                                        {{ $attempt->started_at->format('M d, Y H:i') }}
                                    </div>
                                    <div class="info-item">
                                        <strong>Completed:</strong>
                                        {{ $attempt->completed_at->format('M d, Y H:i') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Performance Chart -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">Performance Breakdown</h6>
                                </div>
                                <div class="card-body">
                                    <canvas id="performanceChart" width="400" height="200"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">Question Types Performance</h6>
                                </div>
                                <div class="card-body">
                                    @php
                                        $questionTypes = [];
                                        foreach($attempt->assessment->questions as $index => $question) {
                                            $type = $question['type'];
                                            if (!isset($questionTypes[$type])) {
                                                $questionTypes[$type] = ['total' => 0, 'correct' => 0];
                                            }
                                            $questionTypes[$type]['total']++;
                                            if($attempt->results->where('question_index', $index)->first()?->is_correct) {
                                                $questionTypes[$type]['correct']++;
                                            }
                                        }
                                    @endphp
                                    @foreach($questionTypes as $type => $stats)
                                        <div class="type-performance">
                                            <div class="d-flex justify-content-between">
                                                <span>{{ ucfirst(str_replace('_', ' ', $type)) }}</span>
                                                <span>{{ $stats['correct'] }}/{{ $stats['total'] }}</span>
                                            </div>
                                            <div class="progress">
                                                <div class="progress-bar" style="width: {{ $stats['total'] > 0 ? ($stats['correct'] / $stats['total']) * 100 : 0 }}%"></div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Detailed Results -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Detailed Results</h5>
                        </div>
                        <div class="card-body">
                            @foreach($attempt->results as $result)
                                @php
                                    $question = $attempt->assessment->questions[$result->question_index];
                                @endphp
                                <div class="question-result {{ $result->is_correct ? 'correct' : 'incorrect' }}">
                                    <div class="question-header">
                                        <h6>
                                            Question {{ $result->question_index + 1 }}
                                            <span class="badge badge-{{ $result->is_correct ? 'success' : 'danger' }}">
                                                {{ $result->is_correct ? 'Correct' : 'Incorrect' }}
                                            </span>
                                            <span class="points">{{ $result->points_earned }}/{{ $result->max_points }} points</span>
                                        </h6>
                                    </div>
                                    
                                    <div class="question-content">
                                        <p><strong>Question:</strong> {{ $result->question_text }}</p>
                                        
                                        <div class="row">
                                            <div class="col-md-6">
                                                <p><strong>Your Answer:</strong></p>
                                                <div class="answer user-answer">
                                                    {{ $result->getUserAnswerText() }}
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <p><strong>Correct Answer:</strong></p>
                                                <div class="answer correct-answer">
                                                    {{ $result->getCorrectAnswerText() }}
                                                </div>
                                            </div>
                                        </div>
                                        
                                        @if($result->feedback)
                                            <div class="feedback">
                                                <strong>Explanation:</strong>
                                                <p>{{ $result->feedback }}</p>
                                            </div>
                                        @elseif(isset($question['explanation']))
                                            <div class="feedback">
                                                <strong>Explanation:</strong>
                                                <p>{{ $question['explanation'] }}</p>
                                            </div>
                                        @endif
                                        
                                        @if($result->time_spent)
                                            <div class="time-spent">
                                                <small class="text-muted">
                                                    <i class="fas fa-clock"></i> Time spent: {{ $result->getFormattedTimeSpent() }}
                                                </small>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Recommendations -->
                    @if($attempt->score < $attempt->assessment->passing_score)
                        <div class="card mt-4">
                            <div class="card-header">
                                <h5 class="mb-0">Recommendations for Improvement</h5>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-info">
                                    <h6>Areas to Focus On:</h6>
                                    <ul>
                                        @foreach($questionTypes as $type => $stats)
                                            @if($stats['total'] > 0 && ($stats['correct'] / $stats['total']) < 0.7)
                                                <li>{{ ucfirst(str_replace('_', ' ', $type)) }} questions ({{ number_format(($stats['correct'] / $stats['total']) * 100, 1) }}% correct)</li>
                                            @endif
                                        @endforeach
                                    </ul>
                                    @if($attempt->assessment->allow_retake)
                                        <p class="mb-0">Consider reviewing the material and retaking the assessment when you're ready.</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="card mt-4">
                            <div class="card-header">
                                <h5 class="mb-0">Congratulations!</h5>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-success">
                                    <h6>Excellent Performance!</h6>
                                    <p>You have successfully passed this assessment with a score of {{ number_format($attempt->score, 1) }}%.</p>
                                    <p class="mb-0">Keep up the great work and continue learning!</p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.result-card {
    background: #f8f9fa;
    border-radius: 10px;
    padding: 20px;
    border: 2px solid #dee2e6;
}

.result-score {
    font-size: 3em;
    font-weight: bold;
    margin-bottom: 10px;
}

.result-score.passed {
    color: #28a745;
}

.result-score.failed {
    color: #dc3545;
}

.result-status {
    font-size: 1.2em;
}

.info-item {
    margin-bottom: 10px;
    padding-bottom: 5px;
}

.question-result {
    border: 1px solid #dee2e6;
    border-radius: 8px;
    margin-bottom: 20px;
    overflow: hidden;
}

.question-result.correct {
    border-left: 4px solid #28a745;
}

.question-result.incorrect {
    border-left: 4px solid #dc3545;
}

.question-header {
    background: #f8f9fa;
    padding: 15px;
    border-bottom: 1px solid #dee2e6;
}

.question-header h6 {
    margin: 0;
    display: flex;
    align-items: center;
    gap: 10px;
}

.points {
    margin-left: auto;
    font-size: 0.9em;
    color: #6c757d;
}

.question-content {
    padding: 20px;
}

.answer {
    background: #f8f9fa;
    padding: 10px;
    border-radius: 4px;
    border: 1px solid #dee2e6;
    min-height: 40px;
}

.user-answer {
    border-left: 3px solid #6c757d;
}

.correct-answer {
    border-left: 3px solid #28a745;
}

.feedback {
    background: #e7f3ff;
    padding: 15px;
    border-radius: 4px;
    margin-top: 15px;
    border-left: 3px solid #007bff;
}

.time-spent {
    margin-top: 10px;
    text-align: right;
}

.type-performance {
    margin-bottom: 15px;
}

.type-performance .progress {
    height: 20px;
    margin-top: 5px;
}

@media print {
    .btn-group {
        display: none;
    }
    
    .card {
        border: none;
        box-shadow: none;
    }
    
    .question-result {
        page-break-inside: avoid;
    }
}
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Performance Chart
const ctx = document.getElementById('performanceChart').getContext('2d');
const chart = new Chart(ctx, {
    type: 'doughnut',
    data: {
        labels: ['Correct', 'Incorrect'],
        datasets: [{
            data: [
                {{ $attempt->results->where('is_correct', true)->count() }},
                {{ $attempt->results->where('is_correct', false)->count() }}
            ],
            backgroundColor: ['#28a745', '#dc3545'],
            borderWidth: 0
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
</script>
@endsection