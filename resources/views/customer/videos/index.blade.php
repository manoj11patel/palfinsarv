@extends('customer.layout')

@section('title', 'Videos')

@section('content')
<div class="mb-4">
    <h1 class="h3"><i class="bi bi-play-circle me-2"></i>Videos</h1>
    <p class="text-muted">Videos shared with you by Pal Finsarv.</p>
</div>

@if($shares->isEmpty())
    <div class="card">
        <div class="card-body text-center py-5 text-muted">
            <i class="bi bi-camera-video" style="font-size:3rem;"></i>
            <p class="mt-2">No videos have been shared with you yet.</p>
        </div>
    </div>
@else
    <div class="row g-4">
        @foreach($shares as $share)
        @php $video = $share->video; @endphp
        <div class="col-md-6 col-lg-4">
            <div class="card h-100">
                @if($video->video_type === 'youtube' && $video->embed_url)
                    <div class="ratio ratio-16x9">
                        <iframe src="{{ $video->embed_url }}" allowfullscreen class="rounded-top"></iframe>
                    </div>
                @elseif($video->thumbnail_path)
                    <img src="{{ Storage::url($video->thumbnail_path) }}" class="card-img-top" style="height:160px;object-fit:cover;" alt="">
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

                    @if($share->note)
                        <div class="alert alert-info py-1 px-2 small mb-2">
                            <i class="bi bi-chat-left-text me-1"></i>{{ $share->note }}
                        </div>
                    @endif

                    <div class="mt-auto pt-2 d-flex justify-content-between align-items-center">
                        @if($video->video_type === 'youtube' && $video->video_url)
                            <a href="{{ $video->video_url }}" target="_blank" class="btn btn-sm btn-danger">
                                <i class="bi bi-youtube me-1"></i>Watch on YouTube
                            </a>
                        @elseif($video->video_type === 'url' && $video->video_url)
                            <a href="{{ $video->video_url }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-box-arrow-up-right me-1"></i>Watch
                            </a>
                        @elseif($video->video_type === 'upload' && $video->video_path)
                            <a href="{{ Storage::url($video->video_path) }}" target="_blank" class="btn btn-sm btn-primary">
                                <i class="bi bi-play-fill me-1"></i>Watch
                            </a>
                        @endif
                        <span class="small text-muted">{{ $share->created_at->format('d M Y') }}</span>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
@endif
@endsection
