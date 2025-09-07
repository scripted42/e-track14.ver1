@extends('admin.layouts.app')

@section('title', 'Manajemen Izin & Cuti')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Manajemen Izin & Cuti</h1>
            <p class="text-muted mb-0">Kelola pengajuan izin, cuti, dan perjalanan dinas pegawai</p>
        </div>
        <div>
            <button type="button" class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#createLeaveModal">
                <i class="fas fa-plus me-2"></i>Ajukan Izin
            </button>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $stats['pending'] }}</h4>
                            <p class="mb-0">Menunggu Persetujuan</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-clock fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $stats['approved_today'] }}</h4>
                            <p class="mb-0">Disetujui Hari Ini</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-check-circle fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $stats['this_month'] }}</h4>
                            <p class="mb-0">Total Bulan Ini</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-calendar-alt fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $stats['rejected_today'] ?? 0 }}</h4>
                            <p class="mb-0">Ditolak Hari Ini</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-times-circle fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filter Form -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="fas fa-search me-2"></i>Pencarian & Filter
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
                    <label for="status_filter" class="form-label">Status</label>
                    <select class="form-select" id="status_filter" name="status">
                        <option value="">Semua Status</option>
                        @foreach($statuses as $status)
                            <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                                {{ ucfirst($status) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-2">
                    <label for="leave_type_filter" class="form-label">Tipe Izin</label>
                    <select class="form-select" id="leave_type_filter" name="leave_type">
                        <option value="">Semua Tipe</option>
                        @foreach($leaveTypes as $type)
                            <option value="{{ $type }}" {{ request('leave_type') == $type ? 'selected' : '' }}>
                                {{ ucfirst(str_replace('_', ' ', $type)) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-2">
                    <label for="user_filter" class="form-label">Pegawai</label>
                    <select class="form-select" id="user_filter" name="user_id">
                        <option value="">Semua Pegawai</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-2">
                    <label for="sort_by" class="form-label">Urutkan</label>
                    <select class="form-select" id="sort_by" name="sort_by">
                        <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Tanggal Pengajuan</option>
                        <option value="start_date" {{ request('sort_by') == 'start_date' ? 'selected' : '' }}>Tanggal Mulai</option>
                        <option value="status" {{ request('sort_by') == 'status' ? 'selected' : '' }}>Status</option>
                        <option value="leave_type" {{ request('sort_by') == 'leave_type' ? 'selected' : '' }}>Tipe Izin</option>
                    </select>
                </div>
                
                <div class="col-12">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Cari
                        </button>
                        <a href="{{ route('admin.leaves.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times"></i> Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>



    <div class="card">
        <div class="card-body">
            <!-- Results Info -->
            @if(request()->hasAny(['search', 'status', 'leave_type', 'user_id']))
                <div class="alert alert-info mb-3">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Hasil Pencarian:</strong> 
                    Ditemukan {{ $leaves->total() }} pengajuan
                    @if(request('search'))
                        dengan kata kunci "{{ request('search') }}"
                    @endif
                    @if(request('status'))
                        dengan status {{ ucfirst(request('status')) }}
                    @endif
                    @if(request('leave_type'))
                        dengan tipe {{ ucfirst(str_replace('_', ' ', request('leave_type'))) }}
                    @endif
                </div>
            @endif
            
            @if(isset($leaves) && $leaves->count())
                <!-- Total Records Info -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="text-muted">
                        <i class="fas fa-info-circle me-1"></i>
                        Menampilkan {{ $leaves->firstItem() ?? 0 }} - {{ $leaves->lastItem() ?? 0 }} dari {{ $leaves->total() }} total pengajuan
                    </div>
                    <div class="text-muted">
                        Halaman {{ $leaves->currentPage() }} dari {{ $leaves->lastPage() }}
                    </div>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-striped align-middle mb-0">
                        <thead>
                            <tr>
                                <th width="50" class="text-center">No</th>
                                <th>Tanggal Pengajuan</th>
                                <th>Pegawai</th>
                                <th>Tipe</th>
                                <th>Periode</th>
                                <th>Durasi</th>
                                <th>Status</th>
                                <th>Evidence</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($leaves as $index => $leave)
                            <tr>
                                <td class="text-center">
                                    {{ $leaves->firstItem() + $index }}
                                </td>
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
                                    <span class="badge bg-primary">{{ $leave->getLeaveTypeLabel() }}</span>
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
                                    <span class="badge bg-info">{{ $leave->getDurationDays() }} hari</span>
                                </td>
                                <td>
                                    <span class="badge {{ $leave->status === 'disetujui' ? 'bg-success' : ($leave->status === 'ditolak' ? 'bg-danger' : 'bg-warning') }}">
                                        {{ $leave->getStatusLabel() }}
                                    </span>
                                </td>
                                <td>
                                    @if($leave->evidence_path)
                                        <a href="{{ route('admin.leaves.evidence', $leave) }}" 
                                           class="btn btn-sm btn-outline-primary" 
                                           target="_blank" 
                                           title="Lihat Evidence">
                                            <i class="fas fa-file-alt me-1"></i>
                                            Evidence
                                        </a>
                                    @else
                                        <span class="text-muted">
                                            <i class="fas fa-minus me-1"></i>Tidak ada
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.leaves.show', $leave) }}" class="btn btn-sm btn-outline-info" title="Lihat Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if($leave->isPending() && (auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Kepala Sekolah') || auth()->user()->hasRole('Waka Kurikulum')))
                                            <button type="button" 
                                                    class="btn btn-sm btn-outline-success"
                                                    onclick="approveLeave({{ $leave->id }})"
                                                    title="Setujui">
                                                <i class="fas fa-check"></i>
                                            </button>
                                            <button type="button" 
                                                    class="btn btn-sm btn-outline-danger"
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
                <!-- Enhanced Pagination -->
                <div class="mt-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted">
                            <small>
                                <i class="fas fa-list me-1"></i>
                                Total: {{ $leaves->total() }} pengajuan | 
                                Per halaman: {{ $leaves->perPage() }} | 
                                Halaman {{ $leaves->currentPage() }} dari {{ $leaves->lastPage() }}
                            </small>
                        </div>
                        <div>
                            {{ $leaves->appends(request()->query())->links('pagination::bootstrap-4') }}
                        </div>
                    </div>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                    <h5 class="mb-1">Belum ada data pengajuan izin</h5>
                    <p class="text-muted">Silakan ajukan izin baru.</p>
                </div>
            @endif
        </div>
    </div>
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
    // Auto-submit form when filter changes
    const filterSelects = document.querySelectorAll('#status_filter, #leave_type_filter, #user_filter, #sort_by');
    filterSelects.forEach(select => {
        select.addEventListener('change', function() {
            this.form.submit();
        });
    });
    
    // Search with Enter key
    const searchInput = document.getElementById('search');
    searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            this.form.submit();
        }
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