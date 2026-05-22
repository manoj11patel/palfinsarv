@extends('customer.layout')

@section('title', 'Customer Dashboard')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <h1>My Account</h1>
        <p class="text-muted">Welcome, {{ auth()->user()->name }}! Track your applications and submissions here.</p>
    </div>
</div>

<!-- Customer Info -->
@if($customer)
    <div class="card mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0">My Information</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Full Name:</strong> {{ $customer->full_name }}</p>
                    <p><strong>Email:</strong> {{ $customer->email }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Phone:</strong> {{ $customer->phone }}</p>
                    <p><strong>Account Status:</strong> <span class="badge badge-{{ $customer->status }}">{{ ucfirst($customer->status) }}</span></p>
                </div>
            </div>
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
                <h6>Submitted</h6>
                <div class="value">{{ $submittedApplications }}</div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="stat-card">
                <h6>Verified</h6>
                <div class="value">{{ $verifiedApplications }}</div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="stat-card">
                <h6>Converted</h6>
                <div class="value">{{ $convertedApplications }}</div>
            </div>
        </div>
    </div>

    <!-- My Applications -->
    <div class="card">
        <div class="card-header bg-light">
            <h5 class="mb-0">My Applications</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Product</th>
                        <th>Status</th>
                        <th>Submitted</th>
                        <th>Last Updated</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($applications as $application)
                        <tr>
                            <td><strong>#{{ $application->id }}</strong></td>
                            <td>{{ $application->product->name }}</td>
                            <td>
                                <span class="badge badge-{{ $application->status }}">
                                    {{ ucfirst($application->status) }}
                                </span>
                            </td>
                            <td>{{ $application->submitted_at ? $application->submitted_at->format('M d, Y') : 'Not Submitted' }}</td>
                            <td>{{ $application->updated_at->format('M d, Y H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
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
        <i class="bi bi-info-circle"></i> <strong>Application Status Guide:</strong><br>
        <ul class="mb-0 mt-2">
            <li><strong>Draft:</strong> Your application is saved but not submitted yet</li>
            <li><strong>Submitted:</strong> Waiting for admin review and verification</li>
            <li><strong>Verified:</strong> Your documents have been reviewed and approved</li>
            <li><strong>Converted:</strong> Your application has been approved successfully!</li>
        </ul>
    </div>
@else
    <div class="alert alert-warning" role="alert">
        <i class="bi bi-exclamation-triangle"></i> <strong>No Customer Account Found</strong><br>
        Your email address is not linked to a customer account. Please contact the support team to set up your account.
    </div>
@endif
@endsection
