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
            <button type="button" class="btn btn-warning me-2" data-bs-toggle="modal" data-bs-target="#transferWalikelasModal">
                <i class="fas fa-exchange-alt me-2"></i>Transfer Wali Kelas
            </button>
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
                                        
                                        <!-- Walikelas Management -->
                                        @if($classroom->walikelas)
                                            <button type="button" class="btn btn-sm btn-outline-warning" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#assignWalikelasModal" 
                                                    data-classroom-id="{{ $classroom->id }}"
                                                    data-classroom-name="{{ $classroom->name }}"
                                                    data-current-walikelas="{{ $classroom->walikelas->name }}"
                                                    title="Ganti Wali Kelas">
                                                <i class="fas fa-user-edit"></i>
                                            </button>
                                            <form action="{{ route('admin.classrooms.remove-walikelas', $classroom) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus wali kelas dari kelas {{ $classroom->name }}?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus Wali Kelas">
                                                    <i class="fas fa-user-times"></i>
                                                </button>
                                            </form>
                                        @else
                                            <button type="button" class="btn btn-sm btn-outline-success" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#assignWalikelasModal" 
                                                    data-classroom-id="{{ $classroom->id }}"
                                                    data-classroom-name="{{ $classroom->name }}"
                                                    data-current-walikelas=""
                                                    title="Assign Wali Kelas">
                                                <i class="fas fa-user-plus"></i>
                                            </button>
                                        @endif
                                        
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

<!-- Assign Walikelas Modal -->
<div class="modal fade" id="assignWalikelasModal" tabindex="-1" aria-labelledby="assignWalikelasModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="assignWalikelasModalLabel">Assign Wali Kelas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="assignWalikelasForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Kelas</label>
                        <input type="text" class="form-control" id="classroomName" readonly>
                    </div>
                    
                    <div class="mb-3">
                        <label for="walikelas_id" class="form-label">Pilih Guru sebagai Wali Kelas</label>
                        <select class="form-select" id="walikelas_id" name="walikelas_id" required>
                            <option value="">-- Pilih Guru --</option>
                        </select>
                        <div class="form-text">Hanya guru yang belum menjadi wali kelas yang ditampilkan.</div>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Catatan:</strong> Guru yang dipilih akan menjadi wali kelas untuk kelas ini dan dapat mengakses data siswa di kelas tersebut.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Assign Wali Kelas</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Transfer Walikelas Modal -->
<div class="modal fade" id="transferWalikelasModal" tabindex="-1" aria-labelledby="transferWalikelasModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="transferWalikelasModalLabel">Transfer Wali Kelas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="transferWalikelasForm" method="POST" action="{{ route('admin.classrooms.transfer-walikelas') }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="from_classroom_id" class="form-label">Dari Kelas</label>
                        <select class="form-select" id="from_classroom_id" name="from_classroom_id" required>
                            <option value="">-- Pilih Kelas Asal --</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="walikelas_id" class="form-label">Wali Kelas</label>
                        <select class="form-select" id="transfer_walikelas_id" name="walikelas_id" required>
                            <option value="">-- Pilih Wali Kelas --</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="to_classroom_id" class="form-label">Ke Kelas</label>
                        <select class="form-select" id="to_classroom_id" name="to_classroom_id" required>
                            <option value="">-- Pilih Kelas Tujuan --</option>
                        </select>
                    </div>
                    
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Peringatan:</strong> Transfer ini akan memindahkan wali kelas dari kelas asal ke kelas tujuan. Pastikan kelas tujuan belum memiliki wali kelas.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning">Transfer Wali Kelas</button>
                </div>
            </form>
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

    // Assign Walikelas Modal
    const assignModal = document.getElementById('assignWalikelasModal');
    if (assignModal) {
        assignModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const classroomId = button.getAttribute('data-classroom-id');
            const classroomName = button.getAttribute('data-classroom-name');
            const currentWalikelas = button.getAttribute('data-current-walikelas');
            
            // Update modal title and form action
            const modalTitle = assignModal.querySelector('.modal-title');
            const form = assignModal.querySelector('#assignWalikelasForm');
            const classroomNameInput = assignModal.querySelector('#classroomName');
            
            if (currentWalikelas) {
                modalTitle.textContent = 'Ganti Wali Kelas';
            } else {
                modalTitle.textContent = 'Assign Wali Kelas';
            }
            
            form.action = `/admin/classrooms/${classroomId}/assign-walikelas`;
            classroomNameInput.value = classroomName;
            
            // Load available teachers
            loadAvailableTeachers();
        });
    }

    // Load available teachers function
    function loadAvailableTeachers() {
        const select = document.getElementById('walikelas_id');
        select.innerHTML = '<option value="">-- Pilih Guru --</option>';
        
        // Use data from server-side
        const availableTeachers = @json($availableTeachers);
        
        if (availableTeachers && availableTeachers.length > 0) {
            availableTeachers.forEach(teacher => {
                const option = document.createElement('option');
                option.value = teacher.id;
                option.textContent = `${teacher.name} (${teacher.email})`;
                select.appendChild(option);
            });
        } else {
            select.innerHTML = '<option value="">Tidak ada guru tersedia</option>';
        }
    }

    // Transfer Walikelas Modal
    const transferModal = document.getElementById('transferWalikelasModal');
    if (transferModal) {
        transferModal.addEventListener('show.bs.modal', function (event) {
            loadClassroomsForTransfer();
        });
    }

    // Load classrooms for transfer
    function loadClassroomsForTransfer() {
        // Load classrooms with walikelas (from)
        const fromSelect = document.getElementById('from_classroom_id');
        const toSelect = document.getElementById('to_classroom_id');
        const walikelasSelect = document.getElementById('transfer_walikelas_id');
        
        fromSelect.innerHTML = '<option value="">-- Pilih Kelas Asal --</option>';
        toSelect.innerHTML = '<option value="">-- Pilih Kelas Tujuan --</option>';
        walikelasSelect.innerHTML = '<option value="">-- Pilih Wali Kelas --</option>';
        
        // Load all classrooms
        fetch('/admin/classrooms')
            .then(response => response.text())
            .then(html => {
                // Parse HTML to extract classroom data
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const rows = doc.querySelectorAll('tbody tr');
                
                rows.forEach(row => {
                    const cells = row.querySelectorAll('td');
                    if (cells.length >= 4) {
                        const classroomName = cells[1].querySelector('.fw-semibold')?.textContent;
                        const walikelasName = cells[3].querySelector('.fw-semibold')?.textContent;
                        const classroomId = row.querySelector('button[data-classroom-id]')?.getAttribute('data-classroom-id');
                        
                        if (classroomName && classroomId) {
                            // Add to from select if has walikelas
                            if (walikelasName && walikelasName !== 'Belum ada walikelas') {
                                const fromOption = document.createElement('option');
                                fromOption.value = classroomId;
                                fromOption.textContent = `${classroomName} (${walikelasName})`;
                                fromSelect.appendChild(fromOption);
                            }
                            
                            // Add to to select if no walikelas
                            if (!walikelasName || walikelasName === 'Belum ada walikelas') {
                                const toOption = document.createElement('option');
                                toOption.value = classroomId;
                                toOption.textContent = classroomName;
                                toSelect.appendChild(toOption);
                            }
                        }
                    }
                });
            })
            .catch(error => {
                console.error('Error loading classrooms:', error);
            });
    }

    // Handle from classroom change
    document.getElementById('from_classroom_id').addEventListener('change', function() {
        const walikelasSelect = document.getElementById('transfer_walikelas_id');
        walikelasSelect.innerHTML = '<option value="">-- Pilih Wali Kelas --</option>';
        
        if (this.value) {
            const selectedOption = this.options[this.selectedIndex];
            const walikelasName = selectedOption.textContent.match(/\(([^)]+)\)/)?.[1];
            
            if (walikelasName) {
                const option = document.createElement('option');
                option.value = this.value; // This should be the walikelas_id, but we need to get it from the data
                option.textContent = walikelasName;
                walikelasSelect.appendChild(option);
            }
        }
    });
});
</script>
@endpush
