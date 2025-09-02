@extends('admin.layouts.app')

@section('title', 'Detail Siswa')

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

.info-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem 0;
    border-bottom: 1px solid #f1f3f4;
}

.info-item:last-child {
    border-bottom: none;
}

.info-label {
    font-weight: 600;
    color: #495057;
    margin: 0;
}

.info-value {
    color: #6c757d;
    margin: 0;
}

.student-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 0.5rem;
}

.student-subtitle {
    color: #6c757d;
    margin-bottom: 0;
}

.status-badge {
    padding: 0.375rem 0.75rem;
    border-radius: 4px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
}

.status-active {
    background: #d4edda;
    color: #155724;
}

.status-inactive {
    background: #f8d7da;
    color: #721c24;
}

.class-badge {
    padding: 0.375rem 0.75rem;
    border-radius: 4px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    background: #e3f2fd;
    color: #1976d2;
}

.student-avatar {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    background: #6c757d;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 3rem;
    font-weight: 600;
    margin: 0 auto 1rem;
    position: relative;
    overflow: hidden;
    border: 4px solid #fff;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.student-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 50%;
}

.classroom-info {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 1rem;
    border: 1px solid #e9ecef;
    text-align: center;
}

.classroom-badge {
    background: #e3f2fd;
    color: #1976d2;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
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

.contact-info {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 1rem;
    border: 1px solid #e9ecef;
}

.contact-item {
    display: flex;
    align-items: center;
    margin-bottom: 0.75rem;
}

.contact-item:last-child {
    margin-bottom: 0;
}

.contact-icon {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: #e9ecef;
    color: #6c757d;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 0.75rem;
    font-size: 0.875rem;
}

.contact-text {
    flex: 1;
}

.contact-label {
    font-size: 0.75rem;
    color: #6c757d;
    margin: 0;
}

.contact-value {
    font-weight: 500;
    color: #495057;
    margin: 0;
}

.qr-code-container {
    text-align: center;
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 8px;
    border: 1px solid #e9ecef;
    max-width: 200px;
    margin: 0 auto;
}

.qr-code-image {
    max-width: 150px;
    height: auto;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    border: 2px solid #fff;
}

.qr-actions {
    margin-top: 1rem;
    display: flex;
    gap: 0.5rem;
    justify-content: center;
}

.qr-actions .btn {
    padding: 0.375rem 0.75rem;
    font-size: 0.875rem;
}

.attendance-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
    gap: 1rem;
    margin-top: 1rem;
}

.stat-item {
    text-align: center;
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 8px;
    border: 1px solid #e9ecef;
}

.stat-number {
    font-size: 1.5rem;
    font-weight: 700;
    color: #007bff;
    margin-bottom: 0.25rem;
}

.stat-label {
    font-size: 0.75rem;
    color: #6c757d;
    text-transform: uppercase;
    font-weight: 600;
}
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Detail Siswa</h1>
            <p class="text-muted mb-0">Informasi lengkap {{ $student->name }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.students.edit', $student) }}" class="btn btn-warning">
                <i class="fas fa-edit me-2"></i>Edit Siswa
            </a>
            <a href="{{ route('admin.students.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Kembali
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Main Information -->
        <div class="col-lg-8">
            <!-- Student Profile Card -->
            <div class="detail-card">
                <div class="detail-header">
                    <div class="row align-items-center">
                        <div class="col-md-6 text-center">
                            <div class="student-avatar">
                                @if($student->photo)
                                    @php
                                        $photoPath = str_starts_with($student->photo, 'student-photos/') 
                                            ? $student->photo 
                                            : 'student-photos/' . $student->photo;
                                    @endphp
                                    @if(file_exists(public_path('storage/' . $photoPath)))
                                        <img src="{{ asset('storage/' . $photoPath) }}" alt="Foto {{ $student->name }}">
                                    @else
                                        {{ substr($student->name, 0, 1) }}
                                    @endif
                                @else
                                    {{ substr($student->name, 0, 1) }}
                                @endif
                            </div>
                            <h2 class="student-title">{{ $student->name }}</h2>
                            <p class="student-subtitle">
                                <span class="class-badge">
                                    Kelas {{ $student->class_name }}
                                </span>
                            </p>
                        </div>
                        <div class="col-md-6 text-center">
                            @if($student->card_qr_code)
                                <div class="qr-code-container">
                                    @if(isset($qrCodeBase64))
                                        <img src="{{ $qrCodeBase64 }}" 
                                             alt="QR Code {{ $student->name }}" 
                                             class="qr-code-image">
                                    @else
                                        <img src="{{ asset('storage/qr-codes/' . $student->card_qr_code) }}" 
                                             alt="QR Code {{ $student->name }}" 
                                             class="qr-code-image"
                                             onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                                        <div style="display: none; color: #6c757d;">
                                            <i class="fas fa-qrcode fa-3x mb-2"></i>
                                            <p>QR Code tidak tersedia</p>
                                        </div>
                                    @endif
                                    <div class="qr-actions">
                                        @if(isset($qrCodeBase64))
                                            <button type="button" 
                                                    class="btn btn-primary btn-sm" 
                                                    onclick="downloadQRCode('{{ $qrCodeBase64 }}', '{{ $student->name }}_QR_Code.svg')">
                                                <i class="fas fa-download me-1"></i>Download
                                            </button>
                                        @else
                                            <a href="{{ asset('storage/qr-codes/' . $student->card_qr_code) }}" 
                                               download="{{ $student->name }}_QR_Code.png" 
                                               class="btn btn-primary btn-sm">
                                                <i class="fas fa-download me-1"></i>Download
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            @else
                                <div class="qr-code-container">
                                    <div style="color: #6c757d;">
                                        <i class="fas fa-qrcode fa-3x mb-2"></i>
                                        <p>QR Code tidak tersedia</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="detail-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-item">
                                <p class="info-label">NISN</p>
                                <p class="info-value">{{ $student->nisn ?? 'Tidak ada' }}</p>
                            </div>
                            <div class="info-item">
                                <p class="info-label">Kelas</p>
                                <p class="info-value">{{ $student->class_name ?? 'Tidak ada' }}</p>
                            </div>
                            <div class="info-item">
                                <p class="info-label">Status</p>
                                <p class="info-value">
                                    @if($student->status == 'Aktif')
                                        <span class="status-badge status-active">Aktif</span>
                                    @else
                                        <span class="status-badge status-inactive">Non-Aktif</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item">
                                <p class="info-label">Tanggal Dibuat</p>
                                <p class="info-value">{{ $student->created_at->format('d M Y H:i') }}</p>
                            </div>
                            <div class="info-item">
                                <p class="info-label">Terakhir Diupdate</p>
                                <p class="info-value">{{ $student->updated_at->format('d M Y H:i') }}</p>
                            </div>
                            <div class="info-item">
                                <p class="info-label">ID Siswa</p>
                                <p class="info-value">#{{ $student->id }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Address Information -->
            @if($student->address)
            <div class="detail-card">
                <div class="detail-header">
                    <h5 class="mb-0">
                        <i class="fas fa-map-marker-alt me-2"></i>Alamat
                    </h5>
                </div>
                <div class="detail-body">
                    <p class="mb-0">{{ $student->address }}</p>
                </div>
            </div>
            @endif


        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Contact Information -->
            <div class="detail-card">
                <div class="detail-header">
                    <h5 class="mb-0">
                        <i class="fas fa-id-card me-2"></i>Informasi Siswa
                    </h5>
                </div>
                <div class="detail-body">
                    <div class="contact-info">
                        <div class="contact-item">
                            <div class="contact-icon">
                                <i class="fas fa-id-card"></i>
                            </div>
                            <div class="contact-text">
                                <p class="contact-label">NISN</p>
                                <p class="contact-value">{{ $student->nisn ?? 'Tidak ada' }}</p>
                            </div>
                        </div>
                        <div class="contact-item">
                            <div class="contact-icon">
                                <i class="fas fa-graduation-cap"></i>
                            </div>
                            <div class="contact-text">
                                <p class="contact-label">Kelas</p>
                                <p class="contact-value">{{ $student->class_name ?? 'Tidak ada' }}</p>
                            </div>
                        </div>
                        @if($student->address)
                        <div class="contact-item">
                            <div class="contact-icon">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div class="contact-text">
                                <p class="contact-label">Alamat</p>
                                <p class="contact-value">{{ Str::limit($student->address, 50) }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Class Information -->
            @if($student->class_name)
            <div class="detail-card">
                <div class="detail-header">
                    <h5 class="mb-0">
                        <i class="fas fa-chalkboard me-2"></i>Informasi Kelas
                    </h5>
                </div>
                <div class="detail-body">
                    <div class="classroom-info">
                        <div class="classroom-badge mb-2">
                            Kelas {{ $student->class_name }}
                        </div>
                        <h6 class="fw-semibold mb-1">{{ $student->class_name }}</h6>
                        <p class="text-muted mb-0">
                            <i class="fas fa-user-graduate me-1"></i>
                            Siswa Kelas {{ $student->class_name }}
                        </p>
                    </div>
                </div>
            </div>
            @endif

            <!-- Quick Actions -->
            <div class="detail-card">
                <div class="detail-header">
                    <h5 class="mb-0">
                        <i class="fas fa-bolt me-2"></i>Aksi Cepat
                    </h5>
                </div>
                <div class="detail-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.students.edit', $student) }}" class="btn btn-warning">
                            <i class="fas fa-edit me-2"></i>Edit Siswa
                        </a>
                        @if($student->class_name)
                        <a href="{{ route('admin.classrooms.index') }}?search={{ $student->class_name }}" class="btn btn-secondary">
                            <i class="fas fa-chalkboard me-2"></i>Lihat Kelas
                        </a>
                        @endif
                        <a href="{{ route('admin.students.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-list me-2"></i>Daftar Siswa
                        </a>
                    </div>
                </div>
            </div>

            <!-- Student Status -->
            <div class="detail-card">
                <div class="detail-header">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>Status Siswa
                    </h5>
                </div>
                <div class="detail-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="mb-2">
                                @if($student->status == 'Aktif')
                                    <i class="fas fa-check-circle text-success fa-2x"></i>
                                @else
                                    <i class="fas fa-times-circle text-danger fa-2x"></i>
                                @endif
                            </div>
                            <p class="mb-0 small">Status</p>
                            <p class="fw-semibold">{{ $student->status }}</p>
                        </div>
                        <div class="col-6">
                            <div class="mb-2">
                                @if($student->card_qr_code)
                                    <i class="fas fa-qrcode text-primary fa-2x"></i>
                                @else
                                    <i class="fas fa-exclamation-triangle text-warning fa-2x"></i>
                                @endif
                            </div>
                            <p class="mb-0 small">QR Code</p>
                            <p class="fw-semibold">{{ $student->card_qr_code ? 'Ada' : 'Tidak Ada' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function downloadQRCode(base64Data, filename) {
    // Create a temporary link element
    const link = document.createElement('a');
    link.href = base64Data;
    link.download = filename;
    
    // Append to body, click, and remove
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}
</script>
@endpush
@endsection