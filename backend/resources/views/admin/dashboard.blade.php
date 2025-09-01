@extends('admin.layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0">Dashboard</h1>
        <p class="text-muted">Selamat datang di panel admin E-Track14</p>
    </div>
    <div class="text-muted">
        <i class="fas fa-calendar me-1"></i>
        {{ \Carbon\Carbon::now()->format('d F Y') }}
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card stat-card">
            <div class="stat-icon" style="background-color: rgba(34, 197, 94, 0.1); color: #22c55e;">
                <i class="fas fa-user-check"></i>
            </div>
            <div class="stat-number text-success">{{ $todayAttendance }}</div>
            <div class="stat-label">Absensi Hari Ini</div>
            <small class="text-muted">dari {{ $totalEmployees }} pegawai</small>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card stat-card">
            <div class="stat-icon" style="background-color: rgba(59, 130, 246, 0.1); color: #3b82f6;">
                <i class="fas fa-user-graduate"></i>
            </div>
            <div class="stat-number text-primary">{{ $todayStudentAttendance }}</div>
            <div class="stat-label">Siswa Hadir Hari Ini</div>
            <small class="text-muted">dari {{ $totalStudents }} siswa</small>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card stat-card">
            <div class="stat-icon" style="background-color: rgba(251, 146, 60, 0.1); color: #fb923c;">
                <i class="fas fa-calendar-times"></i>
            </div>
            <div class="stat-number text-warning">{{ $pendingLeaves }}</div>
            <div class="stat-label">Izin Menunggu</div>
            <small class="text-muted">perlu persetujuan</small>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card stat-card">
            <div class="stat-icon" style="background-color: rgba(168, 85, 247, 0.1); color: #a855f7;">
                <i class="fas fa-qrcode"></i>
            </div>
            <div class="stat-number {{ $todayQr ? 'text-success' : 'text-danger' }}">
                {{ $todayQr ? 'AKTIF' : 'NONAKTIF' }}
            </div>
            <div class="stat-label">QR Code Hari Ini</div>
            @if($todayQr)
                <small class="text-muted">Berlaku sampai {{ $todayQr->valid_until->format('H:i') }}</small>
            @else
                <small class="text-muted">Belum dibuat</small>
            @endif
        </div>
    </div>
</div>

<!-- Charts and Recent Activity -->
<div class="row">
    <!-- Weekly Attendance Chart -->
    <div class="col-lg-8 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-chart-line me-2"></i>
                    Absensi 7 Hari Terakhir
                </h5>
            </div>
            <div class="card-body">
                <canvas id="weeklyChart" height="300"></canvas>
            </div>
        </div>
    </div>
    
    <!-- Quick Actions -->
    <div class="col-lg-4 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-bolt me-2"></i>
                    Aksi Cepat
                </h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.attendance.qr') }}" class="btn btn-primary">
                        <i class="fas fa-qrcode me-2"></i>
                        Generate QR Code
                    </a>
                    <a href="{{ route('admin.leaves.index') }}" class="btn btn-warning">
                        <i class="fas fa-calendar-times me-2"></i>
                        Kelola Izin ({{ $pendingLeaves }})
                    </a>
                    <a href="{{ route('admin.reports.index') }}" class="btn btn-info">
                        <i class="fas fa-chart-bar me-2"></i>
                        Lihat Laporan
                    </a>
                    <a href="{{ route('admin.settings.index') }}" class="btn btn-secondary">
                        <i class="fas fa-cog me-2"></i>
                        Pengaturan
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activity -->
<div class="row">
    <!-- Recent Attendance -->
    <div class="col-lg-8 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-clock me-2"></i>
                    Absensi Terbaru
                </h5>
                <a href="{{ route('admin.attendance.index') }}" class="btn btn-sm btn-outline-primary">
                    Lihat Semua
                </a>
            </div>
            <div class="card-body">
                @if($recentAttendance->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Jenis</th>
                                    <th>Status</th>
                                    <th>Waktu</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentAttendance as $attendance)
                                <tr>
                                    <td>
                                        <div class="fw-medium">{{ $attendance->user->name }}</div>
                                    </td>
                                    <td>
                                        <span class="badge {{ $attendance->type === 'checkin' ? 'bg-success' : 'bg-warning' }}">
                                            {{ $attendance->type === 'checkin' ? 'Check In' : 'Check Out' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">{{ ucfirst($attendance->status) }}</span>
                                    </td>
                                    <td class="text-muted">
                                        {{ $attendance->timestamp->format('H:i') }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-clock fa-2x text-muted mb-3"></i>
                        <p class="text-muted">Belum ada absensi hari ini</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Pending Leaves -->
    <div class="col-lg-4 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-calendar-times me-2"></i>
                    Izin Menunggu
                </h5>
                <a href="{{ route('admin.leaves.index') }}" class="btn btn-sm btn-outline-warning">
                    Lihat Semua
                </a>
            </div>
            <div class="card-body">
                @if($recentLeaves->count() > 0)
                    @foreach($recentLeaves as $leave)
                    <div class="d-flex align-items-center mb-3 pb-3 border-bottom">
                        <div class="flex-grow-1">
                            <div class="fw-medium">{{ $leave->user->name }}</div>
                            <small class="text-muted">
                                {{ ucfirst($leave->leave_type) }} - 
                                {{ $leave->start_date->format('d/m') }} s/d {{ $leave->end_date->format('d/m') }}
                            </small>
                        </div>
                        <span class="badge bg-warning">{{ $leave->durationDays }} hari</span>
                    </div>
                    @endforeach
                @else
                    <div class="text-center py-3">
                        <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                        <p class="text-muted mb-0">Semua izin sudah diproses</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Monthly Statistics -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-chart-pie me-2"></i>
                    Statistik Bulanan
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="text-center">
                            <h3 class="text-primary">{{ $monthlyStats['attendance']['current'] }}</h3>
                            <p class="text-muted mb-1">Total Absensi Bulan Ini</p>
                            <small class="text-{{ $monthlyStats['attendance']['change'] >= 0 ? 'success' : 'danger' }}">
                                {{ $monthlyStats['attendance']['change'] >= 0 ? '+' : '' }}{{ number_format($monthlyStats['attendance']['change'], 1) }}% dari bulan lalu
                            </small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-center">
                            <h3 class="text-success">{{ $monthlyStats['students']['current'] }}</h3>
                            <p class="text-muted mb-1">Absensi Siswa Bulan Ini</p>
                            <small class="text-{{ $monthlyStats['students']['change'] >= 0 ? 'success' : 'danger' }}">
                                {{ $monthlyStats['students']['change'] >= 0 ? '+' : '' }}{{ number_format($monthlyStats['students']['change'], 1) }}% dari bulan lalu
                            </small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-center">
                            <h3 class="text-warning">{{ $monthlyStats['leaves']['current'] }}</h3>
                            <p class="text-muted mb-1">Pengajuan Izin Bulan Ini</p>
                            <small class="text-{{ $monthlyStats['leaves']['change'] >= 0 ? 'warning' : 'success' }}">
                                {{ $monthlyStats['leaves']['change'] >= 0 ? '+' : '' }}{{ number_format($monthlyStats['leaves']['change'], 1) }}% dari bulan lalu
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- Weekly Chart Data -->
<script id="weekly-chart-data" type="application/json">
<?php
$chartData = [
    'labels' => isset($weeklyData) ? array_column($weeklyData, 'day') : [],
    'employeeData' => isset($weeklyData) ? array_column($weeklyData, 'employees') : [],
    'studentData' => isset($weeklyData) ? array_column($weeklyData, 'students') : []
];
echo json_encode($chartData);
?>
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Weekly Attendance Chart
        const weeklyCtx = document.getElementById('weeklyChart');
        if (weeklyCtx) {
            // Get data from JSON script tag
            const dataScript = document.getElementById('weekly-chart-data');
            const chartData = dataScript ? JSON.parse(dataScript.textContent) : {
                labels: [],
                employeeData: [],
                studentData: []
            };
            
            const weeklyChart = new Chart(weeklyCtx.getContext('2d'), {
                type: 'line',
                data: {
                    labels: chartData.labels,
                    datasets: [{
                        label: 'Pegawai',
                        data: chartData.employeeData,
                        borderColor: '#2563EB',
                        backgroundColor: 'rgba(37, 99, 235, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4
                    }, {
                        label: 'Siswa',
                        data: chartData.studentData,
                        borderColor: '#10B981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                usePointStyle: true,
                                padding: 20
                            }
                        }
                    }
                }
            });
        }
    });
</script>
@endpush