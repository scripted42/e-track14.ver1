@extends('admin.layouts.app')

@section('title', 'Detail Pengguna')

@push('styles')
<style>
.profile-card {
    background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
    border: none;
    border-radius: 1rem;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.profile-header {
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    border-radius: 1rem 1rem 0 0;
    padding: 2rem;
    text-align: center;
    color: white;
}

.avatar-wrapper {
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
    font-weight: bold;
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

.role-badge {
    padding: 0.5rem 1rem;
    border-radius: 0.5rem;
    font-size: 0.875rem;
    font-weight: 600;
    text-transform: uppercase;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.role-admin {
    background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
    color: #991b1b;
}

.role-kepala_sekolah {
    background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
    color: #92400e;
}

.role-waka_kurikulum {
    background: linear-gradient(135deg, #e0f2fe 0%, #b3e5fc 100%);
    color: #0369a1;
}

.role-guru {
    background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%);
    color: #166534;
}

.role-pegawai {
    background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
    color: #374151;
}

.role-siswa {
    background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
    color: #1e40af;
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

.status-badge {
    padding: 0.375rem 0.75rem;
    border-radius: 0.5rem;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
}

.status-active {
    background-color: #dcfce7;
    color: #166534;
}

.status-inactive {
    background-color: #fee2e2;
    color: #991b1b;
}

.activity-item {
    padding: 1rem;
    border-left: 3px solid #e2e8f0;
    margin-bottom: 1rem;
    background: #f8fafc;
    border-radius: 0 0.5rem 0.5rem 0;
}

.activity-item.recent {
    border-left-color: #3b82f6;
    background: #eff6ff;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
}

.stat-card {
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 0.75rem;
    padding: 1.5rem;
    text-align: center;
    transition: all 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1rem;
    font-size: 1.5rem;
}

.stat-value {
    font-size: 2rem;
    font-weight: bold;
    margin-bottom: 0.5rem;
}

.stat-label {
    color: #6b7280;
    font-size: 0.875rem;
    font-weight: 500;
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
                    <h1 class="h3 mb-1 text-gray-800">👤 Detail Pengguna</h1>
                    <p class="text-muted mb-0">Informasi lengkap pengguna sistem</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-primary btn-modern">
                        <i class="fas fa-edit me-2"></i>Edit Pengguna
                    </a>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary btn-modern">
                        <i class="fas fa-arrow-left me-2"></i>Kembali ke Daftar
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Profile Card -->
        <div class="col-lg-4 mb-4">
            <div class="profile-card">
                <div class="profile-header">
                    <div class="avatar-wrapper">
                        {{ substr($user->name, 0, 1) }}
                    </div>
                    <h4 class="mb-2">{{ $user->name }}</h4>
                    <p class="mb-3 opacity-75">{{ $user->email }}</p>
                    
                    @php
                        $roleClass = 'role-' . strtolower(str_replace(' ', '_', $user->role->role_name ?? 'user'));
                    @endphp
                    <div class="role-badge {{ $roleClass }}">
                        @switch($user->role->role_name ?? '')
                            @case('admin')
                                <i class="fas fa-user-shield"></i>
                                Administrator
                                @break
                            @case('kepala sekolah')
                                <i class="fas fa-crown"></i>
                                Kepala Sekolah
                                @break
                            @case('waka kurikulum')
                                <i class="fas fa-user-tie"></i>
                                Waka Kurikulum
                                @break
                            @case('guru')
                                <i class="fas fa-chalkboard-teacher"></i>
                                Guru
                                @break
                            @case('pegawai')
                                <i class="fas fa-user-cog"></i>
                                Pegawai
                                @break
                            @case('siswa')
                                <i class="fas fa-graduation-cap"></i>
                                Siswa
                                @break
                            @default
                                <i class="fas fa-user"></i>
                                Pengguna
                        @endswitch
                    </div>
                </div>
                
                <div class="card-body p-4">
                    <!-- Account Information -->
                    <div class="info-card">
                        <div class="info-label">
                            <i class="fas fa-info-circle me-2"></i>Informasi Akun
                        </div>
                        <div class="row mt-3">
                            <div class="col-6">
                                <div class="info-label">ID Pengguna</div>
                                <div class="info-value">#{{ str_pad($user->id, 4, '0', STR_PAD_LEFT) }}</div>
                            </div>
                            <div class="col-6">
                                <div class="info-label">Status</div>
                                <div class="info-value">
                                    <span class="status-badge status-active">
                                        <i class="fas fa-check-circle me-1"></i>Aktif
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Registration Information -->
                    <div class="info-card">
                        <div class="info-label">
                            <i class="fas fa-calendar-plus me-2"></i>Tanggal Registrasi
                        </div>
                        <div class="info-value">
                            {{ \Carbon\Carbon::parse($user->created_at)->format('d F Y') }}
                        </div>
                        <small class="text-muted">
                            {{ \Carbon\Carbon::parse($user->created_at)->diffForHumans() }}
                        </small>
                    </div>

                    <!-- Last Updated -->
                    <div class="info-card">
                        <div class="info-label">
                            <i class="fas fa-clock me-2"></i>Terakhir Diperbarui
                        </div>
                        <div class="info-value">
                            {{ \Carbon\Carbon::parse($user->updated_at)->format('d F Y, H:i') }}
                        </div>
                        <small class="text-muted">
                            {{ \Carbon\Carbon::parse($user->updated_at)->diffForHumans() }}
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Details and Statistics -->
        <div class="col-lg-8">
            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="profile-card">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">
                                <i class="fas fa-chart-bar text-primary me-2"></i>Statistik Pengguna
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="stats-grid">
                                <div class="stat-card">
                                    <div class="stat-icon bg-success bg-gradient text-white">
                                        <i class="fas fa-calendar-check"></i>
                                    </div>
                                    <div class="stat-value text-success">0</div>
                                    <div class="stat-label">Total Kehadiran</div>
                                </div>
                                
                                <div class="stat-card">
                                    <div class="stat-icon bg-warning bg-gradient text-white">
                                        <i class="fas fa-calendar-times"></i>
                                    </div>
                                    <div class="stat-value text-warning">0</div>
                                    <div class="stat-label">Izin/Cuti</div>
                                </div>
                                
                                <div class="stat-card">
                                    <div class="stat-icon bg-info bg-gradient text-white">
                                        <i class="fas fa-percentage"></i>
                                    </div>
                                    <div class="stat-value text-info">0%</div>
                                    <div class="stat-label">Tingkat Kehadiran</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Information -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="profile-card">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">
                                <i class="fas fa-address-card text-primary me-2"></i>Informasi Kontak
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="info-card">
                                        <div class="info-label">
                                            <i class="fas fa-envelope me-2"></i>Email
                                        </div>
                                        <div class="info-value">{{ $user->email }}</div>
                                        <small class="text-muted">Email utama untuk komunikasi</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-card">
                                        <div class="info-label">
                                            <i class="fas fa-user-tag me-2"></i>Peran
                                        </div>
                                        <div class="info-value">{{ ucfirst($user->role->role_name ?? 'N/A') }}</div>
                                        <small class="text-muted">Tingkat akses dalam sistem</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Role Permissions -->
            <div class="row">
                <div class="col-12">
                    <div class="profile-card">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">
                                <i class="fas fa-shield-alt text-primary me-2"></i>Hak Akses dan Izin
                            </h5>
                        </div>
                        <div class="card-body">
                            @php
                                $permissions = [];
                                switch($user->role->role_name ?? '') {
                                    case 'admin':
                                        $permissions = [
                                            ['name' => 'Kelola Pengguna', 'icon' => 'fas fa-users', 'status' => true],
                                            ['name' => 'Kelola Laporan', 'icon' => 'fas fa-chart-line', 'status' => true],
                                            ['name' => 'Kelola Pengaturan', 'icon' => 'fas fa-cogs', 'status' => true],
                                            ['name' => 'Kelola Kehadiran', 'icon' => 'fas fa-calendar-check', 'status' => true],
                                            ['name' => 'Kelola Izin/Cuti', 'icon' => 'fas fa-calendar-times', 'status' => true],
                                        ];
                                        break;
                                    case 'kepala sekolah':
                                        $permissions = [
                                            ['name' => 'Lihat Semua Laporan', 'icon' => 'fas fa-chart-line', 'status' => true],
                                            ['name' => 'Approve Izin/Cuti', 'icon' => 'fas fa-calendar-times', 'status' => true],
                                            ['name' => 'Kelola Pengaturan Sekolah', 'icon' => 'fas fa-school', 'status' => true],
                                            ['name' => 'Monitor Kehadiran', 'icon' => 'fas fa-calendar-check', 'status' => true],
                                            ['name' => 'Kelola Pengguna', 'icon' => 'fas fa-users', 'status' => false],
                                        ];
                                        break;
                                    case 'waka kurikulum':
                                        $permissions = [
                                            ['name' => 'Lihat Laporan Akademik', 'icon' => 'fas fa-chart-line', 'status' => true],
                                            ['name' => 'Kelola Kurikulum', 'icon' => 'fas fa-book', 'status' => true],
                                            ['name' => 'Monitor Kehadiran Siswa', 'icon' => 'fas fa-calendar-check', 'status' => true],
                                            ['name' => 'Approve Izin Guru', 'icon' => 'fas fa-calendar-times', 'status' => true],
                                            ['name' => 'Kelola Pengguna', 'icon' => 'fas fa-users', 'status' => false],
                                        ];
                                        break;
                                    case 'guru':
                                        $permissions = [
                                            ['name' => 'Lihat Laporan', 'icon' => 'fas fa-chart-line', 'status' => true],
                                            ['name' => 'Kelola Kehadiran Siswa', 'icon' => 'fas fa-calendar-check', 'status' => true],
                                            ['name' => 'Ajukan Izin/Cuti', 'icon' => 'fas fa-calendar-times', 'status' => true],
                                            ['name' => 'Kelola Kelas', 'icon' => 'fas fa-chalkboard', 'status' => true],
                                            ['name' => 'Kelola Pengguna', 'icon' => 'fas fa-users', 'status' => false],
                                        ];
                                        break;
                                    case 'pegawai':
                                        $permissions = [
                                            ['name' => 'Lihat Kehadiran Sendiri', 'icon' => 'fas fa-calendar-check', 'status' => true],
                                            ['name' => 'Ajukan Izin/Cuti', 'icon' => 'fas fa-calendar-times', 'status' => true],
                                            ['name' => 'Lihat Profil', 'icon' => 'fas fa-user', 'status' => true],
                                            ['name' => 'Kelola Laporan', 'icon' => 'fas fa-chart-line', 'status' => false],
                                            ['name' => 'Kelola Pengguna', 'icon' => 'fas fa-users', 'status' => false],
                                        ];
                                        break;
                                    case 'siswa':
                                        $permissions = [
                                            ['name' => 'Lihat Kehadiran Sendiri', 'icon' => 'fas fa-calendar-check', 'status' => true],
                                            ['name' => 'Ajukan Izin', 'icon' => 'fas fa-calendar-times', 'status' => true],
                                            ['name' => 'Lihat Profil', 'icon' => 'fas fa-user', 'status' => true],
                                            ['name' => 'Kelola Laporan', 'icon' => 'fas fa-chart-line', 'status' => false],
                                            ['name' => 'Kelola Pengguna', 'icon' => 'fas fa-users', 'status' => false],
                                        ];
                                        break;
                                    default:
                                        $permissions = [
                                            ['name' => 'Akses Dasar', 'icon' => 'fas fa-eye', 'status' => true],
                                        ];
                                }
                            @endphp

                            <div class="row">
                                @foreach($permissions as $permission)
                                    <div class="col-md-6 mb-3">
                                        <div class="d-flex align-items-center">
                                            <div class="me-3">
                                                @if($permission['status'])
                                                    <i class="fas fa-check-circle text-success"></i>
                                                @else
                                                    <i class="fas fa-times-circle text-danger"></i>
                                                @endif
                                            </div>
                                            <div class="me-3">
                                                <i class="{{ $permission['icon'] }} text-primary"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <span class="{{ $permission['status'] ? 'text-dark' : 'text-muted' }}">
                                                    {{ $permission['name'] }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="d-flex justify-content-center gap-3">
                <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-primary btn-modern">
                    <i class="fas fa-edit me-2"></i>Edit Pengguna
                </a>
                
                @if($user->role->role_name !== 'admin')
                    <button type="button" class="btn btn-danger btn-modern" data-bs-toggle="modal" data-bs-target="#deleteModal">
                        <i class="fas fa-trash me-2"></i>Hapus Pengguna
                    </button>
                @endif
                
                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary btn-modern">
                    <i class="fas fa-list me-2"></i>Daftar Semua Pengguna
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
@if($user->role->role_name !== 'admin')
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteModalLabel">
                    <i class="fas fa-exclamation-triangle me-2"></i>Konfirmasi Hapus
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center">
                    <i class="fas fa-user-times fa-3x text-danger mb-3"></i>
                    <h5>Hapus Pengguna</h5>
                    <p class="mb-3">
                        Apakah Anda yakin ingin menghapus pengguna <strong>{{ $user->name }}</strong>?
                    </p>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Peringatan:</strong> Tindakan ini tidak dapat dibatalkan dan akan menghapus semua data terkait pengguna ini.
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Batal
                </button>
                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-2"></i>Ya, Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add any interactive features here
    console.log('User detail page loaded for user: {{ $user->name }}');
});
</script>
@endpush