@extends('layouts.admin')

@section('title', 'Applications')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="mb-0">Applications Management</h1>
    <a href="{{ route('admin.applications.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Add Application
    </a>
</div>

<!-- Search & Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="Search by customer name or email" value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">All Statuses</option>
                    @foreach($statuses as $status)
                        <option value="{{ $status }}" @if(request('status') == $status) selected @endif>
                            {{ ucfirst($status) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select name="product_id" class="form-select">
                    <option value="">All Products</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}" @if(request('product_id') == $product->id) selected @endif>
                            {{ $product->name }}
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

<!-- Applications Table -->
<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Customer</th>
                    <th>Product</th>
                    <th>Agent</th>
                    <th>Status</th>
                    <th>Submitted</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($applications as $app)
                    <tr>
                        <td><strong>#{{ $app->id }}</strong></td>
                        <td>
                            <div>{{ $app->customer->full_name }}</div>
                            <small class="text-muted">{{ $app->customer->email }}</small>
                        </td>
                        <td>{{ $app->product->name }}</td>
                        <td>
                            {{ $app->agent?->name ?? '—' }}
                            @if($app->agent && $app->agent->role === 'admin')
                                <span class="badge bg-secondary ms-1" style="font-size:10px;">Admin</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge badge-{{ $app->status }}">{{ ucfirst($app->status) }}</span>
                        </td>
                        <td>{{ $app->submitted_at ? $app->submitted_at->format('M d, Y') : '-' }}</td>
                        <td>
                            <a href="{{ route('admin.applications.show', $app) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-eye"></i> View
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">No applications found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Pagination -->
<div class="d-flex justify-content-center mt-4">
    {{ $applications->links() }}
</div>
@endsection
