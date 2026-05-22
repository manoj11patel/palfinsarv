@extends('agent.layout')

@section('title', 'Agent Dashboard')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <h1>Agent Dashboard</h1>
        <p class="text-muted">Welcome back, {{ auth()->user()->name }}! Here's your performance overview.</p>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-6 col-lg-3">
        <div class="stat-card">
            <h6>Total Applications</h6>
            <div class="value">{{ $totalApplications }}</div>
        </div>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="stat-card">
            <h6>Submitted (Pending)</h6>
            <div class="value">{{ $submittedApplications }}</div>
        </div>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="stat-card">
            <h6>Verified (Ready)</h6>
            <div class="value">{{ $verifiedApplications }}</div>
        </div>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="stat-card">
            <h6>Conversion Rate</h6>
            <div class="value">{{ $conversionRate }}%</div>
        </div>
    </div>
</div>

<!-- Recent Applications -->
<div class="card">
    <div class="card-header bg-light">
        <h5 class="mb-0">Recent Applications</h5>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Customer</th>
                    <th>Product</th>
                    <th>Status</th>
                    <th>Submitted</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentApplications as $application)
                    <tr>
                        <td>
                            <strong>{{ $application->customer->full_name }}</strong><br>
                            <small class="text-muted">{{ $application->customer->email }}</small>
                        </td>
                        <td>{{ $application->product->name }}</td>
                        <td>
                            <span class="badge badge-{{ $application->status }}">
                                {{ ucfirst($application->status) }}
                            </span>
                        </td>
                        <td>{{ $application->submitted_at ? $application->submitted_at->format('M d, Y') : 'Not Submitted' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted py-4">
                            <i class="bi bi-inbox"></i> No applications yet
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Info Box -->
<div class="alert alert-info mt-4" role="alert">
    <i class="bi bi-info-circle"></i> <strong>Note:</strong> You can view and manage your assigned customers and applications through the API endpoints. 
    For admin-only features like managing other agents' data or system settings, contact the administrator.
</div>
@endsection
