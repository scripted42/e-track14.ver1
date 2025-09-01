@extends('admin.layouts.app')

@section('title', 'Laporan Kehadiran Pegawai')

@push('styles')
<style>
.filter-card {
    background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
    border: none;
    border-radius: 1rem;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.data-table {
    background: white;
    border-radius: 1rem;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    overflow: hidden;
}

.table th {
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    color: white;
    border: none;
    font-weight: 600;
    padding: 1rem;
    text-transform: uppercase;
    font-size: 0.75rem;
    letter-spacing: 0.5px;
}

.table td {
    padding: 1rem;
    vertical-align: middle;
    border-color: #e2e8f0;
}

.table tbody tr:hover {
    background-color: #f8fafc;
    transform: translateX(2px);
    transition: all 0.3s ease;
}

.status-badge {
    padding: 0.375rem 0.75rem;
    border-radius: 0.5rem;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
}

.status-hadir {
    background-color: #dcfce7;
    color: #166534;
}

.status-terlambat {
    background-color: #fef3c7;
    color: #92400e;
}

.status-izin {
    background-color: #dbeafe;
    color: #1e40af;
}

.status-sakit {
    background-color: #fee2e2;
    color: #991b1b;
}

.stats-card {
    background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
    border: 1px solid #e2e8f0;
    border-radius: 1rem;
    padding: 1.5rem;
    text-align: center;
    transition: all 0.3s ease;
}

.stats-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px -3px rgba(0, 0, 0, 0.1);
}

.stats-value {
    font-size: 2rem;
    font-weight: bold;
    margin-bottom: 0.5rem;
}

.stats-label {
    color: #64748b;
    font-size: 0.875rem;
    font-weight: 500;
}

.attendance-rate {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.rate-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.75rem;
    font-weight: bold;
    color: white;
}

.rate-excellent { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
.rate-good { background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); }
.rate-average { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }
.rate-poor { background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); }

.btn-modern {
    border-radius: 0.5rem;
    padding: 0.75rem 1.5rem;
    font-weight: 500;
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.btn-modern:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.form-control, .form-select {
    border-radius: 0.5rem;
    border: 1px solid #d1d5db;
    padding: 0.75rem 1rem;
    transition: all 0.3s ease;
}

.form-control:focus, .form-select:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-1 text-gray-800">👥 Laporan Kehadiran Pegawai</h1>
                    <p class="text-muted mb-0">Pantau dan analisis kehadiran pegawai secara detail</p>
                </div>
                <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-secondary btn-modern">
                    <i class="fas fa-arrow-left me-2"></i>Kembali ke Dashboard
                </a>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="filter-card card">
                <div class="card-body">
                    <h5 class="card-title mb-3">
                        <i class="fas fa-filter text-primary me-2"></i>Filter Laporan
                    </h5>
                    <form method="GET" action="{{ route('admin.reports.attendance') }}">
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label for="year" class="form-label fw-semibold">Tahun</label>
                                <select class="form-select" id="year" name="year">
                                    @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="month" class="form-label fw-semibold">Bulan</label>
                                <select class="form-select" id="month" name="month">
                                    @php
                                        $months = [
                                            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
                                            '04' => 'April', '05' => 'Mei', '06' => 'Juni',
                                            '07' => 'Juli', '08' => 'Agustus', '09' => 'September',
                                            '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
                                        ];
                                    @endphp
                                    @foreach($months as $num => $name)
                                        <option value="{{ $num }}" {{ $month == $num ? 'selected' : '' }}>{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="user_id" class="form-label fw-semibold">Pegawai</label>
                                <select class="form-select" id="user_id" name="user_id">
                                    <option value="">Semua Pegawai</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ $userId == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2 mb-3 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary btn-modern w-100">
                                    <i class="fas fa-search me-2"></i>Filter
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Summary -->
    @if($userStats->count() > 0)
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="stats-card">
                <div class="stats-value text-primary">{{ $userStats->count() }}</div>
                <div class="stats-label">Total Pegawai</div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stats-card">
                <div class="stats-value text-success">{{ $userStats->sum('present') }}</div>
                <div class="stats-label">Total Hadir</div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stats-card">
                <div class="stats-value text-warning">{{ $userStats->sum('late') }}</div>
                <div class="stats-label">Total Terlambat</div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stats-card">
                <div class="stats-value text-info">{{ $userStats->sum('leave') }}</div>
                <div class="stats-label">Total Izin</div>
            </div>
        </div>
    </div>
    @endif

    <!-- User Statistics Table -->
    @if($userStats->count() > 0)
    <div class="row mb-4">
        <div class="col-12">
            <div class="data-table">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-bar me-2"></i>Ringkasan Kehadiran Pegawai
                    </h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Pegawai</th>
                                <th>Jabatan</th>
                                <th>Total Hari</th>
                                <th>Hadir</th>
                                <th>Terlambat</th>
                                <th>Izin</th>
                                <th>Tingkat Kehadiran</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($userStats as $stat)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" 
                                                 style="width: 40px; height: 40px;">
                                                {{ substr($stat['user']->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <div class="fw-semibold">{{ $stat['user']->name }}</div>
                                                <small class="text-muted">{{ $stat['user']->email }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark">
                                            {{ $stat['user']->role->role_name ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td><span class="fw-semibold">{{ $stat['total_days'] }}</span></td>
                                    <td><span class="badge bg-success">{{ $stat['present'] }}</span></td>
                                    <td><span class="badge bg-warning">{{ $stat['late'] }}</span></td>
                                    <td><span class="badge bg-info">{{ $stat['leave'] }}</span></td>
                                    <td>
                                        <div class="attendance-rate">
                                            @php
                                                $rate = $stat['attendance_rate'];
                                                $class = $rate >= 95 ? 'rate-excellent' : 
                                                        ($rate >= 85 ? 'rate-good' : 
                                                        ($rate >= 70 ? 'rate-average' : 'rate-poor'));
                                            @endphp
                                            <div class="rate-circle {{ $class }}">
                                                {{ $rate }}%
                                            </div>
                                            <span class="fw-semibold">{{ $rate }}%</span>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Detailed Attendance Records -->
    @if($attendance->count() > 0)
    <div class="row">
        <div class="col-12">
            <div class="data-table">
                <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-list me-2"></i>Detail Rekaman Kehadiran
                    </h5>
                    <span class="badge bg-light text-dark">{{ $attendance->count() }} record</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Pegawai</th>
                                <th>Waktu</th>
                                <th>Status</th>
                                <th>Tipe</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($attendance as $record)
                                <tr>
                                    <td>
                                        <div class="fw-semibold">
                                            {{ \Carbon\Carbon::parse($record->timestamp)->format('d M Y') }}
                                        </div>
                                        <small class="text-muted">
                                            {{ \Carbon\Carbon::parse($record->timestamp)->format('l') }}
                                        </small>
                                    </td>
                                    <td>
                                        <div>
                                            <div class="fw-semibold">{{ $record->user->name }}</div>
                                            <small class="text-muted">{{ $record->user->role->role_name ?? 'N/A' }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="fw-semibold">
                                            {{ \Carbon\Carbon::parse($record->timestamp)->format('H:i') }}
                                        </span>
                                    </td>
                                    <td>
                                        @php
                                            $statusClass = '';
                                            switch($record->status) {
                                                case 'hadir':
                                                    $statusClass = 'status-hadir';
                                                    break;
                                                case 'terlambat':
                                                    $statusClass = 'status-terlambat';
                                                    break;
                                                case 'izin':
                                                    $statusClass = 'status-izin';
                                                    break;
                                                case 'sakit':
                                                    $statusClass = 'status-sakit';
                                                    break;
                                                default:
                                                    $statusClass = 'status-izin';
                                            }
                                        @endphp
                                        <span class="status-badge {{ $statusClass }}">
                                            {{ ucfirst($record->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark">
                                            {{ ucfirst($record->type) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="text-muted">
                                            {{ $record->notes ?: '-' }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @else
    <div class="row">
        <div class="col-12">
            <div class="data-table">
                <div class="card-body text-center py-5">
                    <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Tidak Ada Data Kehadiran</h5>
                    <p class="text-muted mb-0">Belum ada data kehadiran untuk periode yang dipilih.</p>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
// Export functionality placeholder
function exportReport() {
    const year = document.getElementById('year').value;
    const month = document.getElementById('month').value;
    const userId = document.getElementById('user_id').value;
    
    const params = new URLSearchParams({
        year: year,
        month: month,
        ...(userId && { user_id: userId })
    });
    
    // For now, just show a message
    alert('Fitur export sedang dalam pengembangan');
    
    // Future implementation:
    // window.location.href = `/admin/reports/export/attendance?${params}`;
}

// Auto-submit form when filters change
document.addEventListener('DOMContentLoaded', function() {
    const filterInputs = document.querySelectorAll('#year, #month, #user_id');
    filterInputs.forEach(input => {
        input.addEventListener('change', function() {
            // Auto-submit can be enabled here if desired
            // this.form.submit();
        });
    });
});
</script>
@endpush