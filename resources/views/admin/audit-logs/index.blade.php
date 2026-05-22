@extends('layouts.admin')

@section('title', 'Audit Logs')

@section('content')
<div class="mb-4">
    <h1>Audit Logs</h1>
</div>

<!-- Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-5">
                <select name="action" class="form-select">
                    <option value="">-- All Actions --</option>
                    @foreach($actions as $action)
                        <option value="{{ $action }}" @if(request('action') == $action) selected @endif>
                            {{ $action }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-5">
                <select name="entity_type" class="form-select">
                    <option value="">-- All Entity Types --</option>
                    @foreach($entityTypes as $type)
                        <option value="{{ $type }}" @if(request('entity_type') == $type) selected @endif>
                            {{ $type }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search"></i> Filter
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Audit Logs Table -->
<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0 table-sm">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>User</th>
                    <th>Action</th>
                    <th>Entity Type</th>
                    <th>Entity ID</th>
                    <th>Metadata</th>
                    <th>Timestamp</th>
                </tr>
            </thead>
            <tbody>
                @forelse($auditLogs as $log)
                    <tr>
                        <td><strong>#{{ $log->id }}</strong></td>
                        <td>{{ $log->user?->name ?? 'System' }}</td>
                        <td>
                            <span class="badge bg-primary">{{ $log->action }}</span>
                        </td>
                        <td>{{ $log->entity_type }}</td>
                        <td>#{{ $log->entity_id }}</td>
                        <td>
                            @if($log->meta)
                                <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#modal{{ $log->id }}">
                                    <i class="bi bi-eye"></i> View
                                </button>
                                
                                <!-- Modal -->
                                <div class="modal fade" id="modal{{ $log->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Metadata</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <pre>{{ json_encode($log->meta, JSON_PRETTY_PRINT) }}</pre>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>{{ $log->created_at->format('M d, Y H:i:s') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">No audit logs found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Pagination -->
<div class="d-flex justify-content-center mt-4">
    {{ $auditLogs->links() }}
</div>
@endsection
