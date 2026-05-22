@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="mb-0">Dashboard</h1>
    <span class="text-muted">{{ now()->format('l, F j, Y') }}</span>
</div>

{{-- ==================== TODAY'S BIRTHDAYS ==================== --}}
<div class="card mb-4 border-0 shadow-sm" style="border-left: 4px solid #fd7e14 !important; background: linear-gradient(135deg, #fff8f0 0%, #fff 100%);">
    <div class="card-header border-bottom" style="background:transparent;">
        <div class="d-flex align-items-center gap-2">
            <span class="fs-4">🎂</span>
            <h5 class="mb-0 text-warning">Today's Birthdays</h5>
            <span class="badge bg-warning text-dark ms-1">{{ $todayBirthdays->count() + $todayAgentBirthdays->count() }}</span>
            <span class="text-muted small ms-auto">{{ now()->format('d F Y') }}</span>
        </div>
    </div>
    <div class="card-body pb-0">

        {{-- Tabs --}}
        <ul class="nav nav-tabs" id="birthdayTabs">
            <li class="nav-item">
                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#bday-customers">
                    <i class="bi bi-people me-1"></i> Customers
                    <span class="badge bg-warning text-dark ms-1">{{ $todayBirthdays->count() }}</span>
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#bday-agents">
                    <i class="bi bi-briefcase me-1"></i> Agents
                    <span class="badge bg-warning text-dark ms-1">{{ $todayAgentBirthdays->count() }}</span>
                </button>
            </li>
        </ul>

        <div class="tab-content" id="birthdayTabContent">

            {{-- Customers tab --}}
            <div class="tab-pane fade show active pt-2" id="bday-customers">
                @if($todayBirthdays->isEmpty())
                    <p class="text-center text-muted py-4 mb-0">No customer birthdays today</p>
                @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">#</th>
                                <th>Customer</th>
                                <th>Date of Birth</th>
                                <th>Age</th>
                                <th>Agent</th>
                                <th>WhatsApp / Phone</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($todayBirthdays as $i => $c)
                                @php
                                    $age = $c->date_of_birth ? \Carbon\Carbon::parse($c->date_of_birth)->age : null;
                                    $rawPhone = preg_replace('/\D/', '', $c->phone ?? '');
                                    if (strlen($rawPhone) === 10) { $rawPhone = '91' . $rawPhone; }
                                    $waLink = 'https://wa.me/' . $rawPhone;
                                @endphp
                                <tr>
                                    <td class="ps-3 text-muted">{{ $i + 1 }}</td>
                                    <td>
                                        <div class="fw-semibold">{{ $c->full_name }}</div>
                                        <div class="text-muted small">{{ $c->email }}</div>
                                    </td>
                                    <td class="text-muted small">{{ $c->date_of_birth->format('d M Y') }}</td>
                                    <td>
                                        @if($age)
                                            <span class="badge bg-warning text-dark">{{ $age }} yrs</span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td class="text-muted small">{{ $c->agent?->name ?? '—' }}</td>
                                    <td>
                                        @if($rawPhone)
                                            <a href="{{ $waLink }}" target="_blank" rel="noopener noreferrer"
                                               class="btn btn-sm btn-success d-inline-flex align-items-center gap-1">
                                                <i class="bi bi-whatsapp"></i>
                                                <span>{{ $c->phone }}</span>
                                            </a>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.customers.show', $c) }}" class="btn btn-sm btn-outline-secondary">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>

            {{-- Agents tab --}}
            <div class="tab-pane fade pt-2" id="bday-agents">
                @if($todayAgentBirthdays->isEmpty())
                    <p class="text-center text-muted py-4 mb-0">No agent birthdays today</p>
                @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">#</th>
                                <th>Agent</th>
                                <th>Employee Code</th>
                                <th>Date of Birth</th>
                                <th>Age</th>
                                <th>WhatsApp / Phone</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($todayAgentBirthdays as $i => $ap)
                                @php
                                    $age = $ap->date_of_birth ? \Carbon\Carbon::parse($ap->date_of_birth)->age : null;
                                    $rawPhone = preg_replace('/\D/', '', $ap->phone ?? '');
                                    if (strlen($rawPhone) === 10) { $rawPhone = '91' . $rawPhone; }
                                    $waLink = 'https://wa.me/' . $rawPhone;
                                @endphp
                                <tr>
                                    <td class="ps-3 text-muted">{{ $i + 1 }}</td>
                                    <td>
                                        <div class="fw-semibold">{{ $ap->user?->name ?? '—' }}</div>
                                        <div class="text-muted small">{{ $ap->user?->email ?? '' }}</div>
                                    </td>
                                    <td class="text-muted small">{{ $ap->employee_code ?? '—' }}</td>
                                    <td class="text-muted small">{{ $ap->date_of_birth->format('d M Y') }}</td>
                                    <td>
                                        @if($age)
                                            <span class="badge bg-warning text-dark">{{ $age }} yrs</span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($rawPhone)
                                            <a href="{{ $waLink }}" target="_blank" rel="noopener noreferrer"
                                               class="btn btn-sm btn-success d-inline-flex align-items-center gap-1">
                                                <i class="bi bi-whatsapp"></i>
                                                <span>{{ $ap->phone }}</span>
                                            </a>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>

        </div>{{-- end tab-content --}}
    </div>
</div>
{{-- ==================== END TODAY'S BIRTHDAYS ==================== --}}

{{-- ==================== USER STATUS OVERVIEW ==================== --}}
<div class="card mb-4">
    <div class="card-header bg-white border-bottom">
        <h5 class="mb-0"><i class="bi bi-bar-chart-steps me-2 text-primary"></i>User Status Overview</h5>
    </div>
    <div class="card-body pb-0">

        {{-- Three stat counters --}}
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="border rounded p-3 d-flex align-items-center gap-3" style="border-left: 4px solid #0d6efd !important;">
                    <div class="fs-1 text-primary"><i class="bi bi-phone"></i></div>
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase">App Login Users</div>
                        <div class="fs-2 fw-bold text-primary">{{ $appLoginCount }}</div>
                        <div class="text-muted small">Downloaded app &amp; signed in</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="border rounded p-3 d-flex align-items-center gap-3" style="border-left: 4px solid #fd7e14 !important;">
                    <div class="fs-1 text-warning"><i class="bi bi-hourglass-split"></i></div>
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase">Pending — No Documents</div>
                        <div class="fs-2 fw-bold text-warning">{{ $pendingCount }}</div>
                        <div class="text-muted small">Basic details added, docs missing</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="border rounded p-3 d-flex align-items-center gap-3" style="border-left: 4px solid #198754 !important;">
                    <div class="fs-1 text-success"><i class="bi bi-patch-check"></i></div>
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase">Completed Applications</div>
                        <div class="fs-2 fw-bold text-success">{{ $completedCount }}</div>
                        <div class="text-muted small">Fully converted &amp; done</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tabs --}}
        <ul class="nav nav-tabs" id="userStatusTabs">
            <li class="nav-item">
                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-app-login">
                    <i class="bi bi-phone me-1"></i> App Login
                    <span class="badge bg-primary ms-1">{{ $appLoginCount }}</span>
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-pending">
                    <i class="bi bi-hourglass-split me-1"></i> Pending
                    <span class="badge bg-warning text-dark ms-1">{{ $pendingCount }}</span>
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-completed">
                    <i class="bi bi-patch-check me-1"></i> Completed
                    <span class="badge bg-success ms-1">{{ $completedCount }}</span>
                </button>
            </li>
        </ul>

        <div class="tab-content pt-3" id="userStatusTabContent">

            {{-- Tab 1: App Login Users --}}
            <div class="tab-pane fade show active" id="tab-app-login">
                <p class="text-muted small mb-2">Customers who have registered on the mobile app and logged in (most recent 5)</p>
                @if($recentAppLogins->isEmpty())
                    <p class="text-center text-muted py-4">No app login users yet</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-sm table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Customer</th>
                                    <th>Phone</th>
                                    <th>Email</th>
                                    <th>Agent</th>
                                    <th>Joined</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentAppLogins as $c)
                                    <tr>
                                        <td><strong>{{ $c->full_name }}</strong></td>
                                        <td>{{ $c->phone }}</td>
                                        <td class="text-muted small">{{ $c->email }}</td>
                                        <td>{{ $c->agent?->name ?? '—' }}</td>
                                        <td class="text-muted small">{{ $c->created_at->format('d M Y') }}</td>
                                        <td>
                                            <a href="{{ route('admin.customers.show', $c) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            {{-- Tab 2: Pending (no documents) --}}
            <div class="tab-pane fade" id="tab-pending">
                <p class="text-muted small mb-2">Customers with a draft application but no documents uploaded yet (most recent 5)</p>
                @if($recentPending->isEmpty())
                    <p class="text-center text-muted py-4">No pending users</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-sm table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Customer</th>
                                    <th>Phone</th>
                                    <th>Email</th>
                                    <th>Agent</th>
                                    <th>Application</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentPending as $c)
                                    @php $app = $c->applications->first(); @endphp
                                    <tr>
                                        <td><strong>{{ $c->full_name }}</strong></td>
                                        <td>{{ $c->phone }}</td>
                                        <td class="text-muted small">{{ $c->email }}</td>
                                        <td>{{ $c->agent?->name ?? '—' }}</td>
                                        <td>
                                            @if($app)
                                                <span class="badge bg-warning text-dark">Draft</span>
                                                <span class="text-muted small ms-1">No docs</span>
                                            @else
                                                <span class="badge bg-secondary">No application</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.customers.show', $c) }}" class="btn btn-sm btn-outline-warning">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            {{-- Tab 3: Completed --}}
            <div class="tab-pane fade" id="tab-completed">
                <p class="text-muted small mb-2">Customers with at least one fully converted application (most recent 5)</p>
                @if($recentCompleted->isEmpty())
                    <p class="text-center text-muted py-4">No completed applications yet</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-sm table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Customer</th>
                                    <th>Phone</th>
                                    <th>Email</th>
                                    <th>Agent</th>
                                    <th>Converted On</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentCompleted as $c)
                                    @php $app = $c->applications->first(); @endphp
                                    <tr>
                                        <td><strong>{{ $c->full_name }}</strong></td>
                                        <td>{{ $c->phone }}</td>
                                        <td class="text-muted small">{{ $c->email }}</td>
                                        <td>{{ $c->agent?->name ?? '—' }}</td>
                                        <td class="text-muted small">
                                            {{ $app?->converted_at?->format('d M Y') ?? '—' }}
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.customers.show', $c) }}" class="btn btn-sm btn-outline-success">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

        </div>{{-- end tab-content --}}
    </div>
</div>
{{-- ==================== END USER STATUS OVERVIEW ==================== --}}

{{-- Key Metrics --}}
<div class="row mb-4">
    <div class="col-md-6 col-lg-3">
        <div class="stat-card">
            <h6>Total Applications</h6>
            <div class="value">{{ $totalApplications }}</div>
        </div>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="stat-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
            <h6>Converted</h6>
            <div class="value">{{ $convertedApplications }}</div>
        </div>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="stat-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
            <h6>Pending Review</h6>
            <div class="value">{{ $submittedApplications }}</div>
        </div>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="stat-card" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
            <h6>Conversion Rate</h6>
            <div class="value">{{ $conversionRate }}%</div>
        </div>
    </div>
</div>

{{-- Summary Cards --}}
<div class="row mb-4">
    <div class="col-md-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="text-muted mb-2">Total Customers</h6>
                        <p class="h4 mb-0">{{ $totalCustomers }}</p>
                    </div>
                    <div class="fs-2 text-primary"><i class="bi bi-people"></i></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="text-muted mb-2">Active Agents</h6>
                        <p class="h4 mb-0">{{ $totalAgents }}</p>
                    </div>
                    <div class="fs-2 text-success"><i class="bi bi-briefcase"></i></div>
                </div>
            </div>
        </div>
    </div>
</div>



{{-- ==================== AGENT BUSINESS LEADERBOARD ==================== --}}
<div class="card mb-4">
    <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-trophy me-2 text-warning"></i>Agent Business Leaderboard</h5>
        <span class="text-muted small">Ranked by total customers</span>
    </div>
    <div class="card-body p-0">
        @if($agentLeaderboard->isEmpty())
            <p class="text-center text-muted py-4">No agents found</p>
        @else
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3" style="width:48px;">Rank</th>
                        <th>Agent</th>
                        <th class="text-center">Total<br><small class="fw-normal text-muted">Customers</small></th>
                        <th class="text-center">Converted<br><small class="fw-normal text-muted">Done</small></th>
                        <th class="text-center">Pending<br><small class="fw-normal text-muted">In progress</small></th>
                        <th>Business Amount<br><small class="fw-normal text-muted">Investment sum</small></th>
                        <th style="min-width:160px;">Share of Business</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($agentLeaderboard as $i => $agent)
                        @php
                            $amount      = $agent->assigned_customers_sum_investment_amount ?? 0;
                            $customerPct = round(($agent->total_customers / $leaderboardTotalCustomers) * 100, 1);
                            $amountPct   = round(($amount / $leaderboardTotalAmount) * 100, 1);
                            $rankColors  = ['text-warning', 'text-secondary', 'text-danger'];
                            $rankIcons   = ['bi-trophy-fill', 'bi-award-fill', 'bi-star-fill'];
                            $barColors   = ['bg-primary', 'bg-success', 'bg-info', 'bg-warning', 'bg-secondary'];
                            $barColor    = $barColors[$i % count($barColors)];
                        @endphp
                        <tr>
                            <td class="ps-3 text-center">
                                @if($i < 3)
                                    <i class="bi {{ $rankIcons[$i] }} fs-5 {{ $rankColors[$i] }}"></i>
                                @else
                                    <span class="text-muted fw-bold">#{{ $i + 1 }}</span>
                                @endif
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $agent->name }}</div>
                                <div class="text-muted small">
                                    {{ $agent->agentProfile?->employee_code ?? '—' }}
                                    &nbsp;·&nbsp;
                                    {{ $agent->agentProfile?->phone ?? '' }}
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="fs-5 fw-bold">{{ $agent->total_customers }}</span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-success fs-6">{{ $agent->converted_count }}</span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-warning text-dark fs-6">{{ $agent->pending_count }}</span>
                            </td>
                            <td>
                                <div class="fw-semibold">
                                    ₹{{ number_format($amount, 0) }}
                                </div>
                                <div class="text-muted small">{{ $amountPct }}% of total</div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="progress flex-grow-1" style="height:10px;" title="{{ $customerPct }}% of customers">
                                        <div class="progress-bar {{ $barColor }}" style="width:{{ $customerPct }}%;"></div>
                                    </div>
                                    <span class="text-muted small" style="width:36px;">{{ $customerPct }}%</span>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="table-light fw-bold">
                    <tr>
                        <td class="ps-3" colspan="2">Total</td>
                        <td class="text-center">{{ $agentLeaderboard->sum('total_customers') }}</td>
                        <td class="text-center">{{ $agentLeaderboard->sum('converted_count') }}</td>
                        <td class="text-center">{{ $agentLeaderboard->sum('pending_count') }}</td>
                        <td>₹{{ number_format($agentLeaderboard->sum('assigned_customers_sum_investment_amount'), 0) }}</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        @endif
    </div>
</div>
{{-- ==================== END AGENT LEADERBOARD ==================== --}}

{{-- Recent Applications --}}
<div class="card">
    <div class="card-header bg-white border-bottom">
        <h5 class="mb-0">Recent Applications</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Customer</th>
                        <th>Product</th>
                        <th>Agent</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentApplications as $app)
                        <tr>
                            <td><strong>{{ $app->id }}</strong></td>
                            <td>{{ $app->customer->full_name }}</td>
                            <td>{{ $app->product->name }}</td>
                            <td>{{ $app->agent->name }}</td>
                            <td>
                                <span class="badge badge-{{ $app->status }}">{{ ucfirst($app->status) }}</span>
                            </td>
                            <td>{{ $app->created_at->format('M d, Y') }}</td>
                            <td>
                                <a href="{{ route('admin.applications.show', $app) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i> View
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">No applications yet</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Application Status Distribution --}}
<div class="card mt-4">
    <div class="card-header bg-white border-bottom">
        <h5 class="mb-0">Application Status Distribution</h5>
    </div>
    <div class="card-body">
        <div class="row">
            @foreach($applicationsByStatus as $status => $count)
                <div class="col-md-3">
                    <div class="mb-3">
                        <p class="text-muted mb-2">{{ ucfirst($status) }}</p>
                        <div class="progress" style="height: 30px;">
                            <div class="progress-bar badge-{{ $status }}" role="progressbar"
                                style="width: {{ $totalApplications > 0 ? ($count / $totalApplications * 100) : 0 }}%;">
                                {{ $count }}
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
