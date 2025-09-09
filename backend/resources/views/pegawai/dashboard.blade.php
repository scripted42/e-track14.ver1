@extends('admin.layouts.app')

@section('title', 'Dashboard Pegawai')

@push('styles')
<style>
.action-btn {
    padding: 0.375rem 0.75rem;
    font-size: 0.875rem;
    border-radius: 4px;
    border: none;
    transition: all 0.2s ease;
}

.action-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.table th {
    background: #f8f9fa;
    border-bottom: 2px solid #dee2e6;
    font-weight: 600;
    color: #495057;
    padding: 1rem 0.75rem;
}

.table td {
    padding: 1rem 0.75rem;
    vertical-align: middle;
    border-bottom: 1px solid #dee2e6;
}

.status-badge {
    padding: 0.375rem 0.75rem;
    border-radius: 4px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
}

.status-hadir {
    background: #d4edda;
    color: #155724;
}

.status-terlambat {
    background: #fff3cd;
    color: #856404;
}

.status-izin {
    background: #cce5ff;
    color: #004085;
}

.status-sakit {
    background: #f8d7da;
    color: #721c24;
}

.status-alpha {
    background: #f5c6cb;
    color: #721c24;
}

.type-badge {
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
}

.type-checkin {
    background: #d1ecf1;
    color: #0c5460;
}

.type-checkout {
    background: #e2e3e5;
    color: #383d41;
}

.weekly-attendance {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 1rem;
    flex-wrap: wrap;
}

.weekly-day {
    text-align: center;
    min-width: 80px;
    padding: 1rem;
    border-radius: 8px;
    background: #f8f9fa;
    border: 1px solid #dee2e6;
}

.weekly-day.present {
    background: #d4edda;
    border-color: #c3e6cb;
}

.weekly-day.late {
    background: #fff3cd;
    border-color: #ffeaa7;
}

.weekly-day.absent {
    background: #f8d7da;
    border-color: #f5c6cb;
}
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Dashboard Pegawai</h1>
        <div class="text-muted">
            <i class="fas fa-calendar-alt"></i> {{ now()->format('d F Y') }}
        </div>
    </div>

    <!-- Ringkasan Periode -->
    <div class="row mb-4">
        <!-- Hari Ini -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Hari Ini
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $todayStats['attendance_rate'] }}%
                            </div>
                            <div class="text-xs text-muted">
                                @if($todayStats['present_days'] > 0)
                                    Hadir: {{ $todayStats['present_days'] }} hari
                                    @if($todayStats['late_days'] > 0)
                                        ({{ $todayStats['late_days'] }} terlambat)
                                    @endif
                                @elseif($todayStats['leave_days'] > 0)
                                    Izin: {{ $todayStats['leave_days'] }} hari
                                @else
                                    Belum ada data
                                @endif
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-day fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Minggu Ini -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Minggu Ini
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $weekStats['attendance_rate'] }}%
                            </div>
                            <div class="text-xs text-muted">
                                Hadir: {{ $weekStats['present_days'] }}/{{ $weekStats['total_days'] }} hari
                                @if($weekStats['leave_days'] > 0)
                                    | Izin: {{ $weekStats['leave_days'] }} hari
                                @endif
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-week fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bulan Ini -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Bulan Ini
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $monthStats['attendance_rate'] }}%
                            </div>
                            <div class="text-xs text-muted">
                                Hadir: {{ $monthStats['present_days'] }}/{{ $monthStats['total_days'] }} hari
                                @if($monthStats['leave_days'] > 0)
                                    | Izin: {{ $monthStats['leave_days'] }} hari
                                @endif
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detail Kehadiran -->
    <div class="row mb-4">
        <!-- Persentase Ketepatan Waktu -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Ketepatan Waktu</h6>
                    <small class="text-muted">(Tepat waktu / Total hadir) × 100%</small>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="text-center">
                                <div class="h4 text-success">{{ $weekStats['punctuality_rate'] }}%</div>
                                <div class="text-sm text-muted">Minggu Ini</div>
                                <div class="text-xs text-muted">
                                    {{ $weekStats['on_time_days'] }}/{{ $weekStats['present_days'] }} hari tepat waktu
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center">
                                <div class="h4 text-info">{{ $monthStats['punctuality_rate'] }}%</div>
                                <div class="text-sm text-muted">Bulan Ini</div>
                                <div class="text-xs text-muted">
                                    {{ $monthStats['on_time_days'] }}/{{ $monthStats['present_days'] }} hari tepat waktu
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ringkasan Izin -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Ringkasan Izin</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-4">
                            <div class="text-center">
                                <div class="h4 text-warning">{{ $todayStats['leave_days'] }}</div>
                                <div class="text-sm text-muted">Hari Ini</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="text-center">
                                <div class="h4 text-info">{{ $weekStats['leave_days'] }}</div>
                                <div class="text-sm text-muted">Minggu Ini</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="text-center">
                                <div class="h4 text-primary">{{ $monthStats['leave_days'] }}</div>
                                <div class="text-sm text-muted">Bulan Ini</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart Kehadiran Minggu Ini -->
    <div class="row mb-4">
        <div class="col-xl-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Kehadiran Minggu Ini</h6>
                </div>
                <div class="card-body">
                    <div class="weekly-attendance">
                        @foreach($weeklyChartData as $day)
                        <div class="weekly-day {{ $day['status'] === 'hadir' ? 'present' : ($day['status'] === 'terlambat' ? 'late' : 'absent') }}">
                            <div class="fw-bold text-muted">{{ $day['day'] }}</div>
                            <div class="small text-muted">{{ $day['date'] }}</div>
                            <div class="mt-2">
                                @if($day['status'] === 'hadir')
                                    <i class="fas fa-check-circle text-success fa-2x"></i>
                                    <div class="small text-success mt-1">{{ $day['time'] }}</div>
                                @elseif($day['status'] === 'terlambat')
                                    <i class="fas fa-clock text-warning fa-2x"></i>
                                    <div class="small text-warning mt-1">{{ $day['time'] }}</div>
                                @else
                                    <i class="fas fa-times-circle text-danger fa-2x"></i>
                                    <div class="small text-muted mt-1">-</div>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detail Kehadiran Hari Ini -->
    <div class="row mb-4">
        <div class="col-xl-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Detail Kehadiran Hari Ini</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th width="50" class="text-center">No</th>
                                    <th>Nama</th>
                                    <th>Role</th>
                                    <th>Tanggal</th>
                                    <th>Check-in</th>
                                    <th>Check-out</th>
                                    <th>Status</th>
                                    <th>Evidence</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="text-center">1</td>
                                    <td>
                                        <div class="fw-semibold">{{ $todayAttendanceFormatted['user']->name ?? 'N/A' }}</div>
                                        <small class="text-muted">{{ $todayAttendanceFormatted['user']->email ?? 'N/A' }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $todayAttendanceFormatted['user']->roles->first() ? $todayAttendanceFormatted['user']->roles->first()->name : 'No Role' }}</span>
                                    </td>
                                    <td>
                                        <span class="fw-medium">{{ \Carbon\Carbon::parse($todayAttendanceFormatted['date'])->format('d M Y') }}</span>
                                    </td>
                                    <td>
                                        @if($todayAttendanceFormatted['checkin'])
                                            <div>
                                                <span class="fw-medium text-success">{{ $todayAttendanceFormatted['checkin']['time'] }}</span>
                                                <br>
                                                <small class="text-muted">
                                                    <i class="fas fa-map-marker-alt"></i> Lokasi tercatat
                                                </small>
                                            </div>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($todayAttendanceFormatted['checkout'])
                                            <div>
                                                <span class="fw-medium text-danger">{{ $todayAttendanceFormatted['checkout']['time'] }}</span>
                                                <br>
                                                <small class="text-muted">
                                                    <i class="fas fa-map-marker-alt"></i> Lokasi tercatat
                                                </small>
                                            </div>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column gap-1">
                                            @if(isset($todayAttendanceFormatted['leave']))
                                                <span class="status-badge status-{{ $todayAttendanceFormatted['leave']['type'] }}">
                                                    {{ ucfirst($todayAttendanceFormatted['leave']['type']) }}
                                                </span>
                                                @if($todayAttendanceFormatted['leave']['status'] === 'disetujui')
                                                    <small class="text-success">✓ Disetujui</small>
                                                @elseif($todayAttendanceFormatted['leave']['status'] === 'ditolak')
                                                    <small class="text-danger">✗ Ditolak</small>
                                                @else
                                                    <small class="text-warning">⏳ Menunggu</small>
                                                @endif
                                            @elseif($todayAttendanceFormatted['checkin'])
                                                <span class="status-badge status-{{ strtolower(str_replace(' ', '-', $todayAttendanceFormatted['checkin']['status'])) }}">
                                                    Check-in: {{ $todayAttendanceFormatted['checkin']['status'] }}
                                                </span>
                                            @endif
                                            @if($todayAttendanceFormatted['checkout'])
                                                <span class="status-badge status-{{ strtolower(str_replace(' ', '-', $todayAttendanceFormatted['checkout']['status'])) }}">
                                                    Check-out: {{ $todayAttendanceFormatted['checkout']['status'] }}
                                                </span>
                                            @endif
                                            @if(!$todayAttendanceFormatted['checkin'] && !$todayAttendanceFormatted['checkout'] && !isset($todayAttendanceFormatted['leave']))
                                                <span class="status-badge status-alpha">
                                                    Alpha
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        @if(isset($todayAttendanceFormatted['leave']) && $todayAttendanceFormatted['leave']['evidence_path'])
                                            <span class="text-success">
                                                <i class="fas fa-file-alt me-1"></i>Evidence
                                            </span>
                                        @elseif($todayAttendanceFormatted['checkin'] && $todayAttendanceFormatted['checkin']['photo_path'])
                                            <span class="text-success">
                                                <i class="fas fa-camera me-1"></i>Foto
                                            </span>
                                        @elseif($todayAttendanceFormatted['checkout'] && $todayAttendanceFormatted['checkout']['photo_path'])
                                            <span class="text-success">
                                                <i class="fas fa-camera me-1"></i>Foto
                                            </span>
                                        @else
                                            <span class="text-muted">
                                                <i class="fas fa-minus me-1"></i>Tidak ada
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-1">
                                            <button type="button" 
                                                    class="action-btn btn btn-info" 
                                                    title="Lihat Detail"
                                                    onclick="showDetail('{{ $todayAttendanceFormatted['user']->id }}', '{{ $todayAttendanceFormatted['date'] }}')">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Izin Terbaru -->
    <div class="row">
        <div class="col-xl-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Izin Terbaru</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th width="50" class="text-center">No</th>
                                    <th>Tanggal Pengajuan</th>
                                    <th>Tipe</th>
                                    <th>Periode</th>
                                    <th>Durasi</th>
                                    <th>Alasan</th>
                                    <th>Status</th>
                                    <th>Evidence</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentLeaves as $index => $leave)
                                <tr>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td>
                                        <div class="fw-semibold">{{ $leave->created_at->format('d M Y') }}</div>
                                        <small class="text-muted">{{ $leave->created_at->format('H:i') }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">{{ $leave->getLeaveTypeLabel() }}</span>
                                    </td>
                                    <td>
                                        <div class="fw-semibold">
                                            {{ $leave->start_date->format('d M') }} - 
                                            {{ $leave->end_date->format('d M Y') }}
                                        </div>
                                        <small class="text-muted">
                                            {{ $leave->start_date->format('l') }} - 
                                            {{ $leave->end_date->format('l') }}
                                        </small>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $leave->getDurationDays() }} hari</span>
                                    </td>
                                    <td>
                                        <div class="text-truncate" style="max-width: 200px;" title="{{ $leave->reason }}">
                                            {{ $leave->reason }}
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge {{ $leave->status === 'disetujui' ? 'bg-success' : ($leave->status === 'ditolak' ? 'bg-danger' : 'bg-warning') }}">
                                            {{ $leave->getStatusLabel() }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($leave->evidence_path)
                                            <span class="text-success">
                                                <i class="fas fa-file-alt me-1"></i>Evidence
                                            </span>
                                        @else
                                            <span class="text-muted">
                                                <i class="fas fa-minus me-1"></i>Tidak ada
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-1">
                                            <button type="button" 
                                                    class="action-btn btn btn-info" 
                                                    title="Lihat Detail"
                                                    onclick="showLeaveDetail({{ $leave->id }})">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center text-muted">Belum ada data izin</td>
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
function showDetail(userId, date) {
    // Implementasi untuk menampilkan detail kehadiran
    // Bisa diarahkan ke halaman detail atau modal
    alert('Detail kehadiran untuk user ID: ' + userId + ' pada tanggal: ' + date);
}

function showLeaveDetail(leaveId) {
    // Implementasi untuk menampilkan detail izin
    // Bisa diarahkan ke halaman detail atau modal
    alert('Detail izin untuk ID: ' + leaveId);
}
</script>
@endsection