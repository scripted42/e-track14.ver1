@extends('admin.layouts.app')

@section('title', 'Detail Kelas')

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

.classroom-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 0.5rem;
}

.classroom-subtitle {
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

.walikelas-card {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 1rem;
    border: 1px solid #e9ecef;
}

.walikelas-avatar {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: #6c757d;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    font-weight: 600;
    margin-bottom: 1rem;
}

.students-table {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    border: 1px solid #e9ecef;
    overflow: hidden;
}

.table th {
    background: #f8f9fa;
    border-bottom: 2px solid #dee2e6;
    font-weight: 600;
    color: #495057;
    padding: 1rem 0.75rem;
}

.table td {
    padding: 1rem 0.75rem;
    vertical-align: middle;
    border-bottom: 1px solid #dee2e6;
}

.student-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #6c757d;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.875rem;
    font-weight: 600;
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

.empty-state {
    text-align: center;
    padding: 3rem 1rem;
    color: #6c757d;
}

.empty-state i {
    font-size: 3rem;
    margin-bottom: 1rem;
    opacity: 0.5;
}

.empty-state h5 {
    margin-bottom: 0.5rem;
    color: #495057;
}

.empty-state p {
    margin-bottom: 1.5rem;
}
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Detail Kelas</h1>
            <p class="text-muted mb-0">Informasi lengkap kelas {{ $classroom->name }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.classrooms.edit', $classroom) }}" class="btn btn-primary">
                <i class="fas fa-edit me-2"></i>Edit Kelas
            </a>
            <a href="{{ route('admin.classrooms.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Kembali
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Main Information -->
        <div class="col-lg-8">
            <div class="detail-card">
                <div class="detail-header">
                    <h2 class="classroom-title">{{ $classroom->name }}</h2>
                    <p class="classroom-subtitle">{{ $classroom->description ?? 'Tidak ada deskripsi' }}</p>
                </div>
                <div class="detail-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-item">
                                <p class="info-label">Nama Kelas</p>
                                <p class="info-value">{{ $classroom->name }}</p>
                            </div>
                            <div class="info-item">
                                <p class="info-label">Tingkat</p>
                                <p class="info-value">Kelas {{ $classroom->level }}</p>
                            </div>
                            <div class="info-item">
                                <p class="info-label">Status</p>
                                <p class="info-value">
                                    @if($classroom->is_active)
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
                                <p class="info-value">{{ $classroom->created_at->format('d M Y H:i') }}</p>
                            </div>
                            <div class="info-item">
                                <p class="info-label">Terakhir Diupdate</p>
                                <p class="info-value">{{ $classroom->updated_at->format('d M Y H:i') }}</p>
                            </div>
                            <div class="info-item">
                                <p class="info-label">Jumlah Siswa</p>
                                <p class="info-value">{{ $classroom->students->count() }} Siswa</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Students List -->
            <div class="students-table">
                <div class="detail-header">
                    <h5 class="mb-0">
                        <i class="fas fa-user-graduate me-2"></i>Daftar Siswa
                    </h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>NISN</th>
                                <th>Siswa</th>
                                <th>Status</th>
                                <th>Tanggal Bergabung</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($classroom->students as $student)
                                <tr>
                                    <td>
                                        <span class="fw-medium">{{ $student->nisn ?? 'N/A' }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="student-avatar me-3">
                                                @if($student->photo && file_exists(public_path('storage/student-photos/' . $student->photo)))
                                                    <img src="{{ asset('storage/student-photos/' . $student->photo) }}" alt="Foto" class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;">
                                                @else
                                                    {{ substr($student->name, 0, 1) }}
                                                @endif
                                            </div>
                                            <div>
                                                <div class="fw-semibold">{{ $student->name }}</div>
                                                <small class="text-muted">{{ $student->class_name }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($student->status == 'Aktif')
                                            <span class="status-badge status-active">Aktif</span>
                                        @else
                                            <span class="status-badge status-inactive">Non-Aktif</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="fw-medium">{{ $student->created_at->format('d M Y') }}</span>
                                        <br><small class="text-muted">{{ $student->created_at->format('H:i') }}</small>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">
                                        <div class="empty-state">
                                            <i class="fas fa-user-graduate"></i>
                                            <h5>Belum ada siswa</h5>
                                            <p>Belum ada siswa yang terdaftar di kelas ini</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Walikelas Information -->
            <div class="detail-card">
                <div class="detail-header">
                    <h5 class="mb-0">
                        <i class="fas fa-user-tie me-2"></i>Walikelas
                    </h5>
                </div>
                <div class="detail-body">
                    @if($classroom->walikelas)
                        <div class="walikelas-card text-center">
                            <div class="walikelas-avatar mx-auto">
                                @if($classroom->walikelas->photo && file_exists(public_path('storage/user-photos/' . $classroom->walikelas->photo)))
                                    <img src="{{ asset('storage/user-photos/' . $classroom->walikelas->photo) }}" alt="Foto" class="rounded-circle" style="width: 60px; height: 60px; object-fit: cover;">
                                @else
                                    {{ substr($classroom->walikelas->name, 0, 1) }}
                                @endif
                            </div>
                            <h6 class="fw-semibold mb-1">{{ $classroom->walikelas->name }}</h6>
                            <p class="text-muted mb-2">{{ $classroom->walikelas->nip_nik ?? 'N/A' }}</p>
                            <p class="text-muted small mb-0">{{ $classroom->walikelas->email }}</p>
                        </div>
                    @else
                        <div class="text-center text-muted">
                            <i class="fas fa-user-slash fa-2x mb-3"></i>
                            <p>Belum ada walikelas</p>
                            <a href="{{ route('admin.classrooms.edit', $classroom) }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus me-2"></i>Tentukan Walikelas
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="detail-card">
                <div class="detail-header">
                    <h5 class="mb-0">
                        <i class="fas fa-bolt me-2"></i>Aksi Cepat
                    </h5>
                </div>
                <div class="detail-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.classrooms.edit', $classroom) }}" class="btn btn-primary">
                            <i class="fas fa-edit me-2"></i>Edit Kelas
                        </a>
                        <a href="{{ route('admin.students.index', ['class' => $classroom->name]) }}" class="btn btn-secondary">
                            <i class="fas fa-user-graduate me-2"></i>Lihat Semua Siswa
                        </a>
                        <a href="{{ route('admin.classrooms.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-list me-2"></i>Daftar Kelas
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
