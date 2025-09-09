@extends('admin.layouts.app')

@section('title', 'Dashboard Kepala Sekolah')

@section('content')
<!-- Modern Dashboard Header -->
<div class="dashboard-header mb-5">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="dashboard-title mb-2">Dashboard Kepala Sekolah</h1>
            <p class="dashboard-subtitle">Selamat datang, {{ auth()->user()->name }}</p>
        </div>
        <div class="dashboard-date">
            <i class="fas fa-calendar me-2"></i>
            {{ now()->format('d F Y') }}
        </div>
    </div>
</div>

<!-- Statistics Cards - Modern Design -->
<div class="row mb-5">
    <!-- Employee Attendance Today -->
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="stat-card stat-card--primary">
            <div class="stat-icon">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-number">{{ $todayEmployeeAttendance }}/{{ $totalEmployees }}</div>
            <div class="stat-label">Kehadiran Pegawai Hari Ini</div>
            <small>Persentase: {{ $totalEmployees > 0 ? number_format(($todayEmployeeAttendance / $totalEmployees) * 100, 1) : 0 }}%</small>
        </div>
    </div>

    <!-- Student Attendance Today -->
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="stat-card stat-card--success">
            <div class="stat-icon">
                <i class="fas fa-user-graduate"></i>
            </div>
            <div class="stat-number">{{ $todayStudentAttendance }}/{{ $totalStudents }}</div>
            <div class="stat-label">Kehadiran Siswa Hari Ini</div>
            <small>Persentase: {{ $totalStudents > 0 ? number_format(($todayStudentAttendance / $totalStudents) * 100, 1) : 0 }}%</small>
        </div>
    </div>

    <!-- Pending Leave Requests -->
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="stat-card stat-card--warning">
            <div class="stat-icon">
                <i class="fas fa-calendar-times"></i>
            </div>
            <div class="stat-number">{{ $pendingLeaves }}</div>
            <div class="stat-label">Izin Menunggu Persetujuan</div>
            <small>perlu ditinjau</small>
        </div>
    </div>

    <!-- Total Staff -->
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="stat-card stat-card--info">
            <div class="stat-icon">
                <i class="fas fa-chalkboard-teacher"></i>
            </div>
            <div class="stat-number">{{ $totalEmployees }}</div>
            <div class="stat-label">Total Staff</div>
            <small>Guru, Pegawai, Waka, Kepsek</small>
        </div>
    </div>
    </div>

    <div class="row">
        <!-- Weekly Overview Chart -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Overview Kehadiran (Minggu Ini)</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="weeklyOverviewChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activities -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Aktivitas Terbaru</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Pegawai</th>
                                    <th>Waktu</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentAttendance->take(5) as $attendance)
                                <tr>
                                    <td>{{ $attendance->user->name }}</td>
                                    <td>{{ $attendance->timestamp->format('H:i') }}</td>
                                    <td>
                                        <span class="badge badge-{{ $attendance->status == 'hadir' ? 'success' : 'warning' }}">
                                            {{ ucfirst($attendance->status) }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted">Belum ada aktivitas</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Department Statistics -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Statistik per Departemen</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Departemen</th>
                                    <th>Total</th>
                                    <th>Hadir</th>
                                    <th>%</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($departmentStats as $dept)
                                <tr>
                                    <td>{{ $dept['department'] }}</td>
                                    <td>{{ $dept['total'] }}</td>
                                    <td>{{ $dept['present'] }}</td>
                                    <td>
                                        <span class="badge badge-{{ $dept['percentage'] >= 80 ? 'success' : ($dept['percentage'] >= 60 ? 'warning' : 'danger') }}">
                                            {{ number_format($dept['percentage'], 1) }}%
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">Belum ada data departemen</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Leave Requests -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Izin Menunggu Persetujuan</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Pegawai</th>
                                    <th>Tanggal</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentLeaves as $leave)
                                <tr>
                                    <td>{{ $leave->user->name }}</td>
                                    <td>{{ $leave->start_date->format('d/m') }}</td>
                                    <td>
                                        <span class="badge badge-warning">
                                            {{ ucfirst($leave->status) }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted">Tidak ada izin menunggu</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Monthly Trends -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Tren Bulanan</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="text-center">
                                <h4 class="text-primary">{{ $monthlyTrends['employees']['current'] }}</h4>
                                <p class="text-muted">Kehadiran Pegawai Bulan Ini</p>
                                @if($monthlyTrends['employees']['change'] != 0)
                                    <span class="badge badge-{{ $monthlyTrends['employees']['change'] > 0 ? 'success' : 'danger' }}">
                                        {{ $monthlyTrends['employees']['change'] > 0 ? '+' : '' }}{{ number_format($monthlyTrends['employees']['change'], 1) }}%
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-center">
                                <h4 class="text-success">{{ $monthlyTrends['students']['current'] }}</h4>
                                <p class="text-muted">Kehadiran Siswa Bulan Ini</p>
                                @if($monthlyTrends['students']['change'] != 0)
                                    <span class="badge badge-{{ $monthlyTrends['students']['change'] > 0 ? 'success' : 'danger' }}">
                                        {{ $monthlyTrends['students']['change'] > 0 ? '+' : '' }}{{ number_format($monthlyTrends['students']['change'], 1) }}%
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@vite(["resources/js/app.js"])
<script>
// Weekly Overview Chart
const ctx = document.getElementById('weeklyOverviewChart').getContext('2d');
const weeklyOverviewChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: [
            @foreach($weeklyData as $data)
                '{{ $data["day"] }}',
            @endforeach
        ],
        datasets: [{
            label: 'Pegawai',
            data: [
                @foreach($weeklyData as $data)
                    {{ $data['employees'] }},
                @endforeach
            ],
            borderColor: 'rgb(75, 192, 192)',
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            tension: 0.1
        }, {
            label: 'Siswa',
            data: [
                @foreach($weeklyData as $data)
                    {{ $data['students'] }},
                @endforeach
            ],
            borderColor: 'rgb(255, 99, 132)',
            backgroundColor: 'rgba(255, 99, 132, 0.2)',
            tension: 0.1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});
</script>
@endsection
