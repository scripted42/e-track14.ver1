@extends('admin.layouts.app')

@section('title', 'Manajemen Izin & Cuti')

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

.action-buttons {
    display: flex;
    gap: 0.5rem;
    justify-content: center;
}

.action-buttons .btn {
    padding: 0.375rem 0.75rem;
    font-size: 0.75rem;
}

.search-container {
    position: relative;
}

.search-container i {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: #6b7280;
}

.search-container input {
    padding-left: 2.5rem;
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
                    <h1 class="h3 mb-1 text-gray-800">📋 Manajemen Izin & Cuti</h1>
                    <p class="text-muted mb-0">Kelola pengajuan izin, cuti, dan perjalanan dinas pegawai</p>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-success btn-modern" data-bs-toggle="modal" data-bs-target="#exportModal">
                        <i class="fas fa-download me-2"></i>Export Data
                    </button>
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary btn-modern">
                        <i class="fas fa-arrow-left me-2"></i>Kembali ke Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Summary -->
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="stats-card">
                <div class="stats-icon bg-warning bg-gradient text-white">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stats-value text-warning">{{ $stats['pending'] }}</div>
                <div class="stats-label">Menunggu Persetujuan</div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="stats-card">
                <div class="stats-icon bg-success bg-gradient text-white">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stats-value text-success">{{ $stats['approved_today'] }}</div>
                <div class="stats-label">Disetujui Hari Ini</div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="stats-card">
                <div class="stats-icon bg-info bg-gradient text-white">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <div class="stats-value text-info">{{ $stats['this_month'] }}</div>
                <div class="stats-label">Total Bulan Ini</div>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="filter-card card">
                <div class="card-body">
                    <h5 class="card-title mb-3">
                        <i class="fas fa-filter text-warning me-2"></i>Filter & Pencarian
                    </h5>
                    <form method="GET" action="{{ route('admin.leaves.index') }}" id="filterForm">
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label for="search" class="form-label fw-semibold">Pencarian</label>
                                <div class="search-container">
                                    <i class="fas fa-search"></i>
                                    <input type="text" 
                                           class="form-control" 
                                           id="search" 
                                           name="search" 
                                           value="{{ request('search') }}"
                                           placeholder="Cari nama, email, alasan...">
                                </div>
                            </div>
                            <div class="col-md-2 mb-3">
                                <label for="status" class="form-label fw-semibold">Status</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="">Semua Status</option>
                                    @foreach($statuses as $status)
                                        <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                                            {{ ucfirst($status) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2 mb-3">
                                <label for="leave_type" class="form-label fw-semibold">Tipe Izin</label>
                                <select class="form-select" id="leave_type" name="leave_type">
                                    <option value="">Semua Tipe</option>
                                    @foreach($leaveTypes as $type)
                                        <option value="{{ $type }}" {{ request('leave_type') == $type ? 'selected' : '' }}>
                                            {{ ucfirst(str_replace('_', ' ', $type)) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2 mb-3">
                                <label for="user_id" class="form-label fw-semibold">Pegawai</label>
                                <select class="form-select" id="user_id" name="user_id">
                                    <option value="">Semua Pegawai</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="date_range" class="form-label fw-semibold">Periode Tanggal</label>
                                <div class="row">
                                    <div class="col-6">
                                        <input type="date" 
                                               class="form-control" 
                                               id="date_from" 
                                               name="date_from" 
                                               value="{{ request('date_from') }}"
                                               placeholder="Dari">
                                    </div>
                                    <div class="col-6">
                                        <input type="date" 
                                               class="form-control" 
                                               id="date_to" 
                                               name="date_to" 
                                               value="{{ request('date_to') }}"
                                               placeholder="Sampai">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-warning btn-modern">
                                        <i class="fas fa-search me-2"></i>Filter
                                    </button>
                                    <a href="{{ route('admin.leaves.index') }}" class="btn btn-outline-secondary btn-modern">
                                        <i class="fas fa-refresh me-2"></i>Reset
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Leaves Table -->
    @if($leaves->count() > 0)
    <div class="row">
        <div class="col-12">
            <div class="data-table">
                <div class="card-header bg-warning text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-list me-2"></i>Daftar Pengajuan Izin & Cuti
                    </h5>
                    <span class="badge bg-light text-dark">{{ $leaves->total() }} pengajuan</span>
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
                                <th>Alasan</th>
                                <th>Aksi</th>
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
                                            {{ $leave->getLeaveTypeLabel() }}
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
                                            {{ $leave->getStatusLabel() }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="text-muted" title="{{ $leave->reason }}">
                                            {{ Str::limit($leave->reason, 50) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="{{ route('admin.leaves.show', $leave) }}" 
                                               class="btn btn-sm btn-info btn-modern" 
                                               title="Lihat Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if($leave->isPending())
                                                <button type="button" 
                                                        class="btn btn-sm btn-success btn-modern"
                                                        onclick="approveLeave({{ $leave->id }})"
                                                        title="Setujui">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                                <button type="button" 
                                                        class="btn btn-sm btn-danger btn-modern"
                                                        onclick="rejectLeave({{ $leave->id }})"
                                                        title="Tolak">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                @if($leaves->hasPages())
                <div class="card-footer bg-light">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted">
                                Menampilkan {{ $leaves->firstItem() ?? 0 }} - {{ $leaves->lastItem() ?? 0 }} 
                                dari {{ $leaves->total() }} pengajuan
                            </small>
                        </div>
                        <div>
                            {{ $leaves->appends(request()->query())->links() }}
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
    @else
    <div class="row">
        <div class="col-12">
            <div class="data-table">
                <div class="card-body text-center py-5">
                    <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Tidak Ada Pengajuan Izin</h5>
                    <p class="text-muted mb-0">Belum ada pengajuan izin atau cuti yang ditemukan.</p>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Approve Modal -->
<div class="modal fade" id="approveModal" tabindex="-1" aria-labelledby="approveModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="approveModalLabel">
                    <i class="fas fa-check-circle me-2"></i>Setujui Pengajuan Izin
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="approveForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="text-center mb-3">
                        <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                        <h6>Apakah Anda yakin ingin menyetujui pengajuan izin ini?</h6>
                        <p class="text-muted">Pengajuan yang telah disetujui tidak dapat dibatalkan.</p>
                    </div>
                    <div class="mb-3">
                        <label for="approve_comment" class="form-label">Komentar (Opsional)</label>
                        <textarea class="form-control" id="approve_comment" name="comment" rows="3" 
                                  placeholder="Tambahkan komentar untuk persetujuan..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Batal
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check me-2"></i>Ya, Setujui
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="rejectModalLabel">
                    <i class="fas fa-times-circle me-2"></i>Tolak Pengajuan Izin
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="rejectForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="text-center mb-3">
                        <i class="fas fa-times-circle fa-3x text-danger mb-3"></i>
                        <h6>Apakah Anda yakin ingin menolak pengajuan izin ini?</h6>
                        <p class="text-muted">Pengajuan yang telah ditolak tidak dapat dibatalkan.</p>
                    </div>
                    <div class="mb-3">
                        <label for="reject_comment" class="form-label">Alasan Penolakan <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="reject_comment" name="comment" rows="3" 
                                  placeholder="Masukkan alasan penolakan..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Batal
                    </button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-times me-2"></i>Ya, Tolak
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Export Modal -->
<div class="modal fade" id="exportModal" tabindex="-1" aria-labelledby="exportModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="exportModalLabel">
                    <i class="fas fa-download me-2"></i>Export Data Izin
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3">
                    <i class="fas fa-file-excel fa-3x text-success mb-3"></i>
                    <h6>Pilih Format Export</h6>
                    <p class="text-muted">Data akan diexport sesuai filter yang sedang aktif.</p>
                </div>
                <div class="d-grid gap-2">
                    <button type="button" class="btn btn-success btn-modern" onclick="exportData('excel')">
                        <i class="fas fa-file-excel me-2"></i>Export ke Excel
                    </button>
                    <button type="button" class="btn btn-danger btn-modern" onclick="exportData('pdf')">
                        <i class="fas fa-file-pdf me-2"></i>Export ke PDF
                    </button>
                    <button type="button" class="btn btn-info btn-modern" onclick="exportData('csv')">
                        <i class="fas fa-file-csv me-2"></i>Export ke CSV
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Real-time search
    const searchInput = document.getElementById('search');
    if (searchInput) {
        let searchTimeout;
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(function() {
                document.getElementById('filterForm').submit();
            }, 500);
        });
    }

    // Auto-submit form when filters change
    const filterInputs = document.querySelectorAll('#status, #leave_type, #user_id, #date_from, #date_to');
    filterInputs.forEach(input => {
        input.addEventListener('change', function() {
            document.getElementById('filterForm').submit();
        });
    });
});

function approveLeave(leaveId) {
    const modal = new bootstrap.Modal(document.getElementById('approveModal'));
    const form = document.getElementById('approveForm');
    form.action = `/admin/leaves/${leaveId}/approve`;
    modal.show();
}

function rejectLeave(leaveId) {
    const modal = new bootstrap.Modal(document.getElementById('rejectModal'));
    const form = document.getElementById('rejectForm');
    form.action = `/admin/leaves/${leaveId}/reject`;
    modal.show();
}

function exportData(format) {
    // Get current filter parameters
    const params = new URLSearchParams(window.location.search);
    params.set('format', format);
    
    // For now, just show a message
    alert(`Export ${format.toUpperCase()} sedang dalam pengembangan`);
    
    // Future implementation:
    // window.location.href = `/admin/leaves/export?${params}`;
    
    const modal = bootstrap.Modal.getInstance(document.getElementById('exportModal'));
    modal.hide();
}
</script>
@endpush