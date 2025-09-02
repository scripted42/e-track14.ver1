@extends('admin.layouts.app')

@section('title', 'Manajemen Kehadiran')

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

.status-badge {
    padding: 0.375rem 0.75rem;
    border-radius: 4px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
}

.status-hadir {
    background: #d4edda;
    color: #155724;
}

.status-terlambat {
    background: #fff3cd;
    color: #856404;
}

.status-izin {
    background: #cce5ff;
    color: #004085;
}

.status-sakit {
    background: #f8d7da;
    color: #721c24;
}

.status-alpha {
    background: #f5c6cb;
    color: #721c24;
}

.status-cuti {
    background: #e2e3e5;
    color: #383d41;
}

.status-dinas_luar {
    background: #d1ecf1;
    color: #0c5460;
}

.type-badge {
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
}

.type-checkin {
    background: #d4edda;
    color: #155724;
}

.type-checkout {
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
            <h1 class="h3 mb-0">Manajemen Kehadiran</h1>
            <p class="text-muted mb-0">Kelola data kehadiran pegawai dan guru</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.attendance.qr') }}" class="btn btn-info">
                <i class="fas fa-qrcode me-2"></i>QR Code
            </a>
            <a href="{{ route('admin.attendance.export', request()->query()) }}" class="btn btn-success">
                <i class="fas fa-download me-2"></i>Export Data
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="stats-card">
                <div class="stats-icon" style="background: #e8f5e8; color: #28a745;">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stats-number">{{ $stats['today_total'] }}</div>
                <div class="stats-label">Hadir Hari Ini</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stats-card">
                <div class="stats-icon" style="background: #fff3cd; color: #856404;">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stats-number">{{ $stats['today_late'] }}</div>
                <div class="stats-label">Terlambat Hari Ini</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stats-card">
                <div class="stats-icon" style="background: #e3f2fd; color: #1976d2;">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <div class="stats-number">{{ $stats['this_month'] }}</div>
                <div class="stats-label">Total Bulan Ini</div>
            </div>
        </div>
    </div>

    <!-- Search and Filter -->
    <div class="search-form">
        <form method="GET" action="{{ route('admin.attendance.index') }}">
            <div class="row">
                <div class="col-md-3">
                    <label class="form-label">Pegawai/Guru</label>
                    <select name="user_id" class="form-control">
                        <option value="">Semua Pegawai</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-control">
                        <option value="">Semua Status</option>
                        @foreach($statuses as $status)
                            <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                                {{ ucfirst($status) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Jenis</label>
                    <select name="type" class="form-control">
                        <option value="">Semua Jenis</option>
                        @foreach($types as $type)
                            <option value="{{ $type }}" {{ request('type') == $type ? 'selected' : '' }}>
                                {{ ucfirst($type) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Dari Tanggal</label>
                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Sampai Tanggal</label>
                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                </div>
                <div class="col-md-1">
                    <label class="form-label">&nbsp;</label>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-fill">
                            <i class="fas fa-search"></i>
                        </button>
                        <a href="{{ route('admin.attendance.index') }}" class="btn btn-secondary">
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
                <i class="fas fa-list me-2"></i>Daftar Kehadiran
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Role</th>
                            <th>Jenis</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                            <th>Waktu</th>
                            <th>Lokasi</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($attendance as $record)
                            <tr>
                                <td>
                                    <div class="fw-semibold">{{ $record->user->name }}</div>
                                    <small class="text-muted">{{ $record->user->email }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ $record->user->role ? $record->user->role->role_name : 'No Role' }}</span>
                                </td>
                                <td>
                                    <span class="type-badge type-{{ $record->type }}">
                                        {{ ucfirst($record->type) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="status-badge status-{{ $record->status }}">
                                        {{ ucfirst($record->status) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="fw-medium">{{ $record->timestamp->format('d M Y') }}</span>
                                </td>
                                <td>
                                    <span class="fw-medium">{{ $record->timestamp->format('H:i:s') }}</span>
                                </td>
                                <td>
                                    @if($record->latitude && $record->longitude)
                                        <small class="text-muted">
                                            {{ number_format($record->latitude, 6) }}, {{ number_format($record->longitude, 6) }}
                                        </small>
                                        @if($record->accuracy)
                                            <br><small class="text-muted">±{{ $record->accuracy }}m</small>
                                        @endif
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-1">
                                        <button type="button" 
                                                class="action-btn btn btn-info" 
                                                title="Lihat Detail"
                                                onclick="showDetail({{ $record->id }})">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">
                                    <div class="empty-state">
                                        <i class="fas fa-clipboard-list"></i>
                                        <h5>Belum ada data kehadiran</h5>
                                        <p>Data kehadiran akan muncul setelah pegawai melakukan absensi</p>
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
    @if($attendance->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $attendance->links() }}
        </div>
    @endif
</div>

<!-- Detail Modal -->
<div class="modal fade" id="detailModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Kehadiran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="detailContent">
                <!-- Content will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function showDetail(id) {
    // For now, just show a placeholder
    // In a real implementation, you would fetch the detail via AJAX
    document.getElementById('detailContent').innerHTML = `
        <div class="text-center p-4">
            <i class="fas fa-info-circle fa-3x text-primary mb-3"></i>
            <h6>Detail Kehadiran #${id}</h6>
            <p class="text-muted">Fitur detail akan segera tersedia</p>
        </div>
    `;
    new bootstrap.Modal(document.getElementById('detailModal')).show();
}
</script>
@endpush
