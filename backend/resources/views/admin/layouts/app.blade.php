<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Dashboard') - E-Track14</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        :root {
            --primary-color: #2563EB;
            --secondary-color: #10B981;
            --sidebar-width: 250px;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: #f8fafc;
        }
        
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: var(--sidebar-width);
            background: linear-gradient(135deg, var(--primary-color) 0%, #1e40af 100%);
            color: white;
            z-index: 1000;
            overflow-y: auto;
        }
        
        .sidebar .brand {
            padding: 1.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 0.75rem 1.5rem;
            border-radius: 0;
            transition: all 0.3s ease;
            text-decoration: none;
            display: flex;
            align-items: center;
        }
        
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            color: white;
            background-color: rgba(255, 255, 255, 0.1);
        }
        
        .sidebar .nav-link i {
            width: 20px;
            margin-right: 10px;
        }
        
        /* Submenu Styles */
        .sidebar .nav-item.has-submenu {
            position: relative;
        }
        
        .sidebar .nav-item.has-submenu > .nav-link {
            cursor: pointer;
        }
        
        .sidebar .nav-item.has-submenu > .nav-link::after {
            content: '\f107';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
            margin-left: auto;
            transition: transform 0.3s ease;
        }
        
        .sidebar .nav-item.has-submenu.open > .nav-link::after {
            transform: rotate(180deg);
        }
        
        .sidebar .submenu {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
            background-color: rgba(0, 0, 0, 0.1);
        }
        
        .sidebar .submenu.show {
            max-height: 300px;
        }
        
        .sidebar .submenu .nav-link {
            padding: 0.5rem 1.5rem 0.5rem 3rem;
            font-size: 0.9rem;
            border-left: 3px solid transparent;
        }
        
        .sidebar .submenu .nav-link:hover,
        .sidebar .submenu .nav-link.active {
            background-color: rgba(255, 255, 255, 0.15);
            border-left-color: rgba(255, 255, 255, 0.5);
        }
        
        .sidebar .submenu .nav-link i {
            width: 16px;
            margin-right: 8px;
            font-size: 0.8rem;
        }
        
        /* Mobile Responsiveness */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }
            
            .sidebar.show {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .sidebar .submenu {
                max-height: 200px;
            }
        }
        
        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
        }
        
        .navbar {
            background: white;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            border: none;
        }
        
        .content {
            padding: 2rem;
        }
        
        .card {
            border: none;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            border-radius: 12px;
        }
        
        .card-header {
            background: white;
            border-bottom: 1px solid #e5e7eb;
            font-weight: 600;
        }
        
        .stat-card {
            text-align: center;
            padding: 1.5rem;
        }
        
        .stat-card .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-size: 1.5rem;
        }
        
        .stat-card .stat-number {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }
        
        .stat-card .stat-label {
            color: #6b7280;
            font-size: 0.875rem;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-success {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
        }
        
        .table {
            margin-bottom: 0;
        }
        
        .badge {
            font-size: 0.75rem;
            padding: 0.375rem 0.75rem;
        }
        
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }
            
            .sidebar.show {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
            }
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <!-- Sidebar -->
    <nav class="sidebar">
        <div class="brand">
            <h4 class="mb-0">
                <i class="fas fa-school me-2"></i>
                E-Track14
            </h4>
            <small class="text-white-50">SMPN 14 Surabaya</small>
        </div>
        
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" 
                   href="{{ route('admin.dashboard') }}">
                    <i class="fas fa-tachometer-alt"></i>
                    Dashboard
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.attendance.*') ? 'active' : '' }}" 
                   href="{{ route('admin.attendance.index') }}">
                    <i class="fas fa-clock"></i>
                    Absensi Pegawai
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.attendance.qr') ? 'active' : '' }}" 
                   href="{{ route('admin.attendance.qr') }}">
                    <i class="fas fa-qrcode"></i>
                    Kelola QR Code
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.students.*') ? 'active' : '' }}" 
                   href="{{ route('admin.students.index') }}">
                    <i class="fas fa-user-graduate"></i>
                    Siswa
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.classrooms.*') ? 'active' : '' }}" 
                   href="{{ route('admin.classrooms.index') }}">
                    <i class="fas fa-chalkboard"></i>
                    Manajemen Kelas
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.leaves.*') ? 'active' : '' }}" 
                   href="{{ route('admin.leaves.index') }}">
                    <i class="fas fa-calendar-times"></i>
                    Manajemen Izin
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" 
                   href="{{ route('admin.users.index') }}">
                    <i class="fas fa-users"></i>
                    Manajemen Staff
                </a>
            </li>
            
            <li class="nav-item has-submenu {{ request()->routeIs('admin.reports.*') ? 'open' : '' }}">
                <a class="nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}" 
                   href="#" onclick="toggleSubmenu(this)">
                    <i class="fas fa-chart-bar"></i>
                    Laporan
                </a>
                <ul class="submenu {{ request()->routeIs('admin.reports.*') ? 'show' : '' }}">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.reports.index') ? 'active' : '' }}" 
                           href="{{ route('admin.reports.index') }}">
                            <i class="fas fa-tachometer-alt"></i>
                            Dashboard Laporan
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.reports.attendance') ? 'active' : '' }}" 
                           href="{{ route('admin.reports.attendance') }}">
                            <i class="fas fa-users"></i>
                            Kehadiran Pegawai
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.reports.leaves') ? 'active' : '' }}" 
                           href="{{ route('admin.reports.leaves') }}">
                            <i class="fas fa-calendar-times"></i>
                            Izin & Cuti
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.reports.students') ? 'active' : '' }}" 
                           href="{{ route('admin.reports.students') }}">
                            <i class="fas fa-user-graduate"></i>
                            Kehadiran Siswa
                        </a>
                    </li>
                </ul>
            </li>
            
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}" 
                   href="{{ route('admin.settings.index') }}">
                    <i class="fas fa-cog"></i>
                    Pengaturan
                </a>
            </li>
        </ul>
    </nav>
    
    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Navbar -->
        <nav class="navbar navbar-expand-lg navbar-light">
            <div class="container-fluid">
                <button class="navbar-toggler d-lg-none" type="button" id="sidebarToggle">
                    <span class="navbar-toggler-icon"></span>
                </button>
                
                <div class="d-flex align-items-center ms-auto">
                    <div class="dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" 
                           href="#" role="button" data-bs-toggle="dropdown">
                            <div class="me-2">
                                <div class="fw-bold">{{ auth()->user()->name }}</div>
                                <div class="text-muted small">{{ auth()->user()->role->role_name }}</div>
                            </div>
                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" 
                                 style="width: 40px; height: 40px;">
                                {{ substr(auth()->user()->name, 0, 1) }}
                            </div>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <form action="{{ route('admin.logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item">
                                        <i class="fas fa-sign-out-alt me-2"></i>
                                        Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>
        
        <!-- Page Content -->
        <div class="content">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            
            @yield('content')
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Sidebar toggle for mobile
        document.getElementById('sidebarToggle')?.addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('show');
        });
        
        // Submenu toggle functionality
        function toggleSubmenu(element) {
            event.preventDefault();
            const navItem = element.closest('.nav-item');
            const submenu = navItem.querySelector('.submenu');
            
            // Toggle the open class
            navItem.classList.toggle('open');
            
            // Toggle the submenu visibility
            if (submenu.classList.contains('show')) {
                submenu.classList.remove('show');
            } else {
                submenu.classList.add('show');
            }
        }
        
        // Initialize submenu state on page load
        document.addEventListener('DOMContentLoaded', function() {
            // Check if any submenu should be open based on current route
            const currentRoute = window.location.pathname;
            
            if (currentRoute.includes('/admin/reports')) {
                const reportNavItem = document.querySelector('.nav-item.has-submenu');
                const reportSubmenu = document.querySelector('.nav-item.has-submenu .submenu');
                
                if (reportNavItem && !reportNavItem.classList.contains('open')) {
                    reportNavItem.classList.add('open');
                    if (reportSubmenu && !reportSubmenu.classList.contains('show')) {
                        reportSubmenu.classList.add('show');
                    }
                }
            }
        });
        
        // CSRF token setup for AJAX
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (csrfToken && window.axios) {
            window.axios.defaults.headers.common['X-CSRF-TOKEN'] = csrfToken.content;
        }
    </script>
    
    @stack('scripts')
</body>
</html>