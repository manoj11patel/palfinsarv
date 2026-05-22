@extends('layouts.admin')

@section('title', 'Agent Details')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Agent Details</h1>
    <a href="{{ route('admin.agents.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Back to Agents
    </a>
</div>

<div class="row">
    {{-- Agent Information --}}
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0">Agent Information</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-sm-4 fw-bold text-muted">Name:</div>
                    <div class="col-sm-8">{{ $agent->name }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-4 fw-bold text-muted">Email:</div>
                    <div class="col-sm-8">{{ $agent->email }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-4 fw-bold text-muted">Employee Code:</div>
                    <div class="col-sm-8">{{ $agent->agentProfile->employee_code }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-4 fw-bold text-muted">Phone:</div>
                    <div class="col-sm-8">{{ $agent->agentProfile->phone }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-4 fw-bold text-muted">Date of Birth:</div>
                    <div class="col-sm-8">
                        @if($agent->agentProfile->date_of_birth)
                            {{ $agent->agentProfile->date_of_birth->format('d M Y') }}
                            <span class="text-muted small ms-2">
                                ({{ $agent->agentProfile->date_of_birth->age }} yrs)
                            </span>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-4 fw-bold text-muted">Status:</div>
                    <div class="col-sm-8">
                        @if($agent->agentProfile->is_active)
                            <span class="badge bg-success">Active</span>
                        @else
                            <span class="badge bg-danger">Inactive</span>
                        @endif
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-4 fw-bold text-muted">Member Since:</div>
                    <div class="col-sm-8">{{ $agent->created_at->format('M d, Y') }}</div>
                </div>
            </div>
            <div class="card-footer bg-light d-flex gap-2">
                <a href="{{ route('admin.agents.edit', $agent) }}" class="btn btn-primary btn-sm flex-grow-1">
                    <i class="bi bi-pencil"></i> Edit Agent
                </a>
                <form method="POST" action="{{ route('admin.agents.update-status', $agent) }}" class="flex-grow-1" onsubmit="return confirm('Toggle agent status?');">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="is_active" value="{{ $agent->agentProfile->is_active ? 0 : 1 }}">
                    <button type="submit" class="btn btn-{{ $agent->agentProfile->is_active ? 'danger' : 'success' }} btn-sm w-100">
                        <i class="bi bi-{{ $agent->agentProfile->is_active ? 'lock' : 'unlock' }}"></i>
                        {{ $agent->agentProfile->is_active ? 'Deactivate' : 'Activate' }}
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Assigned Customers --}}
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-light">
                <h5 class="mb-0">Assigned Customers <span class="badge bg-info ms-2">{{ $agent->assignedCustomers->count() }}</span></h5>
            </div>
            <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                @forelse($agent->assignedCustomers as $customer)
                    <div class="border-bottom pb-3 mb-3">
                        <p class="mb-1"><strong>{{ $customer->full_name }}</strong></p>
                        <p class="mb-1 small text-muted">{{ $customer->email }}</p>
                        <p class="mb-0 small text-muted">Phone: {{ $customer->phone }}</p>
                    </div>
                @empty
                    <p class="text-center text-muted py-4">No customers assigned yet</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

{{-- Payout Summary --}}
<div class="card mt-4">
    <div class="card-header bg-light d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-cash-stack"></i> Payout Summary</h5>
        <div class="d-flex align-items-center gap-2">
            <label class="mb-0 small text-muted">Year:</label>
            <select id="summary-year" class="form-select form-select-sm" style="width:auto;">
                @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                    <option value="{{ $y }}">{{ $y }}</option>
                @endfor
            </select>
        </div>
    </div>
    <div class="card-body">
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="border p-3 rounded bg-light text-center">
                    <div class="fw-bold text-muted small">Total Policies Sold</div>
                    <div class="fs-3 fw-bold text-primary" id="total-policies">—</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="border p-3 rounded bg-light text-center">
                    <div class="fw-bold text-muted small">Total Earnings</div>
                    <div class="fs-3 fw-bold text-success" id="total-earnings">—</div>
                </div>
            </div>
        </div>

        <h6 class="mb-3">Monthly Breakdown</h6>
        <div class="table-responsive">
            <table class="table table-bordered table-sm table-hover" id="payout-summary-table">
                <thead class="table-light">
                    <tr>
                        <th>Month</th>
                        <th>Year</th>
                        <th>Policies</th>
                        <th>Total Amount</th>
                        <th>Commission</th>
                        <th>Deductions</th>
                        <th>Net Amount</th>
                        <th>Slip</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td colspan="8" class="text-center text-muted py-3">Loading...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('extra-js')
<script>
const agentProfileId = {{ $agent->agentProfile->id }};

function fetchPayoutSummary(year) {
    $.get(`/admin/agents/${agentProfileId}/payouts/summary`, { year: year }, function (data) {
        $('#total-policies').text(data.total_policies);
        $('#total-earnings').text('₹' + parseFloat(data.total_earnings).toFixed(2));

        if (!data.payouts || data.payouts.length === 0) {
            $('#payout-summary-table tbody').html('<tr><td colspan="8" class="text-center text-muted py-3">No payout data for this year</td></tr>');
            return;
        }

        let rows = '';
        data.payouts.forEach(function (p) {
            rows += `<tr>
                <td>${p.month}</td>
                <td>${p.year}</td>
                <td>${p.total_policies}</td>
                <td>₹${parseFloat(p.total_amount).toFixed(2)}</td>
                <td>₹${parseFloat(p.commission).toFixed(2)}</td>
                <td>₹${parseFloat(p.deductions).toFixed(2)}</td>
                <td><strong>₹${parseFloat(p.net_amount).toFixed(2)}</strong></td>
                <td>
                    <a href="/admin/payouts/${p.id}/download" class="btn btn-sm btn-outline-secondary" title="Download Slip">
                        <i class="bi bi-file-pdf"></i>
                    </a>
                </td>
            </tr>`;
        });
        $('#payout-summary-table tbody').html(rows);
    }).fail(function () {
        $('#payout-summary-table tbody').html('<tr><td colspan="8" class="text-center text-danger">Failed to load summary</td></tr>');
    });
}

$(function () {
    fetchPayoutSummary($('#summary-year').val());
    $('#summary-year').on('change', function () {
        fetchPayoutSummary($(this).val());
    });
});
</script>
@endsection
