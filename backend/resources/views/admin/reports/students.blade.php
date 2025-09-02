@extends('admin.layouts.app')

@section('title', 'Laporan Kehadiran Siswa')

@push('styles')
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

.class-badge {
    background: #007bff;
    color: white;
    padding: 0.375rem 0.75rem;
    border-radius: 4px;
    font-size: 0.75rem;
    font-weight: 600;
}

.attendance-rate {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.rate-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.75rem;
    font-weight: bold;
    color: white;
}

.rate-excellent { background: #28a745; }
.rate-good { background: #007bff; }
.rate-average { background: #ffc107; }
.rate-poor { background: #dc3545; }

.class-stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1rem;
    margin-top: 1rem;
}

.class-stats-card {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 1.5rem;
    border: 1px solid #e9ecef;
    text-align: center;
    transition: all 0.2s ease;
}

.class-stats-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.class-stats-card h6 {
    margin-bottom: 1rem;
    color: #495057;
}

.class-stats-card .stats {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 0.5rem;
    margin-top: 1rem;
}

.class-stats-card .stat-item {
    text-align: center;
}

.class-stats-card .stat-value {
    font-size: 1.5rem;
    font-weight: 700;
    margin-bottom: 0.25rem;
}

.class-stats-card .stat-label {
    font-size: 0.75rem;
    color: #6c757d;
}

.progress {
    height: 8px;
    border-radius: 4px;
    background-color: #e9ecef;
    margin-top: 1rem;
}

.progress-bar {
    border-radius: 4px;
}

.student-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #007bff;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: bold;
    margin-right: 1rem;
}

.teacher-info {
    background: #f8f9fa;
    padding: 0.5rem 0.75rem;
    border-radius: 4px;
    border: 1px solid #e9ecef;
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

@media (max-width: 768px) {
    .stats-card {
        margin-bottom: 1rem;
    }
    
    .table-responsive {
        border-radius: 6px;
    }
    
    .class-stats-grid {
        grid-template-columns: 1fr;
    }
}
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Laporan Kehadiran Siswa</h1>
            <p class="text-muted mb-0">Monitoring dan analisis kehadiran siswa berdasarkan kelas</p>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-primary" onclick="exportReport()">
                <i class="fas fa-file-excel me-2"></i>Export Excel
            </button>
            <a href="{{ route('admin.reports.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Kembali
            </a>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="filter-section">
        <h5 class="mb-3">
            <i class="fas fa-filter me-2"></i>Filter Laporan
        </h5>
        <form method="GET" action="{{ route('admin.reports.students') }}">
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label for="year" class="form-label">Tahun</label>
                    <select class="form-select" id="year" name="year">
                        @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="month" class="form-label">Bulan</label>
                    <select class="form-select" id="month" name="month">
                        @php
                            $months = [
                                '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
                                '04' => 'April', '05' => 'Mei', '06' => 'Juni',
                                '07' => 'Juli', '08' => 'Agustus', '09' => 'September',
                                '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
                            ];
                        @endphp
                        @foreach($months as $num => $name)
                            <option value="{{ $num }}" {{ $month == $num ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="class_name" class="form-label">Kelas</label>
                    <select class="form-select" id="class_name" name="class_name">
                        <option value="">Semua Kelas</option>
                        @foreach($classes as $class)
                            <option value="{{ $class }}" {{ $className == $class ? 'selected' : '' }}>
                                {{ $class }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 mb-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search me-2"></i>Filter
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Overall Statistics -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="stats-card">
                <div class="stats-icon bg-success">
                    <i class="fas fa-user-graduate"></i>
                </div>
                <div class="stats-value text-success">{{ $studentStats->count() }}</div>
                <div class="stats-label">Total Siswa</div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stats-card">
                <div class="stats-icon bg-primary">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stats-value text-primary">{{ $studentStats->sum('present') }}</div>
                <div class="stats-label">Total Hadir</div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stats-card">
                <div class="stats-icon bg-warning">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stats-value text-warning">{{ $studentStats->sum('late') }}</div>
                <div class="stats-label">Total Terlambat</div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stats-card">
                <div class="stats-icon bg-danger">
                    <i class="fas fa-times-circle"></i>
                </div>
                <div class="stats-value text-danger">{{ $studentStats->sum('absent') }}</div>
                <div class="stats-label">Total Tidak Hadir</div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stats-card">
                <div class="stats-icon bg-info">
                    <i class="fas fa-school"></i>
                </div>
                <div class="stats-value text-info">{{ $classStats->count() }}</div>
                <div class="stats-label">Kelas Aktif</div>
            </div>
        </div>
    </div>

    <!-- Class Statistics -->
    @if($classStats->count() > 0)
    <div class="detail-card">
        <div class="detail-header">
            <h5 class="mb-0">
                <i class="fas fa-chart-bar me-2"></i>Statistik Berdasarkan Kelas
            </h5>
        </div>
        <div class="detail-body">
            <div class="class-stats-grid">
                @foreach($classStats as $class => $stats)
                    <div class="class-stats-card">
                        <h6>{{ $class }}</h6>
                        <div class="stats">
                            <div class="stat-item">
                                <div class="stat-value text-primary">{{ $stats['total_records'] }}</div>
                                <div class="stat-label">Total</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-value text-success">{{ $stats['present'] }}</div>
                                <div class="stat-label">Hadir</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-value text-warning">{{ $stats['late'] }}</div>
                                <div class="stat-label">Terlambat</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-value text-danger">{{ $stats['absent'] }}</div>
                                <div class="stat-label">Tidak Hadir</div>
                            </div>
                        </div>
                        <div class="progress">
                            <div class="progress-bar bg-success" role="progressbar" 
                                 style="width: {{ $stats['attendance_rate'] }}%"></div>
                        </div>
                        <div class="text-center mt-2">
                            <small class="text-muted">{{ $stats['attendance_rate'] }}% kehadiran</small>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- Individual Student Statistics -->
    @if($studentStats->count() > 0)
    <div class="detail-card">
        <div class="detail-header">
            <h5 class="mb-0">
                <i class="fas fa-users me-2"></i>Statistik Individual Siswa
            </h5>
        </div>
        <div class="detail-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                                            <thead>
                            <tr>
                                <th>Siswa</th>
                                <th>Kelas</th>
                                <th>Total Hari</th>
                                <th>Hadir</th>
                                <th>Terlambat</th>
                                <th>Tidak Hadir</th>
                                <th>Tingkat Kehadiran</th>
                            </tr>
                        </thead>
                    <tbody>
                        @foreach($studentStats as $stat)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="student-avatar">
                                            {{ substr($stat['student']->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="fw-semibold">{{ $stat['student']->name }}</div>
                                            <small class="text-muted">ID: {{ $stat['student']->id }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="class-badge">{{ $stat['student']->class_name }}</span>
                                </td>
                                <td><span class="fw-semibold">{{ $stat['total_days'] }}</span></td>
                                <td><span class="badge bg-success">{{ $stat['present'] }}</span></td>
                                <td><span class="badge bg-warning">{{ $stat['late'] }}</span></td>
                                <td><span class="badge bg-danger">{{ $stat['absent'] }}</span></td>
                                <td>
                                    <div class="attendance-rate">
                                        @php
                                            $rate = $stat['attendance_rate'];
                                            $class = $rate >= 95 ? 'rate-excellent' : 
                                                    ($rate >= 85 ? 'rate-good' : 
                                                    ($rate >= 70 ? 'rate-average' : 'rate-poor'));
                                        @endphp
                                        <div class="rate-circle {{ $class }}">
                                            {{ $rate }}%
                                        </div>
                                        <span class="fw-semibold">{{ $rate }}%</span>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    <!-- Detailed Attendance Records -->
    @if($attendance->count() > 0)
    <div class="detail-card">
        <div class="detail-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="fas fa-list me-2"></i>Detail Rekaman Kehadiran Siswa
            </h5>
            <span class="badge bg-primary">{{ $attendance->count() }} record</span>
        </div>
        <div class="detail-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Siswa</th>
                            <th>Kelas</th>
                            <th>Status</th>
                            <th>Guru Pengajar</th>
                            <th>Waktu Absen</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($attendance as $record)
                            <tr>
                                <td>
                                    <div class="fw-semibold">
                                        {{ \Carbon\Carbon::parse($record->created_at)->format('d M Y') }}
                                    </div>
                                    <small class="text-muted">
                                        {{ \Carbon\Carbon::parse($record->created_at)->format('l') }}
                                    </small>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="student-avatar" style="width: 32px; height: 32px; font-size: 0.7rem;">
                                            {{ substr($record->student->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="fw-semibold">{{ $record->student->name }}</div>
                                            <small class="text-muted">ID: {{ $record->student->id }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="class-badge">{{ $record->student->class_name }}</span>
                                </td>
                                <td>
                                    @php
                                        $statusClass = match($record->status) {
                                            'hadir' => 'bg-success',
                                            'terlambat' => 'bg-warning',
                                            'izin' => 'bg-info',
                                            'sakit' => 'bg-danger',
                                            'alpha' => 'bg-secondary',
                                            default => 'bg-secondary'
                                        };
                                    @endphp
                                    <span class="badge {{ $statusClass }}">
                                        {{ ucfirst($record->status) }}
                                    </span>
                                </td>
                                <td>
                                    @if($record->teacher)
                                        <div class="teacher-info">
                                            <div class="fw-semibold">{{ $record->teacher->name }}</div>
                                            <small class="text-muted">{{ $record->teacher->email }}</small>
                                        </div>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="fw-semibold">
                                        {{ \Carbon\Carbon::parse($record->created_at)->format('H:i:s') }}
                                    </span>
                                </td>
                                <td>
                                    <span class="text-muted">
                                        {{ $record->notes ?: '-' }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @else
    <div class="detail-card">
        <div class="detail-body">
            <div class="empty-state">
                <i class="fas fa-user-graduate"></i>
                <h6>Tidak Ada Data Kehadiran Siswa</h6>
                <p>Belum ada data kehadiran siswa untuk periode yang dipilih.</p>
            </div>
        </div>
    </div>
    @endif

    <!-- Summary Information -->
    @if($attendance->count() > 0)
    <div class="detail-card">
        <div class="detail-body">
            <div class="alert alert-info border-0" style="background: #e3f2fd;">
                <div class="d-flex align-items-center">
                    <i class="fas fa-chart-line text-info me-3" style="font-size: 1.5rem;"></i>
                    <div>
                        <h6 class="mb-1 text-info">📈 Ringkasan Kehadiran Siswa</h6>
                        <p class="mb-0 text-info">
                            Periode ini terdapat <strong>{{ $attendance->count() }} rekaman kehadiran</strong> dari 
                            <strong>{{ $studentStats->count() }} siswa</strong> di 
                            <strong>{{ $classStats->count() }} kelas</strong> yang berbeda.
                            @if($studentStats->count() > 0)
                                Rata-rata tingkat kehadiran: <strong>{{ round($studentStats->avg('attendance_rate'), 1) }}%</strong>
                                (Hadir: {{ $studentStats->sum('present') }}, Terlambat: {{ $studentStats->sum('late') }}, Tidak Hadir: {{ $studentStats->sum('absent') }})
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
// Export functionality
function exportReport() {
    const year = document.getElementById('year').value;
    const month = document.getElementById('month').value;
    const className = document.getElementById('class_name').value;
    
    const params = new URLSearchParams({
        year: year,
        month: month,
        ...(className && { class_name: className })
    });
    
    // Show loading message
    const exportBtn = event.target;
    const originalText = exportBtn.innerHTML;
    exportBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Mengekspor...';
    exportBtn.disabled = true;
    
    // For now, just show a message
    setTimeout(() => {
        alert('Fitur export sedang dalam pengembangan\n\nData yang akan diekspor:\n- Tahun: ' + year + '\n- Bulan: ' + month + '\n- Kelas: ' + (className || 'Semua'));
        
        exportBtn.innerHTML = originalText;
        exportBtn.disabled = false;
    }, 2000);
    
    // Future implementation:
    // window.location.href = `/admin/reports/export/students?${params}`;
}

// Auto-submit form when filters change (optional)
document.addEventListener('DOMContentLoaded', function() {
    const filterInputs = document.querySelectorAll('#year, #month, #class_name');
    filterInputs.forEach(input => {
        input.addEventListener('change', function() {
            // Auto-submit can be enabled here if desired
            // this.form.submit();
        });
    });
});
</script>
@endpush