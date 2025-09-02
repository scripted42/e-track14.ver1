@extends('admin.layouts.app')

@section('title', 'Laporan Kehadiran Pegawai')

@push('styles')
<style>
.detail-card {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    border: 1px solid #e9ecef;
    margin-bottom: 1.5rem;
}

.detail-header {
    background: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
    padding: 1.5rem;
    border-radius: 8px 8px 0 0;
}

.detail-body {
    padding: 1.5rem;
}

.stats-card {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    border: 1px solid #e9ecef;
    padding: 1.5rem;
    text-align: center;
    transition: all 0.2s ease;
}

.stats-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.stats-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    margin: 0 auto 1rem;
    color: white;
}

.stats-value {
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
}

.stats-label {
    color: #6c757d;
    font-size: 0.875rem;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.bg-primary {
    background: #007bff !important;
}

.bg-success {
    background: #28a745 !important;
}

.bg-warning {
    background: #ffc107 !important;
}

.bg-danger {
    background: #dc3545 !important;
}

.bg-info {
    background: #17a2b8 !important;
}

.bg-secondary {
    background: #6c757d !important;
}

.text-primary {
    color: #007bff !important;
}

.text-success {
    color: #28a745 !important;
}

.text-warning {
    color: #ffc107 !important;
}

.text-danger {
    color: #dc3545 !important;
}

.text-info {
    color: #17a2b8 !important;
}

.text-secondary {
    color: #6c757d !important;
}

.btn-modern {
    border-radius: 6px;
    padding: 0.5rem 1.2rem;
    font-weight: 500;
    border: 1px solid #dee2e6;
    transition: all 0.2s ease;
    background: white;
    color: #6c757d;
}

.btn-modern:hover {
    background: #f8f9fa;
    border-color: #adb5bd;
    color: #495057;
}

.btn-primary {
    background: #007bff;
    color: white;
    border-color: #007bff;
}

.btn-primary:hover {
    background: #0056b3;
    border-color: #0056b3;
    color: white;
}

.btn-secondary {
    background: #6c757d;
    color: white;
    border-color: #6c757d;
}

.btn-secondary:hover {
    background: #545b62;
    border-color: #545b62;
    color: white;
}

.btn-warning {
    background: #ffc107;
    color: #212529;
    border-color: #ffc107;
}

.btn-warning:hover {
    background: #e0a800;
    border-color: #e0a800;
    color: #212529;
}

.btn-danger {
    background: #dc3545;
    color: white;
    border-color: #dc3545;
}

.btn-danger:hover {
    background: #c82333;
    border-color: #c82333;
    color: white;
}

.filter-section {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    border: 1px solid #e9ecef;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
}

.form-control, .form-select {
    border-radius: 6px;
    border: 1px solid #dee2e6;
    padding: 0.5rem 0.75rem;
    transition: all 0.2s ease;
}

.form-control:focus, .form-select:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.table {
    margin-bottom: 0;
}

.table thead th {
    background: #f8f9fa;
    border: none;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.75rem;
    letter-spacing: 0.5px;
    color: #495057;
    padding: 1rem;
}

.table tbody td {
    padding: 1rem;
    vertical-align: middle;
    border-color: #f1f3f4;
}

.table tbody tr {
    transition: all 0.2s ease;
}

.table tbody tr:hover {
    background-color: #f8f9fa;
}

.badge {
    font-size: 0.75rem;
    padding: 0.375rem 0.75rem;
    border-radius: 4px;
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

.rate-excellent { background: #28a745; }
.rate-good { background: #007bff; }
.rate-average { background: #ffc107; }
.rate-poor { background: #dc3545; }

.empty-state {
    text-align: center;
    padding: 2rem 1rem;
    color: #6c757d;
}

.empty-state i {
    font-size: 2rem;
    margin-bottom: 1rem;
    opacity: 0.5;
}

.empty-state h6 {
    margin-bottom: 0.5rem;
    color: #495057;
}

.empty-state p {
    margin-bottom: 0;
    font-size: 0.875rem;
}

@media (max-width: 768px) {
    .stats-card {
        margin-bottom: 1rem;
    }
    
    .table-responsive {
        border-radius: 6px;
    }
}
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Laporan Kehadiran Pegawai</h1>
            <p class="text-muted mb-0">Analisis dan monitoring kehadiran pegawai</p>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-primary" onclick="exportReport()">
                <i class="fas fa-file-excel me-2"></i>Export Excel
            </button>
            <a href="{{ route('admin.reports.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Kembali
            </a>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="filter-section">
        <h5 class="mb-3">
            <i class="fas fa-filter me-2"></i>Filter Laporan
        </h5>
        <form method="GET" action="{{ route('admin.reports.attendance') }}">
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label for="year" class="form-label">Tahun</label>
                    <select class="form-select" id="year" name="year">
                        @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="month" class="form-label">Bulan</label>
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
                    <label for="user_id" class="form-label">Pegawai</label>
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
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search me-2"></i>Filter
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Statistics Summary -->
    @if($userStats->count() > 0)
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="stats-card">
                <div class="stats-icon bg-primary">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stats-value text-primary">{{ $userStats->count() }}</div>
                <div class="stats-label">Total Pegawai</div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stats-card">
                <div class="stats-icon bg-success">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stats-value text-success">{{ $userStats->sum('present') }}</div>
                <div class="stats-label">Total Hadir</div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stats-card">
                <div class="stats-icon bg-warning">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stats-value text-warning">{{ $userStats->sum('late') }}</div>
                <div class="stats-label">Total Terlambat</div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stats-card">
                <div class="stats-icon bg-info">
                    <i class="fas fa-calendar-times"></i>
                </div>
                <div class="stats-value text-info">{{ $userStats->sum('leave') }}</div>
                <div class="stats-label">Total Izin</div>
            </div>
        </div>
    </div>
    @endif

    <!-- User Statistics Table -->
    @if($userStats->count() > 0)
    <div class="detail-card">
        <div class="detail-header">
            <h5 class="mb-0">
                <i class="fas fa-chart-bar me-2"></i>Ringkasan Kehadiran Pegawai
            </h5>
        </div>
        <div class="detail-body p-0">
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
    @endif

    <!-- Detailed Attendance Records -->
    @if($attendance->count() > 0)
    <div class="detail-card">
        <div class="detail-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="fas fa-list me-2"></i>Detail Rekaman Kehadiran
            </h5>
            <span class="badge bg-primary">{{ $attendance->count() }} record</span>
        </div>
        <div class="detail-body p-0">
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
                                        $statusClass = match($record->status) {
                                            'hadir' => 'bg-success',
                                            'terlambat' => 'bg-warning',
                                            'izin' => 'bg-info',
                                            'sakit' => 'bg-danger',
                                            default => 'bg-secondary'
                                        };
                                    @endphp
                                    <span class="badge {{ $statusClass }}">
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
    @else
    <div class="detail-card">
        <div class="detail-body">
            <div class="empty-state">
                <i class="fas fa-calendar-times"></i>
                <h6>Tidak Ada Data Kehadiran</h6>
                <p>Belum ada data kehadiran untuk periode yang dipilih.</p>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
// Export functionality
function exportReport() {
    const year = document.getElementById('year').value;
    const month = document.getElementById('month').value;
    const userId = document.getElementById('user_id').value;
    
    const params = new URLSearchParams({
        year: year,
        month: month,
        ...(userId && { user_id: userId })
    });
    
    // Show loading message
    const exportBtn = event.target;
    const originalText = exportBtn.innerHTML;
    exportBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Mengekspor...';
    exportBtn.disabled = true;
    
    // For now, just show a message
    setTimeout(() => {
        alert('Fitur export sedang dalam pengembangan\n\nData yang akan diekspor:\n- Tahun: ' + year + '\n- Bulan: ' + month + '\n- Pegawai: ' + (userId ? 'Dipilih' : 'Semua'));
        
        exportBtn.innerHTML = originalText;
        exportBtn.disabled = false;
    }, 2000);
    
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