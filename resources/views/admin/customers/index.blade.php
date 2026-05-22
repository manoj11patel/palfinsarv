@extends('layouts.admin')

@section('title', 'Customers')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Customers Management</h1>
    <a href="{{ route('admin.customers.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Add Customer
    </a>
</div>

<!-- Search & Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-3">
                <input type="text" name="search" class="form-control" placeholder="Search by name, email, or phone" value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select">
                    <option value="">All Statuses</option>
                    @foreach($statuses as $status)
                        <option value="{{ $status }}" @if(request('status') == $status) selected @endif>
                            {{ ucfirst($status) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="income_type" class="form-select">
                    <option value="">All Income Types</option>
                    <option value="job"      @if(request('income_type') === 'job')      selected @endif>Job / Salaried</option>
                    <option value="business" @if(request('income_type') === 'business') selected @endif>Business / Self-employed</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="star_rating" class="form-select">
                    <option value="">All Ratings</option>
                    @for($i = 5; $i >= 1; $i--)
                        <option value="{{ $i }}" @if(request('star_rating') == $i) selected @endif>
                            {{ str_repeat('★', $i) }}{{ str_repeat('☆', 5 - $i) }}
                        </option>
                    @endfor
                </select>
            </div>
            <div class="col-md-1">
                <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search"></i></button>
            </div>
            <div class="col-md-2">
                <a href="{{ route('admin.customers.index') }}" class="btn btn-outline-secondary w-100">
                    <i class="bi bi-x-circle"></i> Clear
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Customers Table -->
<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Phone</th>
                    <th>Agent</th>
                    <th>Income</th>
                    <th>Rating</th>
                    <th>Products</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($customers as $customer)
                    <tr>
                        <td><strong>#{{ $customer->id }}</strong></td>
                        <td>
                            <div class="fw-semibold">{{ $customer->full_name }}</div>
                            <small class="text-muted">{{ $customer->email }}</small>
                        </td>
                        <td>{{ $customer->phone }}</td>
                        <td>{{ $customer->agent->name }}</td>
                        <td style="max-width:150px;">
                            @if($customer->income_type === 'job')
                                <span class="badge bg-primary"><i class="bi bi-briefcase me-1"></i>Job</span>
                            @elseif($customer->income_type === 'business')
                                <span class="badge bg-warning text-dark"><i class="bi bi-shop me-1"></i>Business</span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                            @if($customer->income_details)
                                <div class="text-muted small mt-1"
                                     style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:140px;"
                                     title="{{ $customer->income_details }}">
                                    {{ $customer->income_details }}
                                </div>
                            @endif
                        </td>

                        {{-- Inline star rating --}}
                        <td>
                            <div class="star-rating d-flex gap-1" data-id="{{ $customer->id }}" data-url="{{ route('admin.customers.rating', $customer) }}">
                                @for($s = 1; $s <= 5; $s++)
                                    <i class="bi {{ ($customer->star_rating ?? 0) >= $s ? 'bi-star-fill text-warning' : 'bi-star text-secondary' }} star-btn"
                                       data-value="{{ $s }}"
                                       role="button"
                                       style="font-size:1.1rem;cursor:pointer;"
                                       title="{{ $s }} star{{ $s > 1 ? 's' : '' }}"></i>
                                @endfor
                            </div>
                        </td>

                        <td>
                            @if($customer->products->count() > 0)
                                <span class="badge bg-info">{{ $customer->products->count() }}</span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge badge-{{ $customer->status }}">{{ ucfirst($customer->status) }}</span>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('admin.customers.show', $customer) }}" class="btn btn-outline-info" title="View">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('admin.customers.edit', $customer) }}" class="btn btn-outline-primary" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted py-4">No customers found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Pagination -->
<div class="d-flex justify-content-center mt-4">
    {{ $customers->links() }}
</div>
@endsection

@section('extra-js')
<script>
const csrfToken = '{{ csrf_token() }}';

document.querySelectorAll('.star-rating').forEach(function (widget) {
    const stars = widget.querySelectorAll('.star-btn');
    const url   = widget.dataset.url;

    // Hover: highlight up to hovered star
    stars.forEach(function (star) {
        star.addEventListener('mouseenter', function () {
            const val = parseInt(this.dataset.value);
            stars.forEach(function (s) {
                const sv = parseInt(s.dataset.value);
                s.className = sv <= val
                    ? 'bi bi-star-fill text-warning star-btn'
                    : 'bi bi-star text-secondary star-btn';
                s.style.fontSize = '1.1rem';
                s.style.cursor   = 'pointer';
            });
        });

        // Mouse leave: restore saved rating
        star.addEventListener('mouseleave', function () {
            restoreStars(widget);
        });

        // Click: save via AJAX
        star.addEventListener('click', function () {
            const val = parseInt(this.dataset.value);
            widget.dataset.rating = val;

            $.ajax({
                url: url,
                method: 'POST',
                data: { _method: 'PATCH', _token: csrfToken, rating: val },
                success: function () {
                    restoreStars(widget);
                },
                error: function () {
                    alert('Failed to save rating.');
                }
            });
        });
    });
});

function restoreStars(widget) {
    const saved = parseInt(widget.dataset.rating || 0);
    widget.querySelectorAll('.star-btn').forEach(function (s) {
        const sv = parseInt(s.dataset.value);
        s.className = sv <= saved
            ? 'bi bi-star-fill text-warning star-btn'
            : 'bi bi-star text-secondary star-btn';
        s.style.fontSize = '1.1rem';
        s.style.cursor   = 'pointer';
    });
}

// Seed each widget's current rating into data-rating
document.querySelectorAll('.star-rating').forEach(function (widget) {
    const filled = widget.querySelectorAll('.bi-star-fill').length;
    widget.dataset.rating = filled;
});
</script>
@endsection
