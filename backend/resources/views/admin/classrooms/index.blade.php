@extends('admin.layouts.app')

@section('title', 'Manajemen Kelas')

@push('styles')
<style>
.stats-card {
    background: white;
    border-radius: 8px;
    padding: 1.5rem;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    border: 1px solid #e9ecef;
}

.stats-icon {
    width: 50px;
    height: 50px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    margin-bottom: 1rem;
}

.stats-number {
    font-size: 2rem;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 0.25rem;
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

.btn-info {
    background: #17a2b8;
    color: white;
    border-color: #17a2b8;
}

.btn-info:hover {
    background: #138496;
    border-color: #138496;
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

.action-btn {
    padding: 0.375rem 0.75rem;
    font-size: 0.875rem;
    border-radius: 4px;
    border: none;
    transition: all 0.2s ease;
}

.action-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
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

.classroom-name {
    font-weight: 600;
    color: #2c3e50;
    font-size: 1.1rem;
}

.classroom-level {
    background: #e3f2fd;
    color: #1976d2;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
}

.walikelas-info {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.walikelas-avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: #6c757d;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.875rem;
    font-weight: 600;
}

.student-count {
    background: #d4edda;
    color: #155724;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-size: 0.75rem;
    font-weight: 600;
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

.search-form {
    background: white;
    border-radius: 8px;
    padding: 1.5rem;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    border: 1px solid #e9ecef;
    margin-bottom: 1.5rem;
}

.form-control {
    border-radius: 6px;
    border: 1px solid #dee2e6;
    padding: 0.5rem 1rem;
    transition: all 0.2s ease;
}

.form-control:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
}

.main-card {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    border: 1px solid #e9ecef;
    overflow: hidden;
}

.card-header {
    background: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
    padding: 1.5rem;
}

.card-title {
    margin: 0;
    font-size: 1.25rem;
    font-weight: 600;
    color: #2c3e50;
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
            <h1 class="h3 mb-0">Manajemen Kelas</h1>
            <p class="text-muted mb-0">Kelola data kelas dan walikelas</p>
        </div>
        <a href="{{ route('admin.classrooms.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Tambah Kelas
        </a>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stats-card">
                <div class="stats-icon" style="background: #e3f2fd; color: #1976d2;">
                    <i class="fas fa-chalkboard"></i>
                </div>
                <div class="stats-number">{{ $classRooms->total() }}</div>
                <div class="stats-label">Total Kelas</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <div class="stats-icon" style="background: #e8f5e8; color: #28a745;">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stats-number">{{ $classRooms->where('is_active', true)->count() }}</div>
                <div class="stats-label">Kelas Aktif</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <div class="stats-icon" style="background: #fff3cd; color: #856404;">
                    <i class="fas fa-user-tie"></i>
                </div>
                <div class="stats-number">{{ $classRooms->where('walikelas_id', '!=', null)->count() }}</div>
                <div class="stats-label">Dengan Walikelas</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <div class="stats-icon" style="background: #f8d7da; color: #721c24;">
                    <i class="fas fa-times-circle"></i>
                </div>
                <div class="stats-number">{{ $classRooms->where('is_active', false)->count() }}</div>
                <div class="stats-label">Kelas Non-Aktif</div>
            </div>
        </div>
    </div>

    <!-- Search and Filter -->
    <div class="search-form">
        <form method="GET" action="{{ route('admin.classrooms.index') }}">
            <div class="row">
                <div class="col-md-4">
                    <label class="form-label">Cari Kelas</label>
                    <input type="text" name="search" class="form-control" placeholder="Nama kelas atau tingkat..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Tingkat</label>
                    <select name="level" class="form-control">
                        <option value="">Semua Tingkat</option>
                        <option value="7" {{ request('level') == '7' ? 'selected' : '' }}>Kelas 7</option>
                        <option value="8" {{ request('level') == '8' ? 'selected' : '' }}>Kelas 8</option>
                        <option value="9" {{ request('level') == '9' ? 'selected' : '' }}>Kelas 9</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-control">
                        <option value="">Semua Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Non-Aktif</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-fill">
                            <i class="fas fa-search"></i>
                        </button>
                        <a href="{{ route('admin.classrooms.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i>
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Main Content -->
    <div class="main-card">
        <div class="card-header">
            <h5 class="card-title">
                <i class="fas fa-list me-2"></i>Daftar Kelas
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Kelas</th>
                            <th>Tingkat</th>
                            <th>Walikelas</th>
                            <th>Jumlah Siswa</th>
                            <th>Status</th>
                            <th>Tanggal Dibuat</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($classRooms as $classroom)
                            <tr>
                                <td>
                                    <div class="classroom-name">{{ $classroom->name }}</div>
                                    @if($classroom->description)
                                        <small class="text-muted">{{ $classroom->description }}</small>
                                    @endif
                                </td>
                                <td>
                                    <span class="classroom-level">Kelas {{ $classroom->level }}</span>
                                </td>
                                <td>
                                    @if($classroom->walikelas)
                                        <div class="walikelas-info">
                                            <div class="walikelas-avatar">
                                                {{ substr($classroom->walikelas->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <div class="fw-semibold">{{ $classroom->walikelas->name }}</div>
                                                <small class="text-muted">{{ $classroom->walikelas->nip_nik ?? 'N/A' }}</small>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-muted">Belum ada walikelas</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="student-count">{{ $classroom->student_count ?? 0 }} Siswa</span>
                                </td>
                                <td>
                                    @if($classroom->is_active)
                                        <span class="status-badge status-active">Aktif</span>
                                    @else
                                        <span class="status-badge status-inactive">Non-Aktif</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="fw-medium">{{ $classroom->created_at->format('d M Y') }}</span>
                                    <br><small class="text-muted">{{ $classroom->created_at->format('H:i') }}</small>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-1">
                                        <a href="{{ route('admin.classrooms.show', $classroom) }}" 
                                           class="action-btn btn btn-info" 
                                           title="Lihat Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.classrooms.edit', $classroom) }}" 
                                           class="action-btn btn btn-warning" 
                                           title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" 
                                                class="action-btn btn btn-danger" 
                                                title="Hapus"
                                                onclick="confirmDelete({{ $classroom->id }}, '{{ $classroom->name }}')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">
                                    <div class="empty-state">
                                        <i class="fas fa-chalkboard"></i>
                                        <h5>Belum ada kelas</h5>
                                        <p>Mulai dengan membuat kelas pertama Anda</p>
                                        <a href="{{ route('admin.classrooms.create') }}" class="btn btn-primary">
                                            <i class="fas fa-plus me-2"></i>Tambah Kelas
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    @if($classRooms->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $classRooms->links() }}
        </div>
    @endif
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus kelas <strong id="className"></strong>?</p>
                <p class="text-danger small">Tindakan ini tidak dapat dibatalkan.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function confirmDelete(id, name) {
    document.getElementById('className').textContent = name;
    document.getElementById('deleteForm').action = `/admin/classrooms/${id}`;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}
</script>
@endpush
