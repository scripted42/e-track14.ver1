@extends('admin.layouts.app')

@section('title', 'Manajemen Staff')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Manajemen Staff</h1>
            <p class="text-muted mb-0">Kelola staff sekolah, role, dan hak akses</p>
        </div>
        <div>
            <a href="{{ route('admin.users.import') }}" class="btn btn-success me-2">
                <i class="fas fa-upload me-2"></i>Import Staff
            </a>
            <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Tambah Staff Baru
            </a>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <!-- First Row -->
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $stats['total_users'] }}</h4>
                            <p class="mb-0">Total Pengguna</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-users fa-2x opacity-75"></i>
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
                            <h4 class="mb-0">{{ $stats['admin_count'] }}</h4>
                            <p class="mb-0">Administrator</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-user-shield fa-2x opacity-75"></i>
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
                            <h4 class="mb-0">{{ $stats['kepala_sekolah_count'] }}</h4>
                            <p class="mb-0">Kepala Sekolah</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-user-graduate fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card bg-secondary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $stats['waka_kurikulum_count'] }}</h4>
                            <p class="mb-0">Waka Kurikulum</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-user-cog fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Second Row -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $stats['teacher_count'] }}</h4>
                            <p class="mb-0">Guru</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-chalkboard-teacher fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $stats['employee_count'] }}</h4>
                            <p class="mb-0">Pegawai</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-user-tie fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filter Form -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="fas fa-search me-2"></i>Pencarian & Filter
            </h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.users.index') }}" class="row g-3">
                <div class="col-md-4">
                    <label for="search" class="form-label">Pencarian</label>
                    <input type="text" 
                           class="form-control" 
                           id="search" 
                           name="search" 
                           value="{{ request('search') }}" 
                           placeholder="Cari nama atau email...">
                </div>
                
                <div class="col-md-3">
                    <label for="role_filter" class="form-label">Role</label>
                    <select class="form-select" id="role_filter" name="role">
                        <option value="">Semua Role</option>
                        <option value="Admin" {{ request('role') == 'Admin' ? 'selected' : '' }}>Administrator</option>
                        <option value="Kepala Sekolah" {{ request('role') == 'Kepala Sekolah' ? 'selected' : '' }}>Kepala Sekolah</option>
                        <option value="Waka Kurikulum" {{ request('role') == 'Waka Kurikulum' ? 'selected' : '' }}>Waka Kurikulum</option>
                        <option value="Guru" {{ request('role') == 'Guru' ? 'selected' : '' }}>Guru</option>
                        <option value="Pegawai" {{ request('role') == 'Pegawai' ? 'selected' : '' }}>Pegawai</option>
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label for="status_filter" class="form-label">Status</label>
                    <select class="form-select" id="status_filter" name="status">
                        <option value="">Semua Status</option>
                        <option value="Aktif" {{ request('status') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="Non-Aktif" {{ request('status') == 'Non-Aktif' ? 'selected' : '' }}>Non-Aktif</option>
                    </select>
                </div>
                
                <div class="col-md-2">
                    <label for="sort_by" class="form-label">Urutkan</label>
                    <select class="form-select" id="sort_by" name="sort_by">
                        <option value="name" {{ request('sort_by') == 'name' ? 'selected' : '' }}>Nama</option>
                        <option value="email" {{ request('sort_by') == 'email' ? 'selected' : '' }}>Email</option>
                        <option value="role" {{ request('sort_by') == 'role' ? 'selected' : '' }}>Role</option>
                        <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Tanggal Dibuat</option>
                    </select>
                </div>
                
                <div class="col-12">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Cari
                        </button>
                        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times"></i> Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <!-- Results Info -->
            @if(request()->hasAny(['search', 'role', 'status']))
                <div class="alert alert-info mb-3">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Hasil Pencarian:</strong> 
                    Ditemukan {{ $users->total() }} pengguna
                    @if(request('search'))
                        dengan kata kunci "{{ request('search') }}"
                    @endif
                    @if(request('role'))
                        dengan role {{ ucfirst(request('role')) }}
                    @endif
                    @if(request('status'))
                        dengan status {{ request('status') }}
                    @endif
                </div>
            @endif
            
            @if(isset($users) && $users->count())
                <div class="table-responsive">
                    <table class="table table-striped align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Pengguna</th>
                                <th>NIP/NIK</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center me-2" 
                                             style="width: 40px; height: 40px; font-size: 0.875rem; font-weight: 600;">
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
                                    <span class="badge {{ strtolower($user->role->role_name ?? '') === 'admin' ? 'bg-danger' : (strtolower($user->role->role_name ?? '') === 'teacher' ? 'bg-info' : 'bg-success') }}">
                                        {{ ucfirst($user->role->role_name ?? 'Unknown') }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge {{ $user->status === 'Aktif' ? 'bg-success' : 'bg-secondary' }}">
                                        {{ $user->status }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.users.show', $user) }}" class="btn btn-sm btn-outline-info" title="Lihat Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @if($user->id !== auth()->id())
                                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus pengguna ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-3 d-flex justify-content-center">{{ $users->links('pagination::bootstrap-4') }}</div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-user-times fa-3x text-muted mb-3"></i>
                    <h5 class="mb-1">Belum ada data pengguna</h5>
                    <p class="text-muted">Silakan tambahkan pengguna baru.</p>
                </div>
            @endif
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-submit form when filter changes
    const filterSelects = document.querySelectorAll('#role_filter, #status_filter, #sort_by');
    filterSelects.forEach(select => {
        select.addEventListener('change', function() {
            this.form.submit();
        });
    });
    
    // Search with Enter key
    const searchInput = document.getElementById('search');
    searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            this.form.submit();
        }
    });
});
</script>
@endpush