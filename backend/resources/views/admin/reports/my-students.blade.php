@extends('admin.layouts.app')

@section('title', 'Laporan Siswa Saya')

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
            <h1 class="h3 mb-0">Laporan Siswa Saya</h1>
            <p class="text-muted mb-0">Data Siswa yang Diampu - {{ \Carbon\Carbon::now()->format('d F Y') }}</p>
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

    <!-- Teacher Info -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="alert alert-info border-0 py-2">
                <div class="d-flex align-items-center">
                    <i class="fas fa-chalkboard-teacher fa-2x me-3 text-info"></i>
                    <div>
                        <h5 class="mb-1">Guru: {{ auth()->user()->name }}</h5>
                        <p class="mb-0">Kelas yang Diampu: {{ $myClasses->count() }} kelas | Total Siswa: {{ $myStats['total_students'] }} siswa</p>
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
                <div class="stats-value text-primary">{{ number_format($myStats['total_students']) }}</div>
                <div class="stats-label">Total Siswa</div>
            </div>
        </div>
        <div class="col-md-3 mb-2">
            <div class="stats-card">
                <div class="stats-icon bg-success">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stats-value text-success">{{ number_format($myStats['present']) }}</div>
                <div class="stats-label">Hadir Hari Ini</div>
            </div>
        </div>
        <div class="col-md-3 mb-2">
            <div class="stats-card">
                <div class="stats-icon bg-warning">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stats-value text-warning">{{ number_format($myStats['late']) }}</div>
                <div class="stats-label">Terlambat</div>
            </div>
        </div>
        <div class="col-md-3 mb-2">
            <div class="stats-card">
                <div class="stats-icon bg-info">
                    <i class="fas fa-percentage"></i>
                </div>
                <div class="stats-value text-info">{{ number_format($myStats['attendance_rate'], 1) }}%</div>
                <div class="stats-label">Tingkat Kehadiran</div>
            </div>
        </div>
    </div>

    <!-- Classes Overview -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="detail-card">
                <div class="detail-header">
                    <h5 class="mb-0">
                        <i class="fas fa-school me-2"></i>Kelas yang Diampu
                    </h5>
                </div>
                <div class="detail-body">
                    @if($myClasses->count() > 0)
                        <div class="row">
                            @foreach($myClasses as $class)
                                @php
                                    $classStudents = $myStudents->where('class_room_id', $class->id);
                                    $classPresent = $attendanceData->whereIn('student_id', $classStudents->pluck('id'))->where('status', 'hadir')->count();
                                    $classLate = $attendanceData->whereIn('student_id', $classStudents->pluck('id'))->where('status', 'terlambat')->count();
                                    $classTotal = $classStudents->count();
                                    $classRate = $classTotal > 0 ? round(($classPresent / $classTotal) * 100, 1) : 0;
                                @endphp
                                <div class="col-md-4 mb-3">
                                    <div class="card border">
                                        <div class="card-body text-center">
                                            <h6 class="card-title">{{ $class->name }}</h6>
                                            <div class="row">
                                                <div class="col-4">
                                                    <div class="text-success">
                                                        <i class="fas fa-check-circle"></i>
                                                        <div class="fw-bold">{{ $classPresent }}</div>
                                                        <small>Hadir</small>
                                                    </div>
                                                </div>
                                                <div class="col-4">
                                                    <div class="text-warning">
                                                        <i class="fas fa-clock"></i>
                                                        <div class="fw-bold">{{ $classLate }}</div>
                                                        <small>Terlambat</small>
                                                    </div>
                                                </div>
                                                <div class="col-4">
                                                    <div class="text-info">
                                                        <i class="fas fa-percentage"></i>
                                                        <div class="fw-bold">{{ $classRate }}%</div>
                                                        <small>Rate</small>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="progress mt-2">
                                                <div class="progress-bar bg-success" style="width: {{ $classRate }}%"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-school fa-3x mb-3"></i>
                            <h5>Belum ada kelas yang diampu</h5>
                            <p>Hubungi admin untuk mengatur kelas yang diampu</p>
                        </div>
                    @endif
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
                        <i class="fas fa-user-graduate me-2"></i>Daftar Siswa
                    </h5>
                </div>
                <div class="detail-body">
                    @if($myStudents->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Siswa</th>
                                        <th>Kelas</th>
                                        <th>Status Hari Ini</th>
                                        <th>Waktu Absen</th>
                                        <th>Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($myStudents as $index => $student)
                                        @php
                                            $todayAttendance = $attendanceData->where('student_id', $student->id)->first();
                                        @endphp
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
                                                <span class="badge bg-info">{{ $student->classRoom->name ?? 'N/A' }}</span>
                                            </td>
                                            <td>
                                                @if($todayAttendance)
                                                    @if($todayAttendance->status == 'hadir')
                                                        <span class="badge bg-success">
                                                            <i class="fas fa-check me-1"></i>Hadir
                                                        </span>
                                                    @elseif($todayAttendance->status == 'terlambat')
                                                        <span class="badge bg-warning">
                                                            <i class="fas fa-clock me-1"></i>Terlambat
                                                        </span>
                                                    @else
                                                        <span class="badge bg-secondary">
                                                            <i class="fas fa-question me-1"></i>{{ ucfirst($todayAttendance->status) }}
                                                        </span>
                                                    @endif
                                                @else
                                                    <span class="badge bg-danger">
                                                        <i class="fas fa-times me-1"></i>Tidak Hadir
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($todayAttendance)
                                                    <small class="text-muted">
                                                        {{ $todayAttendance->created_at->format('H:i') }}
                                                    </small>
                                                @else
                                                    <small class="text-muted">-</small>
                                                @endif
                                            </td>
                                            <td>
                                                @if($todayAttendance && $todayAttendance->notes)
                                                    <small class="text-muted">{{ $todayAttendance->notes }}</small>
                                                @else
                                                    <small class="text-muted">-</small>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-user-graduate fa-3x mb-3"></i>
                            <h5>Belum ada siswa yang diampu</h5>
                            <p>Hubungi admin untuk mengatur kelas yang diampu</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Attendance Summary Chart -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="detail-card">
                <div class="detail-header">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-pie me-2"></i>Ringkasan Kehadiran
                    </h5>
                </div>
                <div class="detail-body">
                    <div class="row">
                        <div class="col-md-6">
                            <canvas id="attendanceChart" height="200"></canvas>
                        </div>
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-6 mb-3">
                                    <div class="text-center">
                                        <div class="stats-value text-success">{{ number_format($myStats['present']) }}</div>
                                        <div class="stats-label">Hadir</div>
                                    </div>
                                </div>
                                <div class="col-6 mb-3">
                                    <div class="text-center">
                                        <div class="stats-value text-warning">{{ number_format($myStats['late']) }}</div>
                                        <div class="stats-label">Terlambat</div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="text-center">
                                        <div class="stats-value text-danger">{{ number_format($myStats['total_students'] - $myStats['present'] - $myStats['late']) }}</div>
                                        <div class="stats-label">Tidak Hadir</div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="text-center">
                                        <div class="stats-value text-info">{{ number_format($myStats['classes_count']) }}</div>
                                        <div class="stats-label">Kelas</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
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
                                    <h6 class="mb-1">Periode Laporan</h6>
                                    <small class="text-muted">{{ $startDate->format('d M Y') }} - {{ $endDate->format('d M Y') }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center justify-content-center">
                                <i class="fas fa-chalkboard-teacher fa-2x text-success me-3"></i>
                                <div class="text-start">
                                    <h6 class="mb-1">Guru: {{ auth()->user()->name }}</h6>
                                    <small class="text-muted">{{ $myStats['classes_count'] }} kelas diampu</small>
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
                    {{ $myStats['present'] }},
                    {{ $myStats['late'] }},
                    {{ $myStats['total_students'] - $myStats['present'] - $myStats['late'] }}
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

function exportReport(type) {
    const startDate = '{{ $startDate->format("Y-m-d") }}';
    const endDate = '{{ $endDate->format("Y-m-d") }}';
    
    // Show loading message
    const exportBtn = event.target;
    const originalText = exportBtn.innerHTML;
    exportBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Mengekspor...';
    exportBtn.disabled = true;
    
    // Build export URL
    let exportUrl = '';
    if (type === 'excel') {
        exportUrl = '{{ route("admin.reports.my-students.excel") }}';
    } else if (type === 'pdf') {
        exportUrl = '{{ route("admin.reports.my-students.pdf") }}';
    }
    
    // Add date parameters
    exportUrl += `?start_date=${startDate}&end_date=${endDate}`;
    
    // Create temporary link and trigger download
    const link = document.createElement('a');
    link.href = exportUrl;
    link.download = '';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    
    // Reset button after a short delay
    setTimeout(() => {
        exportBtn.innerHTML = originalText;
        exportBtn.disabled = false;
    }, 1000);
}
</script>
@endpush
