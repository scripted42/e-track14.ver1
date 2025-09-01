@extends('admin.layouts.app')

@section('title', 'Laporan')

@push('styles')
<style>
.stat-card {
    background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
    border: none;
    border-radius: 1rem;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    transition: all 0.3s ease;
    height: 100%;
    overflow: hidden;
}

.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
}

.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    margin-bottom: 1rem;
}

.stat-value {
    font-size: 2.5rem;
    font-weight: bold;
    margin-bottom: 0.5rem;
    line-height: 1;
}

.stat-label {
    color: #64748b;
    font-size: 0.875rem;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.stat-trend {
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
    border-radius: 0.375rem;
    font-weight: 500;
}

.bg-primary-gradient {
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
}

.bg-success-gradient {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
}

.bg-warning-gradient {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
}

.bg-info-gradient {
    background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);
}

.bg-purple-gradient {
    background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
}

.bg-pink-gradient {
    background: linear-gradient(135deg, #ec4899 0%, #db2777 100%);
}

.filter-card {
    background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
    border: none;
    border-radius: 1rem;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    margin-bottom: 2rem;
}

.filter-card .form-control, .filter-card .form-select {
    border-radius: 0.5rem;
    border: 1px solid #d1d5db;
    padding: 0.75rem 1rem;
    transition: all 0.3s ease;
    background: white;
}

.filter-card .form-control:focus, .filter-card .form-select:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    transform: translateY(-1px);
}

.search-highlight {
    background-color: #fef3c7;
    padding: 0.1rem 0.2rem;
    border-radius: 0.25rem;
}

.report-section {
    background: white;
    border-radius: 1rem;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    margin-bottom: 2rem;
    overflow: hidden;
    transition: all 0.3s ease;
}

.report-section:hover {
    box-shadow: 0 10px 25px -3px rgba(0, 0, 0, 0.1);
}

.section-header {
    background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
    padding: 1.5rem;
    border-bottom: 1px solid #e2e8f0;
}

.section-content {
    padding: 1.5rem;
}

.chart-container {
    height: 300px;
    background: linear-gradient(135deg, #f8fafc 0%, #ffffff 100%);
    border-radius: 0.5rem;
    border: 1px solid #e2e8f0;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #64748b;
}

.top-performer-item {
    display: flex;
    align-items: center;
    padding: 1rem;
    background: linear-gradient(135deg, #f8fafc 0%, #ffffff 100%);
    border-radius: 0.5rem;
    border: 1px solid #e2e8f0;
    margin-bottom: 0.75rem;
    transition: all 0.3s ease;
}

.top-performer-item:hover {
    transform: translateX(5px);
    border-color: #3b82f6;
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.15);
}

.performer-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: bold;
    margin-right: 1rem;
}

.btn-modern {
    border-radius: 0.5rem;
    padding: 0.75rem 1.5rem;
    font-weight: 500;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    border: 2px solid transparent;
}

.btn-modern:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.table {
    margin-bottom: 0;
}

.table thead th {
    background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
    border: none;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.75rem;
    letter-spacing: 0.5px;
    color: #475569;
    padding: 1rem;
}

.table tbody td {
    padding: 1rem;
    vertical-align: middle;
    border-color: #f1f5f9;
}

.table tbody tr {
    transition: all 0.3s ease;
}

.table tbody tr:hover {
    background-color: #f8fafc;
    transform: translateX(2px);
}

.badge {
    font-size: 0.75rem;
    padding: 0.375rem 0.75rem;
    border-radius: 0.5rem;
    font-weight: 500;
}

.tooltip-custom {
    animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-5px); }
    to { opacity: 1; transform: translateY(0); }
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in {
    animation: fadeInUp 0.6s ease-out;
}

.animate-delay-1 { animation-delay: 0.1s; }
.animate-delay-2 { animation-delay: 0.2s; }
.animate-delay-3 { animation-delay: 0.3s; }
.animate-delay-4 { animation-delay: 0.4s; }
.animate-delay-5 { animation-delay: 0.5s; }
.animate-delay-6 { animation-delay: 0.6s; }

/* Loading states */
.btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9999;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s ease;
}

.loading-overlay.show {
    opacity: 1;
    visibility: visible;
}

.loading-spinner {
    background: white;
    padding: 2rem;
    border-radius: 1rem;
    text-align: center;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
}

/* Responsive design */
@media (max-width: 768px) {
    .stat-card {
        margin-bottom: 1rem;
    }
    
    .section-header {
        padding: 1rem;
    }
    
    .section-content {
        padding: 1rem;
    }
    
    .filter-card .row .col-md-3,
    .filter-card .row .col-md-4 {
        margin-bottom: 1rem;
    }
    
    .btn-modern {
        width: 100%;
        justify-content: center;
        margin-bottom: 0.5rem;
    }
    
    .table-responsive {
        border-radius: 0.5rem;
    }
}

/* Print styles */
@media print {
    .filter-card,
    .btn,
    .section-header .btn {
        display: none !important;
    }
    
    .report-section {
        box-shadow: none;
        border: 1px solid #ccc;
        margin-bottom: 1rem;
        page-break-inside: avoid;
    }
    
    .table {
        font-size: 0.8rem;
    }
}
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-1 text-gray-800">📊 Dashboard Laporan</h1>
                    <p class="text-muted mb-0">Pantau statistik kehadiran dan kinerja sistem secara real-time</p>
                </div>
                <div class="d-flex gap-2">
                    <div class="badge bg-success px-3 py-2">
                        <i class="fas fa-calendar-check me-1"></i>
                        {{ \Carbon\Carbon::now()->format('d M Y') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced Filter Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="filter-card card">
                <div class="card-body">
                    <h5 class="card-title mb-3">
                        <i class="fas fa-filter text-primary me-2"></i>Filter & Pencarian Laporan
                    </h5>
                    <form id="reportFilterForm" method="GET">
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label for="start_date" class="form-label fw-semibold">
                                    <i class="fas fa-calendar-alt me-1"></i>Tanggal Mulai
                                </label>
                                <input type="date" class="form-control form-control-lg" id="start_date" name="start_date" 
                                       value="{{ request('start_date', \Carbon\Carbon::now()->startOfMonth()->format('Y-m-d')) }}">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="end_date" class="form-label fw-semibold">
                                    <i class="fas fa-calendar-alt me-1"></i>Tanggal Akhir
                                </label>
                                <input type="date" class="form-control form-control-lg" id="end_date" name="end_date" 
                                       value="{{ request('end_date', \Carbon\Carbon::now()->format('Y-m-d')) }}">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="report_type" class="form-label fw-semibold">
                                    <i class="fas fa-chart-bar me-1"></i>Tipe Laporan
                                </label>
                                <select class="form-select form-control-lg" id="report_type" name="report_type">
                                    <option value="all" {{ request('report_type') == 'all' ? 'selected' : '' }}>Semua Laporan</option>
                                    <option value="attendance" {{ request('report_type') == 'attendance' ? 'selected' : '' }}>Kehadiran Pegawai</option>
                                    <option value="students" {{ request('report_type') == 'students' ? 'selected' : '' }}>Kehadiran Siswa</option>
                                    <option value="leaves" {{ request('report_type') == 'leaves' ? 'selected' : '' }}>Izin & Cuti</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="search" class="form-label fw-semibold">
                                    <i class="fas fa-search me-1"></i>Pencarian
                                </label>
                                <div class="input-group">
                                    <input type="text" class="form-control form-control-lg" id="search" name="search" 
                                           placeholder="Cari nama, email, kelas..." value="{{ request('search') }}">
                                    <button class="btn btn-primary" type="submit">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-9">
                                <div class="d-flex gap-2 flex-wrap">
                                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setDateRange('today')">
                                        <i class="fas fa-calendar-day me-1"></i>Hari Ini
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setDateRange('week')">
                                        <i class="fas fa-calendar-week me-1"></i>Minggu Ini
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setDateRange('month')">
                                        <i class="fas fa-calendar me-1"></i>Bulan Ini
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setDateRange('year')">
                                        <i class="fas fa-calendar-alt me-1"></i>Tahun Ini
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-3 text-end">
                                <button type="submit" class="btn btn-primary btn-lg me-2">
                                    <i class="fas fa-filter me-2"></i>Terapkan Filter
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-lg" onclick="resetFilters()">
                                    <i class="fas fa-undo me-2"></i>Reset
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <!-- Employee Statistics -->
        <div class="col-md-6 col-xl-3 mb-3">
            <div class="stat-card animate-fade-in">
                <div class="card-body text-center">
                    <div class="stat-icon bg-primary-gradient text-white mx-auto">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-value text-primary">{{ number_format($employeeStats['total_employees']) }}</div>
                    <div class="stat-label">Total Pegawai</div>
                    <div class="mt-2">
                        <small class="text-success">
                            <i class="fas fa-check-circle me-1"></i>
                            {{ $employeeStats['today_attendance'] }} hadir hari ini
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3 mb-3">
            <div class="stat-card animate-fade-in animate-delay-1">
                <div class="card-body text-center">
                    <div class="stat-icon bg-success-gradient text-white mx-auto">
                        <i class="fas fa-user-graduate"></i>
                    </div>
                    <div class="stat-value text-success">{{ number_format($studentStats['total_students']) }}</div>
                    <div class="stat-label">Total Siswa</div>
                    <div class="mt-2">
                        <small class="text-info">
                            <i class="fas fa-school me-1"></i>
                            {{ $studentStats['classes_count'] }} kelas aktif
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3 mb-3">
            <div class="stat-card animate-fade-in animate-delay-2">
                <div class="card-body text-center">
                    <div class="stat-icon bg-warning-gradient text-white mx-auto">
                        <i class="fas fa-calendar-times"></i>
                    </div>
                    <div class="stat-value text-warning">{{ number_format($leaveStats['pending_leaves']) }}</div>
                    <div class="stat-label">Izin Menunggu</div>
                    <div class="mt-2">
                        <small class="text-primary">
                            <i class="fas fa-calendar me-1"></i>
                            {{ $leaveStats['total_leaves'] }} total bulan ini
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3 mb-3">
            <div class="stat-card animate-fade-in animate-delay-3">
                <div class="card-body text-center">
                    <div class="stat-icon bg-info-gradient text-white mx-auto">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-value text-info">{{ number_format($employeeStats['late_today']) }}</div>
                    <div class="stat-label">Terlambat Hari Ini</div>
                    <div class="mt-2">
                        <small class="text-muted">
                            <i class="fas fa-chart-line me-1"></i>
                            Monitoring ketepatan waktu
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Monthly Statistics -->
    <div class="row mb-4">
        <div class="col-lg-6 mb-3">
            <div class="stat-card animate-fade-in animate-delay-4">
                <div class="card-body text-center">
                    <div class="stat-icon bg-purple-gradient text-white mx-auto">
                        <i class="fas fa-chart-bar"></i>
                    </div>
                    <div class="stat-value text-purple">{{ number_format($employeeStats['monthly_attendance']) }}</div>
                    <div class="stat-label">Kehadiran Pegawai Bulan Ini</div>
                    <div class="mt-2">
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-purple" role="progressbar" 
                                 style="width: {{ $employeeStats['total_employees'] > 0 ? ($employeeStats['monthly_attendance'] / $employeeStats['total_employees']) * 100 : 0 }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-3">
            <div class="stat-card animate-fade-in animate-delay-5">
                <div class="card-body text-center">
                    <div class="stat-icon bg-pink-gradient text-white mx-auto">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <div class="stat-value text-pink">{{ number_format($studentStats['monthly_attendance']) }}</div>
                    <div class="stat-label">Kehadiran Siswa Bulan Ini</div>
                    <div class="mt-2">
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-pink" role="progressbar" 
                                 style="width: {{ $studentStats['total_students'] > 0 ? ($studentStats['monthly_attendance'] / $studentStats['total_students']) * 100 : 0 }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts and Reports Section -->
    <div class="row">
        <!-- Attendance Trend Chart -->
        <div class="col-lg-8 mb-4">
            <div class="report-section animate-fade-in animate-delay-6">
                <div class="section-header">
                    <h5 class="mb-0 d-flex align-items-center">
                        <i class="fas fa-chart-line text-primary me-2"></i>
                        Tren Kehadiran 30 Hari Terakhir
                    </h5>
                </div>
                <div class="section-content">
                    <div class="chart-container">
                        <div class="text-center">
                            <i class="fas fa-chart-area fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Grafik tren kehadiran akan ditampilkan di sini</p>
                            <small class="text-muted">Fitur visualisasi data sedang dalam pengembangan</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Performers -->
        <div class="col-lg-4 mb-4">
            <div class="report-section animate-fade-in animate-delay-6">
                <div class="section-header">
                    <h5 class="mb-0 d-flex align-items-center">
                        <i class="fas fa-trophy text-warning me-2"></i>
                        Pegawai Terbaik
                    </h5>
                </div>
                <div class="section-content">
                    @if($topPerformers->count() > 0)
                        @foreach($topPerformers->take(5) as $index => $performer)
                            <div class="top-performer-item">
                                <div class="performer-avatar">
                                    {{ substr($performer->name, 0, 1) }}
                                </div>
                                <div class="flex-grow-1">
                                    <div class="fw-semibold">{{ $performer->name }}</div>
                                    <small class="text-muted">{{ $performer->days_present }} hari hadir</small>
                                </div>
                                <div class="text-end">
                                    @if($index == 0)
                                        <i class="fas fa-crown text-warning"></i>
                                    @elseif($index == 1)
                                        <i class="fas fa-medal text-secondary"></i>
                                    @elseif($index == 2)
                                        <i class="fas fa-award text-warning"></i>
                                    @else
                                        <span class="badge bg-light text-dark">{{ $index + 1 }}</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-user-clock fa-2x text-muted mb-2"></i>
                            <p class="text-muted">Belum ada data kehadiran bulan ini</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Tables Section -->
    <div class="row">
        <!-- Recent Attendance Records -->
        <div class="col-lg-6 mb-4">
            <div class="report-section">
                <div class="section-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 d-flex align-items-center">
                        <i class="fas fa-clock text-success me-2"></i>
                        Kehadiran Terbaru (Hari Ini)
                    </h5>
                    <a href="{{ route('admin.reports.attendance') }}" class="btn btn-sm btn-outline-success">
                        <i class="fas fa-eye me-1"></i>Lihat Semua
                    </a>
                </div>
                <div class="section-content p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Pegawai</th>
                                    <th>Waktu</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $recentAttendance = \App\Models\Attendance::with('user')
                                        ->whereDate('timestamp', today())
                                        ->orderBy('timestamp', 'desc')
                                        ->take(5)
                                        ->get();
                                @endphp
                                @forelse($recentAttendance as $record)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-2" 
                                                     style="width: 30px; height: 30px; font-size: 0.7rem;">
                                                    {{ substr($record->user->name, 0, 1) }}
                                                </div>
                                                <div>
                                                    <div class="fw-semibold">{{ $record->user->name }}</div>
                                                    <small class="text-muted">{{ $record->user->email }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="fw-semibold">{{ \Carbon\Carbon::parse($record->timestamp)->format('H:i') }}</span>
                                        </td>
                                        <td>
                                            @php
                                                $statusClass = match($record->status) {
                                                    'hadir' => 'bg-success',
                                                    'terlambat' => 'bg-warning',
                                                    'izin' => 'bg-info',
                                                    'sakit' => 'bg-danger',
                                                    default => 'bg-secondary'
                                                };
                                            @endphp
                                            <span class="badge {{ $statusClass }}">{{ ucfirst($record->status) }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-3 text-muted">
                                            <i class="fas fa-calendar-times me-2"></i>Belum ada kehadiran hari ini
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Leave Requests -->
        <div class="col-lg-6 mb-4">
            <div class="report-section">
                <div class="section-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 d-flex align-items-center">
                        <i class="fas fa-calendar-times text-warning me-2"></i>
                        Pengajuan Izin Terbaru
                    </h5>
                    <a href="{{ route('admin.reports.leaves') }}" class="btn btn-sm btn-outline-warning">
                        <i class="fas fa-eye me-1"></i>Lihat Semua
                    </a>
                </div>
                <div class="section-content p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Pegawai</th>
                                    <th>Tipe</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $recentLeaves = \App\Models\Leave::with('user')
                                        ->orderBy('created_at', 'desc')
                                        ->take(5)
                                        ->get();
                                @endphp
                                @forelse($recentLeaves as $leave)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="bg-warning text-white rounded-circle d-flex align-items-center justify-content-center me-2" 
                                                     style="width: 30px; height: 30px; font-size: 0.7rem;">
                                                    {{ substr($leave->user->name, 0, 1) }}
                                                </div>
                                                <div>
                                                    <div class="fw-semibold">{{ $leave->user->name }}</div>
                                                    <small class="text-muted">{{ \Carbon\Carbon::parse($leave->start_date)->format('d M') }} - {{ \Carbon\Carbon::parse($leave->end_date)->format('d M') }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark">{{ ucfirst(str_replace('_', ' ', $leave->leave_type)) }}</span>
                                        </td>
                                        <td>
                                            @php
                                                $statusClass = match($leave->status) {
                                                    'disetujui' => 'bg-success',
                                                    'ditolak' => 'bg-danger',
                                                    'menunggu' => 'bg-warning',
                                                    default => 'bg-secondary'
                                                };
                                            @endphp
                                            <span class="badge {{ $statusClass }}">{{ ucfirst($leave->status) }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-3 text-muted">
                                            <i class="fas fa-inbox me-2"></i>Belum ada pengajuan izin
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Student Attendance -->
    <div class="row">
        <div class="col-12 mb-4">
            <div class="report-section">
                <div class="section-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 d-flex align-items-center">
                        <i class="fas fa-graduation-cap text-info me-2"></i>
                        Kehadiran Siswa Terbaru (Hari Ini)
                    </h5>
                    <a href="{{ route('admin.reports.students') }}" class="btn btn-sm btn-outline-info">
                        <i class="fas fa-eye me-1"></i>Lihat Semua
                    </a>
                </div>
                <div class="section-content p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Siswa</th>
                                    <th>Kelas</th>
                                    <th>Status</th>
                                    <th>Waktu</th>
                                    <th>Guru Pengajar</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $recentStudentAttendance = \App\Models\StudentAttendance::with(['student', 'teacher'])
                                        ->whereDate('created_at', today())
                                        ->orderBy('created_at', 'desc')
                                        ->take(8)
                                        ->get();
                                @endphp
                                @forelse($recentStudentAttendance as $record)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="bg-info text-white rounded-circle d-flex align-items-center justify-content-center me-2" 
                                                     style="width: 30px; height: 30px; font-size: 0.7rem;">
                                                    {{ substr($record->student->name, 0, 1) }}
                                                </div>
                                                <div>
                                                    <div class="fw-semibold">{{ $record->student->name }}</div>
                                                    <small class="text-muted">ID: {{ $record->student->id }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td><span class="badge bg-primary">{{ $record->student->class_name }}</span></td>
                                        <td>
                                            @php
                                                $statusClass = match($record->status) {
                                                    'hadir' => 'bg-success',
                                                    'izin' => 'bg-info',
                                                    'sakit' => 'bg-danger',
                                                    'alpha' => 'bg-secondary',
                                                    default => 'bg-secondary'
                                                };
                                            @endphp
                                            <span class="badge {{ $statusClass }}">{{ ucfirst($record->status) }}</span>
                                        </td>
                                        <td>
                                            <span class="fw-semibold">{{ \Carbon\Carbon::parse($record->created_at)->format('H:i') }}</span>
                                        </td>
                                        <td>
                                            @if($record->teacher)
                                                <div>
                                                    <div class="fw-semibold">{{ $record->teacher->name }}</div>
                                                    <small class="text-muted">{{ $record->teacher->email }}</small>
                                                </div>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-3 text-muted">
                                            <i class="fas fa-user-graduate me-2"></i>Belum ada kehadiran siswa hari ini
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Access Reports -->
    <div class="row">
        <div class="col-12">
            <div class="report-section">
                <div class="section-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 d-flex align-items-center">
                            <i class="fas fa-file-alt text-success me-2"></i>
                            Akses Cepat Laporan & Export
                        </h5>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-outline-success btn-sm" onclick="exportReport('excel')">
                                <i class="fas fa-file-excel me-1"></i>Export Excel
                            </button>
                            <button type="button" class="btn btn-outline-danger btn-sm" onclick="exportReport('pdf')">
                                <i class="fas fa-file-pdf me-1"></i>Export PDF
                            </button>
                            <button type="button" class="btn btn-outline-info btn-sm" onclick="exportReport('csv')">
                                <i class="fas fa-file-csv me-1"></i>Export CSV
                            </button>
                        </div>
                    </div>
                </div>
                <div class="section-content">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <div class="card border-primary">
                                <div class="card-body text-center">
                                    <i class="fas fa-user-check fa-3x text-primary mb-3"></i>
                                    <h6 class="card-title">Laporan Kehadiran Pegawai</h6>
                                    <p class="card-text text-muted">Analisis kehadiran, keterlambatan, dan performa pegawai</p>
                                    <a href="{{ route('admin.reports.attendance') }}" class="btn btn-outline-primary btn-modern w-100">
                                        <i class="fas fa-eye me-2"></i>Lihat Detail
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card border-warning">
                                <div class="card-body text-center">
                                    <i class="fas fa-calendar-times fa-3x text-warning mb-3"></i>
                                    <h6 class="card-title">Laporan Izin & Cuti</h6>
                                    <p class="card-text text-muted">Monitoring pengajuan izin, cuti, dan perjalanan dinas</p>
                                    <a href="{{ route('admin.reports.leaves') }}" class="btn btn-outline-warning btn-modern w-100">
                                        <i class="fas fa-eye me-2"></i>Lihat Detail
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card border-success">
                                <div class="card-body text-center">
                                    <i class="fas fa-graduation-cap fa-3x text-success mb-3"></i>
                                    <h6 class="card-title">Laporan Kehadiran Siswa</h6>
                                    <p class="card-text text-muted">Statistik kehadiran siswa berdasarkan kelas dan periode</p>
                                    <a href="{{ route('admin.reports.students') }}" class="btn btn-outline-success btn-modern w-100">
                                        <i class="fas fa-eye me-2"></i>Lihat Detail
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Summary Information -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="alert alert-info border-0" style="background: linear-gradient(135deg, #e0f2fe 0%, #b3e5fc 100%);">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-info-circle text-info me-3" style="font-size: 1.5rem;"></i>
                                    <div>
                                        <h6 class="mb-1 text-info">📊 Informasi Filter Aktif</h6>
                                        <p class="mb-0 text-info">
                                            <strong>Periode:</strong> {{ $startDate->format('d M Y') }} - {{ $endDate->format('d M Y') }} |
                                            <strong>Tipe:</strong> {{ ucfirst($reportType) }} |
                                            <strong>Pencarian:</strong> {{ $search ?: 'Tidak ada' }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Date range filter functions
function setDateRange(range) {
    const startDate = document.getElementById('start_date');
    const endDate = document.getElementById('end_date');
    const today = new Date();
    
    switch(range) {
        case 'today':
            const todayStr = today.toISOString().split('T')[0];
            startDate.value = todayStr;
            endDate.value = todayStr;
            break;
            
        case 'week':
            const startOfWeek = new Date(today.setDate(today.getDate() - today.getDay()));
            const endOfWeek = new Date(today.setDate(today.getDate() - today.getDay() + 6));
            startDate.value = startOfWeek.toISOString().split('T')[0];
            endDate.value = endOfWeek.toISOString().split('T')[0];
            break;
            
        case 'month':
            const startOfMonth = new Date(today.getFullYear(), today.getMonth(), 1);
            const endOfMonth = new Date(today.getFullYear(), today.getMonth() + 1, 0);
            startDate.value = startOfMonth.toISOString().split('T')[0];
            endDate.value = endOfMonth.toISOString().split('T')[0];
            break;
            
        case 'year':
            const startOfYear = new Date(today.getFullYear(), 0, 1);
            const endOfYear = new Date(today.getFullYear(), 11, 31);
            startDate.value = startOfYear.toISOString().split('T')[0];
            endDate.value = endOfYear.toISOString().split('T')[0];
            break;
    }
    
    // Auto-submit form after setting date range
    document.getElementById('reportFilterForm').submit();
}

// Reset all filters
function resetFilters() {
    document.getElementById('start_date').value = '';
    document.getElementById('end_date').value = '';
    document.getElementById('report_type').value = 'all';
    document.getElementById('search').value = '';
    
    // Clear URL parameters and reload
    window.location.href = window.location.pathname;
}

// Real-time search functionality
function initializeSearch() {
    const searchInput = document.getElementById('search');
    const tables = document.querySelectorAll('.table tbody');
    
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        
        tables.forEach(tbody => {
            const rows = tbody.querySelectorAll('tr');
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                const shouldShow = text.includes(searchTerm);
                row.style.display = shouldShow ? '' : 'none';
            });
        });
    });
}

// Export functionality
function exportReport(type = 'all') {
    const startDate = document.getElementById('start_date').value;
    const endDate = document.getElementById('end_date').value;
    const reportType = document.getElementById('report_type').value;
    const search = document.getElementById('search').value;
    
    const params = new URLSearchParams({
        start_date: startDate,
        end_date: endDate,
        report_type: reportType,
        search: search,
        export: type
    });
    
    // Show loading message
    const exportBtn = event.target;
    const originalText = exportBtn.innerHTML;
    exportBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Mengekspor...';
    exportBtn.disabled = true;
    
    // For now, just show a message (future implementation: actual export)
    setTimeout(() => {
        alert(`Fitur export ${type.toUpperCase()} sedang dalam pengembangan\n\nData yang akan diekspor:\n- Tanggal: ${startDate || 'Semua'} - ${endDate || 'Semua'}\n- Tipe: ${reportType}\n- Pencarian: ${search || 'Tidak ada'}`);
        
        exportBtn.innerHTML = originalText;
        exportBtn.disabled = false;
    }, 2000);
    
    // Future implementation:
    // window.location.href = `/admin/reports/export?${params}`;
}

// Auto-refresh statistics every 5 minutes
let refreshInterval;

function startAutoRefresh() {
    refreshInterval = setInterval(function() {
        // Refresh only statistics cards, not the entire page
        refreshStatistics();
    }, 300000); // 5 minutes
}

function refreshStatistics() {
    // Future implementation: AJAX call to refresh statistics
    console.log('Statistics refresh - ' + new Date().toLocaleTimeString());
    
    // Show refresh indicator
    const indicator = document.createElement('div');
    indicator.className = 'position-fixed top-0 end-0 m-3 alert alert-info alert-dismissible';
    indicator.innerHTML = '<i class="fas fa-sync-alt me-2"></i>Memperbarui statistik... <button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
    document.body.appendChild(indicator);
    
    // Auto-remove after 3 seconds
    setTimeout(() => {
        if (indicator.parentNode) {
            indicator.remove();
        }
    }, 3000);
}

// Initialize everything when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Initialize search functionality
    initializeSearch();
    
    // Start auto-refresh
    startAutoRefresh();
    
    // Add hover effects to statistics cards
    const statCards = document.querySelectorAll('.stat-card');
    statCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px) scale(1.02)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });
    
    // Add smooth scrolling to navigation links
    const navLinks = document.querySelectorAll('a[href^="#"]');
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({ behavior: 'smooth' });
            }
        });
    });
    
    // Add loading states to buttons
    const buttons = document.querySelectorAll('.btn');
    buttons.forEach(button => {
        button.addEventListener('click', function() {
            if (this.type === 'submit' || this.classList.contains('btn-primary')) {
                const originalText = this.innerHTML;
                this.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Memproses...';
                this.disabled = true;
                
                // Re-enable after form submission or navigation
                setTimeout(() => {
                    this.innerHTML = originalText;
                    this.disabled = false;
                }, 2000);
            }
        });
    });
    
    // Enhanced table interactions
    const tableRows = document.querySelectorAll('.table tbody tr');
    tableRows.forEach(row => {
        row.addEventListener('mouseenter', function() {
            this.style.backgroundColor = '#f8f9fa';
            this.style.cursor = 'pointer';
        });
        
        row.addEventListener('mouseleave', function() {
            this.style.backgroundColor = '';
        });
    });
    
    // Add tooltips to badges and status indicators
    const badges = document.querySelectorAll('.badge');
    badges.forEach(badge => {
        badge.addEventListener('mouseenter', function() {
            // Simple tooltip implementation
            const tooltip = document.createElement('div');
            tooltip.className = 'tooltip-custom';
            tooltip.textContent = this.textContent + ' - ' + new Date().toLocaleString();
            tooltip.style.position = 'absolute';
            tooltip.style.backgroundColor = '#333';
            tooltip.style.color = 'white';
            tooltip.style.padding = '5px 10px';
            tooltip.style.borderRadius = '4px';
            tooltip.style.fontSize = '12px';
            tooltip.style.zIndex = '1000';
            tooltip.style.pointerEvents = 'none';
            
            document.body.appendChild(tooltip);
            
            const rect = this.getBoundingClientRect();
            tooltip.style.left = rect.left + 'px';
            tooltip.style.top = (rect.top - 30) + 'px';
            
            this.tooltipElement = tooltip;
        });
        
        badge.addEventListener('mouseleave', function() {
            if (this.tooltipElement) {
                this.tooltipElement.remove();
                this.tooltipElement = null;
            }
        });
    });
    
    // Form validation
    const form = document.getElementById('reportFilterForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            const startDate = document.getElementById('start_date').value;
            const endDate = document.getElementById('end_date').value;
            
            if (startDate && endDate && new Date(startDate) > new Date(endDate)) {
                e.preventDefault();
                alert('Tanggal mulai tidak boleh lebih besar dari tanggal akhir');
                return false;
            }
        });
    }
});

// Cleanup on page unload
window.addEventListener('beforeunload', function() {
    if (refreshInterval) {
        clearInterval(refreshInterval);
    }
});
</script>
@endpush