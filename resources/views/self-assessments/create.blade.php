@extends('layouts.app')

@section('title', 'Create Self Assessment')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-plus-circle"></i>
                        Create Self Assessment
                    </h4>
                    <a href="{{ route('self-assessments.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Back to List
                    </a>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('self-assessments.store') }}">
                        @csrf
                        
                        <div class="row">
                            <!-- Period Selection -->
                            <div class="col-md-6 mb-3">
                                <label for="period" class="form-label required">Assessment Period</label>
                                <select name="period" id="period" class="form-select @error('period') is-invalid @enderror" required>
                                    <option value="">Select Period</option>
                                    @foreach($availablePeriods as $period)
                                        <option value="{{ $period }}" {{ old('period') === $period ? 'selected' : '' }}>
                                            {{ $period }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('period')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Select the period for this self assessment.</div>
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
                                          placeholder="Describe your key targets, objectives, and goals for this assessment period..." required>{{ old('targets') }}</textarea>
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
                                          placeholder="Detail your key achievements, completed projects, and successful outcomes..." required>{{ old('achievements') }}</textarea>
                                @error('achievements')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Include quantifiable results and specific accomplishments.</div>
                            </div>
                        </div>

                        <!-- Problems Section -->
                        <div class="mb-4">
                            <h5 class="border-bottom pb-2 mb-3">
                                <span style="color: #ffc107;">⚠</span>
                                Challenges & Problems
                            </h5>
                            <div class="mb-3">
                                <label for="problems" class="form-label required">What challenges or problems did you encounter?</label>
                                <textarea name="problems" id="problems" rows="4" 
                                          class="form-control @error('problems') is-invalid @enderror" 
                                          placeholder="Describe the main challenges, obstacles, or problems you faced during this period..." required>{{ old('problems') }}</textarea>
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
                                          placeholder="Explain the solutions you implemented and suggest improvements for future periods..." required>{{ old('solutions') }}</textarea>
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
                                          placeholder="Share your observations about market conditions, customer behavior, competitive landscape, and business opportunities..." required>{{ old('market_analysis') }}</textarea>
                                @error('market_analysis')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Include market trends, customer feedback, and strategic insights.</div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-between">
                            <div>
                                <button type="submit" name="action" value="save_draft" class="btn btn-outline-secondary">
                                    <i class="fas fa-save"></i> Save as Draft
                                </button>
                            </div>
                            <div>
                                <a href="{{ route('self-assessments.index') }}" class="btn btn-light me-2">
                                    × Cancel
                                </a>
                                <button type="submit" name="action" value="submit" class="btn btn-primary">
                                    <i class="fas fa-paper-plane"></i> Submit Assessment
                                </button>
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
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Auto-save functionality (optional)
    let autoSaveTimeout;
    $('textarea').on('input', function() {
        clearTimeout(autoSaveTimeout);
        autoSaveTimeout = setTimeout(function() {
            // Could implement auto-save to localStorage here
            console.log('Auto-save triggered');
        }, 2000);
    });
    
    // Form validation
    $('form').on('submit', function(e) {
        let isValid = true;
        const requiredFields = ['period', 'targets', 'achievements', 'problems', 'solutions', 'market_analysis'];
        
        requiredFields.forEach(function(field) {
            const input = $(`[name="${field}"]`);
            if (!input.val().trim()) {
                input.addClass('is-invalid');
                isValid = false;
            } else {
                input.removeClass('is-invalid');
            }
        });
        
        if (!isValid) {
            e.preventDefault();
            $('html, body').animate({
                scrollTop: $('.is-invalid').first().offset().top - 100
            }, 500);
        }
    });
    
    // Remove validation errors on input
    $('input, textarea, select').on('input change', function() {
        $(this).removeClass('is-invalid');
    });
});
</script>
@endpush
@endsection