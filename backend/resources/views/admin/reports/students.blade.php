@extends('admin.layouts.app')

@section('title', 'Laporan Kehadiran Siswa')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Laporan Kehadiran Siswa</h1>
            <p class="text-muted mb-0">Laporan detail kehadiran siswa</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.reports.students.export.excel', request()->query()) }}" class="btn btn-success">
                <i class="fas fa-file-excel me-2"></i>Export Excel
            </a>
            <a href="{{ route('admin.reports.students.export.pdf', request()->query()) }}" class="btn btn-danger">
                <i class="fas fa-file-pdf me-2"></i>Export PDF
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
                            <h4 class="mb-0">{{ $totalStudents }}</h4>
                            <p class="mb-0">Total Siswa</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-graduation-cap fa-2x opacity-75"></i>
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
                            <h4 class="mb-0">{{ $presentToday }}</h4>
                            <p class="mb-0">Hadir Hari Ini</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-check-circle fa-2x opacity-75"></i>
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
                            <h4 class="mb-0">{{ $lateToday }}</h4>
                            <p class="mb-0">Terlambat</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-clock fa-2x opacity-75"></i>
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
                            <h4 class="mb-0">{{ $absentToday }}</h4>
                            <p class="mb-0">Tidak Hadir</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-times-circle fa-2x opacity-75"></i>
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
                <i class="fas fa-search me-2"></i>Filter Data
            </h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.reports.students') }}" class="row g-3">
                <div class="col-md-3">
                    <label for="start_date" class="form-label">Tanggal Mulai</label>
                    <input type="date" 
                           class="form-control" 
                           id="start_date" 
                           name="start_date" 
                           value="{{ request('start_date', now()->startOfMonth()->format('Y-m-d')) }}">
                </div>
                
                <div class="col-md-3">
                    <label for="end_date" class="form-label">Tanggal Selesai</label>
                    <input type="date" 
                           class="form-control" 
                           id="end_date" 
                           name="end_date" 
                           value="{{ request('end_date', now()->format('Y-m-d')) }}">
                </div>
                
                <div class="col-md-3">
                    <label for="search" class="form-label">Pencarian</label>
                    <input type="text" 
                           class="form-control" 
                           id="search" 
                           name="search" 
                           value="{{ request('search') }}" 
                           placeholder="Cari nama siswa...">
                </div>
                
                <div class="col-md-3">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">Semua Status</option>
                        <option value="hadir" {{ request('status') == 'hadir' ? 'selected' : '' }}>Hadir</option>
                        <option value="izin" {{ request('status') == 'izin' ? 'selected' : '' }}>Izin</option>
                        <option value="sakit" {{ request('status') == 'sakit' ? 'selected' : '' }}>Sakit</option>
                        <option value="alpha" {{ request('status') == 'alpha' ? 'selected' : '' }}>Alpha</option>
                    </select>
                </div>
                
                <div class="col-12">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Filter Data
                        </button>
                        <a href="{{ route('admin.reports.students') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times"></i> Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            @if(isset($data) && $data->count())
                <!-- Total Records Info -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="text-muted">
                        <i class="fas fa-info-circle me-1"></i>
                        Menampilkan {{ $data->firstItem() ?? 0 }} - {{ $data->lastItem() ?? 0 }} dari {{ $data->total() }} total kehadiran
                    </div>
                    <div class="text-muted">
                        Halaman {{ $data->currentPage() }} dari {{ $data->lastPage() }}
                    </div>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-striped align-middle mb-0">
                        <thead>
                            <tr>
                                <th width="50" class="text-center">No</th>
                                <th>Nama Siswa</th>
<th>Kelas</th>
<th>Tanggal</th>
<th>Waktu Check In</th>
<th>Status</th>
<th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
@foreach($data as $index => $item)
<tr>
                                <td class="text-center">
                                    {{ $data->firstItem() + $index }}
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ $item->student->name }}</div>
                                    <small class="text-muted">NISN: {{ $item->student->nisn ?? '-' }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-primary">{{ $item->student->classRoom->name ?? '-' }}</span>
                                </td>
                                <td>
                                    <span class="fw-medium">{{ \Carbon\Carbon::parse($item->created_at)->format('d M Y') }}</span>
                                </td>
                                <td>
                                    <span class="fw-medium text-success">{{ \Carbon\Carbon::parse($item->created_at)->format('H:i') }}</span>
                                </td>
    <td>
        @switch($item->status)
            @case('hadir')
                <span class="badge bg-success">Hadir</span>
                @break
            @case('izin')
                <span class="badge bg-info">Izin</span>
                @break
            @case('sakit')
                <span class="badge bg-warning">Sakit</span>
                @break
            @case('alpha')
                <span class="badge bg-danger">Alpha</span>
                @break
            @default
                <span class="badge bg-secondary">{{ ucfirst($item->status) }}</span>
        @endswitch
    </td>
                                <td>
                                    <span class="text-muted">{{ $item->notes ?? '-' }}</span>
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
                                Total: {{ $data->total() }} kehadiran | 
                                Per halaman: {{ $data->perPage() }} | 
                                Halaman {{ $data->currentPage() }} dari {{ $data->lastPage() }}
                            </small>
                        </div>
                        <div>
                            {{ $data->appends(request()->query())->links('pagination::bootstrap-4') }}
                        </div>
                    </div>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-user-graduate fa-3x text-muted mb-3"></i>
                    <h5 class="mb-1">Tidak ada data kehadiran siswa</h5>
                    <p class="text-muted">Tidak ada data yang sesuai dengan filter yang dipilih.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-submit form when date inputs change
    const startDate = document.getElementById('start_date');
    const endDate = document.getElementById('end_date');
    
    if (startDate && endDate) {
        startDate.addEventListener('change', function() {
            if (endDate.value && startDate.value > endDate.value) {
                endDate.value = startDate.value;
            }
        });
        
        endDate.addEventListener('change', function() {
            if (startDate.value && endDate.value < startDate.value) {
                startDate.value = endDate.value;
            }
        });
    }
});
</script>
@endpush