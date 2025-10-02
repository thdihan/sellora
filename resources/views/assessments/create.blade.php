@extends('layouts.app')

@section('title', 'Create Assessment')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Create Assessment</h1>
        <a href="{{ route('assessments.index') }}" class="btn btn-secondary">
            Back to Assessments
        </a>
    </div>

    <form action="{{ route('assessments.store') }}" method="POST" id="assessmentForm">
        @csrf
        
        <div class="row">
            <!-- Main Form -->
            <div class="col-lg-8">
                <!-- Basic Information -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Basic Information</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                       id="title" name="title" value="{{ old('title') }}" required>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-12 mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" 
                                          id="description" name="description" rows="3">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="category" class="form-label">Category</label>
                                <select class="form-control @error('category') is-invalid @enderror" id="category" name="category">
                                    <option value="">Select Category</option>
                                    <option value="technical" {{ old('category') === 'technical' ? 'selected' : '' }}>Technical</option>
                                    <option value="soft_skills" {{ old('category') === 'soft_skills' ? 'selected' : '' }}>Soft Skills</option>
                                    <option value="knowledge" {{ old('category') === 'knowledge' ? 'selected' : '' }}>Knowledge</option>
                                    <option value="personality" {{ old('category') === 'personality' ? 'selected' : '' }}>Personality</option>
                                </select>
                                @error('category')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="type" class="form-label">Type <span class="text-danger">*</span></label>
                                <select class="form-control @error('type') is-invalid @enderror" id="type" name="type" required>
                                    <option value="">Select Type</option>
                                    <option value="quiz" {{ old('type') === 'quiz' ? 'selected' : '' }}>Quiz</option>
                                    <option value="survey" {{ old('type') === 'survey' ? 'selected' : '' }}>Survey</option>
                                    <option value="exam" {{ old('type') === 'exam' ? 'selected' : '' }}>Exam</option>
                                    <option value="self_assessment" {{ old('type') === 'self_assessment' ? 'selected' : '' }}>Self Assessment</option>
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Questions -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">Questions</h6>
                        <button type="button" class="btn btn-sm btn-primary" onclick="addQuestion()">
                            <i class="fas fa-plus"></i> Add Question
                        </button>
                    </div>
                    <div class="card-body">
                        <div id="questionsContainer">
                            <!-- Questions will be added here dynamically -->
                        </div>
                        @error('questions')
                            <div class="text-danger mt-2">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Scoring & Settings -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Scoring & Settings</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="scoring_method" class="form-label">Scoring Method <span class="text-danger">*</span></label>
                                <select class="form-control @error('scoring_method') is-invalid @enderror" 
                                        id="scoring_method" name="scoring_method" required>
                                    <option value="percentage" {{ old('scoring_method') === 'percentage' ? 'selected' : '' }}>Percentage</option>
                                    <option value="points" {{ old('scoring_method') === 'points' ? 'selected' : '' }}>Points</option>
                                    <option value="weighted" {{ old('scoring_method') === 'weighted' ? 'selected' : '' }}>Weighted</option>
                                </select>
                                @error('scoring_method')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="max_score" class="form-label">Max Score <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('max_score') is-invalid @enderror" 
                                       id="max_score" name="max_score" value="{{ old('max_score', 100) }}" min="1" required>
                                @error('max_score')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="passing_score" class="form-label">Passing Score <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('passing_score') is-invalid @enderror" 
                                       id="passing_score" name="passing_score" value="{{ old('passing_score', 70) }}" min="0" required>
                                @error('passing_score')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="difficulty_level" class="form-label">Difficulty Level <span class="text-danger">*</span></label>
                                <select class="form-control @error('difficulty_level') is-invalid @enderror" 
                                        id="difficulty_level" name="difficulty_level" required>
                                    <option value="beginner" {{ old('difficulty_level') === 'beginner' ? 'selected' : '' }}>Beginner</option>
                                    <option value="intermediate" {{ old('difficulty_level') === 'intermediate' ? 'selected' : '' }}>Intermediate</option>
                                    <option value="advanced" {{ old('difficulty_level') === 'advanced' ? 'selected' : '' }}>Advanced</option>
                                </select>
                                @error('difficulty_level')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="time_limit" class="form-label">Time Limit (minutes)</label>
                                <input type="number" class="form-control @error('time_limit') is-invalid @enderror" 
                                       id="time_limit" name="time_limit" value="{{ old('time_limit') }}" min="1">
                                @error('time_limit')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="attempts_allowed" class="form-label">Attempts Allowed</label>
                                <input type="number" class="form-control @error('attempts_allowed') is-invalid @enderror" 
                                       id="attempts_allowed" name="attempts_allowed" value="{{ old('attempts_allowed') }}" min="1">
                                <small class="form-text text-muted">Leave empty for unlimited attempts</small>
                                @error('attempts_allowed')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Advanced Settings -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Advanced Settings</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="start_date" class="form-label">Start Date</label>
                                <input type="datetime-local" class="form-control @error('start_date') is-invalid @enderror" 
                                       id="start_date" name="start_date" value="{{ old('start_date') }}">
                                @error('start_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="end_date" class="form-label">End Date</label>
                                <input type="datetime-local" class="form-control @error('end_date') is-invalid @enderror" 
                                       id="end_date" name="end_date" value="{{ old('end_date') }}">
                                @error('end_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-12 mb-3">
                                <label for="instructions" class="form-label">Instructions</label>
                                <textarea class="form-control @error('instructions') is-invalid @enderror" 
                                          id="instructions" name="instructions" rows="3">{{ old('instructions') }}</textarea>
                                @error('instructions')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-12 mb-3">
                                <label for="completion_message" class="form-label">Completion Message</label>
                                <textarea class="form-control @error('completion_message') is-invalid @enderror" 
                                          id="completion_message" name="completion_message" rows="2">{{ old('completion_message') }}</textarea>
                                @error('completion_message')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" 
                                           {{ old('is_active', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        Active (Available for taking)
                                    </label>
                                </div>
                                
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" id="auto_grade" name="auto_grade" value="1" 
                                           {{ old('auto_grade', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="auto_grade">
                                        Auto Grade
                                    </label>
                                </div>
                                
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" id="show_results_immediately" name="show_results_immediately" value="1" 
                                           {{ old('show_results_immediately', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="show_results_immediately">
                                        Show Results Immediately
                                    </label>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" id="randomize_questions" name="randomize_questions" value="1" 
                                           {{ old('randomize_questions') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="randomize_questions">
                                        Randomize Questions
                                    </label>
                                </div>
                                
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" id="allow_review" name="allow_review" value="1" 
                                           {{ old('allow_review', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="allow_review">
                                        Allow Review
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Preview -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Preview</h6>
                    </div>
                    <div class="card-body">
                        <div id="assessmentPreview">
                            <div class="text-muted text-center py-4">
                                <i class="fas fa-eye fa-2x mb-2"></i>
                                <p>Fill in the form to see preview</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Actions -->
                <div class="card shadow">
                    <div class="card-body">
                        <button type="submit" class="btn btn-primary btn-block mb-2">
                            <i class="fas fa-save"></i> Create Assessment
                        </button>
                        <a href="{{ route('assessments.index') }}" class="btn btn-secondary btn-block">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Question Template -->
<template id="questionTemplate">
    <div class="question-item border rounded p-3 mb-3" data-question-index="0">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="mb-0">Question <span class="question-number">1</span></h6>
            <button type="button" class="btn btn-sm btn-danger" onclick="removeQuestion(this)">
                <i class="fas fa-trash"></i>
            </button>
        </div>
        
        <div class="mb-3">
            <label class="form-label">Question Text <span class="text-danger">*</span></label>
            <textarea class="form-control question-text" name="questions[0][question]" rows="2" required></textarea>
        </div>
        
        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">Question Type <span class="text-danger">*</span></label>
                <select class="form-control question-type" name="questions[0][type]" onchange="updateQuestionOptions(this)" required>
                    <option value="multiple_choice">Multiple Choice</option>
                    <option value="true_false">True/False</option>
                    <option value="multiple_select">Multiple Select</option>
                    <option value="text">Text Answer</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Points</label>
                <input type="number" class="form-control" name="questions[0][points]" value="1" min="1">
            </div>
        </div>
        
        <div class="question-options">
            <!-- Options will be populated based on question type -->
        </div>
        
        <div class="correct-answer-section">
            <label class="form-label">Correct Answer <span class="text-danger">*</span></label>
            <input type="text" class="form-control correct-answer" name="questions[0][correct_answer]" required>
        </div>
    </div>
</template>

@push('scripts')
<script>
let questionIndex = 0;

function addQuestion() {
    const template = document.getElementById('questionTemplate');
    const clone = template.content.cloneNode(true);
    
    // Update indices and names
    const questionItem = clone.querySelector('.question-item');
    questionItem.setAttribute('data-question-index', questionIndex);
    
    // Update question number
    clone.querySelector('.question-number').textContent = questionIndex + 1;
    
    // Update form field names
    const inputs = clone.querySelectorAll('input, select, textarea');
    inputs.forEach(input => {
        const name = input.getAttribute('name');
        if (name) {
            input.setAttribute('name', name.replace('[0]', `[${questionIndex}]`));
        }
    });
    
    document.getElementById('questionsContainer').appendChild(clone);
    
    // Initialize options for the new question
    const questionTypeSelect = questionItem.querySelector('.question-type');
    updateQuestionOptions(questionTypeSelect);
    
    questionIndex++;
    updatePreview();
}

function removeQuestion(button) {
    const questionItem = button.closest('.question-item');
    questionItem.remove();
    
    // Renumber questions
    const questions = document.querySelectorAll('.question-item');
    questions.forEach((question, index) => {
        question.querySelector('.question-number').textContent = index + 1;
        
        // Update form field names
        const inputs = question.querySelectorAll('input, select, textarea');
        inputs.forEach(input => {
            const name = input.getAttribute('name');
            if (name) {
                const newName = name.replace(/\[\d+\]/, `[${index}]`);
                input.setAttribute('name', newName);
            }
        });
        
        question.setAttribute('data-question-index', index);
    });
    
    questionIndex = questions.length;
    updatePreview();
}

function updateQuestionOptions(select) {
    const questionItem = select.closest('.question-item');
    const optionsContainer = questionItem.querySelector('.question-options');
    const correctAnswerSection = questionItem.querySelector('.correct-answer-section');
    const correctAnswerInput = correctAnswerSection.querySelector('.correct-answer');
    const questionIndex = questionItem.getAttribute('data-question-index');
    
    optionsContainer.innerHTML = '';
    
    switch (select.value) {
        case 'multiple_choice':
        case 'multiple_select':
            optionsContainer.innerHTML = `
                <label class="form-label">Options <span class="text-danger">*</span></label>
                <div class="options-list">
                    <div class="input-group mb-2">
                        <input type="text" class="form-control" name="questions[${questionIndex}][options][]" placeholder="Option 1" required>
                        <button type="button" class="btn btn-outline-danger" onclick="removeOption(this)">×</button>
                    </div>
                    <div class="input-group mb-2">
                        <input type="text" class="form-control" name="questions[${questionIndex}][options][]" placeholder="Option 2" required>
                        <button type="button" class="btn btn-outline-danger" onclick="removeOption(this)">×</button>
                    </div>
                </div>
                <button type="button" class="btn btn-sm btn-outline-primary" onclick="addOption(this, ${questionIndex})">Add Option</button>
            `;
            correctAnswerInput.placeholder = 'Enter the correct option text';
            break;
            
        case 'true_false':
            optionsContainer.innerHTML = `
                <input type="hidden" name="questions[${questionIndex}][options][]" value="True">
                <input type="hidden" name="questions[${questionIndex}][options][]" value="False">
            `;
            correctAnswerInput.placeholder = 'Enter "True" or "False"';
            break;
            
        case 'text':
            correctAnswerInput.placeholder = 'Enter the expected answer or keywords';
            break;
    }
}

function addOption(button, questionIndex) {
    const optionsList = button.previousElementSibling;
    const optionCount = optionsList.children.length + 1;
    
    const optionDiv = document.createElement('div');
    optionDiv.className = 'input-group mb-2';
    optionDiv.innerHTML = `
        <input type="text" class="form-control" name="questions[${questionIndex}][options][]" placeholder="Option ${optionCount}" required>
        <button type="button" class="btn btn-outline-danger" onclick="removeOption(this)">×</button>
    `;
    
    optionsList.appendChild(optionDiv);
}

function removeOption(button) {
    const optionDiv = button.closest('.input-group');
    optionDiv.remove();
}

function updatePreview() {
    const title = document.getElementById('title').value;
    const description = document.getElementById('description').value;
    const category = document.getElementById('category').value;
    const type = document.getElementById('type').value;
    const questions = document.querySelectorAll('.question-item');
    
    let previewHtml = '';
    
    if (title) {
        previewHtml += `<h5>${title}</h5>`;
    }
    
    if (description) {
        previewHtml += `<p class="text-muted">${description}</p>`;
    }
    
    if (category) {
        previewHtml += `<span class="badge badge-secondary mb-2">${category.replace('_', ' ')}</span><br>`;
    }
    
    if (type) {
        previewHtml += `<span class="badge badge-info mb-3">${type.replace('_', ' ')}</span>`;
    }
    
    if (questions.length > 0) {
        previewHtml += `<hr><small class="text-muted">${questions.length} question(s)</small>`;
    }
    
    if (!previewHtml) {
        previewHtml = `
            <div class="text-muted text-center py-4">
                <i class="fas fa-eye fa-2x mb-2"></i>
                <p>Fill in the form to see preview</p>
            </div>
        `;
    }
    
    document.getElementById('assessmentPreview').innerHTML = previewHtml;
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    // Add first question
    addQuestion();
    
    // Add event listeners for preview updates
    ['title', 'description', 'category', 'type'].forEach(id => {
        const element = document.getElementById(id);
        if (element) {
            element.addEventListener('input', updatePreview);
            element.addEventListener('change', updatePreview);
        }
    });
    
    // Form validation
    document.getElementById('assessmentForm').addEventListener('submit', function(e) {
        const questions = document.querySelectorAll('.question-item');
        if (questions.length === 0) {
            e.preventDefault();
            alert('Please add at least one question.');
            return false;
        }
    });
});
</script>
@endpush
@endsection