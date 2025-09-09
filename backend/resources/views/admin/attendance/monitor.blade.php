@extends('admin.layouts.app')

@section('title', 'Dashboard Langsung - Monitoring Kehadiran')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-tv text-warning me-2"></i>
                Dashboard Langsung
            </h1>
            <p class="text-muted mb-0">Monitoring kehadiran real-time - {{ \Carbon\Carbon::now()->format('d F Y, H:i') }}</p>
        </div>
        <div>
            <button class="btn btn-outline-primary" onclick="refreshData()">
                <i class="fas fa-sync-alt me-1"></i>
                Refresh Data
            </button>
            <button class="btn btn-outline-success" onclick="toggleAutoRefresh()">
                <i class="fas fa-play me-1" id="autoRefreshIcon"></i>
                <span id="autoRefreshText">Auto Refresh</span>
            </button>
        </div>
    </div>

    <!-- Real-time Statistics Cards -->
    <div class="row mb-2">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Staff
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalStaff">
                                {{ $totalStaff }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Hadir Hari Ini
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="presentToday">
                                {{ $presentToday }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2 clickable-card" 
                 onclick="showStaffModal('late')" 
                 style="cursor: pointer; transition: transform 0.2s ease;">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Terlambat
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="lateToday">
                                {{ $lateToday }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2 clickable-card" 
                 onclick="showStaffModal('absent')" 
                 style="cursor: pointer; transition: transform 0.2s ease;">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Tidak Hadir
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="absentToday">
                                {{ $absentToday }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-times-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Student Attendance Cards -->
    <div class="row mb-2">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Siswa
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalStudents">
                                {{ $totalStudents }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Hadir
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="presentStudents">
                                {{ $presentStudents }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2 clickable-card" 
                 onclick="showStudentModal('late')" 
                 style="cursor: pointer; transition: transform 0.2s ease;">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Terlambat
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="lateStudents">
                                {{ $lateStudents }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2 clickable-card" 
                 onclick="showStudentModal('absent')" 
                 style="cursor: pointer; transition: transform 0.2s ease;">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Tidak Hadir
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="absentStudents">
                                {{ $absentStudents }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-times-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Attendance Rate Progress Bar -->
    <div class="row mb-2">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-pie me-2"></i>
                        Tingkat Kehadiran Hari Ini
                    </h6>
                </div>
                <div class="card-body">
                    <div class="progress mb-3" style="height: 30px;">
                        <div class="progress-bar bg-success" role="progressbar" 
                             style="width: {{ $attendanceRate }}%" 
                             aria-valuenow="{{ $attendanceRate }}" 
                             aria-valuemin="0" 
                             aria-valuemax="100">
                            <strong>{{ $attendanceRate }}%</strong>
                        </div>
                    </div>
                    <div class="row text-center">
                        <div class="col-md-3">
                            <small class="text-muted">Target: 95%</small>
                        </div>
                        <div class="col-md-3">
                            <small class="text-success">Hadir: {{ $presentToday }}/{{ $totalStaff }}</small>
                        </div>
                        <div class="col-md-3">
                            <small class="text-warning">Terlambat: {{ $lateToday }}</small>
                        </div>
                        <div class="col-md-3">
                            <small class="text-danger">Tidak Hadir: {{ $absentToday }}</small>
                        </div>
                    </div>
                    
                    <!-- Student Attendance Rate -->
                    <hr class="my-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="text-primary mb-0">
                            <i class="fas fa-graduation-cap me-2"></i>
                            Tingkat Kehadiran Siswa
                        </h6>
                        <span class="badge bg-primary fs-6">{{ $studentAttendanceRate }}%</span>
                    </div>
                    <div class="progress mb-3" style="height: 30px;">
                        <div class="progress-bar bg-primary" role="progressbar" 
                             style="width: {{ $studentAttendanceRate }}%" 
                             aria-valuenow="{{ $studentAttendanceRate }}" 
                             aria-valuemin="0" 
                             aria-valuemax="100">
                            <strong>{{ $studentAttendanceRate }}%</strong>
                        </div>
                    </div>
                    <div class="row text-center">
                        <div class="col-md-3">
                            <small class="text-muted">Target: 95%</small>
                        </div>
                        <div class="col-md-3">
                            <small class="text-success">Hadir: {{ $presentStudents }}/{{ $totalStudents }}</small>
                        </div>
                        <div class="col-md-3">
                            <small class="text-warning">Terlambat: {{ $lateStudents }}</small>
                        </div>
                        <div class="col-md-3">
                            <small class="text-danger">Tidak Hadir: {{ $absentStudents }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Recent Activities -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-history me-2"></i>
                        Aktivitas Terbaru
                    </h6>
                </div>
                <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                    @forelse($recentActivities as $activity)
                    <div class="d-flex align-items-center mb-3 p-2 border-left border-{{ $activity->status_class }} bg-light">
                        <div class="flex-shrink-0 me-3">
                            <i class="fas {{ $activity->status_icon }} text-{{ $activity->status_class }}"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>{{ $activity->user->name }}</strong>
                                    <span class="badge bg-{{ $activity->status_class }} ms-2">{{ $activity->status_text }}</span>
                                </div>
                                <small class="text-muted">{{ \Carbon\Carbon::parse($activity->timestamp)->format('H:i') }}</small>
                            </div>
                            <div class="text-muted small">
                                {{ $activity->user->role->role_name }} • 
                                @if($activity->activity_type === 'attendance')
                                    {{ $activity->type === 'checkin' ? 'Check In' : 'Check Out' }}
                                @else
                                    {{ $activity->leave_type ?? 'Izin' }}
                                @endif
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-inbox fa-3x mb-3"></i>
                        <p>Tidak ada aktivitas kehadiran hari ini</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Department Statistics -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-building me-2"></i>
                        Statistik Departemen
                    </h6>
                </div>
                <div class="card-body">
                    @foreach($departmentStats as $dept)
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="fw-bold">{{ $dept['department'] }}</span>
                            <span class="badge bg-{{ $dept['rate'] >= 90 ? 'success' : ($dept['rate'] >= 70 ? 'warning' : 'danger') }}">
                                {{ $dept['rate'] }}%
                            </span>
                        </div>
                        <div class="progress mb-1" style="height: 6px;">
                            <div class="progress-bar bg-{{ $dept['rate'] >= 90 ? 'success' : ($dept['rate'] >= 70 ? 'warning' : 'danger') }}" 
                                 style="width: {{ $dept['rate'] }}%"></div>
                        </div>
                        <small class="text-muted">
                            {{ $dept['present'] }}/{{ $dept['total'] }} hadir
                        </small>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Hourly Distribution Chart -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-bar me-2"></i>
                        Distribusi Kehadiran Per Jam
                    </h6>
                </div>
                <div class="card-body">
                    <canvas id="hourlyChart" height="80"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Today's Leave Requests -->
    @if($todayLeaves->count() > 0)
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-info">
                        <i class="fas fa-calendar-times me-2"></i>
                        Izin Hari Ini
                    </h6>
                    <div class="btn-group btn-group-sm" role="group">
                        <button type="button" class="btn btn-outline-info active" onclick="showLeaveView('table')" id="tableBtn">
                            <i class="fas fa-table"></i> Table
                        </button>
                        <button type="button" class="btn btn-outline-info" onclick="showLeaveView('cards')" id="cardsBtn">
                            <i class="fas fa-th-large"></i> Cards
                        </button>
                        <button type="button" class="btn btn-outline-info" onclick="showLeaveView('compact')" id="compactBtn">
                            <i class="fas fa-list"></i> Compact
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Table View -->
                    <div id="tableView" class="leave-view">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Nama</th>
                                        <th>Role</th>
                                        <th>Jenis Izin</th>
                                        <th>Alasan</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($todayLeaves as $leave)
                                    <tr>
                                        <td><strong>{{ $leave->user->name }}</strong></td>
                                        <td><span class="badge bg-secondary">{{ $leave->user->role->role_name }}</span></td>
                                        <td><span class="badge bg-info">{{ ucfirst($leave->leave_type) }}</span></td>
                                        <td class="text-muted">{{ Str::limit($leave->reason, 30) }}</td>
                                        <td><span class="badge bg-success">Disetujui</span></td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Cards View -->
                    <div id="cardsView" class="leave-view" style="display: none;">
                        <div class="row">
                            @foreach($todayLeaves as $leave)
                            <div class="col-md-6 col-lg-4 mb-3">
                                <div class="card border-left-info h-100">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center mb-2">
                                            <i class="fas fa-user-circle text-info me-2"></i>
                                            <strong>{{ $leave->user->name }}</strong>
                                        </div>
                                        <div class="mb-2">
                                            <span class="badge bg-secondary">{{ $leave->user->role->role_name }}</span>
                                            <span class="badge bg-info ms-1">{{ ucfirst($leave->leave_type) }}</span>
                                        </div>
                                        <p class="text-muted small mb-2">{{ Str::limit($leave->reason, 50) }}</p>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="badge bg-success">Disetujui</span>
                                            <small class="text-muted">
                                                <i class="fas fa-clock me-1"></i>
                                                {{ \Carbon\Carbon::parse($leave->created_at)->format('H:i') }}
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Compact View -->
                    <div id="compactView" class="leave-view" style="display: none;">
                        @foreach($todayLeaves as $leave)
                        <div class="d-flex align-items-center justify-content-between p-2 border-bottom">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-user-circle text-info me-2"></i>
                                <div>
                                    <strong>{{ $leave->user->name }}</strong>
                                    <span class="badge bg-secondary ms-2">{{ $leave->user->role->role_name }}</span>
                                </div>
                            </div>
                            <div class="d-flex align-items-center">
                                <span class="badge bg-info me-2">{{ ucfirst($leave->leave_type) }}</span>
                                <span class="badge bg-success">Disetujui</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Staff Detail Modal -->
<div class="modal fade" id="staffModal" tabindex="-1" aria-labelledby="staffModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staffModalLabel">
                    <i class="fas fa-users me-2"></i>
                    <span id="modalTitle">Detail Staff</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="staffContent">
                    <!-- Content will be loaded here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Student Detail Modal -->
<div class="modal fade" id="studentModal" tabindex="-1" aria-labelledby="studentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="studentModalLabel">
                    <i class="fas fa-graduation-cap me-2"></i>
                    <span id="studentModalTitle">Detail Siswa</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="studentContent">
                    <!-- Content will be loaded here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js -->
@vite(["resources/js/app.js"])

<script>
let autoRefreshInterval;
let isAutoRefresh = false;

// Initialize chart
const ctx = document.getElementById('hourlyChart').getContext('2d');
const hourlyChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: {!! json_encode(array_column($hourlyData, 'hour')) !!},
        datasets: [{
            label: 'Jumlah Kehadiran',
            data: {!! json_encode(array_column($hourlyData, 'count')) !!},
            backgroundColor: 'rgba(54, 162, 235, 0.6)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            }
        },
        plugins: {
            legend: {
                display: false
            }
        }
    }
});

// Auto refresh functionality
function refreshData() {
    location.reload();
}

function toggleAutoRefresh() {
    if (isAutoRefresh) {
        clearInterval(autoRefreshInterval);
        document.getElementById('autoRefreshIcon').className = 'fas fa-play me-1';
        document.getElementById('autoRefreshText').textContent = 'Auto Refresh';
        isAutoRefresh = false;
    } else {
        autoRefreshInterval = setInterval(refreshData, 30000); // Refresh every 30 seconds
        document.getElementById('autoRefreshIcon').className = 'fas fa-pause me-1';
        document.getElementById('autoRefreshText').textContent = 'Stop Auto';
        isAutoRefresh = true;
    }
}

// Update time display every second
setInterval(function() {
    const now = new Date();
    const timeString = now.toLocaleString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
    document.querySelector('.text-muted.mb-0').textContent = 'Monitoring kehadiran real-time - ' + timeString;
}, 1000);

// Start auto refresh by default
document.addEventListener('DOMContentLoaded', function() {
    toggleAutoRefresh();
});

// Leave view toggle function
function showLeaveView(viewType) {
    // Hide all views
    document.querySelectorAll('.leave-view').forEach(view => {
        view.style.display = 'none';
    });
    
    // Remove active class from all buttons
    document.querySelectorAll('.btn-group .btn').forEach(btn => {
        btn.classList.remove('active');
    });
    
    // Show selected view
    document.getElementById(viewType + 'View').style.display = 'block';
    
    // Add active class to clicked button
    document.getElementById(viewType + 'Btn').classList.add('active');
}

// Staff data from PHP
const lateStaff = @json($lateStaff);
const absentStaff = @json($absentStaff);

// Student data from PHP
const lateStudentDetails = @json($lateStudentDetails);
const absentStudentDetails = @json($absentStudentDetails);

// Show staff modal function
function showStaffModal(type) {
    const modal = new bootstrap.Modal(document.getElementById('staffModal'));
    const modalTitle = document.getElementById('modalTitle');
    const staffContent = document.getElementById('staffContent');
    
    let title, content, staffData;
    
    if (type === 'late') {
        title = 'Staff Terlambat';
        staffData = lateStaff;
        content = generateLateStaffContent(staffData);
    } else if (type === 'absent') {
        title = 'Staff Tidak Hadir';
        staffData = absentStaff;
        content = generateAbsentStaffContent(staffData);
    }
    
    modalTitle.textContent = title;
    staffContent.innerHTML = content;
    modal.show();
}

// Generate late staff content
function generateLateStaffContent(staff) {
    if (staff.length === 0) {
        return '<div class="text-center text-muted py-4"><i class="fas fa-check-circle fa-3x mb-3"></i><p>Tidak ada staff yang terlambat</p></div>';
    }
    
    let content = '<div class="table-responsive"><table class="table table-hover"><thead class="table-light"><tr><th>Nama</th><th>Role</th><th>Waktu Check-in</th><th>Terlambat</th></tr></thead><tbody>';
    
    staff.forEach(attendance => {
        // Use the check_in_time from server instead of parsing timestamp
        const checkInTime = attendance.check_in_time || new Date(attendance.timestamp).toLocaleTimeString('id-ID', {hour: '2-digit', minute: '2-digit'});
        const lateMinutes = attendance.late_minutes;
        const hours = Math.floor(lateMinutes / 60);
        const minutes = lateMinutes % 60;
        const lateText = hours > 0 ? `${hours}j ${minutes}m` : `${minutes}m`;
        
        content += `
            <tr>
                <td><strong>${attendance.user.name}</strong></td>
                <td><span class="badge bg-secondary">${attendance.user.role.role_name}</span></td>
                <td>${checkInTime}</td>
                <td><span class="badge bg-warning">${lateText}</span></td>
            </tr>
        `;
    });
    
    content += '</tbody></table></div>';
    return content;
}

// Generate absent staff content
function generateAbsentStaffContent(staff) {
    if (staff.length === 0) {
        return '<div class="text-center text-muted py-4"><i class="fas fa-check-circle fa-3x mb-3"></i><p>Tidak ada staff yang tidak hadir</p></div>';
    }
    
    let content = '<div class="table-responsive"><table class="table table-hover"><thead class="table-light"><tr><th>Nama</th><th>Role</th><th>Status</th></tr></thead><tbody>';
    
    staff.forEach(user => {
        content += `
            <tr>
                <td><strong>${user.name}</strong></td>
                <td><span class="badge bg-secondary">${user.role.role_name}</span></td>
                <td><span class="badge bg-danger">Tidak Hadir</span></td>
            </tr>
        `;
    });
    
    content += '</tbody></table></div>';
    return content;
}

// Show student modal function
function showStudentModal(type) {
    const modal = new bootstrap.Modal(document.getElementById('studentModal'));
    const modalTitle = document.getElementById('studentModalTitle');
    const studentContent = document.getElementById('studentContent');
    
    let title, content, studentData;
    
    if (type === 'late') {
        title = 'Siswa Terlambat';
        studentData = lateStudentDetails;
        content = generateLateStudentContent(studentData);
    } else if (type === 'absent') {
        title = 'Siswa Tidak Hadir';
        studentData = absentStudentDetails;
        content = generateAbsentStudentContent(studentData);
    }
    
    modalTitle.textContent = title;
    studentContent.innerHTML = content;
    modal.show();
}

// Generate late student content
function generateLateStudentContent(students) {
    if (students.length === 0) {
        return '<div class="text-center text-muted py-4"><i class="fas fa-check-circle fa-3x mb-3"></i><p>Tidak ada siswa yang terlambat</p></div>';
    }
    
    let content = '<div class="table-responsive"><table class="table table-hover"><thead class="table-light"><tr><th>Nama</th><th>Kelas</th><th>Waktu Check-in</th><th>Terlambat</th></tr></thead><tbody>';
    
    students.forEach(attendance => {
        const checkInTime = new Date(attendance.created_at);
        const lateMinutes = attendance.late_minutes;
        const hours = Math.floor(lateMinutes / 60);
        const minutes = lateMinutes % 60;
        const lateText = hours > 0 ? `${hours}j ${minutes}m` : `${minutes}m`;
        
        content += `
            <tr>
                <td><strong>${attendance.student.name}</strong></td>
                <td><span class="badge bg-info">${attendance.student.class_room ? attendance.student.class_room.name : 'N/A'}</span></td>
                <td>${checkInTime.toLocaleTimeString('id-ID', {hour: '2-digit', minute: '2-digit'})}</td>
                <td><span class="badge bg-warning">${lateText}</span></td>
            </tr>
        `;
    });
    
    content += '</tbody></table></div>';
    return content;
}

// Generate absent student content
function generateAbsentStudentContent(students) {
    if (students.length === 0) {
        return '<div class="text-center text-muted py-4"><i class="fas fa-check-circle fa-3x mb-3"></i><p>Tidak ada siswa yang tidak hadir</p></div>';
    }
    
    let content = '<div class="table-responsive"><table class="table table-hover"><thead class="table-light"><tr><th>Nama</th><th>Kelas</th><th>Status</th></tr></thead><tbody>';
    
    students.forEach(student => {
        content += `
            <tr>
                <td><strong>${student.name}</strong></td>
                <td><span class="badge bg-info">${student.class_room ? student.class_room.name : 'N/A'}</span></td>
                <td><span class="badge bg-danger">Tidak Hadir</span></td>
            </tr>
        `;
    });
    
    content += '</tbody></table></div>';
    return content;
}
</script>

<style>
.border-left {
    border-left: 4px solid !important;
}

.border-left-primary {
    border-left-color: #4e73df !important;
}

.border-left-success {
    border-left-color: #1cc88a !important;
}

.border-left-warning {
    border-left-color: #f6c23e !important;
}

.border-left-danger {
    border-left-color: #e74a3b !important;
}

.border-left-info {
    border-left-color: #36b9cc !important;
}

.card {
    transition: transform 0.2s ease-in-out;
}

.card:hover {
    transform: translateY(-2px);
}

.progress {
    border-radius: 10px;
}

.progress-bar {
    border-radius: 10px;
}

/* Simple Activity Items */
.border-left {
    border-left: 4px solid !important;
}

.border-left-success {
    border-left-color: #28a745 !important;
}

.border-left-warning {
    border-left-color: #ffc107 !important;
}

.border-left-info {
    border-left-color: #17a2b8 !important;
}

.border-left-secondary {
    border-left-color: #6c757d !important;
}

/* Simple Scrollbar */
.card-body::-webkit-scrollbar {
    width: 4px;
}

.card-body::-webkit-scrollbar-track {
    background: #f1f1f1;
}

.card-body::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 2px;
}

/* Button Group Styling */
.btn-group .btn {
    border-radius: 0.375rem;
    margin-right: 2px;
}

.btn-group .btn:first-child {
    border-top-right-radius: 0;
    border-bottom-right-radius: 0;
}

.btn-group .btn:last-child {
    border-top-left-radius: 0;
    border-bottom-left-radius: 0;
    margin-right: 0;
}

.btn-group .btn:not(:first-child):not(:last-child) {
    border-radius: 0;
}

.btn-group .btn.active {
    background-color: #0dcaf0;
    border-color: #0dcaf0;
    color: white;
}

/* Table Styling */
.table th {
    border-top: none;
    font-weight: 600;
    color: #495057;
}

.table-hover tbody tr:hover {
    background-color: rgba(13, 202, 240, 0.1);
}

/* Cards View Styling */
.card.border-left-info {
    transition: transform 0.2s ease;
}

.card.border-left-info:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

/* Clickable Cards */
.clickable-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 12px rgba(0,0,0,0.15);
}

.clickable-card:active {
    transform: translateY(-1px);
}

/* Modal Styling */
.modal-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
}

.modal-title {
    color: #495057;
    font-weight: 600;
}

/* Table in Modal */
.modal .table th {
    background-color: #f8f9fa;
    border-top: none;
    font-weight: 600;
    color: #495057;
}

.modal .table-hover tbody tr:hover {
    background-color: rgba(13, 202, 240, 0.1);
}
</style>
@endsection
