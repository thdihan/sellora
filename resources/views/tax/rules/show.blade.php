@extends('layouts.app')

@section('title', 'Tax Rule Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">Tax Rule Details</h3>
                    <div>
                        <a href="{{ route('rules.edit', $taxRule) }}" class="btn btn-primary">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <a href="{{ route('tax.rules.index', ['tax' => request()->route('tax')]) }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Tax Rules
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Basic Information</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Name:</strong></td>
                                    <td>{{ $taxRule->name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Priority:</strong></td>
                                    <td>{{ $taxRule->priority }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Status:</strong></td>
                                    <td>
                                        @if($taxRule->is_active)
                                            <span class="badge badge-success">Active</span>
                                        @else
                                            <span class="badge badge-secondary">Inactive</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Created:</strong></td>
                                    <td>{{ $taxRule->created_at->format('M d, Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Updated:</strong></td>
                                    <td>{{ $taxRule->updated_at->format('M d, Y H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                        
                        <div class="col-md-6">
                            <h5>Tax Rate Information</h5>
                            @if($taxRule->taxRate)
                                <table class="table table-borderless">
                                    <tr>
                                        <td><strong>Tax Rate Name:</strong></td>
                                        <td>{{ $taxRule->taxRate->name }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Rate:</strong></td>
                                        <td>{{ $taxRule->taxRate->rate }}%</td>
                                    </tr>
                                    @if($taxRule->taxRate->taxCode)
                                        <tr>
                                            <td><strong>Tax Code:</strong></td>
                                            <td>{{ $taxRule->taxRate->taxCode->name }}</td>
                                        </tr>
                                    @endif
                                </table>
                            @else
                                <p class="text-muted">No tax rate assigned to this rule.</p>
                            @endif
                        </div>
                    </div>
                    
                    @if($taxRule->description)
                        <div class="row mt-4">
                            <div class="col-12">
                                <h5>Description</h5>
                                <p class="text-muted">{{ $taxRule->description }}</p>
                            </div>
                        </div>
                    @endif
                    
                    <div class="row mt-4">
                        <div class="col-12">
                            <h5>Rule Conditions</h5>
                            @if($taxRule->conditions)
                                @php
                                    $conditions = json_decode($taxRule->conditions, true);
                                @endphp
                                @if(is_array($conditions) && count($conditions) > 0)
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Field</th>
                                                    <th>Operator</th>
                                                    <th>Value</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($conditions as $condition)
                                                    <tr>
                                                        <td>
                                                            <span class="badge badge-info">
                                                                {{ ucwords(str_replace('_', ' ', $condition['field'] ?? 'Unknown')) }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <span class="badge badge-secondary">
                                                                {{ ucwords(str_replace('_', ' ', $condition['operator'] ?? 'Unknown')) }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <code>{{ $condition['value'] ?? 'No value' }}</code>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <p class="text-muted">No conditions defined for this rule.</p>
                                @endif
                            @else
                                <p class="text-muted">No conditions defined for this rule.</p>
                            @endif
                        </div>
                    </div>
                    
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="btn-group" role="group">
                                <a href="{{ route('rules.edit', $taxRule) }}" class="btn btn-primary">
                                    <i class="fas fa-edit"></i> Edit Rule
                                </a>
                                <form action="{{ route('rules.destroy', $taxRule) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this tax rule?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">
                                        <i class="fas fa-trash"></i> Delete Rule
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection