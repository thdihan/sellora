@extends('layouts.app')

@section('title', 'Edit Self Assessment')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-edit"></i>
                        Edit Self Assessment - {{ $assessment->period }}
                    </h4>
                    <div class="d-flex gap-2">
                        <a href="{{ route('self-assessments.show', $assessment) }}" class="btn btn-outline-info">
                            <i class="fas fa-eye"></i> View
                        </a>
                        <a href="{{ route('self-assessments.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Status Info -->
                    <div class="alert alert-info d-flex align-items-center mb-4">
                        <i class="fas fa-info-circle me-2"></i>
                        <div>
                            <strong>Status:</strong> {!! $assessment->status_badge !!}
                            @if($assessment->status === 'submitted')
                                <span class="ms-2 text-muted">Submitted on {{ $assessment->submitted_at->format('M d, Y \a\t g:i A') }}</span>
                            @elseif($assessment->status === 'reviewed')
                                <span class="ms-2 text-muted">Reviewed on {{ $assessment->reviewed_at->format('M d, Y \a\t g:i A') }}</span>
                            @endif
                        </div>
                    </div>

                    @if(!$assessment->is_editable)
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            This assessment cannot be edited because it has been {{ $assessment->status }}.
                            @if($assessment->status === 'submitted')
                                You can revert it to draft status to make changes.
                            @endif
                        </div>
                    @endif

                    <form method="POST" action="{{ route('self-assessments.update', $assessment) }}">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <!-- Period Display -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Assessment Period</label>
                                <input type="text" class="form-control" value="{{ $assessment->period }}" readonly>
                                <div class="form-text">Period cannot be changed after creation.</div>
                            </div>
                        </div>

                        <!-- Targets Section -->
                        <div class="mb-4">
                            <h5 class="border-bottom pb-2 mb-3">
                                <i class="fas fa-bullseye text-primary"></i>
                                Targets & Goals
                            </h5>
                            <div class="mb-3">
                                <label for="targets" class="form-label required">What were your main targets and goals for this period?</label>
                                <textarea name="targets" id="targets" rows="4" 
                                          class="form-control @error('targets') is-invalid @enderror" 
                                          placeholder="Describe your key targets, objectives, and goals for this assessment period..." 
                                          {{ !$assessment->is_editable ? 'readonly' : '' }} required>{{ old('targets', $assessment->targets) }}</textarea>
                                @error('targets')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Be specific about your planned objectives and measurable goals.</div>
                            </div>
                        </div>

                        <!-- Achievements Section -->
                        <div class="mb-4">
                            <h5 class="border-bottom pb-2 mb-3">
                                <i class="fas fa-trophy text-success"></i>
                                Achievements & Accomplishments
                            </h5>
                            <div class="mb-3">
                                <label for="achievements" class="form-label required">What did you achieve during this period?</label>
                                <textarea name="achievements" id="achievements" rows="4" 
                                          class="form-control @error('achievements') is-invalid @enderror" 
                                          placeholder="Detail your key achievements, completed projects, and successful outcomes..." 
                                          {{ !$assessment->is_editable ? 'readonly' : '' }} required>{{ old('achievements', $assessment->achievements) }}</textarea>
                                @error('achievements')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Include quantifiable results and specific accomplishments.</div>
                            </div>
                        </div>

                        <!-- Problems Section -->
                        <div class="mb-4">
                            <h5 class="border-bottom pb-2 mb-3">
                                <i class="fas fa-exclamation-triangle text-warning"></i>
                                Challenges & Problems
                            </h5>
                            <div class="mb-3">
                                <label for="problems" class="form-label required">What challenges or problems did you encounter?</label>
                                <textarea name="problems" id="problems" rows="4" 
                                          class="form-control @error('problems') is-invalid @enderror" 
                                          placeholder="Describe the main challenges, obstacles, or problems you faced during this period..." 
                                          {{ !$assessment->is_editable ? 'readonly' : '' }} required>{{ old('problems', $assessment->problems) }}</textarea>
                                @error('problems')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Be honest about difficulties and roadblocks encountered.</div>
                            </div>
                        </div>

                        <!-- Solutions Section -->
                        <div class="mb-4">
                            <h5 class="border-bottom pb-2 mb-3">
                                <i class="fas fa-lightbulb text-info"></i>
                                Solutions & Improvements
                            </h5>
                            <div class="mb-3">
                                <label for="solutions" class="form-label required">How did you address these challenges and what improvements do you suggest?</label>
                                <textarea name="solutions" id="solutions" rows="4" 
                                          class="form-control @error('solutions') is-invalid @enderror" 
                                          placeholder="Explain the solutions you implemented and suggest improvements for future periods..." 
                                          {{ !$assessment->is_editable ? 'readonly' : '' }} required>{{ old('solutions', $assessment->solutions) }}</textarea>
                                @error('solutions')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Include both implemented solutions and recommendations for improvement.</div>
                            </div>
                        </div>

                        <!-- Market Analysis Section -->
                        <div class="mb-4">
                            <h5 class="border-bottom pb-2 mb-3">
                                <i class="fas fa-chart-line text-primary"></i>
                                Market Analysis & Insights
                            </h5>
                            <div class="mb-3">
                                <label for="market_analysis" class="form-label required">What market trends, opportunities, or insights did you observe?</label>
                                <textarea name="market_analysis" id="market_analysis" rows="4" 
                                          class="form-control @error('market_analysis') is-invalid @enderror" 
                                          placeholder="Share your observations about market conditions, customer behavior, competitive landscape, and business opportunities..." 
                                          {{ !$assessment->is_editable ? 'readonly' : '' }} required>{{ old('market_analysis', $assessment->market_analysis) }}</textarea>
                                @error('market_analysis')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Include market trends, customer feedback, and strategic insights.</div>
                            </div>
                        </div>

                        @if($assessment->reviewer_comments)
                            <!-- Reviewer Comments -->
                            <div class="mb-4">
                                <h5 class="border-bottom pb-2 mb-3">
                                    <i class="fas fa-comments text-info"></i>
                                    Reviewer Comments
                                </h5>
                                <div class="alert alert-light">
                                    <div class="d-flex align-items-start">
                                        <i class="fas fa-user-tie me-2 mt-1"></i>
                                        <div>
                                            <strong>{{ $assessment->reviewer->name ?? 'Reviewer' }}</strong>
                                            <small class="text-muted ms-2">{{ $assessment->reviewed_at->format('M d, Y \a\t g:i A') }}</small>
                                            <div class="mt-2">{{ $assessment->reviewer_comments }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-between">
                            <div>
                                @if($assessment->is_editable)
                                    <button type="submit" name="action" value="save_draft" class="btn btn-outline-secondary">
                                        <i class="fas fa-save"></i> Save as Draft
                                    </button>
                                @endif
                                @if($assessment->status === 'submitted')
                                    <button type="submit" name="action" value="revert_to_draft" class="btn btn-outline-warning"
                                            onclick="return confirm('Are you sure you want to revert this assessment to draft status?')">
                                        <i class="fas fa-undo"></i> Revert to Draft
                                    </button>
                                @endif
                            </div>
                            <div>
                                <a href="{{ route('self-assessments.index') }}" class="btn btn-light me-2">
                                    <i class="fas fa-times"></i> Cancel
                                </a>
                                @if($assessment->is_editable)
                                    <button type="submit" name="action" value="submit" class="btn btn-primary">
                                        <i class="fas fa-paper-plane"></i> Submit Assessment
                                    </button>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.required::after {
    content: ' *';
    color: #dc3545;
}

.form-text {
    font-size: 0.875rem;
    color: #6c757d;
}

.border-bottom {
    border-bottom: 2px solid #e9ecef !important;
}

h5 i {
    margin-right: 8px;
}

textarea.form-control {
    resize: vertical;
    min-height: 100px;
}

textarea[readonly] {
    background-color: #f8f9fa;
    opacity: 0.8;
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Form validation (only if editable)
    @if($assessment->is_editable)
    $('form').on('submit', function(e) {
        const action = $(e.originalEvent.submitter).val();
        
        if (action === 'submit' || action === 'save_draft') {
            let isValid = true;
            const requiredFields = ['targets', 'achievements', 'problems', 'solutions', 'market_analysis'];
            
            requiredFields.forEach(function(field) {
                const input = $(`[name="${field}"]`);
                if (!input.val().trim()) {
                    input.addClass('is-invalid');
                    isValid = false;
                } else {
                    input.removeClass('is-invalid');
                }
            });
            
            if (!isValid && action === 'submit') {
                e.preventDefault();
                $('html, body').animate({
                    scrollTop: $('.is-invalid').first().offset().top - 100
                }, 500);
            }
        }
    });
    
    // Remove validation errors on input
    $('textarea').on('input', function() {
        $(this).removeClass('is-invalid');
    });
    @endif
});
</script>
@endpush
@endsection