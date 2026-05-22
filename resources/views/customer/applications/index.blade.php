@extends('layouts.app')

@section('title', 'My Applications')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>My Applications</h2>
        <a href="{{ route('customer.applications.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> New Application
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-circle"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Product</th>
                        <th>Agent</th>
                        <th>Status</th>
                        <th>Documents</th>
                        <th>Submitted</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($applications as $application)
                        <tr>
                            <td><strong>#{{ $application->id }}</strong></td>
                            <td>{{ $application->product->name }}</td>
                            <td>{{ $application->agent->name }}</td>
                            <td>
                                @php
                                    $statusColor = match($application->status) {
                                        'draft' => 'secondary',
                                        'submitted' => 'info',
                                        'verified' => 'success',
                                        'converted' => 'primary',
                                        default => 'secondary'
                                    };
                                @endphp
                                <span class="badge bg-{{ $statusColor }}">
                                    {{ ucfirst($application->status) }}
                                </span>
                            </td>
                            <td>
                                @if($application->documents->count() > 0)
                                    <span class="badge bg-info">{{ $application->documents->count() }}</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                @if($application->submitted_at)
                                    {{ $application->submitted_at->format('M d, Y') }}
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('customer.applications.show', $application) }}" class="btn btn-outline-info" title="View">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @if($application->status === 'draft')
                                        <a href="{{ route('customer.applications.edit', $application) }}" class="btn btn-outline-primary" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <p class="text-muted mb-3">No applications yet</p>
                                <a href="{{ route('customer.applications.create') }}" class="btn btn-sm btn-primary">
                                    <i class="bi bi-plus-circle"></i> Create Your First Application
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($applications->hasPages())
        <div class="mt-4">
            {{ $applications->links() }}
        </div>
    @endif
</div>
@endsection
