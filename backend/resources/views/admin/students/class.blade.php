@extends('admin.layouts.app')

@section('title', 'Siswa Kelas')

@push('styles')
<style>
.detail-card {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    border: 1px solid #e9ecef;
    margin-bottom: 1rem;
}

.detail-header {
    background: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
    padding: 1rem;
    border-radius: 8px 8px 0 0;
}

.detail-body {
    padding: 1rem;
}

.stats-card {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    border: 1px solid #e9ecef;
    padding: 1rem;
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

.bg-primary { background: #007bff !important; }
.bg-success { background: #28a745 !important; }
.bg-warning { background: #ffc107 !important; }
.bg-danger { background: #dc3545 !important; }
.bg-info { background: #17a2b8 !important; }

.text-primary { color: #007bff !important; }
.text-success { color: #28a745 !important; }
.text-warning { color: #ffc107 !important; }
.text-danger { color: #dc3545 !important; }
.text-info { color: #17a2b8 !important; }

.progress {
    height: 8px;
    border-radius: 4px;
}

.progress-bar {
    border-radius: 4px;
}

.table th {
    background: #f8f9fa;
    border: none;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.75rem;
    letter-spacing: 0.5px;
    color: #495057;
    padding: 1rem;
}

.table td {
    padding: 1rem;
    vertical-align: middle;
    border-color: #f1f3f4;
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
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="h3 mb-0">Siswa Kelas {{ $class->name }}</h1>
            <p class="text-muted mb-0">Data Siswa - {{ \Carbon\Carbon::now()->format('d F Y') }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.reports.class', $class->id) }}" class="btn btn-primary">
                <i class="fas fa-chart-bar me-2"></i>Laporan Kelas
            </a>
            <a href="{{ route('admin.students.my-class') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Kembali
            </a>
        </div>
    </div>

    <!-- Class Info -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="alert alert-info border-0 py-2">
                <div class="d-flex align-items-center">
                    <i class="fas fa-school fa-2x me-3 text-info"></i>
                    <div>
                        <h5 class="mb-1">Kelas: {{ $class->name }}</h5>
                        <p class="mb-0">Total Siswa: {{ $attendanceStats['total_students'] }} siswa | Kehadiran Hari Ini: {{ $attendanceStats['attendance_rate_today'] }}%</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-3">
        <div class="col-md-3 mb-2">
            <div class="stats-card">
                <div class="stats-icon bg-primary">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stats-value text-primary">{{ number_format($attendanceStats['total_students']) }}</div>
                <div class="stats-label">Total Siswa</div>
            </div>
        </div>
        <div class="col-md-3 mb-2">
            <div class="stats-card">
                <div class="stats-icon bg-success">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stats-value text-success">{{ number_format($attendanceStats['present_today']) }}</div>
                <div class="stats-label">Hadir Hari Ini</div>
            </div>
        </div>
        <div class="col-md-3 mb-2">
            <div class="stats-card">
                <div class="stats-icon bg-warning">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stats-value text-warning">{{ number_format($attendanceStats['late_today']) }}</div>
                <div class="stats-label">Terlambat</div>
            </div>
        </div>
        <div class="col-md-3 mb-2">
            <div class="stats-card">
                <div class="stats-icon bg-danger">
                    <i class="fas fa-times"></i>
                </div>
                <div class="stats-value text-danger">{{ number_format($attendanceStats['absent_today']) }}</div>
                <div class="stats-label">Tidak Hadir</div>
            </div>
        </div>
    </div>

    <!-- Attendance Overview -->
    <div class="row mb-3">
        <div class="col-lg-8 mb-3">
            <div class="detail-card">
                <div class="detail-header">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-pie me-2"></i>Ringkasan Kehadiran Hari Ini
                    </h5>
                </div>
                <div class="detail-body">
                    <div class="row">
                        <div class="col-md-6">
                            <canvas id="attendanceChart" height="200"></canvas>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="fw-semibold">Tingkat Kehadiran</span>
                                    <span class="badge bg-success">{{ $attendanceStats['attendance_rate_today'] }}%</span>
                                </div>
                                <div class="progress">
                                    <div class="progress-bar bg-success" style="width: {{ $attendanceStats['attendance_rate_today'] }}%"></div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="fw-semibold">Hadir</span>
                                    <span class="badge bg-success">{{ $attendanceStats['present_today'] }}</span>
                                </div>
                                <div class="progress">
                                    <div class="progress-bar bg-success" style="width: {{ $attendanceStats['total_students'] > 0 ? ($attendanceStats['present_today'] / $attendanceStats['total_students']) * 100 : 0 }}%"></div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="fw-semibold">Terlambat</span>
                                    <span class="badge bg-warning">{{ $attendanceStats['late_today'] }}</span>
                                </div>
                                <div class="progress">
                                    <div class="progress-bar bg-warning" style="width: {{ $attendanceStats['total_students'] > 0 ? ($attendanceStats['late_today'] / $attendanceStats['total_students']) * 100 : 0 }}%"></div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="fw-semibold">Tidak Hadir</span>
                                    <span class="badge bg-danger">{{ $attendanceStats['absent_today'] }}</span>
                                </div>
                                <div class="progress">
                                    <div class="progress-bar bg-danger" style="width: {{ $attendanceStats['total_students'] > 0 ? ($attendanceStats['absent_today'] / $attendanceStats['total_students']) * 100 : 0 }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="col-lg-4 mb-3">
            <div class="detail-card">
                <div class="detail-header">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>Informasi Cepat
                    </h5>
                </div>
                <div class="detail-body">
                    <div class="row text-center">
                        <div class="col-6 mb-3">
                            <div class="stats-value text-success">{{ $attendanceStats['attendance_rate_today'] }}%</div>
                            <div class="stats-label">Kehadiran</div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="stats-value text-info">{{ $attendanceStats['total_students'] }}</div>
                            <div class="stats-label">Total Siswa</div>
                        </div>
                        <div class="col-6">
                            <div class="stats-value text-warning">{{ $attendanceStats['late_today'] }}</div>
                            <div class="stats-label">Terlambat</div>
                        </div>
                        <div class="col-6">
                            <div class="stats-value text-danger">{{ $attendanceStats['absent_today'] }}</div>
                            <div class="stats-label">Absen</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Students List -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="detail-card">
                <div class="detail-header">
                    <h5 class="mb-0">
                        <i class="fas fa-user-graduate me-2"></i>Daftar Siswa Kelas {{ $class->name }}
                    </h5>
                </div>
                <div class="detail-body">
                    @if($students->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Siswa</th>
                                        <th>NISN</th>
                                        <th>Status Hari Ini</th>
                                        <th>Waktu Absen</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($students as $index => $student)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" 
                                                         style="width: 35px; height: 35px; font-size: 14px;">
                                                        {{ substr($student->name, 0, 1) }}
                                                    </div>
                                                    {{ $student->name }}
                                                </div>
                                            </td>
                                            <td>
                                                <span class="text-muted">{{ $student->nisn ?? 'N/A' }}</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-info">
                                                    <i class="fas fa-user me-1"></i>Siswa
                                                </span>
                                            </td>
                                            <td>
                                                <small class="text-muted">-</small>
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.students.show', $student->id) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-user-graduate fa-3x mb-3"></i>
                            <h5>Belum ada siswa di kelas ini</h5>
                            <p>Hubungi admin untuk menambahkan siswa ke kelas</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Report Period -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 bg-light">
                <div class="card-body text-center">
                    <div class="row align-items-center">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <div class="d-flex align-items-center justify-content-center">
                                <i class="fas fa-calendar-alt fa-2x text-primary me-3"></i>
                                <div class="text-start">
                                    <h6 class="mb-1">Tanggal Laporan</h6>
                                    <small class="text-muted">{{ \Carbon\Carbon::now()->format('d M Y') }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center justify-content-center">
                                <i class="fas fa-school fa-2x text-success me-3"></i>
                                <div class="text-start">
                                    <h6 class="mb-1">Kelas: {{ $class->name }}</h6>
                                    <small class="text-muted">{{ $attendanceStats['total_students'] }} siswa</small>
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
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Attendance Chart
const ctx = document.getElementById('attendanceChart');
if (ctx) {
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Hadir', 'Terlambat', 'Tidak Hadir'],
            datasets: [{
                data: [
                    {{ $attendanceStats['present_today'] }},
                    {{ $attendanceStats['late_today'] }},
                    {{ $attendanceStats['absent_today'] }}
                ],
                backgroundColor: [
                    '#28a745',
                    '#ffc107',
                    '#dc3545'
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
</script>
@endpush
