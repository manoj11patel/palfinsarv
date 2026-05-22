@extends('agent.layout')

@section('title', 'Videos')

@section('content')
<div class="mb-4">
    <h1 class="h3"><i class="bi bi-play-circle me-2"></i>Videos</h1>
    <p class="text-muted">Videos shared by Pal Finsarv Admin. Share them with your customers.</p>
</div>

@if($videos->isEmpty())
    <div class="card">
        <div class="card-body text-center py-5 text-muted">
            <i class="bi bi-camera-video" style="font-size:3rem;"></i>
            <p class="mt-2">No videos available yet.</p>
        </div>
    </div>
@else
    <div class="row g-4">
        @foreach($videos as $video)
        <div class="col-md-6 col-lg-4">
            <div class="card h-100">
                @if($video->thumbnail_path)
                    <img src="{{ Storage::url($video->thumbnail_path) }}" class="card-img-top" style="height:160px;object-fit:cover;" alt="">
                @elseif($video->video_type === 'youtube' && $video->embed_url)
                    <div class="ratio ratio-16x9">
                        <iframe src="{{ $video->embed_url }}" allowfullscreen class="rounded-top"></iframe>
                    </div>
                @else
                    <div class="bg-light d-flex align-items-center justify-content-center rounded-top" style="height:160px;">
                        <i class="bi bi-play-circle text-muted" style="font-size:3rem;"></i>
                    </div>
                @endif

                <div class="card-body d-flex flex-column">
                    <h6 class="card-title">{{ $video->title }}</h6>
                    @if($video->description)
                        <p class="card-text small text-muted">{{ Str::limit($video->description, 80) }}</p>
                    @endif

                    <div class="mt-auto pt-2 d-flex gap-2">
                        @if($video->video_type === 'youtube' && $video->embed_url)
                            <a href="{{ $video->video_url }}" target="_blank" class="btn btn-sm btn-outline-danger">
                                <i class="bi bi-youtube me-1"></i>Watch
                            </a>
                        @elseif($video->video_type === 'url' && $video->video_url)
                            <a href="{{ $video->video_url }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-box-arrow-up-right me-1"></i>Watch
                            </a>
                        @elseif($video->video_type === 'upload' && $video->video_path)
                            <a href="{{ Storage::url($video->video_path) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-play-fill me-1"></i>Watch
                            </a>
                        @endif

                        @if($customers->isNotEmpty())
                            <button class="btn btn-sm btn-success" data-bs-toggle="modal"
                                data-bs-target="#shareModal{{ $video->id }}">
                                <i class="bi bi-share me-1"></i>Share
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
@endif

{{-- Share Modals --}}
@foreach($videos as $video)
<div class="modal fade" id="shareModal{{ $video->id }}" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('agent.videos.share', $video) }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-share me-2"></i>Share: {{ Str::limit($video->title, 40) }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Select Your Customers <span class="text-danger">*</span></label>
                        @if($customers->isEmpty())
                            <p class="text-muted small">You have no customers assigned yet.</p>
                        @else
                            <select name="customer_ids[]" class="form-select" multiple size="6" required>
                                @foreach($customers as $c)
                                    <option value="{{ $c->id }}">{{ $c->full_name }} ({{ $c->phone }})</option>
                                @endforeach
                            </select>
                            <div class="form-text">Hold Ctrl / Cmd to select multiple.</div>
                        @endif
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Note (optional)</label>
                        <textarea name="note" class="form-control" rows="2" placeholder="Add a message for your customer…"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success" @if($customers->isEmpty()) disabled @endif>
                        <i class="bi bi-share me-1"></i>Share
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endforeach
@endsection
