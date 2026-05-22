@extends('layouts.admin')

@section('title', 'Documents')

@section('content')
<div class="mb-4">
    <h1>Document Management</h1>
</div>

<!-- Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-8">
                <select name="status" class="form-select">
                    <option value="">All Statuses</option>
                    @foreach($statuses as $status)
                        <option value="{{ $status }}" @if(request('status') == $status) selected @endif>
                            {{ ucfirst($status) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search"></i> Filter
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Documents Table -->
<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Type</th>
                    <th>Application</th>
                    <th>Customer</th>
                    <th>Status</th>
                    <th>Uploaded</th>
                    <th>Reviewed</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($documents as $doc)
                    <tr>
                        <td><strong>#{{ $doc->id }}</strong></td>
                        <td>{{ $doc->document_type }}</td>
                        <td>
                            <a href="{{ route('admin.applications.show', $doc->application) }}" class="text-decoration-none">
                                #{{ $doc->application_id }}
                            </a>
                        </td>
                        <td>{{ $doc->application->customer->full_name }}</td>
                        <td>
                            <span class="badge badge-{{ str_replace(' ', '-', $doc->status) }}">
                                {{ ucfirst($doc->status) }}
                            </span>
                        </td>
                        <td>{{ $doc->created_at->format('M d, Y') }}</td>
                        <td>{{ $doc->reviewed_by ? 'Yes' : 'No' }}</td>
                        <td>
                            <a href="{{ route('admin.documents.review', $doc) }}" class="btn btn-sm btn-outline-warning">
                                <i class="bi bi-pencil"></i> Review
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">No documents found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Pagination -->
<div class="d-flex justify-content-center mt-4">
    {{ $documents->links() }}
</div>
@endsection
