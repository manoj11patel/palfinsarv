@extends('layouts.admin')

@section('title', 'Edit Banner')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0"><i class="bi bi-image"></i> Edit Banner</h4>
    <a href="{{ route('admin.banners.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Back
    </a>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.banners.update', $banner) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label class="form-label">Title <span class="text-danger">*</span></label>
                <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                       value="{{ old('title', $banner->title) }}" placeholder="Banner title">
                @error('title')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Banner Image</label>
                <div class="mb-2">
                    <img id="imagePreview" src="{{ Storage::url($banner->image_path) }}"
                         alt="{{ $banner->title }}"
                         style="max-height:180px;border-radius:6px;border:1px solid #dee2e6;">
                </div>
                <input type="file" name="image" id="imageInput"
                       class="form-control @error('image') is-invalid @enderror"
                       accept="image/jpg,image/jpeg,image/png,image/webp">
                <div class="form-text">Leave blank to keep existing image. Accepted: JPG, JPEG, PNG, WEBP — max 5 MB</div>
                @error('image')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active"
                           value="1" {{ old('is_active', $banner->is_active) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">Active (visible to customers)</label>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-lg"></i> Update Banner
            </button>
        </form>
    </div>
</div>
@endsection

@section('extra-js')
<script>
document.getElementById('imageInput').addEventListener('change', function () {
    const preview = document.getElementById('imagePreview');
    if (this.files && this.files[0]) {
        preview.src = URL.createObjectURL(this.files[0]);
    }
});
</script>
@endsection
