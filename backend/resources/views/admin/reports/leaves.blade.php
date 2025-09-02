@extends('admin.layouts.app')

@section('title', 'Laporan Izin & Cuti')

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

.leave-type-badge {
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
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

.duration-badge {
    background: #e9ecef;
    color: #495057;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-size: 0.7rem;
    font-weight: 600;
}

.leave-type-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-top: 1rem;
}

.leave-type-card {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 1rem;
    border: 1px solid #e9ecef;
    text-align: center;
}

.leave-type-card h6 {
    margin-bottom: 0.5rem;
    color: #495057;
}

.leave-type-card .stats {
    display: flex;
    justify-content: space-between;
    margin-top: 0.5rem;
}

.leave-type-card .stat-item {
    text-align: center;
}

.leave-type-card .stat-value {
    font-size: 1.2rem;
    font-weight: 700;
    margin-bottom: 0.25rem;
}

.leave-type-card .stat-label {
    font-size: 0.7rem;
    color: #6c757d;
}

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
    
    .leave-type-stats {
        grid-template-columns: 1fr;
    }
}
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Laporan Izin & Cuti</h1>
            <p class="text-muted mb-0">Monitoring dan analisis pengajuan izin, cuti, dan perjalanan dinas</p>
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
        <form method="GET" action="{{ route('admin.reports.leaves') }}">
            <div class="row">
                <div class="col-md-2 mb-3">
                    <label for="year" class="form-label">Tahun</label>
                    <select class="form-select" id="year" name="year">
                        @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-2 mb-3">
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
                <div class="col-md-3 mb-3">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">Semua Status</option>
                        <option value="menunggu" {{ $status == 'menunggu' ? 'selected' : '' }}>Menunggu</option>
                        <option value="disetujui" {{ $status == 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                        <option value="ditolak" {{ $status == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="leave_type" class="form-label">Tipe Izin</label>
                    <select class="form-select" id="leave_type" name="leave_type">
                        <option value="">Semua Tipe</option>
                        <option value="izin" {{ $leaveType == 'izin' ? 'selected' : '' }}>Izin</option>
                        <option value="sakit" {{ $leaveType == 'sakit' ? 'selected' : '' }}>Sakit</option>
                        <option value="cuti" {{ $leaveType == 'cuti' ? 'selected' : '' }}>Cuti</option>
                        <option value="dinas_luar" {{ $leaveType == 'dinas_luar' ? 'selected' : '' }}>Dinas Luar</option>
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
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="stats-card">
                <div class="stats-icon bg-warning">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <div class="stats-value text-warning">{{ $stats['total'] }}</div>
                <div class="stats-label">Total Pengajuan</div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stats-card">
                <div class="stats-icon bg-success">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stats-value text-success">{{ $stats['approved'] }}</div>
                <div class="stats-label">Disetujui</div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stats-card">
                <div class="stats-icon bg-danger">
                    <i class="fas fa-times-circle"></i>
                </div>
                <div class="stats-value text-danger">{{ $stats['rejected'] }}</div>
                <div class="stats-label">Ditolak</div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stats-card">
                <div class="stats-icon bg-info">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stats-value text-info">{{ $stats['pending'] }}</div>
                <div class="stats-label">Menunggu</div>
            </div>
        </div>
    </div>

    <!-- Leave Type Statistics -->
    @if($leaveTypeStats->count() > 0)
    <div class="detail-card">
        <div class="detail-header">
            <h5 class="mb-0">
                <i class="fas fa-chart-pie me-2"></i>Statistik Berdasarkan Tipe Izin
            </h5>
        </div>
        <div class="detail-body">
            <div class="leave-type-stats">
                @foreach($leaveTypeStats as $type => $typeStats)
                    <div class="leave-type-card">
                        <h6>{{ ucfirst(str_replace('_', ' ', $type)) }}</h6>
                        <div class="stats">
                            <div class="stat-item">
                                <div class="stat-value text-primary">{{ $typeStats['count'] }}</div>
                                <div class="stat-label">Total</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-value text-success">{{ $typeStats['approved'] }}</div>
                                <div class="stat-label">Disetujui</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-value text-info">{{ $typeStats['total_days'] }}</div>
                                <div class="stat-label">Total Hari</div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- Detailed Leave Records -->
    @if($leaves->count() > 0)
    <div class="detail-card">
        <div class="detail-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="fas fa-list me-2"></i>Detail Pengajuan Izin & Cuti
            </h5>
            <span class="badge bg-primary">{{ $leaves->count() }} pengajuan</span>
        </div>
        <div class="detail-body p-0">
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
                                    @php
                                        $statusClass = match($leave->status) {
                                            'disetujui' => 'bg-success',
                                            'ditolak' => 'bg-danger',
                                            'menunggu' => 'bg-warning',
                                            default => 'bg-secondary'
                                        };
                                    @endphp
                                    <span class="badge {{ $statusClass }}">
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
    @else
    <div class="detail-card">
        <div class="detail-body">
            <div class="empty-state">
                <i class="fas fa-calendar-times"></i>
                <h6>Tidak Ada Data Izin</h6>
                <p>Belum ada pengajuan izin untuk periode yang dipilih.</p>
            </div>
        </div>
    </div>
    @endif

    <!-- Total Leave Days Summary -->
    @if($stats['total_days'] > 0)
    <div class="detail-card">
        <div class="detail-body">
            <div class="alert alert-info border-0" style="background: #e3f2fd;">
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
// Export functionality
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
    
    // Show loading message
    const exportBtn = event.target;
    const originalText = exportBtn.innerHTML;
    exportBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Mengekspor...';
    exportBtn.disabled = true;
    
    // For now, just show a message
    setTimeout(() => {
        alert('Fitur export sedang dalam pengembangan\n\nData yang akan diekspor:\n- Tahun: ' + year + '\n- Bulan: ' + month + '\n- Status: ' + (status || 'Semua') + '\n- Tipe: ' + (leaveType || 'Semua'));
        
        exportBtn.innerHTML = originalText;
        exportBtn.disabled = false;
    }, 2000);
    
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
});
</script>
@endpush