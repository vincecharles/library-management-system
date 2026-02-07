<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'AklatBayon')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-navy: #1a1a2e;
            --primary-blue: #0f3460;
            --primary-red: #e94560;
            --success: #28a745;
            --warning: #ffc107;
            --danger: #dc3545;
            --info: #17a2b8;
            --purple: #6f42c1;
            --gray: #6c757d;
            --light-gray: #e9ecef;
            --sidebar-width: 260px;
        }

        * { box-sizing: border-box; }

        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background-color: #ffffff;
            margin: 0;
            min-height: 100vh;
        }

        /* Top Header */
        .top-header {
            background-color: var(--primary-navy);
            color: #fff;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 24px;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1030;
        }
        .top-header .logo {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 18px;
            font-weight: 700;
        }
        .top-header .logo i {
            font-size: 24px;
            color: var(--primary-red);
        }
        .top-header .user-info {
            display: flex;
            align-items: center;
            gap: 16px;
        }
        .top-header .user-info .user-name {
            font-size: 14px;
        }
        .top-header .user-info .user-role {
            font-size: 11px;
            color: rgba(255,255,255,0.7);
        }
        .top-header .btn-logout {
            color: #fff;
            background: var(--primary-red);
            border: none;
            padding: 6px 16px;
            border-radius: 4px;
            font-size: 13px;
            cursor: pointer;
            text-decoration: none;
        }
        .top-header .btn-logout:hover {
            background: #d63851;
            color: #fff;
        }

        /* Sidebar */
        .sidebar {
            width: var(--sidebar-width);
            background-color: var(--primary-navy);
            position: fixed;
            top: 60px;
            left: 0;
            bottom: 0;
            overflow-y: auto;
            z-index: 1020;
            padding-top: 8px;
        }
        .sidebar::-webkit-scrollbar {
            width: 4px;
        }
        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,0.2);
            border-radius: 4px;
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,0.75);
            padding: 12px 24px;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 12px;
            transition: all 0.2s;
            text-decoration: none;
            border-left: 3px solid transparent;
        }
        .sidebar .nav-link:hover {
            background-color: rgba(255,255,255,0.08);
            color: #fff;
        }
        .sidebar .nav-link.active {
            background-color: var(--primary-red);
            color: #fff;
            border-left-color: #fff;
        }
        .sidebar .nav-link i {
            width: 20px;
            text-align: center;
            font-size: 15px;
        }
        .sidebar .nav-divider {
            border-top: 1px solid rgba(255,255,255,0.1);
            margin: 8px 16px;
        }
        .sidebar .nav-heading {
            color: rgba(255,255,255,0.4);
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 16px 24px 4px;
        }

        /* Main Content */
        .main-content {
            margin-left: var(--sidebar-width);
            margin-top: 60px;
            padding: 24px;
            min-height: calc(100vh - 60px);
            background: #ffffff;
        }

        /* Page Header */
        .page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
            padding-bottom: 16px;
            border-bottom: 1px solid var(--light-gray);
        }
        .page-header h1 {
            font-size: 24px;
            font-weight: 700;
            color: var(--primary-navy);
            margin: 0;
        }

        /* Stat Cards */
        .stat-card {
            background: #fff;
            border-radius: 8px;
            padding: 20px;
            border: 1px solid var(--light-gray);
            transition: box-shadow 0.2s;
        }
        .stat-card:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }
        .stat-card .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            color: #fff;
        }
        .stat-card .stat-number {
            font-size: 28px;
            font-weight: 700;
            color: var(--primary-navy);
        }
        .stat-card .stat-label {
            font-size: 13px;
            color: var(--gray);
            margin-top: 2px;
        }

        /* Cards */
        .card {
            border: 1px solid var(--light-gray);
            border-radius: 8px;
            box-shadow: none;
        }
        .card-header {
            background: #fff;
            border-bottom: 1px solid var(--light-gray);
            font-weight: 600;
            font-size: 16px;
            color: var(--primary-navy);
            padding: 16px 20px;
        }

        /* Tables */
        .table th {
            background-color: #f8f9fa;
            font-weight: 600;
            font-size: 13px;
            color: var(--primary-navy);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid var(--light-gray);
        }
        .table td {
            font-size: 14px;
            vertical-align: middle;
            color: #333;
        }

        /* Buttons */
        .btn-primary {
            background-color: var(--primary-navy);
            border-color: var(--primary-navy);
        }
        .btn-primary:hover {
            background-color: #141428;
            border-color: #141428;
        }
        .btn-danger {
            background-color: var(--primary-red);
            border-color: var(--primary-red);
        }
        .btn-danger:hover {
            background-color: #d63851;
            border-color: #d63851;
        }

        /* Status Badges */
        .badge-active { background-color: var(--success); }
        .badge-inactive { background-color: var(--gray); }
        .badge-overdue { background-color: var(--danger); }
        .badge-borrowed { background-color: var(--warning); color: #000; }
        .badge-returned { background-color: var(--success); }
        .badge-pending { background-color: var(--warning); color: #000; }
        .badge-paid { background-color: var(--success); }
        .badge-waived { background-color: var(--info); }

        /* Action Buttons */
        .btn-action {
            padding: 4px 8px;
            font-size: 13px;
            border-radius: 4px;
            margin: 0 2px;
        }

        /* Search Filters */
        .filter-bar {
            background: #fff;
            padding: 16px;
            border-radius: 8px;
            border: 1px solid var(--light-gray);
            margin-bottom: 20px;
        }

        /* Quick Action Cards */
        .quick-action {
            background: #fff;
            border-radius: 8px;
            padding: 20px;
            border: 1px solid var(--light-gray);
            text-align: center;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
            color: inherit;
            display: block;
        }
        .quick-action:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            transform: translateY(-2px);
            color: inherit;
        }
        .quick-action i {
            font-size: 32px;
            margin-bottom: 8px;
        }
        .quick-action .action-title {
            font-weight: 600;
            font-size: 14px;
        }

        /* Forms */
        .form-label {
            font-weight: 600;
            font-size: 13px;
            color: var(--primary-navy);
        }
        .form-control, .form-select {
            border-color: #dee2e6;
            font-size: 14px;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-blue);
            box-shadow: 0 0 0 0.2rem rgba(15, 52, 96, 0.15);
        }

        /* Alerts */
        .alert {
            border-radius: 8px;
            font-size: 14px;
        }

        /* SweetAlert style info boxes */
        .info-card {
            border-radius: 8px;
            padding: 16px 20px;
            font-size: 14px;
        }
        .info-card.info-blue { background: #e8f4f8; border: 1px solid #b8daef; color: var(--primary-blue); }
        .info-card.info-green { background: #f0f8e8; border: 1px solid #c3e6a8; color: #2d6a2e; }
        .info-card.info-yellow { background: #fff3e8; border: 1px solid #f5d4a8; color: #856404; }
        .info-card.info-red { background: #f8e8e8; border: 1px solid #f5b8b8; color: #9b1c1c; }
        .info-card.info-purple { background: #f8f4fc; border: 1px solid #d4bfe8; color: var(--purple); }

        @media (max-width: 768px) {
            .sidebar { display: none; }
            .main-content { margin-left: 0; }
        }
    </style>
    @stack('styles')
</head>
<body>
    <!-- Top Header -->
    <header class="top-header">
        <div class="logo">
            <i class="fas fa-book-open"></i>
            <span>AklatBayon</span>
        </div>
        <div class="user-info">
            <div class="text-end">
                <div class="user-name">{{ Auth::user()->name }}</div>
                <div class="user-role">{{ Auth::user()->role->name ?? 'User' }}</div>
            </div>
            <form action="{{ route('logout') }}" method="POST" style="display:inline">
                @csrf
                <button type="submit" class="btn-logout">
                    <i class="fas fa-sign-out-alt me-1"></i> Logout
                </button>
            </form>
        </div>
    </header>

    <!-- Sidebar -->
    <nav class="sidebar">
        @php $role = Auth::user()->role->name ?? ''; @endphp

        <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="fas fa-tachometer-alt"></i> Dashboard
        </a>

        @if(in_array($role, ['Administrator', 'Librarian']))
            <div class="nav-heading">Management</div>

            @if($role === 'Administrator')
            <a href="{{ route('users.index') }}" class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                <i class="fas fa-users-cog"></i> User Management
            </a>
            @endif

            <a href="{{ route('students.index') }}" class="nav-link {{ request()->routeIs('students.*') ? 'active' : '' }}">
                <i class="fas fa-user-graduate"></i> Student Management
            </a>
        @endif

        <div class="nav-heading">Catalog</div>
        <a href="{{ route('books.index') }}" class="nav-link {{ request()->routeIs('books.*') ? 'active' : '' }}">
            <i class="fas fa-book"></i> Book Catalog
        </a>

        @if(in_array($role, ['Administrator', 'Librarian']))
        <a href="{{ route('authors.index') }}" class="nav-link {{ request()->routeIs('authors.*') ? 'active' : '' }}">
            <i class="fas fa-pen-fancy"></i> Authors
        </a>
        <a href="{{ route('publishers.index') }}" class="nav-link {{ request()->routeIs('publishers.*') ? 'active' : '' }}">
            <i class="fas fa-building"></i> Publishers
        </a>
        <a href="{{ route('categories.index') }}" class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}">
            <i class="fas fa-tags"></i> Categories
        </a>
        @endif

        <div class="nav-heading">Circulation</div>
        <a href="{{ route('circulation.issue') }}" class="nav-link {{ request()->routeIs('circulation.issue') ? 'active' : '' }}">
            <i class="fas fa-hand-holding"></i> Issue Book
        </a>
        <a href="{{ route('circulation.return') }}" class="nav-link {{ request()->routeIs('circulation.return') ? 'active' : '' }}">
            <i class="fas fa-undo"></i> Return Book
        </a>
        <a href="{{ route('circulation.history') }}" class="nav-link {{ request()->routeIs('circulation.history') ? 'active' : '' }}">
            <i class="fas fa-history"></i> Borrowing History
        </a>

        <div class="nav-heading">Finance</div>
        <a href="{{ route('fines.index') }}" class="nav-link {{ request()->routeIs('fines.*') ? 'active' : '' }}">
            <i class="fas fa-money-bill-wave"></i> Fine Management
        </a>

        @if(in_array($role, ['Administrator', 'Librarian']))
        <div class="nav-heading">Administration</div>
        <a href="{{ route('inventory.index') }}" class="nav-link {{ request()->routeIs('inventory.*') ? 'active' : '' }}">
            <i class="fas fa-boxes-stacked"></i> Inventory
        </a>
        <a href="{{ route('reports.index') }}" class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
            <i class="fas fa-chart-bar"></i> Reports
        </a>
        @endif

        @if($role === 'Administrator')
        <div class="nav-divider"></div>
        <a href="{{ route('settings.index') }}" class="nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}">
            <i class="fas fa-cog"></i> Settings
        </a>
        <a href="{{ route('backup.index') }}" class="nav-link {{ request()->routeIs('backup.*') ? 'active' : '' }}">
            <i class="fas fa-database"></i> Backup / Restore
        </a>
        <a href="{{ route('audit-logs.index') }}" class="nav-link {{ request()->routeIs('audit-logs.*') ? 'active' : '' }}">
            <i class="fas fa-clipboard-list"></i> Audit Logs
        </a>
        @endif
    </nav>

    <!-- Main Content -->
    <main class="main-content">
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        @yield('content')
    </main>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
    <script>
        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });

        // Confirm delete forms
        document.querySelectorAll('.delete-form').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                Swal.fire({
                    title: 'Are you sure?',
                    text: "This action cannot be undone.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#e94560',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) form.submit();
                });
            });
        });
    </script>
    @stack('scripts')
</body>
</html>
