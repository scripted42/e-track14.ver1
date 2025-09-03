@extends('admin.layouts.app')

@section('title', 'Manajemen Kehadiran')

@push('styles')
<style>
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

/* Check-out specific status styles */
.status-pulang-lebih-awal {
    background: #fff3cd;
    color: #856404;
}

.status-lembur {
    background: #f8d7da;
    color: #721c24;
}

.status-on-time {
    background: #d4edda;
    color: #155724;
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
            <a href="{{ route('admin.attendance.export', request()->query()) }}" class="btn btn-success">
                <i class="fas fa-download me-2"></i>Export Data
            </a>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $stats['present_today'] }}</h4>
                            <p class="mb-0">Hadir Hari Ini</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-users fa-2x opacity-75"></i>
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
                            <h4 class="mb-0">{{ $stats['ontime_today'] }}</h4>
                            <p class="mb-0">On-Time Hari Ini</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-clock fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $stats['late_today'] }}</h4>
                            <p class="mb-0">Terlambat Hari Ini</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-exclamation-triangle fa-2x opacity-75"></i>
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
                            <h4 class="mb-0">{{ $stats['not_attended_today'] }}</h4>
                            <p class="mb-0">Belum Absen Hari Ini</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-user-times fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filter -->
    <div class="search-form">
        <form method="GET" action="{{ route('admin.attendance.index') }}">
            <!-- First Row -->
            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label">Cari Nama Pegawai</label>
                    <input type="text" name="search" class="form-control" placeholder="Masukkan nama pegawai..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Periode</label>
                    <select name="period" class="form-control">
                        <option value="">Pilih Periode</option>
                        <option value="today" {{ request('period') == 'today' ? 'selected' : '' }}>Hari Ini</option>
                        <option value="yesterday" {{ request('period') == 'yesterday' ? 'selected' : '' }}>Kemarin</option>
                        <option value="this_week" {{ request('period') == 'this_week' ? 'selected' : '' }}>Minggu Ini</option>
                        <option value="this_month" {{ request('period') == 'this_month' ? 'selected' : '' }}>Bulan Ini</option>
                        <option value="custom" {{ request('period') == 'custom' ? 'selected' : '' }}>Tanggal Kustom</option>
                    </select>
                </div>
                <div class="col-md-3" id="custom-date" style="{{ request('period') == 'custom' ? '' : 'display: none;' }}">
                    <label class="form-label">Tanggal</label>
                    <input type="date" name="date" class="form-control" value="{{ request('date') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-fill">
                            <i class="fas fa-search me-1"></i>Cari
                        </button>
                        <a href="{{ route('admin.attendance.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times me-1"></i>Reset
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Second Row -->
            <div class="row">
                <div class="col-md-6">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-control">
                        <option value="">Semua Status</option>
                        <option value="on-time" {{ request('status') == 'on-time' ? 'selected' : '' }}>On-time</option>
                        <option value="terlambat" {{ request('status') == 'terlambat' ? 'selected' : '' }}>Terlambat</option>
                        <option value="izin" {{ request('status') == 'izin' ? 'selected' : '' }}>Izin</option>
                        <option value="sakit" {{ request('status') == 'sakit' ? 'selected' : '' }}>Sakit</option>
                        <option value="cuti" {{ request('status') == 'cuti' ? 'selected' : '' }}>Cuti</option>
                        <option value="dinas_luar" {{ request('status') == 'dinas_luar' ? 'selected' : '' }}>Dinas Luar</option>
                        <option value="alpha" {{ request('status') == 'alpha' ? 'selected' : '' }}>Alpha</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Jenis</label>
                    <select name="type" class="form-control">
                        <option value="">Semua Jenis</option>
                        <option value="checkin" {{ request('type') == 'checkin' ? 'selected' : '' }}>Check-in</option>
                        <option value="checkout" {{ request('type') == 'checkout' ? 'selected' : '' }}>Check-out</option>
                    </select>
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
                            <th>Tanggal</th>
                            <th>Check-in</th>
                            <th>Check-out</th>
                            <th>Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($attendance as $record)
                            <tr>
                                <td>
                                    <div class="fw-semibold">{{ $record['user']->name }}</div>
                                    <small class="text-muted">{{ $record['user']->email }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ $record['user']->role ? $record['user']->role->role_name : 'No Role' }}</span>
                                </td>
                                <td>
                                    <span class="fw-medium">{{ \Carbon\Carbon::parse($record['date'])->format('d M Y') }}</span>
                                </td>
                                <td>
                                    @if($record['checkin'])
                                        <div>
                                            <span class="fw-medium text-success">{{ $record['checkin']['time'] }}</span>
                                            <br>
                                            <small class="text-muted">
                                                <i class="fas fa-map-marker-alt"></i> Lokasi tercatat
                                            </small>
                                        </div>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($record['checkout'])
                                        <div>
                                            <span class="fw-medium text-danger">{{ $record['checkout']['time'] }}</span>
                                            <br>
                                            <small class="text-muted">
                                                <i class="fas fa-map-marker-alt"></i> Lokasi tercatat
                                            </small>
                                        </div>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex flex-column gap-1">
                                        @if(isset($record['leave']))
                                            <span class="status-badge status-{{ $record['leave']['type'] }}">
                                                {{ ucfirst($record['leave']['type']) }}
                                            </span>
                                            @if($record['leave']['status'] === 'disetujui')
                                                <small class="text-success">✓ Disetujui</small>
                                            @elseif($record['leave']['status'] === 'ditolak')
                                                <small class="text-danger">✗ Ditolak</small>
                                            @else
                                                <small class="text-warning">⏳ Menunggu</small>
                                            @endif
                                        @elseif($record['checkin'])
                                            <span class="status-badge status-{{ strtolower(str_replace(' ', '-', $record['checkin']['status'])) }}">
                                                Check-in: {{ $record['checkin']['status'] }}
                                            </span>
                                        @endif
                                        @if($record['checkout'])
                                            <span class="status-badge status-{{ strtolower(str_replace(' ', '-', $record['checkout']['status'])) }}">
                                                Check-out: {{ $record['checkout']['status'] }}
                                            </span>
                                        @endif
                                        @if(!$record['checkin'] && !$record['checkout'] && !isset($record['leave']))
                                            <span class="status-badge status-alpha">
                                                Alpha
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-1">
                                        <button type="button" 
                                                class="action-btn btn btn-info" 
                                                title="Lihat Detail"
                                                onclick="showDetail('{{ $record['user']->id }}', '{{ $record['date'] }}')">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        @if(isset($record['leave']) && $record['leave']['status'] === 'menunggu' && auth()->user()->hasRole(['Admin', 'Kepala Sekolah', 'Waka Kurikulum']))
                                            <button type="button" 
                                                    class="action-btn btn btn-success" 
                                                    title="Setujui Izin"
                                                    onclick="approveLeave('{{ $record['leave']['id'] ?? '' }}', '{{ $record['user']->name }}')">
                                                <i class="fas fa-check"></i>
                                            </button>
                                            <button type="button" 
                                                    class="action-btn btn btn-danger" 
                                                    title="Tolak Izin"
                                                    onclick="rejectLeave('{{ $record['leave']['id'] ?? '' }}', '{{ $record['user']->name }}')">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">
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
            {{ $attendance->links('pagination::bootstrap-4') }}
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
// Show/hide custom date field based on period selection
document.addEventListener('DOMContentLoaded', function() {
    const periodSelect = document.querySelector('select[name="period"]');
    const customDateDiv = document.getElementById('custom-date');
    
    if (periodSelect && customDateDiv) {
        periodSelect.addEventListener('change', function() {
            if (this.value === 'custom') {
                customDateDiv.style.display = '';
            } else {
                customDateDiv.style.display = 'none';
            }
        });
    }
});

function showDetail(userId, date) {
    // Show loading state
    document.getElementById('detailContent').innerHTML = `
        <div class="text-center p-4">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2">Memuat detail kehadiran...</p>
        </div>
    `;
    
    // Show modal
    new bootstrap.Modal(document.getElementById('detailModal')).show();
    
    // Fetch detail data
    fetch(`/admin/attendance/detail/${userId}/${date}`)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                document.getElementById('detailContent').innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle"></i> ${data.error}
                    </div>
                `;
                return;
            }
            
            // Build detail content
            let content = `
                <div class="row">
                    <div class="col-md-6">
                        <h6><i class="fas fa-user me-2"></i>Informasi Pegawai</h6>
                        <table class="table table-sm">
                            <tr><td><strong>Nama:</strong></td><td>${data.user.name}</td></tr>
                            <tr><td><strong>Email:</strong></td><td>${data.user.email}</td></tr>
                            <tr><td><strong>Role:</strong></td><td>${data.user.role ? data.user.role.role_name : 'No Role'}</td></tr>
                            <tr><td><strong>Tanggal:</strong></td><td>${new Date(data.date).toLocaleDateString('id-ID')}</td></tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6><i class="fas fa-calendar-check me-2"></i>Status Kehadiran</h6>
                        <div class="d-flex flex-column gap-2">
            `;
            
            if (data.leave) {
                content += `
                    <div class="alert alert-info">
                        <strong>${data.leave.type.charAt(0).toUpperCase() + data.leave.type.slice(1)}</strong><br>
                        <small>Status: ${data.leave.status}</small><br>
                        <small>Alasan: ${data.leave.reason}</small>
                        ${data.leave.approved_by ? `<br><small>Disetujui oleh: ${data.leave.approved_by}</small>` : ''}
                    </div>
                `;
            } else if (data.checkin || data.checkout) {
                if (data.checkin) {
                    content += `
                        <div class="alert alert-success">
                            <strong>Check-in:</strong> ${data.checkin.time}<br>
                            <small>Status: ${data.checkin.status}</small>
                        </div>
                    `;
                }
                if (data.checkout) {
                    content += `
                        <div class="alert alert-danger">
                            <strong>Check-out:</strong> ${data.checkout.time}<br>
                            <small>Status: ${data.checkout.status}</small>
                        </div>
                    `;
                }
            } else {
                content += `
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i> Tidak ada data kehadiran
                    </div>
                `;
            }
            
            content += `
                        </div>
                    </div>
                </div>
            `;
            
            // Add location info if available
            if (data.checkin && data.checkin.latitude) {
                content += `
                    <div class="row mt-3">
                        <div class="col-12">
                            <h6><i class="fas fa-map-marker-alt me-2"></i>Informasi Lokasi</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Check-in:</strong><br>
                                    <small>Koordinat: ${data.checkin.latitude}, ${data.checkin.longitude}</small><br>
                                    <small>Akurasi: ${data.checkin.accuracy}m</small>
                                </div>
                `;
                if (data.checkout && data.checkout.latitude) {
                    content += `
                                <div class="col-md-6">
                                    <strong>Check-out:</strong><br>
                                    <small>Koordinat: ${data.checkout.latitude}, ${data.checkout.longitude}</small><br>
                                    <small>Akurasi: ${data.checkout.accuracy}m</small>
                                </div>
                    `;
                }
                content += `
                            </div>
                        </div>
                    </div>
                `;
            }
            
            document.getElementById('detailContent').innerHTML = content;
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('detailContent').innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i> Terjadi kesalahan saat memuat data
                </div>
            `;
        });
}

function approveLeave(leaveId, userName) {
    if (!leaveId) {
        alert('Leave ID tidak ditemukan');
        return;
    }
    
    if (confirm(`Apakah Anda yakin ingin menyetujui izin untuk ${userName}?`)) {
        fetch(`/admin/attendance/leave/${leaveId}/approve`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Izin berhasil disetujui');
                location.reload(); // Refresh page to show updated status
            } else {
                alert('Error: ' + (data.error || 'Terjadi kesalahan'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat menyetujui izin');
        });
    }
}

function rejectLeave(leaveId, userName) {
    if (!leaveId) {
        alert('Leave ID tidak ditemukan');
        return;
    }
    
    if (confirm(`Apakah Anda yakin ingin menolak izin untuk ${userName}?`)) {
        fetch(`/admin/attendance/leave/${leaveId}/reject`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Izin berhasil ditolak');
                location.reload(); // Refresh page to show updated status
            } else {
                alert('Error: ' + (data.error || 'Terjadi kesalahan'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat menolak izin');
        });
    }
}
</script>
@endpush
