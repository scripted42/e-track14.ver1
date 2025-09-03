@extends('admin.layouts.app')

@section('title', 'Manajemen Izin & Cuti')

@push('styles')
<style>
.filter-card {
    background: #ffffff;
    border-radius: 15px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    border: 1px solid #e9ecef;
    overflow: hidden;
}

.data-table {
    background: white;
    border-radius: 15px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    border: 1px solid #e9ecef;
    overflow: hidden;
}

.table th {
    background: #f8f9fa;
    color: #2c3e50;
    border: none;
    font-weight: 600;
    padding: 1rem;
    text-transform: uppercase;
    font-size: 0.8rem;
    letter-spacing: 0.5px;
    border-bottom: 1px solid #e9ecef;
}

.table td {
    padding: 1rem;
    vertical-align: middle;
    border-color: #f8f9fa;
}

.table tbody tr:hover {
    background-color: #f8f9fa;
    transition: all 0.2s ease;
}

.status-badge {
    padding: 0.375rem 0.75rem;
    border-radius: 6px;
    font-size: 0.75rem;
    font-weight: 500;
    border: 1px solid #dee2e6;
}

.status-menunggu {
    background: #fff3cd;
    color: #856404;
    border-color: #ffeaa7;
}

.status-disetujui {
    background: #d4edda;
    color: #155724;
    border-color: #c3e6cb;
}

.status-ditolak {
    background: #f8d7da;
    color: #721c24;
    border-color: #f5c6cb;
}

.leave-type-badge {
    padding: 0.25rem 0.5rem;
    border-radius: 6px;
    font-size: 0.7rem;
    font-weight: 500;
    border: 1px solid #dee2e6;
}

.type-izin {
    background: #d1ecf1;
    color: #0c5460;
    border-color: #bee5eb;
}

.type-sakit {
    background: #f8d7da;
    color: #721c24;
    border-color: #f5c6cb;
}

.type-cuti {
    background: #d4edda;
    color: #155724;
    border-color: #c3e6cb;
}

.type-dinas_luar {
    background: #e2e3e5;
    color: #383d41;
    border-color: #d6d8db;
}

.stats-card {
    background: white;
    border: 1px solid #e9ecef;
    border-radius: 15px;
    padding: 1.5rem;
    text-align: center;
    transition: all 0.2s ease;
    height: 100%;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
}

.stats-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 25px rgba(0,0,0,0.1);
}

.stats-icon {
    width: 50px;
    height: 50px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1rem;
    font-size: 1.25rem;
    color: white;
}

.stats-value {
    font-size: 2rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.stats-label {
    color: #6c757d;
    font-size: 0.875rem;
    font-weight: 500;
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

.btn-success {
    background: #28a745;
    color: white;
    border-color: #28a745;
}

.btn-success:hover {
    background: #1e7e34;
    border-color: #1e7e34;
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

.form-control, .form-select {
    border-radius: 6px;
    border: 1px solid #dee2e6;
    padding: 0.5rem 1rem;
    transition: all 0.2s ease;
}

.form-control:focus, .form-select:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
}

.duration-badge {
    background: #f8f9fa;
    color: #6c757d;
    padding: 0.25rem 0.5rem;
    border-radius: 6px;
    font-size: 0.7rem;
    font-weight: 500;
    border: 1px solid #dee2e6;
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
    color: #6c757d;
}

.search-container input {
    padding-left: 2.5rem;
}
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Manajemen Izin & Cuti</h1>
            <p class="text-muted mb-0">Kelola pengajuan izin, cuti, dan perjalanan dinas pegawai</p>
        </div>
        <div>
            <button type="button" class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#createLeaveModal">
                <i class="fas fa-plus me-2"></i>Ajukan Izin
            </button>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Kembali ke Dashboard
            </a>
        </div>
    </div>

    <!-- Statistics Summary -->
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="stats-card">
                <div class="stats-icon" style="background: #ffc107;">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stats-value" style="color: #ffc107;">{{ $stats['pending'] }}</div>
                <div class="stats-label">Menunggu Persetujuan</div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="stats-card">
                <div class="stats-icon" style="background: #28a745;">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stats-value" style="color: #28a745;">{{ $stats['approved_today'] }}</div>
                <div class="stats-label">Disetujui Hari Ini</div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="stats-card">
                <div class="stats-icon" style="background: #17a2b8;">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <div class="stats-value" style="color: #17a2b8;">{{ $stats['this_month'] }}</div>
                <div class="stats-label">Total Bulan Ini</div>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="fas fa-search me-2"></i>Filter & Pencarian
            </h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.leaves.index') }}" class="row g-3">
                <div class="col-md-4">
                    <label for="search" class="form-label">Pencarian</label>
                    <input type="text" 
                           class="form-control" 
                           id="search" 
                           name="search" 
                           value="{{ request('search') }}"
                           placeholder="Cari nama, email, alasan...">
                </div>
                
                <div class="col-md-2">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">Semua Status</option>
                        @foreach($statuses as $status)
                            <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                                {{ ucfirst($status) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-2">
                    <label for="leave_type" class="form-label">Tipe Izin</label>
                    <select class="form-select" id="leave_type" name="leave_type">
                        <option value="">Semua Tipe</option>
                        @foreach($leaveTypes as $type)
                            <option value="{{ $type }}" {{ request('leave_type') == $type ? 'selected' : '' }}>
                                {{ ucfirst(str_replace('_', ' ', $type)) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-2">
                    <label for="user_id" class="form-label">Pegawai</label>
                    <select class="form-select" id="user_id" name="user_id">
                        <option value="">Semua Pegawai</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-2">
                    <label for="date_from" class="form-label">Tanggal Mulai</label>
                    <input type="date" 
                           class="form-control" 
                           id="date_from" 
                           name="date_from" 
                           value="{{ request('date_from') }}">
                </div>
                
                <div class="col-md-2">
                    <label for="date_to" class="form-label">Tanggal Selesai</label>
                    <input type="date" 
                           class="form-control" 
                           id="date_to" 
                           name="date_to" 
                           value="{{ request('date_to') }}">
                </div>
                
                <div class="col-12">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search me-2"></i>Cari
                        </button>
                        <a href="{{ route('admin.leaves.index') }}" class="btn btn-secondary">
                            <i class="fas fa-refresh me-2"></i>Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>



    <!-- Leaves Table -->
    @if($leaves->count() > 0)
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-list me-2"></i>Daftar Pengajuan Izin & Cuti
                    </h5>
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
                                <th>Evidence</th>
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
                                        @if($leave->evidence_path)
                                            <a href="{{ route('admin.leaves.evidence', $leave) }}" 
                                               class="btn btn-sm btn-outline-primary" 
                                               target="_blank" 
                                               title="Lihat Evidence">
                                                <i class="fas fa-file-alt me-1"></i>
                                                {{ Str::limit($leave->evidence_original_name, 15) }}
                                            </a>
                                        @else
                                            <span class="text-muted">
                                                <i class="fas fa-minus me-1"></i>Tidak ada
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="{{ route('admin.leaves.show', $leave) }}" 
                                               class="btn btn-sm btn-info btn-modern" 
                                               title="Lihat Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if($leave->isPending() && (auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Kepala Sekolah') || auth()->user()->hasRole('Waka Kurikulum')))
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
                <div class="card-footer">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted">
                                Menampilkan {{ $leaves->firstItem() ?? 0 }} - {{ $leaves->lastItem() ?? 0 }} 
                                dari {{ $leaves->total() }} pengajuan
                            </small>
                        </div>
                        <div class="d-flex justify-content-center">
                            {{ $leaves->appends(request()->query())->links('pagination::bootstrap-4') }}
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
            <div class="card">
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



<!-- Create Leave Modal -->
<div class="modal fade" id="createLeaveModal" tabindex="-1" aria-labelledby="createLeaveModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createLeaveModalLabel">
                    <i class="fas fa-plus me-2"></i>Ajukan Izin / Cuti
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.leaves.store') }}" method="POST" id="createLeaveForm" enctype="multipart/form-data">
                @csrf
            <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="leave_type" class="form-label">Jenis Izin <span class="text-danger">*</span></label>
                            <select class="form-select" id="leave_type" name="leave_type" required>
                                <option value="">Pilih Jenis Izin</option>
                                <option value="izin">Izin</option>
                                <option value="sakit">Sakit</option>
                                <option value="cuti">Cuti</option>
                                <option value="dinas_luar">Dinas Luar</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="start_date" class="form-label">Tanggal Mulai <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="start_date" name="start_date" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="end_date" class="form-label">Tanggal Selesai <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="end_date" name="end_date" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="duration" class="form-label">Durasi (Hari)</label>
                            <input type="number" class="form-control" id="duration" name="duration" readonly>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="reason" class="form-label">Alasan <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="reason" name="reason" rows="4" 
                                  placeholder="Jelaskan alasan pengajuan izin..." required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="evidence" class="form-label">Upload Evidence</label>
                        <input type="file" class="form-control" id="evidence" name="evidence" 
                               accept=".jpg,.jpeg,.png,.pdf,.doc,.docx">
                        <div class="form-text">
                            <i class="fas fa-info-circle me-1"></i>
                            Upload bukti pendukung (Surat dokter, undangan, dll). 
                            Format: JPG, PNG, PDF, DOC, DOCX. Maksimal 5MB.
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="notes" class="form-label">Catatan Tambahan</label>
                        <textarea class="form-control" id="notes" name="notes" rows="2" 
                                  placeholder="Catatan tambahan (opsional)"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Batal
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane me-2"></i>Ajukan Izin
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-submit form when filters change (exclude modal inputs)
    const filterInputs = document.querySelectorAll('#status, #user_id, #date_from, #date_to');
    filterInputs.forEach(input => {
        input.addEventListener('change', function() {
            // Only submit if not inside a modal
            if (!this.closest('.modal')) {
                this.closest('form').submit();
            }
        });
    });
    
    // Handle leave_type filter separately (not in modal)
    const leaveTypeFilter = document.querySelector('#leave_type');
    if (leaveTypeFilter && !leaveTypeFilter.closest('.modal')) {
        leaveTypeFilter.addEventListener('change', function() {
            this.closest('form').submit();
        });
    }
    
    // Prevent auto-submit on modal form inputs
    const modalForm = document.getElementById('createLeaveForm');
    if (modalForm) {
        const modalInputs = modalForm.querySelectorAll('select, input');
        modalInputs.forEach(input => {
            input.addEventListener('change', function(e) {
                e.stopPropagation(); // Prevent event bubbling
        });
    });
    }
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

// Calculate duration between start and end date
function calculateDuration() {
    const startDate = document.getElementById('start_date').value;
    const endDate = document.getElementById('end_date').value;
    
    if (startDate && endDate) {
        const start = new Date(startDate);
        const end = new Date(endDate);
        const diffTime = Math.abs(end - start);
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1; // +1 to include both start and end dates
        
        document.getElementById('duration').value = diffDays;
    }
}

// Add event listeners for date inputs in modal
document.addEventListener('DOMContentLoaded', function() {
    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');
    
    if (startDateInput) {
        startDateInput.addEventListener('change', calculateDuration);
    }
    if (endDateInput) {
        endDateInput.addEventListener('change', calculateDuration);
    }
});
</script>
@endpush