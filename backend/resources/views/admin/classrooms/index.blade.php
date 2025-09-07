@extends('admin.layouts.app')

@section('title', 'Manajemen Kelas')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Manajemen Kelas</h1>
            <p class="text-muted mb-0">Kelola data kelas dan walikelas</p>
        </div>
        <div>
            <a href="{{ route('admin.classrooms.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Tambah Kelas
            </a>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $stats['total_classrooms'] }}</h4>
                            <p class="mb-0">Total Kelas</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-chalkboard fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $stats['active_classrooms'] }}</h4>
                            <p class="mb-0">Kelas Aktif</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-check-circle fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $stats['total_students'] }}</h4>
                            <p class="mb-0">Total Siswa</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-user-graduate fa-2x opacity-75"></i>
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
                            <h4 class="mb-0">{{ $stats['classrooms_with_walikelas'] }}</h4>
                            <p class="mb-0">Dengan Walikelas</p>
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
            <form method="GET" action="{{ route('admin.classrooms.index') }}" class="row g-3">
                <div class="col-md-4">
                    <label for="search" class="form-label">Pencarian</label>
                    <input type="text" 
                           class="form-control" 
                           id="search" 
                           name="search" 
                           value="{{ request('search') }}" 
                           placeholder="Cari nama kelas atau tingkat...">
                </div>
                
                <div class="col-md-3">
                    <label for="level_filter" class="form-label">Tingkat</label>
                    <select class="form-select" id="level_filter" name="level">
                        <option value="">Semua Tingkat</option>
                        <option value="7" {{ request('level') == '7' ? 'selected' : '' }}>Kelas 7</option>
                        <option value="8" {{ request('level') == '8' ? 'selected' : '' }}>Kelas 8</option>
                        <option value="9" {{ request('level') == '9' ? 'selected' : '' }}>Kelas 9</option>
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label for="status_filter" class="form-label">Status</label>
                    <select class="form-select" id="status_filter" name="status">
                        <option value="">Semua Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Non-Aktif</option>
                    </select>
                </div>
                
                <div class="col-md-2">
                    <label for="sort_by" class="form-label">Urutkan</label>
                    <select class="form-select" id="sort_by" name="sort_by">
                        <option value="name" {{ request('sort_by') == 'name' ? 'selected' : '' }}>Nama</option>
                        <option value="level" {{ request('sort_by') == 'level' ? 'selected' : '' }}>Tingkat</option>
                        <option value="is_active" {{ request('sort_by') == 'is_active' ? 'selected' : '' }}>Status</option>
                        <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Tanggal Dibuat</option>
                    </select>
                </div>
                
                <div class="col-12">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Cari
                        </button>
                        <a href="{{ route('admin.classrooms.index') }}" class="btn btn-outline-secondary">
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
            @if(request()->hasAny(['search', 'level', 'status']))
                <div class="alert alert-info mb-3">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Hasil Pencarian:</strong> 
                    Ditemukan {{ $classRooms->total() }} kelas
                    @if(request('search'))
                        dengan kata kunci "{{ request('search') }}"
                    @endif
                    @if(request('level'))
                        dari tingkat {{ request('level') }}
                    @endif
                    @if(request('status'))
                        dengan status {{ request('status') == 'active' ? 'Aktif' : 'Non-Aktif' }}
                    @endif
                </div>
            @endif
            
            @if(isset($classRooms) && $classRooms->count())
                <!-- Total Records Info -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="text-muted">
                        <i class="fas fa-info-circle me-1"></i>
                        Menampilkan {{ $classRooms->firstItem() ?? 0 }} - {{ $classRooms->lastItem() ?? 0 }} dari {{ $classRooms->total() }} total kelas
                    </div>
                    <div class="text-muted">
                        Halaman {{ $classRooms->currentPage() }} dari {{ $classRooms->lastPage() }}
                    </div>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-striped align-middle mb-0">
                        <thead>
                            <tr>
                                <th width="50" class="text-center">No</th>
                                <th>Nama Kelas</th>
                                <th>Tingkat</th>
                                <th>Walikelas</th>
                                <th>Jumlah Siswa</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($classRooms as $index => $classroom)
                            <tr>
                                <td class="text-center">
                                    {{ $classRooms->firstItem() + $index }}
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ $classroom->name }}</div>
                                    @if($classroom->description)
                                        <small class="text-muted">{{ $classroom->description }}</small>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-primary">Kelas {{ $classroom->level }}</span>
                                </td>
                                <td>
                                    @if($classroom->walikelas)
                                        <div class="d-flex align-items-center">
                                            <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center me-2" 
                                                 style="width: 32px; height: 32px; font-size: 0.875rem; font-weight: 600;">
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
                                    <span class="badge bg-success">{{ $classroom->student_count ?? 0 }} Siswa</span>
                                </td>
                                <td>
                                    <span class="badge {{ $classroom->is_active ? 'bg-success' : 'bg-secondary' }}">
                                        {{ $classroom->is_active ? 'Aktif' : 'Non-Aktif' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.classrooms.show', $classroom) }}" class="btn btn-sm btn-outline-info" title="Lihat Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.classrooms.edit', $classroom) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.classrooms.destroy', $classroom) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus kelas ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <!-- Enhanced Pagination -->
                <div class="mt-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted">
                            <small>
                                <i class="fas fa-list me-1"></i>
                                Total: {{ $classRooms->total() }} kelas | 
                                Per halaman: {{ $classRooms->perPage() }} | 
                                Halaman {{ $classRooms->currentPage() }} dari {{ $classRooms->lastPage() }}
                            </small>
                        </div>
                        <div>
                            {{ $classRooms->appends(request()->query())->links('pagination::bootstrap-4') }}
                        </div>
                    </div>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-chalkboard fa-3x text-muted mb-3"></i>
                    <h5 class="mb-1">Belum ada data kelas</h5>
                    <p class="text-muted">Silakan tambahkan kelas baru.</p>
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
    const filterSelects = document.querySelectorAll('#level_filter, #status_filter, #sort_by');
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
