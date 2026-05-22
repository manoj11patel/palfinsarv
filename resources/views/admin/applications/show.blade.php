@extends('layouts.admin')

@section('title', 'Application Details')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.applications.index') }}" class="btn btn-outline-secondary mb-3">
        <i class="bi bi-arrow-left"></i> Back to Applications
    </a>
    <h1>Application #{{ $application->id }}</h1>
</div>

<div class="row">
    <div class="col-md-8">
        <!-- Customer Info -->
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0">Customer Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p class="text-muted mb-1">Full Name</p>
                        <p class="h6 mb-3">{{ $application->customer->full_name }}</p>
                    </div>
                    <div class="col-md-6">
                        <p class="text-muted mb-1">Email</p>
                        <p class="h6 mb-3">{{ $application->customer->email }}</p>
                    </div>
                    <div class="col-md-6">
                        <p class="text-muted mb-1">Phone</p>
                        <p class="h6 mb-3">{{ $application->customer->phone }}</p>
                    </div>
                    <div class="col-md-6">
                        <p class="text-muted mb-1">Customer Status</p>
                        <p class="mb-3">
                            <span class="badge badge-{{ $application->customer->status }}">
                                {{ ucfirst($application->customer->status) }}
                            </span>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Application Details -->
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0">Application Details</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p class="text-muted mb-1">Product</p>
                        <p class="h6 mb-3">{{ $application->product->name }}</p>
                    </div>
                    <div class="col-md-6">
                        <p class="text-muted mb-1">Agent</p>
                        <p class="h6 mb-3">
                            {{ $application->agent?->name ?? '—' }}
                            @if($application->agent && $application->agent->role === 'admin')
                                <span class="badge bg-secondary ms-1">Admin</span>
                            @endif
                        </p>
                    </div>
                    <div class="col-md-6">
                        <p class="text-muted mb-1">Current Status</p>
                        <p class="mb-3">
                            <span class="badge badge-{{ $application->status }} fs-6">
                                {{ ucfirst($application->status) }}
                            </span>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <p class="text-muted mb-1">Created Date</p>
                        <p class="h6 mb-3">{{ $application->created_at->format('M d, Y H:i A') }}</p>
                    </div>
                    @if($application->submitted_at)
                        <div class="col-md-6">
                            <p class="text-muted mb-1">Submitted Date</p>
                            <p class="h6 mb-3">{{ $application->submitted_at->format('M d, Y H:i A') }}</p>
                        </div>
                    @endif
                    @if($application->verified_at)
                        <div class="col-md-6">
                            <p class="text-muted mb-1">Verified Date</p>
                            <p class="h6 mb-3">{{ $application->verified_at->format('M d, Y H:i A') }}</p>
                        </div>
                    @endif
                    @if($application->converted_at)
                        <div class="col-md-6">
                            <p class="text-muted mb-1">Converted Date</p>
                            <p class="h6 mb-3">{{ $application->converted_at->format('M d, Y H:i A') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Profile Payload -->
        @if($application->profile_payload)
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Profile Data</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            @php
                                $payload = $application->profile_payload;
                                if (is_string($payload)) {
                                    $payload = json_decode($payload, true);
                                }
                            @endphp
                            @if(is_array($payload) && count($payload) > 0)
                                @foreach($payload as $key => $value)
                                    <tr>
                                        <td><strong>{{ ucfirst(str_replace('_', ' ', $key)) }}</strong></td>
                                        <td>{{ is_array($value) ? json_encode($value) : $value }}</td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="2" class="text-muted text-center">No profile data available</td>
                                </tr>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
        @endif

        <!-- Documents -->
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0">Attached Documents ({{ $application->documents->count() }})</h5>
            </div>
            <div class="card-body">
                @if($application->documents->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th>Type</th>
                                    <th>Status</th>
                                    <th>Reviewed</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($application->documents as $doc)
                                    <tr>
                                        <td>{{ $doc->document_type }}</td>
                                        <td>
                                            <span class="badge badge-{{ str_replace(' ', '-', $doc->status) }}">
                                                {{ ucfirst($doc->status) }}
                                            </span>
                                        </td>
                                        <td>{{ $doc->reviewed_by ? 'Yes' : 'No' }}</td>
                                        <td>
                                            <a href="{{ route('admin.documents.review', $doc) }}" class="btn btn-sm btn-outline-warning">
                                                <i class="bi bi-pencil"></i> Review
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted mb-0">No documents attached</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Actions Sidebar -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-light">
                <h5 class="mb-0">Actions</h5>
            </div>
            <div class="card-body">
                @if($application->status === 'submitted')
                    <form method="POST" action="{{ route('admin.applications.verify', $application) }}" class="mb-2">
                        @csrf
                        <button type="submit" class="btn btn-success w-100 mb-2">
                            <i class="bi bi-check-circle"></i> Verify Application
                        </button>
                    </form>
                @endif

                @if($application->status === 'verified')
                    <form method="POST" action="{{ route('admin.applications.convert', $application) }}" class="mb-2">
                        @csrf
                        <button type="submit" class="btn btn-primary w-100 mb-2">
                            <i class="bi bi-arrow-right-circle"></i> Convert to Customer
                        </button>
                    </form>
                @endif

                @if($application->status === 'converted')
                    <div class="alert alert-success" role="alert">
                        <i class="bi bi-check-circle"></i> This application has been successfully converted.
                    </div>
                @endif

                <div class="alert alert-info" role="alert">
                    <p class="mb-1"><strong>Status Flow:</strong></p>
                    <small>Draft → Submitted → Verified → Converted</small>
                </div>
            </div>
        </div>

        <!-- Timeline -->
        <div class="card mt-3">
            <div class="card-header bg-light">
                <h5 class="mb-0">Timeline</h5>
            </div>
            <div class="card-body">
                <div class="timeline">
                    <div class="mb-3">
                        <p class="mb-1"><strong>Created</strong></p>
                        <p class="text-muted mb-0">{{ $application->created_at->format('M d, Y H:i A') }}</p>
                    </div>
                    @if($application->submitted_at)
                        <div class="mb-3">
                            <p class="mb-1"><strong>Submitted</strong></p>
                            <p class="text-muted mb-0">{{ $application->submitted_at->format('M d, Y H:i A') }}</p>
                        </div>
                    @endif
                    @if($application->verified_at)
                        <div class="mb-3">
                            <p class="mb-1"><strong>Verified</strong></p>
                            <p class="text-muted mb-0">{{ $application->verified_at->format('M d, Y H:i A') }}</p>
                        </div>
                    @endif
                    @if($application->converted_at)
                        <div class="mb-3">
                            <p class="mb-1"><strong>Converted</strong></p>
                            <p class="text-muted mb-0">{{ $application->converted_at->format('M d, Y H:i A') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
