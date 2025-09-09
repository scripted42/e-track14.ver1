@extends('admin.layouts.app')

@section('title', 'Dashboard Waka Kurikulum')

@section('content')
<!-- Modern Dashboard Header -->
<div class="dashboard-header mb-5">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="dashboard-title mb-2">Dashboard Waka Kurikulum</h1>
            <p class="dashboard-subtitle">Selamat datang, {{ auth()->user()->name }}</p>
        </div>
        <div class="dashboard-date">
            <i class="fas fa-calendar me-2"></i>
            {{ now()->format('d F Y') }}
        </div>
    </div>
</div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <!-- Teachers Attendance Today -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Kehadiran Guru Hari Ini
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $teachersAttendanceToday }}/{{ $totalTeachers }}
                            </div>
                            <div class="text-xs text-muted">
                                Persentase: {{ $totalTeachers > 0 ? number_format(($teachersAttendanceToday / $totalTeachers) * 100, 1) : 0 }}%
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chalkboard-teacher fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Students Attendance Today -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Kehadiran Siswa Hari Ini
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $studentsAttendanceToday }}/{{ $totalStudents }}
                            </div>
                            <div class="text-xs text-muted">
                                Persentase: {{ $totalStudents > 0 ? number_format(($studentsAttendanceToday / $totalStudents) * 100, 1) : 0 }}%
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-graduate fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Teachers -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Total Guru
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $totalTeachers }}
                            </div>
                            <div class="text-xs text-muted">
                                Aktif: {{ $totalTeachers }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Students -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Total Siswa
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $totalStudents }}
                            </div>
                            <div class="text-xs text-muted">
                                Aktif: {{ $totalStudents }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-graduation-cap fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Weekly Attendance Chart -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Kehadiran Guru & Siswa (Minggu Ini)</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="weeklyAttendanceChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Teacher Activities -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Aktivitas Guru Hari Ini</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Guru</th>
                                    <th>Waktu</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentTeacherAttendance->take(5) as $attendance)
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
                                    <td colspan="3" class="text-center text-muted">Belum ada aktivitas guru</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Class Attendance Summary -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Ringkasan Kehadiran per Kelas</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Kelas</th>
                                    <th>Total Siswa</th>
                                    <th>Hadir Hari Ini</th>
                                    <th>Persentase</th>
                                    <th>Progress Bar</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($classAttendanceSummary as $class)
                                <tr>
                                    <td><strong>{{ $class['class_name'] }}</strong></td>
                                    <td>{{ $class['total_students'] }}</td>
                                    <td>{{ $class['present_today'] }}</td>
                                    <td>{{ number_format($class['percentage'], 1) }}%</td>
                                    <td>
                                        <div class="progress">
                                            <div class="progress-bar {{ $class['percentage'] >= 80 ? 'bg-success' : ($class['percentage'] >= 60 ? 'bg-warning' : 'bg-danger') }}" 
                                                 role="progressbar" 
                                                 style="width: {{ $class['percentage'] }}%"
                                                 aria-valuenow="{{ $class['percentage'] }}" 
                                                 aria-valuemin="0" 
                                                 aria-valuemax="100">
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">Belum ada data kelas</td>
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

@vite(["resources/js/app.js"])
<script>
// Weekly Attendance Chart
const ctx = document.getElementById('weeklyAttendanceChart').getContext('2d');
const weeklyAttendanceChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: [
            @foreach($weeklyData as $data)
                '{{ $data["day"] }}',
            @endforeach
        ],
        datasets: [{
            label: 'Guru',
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
