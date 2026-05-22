@extends('layouts.admin')

@section('title', 'Review Document')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.documents.index') }}" class="btn btn-outline-secondary mb-3">
        <i class="bi bi-arrow-left"></i> Back to Documents
    </a>
    <h1>Document Review</h1>
</div>

<div class="row">
    <div class="col-md-8">
        <!-- Document Info -->
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0">Document Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p class="text-muted mb-1">Document Type</p>
                        <p class="h6 mb-3">{{ $document->document_type }}</p>
                    </div>
                    <div class="col-md-6">
                        <p class="text-muted mb-1">Current Status</p>
                        <p class="mb-3">
                            <span class="badge badge-{{ str_replace(' ', '-', $document->status) }} fs-6">
                                {{ ucfirst($document->status) }}
                            </span>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <p class="text-muted mb-1">Application</p>
                        <p class="h6 mb-3">
                            <a href="{{ route('admin.applications.show', $document->application) }}" class="text-decoration-none">
                                #{{ $document->application_id }}
                            </a>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <p class="text-muted mb-1">Customer</p>
                        <p class="h6 mb-3">{{ $document->application->customer->full_name }}</p>
                    </div>
                    <div class="col-md-6">
                        <p class="text-muted mb-1">Uploaded Date</p>
                        <p class="h6 mb-3">{{ $document->created_at->format('M d, Y H:i A') }}</p>
                    </div>
                    @if($document->reviewed_at)
                        <div class="col-md-6">
                            <p class="text-muted mb-1">Reviewed Date</p>
                            <p class="h6 mb-3">{{ $document->reviewed_at->format('M d, Y H:i A') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- File Preview -->
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0">File Preview</h5>
            </div>
            <div class="card-body text-center">
                <div class="mb-3">
                    <i class="bi bi-file-pdf" style="font-size: 4rem; color: #dc3545;"></i>
                </div>
                <p class="text-muted mb-2">
                    <strong>{{ basename($document->file_path) }}</strong>
                </p>
                <p class="text-muted small mb-3">Document is stored in system</p>
                <p class="mb-0">
                    <a href="{{ asset('storage/' . $document->file_path) }}" target="_blank" class="btn btn-sm btn-primary">
                        <i class="bi bi-download"></i> Download File
                    </a>
                </p>
            </div>
        </div>

        <!-- Review Form -->
        <div class="card">
            <div class="card-header bg-light">
                <h5 class="mb-0">Approve or Reject Document</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <form method="POST" action="{{ route('admin.documents.approve', $document) }}">
                            @csrf
                            <div class="mb-3">
                                <label for="review_note" class="form-label">Review Notes (Optional)</label>
                                <textarea class="form-control" id="review_note" name="review_note" rows="4" placeholder="Add any notes about this document..."></textarea>
                            </div>
                            <button type="submit" class="btn btn-success w-100">
                                <i class="bi bi-check-circle"></i> Approve Document
                            </button>
                        </form>
                    </div>
                    <div class="col-md-6 mb-3">
                        <form method="POST" action="{{ route('admin.documents.reject', $document) }}">
                            @csrf
                            <div class="mb-3">
                                <label for="review_note" class="form-label">Rejection Reason *</label>
                                <textarea class="form-control" id="review_note" name="review_note" rows="4" placeholder="Explain why this document is rejected..." required></textarea>
                            </div>
                            <button type="submit" class="btn btn-danger w-100">
                                <i class="bi bi-x-circle"></i> Reject Document
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        @if($document->review_note)
            <div class="card mt-3">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Previous Review Note</h5>
                </div>
                <div class="card-body">
                    <p class="mb-0">{{ $document->review_note }}</p>
                </div>
            </div>
        @endif
    </div>

    <!-- Info Sidebar -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-light">
                <h5 class="mb-0">Checklist</h5>
            </div>
            <div class="card-body">
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" id="check1" checked disabled>
                    <label class="form-check-label" for="check1">
                        File is readable
                    </label>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" id="check2">
                    <label class="form-check-label" for="check2">
                        Document is clear
                    </label>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" id="check3">
                    <label class="form-check-label" for="check3">
                        Document is valid
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="check4">
                    <label class="form-check-label" for="check4">
                        Ready to approve
                    </label>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
