<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Clinic Management System')</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #2563eb;
            --secondary-color: #64748b;
            --success-color: #059669;
            --warning-color: #d97706;
            --danger-color: #dc2626;
            --info-color: #0891b2;
            --light-color: #f8fafc;
            --dark-color: #1e293b;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: #f8fafc;
        }

        .navbar-brand {
            font-weight: 700;
            color: var(--primary-color) !important;
        }

        .sidebar {
            min-height: 100vh;
            background: linear-gradient(135deg, var(--primary-color) 0%, #1e40af 100%);
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        }

        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 12px 20px;
            margin: 4px 0;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            color: white;
            background-color: rgba(255,255,255,0.1);
            transform: translateX(5px);
        }

        .sidebar .nav-link i {
            width: 20px;
            margin-right: 10px;
        }

        .main-content {
            padding: 20px;
        }

        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -3px rgba(0,0,0,0.1);
        }

        .card-header {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            border-bottom: 1px solid #e2e8f0;
            font-weight: 600;
        }

        .btn {
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn:hover {
            transform: translateY(-1px);
        }

        .alert {
            border: none;
            border-radius: 8px;
            border-left: 4px solid;
        }

        .alert-success { border-left-color: var(--success-color); }
        .alert-warning { border-left-color: var(--warning-color); }
        .alert-danger { border-left-color: var(--danger-color); }
        .alert-info { border-left-color: var(--info-color); }

        .stats-card {
            background: linear-gradient(135deg, var(--primary-color) 0%, #1e40af 100%);
            color: white;
            border-radius: 12px;
        }

        .stats-card .stats-icon {
            font-size: 2.5rem;
            opacity: 0.8;
        }

        .priority-badge {
            font-size: 0.75rem;
            padding: 4px 8px;
            border-radius: 20px;
            font-weight: 600;
        }

        .priority-faculty { background-color: #059669; color: white; }
        .priority-personnel { background-color: #d97706; color: white; }
        .priority-senior { background-color: #7c3aed; color: white; }
        .priority-regular { background-color: #64748b; color: white; }

        .queue-number {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary-color);
        }

        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255,255,255,.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .table-responsive {
            border-radius: 8px;
            overflow: hidden;
        }

        .table th {
            background-color: #f8fafc;
            border: none;
            font-weight: 600;
            color: var(--dark-color);
        }

        .footer {
            background-color: var(--dark-color);
            color: white;
            padding: 20px 0;
            margin-top: 50px;
        }

        @media (max-width: 768px) {
            .sidebar {
                position: fixed;
                left: -100%;
                transition: left 0.3s ease;
                z-index: 1050;
                width: 250px;
            }

            .sidebar.show {
                left: 0;
            }

            .main-content {
                margin-left: 0;
            }
        }
    </style>
    @stack('styles')
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container-fluid">
            @auth
                <button class="btn btn-outline-primary d-lg-none me-2" type="button" id="sidebarToggle">
                    <i class="fas fa-bars"></i>
                </button>
            @endauth
            
            <a class="navbar-brand" href="{{ route('dashboard') }}">
                <i class="fas fa-heartbeat me-2"></i>
                Clinic Management System
            </a>

            <div class="navbar-nav ms-auto">
                @auth
                    <div class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <img src="{{ auth()->user()->profile_picture ? asset('storage/' . auth()->user()->profile_picture) : 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->full_name) . '&background=2563eb&color=fff' }}" 
                                 class="rounded-circle me-2" width="32" height="32" alt="Profile">
                            <span>{{ auth()->user()->full_name }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="{{ route('profile.show') }}"><i class="fas fa-user me-2"></i>Profile</a></li>
                            <li><a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="fas fa-cog me-2"></i>Settings</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="fas fa-sign-out-alt me-2"></i>Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                @else
                    <a class="nav-link" href="{{ route('login') }}">Login</a>
                @endauth
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            @auth
                <!-- Sidebar -->
                <nav class="col-lg-2 d-lg-block sidebar collapse" id="sidebar">
                    <div class="position-sticky pt-3">
                        <ul class="nav flex-column">
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                                    <i class="fas fa-tachometer-alt"></i>
                                    Dashboard
                                </a>
                            </li>
                            
                            @if(auth()->user()->isStaff())
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('patients.*') ? 'active' : '' }}" href="{{ route('patients.index') }}">
                                        <i class="fas fa-users"></i>
                                        Patients
                                    </a>
                                </li>
                                
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('appointments.*') ? 'active' : '' }}" href="{{ route('appointments.index') }}">
                                        <i class="fas fa-calendar-check"></i>
                                        Appointments
                                    </a>
                                </li>
                                
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('medical-records.*') ? 'active' : '' }}" href="{{ route('medical-records.index') }}">
                                        <i class="fas fa-file-medical"></i>
                                        Medical Records
                                    </a>
                                </li>
                                
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('queue.*') ? 'active' : '' }}" href="{{ route('queue.management') }}">
                                        <i class="fas fa-list-ol"></i>
                                        Queue Management
                                    </a>
                                </li>
                            @endif
                            
                            @if(auth()->user()->isSuperAdmin())
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('admin.staff.*') ? 'active' : '' }}" href="{{ route('admin.staff.index') }}">
                                        <i class="fas fa-user-tie"></i>
                                        Staff Management
                                    </a>
                                </li>
                                
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('admin.analytics') ? 'active' : '' }}" href="{{ route('admin.analytics') }}">
                                        <i class="fas fa-chart-bar"></i>
                                        Analytics
                                    </a>
                                </li>
                                
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('admin.settings') ? 'active' : '' }}" href="{{ route('admin.settings') }}">
                                        <i class="fas fa-cogs"></i>
                                        System Settings
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </div>
                </nav>

                <!-- Main content -->
                <main class="col-lg-10 ms-sm-auto main-content">
            @else
                <main class="col-12">
            @endauth
                
                <!-- Flash Messages -->
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('warning'))
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        {{ session('warning') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('info'))
                    <div class="alert alert-info alert-dismissible fade show" role="alert">
                        <i class="fas fa-info-circle me-2"></i>
                        {{ session('info') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <!-- Page Content -->
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer mt-auto">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <p>&copy; 2024 University Clinic Management System. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-end">
                    <p>Powered by Laravel & Bootstrap</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Custom JS -->
    <script>
        // Sidebar toggle for mobile
        document.getElementById('sidebarToggle')?.addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('show');
        });

        // Auto-hide alerts after 5 seconds
        setTimeout(() => {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                if (alert.classList.contains('alert-success') || alert.classList.contains('alert-info')) {
                    const bootstrapAlert = new bootstrap.Alert(alert);
                    bootstrapAlert.close();
                }
            });
        }, 5000);

        // CSRF token setup for AJAX
        window.axios = window.axios || {};
        window.axios.defaults = window.axios.defaults || {};
        window.axios.defaults.headers = window.axios.defaults.headers || {};
        window.axios.defaults.headers.common = window.axios.defaults.headers.common || {};
        window.axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    </script>
    @stack('scripts')
</body>
</html>
