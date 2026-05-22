@extends('layouts.admin')

@section('title', 'Edit Agent')

@section('content')
<div class="mb-4">
    <h1>Edit Agent</h1>
</div>

{{-- Agent Details Form --}}
<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.agents.update', $agent) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="name" class="form-label">Name *</label>
                <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $agent->name) }}" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email *</label>
                <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $agent->email) }}" required>
                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label for="employee_code" class="form-label">Employee Code *</label>
                <input type="text" name="employee_code" id="employee_code" class="form-control @error('employee_code') is-invalid @enderror" value="{{ old('employee_code', $agent->agentProfile->employee_code) }}" required>
                @error('employee_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label for="phone" class="form-label">Phone *</label>
                <input type="text" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $agent->agentProfile->phone) }}" required>
                @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label for="date_of_birth" class="form-label">Date of Birth</label>
                <input type="date" name="date_of_birth" id="date_of_birth"
                       class="form-control @error('date_of_birth') is-invalid @enderror"
                       value="{{ old('date_of_birth', $agent->agentProfile->date_of_birth?->format('Y-m-d')) }}"
                       max="{{ date('Y-m-d') }}">
                @error('date_of_birth')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-4">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $agent->agentProfile->is_active) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active"><strong>Active (can login)</strong></label>
                </div>
                <small class="text-muted d-block mt-2">Unchecking this will prevent the agent from logging in</small>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Update Agent
                </button>
                <a href="{{ route('admin.agents.index') }}" class="btn btn-secondary">
                    <i class="bi bi-x-circle"></i> Cancel
                </a>
            </div>
        </form>
    </div>
</div>

{{-- Payout Information Section --}}
<div class="card mt-4">
    <div class="card-header bg-light d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-cash-stack"></i> Payout Information</h5>
    </div>
    <div class="card-body">
        <div id="payout-alert"></div>

        <form id="addPayoutForm" action="{{ route('admin.agents.payouts.store', $agent->agentProfile->id) }}" method="POST">
            @csrf
            <div class="row g-3 align-items-end">
                <div class="col-md-2">
                    <label class="form-label">Month *</label>
                    <select name="month" id="payout_month" class="form-select" required>
                        @foreach(['January','February','March','April','May','June','July','August','September','October','November','December'] as $m)
                            <option value="{{ $m }}" {{ $m == date('F') ? 'selected' : '' }}>{{ $m }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Year *</label>
                    <select name="year" id="payout_year" class="form-select" required>
                        @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                            <option value="{{ $y }}">{{ $y }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-1">
                    <label class="form-label">Policies</label>
                    <input type="number" name="total_policies" id="payout_policies" class="form-control" min="0" value="0">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Total Amount *</label>
                    <input type="number" step="0.01" name="total_amount" id="payout_total" class="form-control" required min="0" placeholder="0.00">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Commission *</label>
                    <input type="number" step="0.01" name="commission" id="payout_commission" class="form-control" required min="0" placeholder="0.00">
                </div>
                <div class="col-md-1">
                    <label class="form-label">Deductions</label>
                    <input type="number" step="0.01" name="deductions" id="payout_deductions" class="form-control" min="0" placeholder="0.00">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Net Amount</label>
                    <input type="number" step="0.01" name="net_amount" id="payout_net" class="form-control bg-light" readonly placeholder="Auto-calculated">
                </div>
            </div>
            <div class="mt-3">
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-plus-circle"></i> Add Payout
                </button>
            </div>
        </form>

        <hr>

        <h6 class="mb-3">Previous Payouts</h6>
        <div class="table-responsive">
            <table class="table table-bordered table-sm table-hover" id="payouts-table">
                <thead class="table-light">
                    <tr>
                        <th>Month</th>
                        <th>Year</th>
                        <th>Policies</th>
                        <th>Total Amount</th>
                        <th>Commission</th>
                        <th>Deductions</th>
                        <th>Net Amount</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr id="payouts-empty-row">
                        <td colspan="8" class="text-center text-muted py-3">Loading...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Edit Payout Modal --}}
<div class="modal fade" id="editPayoutModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editPayoutForm">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Edit Payout</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="edit_payout_id">
                    <div class="mb-3">
                        <label class="form-label">Total Policies</label>
                        <input type="number" id="edit_total_policies" class="form-control" min="0">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Total Amount *</label>
                        <input type="number" step="0.01" id="edit_total_amount" class="form-control" required min="0">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Commission *</label>
                        <input type="number" step="0.01" id="edit_commission" class="form-control" required min="0">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deductions</label>
                        <input type="number" step="0.01" id="edit_deductions" class="form-control" min="0">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Net Amount (auto)</label>
                        <input type="number" step="0.01" id="edit_net_amount" class="form-control bg-light" readonly>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('extra-js')
<script>
const agentProfileId = {{ $agent->agentProfile->id }};
const csrfToken = '{{ csrf_token() }}';

function calcNet(total, commission, deductions) {
    return (parseFloat(total) || 0) + (parseFloat(commission) || 0) - (parseFloat(deductions) || 0);
}

// Auto-calculate net amount in add form
$('#payout_total, #payout_commission, #payout_deductions').on('input', function () {
    const net = calcNet($('#payout_total').val(), $('#payout_commission').val(), $('#payout_deductions').val());
    $('#payout_net').val(net.toFixed(2));
});

// Auto-calculate net amount in edit modal
$('#edit_total_amount, #edit_commission, #edit_deductions').on('input', function () {
    const net = calcNet($('#edit_total_amount').val(), $('#edit_commission').val(), $('#edit_deductions').val());
    $('#edit_net_amount').val(net.toFixed(2));
});

function showAlert(type, msg) {
    $('#payout-alert').html(`<div class="alert alert-${type} alert-dismissible fade show" role="alert">
        ${msg}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>`);
}

function fetchPayouts() {
    $.get(`/admin/agents/${agentProfileId}/payouts/summary`, function (data) {
        if (!data.payouts || data.payouts.length === 0) {
            $('#payouts-table tbody').html('<tr><td colspan="8" class="text-center text-muted py-3">No payouts recorded yet</td></tr>');
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
                <td class="text-nowrap">
                    <button class="btn btn-sm btn-outline-primary me-1" onclick="openEditModal(${p.id}, ${p.total_policies}, ${p.total_amount}, ${p.commission}, ${p.deductions})">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <a href="/admin/payouts/${p.id}/download" class="btn btn-sm btn-outline-secondary me-1" title="Download PDF">
                        <i class="bi bi-file-pdf"></i>
                    </a>
                    <button class="btn btn-sm btn-outline-danger" onclick="deletePayout(${p.id})" title="Delete">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            </tr>`;
        });
        $('#payouts-table tbody').html(rows);
    }).fail(function () {
        $('#payouts-table tbody').html('<tr><td colspan="8" class="text-center text-danger">Failed to load payouts</td></tr>');
    });
}

$('#addPayoutForm').on('submit', function (e) {
    e.preventDefault();
    $.ajax({
        url: $(this).attr('action'),
        method: 'POST',
        data: $(this).serialize(),
        success: function () {
            $('#addPayoutForm')[0].reset();
            $('#payout_net').val('');
            showAlert('success', 'Payout added successfully.');
            fetchPayouts();
        },
        error: function (xhr) {
            const msg = xhr.responseJSON?.error || 'Failed to add payout.';
            showAlert('danger', msg);
        }
    });
});

function openEditModal(id, policies, total, commission, deductions) {
    $('#edit_payout_id').val(id);
    $('#edit_total_policies').val(policies);
    $('#edit_total_amount').val(parseFloat(total).toFixed(2));
    $('#edit_commission').val(parseFloat(commission).toFixed(2));
    $('#edit_deductions').val(parseFloat(deductions).toFixed(2));
    const net = calcNet(total, commission, deductions);
    $('#edit_net_amount').val(net.toFixed(2));
    new bootstrap.Modal(document.getElementById('editPayoutModal')).show();
}

$('#editPayoutForm').on('submit', function (e) {
    e.preventDefault();
    const id = $('#edit_payout_id').val();
    $.ajax({
        url: `/admin/payouts/${id}`,
        method: 'POST',
        data: {
            _method: 'PUT',
            _token: csrfToken,
            total_policies: $('#edit_total_policies').val(),
            total_amount: $('#edit_total_amount').val(),
            commission: $('#edit_commission').val(),
            deductions: $('#edit_deductions').val(),
        },
        success: function () {
            bootstrap.Modal.getInstance(document.getElementById('editPayoutModal')).hide();
            showAlert('success', 'Payout updated successfully.');
            fetchPayouts();
        },
        error: function () {
            showAlert('danger', 'Failed to update payout.');
        }
    });
});

function deletePayout(id) {
    if (!confirm('Delete this payout record?')) return;
    $.ajax({
        url: `/admin/payouts/${id}`,
        method: 'POST',
        data: { _method: 'DELETE', _token: csrfToken },
        success: function () {
            showAlert('success', 'Payout deleted.');
            fetchPayouts();
        },
        error: function () {
            showAlert('danger', 'Failed to delete payout.');
        }
    });
}

$(function () {
    fetchPayouts();
});
</script>
@endsection
