@extends('layouts.app')

@section('title', 'Agent Onboarding')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card border-success mb-4">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0"><i class="bi bi-check-circle"></i> Welcome to Onboarding</h4>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <strong>Agent Referral:</strong> You have been invited by <strong>{{ $agent->name }}</strong> to submit your application
                    </div>

                    <p class="mb-3">
                        To proceed with your application, please choose one of the following options:
                    </p>
                </div>
            </div>

            @if(auth()->check())
                <!-- If Logged In -->
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="bi bi-file-earmark"></i> Option 1: Create New Application</h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-3">
                            You are logged in as <strong>{{ auth()->user()->name }}</strong>. 
                            Create a new application with your details and upload required documents.
                        </p>
                        <a href="{{ route('customer.applications.create', ['agent' => $agent->id]) }}" class="btn btn-primary">
                            <i class="bi bi-plus-circle"></i> Start Application
                        </a>
                    </div>
                </div>

                @if(auth()->user()->role === 'customer')
                    <div class="card mb-4">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0"><i class="bi bi-pencil-square"></i> Option 2: Update Existing Application</h5>
                        </div>
                        <div class="card-body">
                            <p class="mb-3">
                                View and update your existing applications with this agent.
                            </p>
                            <a href="{{ route('customer.applications.index') }}" class="btn btn-info">
                                <i class="bi bi-list"></i> View My Applications
                            </a>
                        </div>
                    </div>
                @endif
            @else
                <!-- If Not Logged In -->
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="bi bi-box-arrow-in-right"></i> Login</h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-3">
                            If you already have an account, please login to proceed.
                        </p>
                        <a href="{{ route('login') }}" class="btn btn-primary">
                            <i class="bi bi-box-arrow-in-right"></i> Login
                        </a>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="bi bi-person-plus"></i> Register</h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-3">
                            Don't have an account yet? Create a new account to submit your application.
                        </p>
                        <a href="{{ route('register') }}" class="btn btn-success">
                            <i class="bi bi-person-plus"></i> Create Account
                        </a>
                    </div>
                </div>
            @endif

            <!-- Agent Information -->
            <div class="card border-secondary">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"><i class="bi bi-person-badge"></i> Your Agent</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p class="text-muted small mb-1">Agent Name</p>
                            <p class="mb-3"><strong>{{ $agent->name }}</strong></p>
                        </div>
                        <div class="col-md-6">
                            <p class="text-muted small mb-1">Email</p>
                            <p class="mb-3"><strong>{{ $agent->email }}</strong></p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <p class="text-muted small mb-1">Phone</p>
                            <p class="mb-0"><strong>{{ $agent->agentProfile->phone }}</strong></p>
                        </div>
                        <div class="col-md-6">
                            <p class="text-muted small mb-1">Employee Code</p>
                            <p class="mb-0"><strong>{{ $agent->agentProfile->employee_code }}</strong></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
