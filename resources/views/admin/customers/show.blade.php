@extends('layouts.admin')

@section('title', 'Customer Details - ' . $customer->full_name)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>{{ $customer->full_name }}</h1>
    <div class="btn-group">
        <a href="{{ route('admin.customers.edit', $customer) }}" class="btn btn-primary">
            <i class="bi bi-pencil"></i> Edit
        </a>
        <a href="{{ route('admin.customers.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>
</div>

<div class="row">
    <!-- Customer Information -->
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0">Customer Information</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label text-muted">Full Name</label>
                    <div>{{ $customer->full_name }}</div>
                </div>
                <div class="mb-3">
                    <label class="form-label text-muted">Email</label>
                    <div><a href="mailto:{{ $customer->email }}">{{ $customer->email }}</a></div>
                </div>
                <div class="mb-3">
                    <label class="form-label text-muted">Phone</label>
                    <div><a href="tel:{{ $customer->phone }}">{{ $customer->phone }}</a></div>
                </div>
                <div class="mb-3">
                    <label class="form-label text-muted">Agent</label>
                    <div>{{ $customer->agent->name }}</div>
                </div>
                <div class="mb-3">
                    <label class="form-label text-muted">Status</label>
                    <div>
                        <span class="badge badge-{{ $customer->status }} fs-6">{{ ucfirst($customer->status) }}</span>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label text-muted">Created</label>
                    <div>{{ $customer->created_at->format('M d, Y H:i A') }}</div>
                </div>
                <div class="mb-0">
                    <label class="form-label text-muted">Star Rating</label>
                    <div class="star-rating d-flex gap-2 align-items-center"
                         data-id="{{ $customer->id }}"
                         data-rating="{{ $customer->star_rating ?? 0 }}"
                         data-url="{{ route('admin.customers.rating', $customer) }}">
                        @for($s = 1; $s <= 5; $s++)
                            <i class="bi {{ ($customer->star_rating ?? 0) >= $s ? 'bi-star-fill text-warning' : 'bi-star text-secondary' }} star-btn"
                               data-value="{{ $s }}"
                               role="button"
                               style="font-size:1.6rem;cursor:pointer;"
                               title="{{ $s }} star{{ $s > 1 ? 's' : '' }}"></i>
                        @endfor
                        <span class="text-muted small ms-1" id="rating-label">
                            {{ $customer->star_rating ? $customer->star_rating . ' / 5' : 'Not rated' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Address Info -->
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0">Address Information</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label text-muted">Date of Birth</label>
                    <div>{{ $customer->date_of_birth?->format('M d, Y') ?? '—' }}</div>
                </div>
                <div class="mb-3">
                    <label class="form-label text-muted">Address</label>
                    <div>{{ $customer->address ?? '—' }}</div>
                </div>
                <div class="mb-3">
                    <label class="form-label text-muted">City</label>
                    <div>{{ $customer->city ?? '—' }}</div>
                </div>
                <div class="mb-3">
                    <label class="form-label text-muted">State</label>
                    <div>{{ $customer->state->name ?? '—' }}</div>
                </div>
                <div class="mb-3">
                    <label class="form-label text-muted">Pincode</label>
                    <div>{{ $customer->pincode ?? '—' }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- KYC & Bank Details -->
<div class="row">
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0">KYC & Bank Details</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label text-muted">PAN Number</label>
                    <div>{{ $customer->pan_number ?? '—' }}</div>
                </div>
                <div class="mb-3">
                    <label class="form-label text-muted">Account Holder Name</label>
                    <div>{{ $customer->account_holder_name ?? '—' }}</div>
                </div>
                <div class="mb-3">
                    <label class="form-label text-muted">Bank Name</label>
                    <div>{{ $customer->bank_name ?? '—' }}</div>
                </div>
                <div class="mb-3">
                    <label class="form-label text-muted">Account Number</label>
                    <div class="font-monospace">{{ $customer->account_number ?? '—' }}</div>
                </div>
                <div class="mb-3">
                    <label class="form-label text-muted">IFSC Code</label>
                    <div>{{ $customer->ifsc_code ?? '—' }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Investment Details -->
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0">Investment & Income Details</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label text-muted">Fund Name</label>
                    <div>{{ $customer->fund_name ?? '—' }}</div>
                </div>
                <div class="mb-3">
                    <label class="form-label text-muted">Investment Type</label>
                    <div>{{ $customer->investment_type ?? '—' }}</div>
                </div>
                <div class="mb-3">
                    <label class="form-label text-muted">Investment Amount</label>
                    <div>{{ $customer->investment_amount ? '₹ ' . number_format($customer->investment_amount, 2) : '—' }}</div>
                </div>

                <hr class="my-3">

                <div class="mb-3">
                    <label class="form-label text-muted">Income Type</label>
                    <div>
                        @if($customer->income_type === 'job')
                            <span class="badge bg-primary"><i class="bi bi-briefcase me-1"></i>Job / Salaried</span>
                        @elseif($customer->income_type === 'business')
                            <span class="badge bg-warning text-dark"><i class="bi bi-shop me-1"></i>Business / Self-employed</span>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label text-muted">Income Details</label>
                    <div>{{ $customer->income_details ?? '—' }}</div>
                </div>

                <hr class="my-3">

                <div class="mb-3">
                    <label class="form-label text-muted">Nominee Name</label>
                    <div>{{ $customer->nominee_name ?? '—' }}</div>
                </div>
                <div class="mb-3">
                    <label class="form-label text-muted">Nominee Relationship</label>
                    <div>{{ $customer->nominee_relationship ?? '—' }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Products Interested -->
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0">Products Interested In</h5>
            </div>
            <div class="card-body">
                @if($customer->products->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($customer->products as $product)
                            <div class="list-group-item">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="mb-1">{{ $product->name }}</h6>
                                        <small class="text-muted">{{ $product->code }}</small>
                                        @if($product->category)
                                            <br><span class="badge bg-info text-dark">{{ $product->category->name }}</span>
                                        @endif
                                    </div>
                                    @if($product->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="alert alert-info mb-0">
                        <i class="bi bi-info-circle"></i> No products selected yet
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Applications -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-light">
                <h5 class="mb-0">Applications ({{ $customer->applications->count() }})</h5>
            </div>
            <div class="card-body">
                @if($customer->applications->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Product</th>
                                    <th>Status</th>
                                    <th>Submitted</th>
                                    <th>Verified</th>
                                    <th>Converted</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($customer->applications as $app)
                                    <tr>
                                        <td>#{{ $app->id }}</td>
                                        <td>{{ $app->product->name }}</td>
                                        <td>
                                            <span class="badge badge-{{ $app->status }}">{{ ucfirst($app->status) }}</span>
                                        </td>
                                        <td>{{ $app->submitted_at ? $app->submitted_at->format('M d, Y') : '—' }}</td>
                                        <td>{{ $app->verified_at ? $app->verified_at->format('M d, Y') : '—' }}</td>
                                        <td>{{ $app->converted_at ? $app->converted_at->format('M d, Y') : '—' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-info mb-0">
                        <i class="bi bi-info-circle"></i> No applications yet
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('extra-js')
<script>
const csrfToken = '{{ csrf_token() }}';

document.querySelectorAll('.star-rating').forEach(function (widget) {
    const stars = widget.querySelectorAll('.star-btn');
    const url   = widget.dataset.url;
    const label = widget.querySelector('#rating-label') || document.getElementById('rating-label');

    stars.forEach(function (star) {
        star.addEventListener('mouseenter', function () {
            const val = parseInt(this.dataset.value);
            stars.forEach(function (s) {
                s.className = parseInt(s.dataset.value) <= val
                    ? 'bi bi-star-fill text-warning star-btn'
                    : 'bi bi-star text-secondary star-btn';
            });
        });

        star.addEventListener('mouseleave', function () {
            restoreStars(widget);
        });

        star.addEventListener('click', function () {
            const val = parseInt(this.dataset.value);
            widget.dataset.rating = val;

            $.ajax({
                url: url,
                method: 'POST',
                data: { _method: 'PATCH', _token: csrfToken, rating: val },
                success: function () {
                    restoreStars(widget);
                    if (label) label.textContent = val + ' / 5';
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
        s.className = parseInt(s.dataset.value) <= saved
            ? 'bi bi-star-fill text-warning star-btn'
            : 'bi bi-star text-secondary star-btn';
    });
}
</script>
@endsection
