@extends('layouts.admin')

@section('title', 'Edit Video')

@section('content')
<div class="mb-4">
    <h1 class="h3"><i class="bi bi-pencil-square me-2"></i>Edit Video</h1>
</div>

<div class="row">
    <div class="col-lg-8">
        <form method="POST" action="{{ route('admin.settings.videos.update', $video) }}" enctype="multipart/form-data">
            @csrf @method('PUT')
            <div class="card mb-4">
                <div class="card-header bg-light"><h5 class="mb-0">Video Details</h5></div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                            value="{{ old('title', $video->title) }}" required>
                        @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control @error('description') is-invalid @enderror"
                            rows="3">{{ old('description', $video->description) }}</textarea>
                        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Video Type <span class="text-danger">*</span></label>
                        <div class="d-flex gap-4 mt-1">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="video_type" id="type_youtube"
                                    value="youtube" {{ old('video_type', $video->video_type) === 'youtube' ? 'checked' : '' }}>
                                <label class="form-check-label" for="type_youtube">
                                    <i class="bi bi-youtube text-danger me-1"></i>YouTube Link
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="video_type" id="type_url"
                                    value="url" {{ old('video_type', $video->video_type) === 'url' ? 'checked' : '' }}>
                                <label class="form-check-label" for="type_url">
                                    <i class="bi bi-link-45deg me-1"></i>External URL
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="video_type" id="type_upload"
                                    value="upload" {{ old('video_type', $video->video_type) === 'upload' ? 'checked' : '' }}>
                                <label class="form-check-label" for="type_upload">
                                    <i class="bi bi-upload me-1"></i>Upload File
                                </label>
                            </div>
                        </div>
                    </div>

                    <div id="url_field" class="mb-3">
                        <label class="form-label">Video URL</label>
                        <input type="url" name="video_url" class="form-control @error('video_url') is-invalid @enderror"
                            value="{{ old('video_url', $video->video_url) }}">
                        @error('video_url')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div id="upload_field" class="mb-3" style="display:none">
                        <label class="form-label">Replace Video File</label>
                        @if($video->video_path)
                            <div class="mb-2">
                                <span class="badge bg-secondary"><i class="bi bi-file-earmark-play me-1"></i>File uploaded</span>
                            </div>
                        @endif
                        <input type="file" name="video_file" class="form-control @error('video_file') is-invalid @enderror"
                            accept=".mp4,.mov,.avi,.webm">
                        <div class="form-text">Leave blank to keep existing file. Max 200 MB.</div>
                        @error('video_file')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Thumbnail</label>
                        @if($video->thumbnail_path)
                            <div class="mb-2">
                                <img src="{{ Storage::url($video->thumbnail_path) }}" alt="thumbnail" height="60" class="rounded">
                            </div>
                        @endif
                        <input type="file" name="thumbnail" class="form-control @error('thumbnail') is-invalid @enderror"
                            accept="image/*">
                        <div class="form-text">Leave blank to keep existing thumbnail.</div>
                        @error('thumbnail')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1"
                            {{ old('is_active', $video->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">Active (visible to agents)</label>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2 mb-4">
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle me-1"></i>Update Video</button>
                <a href="{{ route('admin.settings.videos.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
(function () {
    const radios = document.querySelectorAll('input[name="video_type"]');
    const urlField = document.getElementById('url_field');
    const uploadField = document.getElementById('upload_field');

    function toggle() {
        const val = document.querySelector('input[name="video_type"]:checked').value;
        urlField.style.display = (val === 'youtube' || val === 'url') ? '' : 'none';
        uploadField.style.display = (val === 'upload') ? '' : 'none';
    }

    radios.forEach(r => r.addEventListener('change', toggle));
    toggle();
})();
</script>
@endpush
@endsection
