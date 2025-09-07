@extends('admin.layouts.app')

@section('title', 'Laporan Kelas')

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
            <h1 class="h3 mb-0">Laporan Kelas {{ $class->name }}</h1>
            <p class="text-muted mb-0">Data Kehadiran Siswa - {{ \Carbon\Carbon::now()->format('d F Y') }}</p>
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

    <!-- Class Info -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="alert alert-info border-0 py-2">
                <div class="d-flex align-items-center">
                    <i class="fas fa-school fa-2x me-3 text-info"></i>
                    <div>
                        <h5 class="mb-1">Kelas: {{ $class->name }}</h5>
                        <p class="mb-0">Total Siswa: {{ $classStats['total_students'] }} siswa | Tingkat Kehadiran: {{ $classStats['attendance_rate'] }}%</p>
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
                <div class="stats-value text-primary">{{ number_format($classStats['total_students']) }}</div>
                <div class="stats-label">Total Siswa</div>
            </div>
        </div>
        <div class="col-md-3 mb-2">
            <div class="stats-card">
                <div class="stats-icon bg-success">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stats-value text-success">{{ number_format($classStats['present']) }}</div>
                <div class="stats-label">Hadir Hari Ini</div>
            </div>
        </div>
        <div class="col-md-3 mb-2">
            <div class="stats-card">
                <div class="stats-icon bg-warning">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stats-value text-warning">{{ number_format($classStats['late']) }}</div>
                <div class="stats-label">Terlambat</div>
            </div>
        </div>
        <div class="col-md-3 mb-2">
            <div class="stats-card">
                <div class="stats-icon bg-danger">
                    <i class="fas fa-times"></i>
                </div>
                <div class="stats-value text-danger">{{ number_format($classStats['absent']) }}</div>
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
                        <i class="fas fa-chart-pie me-2"></i>Ringkasan Kehadiran
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
                                    <span class="badge bg-success">{{ $classStats['attendance_rate'] }}%</span>
                                </div>
                                <div class="progress">
                                    <div class="progress-bar bg-success" style="width: {{ $classStats['attendance_rate'] }}%"></div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="fw-semibold">Hadir</span>
                                    <span class="badge bg-success">{{ $classStats['present'] }}</span>
                                </div>
                                <div class="progress">
                                    <div class="progress-bar bg-success" style="width: {{ $classStats['total_students'] > 0 ? ($classStats['present'] / $classStats['total_students']) * 100 : 0 }}%"></div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="fw-semibold">Terlambat</span>
                                    <span class="badge bg-warning">{{ $classStats['late'] }}</span>
                                </div>
                                <div class="progress">
                                    <div class="progress-bar bg-warning" style="width: {{ $classStats['total_students'] > 0 ? ($classStats['late'] / $classStats['total_students']) * 100 : 0 }}%"></div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="fw-semibold">Tidak Hadir</span>
                                    <span class="badge bg-danger">{{ $classStats['absent'] }}</span>
                                </div>
                                <div class="progress">
                                    <div class="progress-bar bg-danger" style="width: {{ $classStats['total_students'] > 0 ? ($classStats['absent'] / $classStats['total_students']) * 100 : 0 }}%"></div>
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
                            <div class="stats-value text-success">{{ $classStats['attendance_rate'] }}%</div>
                            <div class="stats-label">Kehadiran</div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="stats-value text-info">{{ $classStats['total_students'] }}</div>
                            <div class="stats-label">Total Siswa</div>
                        </div>
                        <div class="col-6">
                            <div class="stats-value text-warning">{{ $classStats['late'] }}</div>
                            <div class="stats-label">Terlambat</div>
                        </div>
                        <div class="col-6">
                            <div class="stats-value text-danger">{{ $classStats['absent'] }}</div>
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
                                        <th>Status Hari Ini</th>
                                        <th>Waktu Absen</th>
                                        <th>Keterangan</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($students as $index => $student)
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

    <!-- Attendance Trends -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="detail-card">
                <div class="detail-header">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-line me-2"></i>Tren Kehadiran 7 Hari Terakhir
                    </h5>
                </div>
                <div class="detail-body">
                    <canvas id="trendChart" height="300"></canvas>
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
                                <i class="fas fa-school fa-2x text-success me-3"></i>
                                <div class="text-start">
                                    <h6 class="mb-1">Kelas: {{ $class->name }}</h6>
                                    <small class="text-muted">{{ $classStats['total_students'] }} siswa</small>
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
                    {{ $classStats['present'] }},
                    {{ $classStats['late'] }},
                    {{ $classStats['absent'] }}
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

// Trend Chart
const trendCtx = document.getElementById('trendChart');
if (trendCtx) {
    // Generate sample data for the last 7 days
    const labels = [];
    const presentData = [];
    const lateData = [];
    const absentData = [];
    
    for (let i = 6; i >= 0; i--) {
        const date = new Date();
        date.setDate(date.getDate() - i);
        labels.push(date.toLocaleDateString('id-ID', { weekday: 'short', day: 'numeric' }));
        
        // Sample data - in real implementation, this would come from the controller
        presentData.push(Math.floor(Math.random() * 20) + 15);
        lateData.push(Math.floor(Math.random() * 5) + 1);
        absentData.push(Math.floor(Math.random() * 3) + 1);
    }
    
    new Chart(trendCtx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Hadir',
                data: presentData,
                borderColor: '#28a745',
                backgroundColor: 'rgba(40, 167, 69, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4
            }, {
                label: 'Terlambat',
                data: lateData,
                borderColor: '#ffc107',
                backgroundColor: 'rgba(255, 193, 7, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4
            }, {
                label: 'Tidak Hadir',
                data: absentData,
                borderColor: '#dc3545',
                backgroundColor: 'rgba(220, 53, 69, 0.1)',
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
                    text: 'Tren Kehadiran 7 Hari Terakhir'
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

function exportReport(type) {
    const startDate = '{{ $startDate->format("Y-m-d") }}';
    const endDate = '{{ $endDate->format("Y-m-d") }}';
    const className = '{{ $class->name }}';
    
    // Show loading message
    const exportBtn = event.target;
    const originalText = exportBtn.innerHTML;
    exportBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Mengekspor...';
    exportBtn.disabled = true;
    
    // For now, just show a message (future implementation: actual export)
    setTimeout(() => {
        alert(`Fitur export ${type.toUpperCase()} sedang dalam pengembangan\n\nData yang akan diekspor:\n- Periode: ${startDate} - ${endDate}\n- Kelas: ${className}\n- Tipe: Laporan Kelas`);
        
        exportBtn.innerHTML = originalText;
        exportBtn.disabled = false;
    }, 2000);
}
</script>
@endpush
