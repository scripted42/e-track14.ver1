@extends('admin.layouts.app')

@section('title', 'Daftar Siswa')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Daftar Siswa</h1>
            <p class="text-muted mb-0">Manajemen data siswa</p>
        </div>
        <div>
            @if(auth()->user()->hasRole('Admin'))
            <a href="{{ route('admin.students.import') }}" class="btn btn-success me-2">
                <i class="fas fa-file-import me-2"></i>Import Excel
            </a>
            <a href="{{ route('admin.students.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Tambah Siswa
            </a>
            @endif
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card bg-primary text-white">
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
        
        <div class="col-md-4">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $stats['active_students'] }}</h4>
                            <p class="mb-0">Siswa Aktif</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-check-circle fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card bg-secondary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $stats['inactive_students'] }}</h4>
                            <p class="mb-0">Siswa Non-Aktif</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-user-times fa-2x opacity-75"></i>
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
            <form method="GET" action="{{ route('admin.students.index') }}" class="row g-3">
                <div class="col-md-4">
                    <label for="search" class="form-label">Pencarian</label>
                    <input type="text" 
                           class="form-control" 
                           id="search" 
                           name="search" 
                           value="{{ request('search') }}" 
                           placeholder="Cari nama atau NISN...">
                </div>
                
                <div class="col-md-3">
                    <label for="class_filter" class="form-label">Kelas</label>
                    <select class="form-select" id="class_filter" name="class_filter">
                        <option value="">Semua Kelas</option>
                        @foreach($classes as $class)
                            <option value="{{ $class }}" {{ request('class_filter') == $class ? 'selected' : '' }}>
                                {{ $class }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label for="status_filter" class="form-label">Status</label>
                    <select class="form-select" id="status_filter" name="status_filter">
                        <option value="">Semua Status</option>
                        <option value="Aktif" {{ request('status_filter') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="Non-Aktif" {{ request('status_filter') == 'Non-Aktif' ? 'selected' : '' }}>Non-Aktif</option>
                    </select>
                </div>
                
                <div class="col-md-2">
                    <label for="sort_by" class="form-label">Urutkan</label>
                    <select class="form-select" id="sort_by" name="sort_by">
                        <option value="name" {{ request('sort_by') == 'name' ? 'selected' : '' }}>Nama</option>
                        <option value="nisn" {{ request('sort_by') == 'nisn' ? 'selected' : '' }}>NISN</option>
                        <option value="class_name" {{ request('sort_by') == 'class_name' ? 'selected' : '' }}>Kelas</option>
                        <option value="status" {{ request('sort_by') == 'status' ? 'selected' : '' }}>Status</option>
                        <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Tanggal Dibuat</option>
                    </select>
                </div>
                
                <div class="col-12">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Cari
                        </button>
                        <a href="{{ route('admin.students.index') }}" class="btn btn-outline-secondary">
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
            @if(request()->hasAny(['search', 'class_filter', 'status_filter']))
                <div class="alert alert-info mb-3">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Hasil Pencarian:</strong> 
                    Ditemukan {{ $students->total() }} siswa
                    @if(request('search'))
                        dengan kata kunci "{{ request('search') }}"
                    @endif
                    @if(request('class_filter'))
                        dari kelas {{ request('class_filter') }}
                    @endif
                    @if(request('status_filter'))
                        dengan status {{ request('status_filter') }}
                    @endif
                </div>
            @endif
            
            @if(isset($students) && $students->count())
                <!-- Total Records Info -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="text-muted">
                        <i class="fas fa-info-circle me-1"></i>
                        Menampilkan {{ $students->firstItem() ?? 0 }} - {{ $students->lastItem() ?? 0 }} dari {{ $students->total() }} total siswa
                    </div>
                    <div class="text-muted">
                        Halaman {{ $students->currentPage() }} dari {{ $students->lastPage() }}
                    </div>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-striped align-middle mb-0">
                        <thead>
                            <tr>
                                <th width="50" class="text-center">No</th>
                                <th>NISN</th>
                                <th>Foto</th>
                                <th>Nama</th>
                                <th>Kelas</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($students as $index => $student)
                            <tr>
                                <td class="text-center">
                                    {{ $students->firstItem() + $index }}
                                </td>
                                <td>{{ $student->nisn ?? '-' }}</td>
                                <td>
                                    <div class="photo-container" style="position: relative; width: 40px; height: 40px;">
                                        @if($student->photo)
                                            <img src="{{ asset('storage/' . $student->photo) }}" 
                                                 alt="Foto" 
                                                 class="rounded-circle student-photo" 
                                                 style="width: 40px; height: 40px; object-fit: cover; position: absolute; top: 0; left: 0; z-index: 2;"
                                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                            <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center fallback-icon" 
                                                 style="width: 40px; height: 40px; position: absolute; top: 0; left: 0; z-index: 1; display: none;">
                                                <i class="fas fa-user text-white"></i>
                                            </div>
                                        @else
                                            <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center" 
                                                 style="width: 40px; height: 40px; position: absolute; top: 0; left: 0; z-index: 1;">
                                                <i class="fas fa-user text-white"></i>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td>{{ $student->name }}</td>
                                <td>{{ $student->class_name }}</td>
                                <td>
                                    <span class="badge {{ $student->status === 'Aktif' ? 'bg-success' : 'bg-secondary' }}">
                                        {{ $student->status }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.students.show', $student) }}" class="btn btn-sm btn-outline-info" title="Lihat Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if(auth()->user()->hasRole('Admin'))
                                        <a href="{{ route('admin.students.edit', $student) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.students.destroy', $student) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus siswa ini?')">
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
                <!-- Enhanced Pagination -->
                <div class="mt-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted">
                            <small>
                                <i class="fas fa-list me-1"></i>
                                Total: {{ $students->total() }} siswa | 
                                Per halaman: {{ $students->perPage() }} | 
                                Halaman {{ $students->currentPage() }} dari {{ $students->lastPage() }}
                            </small>
                        </div>
                        <div>
                            {{ $students->appends(request()->query())->links('pagination::bootstrap-4') }}
                        </div>
                    </div>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-user-graduate fa-3x text-muted mb-3"></i>
                    <h5 class="mb-1">Belum ada data siswa</h5>
                    <p class="text-muted">Silakan tambahkan siswa baru.</p>
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
    const filterSelects = document.querySelectorAll('#class_filter, #status_filter, #sort_by');
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



