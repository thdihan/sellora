@extends('layouts.app')

@section('title', 'Tax Rules')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">Tax Rules</h3>
                    <a href="{{ route('tax.rules.create', ['tax' => request()->route('tax')]) }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add Tax Rule
                    </a>
                </div>
                <div class="card-body">
                    @if($taxRules->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Priority</th>
                                        <th>Name</th>
                                        <th>Tax Rate</th>
                                        <th>Conditions</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($taxRules as $rule)
                                        <tr>
                                            <td>{{ $rule->priority }}</td>
                                            <td>{{ $rule->name }}</td>
                                            <td>
                                                @if($rule->taxRate)
                                                    {{ $rule->taxRate->name }} ({{ $rule->taxRate->rate }}%)
                                                @else
                                                    <span class="text-muted">No rate assigned</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($rule->conditions)
                                                    @php
                                                        $conditions = json_decode($rule->conditions, true);
                                                    @endphp
                                                    @if(is_array($conditions) && count($conditions) > 0)
                                                        <small class="text-muted">
                                                            {{ count($conditions) }} condition(s)
                                                        </small>
                                                    @else
                                                        <span class="text-muted">No conditions</span>
                                                    @endif
                                                @else
                                                    <span class="text-muted">No conditions</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($rule->is_active)
                                                    <span class="badge badge-success">Active</span>
                                                @else
                                                    <span class="badge badge-secondary">Inactive</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('rules.show', $rule) }}" class="btn btn-sm btn-outline-info" title="View">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('rules.edit', $rule) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form action="{{ route('rules.destroy', $rule) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this tax rule?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="d-flex justify-content-center">
                            {{ $taxRules->links() }}
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-calculator fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No Tax Rules Found</h5>
                            <p class="text-muted">Create your first tax rule to get started.</p>
                            <a href="{{ route('tax.rules.create', ['tax' => request()->route('tax')]) }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Add Tax Rule
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection