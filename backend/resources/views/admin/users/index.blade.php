@extends('admin.layouts.app')

@section('title', 'Manajemen Pengguna')

@push('styles')
<style>
.user-card {
    background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
    border: 1px solid #e2e8f0;
    border-radius: 1rem;
    transition: all 0.3s ease;
    overflow: hidden;
}

.user-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px -3px rgba(0, 0, 0, 0.1);
    border-color: #3b82f6;
}

.user-avatar {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.5rem;
    font-weight: bold;
    margin-right: 1rem;
}

.role-badge {
    padding: 0.375rem 0.75rem;
    border-radius: 0.5rem;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
}

.role-admin {
    background-color: #fecaca;
    color: #991b1b;
}

.role-teacher {
    background-color: #bfdbfe;
    color: #1e40af;
}

.role-employee {
    background-color: #d1fae5;
    color: #065f46;
}

.stats-card {
    background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
    border: none;
    border-radius: 1rem;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
    height: 100%;
}

.stats-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px -3px rgba(0, 0, 0, 0.1);
}

.stats-icon {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    margin-bottom: 1rem;
}

.stats-value {
    font-size: 2rem;
    font-weight: bold;
    margin-bottom: 0.5rem;
}

.stats-label {
    color: #64748b;
    font-size: 0.875rem;
    font-weight: 500;
}

.filter-card {
    background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
    border: none;
    border-radius: 1rem;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
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

.table {
    margin-bottom: 0;
}

.table thead th {
    background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
    border: none;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.75rem;
    letter-spacing: 0.5px;
    color: #475569;
    padding: 1rem;
}

.table tbody td {
    padding: 1rem;
    vertical-align: middle;
    border-color: #f1f5f9;
}

.table tbody tr {
    transition: all 0.3s ease;
}

.table tbody tr:hover {
    background-color: #f8fafc;
    transform: translateX(2px);
}

.action-btn {
    padding: 0.375rem 0.75rem;
    border-radius: 0.375rem;
    font-size: 0.75rem;
    font-weight: 500;
    transition: all 0.3s ease;
    border: none;
    margin: 0 0.125rem;
}

.action-btn:hover {
    transform: translateY(-1px);
}

.search-highlight {
    background-color: #fef3c7;
    padding: 0.1rem 0.2rem;
    border-radius: 0.25rem;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in {
    animation: fadeInUp 0.6s ease-out;
}

.animate-delay-1 { animation-delay: 0.1s; }
.animate-delay-2 { animation-delay: 0.2s; }
.animate-delay-3 { animation-delay: 0.3s; }
.animate-delay-4 { animation-delay: 0.4s; }

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
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-1 text-gray-800">👥 Manajemen Pengguna</h1>
                    <p class="text-muted mb-0">Kelola pengguna sistem, role, dan hak akses</p>
                </div>
                <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-modern">
                    <i class="fas fa-plus me-2"></i>Tambah Pengguna Baru
                </a>
            </div>
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
            <div class="stats-card card animate-fade-in">
                <div class="card-body text-center">
                    <div class="stats-icon bg-primary bg-gradient text-white mx-auto">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stats-value text-primary">{{ $totalUsers }}</div>
                    <div class="stats-label">Total Pengguna</div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="stats-card card animate-fade-in animate-delay-1">
                <div class="card-body text-center">
                    <div class="stats-icon bg-danger bg-gradient text-white mx-auto">
                        <i class="fas fa-user-shield"></i>
                    </div>
                    <div class="stats-value text-danger">{{ $adminCount }}</div>
                    <div class="stats-label">Administrator</div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="stats-card card animate-fade-in animate-delay-2">
                <div class="card-body text-center">
                    <div class="stats-icon bg-info bg-gradient text-white mx-auto">
                        <i class="fas fa-chalkboard-teacher"></i>
                    </div>
                    <div class="stats-value text-info">{{ $teacherCount }}</div>
                    <div class="stats-label">Guru</div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="stats-card card animate-fade-in animate-delay-3">
                <div class="card-body text-center">
                    <div class="stats-icon bg-success bg-gradient text-white mx-auto">
                        <i class="fas fa-user-tie"></i>
                    </div>
                    <div class="stats-value text-success">{{ $employeeCount }}</div>
                    <div class="stats-label">Pegawai</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter and Search Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="filter-card card animate-fade-in animate-delay-4">
                <div class="card-body">
                    <h5 class="card-title mb-3">
                        <i class="fas fa-filter text-primary me-2"></i>Filter & Pencarian Pengguna
                    </h5>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="roleFilter" class="form-label fw-semibold">Filter Role</label>
                            <select class="form-select" id="roleFilter">
                                <option value="">Semua Role</option>
                                <option value="admin">Administrator</option>
                                <option value="teacher">Guru</option>
                                <option value="employee">Pegawai</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="statusFilter" class="form-label fw-semibold">Filter Status</label>
                            <select class="form-select" id="statusFilter">
                                <option value="">Semua Status</option>
                                <option value="active">Aktif</option>
                                <option value="inactive">Tidak Aktif</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="searchUser" class="form-label fw-semibold">Pencarian</label>
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
        </div>
    </div>

    <!-- Users Table -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-list me-2 text-primary"></i>Daftar Pengguna
                        </h5>
                        <div class="d-flex gap-2">
                            <button class="btn btn-outline-success btn-sm" onclick="exportUsers()">
                                <i class="fas fa-file-excel me-1"></i>Export Excel
                            </button>
                            <button class="btn btn-outline-info btn-sm" onclick="refreshUsers()">
                                <i class="fas fa-sync-alt me-1"></i>Refresh
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" id="usersTable">
                            <thead>
                                <tr>
                                    <th>Pengguna</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Tanggal Dibuat</th>
                                    <th>Status</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($users as $user)
                                    <tr data-role="{{ strtolower($user->role->role_name ?? '') }}" data-email="{{ $user->email }}" data-name="{{ strtolower($user->name) }}">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="user-avatar">
                                                    {{ substr($user->name, 0, 1) }}
                                                </div>
                                                <div>
                                                    <div class="fw-semibold">{{ $user->name }}</div>
                                                    <small class="text-muted">ID: {{ $user->id }}</small>
                                                </div>
                                            </div>
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
                                            <span class="fw-medium">{{ $user->created_at->format('d M Y') }}</span>
                                            <br><small class="text-muted">{{ $user->created_at->format('H:i') }}</small>
                                        </td>
                                        <td>
                                            <span class="badge bg-success">Aktif</span>
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
                    <div class="card-footer bg-light">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="text-muted">
                                Menampilkan {{ $users->firstItem() }} - {{ $users->lastItem() }} dari {{ $users->total() }} pengguna
                            </div>
                            {{ $users->links() }}
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
                <h5 class="modal-title text-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>Konfirmasi Hapus
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