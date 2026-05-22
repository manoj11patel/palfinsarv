@extends('layouts.admin')

@section('title', 'Banners')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0"><i class="bi bi-image"></i> Banners</h4>
    <a href="{{ route('admin.banners.create') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-lg"></i> Add Banner
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Preview</th>
                    <th>Title</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($banners as $banner)
                <tr>
                    <td>{{ $banner->id }}</td>
                    <td>
                        <img src="{{ Storage::url($banner->image_path) }}"
                             alt="{{ $banner->title }}"
                             style="width:90px;height:55px;object-fit:cover;border-radius:4px;">
                    </td>
                    <td>{{ $banner->title }}</td>
                    <td>
                        @if($banner->is_active)
                            <span class="badge bg-success">Active</span>
                        @else
                            <span class="badge bg-secondary">Inactive</span>
                        @endif
                    </td>
                    <td>{{ $banner->created_at->format('d M Y') }}</td>
                    <td>
                        <a href="{{ route('admin.banners.edit', $banner) }}" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form method="POST" action="{{ route('admin.banners.destroy', $banner) }}" class="d-inline"
                              onsubmit="return confirm('Delete this banner?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">No banners found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if($banners->hasPages())
    <div class="mt-3">{{ $banners->links() }}</div>
@endif
@endsection
