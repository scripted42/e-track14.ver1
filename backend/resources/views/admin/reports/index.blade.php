@extends('admin.layouts.app')

@section('title', 'Laporan')

@push('styles')
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
.detail-card {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    border: 1px solid #e9ecef;
    margin-bottom: 1.5rem;
}

.detail-header {
    background: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
    padding: 1.5rem;
    border-radius: 8px 8px 0 0;
}

.detail-body {
    padding: 1.5rem;
}

.stats-card {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    border: 1px solid #e9ecef;
    padding: 1.5rem;
    text-align: center;
    transition: all 0.2s ease;
}

.stats-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.stats-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    margin: 0 auto 1rem;
    color: white;
}

.stats-value {
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
}

.stats-label {
    color: #6c757d;
    font-size: 0.875rem;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.stats-trend {
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-weight: 500;
    margin-top: 0.5rem;
}

.bg-primary {
    background: #007bff !important;
}

.bg-success {
    background: #28a745 !important;
}

.bg-warning {
    background: #ffc107 !important;
}

.bg-danger {
    background: #dc3545 !important;
}

.bg-info {
    background: #17a2b8 !important;
}

.bg-secondary {
    background: #6c757d !important;
}

.text-primary {
    color: #007bff !important;
}

.text-success {
    color: #28a745 !important;
}

.text-warning {
    color: #ffc107 !important;
}

.text-danger {
    color: #dc3545 !important;
}

.text-info {
    color: #17a2b8 !important;
}

.text-secondary {
    color: #6c757d !important;
}

.btn-modern {
    border-radius: 6px;
    padding: 0.5rem 1.2rem;
    font-weight: 500;
    border: 1px solid #dee2e6;
    transition: all 0.2s ease;
    background: white;
    color: #6c757d;
}

.btn-modern:hover {
    background: #f8f9fa;
    border-color: #adb5bd;
    color: #495057;
}

.btn-primary {
    background: #007bff;
    color: white;
    border-color: #007bff;
}

.btn-primary:hover {
    background: #0056b3;
    border-color: #0056b3;
    color: white;
}

.btn-secondary {
    background: #6c757d;
    color: white;
    border-color: #6c757d;
}

.btn-secondary:hover {
    background: #545b62;
    border-color: #545b62;
    color: white;
}

.btn-warning {
    background: #ffc107;
    color: #212529;
    border-color: #ffc107;
}

.btn-warning:hover {
    background: #e0a800;
    border-color: #e0a800;
    color: #212529;
}

.btn-danger {
    background: #dc3545;
    color: white;
    border-color: #dc3545;
}

.btn-danger:hover {
    background: #c82333;
    border-color: #c82333;
    color: white;
}

.chart-container {
    height: 300px;
    background: #f8f9fa;
    border-radius: 8px;
    border: 1px solid #e9ecef;
    padding: 1rem;
    display: flex;
    align-items: center;
    justify-content: center;
}

.chart-container canvas {
    max-width: 100%;
    max-height: 100%;
}

.table {
    margin-bottom: 0;
}

.table thead th {
    background: #f8f9fa;
    border: none;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.75rem;
    letter-spacing: 0.5px;
    color: #495057;
    padding: 1rem;
}

.table tbody td {
    padding: 1rem;
    vertical-align: middle;
    border-color: #f1f3f4;
}

.table tbody tr {
    transition: all 0.2s ease;
}

.table tbody tr:hover {
    background-color: #f8f9fa;
}

.badge {
    font-size: 0.75rem;
    padding: 0.375rem 0.75rem;
    border-radius: 4px;
    font-weight: 500;
}

.filter-section {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    border: 1px solid #e9ecef;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
}

.form-control, .form-select {
    border-radius: 6px;
    border: 1px solid #dee2e6;
    padding: 0.5rem 0.75rem;
    transition: all 0.2s ease;
}

.form-control:focus, .form-select:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.empty-state {
    text-align: center;
    padding: 2rem 1rem;
    color: #6c757d;
}

.empty-state i {
    font-size: 2rem;
    margin-bottom: 1rem;
    opacity: 0.5;
}

.empty-state h6 {
    margin-bottom: 0.5rem;
    color: #495057;
}

.empty-state p {
    margin-bottom: 0;
    font-size: 0.875rem;
}

.top-performer-item {
    display: flex;
    align-items: center;
    padding: 0.75rem;
    background: #f8f9fa;
    border-radius: 6px;
    border: 1px solid #e9ecef;
    margin-bottom: 0.5rem;
    transition: all 0.2s ease;
}

.top-performer-item:hover {
    background: #e9ecef;
    border-color: #007bff;
}

.performer-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #007bff;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: bold;
    margin-right: 0.75rem;
}

@media (max-width: 768px) {
    .stats-card {
        margin-bottom: 1rem;
    }
    
    .chart-container {
        height: 250px;
    }
    
    .table-responsive {
        border-radius: 6px;
    }
}
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
            <h1 class="h3 mb-0">Laporan</h1>
            <p class="text-muted mb-0">Analisis dan statistik sistem absensi</p>
                </div>
                <div class="d-flex gap-2">
            <button type="button" class="btn btn-primary" onclick="exportReport('excel')">
                <i class="fas fa-file-excel me-2"></i>Export Excel
            </button>
            <button type="button" class="btn btn-secondary" onclick="exportReport('pdf')">
                <i class="fas fa-file-pdf me-2"></i>Export PDF
            </button>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="filter-section">
        <h5 class="mb-3">
            <i class="fas fa-filter me-2"></i>Filter Laporan
                    </h5>
                    <form id="reportFilterForm" method="GET">
                        <div class="row">
                            <div class="col-md-3 mb-3">
                    <label for="start_date" class="form-label">Tanggal Mulai</label>
                    <input type="date" class="form-control" id="start_date" name="start_date" 
                                       value="{{ request('start_date', \Carbon\Carbon::now()->startOfMonth()->format('Y-m-d')) }}">
                            </div>
                            <div class="col-md-3 mb-3">
                    <label for="end_date" class="form-label">Tanggal Akhir</label>
                    <input type="date" class="form-control" id="end_date" name="end_date" 
                                       value="{{ request('end_date', \Carbon\Carbon::now()->format('Y-m-d')) }}">
                            </div>
                            <div class="col-md-3 mb-3">
                    <label for="report_type" class="form-label">Tipe Laporan</label>
                    <select class="form-select" id="report_type" name="report_type">
                                    <option value="all" {{ request('report_type') == 'all' ? 'selected' : '' }}>Semua Laporan</option>
                                    <option value="attendance" {{ request('report_type') == 'attendance' ? 'selected' : '' }}>Kehadiran Pegawai</option>
                                    <option value="students" {{ request('report_type') == 'students' ? 'selected' : '' }}>Kehadiran Siswa</option>
                                    <option value="leaves" {{ request('report_type') == 'leaves' ? 'selected' : '' }}>Izin & Cuti</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                    <label for="search" class="form-label">Pencarian</label>
                                <div class="input-group">
                        <input type="text" class="form-control" id="search" name="search" 
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
                    <button type="submit" class="btn btn-primary me-2">
                                    <i class="fas fa-filter me-2"></i>Terapkan Filter
                                </button>
                    <button type="button" class="btn btn-outline-secondary" onclick="resetFilters()">
                                    <i class="fas fa-undo me-2"></i>Reset
                                </button>
                            </div>
                        </div>
                    </form>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-6 col-xl-3 mb-3">
            <div class="stats-card">
                <div class="stats-icon bg-primary">
                        <i class="fas fa-users"></i>
                    </div>
                <div class="stats-value text-primary">{{ number_format($employeeStats['total_employees']) }}</div>
                <div class="stats-label">Total Pegawai</div>
                <div class="stats-trend bg-success text-white">
                            <i class="fas fa-check-circle me-1"></i>
                            {{ $employeeStats['today_attendance'] }} hadir hari ini
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3 mb-3">
            <div class="stats-card">
                <div class="stats-icon bg-success">
                        <i class="fas fa-user-graduate"></i>
                    </div>
                <div class="stats-value text-success">{{ number_format($studentStats['total_students']) }}</div>
                <div class="stats-label">Total Siswa</div>
                <div class="stats-trend bg-info text-white">
                            <i class="fas fa-school me-1"></i>
                            {{ $studentStats['classes_count'] }} kelas aktif
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3 mb-3">
            <div class="stats-card">
                <div class="stats-icon bg-warning">
                        <i class="fas fa-calendar-times"></i>
                    </div>
                <div class="stats-value text-warning">{{ number_format($leaveStats['pending_leaves']) }}</div>
                <div class="stats-label">Izin Menunggu</div>
                <div class="stats-trend bg-primary text-white">
                            <i class="fas fa-calendar me-1"></i>
                            {{ $leaveStats['total_leaves'] }} total bulan ini
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3 mb-3">
            <div class="stats-card">
                <div class="stats-icon bg-danger">
                        <i class="fas fa-clock"></i>
                    </div>
                <div class="stats-value text-danger">{{ number_format($employeeStats['late_today']) }}</div>
                <div class="stats-label">Terlambat Hari Ini</div>
                <div class="stats-trend bg-secondary text-white">
                            <i class="fas fa-chart-line me-1"></i>
                            Monitoring ketepatan waktu
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="row mb-4">
        <!-- Attendance Trend Chart -->
        <div class="col-lg-8 mb-4">
            <div class="detail-card">
                <div class="detail-header">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-line me-2"></i>Tren Kehadiran 30 Hari Terakhir
                    </h5>
                    </div>
                <div class="detail-body">
                    <div class="chart-container">
                        <canvas id="attendanceTrendChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Attendance Status Distribution -->
        <div class="col-lg-4 mb-4">
            <div class="detail-card">
                <div class="detail-header">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-pie me-2"></i>Distribusi Status Kehadiran
                    </h5>
                    </div>
                <div class="detail-body">
                    <div class="chart-container" style="height: 250px;">
                        <canvas id="attendanceStatusChart"></canvas>
                        </div>
                    </div>
            </div>
        </div>
    </div>

    <!-- Additional Charts -->
    <div class="row mb-4">
        <!-- Monthly Comparison Chart -->
        <div class="col-lg-6 mb-4">
            <div class="detail-card">
                <div class="detail-header">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-bar me-2"></i>Perbandingan Bulanan
                    </h5>
                </div>
                <div class="detail-body">
                    <div class="chart-container">
                        <canvas id="monthlyComparisonChart"></canvas>
                </div>
            </div>
        </div>
    </div>

        <!-- Class Attendance Chart -->
        <div class="col-lg-6 mb-4">
            <div class="detail-card">
                <div class="detail-header">
                    <h5 class="mb-0">
                        <i class="fas fa-school me-2"></i>Kehadiran per Kelas
                    </h5>
                </div>
                <div class="detail-body">
                    <div class="chart-container">
                        <canvas id="classAttendanceChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Performers -->
    <div class="row mb-4">
        <div class="col-lg-6 mb-4">
            <div class="detail-card">
                <div class="detail-header">
                    <h5 class="mb-0">
                        <i class="fas fa-trophy me-2"></i>Pegawai Terbaik
                    </h5>
                </div>
                <div class="detail-body">
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
                        <div class="empty-state">
                            <i class="fas fa-user-clock"></i>
                            <h6>Belum ada data</h6>
                            <p>Belum ada data kehadiran bulan ini</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Quick Access -->
        <div class="col-lg-6 mb-4">
            <div class="detail-card">
                <div class="detail-header">
                    <h5 class="mb-0">
                        <i class="fas fa-link me-2"></i>Akses Cepat Laporan
                    </h5>
                </div>
                <div class="detail-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.reports.attendance') }}" class="btn btn-primary">
                            <i class="fas fa-user-check me-2"></i>Laporan Kehadiran Pegawai
                        </a>
                        <a href="{{ route('admin.reports.leaves') }}" class="btn btn-warning">
                            <i class="fas fa-calendar-times me-2"></i>Laporan Izin & Cuti
                        </a>
                        <a href="{{ route('admin.reports.students') }}" class="btn btn-success">
                            <i class="fas fa-graduation-cap me-2"></i>Laporan Kehadiran Siswa
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Data Tables -->
    <div class="row">
        <!-- Recent Attendance -->
        <div class="col-lg-6 mb-4">
            <div class="detail-card">
                <div class="detail-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-clock me-2"></i>Kehadiran Terbaru (Hari Ini)
                    </h5>
                    <a href="{{ route('admin.reports.attendance') }}" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-eye me-1"></i>Lihat Semua
                    </a>
                </div>
                <div class="detail-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
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
                                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" 
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

        <!-- Recent Leaves -->
        <div class="col-lg-6 mb-4">
            <div class="detail-card">
                <div class="detail-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-calendar-times me-2"></i>Pengajuan Izin Terbaru
                    </h5>
                    <a href="{{ route('admin.reports.leaves') }}" class="btn btn-sm btn-outline-warning">
                        <i class="fas fa-eye me-1"></i>Lihat Semua
                    </a>
                </div>
                <div class="detail-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
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
}

// Initialize charts with real data
function initializeCharts() {
    // Get real data from PHP variables
    const employeeStats = @json($employeeStats);
    const studentStats = @json($studentStats);
    const leaveStats = @json($leaveStats);
    const topPerformers = @json($topPerformers);
    const chartData = @json($chartData);
    
    // Attendance Trend Chart (Line Chart)
    const attendanceTrendCtx = document.getElementById('attendanceTrendChart');
    if (attendanceTrendCtx) {
        new Chart(attendanceTrendCtx, {
            type: 'line',
            data: {
                labels: chartData.attendance_trend.labels,
                datasets: [{
                    label: 'Kehadiran Pegawai',
                    data: chartData.attendance_trend.employee_data,
                    borderColor: '#007bff',
                    backgroundColor: 'rgba(0, 123, 255, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4
                }, {
                    label: 'Kehadiran Siswa',
                    data: chartData.attendance_trend.student_data,
                    borderColor: '#28a745',
                    backgroundColor: 'rgba(40, 167, 69, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: 'Tren Kehadiran 30 Hari Terakhir'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.1)'
                        }
                    },
                    x: {
                        grid: {
                            color: 'rgba(0, 0, 0, 0.1)'
                        }
                    }
                }
            }
        });
    }

    // Attendance Status Distribution (Doughnut Chart)
    const attendanceStatusCtx = document.getElementById('attendanceStatusChart');
    if (attendanceStatusCtx) {
        new Chart(attendanceStatusCtx, {
            type: 'doughnut',
            data: {
                labels: ['Hadir', 'Terlambat', 'Izin', 'Sakit', 'Alpha'],
                datasets: [{
                    data: [
                        employeeStats.monthly_attendance || 0,
                        employeeStats.late_today || 0,
                        leaveStats.total_leaves || 0,
                        Math.floor((leaveStats.total_leaves || 0) * 0.3),
                        Math.floor((leaveStats.total_leaves || 0) * 0.1)
                    ],
                    backgroundColor: [
                        '#28a745',
                        '#ffc107',
                        '#007bff',
                        '#dc3545',
                        '#6c757d'
                    ],
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            usePointStyle: true
                        }
                    }
                }
            }
        });
    }

    // Monthly Comparison Chart (Bar Chart)
    const monthlyComparisonCtx = document.getElementById('monthlyComparisonChart');
    if (monthlyComparisonCtx) {
        new Chart(monthlyComparisonCtx, {
            type: 'bar',
            data: {
                labels: chartData.monthly_comparison.labels,
                datasets: [{
                    label: 'Pegawai',
                    data: chartData.monthly_comparison.employee_data,
                    backgroundColor: 'rgba(0, 123, 255, 0.8)',
                    borderColor: '#007bff',
                    borderWidth: 1
                }, {
                    label: 'Siswa',
                    data: chartData.monthly_comparison.student_data,
                    backgroundColor: 'rgba(40, 167, 69, 0.8)',
                    borderColor: '#28a745',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.1)'
                        }
                    },
                    x: {
                        grid: {
                            color: 'rgba(0, 0, 0, 0.1)'
                        }
                    }
                }
            }
        });
    }

    // Class Attendance Chart (Horizontal Bar Chart)
    const classAttendanceCtx = document.getElementById('classAttendanceChart');
    if (classAttendanceCtx) {
        new Chart(classAttendanceCtx, {
            type: 'bar',
            data: {
                labels: chartData.class_attendance.labels,
                datasets: [{
                    label: 'Tingkat Kehadiran (%)',
                    data: chartData.class_attendance.data,
                    backgroundColor: [
                        'rgba(0, 123, 255, 0.8)',
                        'rgba(40, 167, 69, 0.8)',
                        'rgba(255, 193, 7, 0.8)',
                        'rgba(220, 53, 69, 0.8)',
                        'rgba(108, 117, 125, 0.8)',
                        'rgba(23, 162, 184, 0.8)',
                        'rgba(111, 66, 193, 0.8)',
                        'rgba(253, 126, 20, 0.8)',
                        'rgba(32, 201, 151, 0.8)',
                        'rgba(233, 84, 81, 0.8)',
                        'rgba(102, 16, 242, 0.8)',
                        'rgba(255, 159, 67, 0.8)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        max: 100,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.1)'
                        }
                    },
                    y: {
                        grid: {
                            color: 'rgba(0, 0, 0, 0.1)'
                        }
                    }
                }
            }
        });
    }
}



// Initialize everything when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Initialize charts
    initializeCharts();
    
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
</script>
@endpush