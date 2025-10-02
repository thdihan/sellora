@extends('layouts.app')

@section('title', 'Customer Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Customer Management</h5>
                    <a href="{{ route('customers.create') }}" class="btn btn-primary">
                        Add Customer
                    </a>
                </div>
                <div class="card-body">
                    <!-- Search Form -->
                    <form method="GET" action="{{ route('customers.index') }}" class="mb-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <input type="text" name="search" class="form-control" 
                                       placeholder="Search customers by name, shop, or phone..." value="{{ request('search') }}">
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-outline-primary w-100">
                                    <i class="fas fa-search"></i> Search
                                </button>
                            </div>
                            <div class="col-md-2">
                                <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary w-100">
                                    <i class="fas fa-times"></i> Clear
                                </a>
                            </div>
                        </div>
                    </form>

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <!-- Customers Table -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Shop Name</th>
                                    <th>Phone</th>
                                    <th>Email</th>
                                    <th>Outstanding Due</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($customers as $customer)
                                    <tr>
                                        <td>{{ $customer->id }}</td>
                                        <td>
                                            <strong>{{ $customer->name }}</strong>
                                        </td>
                                        <td>{{ $customer->shop_name ?? '-' }}</td>
                                        <td>
                                            <a href="tel:{{ $customer->phone }}" class="text-decoration-none">
                                                {{ $customer->phone }}
                                            </a>
                                        </td>
                                        <td>
                                            @if($customer->email)
                                                <a href="mailto:{{ $customer->email }}" class="text-decoration-none">
                                                    {{ $customer->email }}
                                                </a>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                $due = $customer->outstanding_due;
                                            @endphp
                                            @if($due > 0)
                                                <span class="badge bg-warning text-dark">
                                                    {{ formatBTD($due) }}
                                                </span>
                                            @else
                                                <span class="badge bg-success">Paid</span>
                                            @endif
                                        </td>
                                        <td>{{ $customer->created_at->format('M d, Y') }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('customers.show', $customer) }}" 
                                                   class="btn btn-sm btn-outline-primary" title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('customers.edit', $customer) }}" 
                                                   class="btn btn-sm btn-outline-secondary" title="Edit">
                                                    Edit
                                                </a>
                                                <form action="{{ route('customers.destroy', $customer) }}" 
                                                      method="POST" class="d-inline delete-form">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                        Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="fas fa-users fa-3x mb-3"></i>
                                                <p class="mb-0">No customers found.</p>
                                                @if(request('search'))
                                                    <p class="small">Try adjusting your search criteria.</p>
                                                @else
                                                    <p class="small">
                                                        <a href="{{ route('customers.create') }}" class="text-decoration-none">
                                                            Add your first customer
                                                        </a>
                                                    </p>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($customers->hasPages())
                        <div class="d-flex justify-content-center mt-4">
                            {{ $customers->appends(request()->query())->links('vendor.pagination.custom-3d') }}
                        </div>
                    @endif

                    <!-- Summary -->
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h6 class="card-title">Total Customers</h6>
                                    <h4 class="text-primary">{{ $customers->total() }}</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h6 class="card-title">Showing</h6>
                                    <h4 class="text-info">
                                        {{ $customers->firstItem() ?? 0 }} - {{ $customers->lastItem() ?? 0 }}
                                    </h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle delete confirmation for customers
    const deleteForms = document.querySelectorAll('.delete-form');
    
    deleteForms.forEach(function(form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (confirm('Are you sure you want to delete this customer? This action cannot be undone.')) {
                // Use AJAX to delete without page reload
                const formData = new FormData(form);
                
                fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => {
                    if (response.ok) {
                        // Remove the customer row from the table
                        const row = form.closest('tr');
                        row.remove();
                        
                        // Show success message
                        const alertDiv = document.createElement('div');
                        alertDiv.className = 'alert alert-success alert-dismissible fade show';
                        alertDiv.innerHTML = `
                            Customer deleted successfully.
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        `;
                        document.querySelector('.container-fluid').insertBefore(alertDiv, document.querySelector('.card'));
                        
                        // Auto-hide alert after 5 seconds
                        setTimeout(() => {
                            alertDiv.remove();
                        }, 5000);
                    } else {
                        alert('Error deleting customer. Please try again.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error deleting customer. Please try again.');
                });
            }
        });
    });
});
</script>
@endsection