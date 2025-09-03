@extends('admin.layouts.app')

@section('title', 'Dashboard Guru')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Dashboard Guru</h1>
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

        <!-- My Students (if walikelas) -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Siswa Saya
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $myStudents->count() }}
                            </div>
                            <div class="text-xs text-muted">
                                Hadir hari ini: {{ $myStudentsAttendanceToday }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
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

        <!-- This Month Attendance -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Kehadiran Bulan Ini
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $myRecentAttendance->where('timestamp', '>=', now()->startOfMonth())->count() }}
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
    </div>

    <div class="row">
        <!-- My Students Attendance Chart (if walikelas) -->
        @if($myStudents->count() > 0)
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Kehadiran Siswa Saya (Minggu Ini)</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="myStudentsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- My Recent Attendance -->
        <div class="col-xl-{{ $myStudents->count() > 0 ? '4' : '6' }} col-lg-5">
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

        <!-- My Leave Requests -->
        <div class="col-xl-{{ $myStudents->count() > 0 ? '4' : '6' }} col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Izin Terbaru Saya</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($myLeaves as $leave)
                                <tr>
                                    <td>{{ $leave->start_date->format('d/m') }}</td>
                                    <td>
                                        <span class="badge badge-{{ $leave->status == 'disetujui' ? 'success' : ($leave->status == 'ditolak' ? 'danger' : 'warning') }}">
                                            {{ ucfirst($leave->status) }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="2" class="text-center text-muted">Belum ada data izin</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- My Students List (if walikelas) -->
    @if($myStudents->count() > 0)
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Daftar Siswa Saya</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>NISN</th>
                                    <th>Nama</th>
                                    <th>Kelas</th>
                                    <th>Status</th>
                                    <th>Kehadiran Hari Ini</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($myStudents as $student)
                                <tr>
                                    <td>{{ $student->nisn }}</td>
                                    <td>{{ $student->name }}</td>
                                    <td>{{ $student->classRoom ? $student->classRoom->name : '-' }}</td>
                                    <td>
                                        <span class="badge badge-{{ $student->status == 'Aktif' ? 'success' : 'secondary' }}">
                                            {{ $student->status }}
                                        </span>
                                    </td>
                                    <td>
                                        @php
                                            $todayAttendance = \App\Models\StudentAttendance::where('student_id', $student->id)
                                                ->whereDate('created_at', today())
                                                ->first();
                                        @endphp
                                        @if($todayAttendance)
                                            <span class="badge badge-success">Hadir</span>
                                        @else
                                            <span class="badge badge-danger">Tidak Hadir</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

@if($myStudents->count() > 0)
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// My Students Attendance Chart
const ctx = document.getElementById('myStudentsChart').getContext('2d');
const myStudentsChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: [
            @foreach($weeklyStudentData as $data)
                '{{ $data["day"] }}',
            @endforeach
        ],
        datasets: [{
            label: 'Siswa Hadir',
            data: [
                @foreach($weeklyStudentData as $data)
                    {{ $data['students'] }},
                @endforeach
            ],
            borderColor: 'rgb(75, 192, 192)',
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            tension: 0.1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true,
                max: {{ $myStudents->count() }}
            }
        }
    }
});
</script>
@endif
@endsection
