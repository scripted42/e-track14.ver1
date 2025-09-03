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
            --primary-color: #6366f1;
            --primary-hover: #4f46e5;
            --secondary-color: #10b981;
            --sidebar-width: 280px;
            --sidebar-bg: #ffffff;
            --sidebar-text: #374151;
            --sidebar-text-muted: #6b7280;
            --sidebar-border: #e5e7eb;
            --sidebar-hover: #f3f4f6;
            --sidebar-active: #eef2ff;
            --sidebar-active-text: #4338ca;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: #f9fafb;
        }
        
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: var(--sidebar-width);
            background: var(--sidebar-bg);
            color: var(--sidebar-text);
            z-index: 1000;
            overflow-y: auto;
            border-right: 1px solid var(--sidebar-border);
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
        }
        
        .sidebar .brand {
            padding: 2rem 1.5rem 1.5rem;
            border-bottom: 1px solid var(--sidebar-border);
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-hover) 100%);
            color: white;
            margin: 0;
        }
        
        .sidebar .brand h4 {
            font-weight: 700;
            font-size: 1.25rem;
            margin: 0;
        }
        
        .sidebar .brand small {
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.875rem;
        }
        
        .sidebar .nav-link {
            color: var(--sidebar-text);
            padding: 0.875rem 1.5rem;
            border-radius: 0;
            transition: all 0.2s ease;
            text-decoration: none;
            display: flex;
            align-items: center;
            font-weight: 500;
            font-size: 0.875rem;
            border: none;
            background: none;
        }
        
        .sidebar .nav-link:hover {
            color: var(--primary-color);
            background-color: var(--sidebar-hover);
        }
        
        .sidebar .nav-link.active {
            color: var(--sidebar-active-text);
            background-color: var(--sidebar-active);
            border-right: 3px solid var(--primary-color);
        }
        
        .sidebar .nav-link i {
            width: 20px;
            margin-right: 12px;
            font-size: 1rem;
        }
        
        /* Submenu Styles */
        .sidebar .nav-item.has-submenu {
            position: relative;
        }
        
        .sidebar .nav-item.has-submenu > .nav-link {
            cursor: pointer;
            font-weight: 600;
        }
        
        .sidebar .nav-item.has-submenu > .nav-link::after {
            content: '\f107';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
            margin-left: auto;
            transition: transform 0.2s ease;
            color: var(--sidebar-text-muted);
        }
        
        .sidebar .nav-item.has-submenu.open > .nav-link::after {
            transform: rotate(180deg);
        }
        
        .sidebar .submenu {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
            background-color: #f8fafc;
            border-left: 2px solid var(--sidebar-border);
        }
        
        .sidebar .submenu.show {
            max-height: 400px;
        }
        
        .sidebar .submenu .nav-link {
            padding: 0.75rem 1.5rem 0.75rem 3.5rem;
            font-size: 0.8rem;
            font-weight: 500;
            color: var(--sidebar-text-muted);
            border: none;
            background: none;
        }
        
        .sidebar .submenu .nav-link:hover {
            color: var(--primary-color);
            background-color: var(--sidebar-hover);
        }
        
        .sidebar .submenu .nav-link.active {
            color: var(--sidebar-active-text);
            background-color: var(--sidebar-active);
            border-left: 3px solid var(--primary-color);
        }
        
        .sidebar .submenu .nav-link i {
            width: 16px;
            margin-right: 10px;
            font-size: 0.875rem;
        }
        
        /* Mobile Responsiveness */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            }
            
            .sidebar.show {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .sidebar .submenu {
                max-height: 250px;
            }
            
            .sidebar .brand {
                padding: 1.5rem 1rem;
            }
            
            .sidebar .nav-link {
                padding: 0.75rem 1rem;
            }
            
            .sidebar .submenu .nav-link {
                padding: 0.625rem 1rem 0.625rem 2.5rem;
            }
        }
        
        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
        }
        
        .navbar {
            background: white;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
            border: none;
            border-bottom: 1px solid var(--sidebar-border);
        }
        
        .navbar .dropdown-toggle {
            color: var(--sidebar-text);
            text-decoration: none;
        }
        
        .navbar .dropdown-toggle:hover {
            color: var(--primary-color);
        }
        
        .content {
            padding: 2rem;
        }
        
        .card {
            border: 1px solid var(--sidebar-border);
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
            border-radius: 8px;
            background: white;
        }
        
        .card-header {
            background: white;
            border-bottom: 1px solid var(--sidebar-border);
            font-weight: 600;
            color: var(--sidebar-text);
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
            font-weight: 500;
            border-radius: 6px;
            transition: all 0.2s ease;
        }
        
        .btn-primary:hover {
            background-color: var(--primary-hover);
            border-color: var(--primary-hover);
            transform: translateY(-1px);
        }
        
        .btn-success {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
            font-weight: 500;
            border-radius: 6px;
            transition: all 0.2s ease;
        }
        
        .btn-success:hover {
            transform: translateY(-1px);
        }
        
        .table {
            margin-bottom: 0;
            border-radius: 8px;
            overflow: hidden;
        }
        
        .table thead th {
            background-color: #f8fafc;
            border-bottom: 2px solid var(--sidebar-border);
            font-weight: 600;
            color: var(--sidebar-text);
            padding: 1rem 0.75rem;
        }
        
        .table tbody td {
            padding: 0.875rem 0.75rem;
            border-bottom: 1px solid var(--sidebar-border);
            vertical-align: middle;
        }
        
        .table tbody tr:hover {
            background-color: #f8fafc;
        }
        
        .badge {
            font-size: 0.75rem;
            padding: 0.375rem 0.75rem;
            border-radius: 4px;
            font-weight: 500;
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
        
        /* Custom Pagination Styling */
        .pagination {
            justify-content: center;
            margin: 2rem 0;
        }
        
        .pagination .page-link {
            color: #6b7280;
            background-color: #ffffff;
            border: 1px solid #e5e7eb;
            padding: 0.5rem 0.75rem;
            margin: 0 0.125rem;
            border-radius: 0.375rem;
            font-size: 0.875rem;
            font-weight: 500;
            transition: all 0.2s ease;
            text-decoration: none;
            min-width: 2.5rem;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .pagination .page-link:hover {
            color: #2563eb;
            background-color: #eff6ff;
            border-color: #bfdbfe;
            transform: translateY(-1px);
        }
        
        .pagination .page-item.active .page-link {
            color: #ffffff;
            background-color: #2563eb;
            border-color: #2563eb;
            box-shadow: 0 2px 4px rgba(37, 99, 235, 0.2);
        }
        
        .pagination .page-item.disabled .page-link {
            color: #9ca3af;
            background-color: #f9fafb;
            border-color: #e5e7eb;
            cursor: not-allowed;
        }
        
        .pagination .page-item.disabled .page-link:hover {
            transform: none;
            background-color: #f9fafb;
            border-color: #e5e7eb;
        }
        
        /* Hide the default SVG icons and use Font Awesome instead */
        .pagination .page-link svg {
            display: none;
        }
        
        .pagination .page-link:before {
            font-family: "Font Awesome 6 Free";
            font-weight: 900;
        }
        
        .pagination .page-item:first-child .page-link:before {
            content: "\f104"; /* fa-chevron-left */
        }
        
        .pagination .page-item:last-child .page-link:before {
            content: "\f105"; /* fa-chevron-right */
        }
        
        /* Responsive pagination */
        @media (max-width: 768px) {
            .pagination .page-link {
                padding: 0.375rem 0.5rem;
                font-size: 0.75rem;
                min-width: 2rem;
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
            
            <!-- Absensi Submenu -->
            <li class="nav-item has-submenu {{ request()->routeIs('admin.attendance.*') || request()->routeIs('admin.student-attendance.*') ? 'open' : '' }}">
                <a class="nav-link {{ request()->routeIs('admin.attendance.*') || request()->routeIs('admin.student-attendance.*') ? 'active' : '' }}" 
                   href="#" onclick="toggleSubmenu(this)">
                    <i class="fas fa-clock"></i>
                    Absensi
                </a>
                <ul class="submenu {{ request()->routeIs('admin.attendance.*') || request()->routeIs('admin.student-attendance.*') ? 'show' : '' }}">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.attendance.index') ? 'active' : '' }}" 
                           href="{{ route('admin.attendance.index') }}">
                            <i class="fas fa-users"></i>
                            @if(auth()->user()->hasRole('Admin'))
                                Absensi Pegawai
                            @else
                                Manajemen Kehadiran
                            @endif
                        </a>
                    </li>
                    @if(auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Guru') || auth()->user()->hasRole('Kepala Sekolah') || auth()->user()->hasRole('Waka Kurikulum'))
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.student-attendance.*') ? 'active' : '' }}" 
                           href="{{ route('admin.student-attendance.index') }}">
                            <i class="fas fa-user-graduate"></i>
                            Absensi Siswa
                        </a>
                    </li>
                    @endif
                </ul>
            </li>
            
            <!-- Manajemen Submenu -->
            <li class="nav-item has-submenu {{ request()->routeIs('admin.students.*') || request()->routeIs('admin.classrooms.*') || request()->routeIs('admin.leaves.*') || request()->routeIs('admin.users.*') ? 'open' : '' }}">
                <a class="nav-link {{ request()->routeIs('admin.students.*') || request()->routeIs('admin.classrooms.*') || request()->routeIs('admin.leaves.*') || request()->routeIs('admin.users.*') ? 'active' : '' }}" 
                   href="#" onclick="toggleSubmenu(this)">
                    <i class="fas fa-cogs"></i>
                    Manajemen
                </a>
                <ul class="submenu {{ request()->routeIs('admin.students.*') || request()->routeIs('admin.classrooms.*') || request()->routeIs('admin.leaves.*') || request()->routeIs('admin.users.*') ? 'show' : '' }}">
                    @if(auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Guru') || auth()->user()->hasRole('Kepala Sekolah') || auth()->user()->hasRole('Waka Kurikulum'))
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.students.*') ? 'active' : '' }}" 
                           href="{{ route('admin.students.index') }}">
                            <i class="fas fa-user-graduate"></i>
                            Manajemen Siswa
                        </a>
                    </li>
                    @endif
                    @if(auth()->user()->hasRole('Admin'))
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.classrooms.*') ? 'active' : '' }}" 
                           href="{{ route('admin.classrooms.index') }}">
                            <i class="fas fa-chalkboard"></i>
                            Manajemen Kelas
                        </a>
                    </li>
                    @endif
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.leaves.*') ? 'active' : '' }}" 
                           href="{{ route('admin.leaves.index') }}">
                            <i class="fas fa-calendar-times"></i>
                            Manajemen Izin
                        </a>
                    </li>
                    @if(auth()->user()->hasRole('Admin'))
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" 
                           href="{{ route('admin.users.index') }}">
                            <i class="fas fa-users"></i>
                            Manajemen Staff
                        </a>
                    </li>
                    @endif
                </ul>
            </li>
            
            @if(auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Kepala Sekolah') || auth()->user()->hasRole('Waka Kurikulum'))
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
            @endif
            
            @if(auth()->user()->hasRole('Admin'))
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.attendance.qr') ? 'active' : '' }}" 
                   href="{{ route('admin.attendance.qr') }}">
                    <i class="fas fa-qrcode"></i>
                    Kelola QR Code
                </a>
            </li>
            @endif
            
            @if(auth()->user()->hasRole('Admin'))
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}" 
                   href="{{ route('admin.settings.index') }}">
                    <i class="fas fa-cog"></i>
                    Pengaturan
                </a>
            </li>
            @endif
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
            
            // Check for Absensi submenu (excluding QR routes)
            if ((currentRoute.includes('/admin/attendance') || currentRoute.includes('/admin/student-attendance')) && !currentRoute.includes('/admin/attendance/qr')) {
                const absensiNavItem = document.querySelector('.nav-item.has-submenu:first-of-type');
                const absensiSubmenu = absensiNavItem?.querySelector('.submenu');
                
                if (absensiNavItem && !absensiNavItem.classList.contains('open')) {
                    absensiNavItem.classList.add('open');
                    if (absensiSubmenu && !absensiSubmenu.classList.contains('show')) {
                        absensiSubmenu.classList.add('show');
                    }
                }
            }
            
            // Check for Manajemen submenu
            if (currentRoute.includes('/admin/students') || currentRoute.includes('/admin/classrooms') || 
                currentRoute.includes('/admin/leaves') || currentRoute.includes('/admin/users')) {
                const manajemenNavItems = document.querySelectorAll('.nav-item.has-submenu');
                const manajemenNavItem = manajemenNavItems[1]; // Second submenu is Manajemen
                const manajemenSubmenu = manajemenNavItem?.querySelector('.submenu');
                
                if (manajemenNavItem && !manajemenNavItem.classList.contains('open')) {
                    manajemenNavItem.classList.add('open');
                    if (manajemenSubmenu && !manajemenSubmenu.classList.contains('show')) {
                        manajemenSubmenu.classList.add('show');
                    }
                }
            }
            
            // Check for Reports submenu
            if (currentRoute.includes('/admin/reports')) {
                const reportNavItems = document.querySelectorAll('.nav-item.has-submenu');
                const reportNavItem = reportNavItems[reportNavItems.length - 1]; // Last submenu is Reports
                const reportSubmenu = reportNavItem?.querySelector('.submenu');
                
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