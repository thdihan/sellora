@extends('layouts.app')

@section('title', 'Presentations')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Presentations</h1>
            <p class="mb-0 text-muted">Manage your presentation files and templates</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('presentations.auto-reports') }}" class="btn btn-outline-info">
                📈 Auto Reports
            </a>
            <div class="dropdown">
                <button class="btn btn-outline-primary dropdown-toggle" type="button" id="exportDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    📥 Export
                </button>
                <ul class="dropdown-menu" aria-labelledby="exportDropdown">
                    <li><a class="dropdown-item" href="{{ route('presentations.export', ['format' => 'csv']) }}">
                        📄 CSV Format
                    </a></li>
                    <li><a class="dropdown-item" href="{{ route('presentations.export', ['format' => 'excel']) }}">
                        📊 Excel Format
                    </a></li>
                    <li><a class="dropdown-item" href="{{ route('presentations.export', ['format' => 'pdf']) }}">
                        📄 PDF Format
                    </a></li>
                    <li><a class="dropdown-item" href="{{ route('presentations.export', ['format' => 'word']) }}">
                        📄 Word Format
                    </a></li>
                </ul>
            </div>
            @php
                $nsmPlusRoles = ['NSM', 'NSM+', 'RSM', 'ASM', 'Author'];
            @endphp
            @if(in_array(auth()->user()->role, $nsmPlusRoles))
                <a href="{{ route('reports.index') }}" class="btn btn-outline-success">
                    ✨ Generate from Report
                </a>
            @endif
            <a href="{{ route('presentations.create') }}" class="btn btn-primary">
                ➕ Upload Presentation
            </a>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('presentations.index') }}" class="row g-3">
                <div class="col-md-4">
                    <label for="search" class="form-label">Search</label>
                    <input type="text" class="form-control" id="search" name="search" 
                           value="{{ request('search') }}" placeholder="Search presentations...">
                </div>
                <div class="col-md-2">
                    <label for="category" class="form-label">Category</label>
                    <select class="form-select" id="category" name="category">
                        <option value="">All Categories</option>
                        <option value="business" {{ request('category') == 'business' ? 'selected' : '' }}>Business</option>
                        <option value="education" {{ request('category') == 'education' ? 'selected' : '' }}>Education</option>
                        <option value="marketing" {{ request('category') == 'marketing' ? 'selected' : '' }}>Marketing</option>
                        <option value="training" {{ request('category') == 'training' ? 'selected' : '' }}>Training</option>
                        <option value="other" {{ request('category') == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="archived" {{ request('status') == 'archived' ? 'selected' : '' }}>Archived</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="sort" class="form-label">Sort By</label>
                    <select class="form-select" id="sort" name="sort">
                        <option value="created_at" {{ request('sort') == 'created_at' ? 'selected' : '' }}>Date Created</option>
                        <option value="title" {{ request('sort') == 'title' ? 'selected' : '' }}>Title</option>
                        <option value="views" {{ request('sort') == 'views' ? 'selected' : '' }}>Views</option>
                        <option value="downloads" {{ request('sort') == 'downloads' ? 'selected' : '' }}>Downloads</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-outline-primary me-2">
                        🔍
                    </button>
                    <a href="{{ route('presentations.index') }}" class="btn btn-outline-secondary">
                        ❌
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- View Toggle and Stats -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="d-flex align-items-center">
            <span class="text-muted me-3">{{ $presentations->total() }} presentations found</span>
            @if(request()->hasAny(['search', 'category', 'status']))
                <span class="badge bg-info">Filtered</span>
            @endif
        </div>
        <div class="btn-group" role="group">
            <button type="button" class="btn btn-outline-secondary active" id="gridView">
                ⊞
            </button>
            <button type="button" class="btn btn-outline-secondary" id="listView">
                📋
            </button>
        </div>
    </div>

    <!-- Presentations Grid -->
    <div id="presentationsGrid">
        @if($presentations->count() > 0)
            <div class="row">
                @foreach($presentations as $presentation)
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                        <div class="card h-100 presentation-card" data-id="{{ $presentation->id }}">
                            <div class="position-relative">
                                <img src="{{ $presentation->thumbnail_url }}" class="card-img-top" 
                                     alt="{{ $presentation->title }}" style="height: 200px; object-fit: cover;">
                                <div class="position-absolute top-0 end-0 p-2">
                                    {!! $presentation->status_badge !!}
                                </div>
                                @if($presentation->is_template)
                                    <div class="position-absolute top-0 start-0 p-2">
                                        <span class="badge bg-warning">Template</span>
                                    </div>
                                @endif
                            </div>
                            <div class="card-body d-flex flex-column">
                                <h6 class="card-title">{{ Str::limit($presentation->title, 50) }}</h6>
                                <p class="card-text text-muted small flex-grow-1">
                                    {{ Str::limit($presentation->description, 80) }}
                                </p>
                                <div class="d-flex justify-content-between align-items-center text-muted small mb-2">
                                    <span>👁️ {{ $presentation->views_count }}</span>
                                    <span>📥 {{ $presentation->downloads_count }}</span>
                                    <span>{{ $presentation->formatted_file_size }}</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center text-muted small mb-3">
                                    <span>{{ $presentation->created_at->format('M d, Y') }}</span>
                                    <span class="badge bg-light text-dark">{{ ucfirst($presentation->category) }}</span>
                                </div>
                                <div class="d-flex gap-1">
                                    <a href="{{ route('presentations.show', $presentation) }}" 
                                       class="btn btn-sm btn-outline-primary flex-fill">
                                        👁️
                                    </a>
                                    <a href="{{ route('presentations.download', $presentation) }}" 
                                       class="btn btn-sm btn-outline-success">
                                        📥
                                    </a>
                                    @if($presentation->canBeEditedBy(auth()->user()))
                                        <a href="{{ route('presentations.edit', $presentation) }}" 
                                           class="btn btn-sm btn-outline-warning">
                                            ✏️
                                        </a>
                                    @endif
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" 
                                                type="button" data-bs-toggle="dropdown">
                                            ⋮
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="{{ route('presentations.duplicate', $presentation) }}">
                                                📋 Duplicate
                                            </a></li>
                                            <li><a class="dropdown-item" href="{{ route('presentations.analytics', $presentation) }}">
                                                📊 Analytics
                                            </a></li>
                                            @if($presentation->canBeEditedBy(auth()->user()))
                                                <li><hr class="dropdown-divider"></li>
                                                <li><a class="dropdown-item" href="#" onclick="toggleTemplate({{ $presentation->id }})">
                                                    ⭐ {{ $presentation->is_template ? 'Remove from Templates' : 'Make Template' }}
                                                </a></li>
                                                <li><a class="dropdown-item" href="#" onclick="toggleArchive({{ $presentation->id }})">
                                                    📦 {{ $presentation->status === 'archived' ? 'Restore' : 'Archive' }}
                                                </a></li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li><a class="dropdown-item text-danger" href="#" onclick="deletePresentation({{ $presentation->id }})">
                                                    🗑️ Delete
                                                </a></li>
                                            @endif
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-5">
                <span class="text-muted mb-3" style="font-size: 3rem;">📊</span>
                <h5 class="text-muted">No presentations found</h5>
                <p class="text-muted">Upload your first presentation to get started.</p>
                <a href="{{ route('presentations.create') }}" class="btn btn-primary">
                    ➕ Upload Presentation
                </a>
            </div>
        @endif
    </div>

    <!-- Presentations List (Hidden by default) -->
    <div id="presentationsList" style="display: none;">
        @if($presentations->count() > 0)
            <div class="card">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Title</th>
                                <th>Category</th>
                                <th>Status</th>
                                <th>Views</th>
                                <th>Downloads</th>
                                <th>Size</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($presentations as $presentation)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="{{ $presentation->thumbnail_url }}" 
                                                 class="rounded me-2" width="40" height="30" 
                                                 style="object-fit: cover;">
                                            <div>
                                                <div class="fw-bold">{{ Str::limit($presentation->title, 40) }}</div>
                                                <small class="text-muted">{{ Str::limit($presentation->description, 60) }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-light text-dark">{{ ucfirst($presentation->category) }}</span></td>
                                    <td>{!! $presentation->status_badge !!}</td>
                                    <td>{{ $presentation->views_count }}</td>
                                    <td>{{ $presentation->downloads_count }}</td>
                                    <td>{{ $presentation->formatted_file_size }}</td>
                                    <td>{{ $presentation->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <a href="{{ route('presentations.show', $presentation) }}" 
                                               class="btn btn-sm btn-outline-primary">
                                                👁️
                                            </a>
                                            <a href="{{ route('presentations.download', $presentation) }}" 
                                               class="btn btn-sm btn-outline-success">
                                                📥
                                            </a>
                                            @if($presentation->canBeEditedBy(auth()->user()))
                                                <a href="{{ route('presentations.edit', $presentation) }}" 
                                                   class="btn btn-sm btn-outline-warning">
                                                    ✏️
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>

    <!-- Pagination -->
    @if($presentations->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $presentations->appends(request()->query())->links('vendor.pagination.custom-3d') }}
        </div>
    @endif
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this presentation? This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this presentation? This action cannot be undone.')">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// View toggle functionality
document.getElementById('gridView').addEventListener('click', function() {
    document.getElementById('presentationsGrid').style.display = 'block';
    document.getElementById('presentationsList').style.display = 'none';
    this.classList.add('active');
    document.getElementById('listView').classList.remove('active');
});

document.getElementById('listView').addEventListener('click', function() {
    document.getElementById('presentationsGrid').style.display = 'none';
    document.getElementById('presentationsList').style.display = 'block';
    this.classList.add('active');
    document.getElementById('gridView').classList.remove('active');
});

// Delete presentation
function deletePresentation(id) {
    const form = document.getElementById('deleteForm');
    form.action = `/presentations/${id}`;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
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
            ScrollPreserver.preserveAndReload();
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
            ScrollPreserver.preserveAndReload();
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