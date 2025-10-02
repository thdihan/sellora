@extends('layouts.app')

@section('title', 'Take Assessment: ' . $assessment->title)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-9">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">{{ $assessment->title }}</h4>
                    @if($assessment->time_limit)
                        <div class="timer-display">
                            <i class="fas fa-clock"></i>
                            <span id="timer">{{ $assessment->time_limit }}:00</span>
                        </div>
                    @endif
                </div>
                <div class="card-body">
                    @if($assessment->description)
                        <div class="alert alert-info">
                            <strong>Instructions:</strong> {{ $assessment->description }}
                        </div>
                    @endif

                    <form id="assessmentForm" action="{{ route('assessments.submit', $assessment) }}" method="POST">
                        @csrf
                        <input type="hidden" name="attempt_id" value="{{ $attempt->id }}">
                        <input type="hidden" name="start_time" value="{{ now()->timestamp }}">
                        
                        @foreach($assessment->questions as $index => $question)
                            <div class="question-container mb-4" data-question="{{ $index }}">
                                <div class="question-header">
                                    <h5>Question {{ $index + 1 }} of {{ count($assessment->questions) }}</h5>
                                    @if(isset($question['points']))
                                        <span class="badge badge-primary">{{ $question['points'] }} {{ $question['points'] == 1 ? 'point' : 'points' }}</span>
                                    @endif
                                </div>
                                
                                <div class="question-text mb-3">
                                    <p>{{ $question['question'] }}</p>
                                </div>

                                <div class="question-options">
                                    @if($question['type'] === 'multiple_choice')
                                        @foreach($question['options'] as $optionIndex => $option)
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" 
                                                       name="answers[{{ $index }}]" 
                                                       value="{{ $option }}" 
                                                       id="q{{ $index }}_option{{ $optionIndex }}">
                                                <label class="form-check-label" for="q{{ $index }}_option{{ $optionIndex }}">
                                                    {{ $option }}
                                                </label>
                                            </div>
                                        @endforeach
                                    @elseif($question['type'] === 'multiple_select')
                                        @foreach($question['options'] as $optionIndex => $option)
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" 
                                                       name="answers[{{ $index }}][]" 
                                                       value="{{ $option }}" 
                                                       id="q{{ $index }}_option{{ $optionIndex }}">
                                                <label class="form-check-label" for="q{{ $index }}_option{{ $optionIndex }}">
                                                    {{ $option }}
                                                </label>
                                            </div>
                                        @endforeach
                                    @elseif($question['type'] === 'true_false')
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" 
                                                   name="answers[{{ $index }}]" 
                                                   value="true" 
                                                   id="q{{ $index }}_true">
                                            <label class="form-check-label" for="q{{ $index }}_true">
                                                True
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" 
                                                   name="answers[{{ $index }}]" 
                                                   value="false" 
                                                   id="q{{ $index }}_false">
                                            <label class="form-check-label" for="q{{ $index }}_false">
                                                False
                                            </label>
                                        </div>
                                    @elseif($question['type'] === 'short_answer')
                                        <div class="form-group">
                                            <textarea class="form-control" 
                                                      name="answers[{{ $index }}]" 
                                                      rows="3" 
                                                      placeholder="Enter your answer here..."></textarea>
                                        </div>
                                    @elseif($question['type'] === 'essay')
                                        <div class="form-group">
                                            <textarea class="form-control" 
                                                      name="answers[{{ $index }}]" 
                                                      rows="6" 
                                                      placeholder="Write your essay here..."></textarea>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            @if($index < count($assessment->questions) - 1)
                                <hr>
                            @endif
                        @endforeach

                        <div class="form-actions mt-4">
                            <button type="button" class="btn btn-secondary" onclick="saveDraft()">
                                <i class="fas fa-save"></i> Save Draft
                            </button>
                            <button type="submit" class="btn btn-primary" onclick="return confirmSubmit()">
                                <i class="fas fa-check"></i> Submit Assessment
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <!-- Progress Sidebar -->
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Progress</h6>
                </div>
                <div class="card-body">
                    <div class="progress mb-3">
                        <div class="progress-bar" role="progressbar" style="width: 0%" id="progressBar">
                            0%
                        </div>
                    </div>
                    
                    <div class="question-navigation">
                        @foreach($assessment->questions as $index => $question)
                            <button type="button" class="btn btn-outline-secondary btn-sm question-nav-btn" 
                                    data-question="{{ $index }}" onclick="scrollToQuestion({{ $index }})">
                                {{ $index + 1 }}
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Assessment Info -->
            <div class="card mt-3">
                <div class="card-header">
                    <h6 class="mb-0">Assessment Info</h6>
                </div>
                <div class="card-body">
                    <div class="info-item">
                        <strong>Questions:</strong> {{ count($assessment->questions) }}
                    </div>
                    <div class="info-item">
                        <strong>Time Limit:</strong> {{ $assessment->time_limit ? $assessment->time_limit . ' minutes' : 'No limit' }}
                    </div>
                    <div class="info-item">
                        <strong>Passing Score:</strong> {{ $assessment->passing_score }}%
                    </div>
                    <div class="info-item">
                        <strong>Difficulty:</strong> 
                        <span class="badge badge-{{ $assessment->difficulty_level === 'easy' ? 'success' : ($assessment->difficulty_level === 'medium' ? 'warning' : 'danger') }}">
                            {{ ucfirst($assessment->difficulty_level) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.timer-display {
    font-size: 1.2em;
    font-weight: bold;
    color: #dc3545;
}

.question-container {
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
    padding: 20px;
    background-color: #f8f9fa;
}

.question-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
}

.question-text {
    font-size: 1.1em;
    line-height: 1.5;
}

.form-check {
    margin-bottom: 10px;
}

.form-check-label {
    margin-left: 5px;
    cursor: pointer;
}

.question-navigation {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 5px;
}

.question-nav-btn {
    width: 100%;
    aspect-ratio: 1;
}

.question-nav-btn.answered {
    background-color: #28a745;
    border-color: #28a745;
    color: white;
}

.question-nav-btn.current {
    background-color: #007bff;
    border-color: #007bff;
    color: white;
}

.info-item {
    margin-bottom: 10px;
    padding-bottom: 10px;
    border-bottom: 1px solid #eee;
}

.info-item:last-child {
    border-bottom: none;
}

.form-actions {
    text-align: center;
    padding-top: 20px;
    border-top: 1px solid #dee2e6;
}
</style>

<script>
let timeLimit = {{ $assessment->time_limit ? $assessment->time_limit * 60 : 0 }}; // in seconds
let timeRemaining = timeLimit;
let timerInterval;

// Start timer if there's a time limit
if (timeLimit > 0) {
    timerInterval = setInterval(updateTimer, 1000);
}

function updateTimer() {
    if (timeRemaining <= 0) {
        clearInterval(timerInterval);
        alert('Time is up! The assessment will be submitted automatically.');
        document.getElementById('assessmentForm').submit();
        return;
    }
    
    const minutes = Math.floor(timeRemaining / 60);
    const seconds = timeRemaining % 60;
    document.getElementById('timer').textContent = 
        `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
    
    // Change color when time is running low
    if (timeRemaining <= 300) { // 5 minutes
        document.getElementById('timer').style.color = '#dc3545';
    } else if (timeRemaining <= 600) { // 10 minutes
        document.getElementById('timer').style.color = '#ffc107';
    }
    
    timeRemaining--;
}

// Update progress and navigation
function updateProgress() {
    const questions = document.querySelectorAll('.question-container');
    const answered = document.querySelectorAll('input[type="radio"]:checked, input[type="checkbox"]:checked, textarea:not(:empty)');
    const progress = (answered.length / questions.length) * 100;
    
    document.getElementById('progressBar').style.width = progress + '%';
    document.getElementById('progressBar').textContent = Math.round(progress) + '%';
    
    // Update navigation buttons
    questions.forEach((question, index) => {
        const navBtn = document.querySelector(`[data-question="${index}"]`);
        const inputs = question.querySelectorAll('input, textarea');
        const hasAnswer = Array.from(inputs).some(input => {
            if (input.type === 'radio' || input.type === 'checkbox') {
                return input.checked;
            }
            return input.value.trim() !== '';
        });
        
        if (hasAnswer) {
            navBtn.classList.add('answered');
        } else {
            navBtn.classList.remove('answered');
        }
    });
}

// Scroll to specific question
function scrollToQuestion(index) {
    const question = document.querySelector(`[data-question="${index}"]`);
    question.scrollIntoView({ behavior: 'smooth', block: 'start' });
    
    // Update current question indicator
    document.querySelectorAll('.question-nav-btn').forEach(btn => {
        btn.classList.remove('current');
    });
    document.querySelector(`[data-question="${index}"]`).classList.add('current');
}

// Save draft
function saveDraft() {
    const formData = new FormData(document.getElementById('assessmentForm'));
    formData.append('save_draft', '1');
    
    fetch('{{ route("assessments.submit", $assessment) }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Draft saved successfully!');
        }
    })
    .catch(error => {
        console.error('Error saving draft:', error);
        alert('Error saving draft. Please try again.');
    });
}

// Confirm submission
function confirmSubmit() {
    const unanswered = document.querySelectorAll('.question-container').length - 
                     document.querySelectorAll('input[type="radio"]:checked, input[type="checkbox"]:checked, textarea:not(:empty)').length;
    
    if (unanswered > 0) {
        return confirm(`You have ${unanswered} unanswered questions. Are you sure you want to submit?`);
    }
    
    return confirm('Are you sure you want to submit your assessment? This action cannot be undone.');
}

// Auto-save every 30 seconds
setInterval(() => {
    saveDraft();
}, 30000);

// Listen for form changes to update progress
document.addEventListener('change', updateProgress);
document.addEventListener('input', updateProgress);

// Prevent accidental page leave
window.addEventListener('beforeunload', function(e) {
    e.preventDefault();
    e.returnValue = 'Are you sure you want to leave? Your progress may be lost.';
});

// Remove beforeunload when form is submitted
document.getElementById('assessmentForm').addEventListener('submit', function() {
    window.removeEventListener('beforeunload', function() {});
});

// Initial progress update
updateProgress();
</script>
@endsection