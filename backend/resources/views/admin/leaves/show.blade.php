@extends('admin.layouts.app')

@section('title', 'Detail Pengajuan Izin')

@push('styles')
<style>
.detail-card {
    background: #ffffff;
    border: 1px solid #e9ecef;
    border-radius: 15px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    overflow: hidden;
}

.detail-header {
    background: #f8f9fa;
    border-bottom: 1px solid #e9ecef;
    padding: 2rem;
    text-align: center;
    color: #2c3e50;
}

.status-icon {
    width: 80px;
    height: 80px;
    border-radius: 8px;
    background: #f8f9fa;
    border: 1px solid #e9ecef;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1rem;
    font-size: 2rem;
    color: #6c757d;
}

.info-card {
    background: white;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 1.5rem;
    margin-bottom: 1rem;
    transition: all 0.2s ease;
}

.info-card:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    transform: translateY(-1px);
}

.info-label {
    font-size: 0.875rem;
    font-weight: 600;
    color: #6c757d;
    margin-bottom: 0.5rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.info-value {
    font-size: 1.1rem;
    font-weight: 500;
    color: #2c3e50;
}

.status-badge {
    padding: 0.5rem 1rem;
    border-radius: 6px;
    font-size: 0.875rem;
    font-weight: 500;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
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
    padding: 0.5rem 1rem;
    border-radius: 6px;
    font-size: 0.875rem;
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

.duration-display {
    background: #f8f9fa;
    color: #6c757d;
    padding: 1.5rem;
    border-radius: 8px;
    text-align: center;
    font-weight: 500;
    margin: 1rem 0;
    border: 1px solid #e9ecef;
}

.user-avatar {
    width: 60px;
    height: 60px;
    border-radius: 8px;
    background: #f8f9fa;
    border: 1px solid #e9ecef;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    font-weight: bold;
    color: #6c757d;
    margin: 0 auto 1rem;
}

.approval-section {
    background: #f8f9fa;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 1.5rem;
    margin: 1rem 0;
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

.card-header {
    background: #f8f9fa;
    border-bottom: 1px solid #e9ecef;
    padding: 1rem 1.5rem;
}

.card-title {
    color: #2c3e50;
    font-weight: 600;
    margin: 0;
}

.card-body {
    padding: 1.5rem;
}

.text-muted {
    color: #6c757d !important;
}

.text-primary {
    color: #007bff !important;
}

.text-success {
    color: #28a745 !important;
}

.text-danger {
    color: #dc3545 !important;
}

.text-warning {
    color: #ffc107 !important;
}

.text-info {
    color: #17a2b8 !important;
}
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Detail Pengajuan Izin</h1>
            <p class="text-muted mb-0">Informasi lengkap pengajuan izin dan cuti</p>
        </div>
        <a href="{{ route('admin.leaves.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Kembali ke Daftar
        </a>
    </div>

    <div class="row">
        <!-- Leave Details Card -->
        <div class="col-lg-8 mb-4">
            <div class="detail-card">
                <div class="detail-header">
                    <div class="status-icon">
                        @switch($leave->status)
                            @case('menunggu')
                                <i class="fas fa-clock text-warning"></i>
                                @break
                            @case('disetujui')
                                <i class="fas fa-check-circle text-success"></i>
                                @break
                            @case('ditolak')
                                <i class="fas fa-times-circle text-danger"></i>
                                @break
                        @endswitch
                    </div>
                    <h4 class="mb-2">{{ $leave->getLeaveTypeLabel() }}</h4>
                    <div class="status-badge status-{{ $leave->status }}">
                        @switch($leave->status)
                            @case('menunggu')
                                <i class="fas fa-clock"></i>
                                @break
                            @case('disetujui')
                                <i class="fas fa-check-circle"></i>
                                @break
                            @case('ditolak')
                                <i class="fas fa-times-circle"></i>
                                @break
                        @endswitch
                        {{ $leave->getStatusLabel() }}
                    </div>
                </div>
                
                <div class="card-body">
                    <!-- Leave Information -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="info-card">
                                <div class="info-label">
                                    <i class="fas fa-calendar-alt me-2"></i>Tanggal Mulai
                                </div>
                                <div class="info-value">
                                    {{ \Carbon\Carbon::parse($leave->start_date)->format('d F Y') }}
                                </div>
                                <small class="text-muted">
                                    {{ \Carbon\Carbon::parse($leave->start_date)->format('l') }}
                                </small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-card">
                                <div class="info-label">
                                    <i class="fas fa-calendar-check me-2"></i>Tanggal Selesai
                                </div>
                                <div class="info-value">
                                    {{ \Carbon\Carbon::parse($leave->end_date)->format('d F Y') }}
                                </div>
                                <small class="text-muted">
                                    {{ \Carbon\Carbon::parse($leave->end_date)->format('l') }}
                                </small>
                            </div>
                        </div>
                    </div>

                    <!-- Duration Display -->
                    <div class="duration-display">
                        <div class="row text-center">
                            <div class="col-4">
                                <div class="h4 mb-1">{{ $leave->getDurationDays() }}</div>
                                <small>Hari</small>
                            </div>
                            <div class="col-4">
                                <div class="h4 mb-1">{{ $leave->getDurationDays() * 8 }}</div>
                                <small>Jam Kerja</small>
                            </div>
                            <div class="col-4">
                                <div class="h4 mb-1">{{ ceil($leave->getDurationDays() / 7) }}</div>
                                <small>Minggu</small>
                            </div>
                        </div>
                    </div>

                    <!-- Reason -->
                    <div class="info-card">
                        <div class="info-label">
                            <i class="fas fa-comment-alt me-2"></i>Alasan Pengajuan
                        </div>
                        <div class="info-value">
                            {{ $leave->reason }}
                        </div>
                    </div>

                    <!-- Attachment -->
                    @if($leave->attachment_path)
                    <div class="info-card">
                        <div class="info-label">
                            <i class="fas fa-paperclip me-2"></i>Lampiran
                        </div>
                        <div class="info-value">
                            <a href="{{ asset('storage/' . $leave->attachment_path) }}" 
                               target="_blank" 
                               class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-download me-2"></i>Unduh Lampiran
                            </a>
                        </div>
                    </div>
                    @endif

                    <!-- Submission Details -->
                    <div class="info-card">
                        <div class="info-label">
                            <i class="fas fa-clock me-2"></i>Tanggal Pengajuan
                        </div>
                        <div class="info-value">
                            {{ \Carbon\Carbon::parse($leave->created_at)->format('d F Y, H:i') }}
                        </div>
                        <small class="text-muted">
                            {{ \Carbon\Carbon::parse($leave->created_at)->diffForHumans() }}
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Employee Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="fas fa-user text-primary me-2"></i>Informasi Pemohon
                    </h5>
                </div>
                <div class="card-body text-center">
                    <div class="user-avatar">
                        {{ substr($leave->user->name, 0, 1) }}
                    </div>
                    <h6 class="fw-bold">{{ $leave->user->name }}</h6>
                    <p class="text-muted mb-2">{{ $leave->user->email }}</p>
                    <span class="badge bg-primary">{{ $leave->user->role->role_name ?? 'N/A' }}</span>
                </div>
            </div>

            <!-- Leave Type Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="fas fa-info-circle text-primary me-2"></i>Informasi Izin
                    </h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <div class="leave-type-badge type-{{ $leave->leave_type }}">
                            {{ $leave->getLeaveTypeLabel() }}
                        </div>
                    </div>
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="info-label">ID Pengajuan</div>
                            <div class="info-value">#{{ str_pad($leave->id, 4, '0', STR_PAD_LEFT) }}</div>
                        </div>
                        <div class="col-6">
                            <div class="info-label">Prioritas</div>
                            <div class="info-value">
                                @if($leave->leave_type == 'sakit')
                                    <span class="text-danger">Tinggi</span>
                                @elseif($leave->leave_type == 'dinas_luar')
                                    <span class="text-warning">Sedang</span>
                                @else
                                    <span class="text-success">Normal</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Approval Information -->
            @if($leave->approver || $leave->status !== 'menunggu')
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="fas fa-user-check text-primary me-2"></i>Informasi Persetujuan
                    </h5>
                </div>
                <div class="card-body">
                    @if($leave->approver)
                        <div class="text-center mb-3">
                            <div class="user-avatar">
                                {{ substr($leave->approver->name, 0, 1) }}
                            </div>
                            <h6 class="fw-bold">{{ $leave->approver->name }}</h6>
                            <small class="text-muted">Penyetuju</small>
                        </div>
                        <div class="info-card">
                            <div class="info-label">Tanggal Diproses</div>
                            <div class="info-value">
                                {{ \Carbon\Carbon::parse($leave->approved_at)->format('d F Y, H:i') }}
                            </div>
                            <small class="text-muted">
                                {{ \Carbon\Carbon::parse($leave->approved_at)->diffForHumans() }}
                            </small>
                        </div>
                    @else
                        <div class="text-center text-muted">
                            <i class="fas fa-hourglass-half fa-2x mb-2"></i>
                            <p>Menunggu persetujuan</p>
                        </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Action Buttons -->
            @if($leave->isPending())
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="fas fa-tasks text-primary me-2"></i>Aksi Persetujuan
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button type="button" 
                                class="btn btn-success"
                                data-bs-toggle="modal" 
                                data-bs-target="#approveModal">
                            <i class="fas fa-check me-2"></i>Setujui Pengajuan
                        </button>
                        <button type="button" 
                                class="btn btn-danger"
                                data-bs-toggle="modal" 
                                data-bs-target="#rejectModal">
                            <i class="fas fa-times me-2"></i>Tolak Pengajuan
                        </button>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Approve Modal -->
<div class="modal fade" id="approveModal" tabindex="-1" aria-labelledby="approveModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="approveModalLabel">
                    <i class="fas fa-check-circle text-success me-2"></i>Setujui Pengajuan Izin
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.leaves.approve', $leave) }}" method="POST">
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
            <div class="modal-header">
                <h5 class="modal-title" id="rejectModalLabel">
                    <i class="fas fa-times-circle text-danger me-2"></i>Tolak Pengajuan Izin
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.leaves.reject', $leave) }}" method="POST">
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
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
    
    console.log('Leave detail page loaded for leave ID: {{ $leave->id }}');
});
</script>
@endpush