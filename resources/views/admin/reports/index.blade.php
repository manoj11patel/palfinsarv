@extends('layouts.admin')

@section('title', 'Reports')

@section('content')
<div class="mb-4">
    <h1>Reports & Analytics</h1>
</div>

<!-- Date Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-4">
                <label for="start_date" class="form-label">Start Date</label>
                <input type="date" class="form-control" id="start_date" name="start_date" value="{{ $startDate }}">
            </div>
            <div class="col-md-4">
                <label for="end_date" class="form-label">End Date</label>
                <input type="date" class="form-control" id="end_date" name="end_date" value="{{ $endDate }}">
            </div>
            <div class="col-md-4">
                <label>&nbsp;</label>
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search"></i> Update Report
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Conversion Metrics -->
<div class="card mb-4">
    <div class="card-header bg-light">
        <h5 class="mb-0">Conversion Metrics</h5>
    </div>
    <div class="card-body">
        <div class="row">
            @php
                $total = $conversionData->sum();
                $draft = $conversionData->get('draft', 0);
                $submitted = $conversionData->get('submitted', 0);
                $verified = $conversionData->get('verified', 0);
                $converted = $conversionData->get('converted', 0);
                $conversionRate = $total > 0 ? round(($converted / $total) * 100, 2) : 0;
            @endphp
            <div class="col-md-3">
                <div class="text-center">
                    <h3 class="text-primary">{{ $total }}</h3>
                    <p class="text-muted mb-0">Total Applications</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="text-center">
                    <h3 class="text-warning">{{ $submitted }}</h3>
                    <p class="text-muted mb-0">Submitted</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="text-center">
                    <h3 class="text-info">{{ $verified }}</h3>
                    <p class="text-muted mb-0">Verified</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="text-center">
                    <h3 class="text-success">{{ $converted }}</h3>
                    <p class="text-muted mb-0">Converted</p>
                </div>
            </div>
        </div>
        <hr class="my-3">
        <div class="mb-0">
            <p class="text-muted mb-1">Conversion Rate</p>
            <div class="progress" style="height: 25px;">
                <div class="progress-bar bg-success" role="progressbar" style="width: {{ $conversionRate }}%;">
                    {{ $conversionRate }}%
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Agent Performance -->
<div class="card mb-4">
    <div class="card-header bg-light">
        <h5 class="mb-0">Agent Performance</h5>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Agent</th>
                    <th>Total Applications</th>
                    <th>Converted</th>
                    <th>Conversion Rate</th>
                </tr>
            </thead>
            <tbody>
                @forelse($agentReport as $agent)
                    @php $rate = $agent->total > 0 ? round(($agent->converted / $agent->total) * 100, 2) : 0; @endphp
                    <tr>
                        <td><strong>{{ $agent->agent_name }}</strong></td>
                        <td>{{ $agent->total }}</td>
                        <td>{{ $agent->converted }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="progress flex-grow-1 me-2" style="height: 20px;">
                                    <div class="progress-bar" role="progressbar" style="width: {{ $rate }}%;">
                                    </div>
                                </div>
                                <span>{{ $rate }}%</span>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted py-4">No agent data available</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Product-wise Report -->
<div class="card">
    <div class="card-header bg-light">
        <h5 class="mb-0">Product-wise Report</h5>
    </div>
    <div class="card-body">
        @forelse($productReport as $productName => $statuses)
            <div class="mb-4">
                <h6>{{ $productName }}</h6>
                <div class="row">
                    @foreach(['draft' => 'secondary', 'submitted' => 'info', 'verified' => 'warning', 'converted' => 'success'] as $status => $color)
                        @php $count = $statuses->firstWhere('status', $status)?->count ?? 0; @endphp
                        <div class="col-md-3">
                            <div class="text-center">
                                <p class="mb-1">
                                    <span class="badge bg-{{ $color }}">{{ ucfirst($status) }}</span>
                                </p>
                                <h5>{{ $count }}</h5>
                            </div>
                        </div>
                    @endforeach
                </div>
                <hr>
            </div>
        @empty
            <p class="text-muted mb-0">No product data available</p>
        @endforelse
    </div>
</div>
@endsection
