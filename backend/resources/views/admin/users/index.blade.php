@extends('admin.layouts.app')

@section('title', 'Manajemen Staff')

@push('styles')
<style>
.user-avatar {
    width: 60px;
    height: 60px;
    border-radius: 8px;
    background: #f8f9fa;
    border: 1px solid #e9ecef;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6c757d;
    font-size: 1.5rem;
    font-weight: bold;
    margin-right: 1rem;
}

.role-badge {
    padding: 0.375rem 0.75rem;
    border-radius: 6px;
    font-size: 0.75rem;
    font-weight: 500;
    border: 1px solid #dee2e6;
}

.role-admin {
    background: #f8d7da;
    color: #721c24;
    border-color: #f5c6cb;
}

.role-teacher {
    background: #d1ecf1;
    color: #0c5460;
    border-color: #bee5eb;
}

.role-employee {
    background: #d4edda;
    color: #155724;
    border-color: #c3e6cb;
}

.stats-card {
    background: white;
    border: 1px solid #e9ecef;
    border-radius: 15px;
    padding: 1.5rem;
    text-align: center;
    transition: all 0.2s ease;
    height: 100%;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
}

.stats-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 25px rgba(0,0,0,0.1);
}

.stats-icon {
    width: 50px;
    height: 50px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1rem;
    font-size: 1.25rem;
    color: white;
}

.stats-value {
    font-size: 2rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
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

.table {
    margin-bottom: 0;
}

.table thead th {
    background: #f8f9fa;
    border: none;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.8rem;
    letter-spacing: 0.5px;
    color: #2c3e50;
    padding: 1rem;
    border-bottom: 1px solid #e9ecef;
}

.table tbody td {
    padding: 1rem;
    vertical-align: middle;
    border-color: #f8f9fa;
}

.table tbody tr {
    transition: all 0.2s ease;
}

.table tbody tr:hover {
    background-color: #f8f9fa;
}

.action-btn {
    padding: 0.375rem 0.75rem;
    border-radius: 6px;
    font-size: 0.75rem;
    font-weight: 500;
    transition: all 0.2s ease;
    border: 1px solid #dee2e6;
    margin: 0 0.125rem;
}

.action-btn:hover {
    transform: translateY(-1px);
}

.search-highlight {
    background-color: #fff3cd;
    padding: 0.1rem 0.2rem;
    border-radius: 0.25rem;
}

/* Responsive design */
@media (max-width: 768px) {
    .user-avatar {
        width: 50px;
        height: 50px;
        font-size: 1.2rem;
        margin-right: 0.75rem;
    }
    
    .stats-card {
        margin-bottom: 1rem;
    }
    
    .table-responsive {
        border-radius: 0.5rem;
    }
    
    .action-btn {
        margin-bottom: 0.25rem;
        display: block;
        width: 100%;
    }
}
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Manajemen Staff</h1>
            <p class="text-muted mb-0">Kelola staff sekolah, role, dan hak akses</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.users.import') }}" class="btn btn-success">
                <i class="fas fa-upload me-2"></i>Import Staff
            </a>
            <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Tambah Staff Baru
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        @php
            $totalUsers = $users->total();
            $adminCount = $users->where('role.role_name', 'admin')->count();
            $teacherCount = $users->where('role.role_name', 'teacher')->count();
            $employeeCount = $users->where('role.role_name', 'employee')->count();
        @endphp
        
        <div class="col-md-3 mb-3">
            <div class="stats-card">
                <div class="stats-icon" style="background: #007bff;">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stats-value" style="color: #007bff;">{{ $totalUsers }}</div>
                <div class="stats-label">Total Pengguna</div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="stats-card">
                <div class="stats-icon" style="background: #dc3545;">
                    <i class="fas fa-user-shield"></i>
                </div>
                <div class="stats-value" style="color: #dc3545;">{{ $adminCount }}</div>
                <div class="stats-label">Administrator</div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="stats-card">
                <div class="stats-icon" style="background: #17a2b8;">
                    <i class="fas fa-chalkboard-teacher"></i>
                </div>
                <div class="stats-value" style="color: #17a2b8;">{{ $teacherCount }}</div>
                <div class="stats-label">Guru</div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="stats-card">
                <div class="stats-icon" style="background: #28a745;">
                    <i class="fas fa-user-tie"></i>
                </div>
                <div class="stats-value" style="color: #28a745;">{{ $employeeCount }}</div>
                <div class="stats-label">Pegawai</div>
            </div>
        </div>
    </div>

    <!-- Filter and Search Section -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="fas fa-search me-2"></i>Filter & Pencarian Pengguna
            </h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label for="roleFilter" class="form-label">Filter Role</label>
                    <select class="form-select" id="roleFilter">
                        <option value="">Semua Role</option>
                        <option value="admin">Administrator</option>
                        <option value="teacher">Guru</option>
                        <option value="employee">Pegawai</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="statusFilter" class="form-label">Filter Status</label>
                    <select class="form-select" id="statusFilter">
                        <option value="">Semua Status</option>
                        <option value="active">Aktif</option>
                        <option value="inactive">Tidak Aktif</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="searchUser" class="form-label">Pencarian</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="searchUser" placeholder="Cari nama atau email...">
                        <button class="btn btn-outline-secondary" type="button" onclick="clearSearch()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Users Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-list me-2"></i>Daftar Pengguna
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" id="usersTable">
                            <thead>
                                <tr>
                                    <th>Pengguna</th>
                                    <th>NIP/NIK</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Status</th>
                                    <th>Tanggal Dibuat</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($users as $user)
                                    <tr data-role="{{ strtolower($user->role->role_name ?? '') }}" data-email="{{ $user->email }}" data-name="{{ strtolower($user->name) }}">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="user-avatar">
                                                    @if($user->photo && file_exists(public_path('storage/user-photos/' . $user->photo)))
                                                        <img src="{{ asset('storage/user-photos/' . $user->photo) }}" alt="Foto" class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;">
                                                    @else
                                                        {{ substr($user->name, 0, 1) }}
                                                    @endif
                                                </div>
                                                <div>
                                                    <div class="fw-semibold">{{ $user->name }}</div>
                                                    <small class="text-muted">ID: {{ $user->id }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="fw-medium">{{ $user->nip_nik ?? '-' }}</span>
                                        </td>
                                        <td>
                                            <span class="fw-medium">{{ $user->email }}</span>
                                        </td>
                                        <td>
                                            @php
                                                $roleClass = '';
                                                switch(strtolower($user->role->role_name ?? '')) {
                                                    case 'admin':
                                                        $roleClass = 'role-admin';
                                                        break;
                                                    case 'teacher':
                                                        $roleClass = 'role-teacher';
                                                        break;
                                                    case 'employee':
                                                        $roleClass = 'role-employee';
                                                        break;
                                                    default:
                                                        $roleClass = 'bg-secondary text-white';
                                                }
                                            @endphp
                                            <span class="role-badge {{ $roleClass }}">
                                                {{ ucfirst($user->role->role_name ?? 'Unknown') }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($user->status == 'Aktif')
                                                <span class="badge bg-success">Aktif</span>
                                            @else
                                                <span class="badge bg-danger">Non-Aktif</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="fw-medium">{{ $user->created_at->format('d M Y') }}</span>
                                            <br><small class="text-muted">{{ $user->created_at->format('H:i') }}</small>
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-1">
                                                <a href="{{ route('admin.users.show', $user) }}" 
                                                   class="action-btn btn btn-info" 
                                                   title="Lihat Detail">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.users.edit', $user) }}" 
                                                   class="action-btn btn btn-warning" 
                                                   title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                @if($user->id !== auth()->id())
                                                    <button type="button" 
                                                            class="action-btn btn btn-danger delete-user-btn" 
                                                            title="Hapus"
                                                            data-user-id="{{ $user->id }}"
                                                            data-user-name="{{ $user->name }}">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">
                                            <i class="fas fa-user-times fa-3x mb-3"></i>
                                            <br>Belum ada pengguna yang terdaftar
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($users->hasPages())
                    <div class="card-footer">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="text-muted">
                                Menampilkan {{ $users->firstItem() }} - {{ $users->lastItem() }} dari {{ $users->total() }} pengguna
                            </div>
                            {{ $users->links('pagination::bootstrap-4') }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle text-danger me-2"></i>Konfirmasi Hapus
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus pengguna <strong id="userName"></strong>?</p>
                <p class="text-muted small">Tindakan ini tidak dapat dibatalkan.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form id="deleteForm" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Hapus Pengguna</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Search functionality
document.getElementById('searchUser').addEventListener('input', function() {
    const searchTerm = this.value.toLowerCase();
    filterUsers();
});

// Role filter
document.getElementById('roleFilter').addEventListener('change', function() {
    filterUsers();
});

// Status filter
document.getElementById('statusFilter').addEventListener('change', function() {
    filterUsers();
});

function filterUsers() {
    const searchTerm = document.getElementById('searchUser').value.toLowerCase();
    const roleFilter = document.getElementById('roleFilter').value.toLowerCase();
    const statusFilter = document.getElementById('statusFilter').value.toLowerCase();
    
    const rows = document.querySelectorAll('#usersTable tbody tr');
    
    rows.forEach(row => {
        if (row.cells.length === 1) return; // Skip empty state row
        
        const name = row.dataset.name || '';
        const email = row.dataset.email || '';
        const role = row.dataset.role || '';
        
        const matchesSearch = name.includes(searchTerm) || email.toLowerCase().includes(searchTerm);
        const matchesRole = !roleFilter || role === roleFilter;
        const matchesStatus = true; // All users are active for now
        
        if (matchesSearch && matchesRole && matchesStatus) {
            row.style.display = '';
            highlightSearchTerm(row, searchTerm);
        } else {
            row.style.display = 'none';
        }
    });
}

function highlightSearchTerm(row, term) {
    if (!term) return;
    
    const cells = row.querySelectorAll('td');
    cells.forEach(cell => {
        const originalText = cell.textContent;
        if (originalText.toLowerCase().includes(term)) {
            const regex = new RegExp(`(${term})`, 'gi');
            cell.innerHTML = cell.innerHTML.replace(regex, '<span class="search-highlight">$1</span>');
        }
    });
}

function clearSearch() {
    document.getElementById('searchUser').value = '';
    document.getElementById('roleFilter').value = '';
    document.getElementById('statusFilter').value = '';
    filterUsers();
}

function deleteUser(userId, userName) {
    document.getElementById('userName').textContent = userName;
    document.getElementById('deleteForm').action = `/admin/users/${userId}`;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}

function exportUsers() {
    alert('Fitur export sedang dalam pengembangan');
}

function refreshUsers() {
    window.location.reload();
}

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    // Add delete button event listeners
    document.querySelectorAll('.delete-user-btn').forEach(button => {
        button.addEventListener('click', function() {
            const userId = this.getAttribute('data-user-id');
            const userName = this.getAttribute('data-user-name');
            deleteUser(userId, userName);
        });
    });
    
    // Add tooltips
    const tooltips = document.querySelectorAll('[title]');
    tooltips.forEach(el => {
        new bootstrap.Tooltip(el);
    });
    
    // Auto-dismiss alerts
    setTimeout(() => {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);
});
</script>
@endpush