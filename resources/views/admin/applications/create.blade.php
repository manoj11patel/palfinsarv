@extends('layouts.admin')

@section('title', 'Add Application')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="mb-0">Add Application</h1>
    <a href="{{ route('admin.applications.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Back to Applications
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header bg-light">
                <h5 class="mb-0"><i class="bi bi-file-earmark-plus me-2"></i>New Application</h5>
            </div>
            <div class="card-body">

                @if(session('error'))
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-circle"></i> {{ session('error') }}
                    </div>
                @endif

                <form action="{{ route('admin.applications.store') }}" method="POST">
                    @csrf

                    {{-- Customer --}}
                    <div class="mb-4">
                        <label for="customer_id" class="form-label fw-semibold">Customer <span class="text-danger">*</span></label>
                        <select name="customer_id" id="customer_id" class="form-select @error('customer_id') is-invalid @enderror" required>
                            <option value="">— Select Customer —</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}"
                                    {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                    {{ $customer->full_name }}
                                    ({{ $customer->phone }})
                                    @if($customer->agent)
                                        — Agent: {{ $customer->agent->name }}
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        @error('customer_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Select the customer this application belongs to.</div>
                    </div>

                    {{-- Product --}}
                    <div class="mb-4">
                        <label for="product_id" class="form-label fw-semibold">Product <span class="text-danger">*</span></label>
                        <select name="product_id" id="product_id" class="form-select @error('product_id') is-invalid @enderror" required>
                            <option value="">— Select Product —</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}"
                                    {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                    {{ $product->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('product_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Agent (optional) --}}
                    <div class="mb-4">
                        <label for="agent_user_id" class="form-label fw-semibold">Assign to Agent</label>
                        <select name="agent_user_id" id="agent_user_id" class="form-select @error('agent_user_id') is-invalid @enderror">
                            <option value="">— No Agent (Created by Admin) —</option>
                            @foreach($agents as $agent)
                                <option value="{{ $agent->id }}"
                                    {{ old('agent_user_id') == $agent->id ? 'selected' : '' }}>
                                    {{ $agent->name }}
                                    @if($agent->agentProfile)
                                        ({{ $agent->agentProfile->employee_code }})
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        @error('agent_user_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Leave blank to record this application under the admin account.</div>
                    </div>

                    {{-- Status --}}
                    <div class="mb-4">
                        <label for="status" class="form-label fw-semibold">Initial Status <span class="text-danger">*</span></label>
                        <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                            <option value="submitted" {{ old('status', 'submitted') === 'submitted' ? 'selected' : '' }}>
                                Submitted — ready for review
                            </option>
                            <option value="draft" {{ old('status') === 'draft' ? 'selected' : '' }}>
                                Draft — save without submitting
                            </option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            <strong>Submitted</strong> applications can be verified immediately.
                            <strong>Draft</strong> applications stay pending until submitted.
                        </div>
                    </div>

                    <hr>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Create Application
                        </button>
                        <a href="{{ route('admin.applications.index') }}" class="btn btn-secondary">
                            <i class="bi bi-x-circle"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>

        {{-- Info card --}}
        <div class="card mt-3">
            <div class="card-body">
                <h6 class="text-muted mb-2"><i class="bi bi-info-circle"></i> Status Flow</h6>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="badge badge-draft px-3 py-2">Draft</span>
                    <i class="bi bi-arrow-right text-muted"></i>
                    <span class="badge badge-submitted px-3 py-2">Submitted</span>
                    <i class="bi bi-arrow-right text-muted"></i>
                    <span class="badge badge-verified px-3 py-2">Verified</span>
                    <i class="bi bi-arrow-right text-muted"></i>
                    <span class="badge badge-converted px-3 py-2">Converted</span>
                </div>
                <p class="text-muted small mt-2 mb-0">Applications created by admin start at <strong>Submitted</strong> by default and can be immediately reviewed.</p>
            </div>
        </div>
    </div>
</div>
@endsection
