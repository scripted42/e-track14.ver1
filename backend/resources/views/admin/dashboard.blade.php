@extends('admin.layouts.app')

@section('title', 'Dashboard')

@section('content')
@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="fas fa-exclamation-triangle me-2"></i>
    {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="fas fa-check-circle me-2"></i>
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0">
            @if(auth()->user()->hasRole('Admin'))
                Dashboard Admin
            @elseif(auth()->user()->hasRole('Guru'))
                Dashboard Guru
            @elseif(auth()->user()->hasRole('Kepala Sekolah'))
                Dashboard Kepala Sekolah
            @elseif(auth()->user()->hasRole('Waka Kurikulum'))
                Dashboard Waka Kurikulum
            @elseif(auth()->user()->hasRole('Pegawai'))
                Dashboard Pegawai
            @else
                Dashboard
            @endif
        </h1>
        <p class="text-muted">
            Selamat datang, {{ auth()->user()->name }} - {{ auth()->user()->role->role_name }}
        </p>
    </div>
    <div class="text-muted">
        <i class="fas fa-calendar me-1"></i>
        {{ \Carbon\Carbon::now()->format('d F Y') }}
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    @if(auth()->user()->hasRole('Admin'))
        <!-- Admin Dashboard -->
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card stat-card">
            <div class="stat-icon" style="background-color: rgba(34, 197, 94, 0.1); color: #22c55e;">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-number text-success">{{ $todayEmployeeAttendance ?? 0 }}</div>
                <div class="stat-label">Pegawai Hadir Hari Ini</div>
                <small class="text-muted">dari {{ $totalEmployees ?? 0 }} total pegawai</small>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card stat-card">
                <div class="stat-icon" style="background-color: rgba(59, 130, 246, 0.1); color: #3b82f6;">
                    <i class="fas fa-user-graduate"></i>
                </div>
                <div class="stat-number text-primary">{{ $todayStudentAttendance ?? 0 }}</div>
                <div class="stat-label">Siswa Hadir Hari Ini</div>
                <small class="text-muted">dari {{ $totalStudents ?? 0 }} total siswa</small>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card stat-card">
                <div class="stat-icon" style="background-color: rgba(251, 146, 60, 0.1); color: #fb923c;">
                    <i class="fas fa-calendar-times"></i>
                </div>
                <div class="stat-number text-warning">{{ $pendingLeaves ?? 0 }}</div>
                <div class="stat-label">Izin Menunggu</div>
                <small class="text-muted">perlu persetujuan</small>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card stat-card">
                <div class="stat-icon" style="background-color: rgba(168, 85, 247, 0.1); color: #a855f7;">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-number text-info">{{ $totalEmployees ?? 0 }}</div>
                <div class="stat-label">Total Staff</div>
                <small class="text-muted">{{ $totalStudents ?? 0 }} Siswa</small>
            </div>
        </div>
    @elseif(auth()->user()->hasRole('Guru'))
        <!-- Guru Dashboard -->
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card stat-card">
                <div class="stat-icon" style="background-color: rgba(34, 197, 94, 0.1); color: #22c55e;">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div class="stat-number text-success">{{ $thisMonthAttendance ?? 0 }}</div>
                <div class="stat-label">Kehadiran Bulanan</div>
                <small class="text-muted">{{ $thisMonthLate ?? 0 }} terlambat</small>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card stat-card">
            <div class="stat-icon" style="background-color: rgba(59, 130, 246, 0.1); color: #3b82f6;">
                <i class="fas fa-user-graduate"></i>
            </div>
                <div class="stat-number text-primary">{{ $myStudentsPresentToday ?? 0 }}</div>
            <div class="stat-label">Siswa Hadir Hari Ini</div>
                <small class="text-muted">{{ $myStudentsLateToday ?? 0 }} terlambat</small>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card stat-card">
                <div class="stat-icon" style="background-color: rgba(251, 146, 60, 0.1); color: #fb923c;">
                    <i class="fas fa-calendar-times"></i>
                </div>
                <div class="stat-number text-warning">{{ $myPendingLeaves ?? 0 }}</div>
                <div class="stat-label">Izin Bulanan</div>
                <small class="text-muted">menunggu approval</small>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card stat-card">
                <div class="stat-icon" style="background-color: rgba(168, 85, 247, 0.1); color: #a855f7;">
                    <i class="fas fa-chart-pie"></i>
                </div>
                <div class="stat-number text-info">
                    @if(isset($myStudents) && $myStudents->count() > 0)
                        {{ round((($myStudentsPresentToday ?? 0) / $myStudents->count()) * 100) }}%
                    @else
                        0%
                    @endif
                </div>
                <div class="stat-label">Persentase Kehadiran</div>
                <small class="text-muted">Siswa hari ini</small>
            </div>
        </div>
    @else
        <!-- Default Dashboard for other roles -->
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card stat-card">
                <div class="stat-icon" style="background-color: rgba(34, 197, 94, 0.1); color: #22c55e;">
                    <i class="fas fa-user-check"></i>
                </div>
                <div class="stat-number text-success">{{ $todayAttendance ?? 0 }}</div>
                <div class="stat-label">Absensi Hari Ini</div>
                <small class="text-muted">dari {{ $totalEmployees ?? 0 }} pegawai</small>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card stat-card">
                <div class="stat-icon" style="background-color: rgba(59, 130, 246, 0.1); color: #3b82f6;">
                    <i class="fas fa-user-graduate"></i>
                </div>
                <div class="stat-number text-primary">{{ $todayStudentAttendance ?? 0 }}</div>
                <div class="stat-label">Siswa Hadir Hari Ini</div>
                <small class="text-muted">dari {{ $totalStudents ?? 0 }} siswa</small>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card stat-card">
            <div class="stat-icon" style="background-color: rgba(251, 146, 60, 0.1); color: #fb923c;">
                <i class="fas fa-calendar-times"></i>
            </div>
                <div class="stat-number text-warning">{{ $pendingLeaves ?? 0 }}</div>
            <div class="stat-label">Izin Menunggu</div>
            <small class="text-muted">perlu persetujuan</small>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card stat-card">
            <div class="stat-icon" style="background-color: rgba(168, 85, 247, 0.1); color: #a855f7;">
                <i class="fas fa-qrcode"></i>
            </div>
                <div class="stat-number {{ isset($todayQr) && $todayQr ? 'text-success' : 'text-danger' }}">
                    {{ isset($todayQr) && $todayQr ? 'AKTIF' : 'NONAKTIF' }}
            </div>
            <div class="stat-label">QR Code Hari Ini</div>
                @if(isset($todayQr) && $todayQr)
                <small class="text-muted">Berlaku sampai {{ $todayQr->valid_until->format('H:i') }}</small>
            @else
                <small class="text-muted">Belum dibuat</small>
            @endif
            </div>
        </div>
    @endif
</div>

@if(auth()->user()->hasRole('Admin'))
<!-- HRD Analytics Section -->
<div class="row mb-4">
    <!-- Employee Leaderboard -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-trophy me-2"></i>
                    Top 5 Pegawai Terpunctual
                </h5>
                <small class="text-muted">Bulan {{ \Carbon\Carbon::now()->format('F Y') }}</small>
            </div>
            <div class="card-body">
                @if(isset($leaderboard) && count($leaderboard) > 0)
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Rank</th>
                                    <th>Nama</th>
                                    <th>Role</th>
                                    <th>On-Time Rate</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($leaderboard as $index => $employee)
                                    <tr>
                                        <td>
                                            @if($index == 0)
                                                <i class="fas fa-crown text-warning"></i>
                                            @elseif($index == 1)
                                                <i class="fas fa-medal text-secondary"></i>
                                            @elseif($index == 2)
                                                <i class="fas fa-award text-warning"></i>
                                            @else
                                                <span class="badge bg-light text-dark">{{ $index + 1 }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="fw-semibold">{{ $employee['user']->name }}</div>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">{{ $employee['user']->role->role_name }}</span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="progress me-2" style="width: 60px; height: 8px;">
                                                    <div class="progress-bar bg-success" style="width: {{ $employee['punctuality_rate'] }}%"></div>
                                                </div>
                                                <span class="fw-semibold">{{ $employee['punctuality_rate'] }}%</span>
                                            </div>
                                            <small class="text-muted">{{ $employee['on_time'] }}/{{ $employee['total_attendance'] }} hari</small>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-chart-line fa-3x mb-3"></i>
                        <p>Belum ada data leaderboard</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Student Late Leaderboard -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Top 5 Siswa Sering Terlambat
                </h5>
                <small class="text-muted">Bulan {{ \Carbon\Carbon::now()->format('F Y') }}</small>
            </div>
            <div class="card-body">
                @if(isset($studentLateLeaderboard) && count($studentLateLeaderboard) > 0)
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Rank</th>
                                    <th>Nama</th>
                                    <th>Kelas</th>
                                    <th>Late Rate</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($studentLateLeaderboard as $index => $student)
                                    <tr>
                                        <td>
                                            @if($index == 0)
                                                <i class="fas fa-exclamation-triangle text-danger"></i>
                                            @elseif($index == 1)
                                                <i class="fas fa-exclamation-circle text-warning"></i>
                                            @elseif($index == 2)
                                                <i class="fas fa-exclamation text-orange"></i>
                                            @else
                                                <span class="badge bg-danger text-white">{{ $index + 1 }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="fw-semibold">{{ $student['student']->name }}</div>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $student['student']->classRoom->name ?? 'N/A' }}</span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="progress me-2" style="width: 60px; height: 8px;">
                                                    <div class="progress-bar bg-danger" style="width: {{ $student['late_rate'] }}%"></div>
                                                </div>
                                                <span class="fw-semibold text-danger">{{ $student['late_rate'] }}%</span>
                                            </div>
                                            <small class="text-muted">{{ $student['late'] }}/{{ $student['total_attendance'] }} hari</small>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-smile fa-3x mb-3"></i>
                        <p>Semua siswa memiliki catatan kehadiran yang baik</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Department Performance & Frequent Leavers -->
<div class="row mb-4">
    <!-- Department Performance -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-building me-2"></i>
                    Performa Departemen Hari Ini
                </h5>
            </div>
            <div class="card-body">
                @if(isset($departmentStats) && count($departmentStats) > 0)
                    @foreach($departmentStats as $dept)
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="fw-semibold">{{ $dept['department'] }}</span>
                                <span class="badge bg-primary">{{ $dept['present'] }}/{{ $dept['total'] }}</span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-success" style="width: {{ $dept['percentage'] }}%"></div>
                            </div>
                            <small class="text-muted">{{ number_format($dept['percentage'], 1) }}% kehadiran</small>
                        </div>
                    @endforeach
                @else
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-chart-pie fa-3x mb-3"></i>
                        <p>Belum ada data departemen</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Pegawai dengan Izin Terbanyak -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Pegawai dengan Izin Terbanyak
                </h5>
                <small class="text-muted">Bulan {{ \Carbon\Carbon::now()->format('F Y') }}</small>
            </div>
            <div class="card-body">
                @if(isset($leaveAnalytics) && $leaveAnalytics['frequent_leavers']->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Rank</th>
                                    <th>Nama</th>
                                    <th>Jumlah Izin</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($leaveAnalytics['frequent_leavers'] as $index => $leaver)
                                    <tr>
                                        <td>
                                            @if($index == 0)
                                                <i class="fas fa-exclamation-triangle text-danger"></i>
                                            @elseif($index == 1)
                                                <i class="fas fa-exclamation-circle text-warning"></i>
                                            @elseif($index == 2)
                                                <i class="fas fa-exclamation text-orange"></i>
                                            @else
                                                <span class="badge bg-warning text-dark">{{ $index + 1 }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="fw-semibold">{{ $leaver->user->name }}</div>
                                        </td>
                                        <td>
                                            <span class="badge bg-warning text-dark">{{ $leaver->leave_count }} kali</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-smile fa-3x mb-3"></i>
                        <p>Semua pegawai memiliki catatan izin yang baik</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>



<!-- Attendance Trends Chart -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-chart-line me-2"></i>
                    Tren Kehadiran Pegawai 30 Hari Terakhir
                </h5>
            </div>
            <div class="card-body">
                <canvas id="attendanceTrendsChart" height="400"></canvas>
            </div>
        </div>
    </div>
</div>
@endif

@if(auth()->user()->hasRole('Guru'))
<!-- Class Attendance Progress for Guru -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-chart-bar me-2"></i>
                    Progress Absensi Kelas Hari Ini
                </h5>
            </div>
            <div class="card-body">
                @if(isset($myStudents) && $myStudents->count() > 0)
                    @php
                        $classProgress = [];
                        foreach($myStudents->groupBy('class_name') as $className => $students) {
                            $totalStudents = $students->count();
                            $presentStudents = 0;
                            foreach($students as $student) {
                                $todayAttendance = \App\Models\StudentAttendance::where('student_id', $student->id)
                                    ->whereDate('created_at', \Carbon\Carbon::today())
                                    ->first();
                                if($todayAttendance) {
                                    $presentStudents++;
                                }
                            }
                            $percentage = $totalStudents > 0 ? round(($presentStudents / $totalStudents) * 100) : 0;
                            $classProgress[] = [
                                'class_name' => $className,
                                'total_students' => $totalStudents,
                                'present_students' => $presentStudents,
                                'percentage' => $percentage
                            ];
                        }
                    @endphp
                    
                    @foreach($classProgress as $class)
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div>
                                    <h6 class="mb-0">{{ $class['class_name'] }}</h6>
                                    <small class="text-muted">{{ $class['present_students'] }} dari {{ $class['total_students'] }} siswa hadir</small>
                                </div>
                                <div class="text-end">
                                    <span class="badge {{ $class['percentage'] == 100 ? 'bg-success' : ($class['percentage'] >= 80 ? 'bg-warning' : 'bg-danger') }}">
                                        {{ $class['percentage'] }}%
                                    </span>
                                </div>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar {{ $class['percentage'] == 100 ? 'bg-success' : ($class['percentage'] >= 80 ? 'bg-warning' : 'bg-danger') }}" 
                                     role="progressbar" 
                                     style="width: {{ $class['percentage'] }}%" 
                                     aria-valuenow="{{ $class['percentage'] }}" 
                                     aria-valuemin="0" 
                                     aria-valuemax="100">
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-chart-bar fa-3x mb-3"></i>
                        <h5>Belum ada data kelas</h5>
                        <p>Hubungi admin untuk mengatur kelas yang diampu</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Student Attendance Table for Guru -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-user-graduate me-2"></i>
                    Realtime Absensi Siswa Hari Ini
                </h5>
            </div>
            <div class="card-body">
                @if(isset($myStudents) && $myStudents->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Siswa</th>
                                    <th>Kelas</th>
                                    <th>Status</th>
                                    <th>Waktu Absen</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($myStudents as $index => $student)
                                    @php
                                        $todayAttendance = \App\Models\StudentAttendance::where('student_id', $student->id)
                                            ->whereDate('created_at', \Carbon\Carbon::today())
                                            ->first();
                                    @endphp
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-2" 
                                                     style="width: 35px; height: 35px; font-size: 14px;">
                                                    {{ substr($student->name, 0, 1) }}
                                                </div>
                                                {{ $student->name }}
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $student->class_name }}</span>
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
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">
                                            <i class="fas fa-user-graduate fa-2x mb-2"></i><br>
                                            Tidak ada siswa yang terdaftar
                                        </td>
                                    </tr>
                                @endforelse
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
@endif

@if(auth()->user()->hasRole('Admin'))
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
                        Kelola Izin ({{ $pendingLeaves ?? 0 }})
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
@endif

@if(auth()->user()->hasRole('Admin'))
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
@endif
@endsection

@push('scripts')
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

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

<!-- Attendance Trends Chart Data -->
<script id="attendance-trends-data" type="application/json">
<?php
$trendsData = [
    'labels' => isset($attendanceTrends) ? array_column($attendanceTrends, 'date') : [],
    'employees' => isset($attendanceTrends) ? array_column($attendanceTrends, 'employees') : [],
    'onTime' => isset($attendanceTrends) ? array_column($attendanceTrends, 'on_time') : [],
    'late' => isset($attendanceTrends) ? array_column($attendanceTrends, 'late') : []
];
echo json_encode($trendsData);
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
        
        // Attendance Trends Chart
        const trendsCtx = document.getElementById('attendanceTrendsChart');
        if (trendsCtx) {
            const trendsDataScript = document.getElementById('attendance-trends-data');
            const trendsData = trendsDataScript ? JSON.parse(trendsDataScript.textContent) : {
                labels: [],
                employees: [],
                onTime: [],
                late: []
            };
            
            const trendsChart = new Chart(trendsCtx.getContext('2d'), {
                type: 'line',
                data: {
                    labels: trendsData.labels,
                    datasets: [{
                        label: 'Pegawai Total',
                        data: trendsData.employees,
                        borderColor: '#3B82F6',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        borderWidth: 4,
                        fill: false,
                        tension: 0.4,
                        pointRadius: 5,
                        pointHoverRadius: 7
                    }, {
                        label: 'Pegawai On-Time',
                        data: trendsData.onTime,
                        borderColor: '#10B981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        borderWidth: 4,
                        fill: false,
                        tension: 0.4,
                        pointRadius: 5,
                        pointHoverRadius: 7
                    }, {
                        label: 'Pegawai Terlambat',
                        data: trendsData.late,
                        borderColor: '#EF4444',
                        backgroundColor: 'rgba(239, 68, 68, 0.1)',
                        borderWidth: 4,
                        fill: false,
                        tension: 0.4,
                        pointRadius: 5,
                        pointHoverRadius: 7
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        intersect: false,
                        mode: 'index'
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.1)',
                                lineWidth: 1
                            },
                            ticks: {
                                font: {
                                    size: 14
                                },
                                color: '#666'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                font: {
                                    size: 12
                                },
                                color: '#666',
                                maxRotation: 45,
                                minRotation: 0
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                usePointStyle: true,
                                padding: 25,
                                font: {
                                    size: 14
                                }
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            titleFont: {
                                size: 16
                            },
                            bodyFont: {
                                size: 14
                            },
                            padding: 12
                        }
                    }
                }
            });
        }
    });
</script>
@endpush