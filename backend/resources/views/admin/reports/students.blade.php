@extends('admin.layouts.app')

@section('title', 'Laporan Kehadiran Siswa')

@push('styles')
<style>
.filter-card {
    background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
    border: none;
    border-radius: 1rem;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.data-table {
    background: white;
    border-radius: 1rem;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    overflow: hidden;
}

.table th {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
    border: none;
    font-weight: 600;
    padding: 1rem;
    text-transform: uppercase;
    font-size: 0.75rem;
    letter-spacing: 0.5px;
}

.table td {
    padding: 1rem;
    vertical-align: middle;
    border-color: #e2e8f0;
}

.table tbody tr:hover {
    background-color: #f0fdf4;
    transform: translateX(2px);
    transition: all 0.3s ease;
}

.status-badge {
    padding: 0.375rem 0.75rem;
    border-radius: 0.5rem;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
}

.status-hadir {
    background-color: #dcfce7;
    color: #166534;
}

.status-izin {
    background-color: #dbeafe;
    color: #1e40af;
}

.status-sakit {
    background-color: #fee2e2;
    color: #991b1b;
}

.status-alpha {
    background-color: #f3f4f6;
    color: #374151;
}

.class-badge {
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    color: white;
    padding: 0.375rem 0.75rem;
    border-radius: 0.5rem;
    font-size: 0.75rem;
    font-weight: 600;
}

.stats-card {
    background: linear-gradient(135deg, #ffffff 0%, #f0fdf4 100%);
    border: 1px solid #bbf7d0;
    border-radius: 1rem;
    padding: 1.5rem;
    text-align: center;
    transition: all 0.3s ease;
    height: 100%;
}

.stats-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px -3px rgba(16, 185, 129, 0.1);
    border-color: #10b981;
}

.stats-icon {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1rem;
    font-size: 1.25rem;
}

.stats-value {
    font-size: 2rem;
    font-weight: bold;
    margin-bottom: 0.5rem;
}

.stats-label {
    color: #064e3b;
    font-size: 0.875rem;
    font-weight: 500;
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

.rate-excellent { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
.rate-good { background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); }
.rate-average { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }
.rate-poor { background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); }

.btn-modern {
    border-radius: 0.5rem;
    padding: 0.75rem 1.5rem;
    font-weight: 500;
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.btn-modern:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.form-control, .form-select {
    border-radius: 0.5rem;
    border: 1px solid #d1d5db;
    padding: 0.75rem 1rem;
    transition: all 0.3s ease;
}

.form-control:focus, .form-select:focus {
    border-color: #10b981;
    box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
}

.class-stats-card {
    border: 1px solid #e2e8f0;
    border-radius: 0.75rem;
    padding: 1.25rem;
    margin-bottom: 1rem;
    transition: all 0.3s ease;
    background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
}

.class-stats-card:hover {
    border-color: #10b981;
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.1);
    transform: translateY(-2px);
}

.student-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: bold;
    margin-right: 1rem;
}

.teacher-info {
    background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
    padding: 0.5rem 0.75rem;
    border-radius: 0.5rem;
    border: 1px solid #bbf7d0;
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
                    <h1 class="h3 mb-1 text-gray-800">🎓 Laporan Kehadiran Siswa</h1>
                    <p class="text-muted mb-0">Pantau kehadiran siswa berdasarkan kelas dan periode tertentu</p>
                </div>
                <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-secondary btn-modern">
                    <i class="fas fa-arrow-left me-2"></i>Kembali ke Dashboard
                </a>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="filter-card card">
                <div class="card-body">
                    <h5 class="card-title mb-3">
                        <i class="fas fa-filter text-success me-2"></i>Filter Laporan
                    </h5>
                    <form method="GET" action="{{ route('admin.reports.students') }}">
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label for="year" class="form-label fw-semibold">Tahun</label>
                                <select class="form-select" id="year" name="year">
                                    @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="month" class="form-label fw-semibold">Bulan</label>
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
                                <label for="class_name" class="form-label fw-semibold">Kelas</label>
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
                                <button type="submit" class="btn btn-success btn-modern w-100">
                                    <i class="fas fa-search me-2"></i>Filter
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Overall Statistics -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="stats-card">
                <div class="stats-icon bg-success bg-gradient text-white">
                    <i class="fas fa-user-graduate"></i>
                </div>
                <div class="stats-value text-success">{{ $studentStats->count() }}</div>
                <div class="stats-label">Total Siswa</div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stats-card">
                <div class="stats-icon bg-primary bg-gradient text-white">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stats-value text-primary">{{ $studentStats->sum('present') }}</div>
                <div class="stats-label">Total Hadir</div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stats-card">
                <div class="stats-icon bg-warning bg-gradient text-white">
                    <i class="fas fa-times-circle"></i>
                </div>
                <div class="stats-value text-warning">{{ $studentStats->sum('absent') }}</div>
                <div class="stats-label">Total Tidak Hadir</div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stats-card">
                <div class="stats-icon bg-info bg-gradient text-white">
                    <i class="fas fa-school"></i>
                </div>
                <div class="stats-value text-info">{{ $classStats->count() }}</div>
                <div class="stats-label">Kelas Aktif</div>
            </div>
        </div>
    </div>

    <!-- Class Statistics -->
    @if($classStats->count() > 0)
    <div class="row mb-4">
        <div class="col-12">
            <div class="data-table">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-bar me-2"></i>Statistik Berdasarkan Kelas
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($classStats as $class => $stats)
                            <div class="col-md-4 col-lg-3 mb-3">
                                <div class="class-stats-card">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <span class="class-badge">{{ $class }}</span>
                                        <span class="fw-bold text-success">{{ $stats['total_records'] }}</span>
                                    </div>
                                    <div class="row text-center">
                                        <div class="col-6">
                                            <div class="text-success fw-semibold">{{ $stats['present'] }}</div>
                                            <small class="text-muted">Hadir</small>
                                        </div>
                                        <div class="col-6">
                                            <div class="text-warning fw-semibold">{{ $stats['absent'] }}</div>
                                            <small class="text-muted">Tidak Hadir</small>
                                        </div>
                                    </div>
                                    <div class="mt-3">
                                        <div class="progress" style="height: 8px;">
                                            <div class="progress-bar bg-success" role="progressbar" 
                                                 style="width: {{ $stats['attendance_rate'] }}%"></div>
                                        </div>
                                        <div class="text-center mt-1">
                                            <small class="text-muted">{{ $stats['attendance_rate'] }}% kehadiran</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Individual Student Statistics -->
    @if($studentStats->count() > 0)
    <div class="row mb-4">
        <div class="col-12">
            <div class="data-table">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-users me-2"></i>Statistik Individual Siswa
                    </h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Siswa</th>
                                <th>Kelas</th>
                                <th>Total Hari</th>
                                <th>Hadir</th>
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
                                    <td><span class="badge bg-warning">{{ $stat['absent'] }}</span></td>
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
    </div>
    @endif

    <!-- Detailed Attendance Records -->
    @if($attendance->count() > 0)
    <div class="row">
        <div class="col-12">
            <div class="data-table">
                <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-list me-2"></i>Detail Rekaman Kehadiran Siswa
                    </h5>
                    <span class="badge bg-light text-dark">{{ $attendance->count() }} record</span>
                </div>
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
                                            $statusClass = '';
                                            switch($record->status) {
                                                case 'hadir':
                                                    $statusClass = 'status-hadir';
                                                    break;
                                                case 'izin':
                                                    $statusClass = 'status-izin';
                                                    break;
                                                case 'sakit':
                                                    $statusClass = 'status-sakit';
                                                    break;
                                                case 'alpha':
                                                    $statusClass = 'status-alpha';
                                                    break;
                                                default:
                                                    $statusClass = 'status-alpha';
                                            }
                                        @endphp
                                        <span class="status-badge {{ $statusClass }}">
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
    </div>
    @else
    <div class="row">
        <div class="col-12">
            <div class="data-table">
                <div class="card-body text-center py-5">
                    <i class="fas fa-user-graduate fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Tidak Ada Data Kehadiran Siswa</h5>
                    <p class="text-muted mb-0">Belum ada data kehadiran siswa untuk periode yang dipilih.</p>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Summary Information -->
    @if($attendance->count() > 0)
    <div class="row mt-4">
        <div class="col-12">
            <div class="alert alert-success border-0" style="background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);">
                <div class="d-flex align-items-center">
                    <i class="fas fa-chart-line text-success me-3" style="font-size: 1.5rem;"></i>
                    <div>
                        <h6 class="mb-1 text-success">📈 Ringkasan Kehadiran Siswa</h6>
                        <p class="mb-0 text-success">
                            Periode ini terdapat <strong>{{ $attendance->count() }} rekaman kehadiran</strong> dari 
                            <strong>{{ $studentStats->count() }} siswa</strong> di 
                            <strong>{{ $classStats->count() }} kelas</strong> yang berbeda.
                            @if($studentStats->count() > 0)
                                Rata-rata tingkat kehadiran: <strong>{{ round($studentStats->avg('attendance_rate'), 1) }}%</strong>
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
// Export functionality placeholder
function exportReport() {
    const year = document.getElementById('year').value;
    const month = document.getElementById('month').value;
    const className = document.getElementById('class_name').value;
    
    const params = new URLSearchParams({
        year: year,
        month: month,
        ...(className && { class_name: className })
    });
    
    // For now, just show a message
    alert('Fitur export sedang dalam pengembangan');
    
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
    
    // Add sorting functionality to tables
    const tables = document.querySelectorAll('.table');
    tables.forEach(table => {
        // Table sorting functionality can be added here
    });
});
</script>
@endpush