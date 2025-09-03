@extends('admin.layouts.app')

@section('title', 'Dashboard Pegawai')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Dashboard Pegawai</h1>
            <p class="text-muted">Selamat datang, {{ auth()->user()->name }}</p>
        </div>
        <div class="text-right">
            <div class="text-sm text-muted">Hari ini</div>
            <div class="h5 mb-0 text-primary">{{ now()->format('d F Y') }}</div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <!-- My Attendance Today -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Kehadiran Hari Ini
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $myAttendanceToday->count() }}/2
                            </div>
                            <div class="text-xs text-muted">
                                @if($myAttendanceToday->where('type', 'checkin')->count() > 0)
                                    <span class="text-success">✓ Check-in</span>
                                @else
                                    <span class="text-danger">✗ Check-in</span>
                                @endif
                                @if($myAttendanceToday->where('type', 'checkout')->count() > 0)
                                    <span class="text-success ml-2">✓ Check-out</span>
                                @else
                                    <span class="text-warning ml-2">⏳ Check-out</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- This Month Attendance -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Kehadiran Bulan Ini
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $thisMonthAttendance }}
                            </div>
                            <div class="text-xs text-muted">
                                Hari kerja: {{ now()->startOfMonth()->diffInDays(now()) + 1 }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- My Leave Requests -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Izin Saya
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $myLeaves->count() }}
                            </div>
                            <div class="text-xs text-muted">
                                @if($myLeaves->where('status', 'menunggu')->count() > 0)
                                    <span class="text-warning">{{ $myLeaves->where('status', 'menunggu')->count() }} menunggu</span>
                                @else
                                    <span class="text-success">Semua disetujui</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Attendance Summary -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Persentase Kehadiran
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($attendanceSummary['percentage'], 1) }}%
                            </div>
                            <div class="text-xs text-muted">
                                Tepat waktu: {{ $attendanceSummary['on_time'] }}/{{ $attendanceSummary['total'] }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-percentage fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- My Attendance Chart -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Kehadiran Saya (Minggu Ini)</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="myAttendanceChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- My Recent Attendance -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Kehadiran Terbaru Saya</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Waktu</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($myRecentAttendance->take(5) as $attendance)
                                <tr>
                                    <td>{{ $attendance->timestamp->format('d/m') }}</td>
                                    <td>{{ $attendance->timestamp->format('H:i') }}</td>
                                    <td>
                                        <span class="badge badge-{{ $attendance->status == 'hadir' ? 'success' : 'warning' }}">
                                            {{ ucfirst($attendance->status) }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted">Belum ada data kehadiran</td>
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
        <!-- My Leave Requests -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Izin Terbaru Saya</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Tanggal Mulai</th>
                                    <th>Tanggal Selesai</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($myLeaves as $leave)
                                <tr>
                                    <td>{{ $leave->start_date->format('d/m/Y') }}</td>
                                    <td>{{ $leave->end_date->format('d/m/Y') }}</td>
                                    <td>
                                        <span class="badge badge-{{ $leave->status == 'disetujui' ? 'success' : ($leave->status == 'ditolak' ? 'danger' : 'warning') }}">
                                            {{ ucfirst($leave->status) }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted">Belum ada data izin</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Attendance Summary Details -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Ringkasan Kehadiran Bulan Ini</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="text-center">
                                <div class="h4 text-success">{{ $attendanceSummary['on_time'] }}</div>
                                <div class="text-sm text-muted">Tepat Waktu</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center">
                                <div class="h4 text-warning">{{ $attendanceSummary['late'] }}</div>
                                <div class="text-sm text-muted">Terlambat</div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="text-center">
                        <div class="h5 text-primary">{{ $attendanceSummary['total'] }}</div>
                        <div class="text-sm text-muted">Total Kehadiran</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// My Attendance Chart
const ctx = document.getElementById('myAttendanceChart').getContext('2d');
const myAttendanceChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'],
        datasets: [{
            label: 'Kehadiran',
            data: [1, 1, 1, 1, 1, 0, 0], // This would be dynamic data
            backgroundColor: 'rgba(54, 162, 235, 0.2)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true,
                max: 2
            }
        }
    }
});
</script>
@endsection
