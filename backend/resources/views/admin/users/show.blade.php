@extends('admin.layouts.app')

@section('title', 'Detail Staff')

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

.user-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 0.5rem;
}

.user-subtitle {
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

.role-badge {
    padding: 0.375rem 0.75rem;
    border-radius: 4px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
}

.role-admin {
    background: #d1ecf1;
    color: #0c5460;
}

.role-guru {
    background: #d4edda;
    color: #155724;
}

.role-pegawai {
    background: #fff3cd;
    color: #856404;
}

.user-avatar {
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
}

.user-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 50%;
}

.walikelas-info {
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
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Detail Staff</h1>
            <p class="text-muted mb-0">Informasi lengkap {{ $user->name }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-warning">
                <i class="fas fa-edit me-2"></i>Edit Staff
            </a>
            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Kembali
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Main Information -->
        <div class="col-lg-8">
            <!-- User Profile Card -->
            <div class="detail-card">
                <div class="detail-header">
                    <div class="text-center">
                        <div class="user-avatar">
                            @if($user->photo && file_exists(public_path('storage/user-photos/' . $user->photo)))
                                <img src="{{ asset('storage/user-photos/' . $user->photo) }}" alt="Foto {{ $user->name }}">
                            @else
                                {{ substr($user->name, 0, 1) }}
                            @endif
                        </div>
                        <h2 class="user-title">{{ $user->name }}</h2>
                        <p class="user-subtitle">
                            @php
                                $roleClass = '';
                                switch(strtolower($user->role->role_name ?? '')) {
                                    case 'admin':
                                        $roleClass = 'role-admin';
                                        break;
                                    case 'guru':
                                        $roleClass = 'role-guru';
                                        break;
                                    case 'pegawai':
                                        $roleClass = 'role-pegawai';
                                        break;
                                    default:
                                        $roleClass = 'bg-secondary text-white';
                                }
                            @endphp
                            <span class="role-badge {{ $roleClass }}">
                                {{ ucfirst($user->role->role_name ?? 'Unknown') }}
                            </span>
                        </p>
                    </div>
                </div>
                <div class="detail-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-item">
                                <p class="info-label">NIP/NIK</p>
                                <p class="info-value">{{ $user->nip_nik ?? 'Tidak ada' }}</p>
                            </div>
                            <div class="info-item">
                                <p class="info-label">Status</p>
                                <p class="info-value">
                                    @if($user->status == 'Aktif')
                                        <span class="status-badge status-active">Aktif</span>
                                    @else
                                        <span class="status-badge status-inactive">Non-Aktif</span>
                                    @endif
                                </p>
                            </div>
                            <div class="info-item">
                                <p class="info-label">Peran</p>
                                <p class="info-value">{{ ucfirst($user->role->role_name ?? 'Unknown') }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item">
                                <p class="info-label">Tanggal Dibuat</p>
                                <p class="info-value">{{ $user->created_at->format('d M Y H:i') }}</p>
                            </div>
                            <div class="info-item">
                                <p class="info-label">Terakhir Diupdate</p>
                                <p class="info-value">{{ $user->updated_at->format('d M Y H:i') }}</p>
                            </div>
                            <div class="info-item">
                                <p class="info-label">ID Pengguna</p>
                                <p class="info-value">#{{ $user->id }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Address Information -->
            @if($user->address)
            <div class="detail-card">
                <div class="detail-header">
                    <h5 class="mb-0">
                        <i class="fas fa-map-marker-alt me-2"></i>Alamat
                    </h5>
                </div>
                <div class="detail-body">
                    <p class="mb-0">{{ $user->address }}</p>
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
                        <i class="fas fa-envelope me-2"></i>Informasi Kontak
                    </h5>
                </div>
                <div class="detail-body">
                    <div class="contact-info">
                        <div class="contact-item">
                            <div class="contact-icon">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div class="contact-text">
                                <p class="contact-label">Email</p>
                                <p class="contact-value">{{ $user->email }}</p>
                            </div>
                        </div>
                        @if($user->nip_nik)
                        <div class="contact-item">
                            <div class="contact-icon">
                                <i class="fas fa-id-card"></i>
                            </div>
                            <div class="contact-text">
                                <p class="contact-label">NIP/NIK</p>
                                <p class="contact-value">{{ $user->nip_nik }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Walikelas Information -->
            @if($user->is_walikelas && $user->classRoom)
            <div class="detail-card">
                <div class="detail-header">
                    <h5 class="mb-0">
                        <i class="fas fa-chalkboard me-2"></i>Informasi Walikelas
                    </h5>
                </div>
                <div class="detail-body">
                    <div class="walikelas-info">
                        <div class="classroom-badge mb-2">
                            Kelas {{ $user->classRoom->level }}
                        </div>
                        <h6 class="fw-semibold mb-1">{{ $user->classRoom->name }}</h6>
                        <p class="text-muted mb-2">{{ $user->classRoom->description ?? 'Tidak ada deskripsi' }}</p>
                        <p class="text-muted small mb-0">
                            <i class="fas fa-users me-1"></i>
                            {{ $user->classRoom->students->count() }} Siswa
                        </p>
                    </div>
                </div>
            </div>
            @elseif($user->is_walikelas)
            <div class="detail-card">
                <div class="detail-header">
                    <h5 class="mb-0">
                        <i class="fas fa-chalkboard me-2"></i>Informasi Walikelas
                    </h5>
                </div>
                <div class="detail-body">
                    <div class="empty-state">
                        <i class="fas fa-exclamation-triangle"></i>
                        <h6>Belum Ditugaskan</h6>
                        <p>User ini ditandai sebagai walikelas tetapi belum ditugaskan ke kelas tertentu.</p>
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
                        <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-warning">
                            <i class="fas fa-edit me-2"></i>Edit Staff
                        </a>
                        @if($user->is_walikelas && $user->classRoom)
                        <a href="{{ route('admin.classrooms.show', $user->classRoom) }}" class="btn btn-secondary">
                            <i class="fas fa-chalkboard me-2"></i>Lihat Kelas
                        </a>
                        @endif
                        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-list me-2"></i>Daftar Pengguna
                        </a>
                    </div>
                </div>
            </div>

            <!-- Account Status -->
            <div class="detail-card">
                <div class="detail-header">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>Status Akun
                    </h5>
                </div>
                <div class="detail-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="mb-2">
                                @if($user->status == 'Aktif')
                                    <i class="fas fa-check-circle text-success fa-2x"></i>
                                @else
                                    <i class="fas fa-times-circle text-danger fa-2x"></i>
                                @endif
                            </div>
                            <p class="mb-0 small">Status</p>
                            <p class="fw-semibold">{{ $user->status }}</p>
                        </div>
                        <div class="col-6">
                            <div class="mb-2">
                                @if($user->is_walikelas)
                                    <i class="fas fa-chalkboard text-primary fa-2x"></i>
                                @else
                                    <i class="fas fa-user text-muted fa-2x"></i>
                                @endif
                            </div>
                            <p class="mb-0 small">Walikelas</p>
                            <p class="fw-semibold">{{ $user->is_walikelas ? 'Ya' : 'Tidak' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection