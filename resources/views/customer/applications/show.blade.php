@extends('layouts.app')

@section('title', 'Application Details')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Application Details</h2>
        <a href="{{ route('customer.applications.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>

    <!-- Status Bar -->
    <div class="card mb-4 bg-light">
        <div class="card-body d-flex justify-content-between align-items-center">
            <div>
                <strong>Application Status:</strong>
                <span class="badge bg-{{ $application->status === 'draft' ? 'secondary' : ($application->status === 'submitted' ? 'info' : ($application->status === 'verified' ? 'success' : 'primary')) }}">
                    {{ ucfirst($application->status) }}
                </span>
            </div>
            <div class="text-muted">
                <small>Submitted: {{ $application->created_at->format('M d, Y H:i') }}</small>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Application Details -->
        <div class="col-lg-8">
            <!-- Personal Information -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-person"></i> Personal Information</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p class="text-muted small mb-1">Full Name</p>
                            <p class="mb-0"><strong>{{ $application->full_name }}</strong></p>
                        </div>
                        <div class="col-md-6">
                            <p class="text-muted small mb-1">Email</p>
                            <p class="mb-0"><strong>{{ $application->email }}</strong></p>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p class="text-muted small mb-1">Phone</p>
                            <p class="mb-0"><strong>{{ $application->phone }}</strong></p>
                        </div>
                        <div class="col-md-6">
                            <p class="text-muted small mb-1">Date of Birth</p>
                            <p class="mb-0"><strong>{{ $application->date_of_birth }}</strong></p>
                        </div>
                    </div>
                    <div class="mb-3">
                        <p class="text-muted small mb-1">Address</p>
                        <p class="mb-0"><strong>{{ $application->address }}</strong></p>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p class="text-muted small mb-1">Identification Type</p>
                            <p class="mb-0"><strong>{{ ucfirst(str_replace('_', ' ', $application->identification_type)) }}</strong></p>
                        </div>
                        <div class="col-md-6">
                            <p class="text-muted small mb-1">Identification Number</p>
                            <p class="mb-0"><strong>{{ $application->identification_number }}</strong></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Documents Section -->
            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="bi bi-file-earmark-pdf"></i> Documents</h5>
                </div>
                <div class="card-body">
                    @forelse($application->documents as $document)
                        <div class="d-flex justify-content-between align-items-center p-3 border-bottom">
                            <div class="flex-grow-1">
                                <p class="mb-1"><strong>{{ ucfirst(str_replace('_', ' ', $document->document_type)) }}</strong></p>
                                <small class="text-muted">Uploaded: {{ $document->created_at->format('M d, Y H:i') }}</small>
                                @if($document->status === 'rejected')
                                    <p class="mb-0 mt-2">
                                        <span class="badge bg-danger">Rejected</span>
                                        <small class="text-muted">{{ $document->rejection_reason }}</small>
                                    </p>
                                @endif
                            </div>
                            <div>
                                @if($document->status === 'uploaded')
                                    <span class="badge bg-warning text-dark">Pending Review</span>
                                @elseif($document->status === 'approved')
                                    <span class="badge bg-success">Approved</span>
                                @elseif($document->status === 'rejected')
                                    <span class="badge bg-danger">Rejected</span>
                                @else
                                    <span class="badge bg-secondary">{{ ucfirst($document->status) }}</span>
                                @endif
                            </div>
                        </div>

                        <!-- Resubmit for Rejected Documents -->
                        @if($document->status === 'rejected' && $application->status !== 'converted')
                            <div class="p-3 bg-light">
                                <form action="{{ route('customer.documents.resubmit', [$application, $document]) }}" method="POST" enctype="multipart/form-data" class="d-flex gap-2">
                                    @csrf
                                    <div class="flex-grow-1">
                                        <input type="file" name="file" class="form-control form-control-sm" accept=".pdf,.jpg,.jpeg,.png" required>
                                    </div>
                                    <button type="submit" class="btn btn-sm btn-primary">
                                        <i class="bi bi-upload"></i> Resubmit
                                    </button>
                                </form>
                            </div>
                        @endif
                    @empty
                        <p class="text-center text-muted py-4">No documents uploaded yet</p>
                    @endforelse
                </div>
            </div>

            <!-- Application Actions -->
            @if($application->status === 'draft')
                <div class="card mb-4 bg-light">
                    <div class="card-body d-flex gap-2">
                        <form action="{{ route('customer.applications.submit', $application) }}" method="POST" class="flex-grow-1">
                            @csrf
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-send"></i> Submit Application
                            </button>
                        </form>
                        <a href="{{ route('customer.applications.edit', $application) }}" class="btn btn-outline-secondary">
                            <i class="bi bi-pencil"></i> Edit
                        </a>
                    </div>
                </div>
            @endif
        </div>

        <!-- Side Panel -->
        <div class="col-lg-4">
            <!-- Agent Information -->
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="bi bi-person-badge"></i> Assigned Agent</h5>
                </div>
                <div class="card-body">
                    <p class="mb-1"><strong>{{ $application->agent->name }}</strong></p>
                    <p class="mb-1 small text-muted">{{ $application->agent->email }}</p>
                    <p class="mb-0 small text-muted">{{ $application->agent->agentProfile->phone }}</p>
                </div>
            </div>

            <!-- Product Information -->
            <div class="card mb-4">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="bi bi-box"></i> Product</h5>
                </div>
                <div class="card-body">
                    <p class="mb-0"><strong>{{ $application->product->name }}</strong></p>
                    @if($application->product->description)
                        <p class="mb-0 small text-muted mt-2">{{ $application->product->description }}</p>
                    @endif
                </div>
            </div>

            <!-- Application Timeline -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-clock-history"></i> Timeline</h5>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="timeline-item">
                            <div class="timeline-marker bg-success"></div>
                            <div>
                                <p class="mb-0 small"><strong>Created</strong></p>
                                <p class="mb-0 small text-muted">{{ $application->created_at->format('M d, Y H:i') }}</p>
                            </div>
                        </div>
                        @if($application->submitted_at)
                            <div class="timeline-item">
                                <div class="timeline-marker bg-info"></div>
                                <div>
                                    <p class="mb-0 small"><strong>Submitted</strong></p>
                                    <p class="mb-0 small text-muted">{{ $application->submitted_at->format('M d, Y H:i') }}</p>
                                </div>
                            </div>
                        @endif
                        @if($application->verified_at)
                            <div class="timeline-item">
                                <div class="timeline-marker bg-warning"></div>
                                <div>
                                    <p class="mb-0 small"><strong>Verified</strong></p>
                                    <p class="mb-0 small text-muted">{{ $application->verified_at->format('M d, Y H:i') }}</p>
                                </div>
                            </div>
                        @endif
                        @if($application->converted_at)
                            <div class="timeline-item">
                                <div class="timeline-marker bg-primary"></div>
                                <div>
                                    <p class="mb-0 small"><strong>Converted</strong></p>
                                    <p class="mb-0 small text-muted">{{ $application->converted_at->format('M d, Y H:i') }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
}
.timeline-item {
    display: flex;
    margin-bottom: 20px;
    padding-left: 10px;
}
.timeline-marker {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    margin-right: 15px;
    margin-top: 4px;
}
</style>
@endsection
