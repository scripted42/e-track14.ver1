@extends('admin.layouts.app')

@section('title', 'Dashboard Guru')

@section('content')
<!-- Modern Dashboard Header -->
<div class="dashboard-header mb-5">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="dashboard-title mb-2">Dashboard Guru</h1>
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
    <!-- My Attendance Today -->
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="stat-card stat-card--primary">
            <div class="stat-icon">
                <i class="fas fa-calendar-check"></i>
            </div>
            <div class="stat-number">{{ $myAttendanceToday->count() }}/2</div>
            <div class="stat-label">Kehadiran Hari Ini</div>
            <small>
                @if($myAttendanceToday->where('type', 'checkin')->count() > 0)
                    ✓ Check-in
                @else
                    ✗ Check-in
                @endif
                @if($myAttendanceToday->where('type', 'checkout')->count() > 0)
                    ✓ Check-out
                @else
                    ⏳ Check-out
                @endif
            </small>
        </div>
    </div>

    <!-- My Students (if walikelas) -->
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="stat-card stat-card--success">
            <div class="stat-icon">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-number">{{ $myStudents->count() }}</div>
            <div class="stat-label">Siswa Saya</div>
            <small>Hadir hari ini: {{ $myStudentsAttendanceToday }}</small>
        </div>
    </div>

    <!-- My Leave Requests -->
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="stat-card stat-card--warning">
            <div class="stat-icon">
                <i class="fas fa-clipboard-list"></i>
            </div>
            <div class="stat-number">{{ $myLeaves->count() }}</div>
            <div class="stat-label">Izin Saya</div>
            <small>
                @if($myLeaves->where('status', 'menunggu')->count() > 0)
                    {{ $myLeaves->where('status', 'menunggu')->count() }} menunggu
                @else
                    Semua disetujui
                @endif
            </small>
        </div>
    </div>

    <!-- This Month Attendance -->
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="stat-card stat-card--info">
            <div class="stat-icon">
                <i class="fas fa-chart-line"></i>
            </div>
            <div class="stat-number">{{ $myRecentAttendance->where('timestamp', '>=', now()->startOfMonth())->count() }}</div>
            <div class="stat-label">Kehadiran Bulan Ini</div>
            <small>Hari kerja: {{ now()->startOfMonth()->diffInDays(now()) + 1 }}</small>
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
@vite(["resources/js/app.js"])
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
