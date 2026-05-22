@extends('layouts.admin')

@section('title', 'Videos')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0"><i class="bi bi-play-circle me-2"></i>Videos</h1>
    <a href="{{ route('admin.settings.videos.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-1"></i> Add Video
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
        @if($videos->isEmpty())
            <div class="text-center py-5 text-muted">
                <i class="bi bi-camera-video" style="font-size:3rem;"></i>
                <p class="mt-2">No videos yet. Add your first video.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Title</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Shared With</th>
                            <th>Added By</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($videos as $video)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <strong>{{ $video->title }}</strong>
                                @if($video->description)
                                    <div class="small text-muted">{{ Str::limit($video->description, 60) }}</div>
                                @endif
                            </td>
                            <td>
                                @if($video->video_type === 'youtube')
                                    <span class="badge bg-danger"><i class="bi bi-youtube me-1"></i>YouTube</span>
                                @elseif($video->video_type === 'upload')
                                    <span class="badge bg-primary"><i class="bi bi-upload me-1"></i>Uploaded</span>
                                @else
                                    <span class="badge bg-secondary"><i class="bi bi-link-45deg me-1"></i>URL</span>
                                @endif
                            </td>
                            <td>
                                @if($video->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-info text-dark">{{ $video->shares->count() }} customer(s)</span>
                            </td>
                            <td class="small">{{ $video->creator->name ?? '—' }}</td>
                            <td class="small">{{ $video->created_at->format('d M Y') }}</td>
                            <td>
                                <div class="d-flex gap-1 flex-wrap">
                                    <button class="btn btn-sm btn-outline-success"
                                        data-bs-toggle="modal"
                                        data-bs-target="#shareModal{{ $video->id }}">
                                        <i class="bi bi-share"></i> Share
                                    </button>
                                    <a href="{{ route('admin.settings.videos.edit', $video) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form method="POST" action="{{ route('admin.settings.videos.destroy', $video) }}"
                                        onsubmit="return confirm('Delete this video?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-3 py-2">{{ $videos->links() }}</div>
        @endif
    </div>
</div>

{{-- Share Modals --}}
@foreach($videos as $video)
@php $customers = \App\Models\Customer::orderBy('full_name')->get(); @endphp
<div class="modal fade" id="shareModal{{ $video->id }}" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form method="POST" action="{{ route('admin.settings.videos.share', $video) }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-share me-2"></i>Share: {{ $video->title }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Select Customers <span class="text-danger">*</span></label>
                        <select name="customer_ids[]" class="form-select" multiple size="8" required>
                            @foreach($customers as $c)
                                <option value="{{ $c->id }}">{{ $c->full_name }} ({{ $c->phone }})</option>
                            @endforeach
                        </select>
                        <div class="form-text">Hold Ctrl / Cmd to select multiple customers.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Note (optional)</label>
                        <textarea name="note" class="form-control" rows="2" placeholder="Add a note for the customer…"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success"><i class="bi bi-share me-1"></i>Share</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endforeach
@endsection
