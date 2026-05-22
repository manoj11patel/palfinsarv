<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Agent Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .sidebar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding-top: 1rem;
            position: sticky;
            top: 0;
        }
        .sidebar-header {
            padding: 1.5rem;
            color: white;
            text-align: center;
        }
        .sidebar-header h6 {
            margin: 0;
            font-size: 0.9rem;
        }
        .sidebar-header .user-email {
            font-size: 0.85rem;
            opacity: 0.9;
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
        .logout-btn {
            background-color: #dc3545;
        }
        .logout-btn:hover {
            background-color: #c82333;
        }
    </style>
    @yield('extra-css')
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 sidebar">
                <div class="sidebar-header">
                    <h6 class="mb-1">{{ auth()->user()->name }}</h6>
                    <div class="user-email">{{ auth()->user()->email }}</div>
                    <small class="badge bg-light text-dark mt-2">Agent</small>
                </div>
                <nav class="nav flex-column">
                    <a href="{{ route('agent.dashboard') }}" class="nav-link @if(Route::is('agent.dashboard')) active @endif">
                        <i class="bi bi-graph-up"></i> Dashboard
                    </a>
                    <a href="{{ route('agent.videos.index') }}" class="nav-link @if(Route::is('agent.videos*')) active @endif">
                        <i class="bi bi-play-circle"></i> Videos
                    </a>
                </nav>
                <hr style="border-color: rgba(255,255,255,0.2); margin: 1rem 0;">
                <div class="px-3">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn btn-outline-light btn-sm w-100 logout-btn">
                            <i class="bi bi-box-arrow-right"></i> Logout
                        </button>
                    </form>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 main-content">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @yield('content')
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
