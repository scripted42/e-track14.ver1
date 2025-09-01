@extends('admin.layouts.app')

@section('title', 'Detail Pengajuan Izin')

@push('styles')
<style>
.detail-card {
    background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
    border: none;
    border-radius: 1rem;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.detail-header {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    border-radius: 1rem 1rem 0 0;
    padding: 2rem;
    text-align: center;
    color: white;
}

.status-icon {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(10px);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1rem;
    font-size: 3rem;
    border: 4px solid rgba(255, 255, 255, 0.3);
}

.info-card {
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 0.75rem;
    padding: 1.5rem;
    margin-bottom: 1rem;
    transition: all 0.3s ease;
}

.info-card:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    transform: translateY(-2px);
}

.info-label {
    font-size: 0.875rem;
    font-weight: 600;
    color: #6b7280;
    margin-bottom: 0.25rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.info-value {
    font-size: 1.1rem;
    font-weight: 500;
    color: #1f2937;
}

.status-badge {
    padding: 0.5rem 1rem;
    border-radius: 0.5rem;
    font-size: 0.875rem;
    font-weight: 600;
    text-transform: uppercase;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.status-menunggu {
    background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
    color: #92400e;
}

.status-disetujui {
    background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%);
    color: #166534;
}

.status-ditolak {
    background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
    color: #991b1b;
}

.leave-type-badge {
    padding: 0.5rem 1rem;
    border-radius: 0.5rem;
    font-size: 0.875rem;
    font-weight: 600;
    text-transform: uppercase;
}

.type-izin {
    background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
    color: #1e40af;
}

.type-sakit {
    background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
    color: #991b1b;
}

.type-cuti {
    background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
    color: #166534;
}

.type-dinas_luar {
    background: linear-gradient(135deg, #faf5ff 0%, #f3e8ff 100%);
    color: #7c3aed;
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

.timeline {
    position: relative;
    padding-left: 2rem;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 0.75rem;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e2e8f0;
}

.timeline-item {
    position: relative;
    margin-bottom: 2rem;
}

.timeline-item::before {
    content: '';
    position: absolute;
    left: -1.5rem;
    top: 0.5rem;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background: #f59e0b;
    border: 3px solid white;
    box-shadow: 0 0 0 3px #f59e0b;
}

.timeline-content {
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 0.5rem;
    padding: 1rem;
}

.duration-display {
    background: linear-gradient(135deg, #e2e8f0 0%, #cbd5e1 100%);
    color: #475569;
    padding: 1rem;
    border-radius: 0.75rem;
    text-align: center;
    font-weight: 600;
    margin: 1rem 0;
}

.user-avatar {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    font-weight: bold;
    color: white;
    margin: 0 auto 1rem;
}

.approval-section {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 0.75rem;
    padding: 1.5rem;
    margin: 1rem 0;
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
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-1 text-gray-800">📋 Detail Pengajuan Izin</h1>
                    <p class="text-muted mb-0">Informasi lengkap pengajuan izin dan cuti</p>
                </div>
                <a href="{{ route('admin.leaves.index') }}" class="btn btn-outline-secondary btn-modern">
                    <i class="fas fa-arrow-left me-2"></i>Kembali ke Daftar
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Leave Details Card -->
        <div class="col-lg-8 mb-4">
            <div class="detail-card">
                <div class="detail-header">
                    <div class="status-icon">
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
                
                <div class="card-body p-4">
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
            <div class="detail-card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
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
            <div class="detail-card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
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
            <div class="detail-card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
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
            <div class="detail-card">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fas fa-tasks text-primary me-2"></i>Aksi Persetujuan
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button type="button" 
                                class="btn btn-success btn-modern"
                                data-bs-toggle="modal" 
                                data-bs-target="#approveModal">
                            <i class="fas fa-check me-2"></i>Setujui Pengajuan
                        </button>
                        <button type="button" 
                                class="btn btn-danger btn-modern"
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
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="approveModalLabel">
                    <i class="fas fa-check-circle me-2"></i>Setujui Pengajuan Izin
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
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
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="rejectModalLabel">
                    <i class="fas fa-times-circle me-2"></i>Tolak Pengajuan Izin
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
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