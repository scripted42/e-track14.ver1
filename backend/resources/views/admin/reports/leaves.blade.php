@extends('admin.layouts.app')

@section('title', 'Laporan Izin & Cuti')

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
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
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

.status-menunggu {
    background-color: #fef3c7;
    color: #92400e;
}

.status-disetujui {
    background-color: #dcfce7;
    color: #166534;
}

.status-ditolak {
    background-color: #fee2e2;
    color: #991b1b;
}

.leave-type-badge {
    padding: 0.25rem 0.5rem;
    border-radius: 0.375rem;
    font-size: 0.7rem;
    font-weight: 500;
}

.type-izin {
    background-color: #dbeafe;
    color: #1e40af;
}

.type-sakit {
    background-color: #fee2e2;
    color: #991b1b;
}

.type-cuti {
    background-color: #f0fdf4;
    color: #166534;
}

.type-dinas_luar {
    background-color: #faf5ff;
    color: #7c3aed;
}

.stats-card {
    background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
    border: 1px solid #e2e8f0;
    border-radius: 1rem;
    padding: 1.5rem;
    text-align: center;
    transition: all 0.3s ease;
    height: 100%;
}

.stats-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px -3px rgba(0, 0, 0, 0.1);
}

.stats-icon {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1rem;
    font-size: 1.25rem;
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
    border-color: #f59e0b;
    box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.1);
}

.duration-badge {
    background: linear-gradient(135deg, #e2e8f0 0%, #cbd5e1 100%);
    color: #475569;
    padding: 0.25rem 0.5rem;
    border-radius: 0.375rem;
    font-size: 0.7rem;
    font-weight: 600;
}

.leave-card {
    border: 1px solid #e2e8f0;
    border-radius: 0.75rem;
    padding: 1rem;
    margin-bottom: 1rem;
    transition: all 0.3s ease;
}

.leave-card:hover {
    border-color: #f59e0b;
    box-shadow: 0 4px 12px rgba(245, 158, 11, 0.1);
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
                    <h1 class="h3 mb-1 text-gray-800">📅 Laporan Izin & Cuti</h1>
                    <p class="text-muted mb-0">Pantau dan kelola pengajuan izin, cuti, dan perjalanan dinas</p>
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
                        <i class="fas fa-filter text-warning me-2"></i>Filter Laporan
                    </h5>
                    <form method="GET" action="{{ route('admin.reports.leaves') }}">
                        <div class="row">
                            <div class="col-md-2 mb-3">
                                <label for="year" class="form-label fw-semibold">Tahun</label>
                                <select class="form-select" id="year" name="year">
                                    @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-md-2 mb-3">
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
                            <div class="col-md-3 mb-3">
                                <label for="status" class="form-label fw-semibold">Status</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="">Semua Status</option>
                                    <option value="menunggu" {{ $status == 'menunggu' ? 'selected' : '' }}>Menunggu</option>
                                    <option value="disetujui" {{ $status == 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                                    <option value="ditolak" {{ $status == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="leave_type" class="form-label fw-semibold">Tipe Izin</label>
                                <select class="form-select" id="leave_type" name="leave_type">
                                    <option value="">Semua Tipe</option>
                                    <option value="izin" {{ $leaveType == 'izin' ? 'selected' : '' }}>Izin</option>
                                    <option value="sakit" {{ $leaveType == 'sakit' ? 'selected' : '' }}>Sakit</option>
                                    <option value="cuti" {{ $leaveType == 'cuti' ? 'selected' : '' }}>Cuti</option>
                                    <option value="dinas_luar" {{ $leaveType == 'dinas_luar' ? 'selected' : '' }}>Dinas Luar</option>
                                </select>
                            </div>
                            <div class="col-md-2 mb-3 d-flex align-items-end">
                                <button type="submit" class="btn btn-warning btn-modern w-100">
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
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="stats-card">
                <div class="stats-icon bg-warning bg-gradient text-white">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <div class="stats-value text-warning">{{ $stats['total'] }}</div>
                <div class="stats-label">Total Pengajuan</div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stats-card">
                <div class="stats-icon bg-success bg-gradient text-white">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stats-value text-success">{{ $stats['approved'] }}</div>
                <div class="stats-label">Disetujui</div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stats-card">
                <div class="stats-icon bg-danger bg-gradient text-white">
                    <i class="fas fa-times-circle"></i>
                </div>
                <div class="stats-value text-danger">{{ $stats['rejected'] }}</div>
                <div class="stats-label">Ditolak</div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stats-card">
                <div class="stats-icon bg-info bg-gradient text-white">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stats-value text-info">{{ $stats['pending'] }}</div>
                <div class="stats-label">Menunggu</div>
            </div>
        </div>
    </div>

    <!-- Leave Type Statistics -->
    @if($leaveTypeStats->count() > 0)
    <div class="row mb-4">
        <div class="col-12">
            <div class="data-table">
                <div class="card-header bg-warning text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-pie me-2"></i>Statistik Berdasarkan Tipe Izin
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($leaveTypeStats as $type => $typeStats)
                            <div class="col-md-3 mb-3">
                                <div class="leave-card">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <span class="leave-type-badge type-{{ $type }}">
                                            {{ ucfirst(str_replace('_', ' ', $type)) }}
                                        </span>
                                        <span class="fw-bold">{{ $typeStats['count'] }}</span>
                                    </div>
                                    <div class="row text-center">
                                        <div class="col-6">
                                            <div class="text-success fw-semibold">{{ $typeStats['approved'] }}</div>
                                            <small class="text-muted">Disetujui</small>
                                        </div>
                                        <div class="col-6">
                                            <div class="text-primary fw-semibold">{{ $typeStats['total_days'] }}</div>
                                            <small class="text-muted">Total Hari</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Detailed Leave Records -->
    @if($leaves->count() > 0)
    <div class="row">
        <div class="col-12">
            <div class="data-table">
                <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-list me-2"></i>Detail Pengajuan Izin & Cuti
                    </h5>
                    <span class="badge bg-light text-dark">{{ $leaves->count() }} pengajuan</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Tanggal Pengajuan</th>
                                <th>Pegawai</th>
                                <th>Tipe</th>
                                <th>Periode</th>
                                <th>Durasi</th>
                                <th>Status</th>
                                <th>Disetujui Oleh</th>
                                <th>Alasan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($leaves as $leave)
                                <tr>
                                    <td>
                                        <div class="fw-semibold">
                                            {{ \Carbon\Carbon::parse($leave->created_at)->format('d M Y') }}
                                        </div>
                                        <small class="text-muted">
                                            {{ \Carbon\Carbon::parse($leave->created_at)->format('H:i') }}
                                        </small>
                                    </td>
                                    <td>
                                        <div>
                                            <div class="fw-semibold">{{ $leave->user->name }}</div>
                                            <small class="text-muted">{{ $leave->user->role->role_name ?? 'N/A' }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="leave-type-badge type-{{ $leave->leave_type }}">
                                            {{ ucfirst(str_replace('_', ' ', $leave->leave_type)) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="fw-semibold">
                                            {{ \Carbon\Carbon::parse($leave->start_date)->format('d M') }} - 
                                            {{ \Carbon\Carbon::parse($leave->end_date)->format('d M Y') }}
                                        </div>
                                        <small class="text-muted">
                                            {{ \Carbon\Carbon::parse($leave->start_date)->format('l') }} - 
                                            {{ \Carbon\Carbon::parse($leave->end_date)->format('l') }}
                                        </small>
                                    </td>
                                    <td>
                                        <span class="duration-badge">
                                            {{ $leave->getDurationDays() }} hari
                                        </span>
                                    </td>
                                    <td>
                                        <span class="status-badge status-{{ $leave->status }}">
                                            {{ ucfirst($leave->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($leave->approver)
                                            <div class="fw-semibold">{{ $leave->approver->name }}</div>
                                            @if($leave->approved_at)
                                                <small class="text-muted">
                                                    {{ \Carbon\Carbon::parse($leave->approved_at)->format('d M Y H:i') }}
                                                </small>
                                            @endif
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="text-muted" title="{{ $leave->reason }}">
                                            {{ Str::limit($leave->reason, 50) }}
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
                    <h5 class="text-muted">Tidak Ada Data Izin</h5>
                    <p class="text-muted mb-0">Belum ada pengajuan izin untuk periode yang dipilih.</p>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Total Leave Days Summary -->
    @if($stats['total_days'] > 0)
    <div class="row mt-4">
        <div class="col-12">
            <div class="alert alert-info border-0" style="background: linear-gradient(135deg, #e0f2fe 0%, #b3e5fc 100%);">
                <div class="d-flex align-items-center">
                    <i class="fas fa-info-circle text-info me-3" style="font-size: 1.5rem;"></i>
                    <div>
                        <h6 class="mb-1 text-info">📊 Ringkasan Total Hari Izin</h6>
                        <p class="mb-0 text-info">
                            Total <strong>{{ $stats['total_days'] }} hari</strong> izin telah disetujui pada periode ini 
                            dari <strong>{{ $stats['approved'] }} pengajuan</strong> yang disetujui.
                        </p>
                    </div>
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
    const status = document.getElementById('status').value;
    const leaveType = document.getElementById('leave_type').value;
    
    const params = new URLSearchParams({
        year: year,
        month: month,
        ...(status && { status: status }),
        ...(leaveType && { leave_type: leaveType })
    });
    
    // For now, just show a message
    alert('Fitur export sedang dalam pengembangan');
    
    // Future implementation:
    // window.location.href = `/admin/reports/export/leaves?${params}`;
}

// Auto-submit form when filters change (optional)
document.addEventListener('DOMContentLoaded', function() {
    const filterInputs = document.querySelectorAll('#year, #month, #status, #leave_type');
    filterInputs.forEach(input => {
        input.addEventListener('change', function() {
            // Auto-submit can be enabled here if desired
            // this.form.submit();
        });
    });
    
    // Initialize tooltips for truncated text
    const tooltips = document.querySelectorAll('[title]');
    tooltips.forEach(tooltip => {
        // Bootstrap tooltip initialization would go here
    });
});
</script>
@endpush