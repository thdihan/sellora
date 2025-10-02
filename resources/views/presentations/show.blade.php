@extends('layouts.app')

@section('title', $presentation->title)

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">{{ $presentation->title }}</h1>
            <p class="mb-0 text-muted">{{ $presentation->description ?: 'No description available' }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('presentations.download', $presentation) }}" class="btn btn-success">
                <i class="fas fa-download me-1"></i> Download
            </a>
            @if($presentation->canBeEditedBy(auth()->user()))
                <a href="{{ route('presentations.edit', $presentation) }}" class="btn btn-warning">
                    <i class="fas fa-edit me-1"></i> Edit
                </a>
            @endif
            <a href="{{ route('presentations.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Back
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Presentation Preview -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Presentation Preview</h5>
                    <div class="d-flex gap-2">
                        {!! $presentation->status_badge !!}
                        @if($presentation->is_template)
                            <span class="badge bg-warning">Template</span>
                        @endif
                        @if($presentation->is_public)
                            <span class="badge bg-info">Public</span>
                        @endif
                    </div>
                </div>
                <div class="card-body text-center">
                    <div class="presentation-preview mb-3">
                        <img src="{{ $presentation->thumbnail_url }}" 
                             class="img-fluid rounded shadow" 
                             alt="{{ $presentation->title }}"
                             style="max-height: 400px; width: auto;">
                    </div>
                    <div class="d-flex justify-content-center gap-3 mb-3">
                        <a href="{{ route('presentations.download', $presentation) }}" 
                           class="btn btn-primary btn-lg" onclick="incrementDownload({{ $presentation->id }})">
                            <i class="fas fa-download me-2"></i> Download Presentation
                        </a>
                        @if($presentation->canBeViewedBy(auth()->user()))
                            <button type="button" class="btn btn-outline-primary btn-lg" onclick="openFullscreen()">
                                <i class="fas fa-expand me-2"></i> View Fullscreen
                            </button>
                        @endif
                    </div>
                    <p class="text-muted">{{ $presentation->original_filename }} • {{ $presentation->formatted_file_size }}</p>
                </div>
            </div>

            <!-- Presentation Details -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Details</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <dl class="row">
                                <dt class="col-sm-4">Category:</dt>
                                <dd class="col-sm-8">
                                    <span class="badge bg-light text-dark">{{ ucfirst($presentation->category) }}</span>
                                </dd>
                                <dt class="col-sm-4">Created:</dt>
                                <dd class="col-sm-8">{{ $presentation->created_at->format('M d, Y g:i A') }}</dd>
                                <dt class="col-sm-4">Updated:</dt>
                                <dd class="col-sm-8">{{ $presentation->updated_at->format('M d, Y g:i A') }}</dd>
                                <dt class="col-sm-4">Owner:</dt>
                                <dd class="col-sm-8">{{ $presentation->user->name }}</dd>
                            </dl>
                        </div>
                        <div class="col-md-6">
                            <dl class="row">
                                <dt class="col-sm-4">File Size:</dt>
                                <dd class="col-sm-8">{{ $presentation->formatted_file_size }}</dd>
                                <dt class="col-sm-4">Views:</dt>
                                <dd class="col-sm-8">{{ number_format($presentation->views_count) }}</dd>
                                <dt class="col-sm-4">Downloads:</dt>
                                <dd class="col-sm-8">{{ number_format($presentation->downloads_count) }}</dd>
                                <dt class="col-sm-4">Version:</dt>
                                <dd class="col-sm-8">{{ $presentation->version_number ?? '1.0' }}</dd>
                            </dl>
                        </div>
                    </div>
                    @if($presentation->tags)
                        <hr>
                        <div>
                            <strong>Tags:</strong>
                            @foreach(explode(',', $presentation->tags) as $tag)
                                <span class="badge bg-secondary me-1">{{ trim($tag) }}</span>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <!-- Comments Section -->
            @if($presentation->is_public || $presentation->canBeViewedBy(auth()->user()))
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Comments ({{ $presentation->comments->count() }})</h5>
                    </div>
                    <div class="card-body">
                        <!-- Add Comment Form -->
                        @auth
                            <form action="{{ route('presentations.comments.store', $presentation) }}" method="POST" class="mb-4">
                                @csrf
                                <div class="mb-3">
                                    <textarea class="form-control" name="comment" rows="3" 
                                              placeholder="Add a comment..." required></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="fas fa-comment me-1"></i> Add Comment
                                </button>
                            </form>
                        @endauth

                        <!-- Comments List -->
                        @if($presentation->comments->count() > 0)
                            <div class="comments-list">
                                @foreach($presentation->comments->latest()->take(10) as $comment)
                                    <div class="comment mb-3 pb-3 border-bottom">
                                        <div class="d-flex align-items-start">
                                            <div class="avatar me-3">
                                                <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center" 
                                                     style="width: 40px; height: 40px;">
                                                    <span class="text-white fw-bold">
                                                        {{ strtoupper(substr($comment->user->name, 0, 1)) }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1">
                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                    <strong>{{ $comment->user->name }}</strong>
                                                    <small class="text-muted">{{ $comment->created_at->diffForHumans() }}</small>
                                                </div>
                                                <p class="mb-0">{{ $comment->content }}</p>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted text-center py-3">No comments yet. Be the first to comment!</p>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Quick Stats -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="card-title mb-0">Statistics</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="border-end">
                                <h3 class="text-primary mb-0">{{ number_format($presentation->views_count) }}</h3>
                                <small class="text-muted">Views</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <h3 class="text-success mb-0">{{ number_format($presentation->downloads_count) }}</h3>
                            <small class="text-muted">Downloads</small>
                        </div>
                    </div>
                    @if($presentation->canBeEditedBy(auth()->user()))
                        <hr>
                        <a href="{{ route('presentations.analytics', $presentation) }}" class="btn btn-outline-primary btn-sm w-100">
                            <i class="fas fa-chart-bar me-1"></i> View Detailed Analytics
                        </a>
                    @endif
                </div>
            </div>

            <!-- Actions -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="card-title mb-0">Actions</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('presentations.download', $presentation) }}" 
                           class="btn btn-success btn-sm" onclick="incrementDownload({{ $presentation->id }})">
                            Download
                        </a>
                        <a href="{{ route('presentations.duplicate', $presentation) }}" class="btn btn-info btn-sm">
                                    Duplicate
                                </a>
                        @if($presentation->canBeEditedBy(auth()->user()))
                            <a href="{{ route('presentations.edit', $presentation) }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit me-1"></i> Edit
                            </a>
                            <hr>
                            <button type="button" class="btn btn-outline-warning btn-sm" onclick="toggleTemplate({{ $presentation->id }})">
                                <i class="fas fa-star me-1"></i> {{ $presentation->is_template ? 'Remove from Templates' : 'Make Template' }}
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="toggleArchive({{ $presentation->id }})">
                                <i class="fas fa-archive me-1"></i> {{ $presentation->status === 'archived' ? 'Restore' : 'Archive' }}
                            </button>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Sharing -->
            @if($presentation->is_public || $presentation->canBeEditedBy(auth()->user()))
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="card-title mb-0">Share</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="shareUrl" class="form-label">Share URL</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="shareUrl" 
                                       value="{{ route('presentations.show', $presentation) }}" readonly>
                                <button class="btn btn-outline-secondary" type="button" onclick="copyToClipboard()">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </div>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="https://twitter.com/intent/tweet?url={{ urlencode(route('presentations.show', $presentation)) }}&text={{ urlencode($presentation->title) }}" 
                               target="_blank" class="btn btn-outline-primary btn-sm flex-fill">
                                <i class="fab fa-twitter"></i>
                            </a>
                            <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(route('presentations.show', $presentation)) }}" 
                               target="_blank" class="btn btn-outline-primary btn-sm flex-fill">
                                <i class="fab fa-linkedin"></i>
                            </a>
                            <a href="mailto:?subject={{ urlencode($presentation->title) }}&body={{ urlencode('Check out this presentation: ' . route('presentations.show', $presentation)) }}" 
                               class="btn btn-outline-primary btn-sm flex-fill">
                                <i class="fas fa-envelope"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Version History -->
            @if($presentation->versions->count() > 0)
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="card-title mb-0">Version History</h6>
                    </div>
                    <div class="card-body">
                        <div class="list-group list-group-flush">
                            <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <div>
                                    <strong>Current Version</strong>
                                    <br><small class="text-muted">{{ $presentation->updated_at->format('M d, Y g:i A') }}</small>
                                </div>
                                <span class="badge bg-primary">Active</span>
                            </div>
                            @foreach($presentation->versions->take(5) as $version)
                                <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <div>
                                        <span>Version {{ $version->version_number }}</span>
                                        <br><small class="text-muted">{{ $version->created_at->format('M d, Y g:i A') }}</small>
                                    </div>
                                    <a href="{{ $version->file_url }}" class="btn btn-sm btn-outline-secondary">
                                        <i class="fas fa-download"></i>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Related Presentations -->
            @if(isset($relatedPresentations) && $relatedPresentations->count() > 0)
                <div class="card">
                    <div class="card-header">
                        <h6 class="card-title mb-0">Related Presentations</h6>
                    </div>
                    <div class="card-body">
                        @foreach($relatedPresentations as $related)
                            <div class="d-flex align-items-center mb-3">
                                <img src="{{ $related->thumbnail_url }}" class="rounded me-2" 
                                     width="40" height="30" style="object-fit: cover;">
                                <div class="flex-grow-1">
                                    <a href="{{ route('presentations.show', $related) }}" 
                                       class="text-decoration-none">
                                        <div class="small fw-bold">{{ Str::limit($related->title, 30) }}</div>
                                    </a>
                                    <div class="text-muted" style="font-size: 0.75rem;">
                                        {{ $related->views_count }} views
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Fullscreen Modal -->
<div class="modal fade" id="fullscreenModal" tabindex="-1">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ $presentation->title }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body d-flex align-items-center justify-content-center">
                <img src="{{ $presentation->thumbnail_url }}" class="img-fluid" alt="{{ $presentation->title }}">
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Increment view count on page load
document.addEventListener('DOMContentLoaded', function() {
    incrementView({{ $presentation->id }});
});

// Increment view count
function incrementView(id) {
    fetch(`/presentations/${id}/view`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        }
    })
    .catch(error => console.error('Error incrementing view:', error));
}

// Increment download count
function incrementDownload(id) {
    fetch(`/presentations/${id}/download-count`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        }
    })
    .catch(error => console.error('Error incrementing download:', error));
}

// Open fullscreen view
function openFullscreen() {
    new bootstrap.Modal(document.getElementById('fullscreenModal')).show();
}

// Copy share URL to clipboard
function copyToClipboard() {
    const shareUrl = document.getElementById('shareUrl');
    shareUrl.select();
    shareUrl.setSelectionRange(0, 99999);
    document.execCommand('copy');
    
    // Show feedback
    const button = event.target.closest('button');
    const originalHtml = button.innerHTML;
    button.innerHTML = '<i class="fas fa-check"></i>';
    button.classList.add('btn-success');
    button.classList.remove('btn-outline-secondary');
    
    setTimeout(() => {
        button.innerHTML = originalHtml;
        button.classList.remove('btn-success');
        button.classList.add('btn-outline-secondary');
    }, 2000);
}

// Toggle template status
function toggleTemplate(id) {
    fetch(`/presentations/${id}/toggle-template`, {
        method: 'PATCH',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error updating template status');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error updating template status');
    });
}

// Toggle archive status
function toggleArchive(id) {
    fetch(`/presentations/${id}/toggle-archive`, {
        method: 'PATCH',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error updating archive status');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error updating archive status');
    });
}
</script>
@endpush