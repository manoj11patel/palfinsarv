<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    @stack('styles')
    <style>
        body {
            background-color: #f8f9fa;
        }
        .sidebar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding-top: 1rem;
        }
        .sidebar a {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            display: block;
            padding: 0.75rem 1.5rem;
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
        }
        .sidebar a:hover {
            color: white;
            background-color: rgba(255, 255, 255, 0.1);
            border-left-color: white;
        }
        .sidebar a.active {
            color: white;
            background-color: rgba(255, 255, 255, 0.2);
            border-left-color: white;
            font-weight: 600;
        }
        .main-content {
            padding: 2rem;
        }
        .card {
            border: 0;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            margin-bottom: 1.5rem;
        }
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1.5rem;
            border-radius: 0.5rem;
            text-align: center;
        }
        .stat-card h6 {
            font-size: 0.875rem;
            opacity: 0.9;
            margin-bottom: 0.5rem;
        }
        .stat-card .value {
            font-size: 2rem;
            font-weight: bold;
        }
        .table-hover tbody tr:hover {
            background-color: #f8f9fa;
        }
        .navbar-brand {
            color: white;
            font-weight: bold;
            font-size: 1.5rem;
        }
        .badge-draft { background-color: #6c757d; }
        .badge-submitted { background-color: #0dcaf0; }
        .badge-verified { background-color: #198754; }
        .badge-converted { background-color: #0d6efd; }
        .badge-approved { background-color: #198754; }
        .badge-rejected { background-color: #dc3545; }
        .badge-uploaded { background-color: #ffc107; }
    </style>
    @yield('extra-css')
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 sidebar">
                <div class="navbar-brand ms-3 mb-4">
                    <i class="bi bi-speedometer2"></i> Pal Finsarv
                </div>
                <nav class="nav flex-column">
                    <a href="{{ route('admin.dashboard') }}" class="nav-link @if(Route::is('admin.dashboard')) active @endif">
                        <i class="bi bi-graph-up"></i> Dashboard
                    </a>
                    <a href="{{ route('admin.applications.index') }}" class="nav-link @if(Route::is('admin.applications*')) active @endif">
                        <i class="bi bi-file-earmark"></i> Applications
                    </a>
                    <a href="{{ route('admin.customers.index') }}" class="nav-link @if(Route::is('admin.customers*')) active @endif">
                        <i class="bi bi-people"></i> Customers
                    </a>
                    <a href="{{ route('admin.documents.index') }}" class="nav-link @if(Route::is('admin.documents*')) active @endif">
                        <i class="bi bi-file-pdf"></i> Documents
                    </a>
                    <a href="{{ route('admin.messages.index') }}" class="nav-link @if(Route::is('admin.messages*')) active @endif">
                        <i class="bi bi-chat-dots"></i> Messages
                        @php
                            $unreadCount = auth()->user()->receivedMessages()->where('is_read', false)->count();
                        @endphp
                        @if($unreadCount > 0)
                            <span class="badge bg-danger rounded-pill float-end">{{ $unreadCount }}</span>
                        @endif
                    </a>
                    <a href="{{ route('admin.reports') }}" class="nav-link @if(Route::is('admin.reports')) active @endif">
                        <i class="bi bi-bar-chart"></i> Reports
                    </a>
                    <a href="{{ route('admin.audit-logs') }}" class="nav-link @if(Route::is('admin.audit-logs')) active @endif">
                        <i class="bi bi-clock-history"></i> Audit Logs
                    </a>
                    <a href="{{ route('admin.categories.index') }}" class="nav-link @if(Route::is('admin.categories*')) active @endif">
                        <i class="bi bi-tags"></i> Categories
                    </a>
                    <a href="{{ route('admin.products.index') }}" class="nav-link @if(Route::is('admin.products*')) active @endif">
                        <i class="bi bi-box"></i> Products
                    </a>
                    <a href="{{ route('admin.agents.index') }}" class="nav-link @if(Route::is('admin.agents*')) active @endif">
                        <i class="bi bi-person-badge"></i> Agents
                    </a>
                    <a href="{{ route('admin.users.index') }}" class="nav-link @if(Route::is('admin.users*')) active @endif">
                        <i class="bi bi-lock"></i> Users
                    </a>
                    <a href="{{ route('admin.settings.videos.index') }}" class="nav-link @if(Route::is('admin.settings.videos*')) active @endif">
                        <i class="bi bi-play-circle"></i> Videos
                    </a>
                    <a href="{{ route('admin.banners.index') }}" class="nav-link @if(Route::is('admin.banners*')) active @endif">
                        <i class="bi bi-image"></i> Banners
                    </a>
                    <hr class="my-3" style="border-color: rgba(255,255,255,0.2)">
                    <form method="POST" action="{{ route('logout') }}" class="m-3">
                        @csrf
                        <button type="submit" class="btn btn-outline-light btn-sm w-100">
                            <i class="bi bi-box-arrow-right"></i> Logout
                        </button>
                    </form>
                </nav>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 main-content">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-circle"></i> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @yield('content')
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @yield('extra-js')
    @stack('scripts')
</body>
</html>
