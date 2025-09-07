@extends('admin.layouts.app')

@section('title', 'Laporan Kehadiran Staff')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Laporan Kehadiran Staff</h1>
            <p class="text-muted mb-0">Laporan detail kehadiran pegawai dan guru</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.reports.attendance.export.excel', request()->query()) }}" class="btn btn-success">
                <i class="fas fa-file-excel me-2"></i>Export Excel
            </a>
            <a href="{{ route('admin.reports.attendance.export.pdf', request()->query()) }}" class="btn btn-danger">
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
                            <h4 class="mb-0">{{ $totalStaff }}</h4>
                            <p class="mb-0">Total Staff</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-users fa-2x opacity-75"></i>
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
            <form method="GET" action="{{ route('admin.reports.attendance') }}" class="row g-3">
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
                           placeholder="Cari nama pegawai...">
                </div>
                
                <div class="col-md-3">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">Semua Status</option>
                        <option value="hadir" {{ request('status') == 'hadir' ? 'selected' : '' }}>Hadir</option>
                        <option value="terlambat" {{ request('status') == 'terlambat' ? 'selected' : '' }}>Terlambat</option>
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
                        <a href="{{ route('admin.reports.attendance') }}" class="btn btn-outline-secondary">
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
<th>Nama</th>
<th>Role</th>
<th>Tanggal</th>
<th>Check In</th>
<th>Check Out</th>
<th>Status</th>
<th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
@foreach($data as $index => $item)
<tr>
                                <td class="text-center">
                                    {{ $data->firstItem() + (int)$index }}
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ $item['user']->name ?? 'N/A' }}</div>
                                    <small class="text-muted">{{ $item['user']->email ?? 'N/A' }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ $item['user']->role->role_name ?? 'No Role' }}</span>
                                </td>
                                <td>
                                    <span class="fw-medium">{{ \Carbon\Carbon::createFromFormat('Y-m-d', $item['date'])->format('d M Y') }}</span>
                                </td>
                                <td>
                                    @if($item['checkin_time'])
                                        <span class="fw-medium text-success">{{ $item['checkin_time'] }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($item['checkout_time'])
                                        <span class="fw-medium text-danger">{{ $item['checkout_time'] }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($item['checkin_status'])
                                        @if($item['checkin_status'] === 'terlambat')
                                            <span class="badge bg-warning">Terlambat</span>
                                        @elseif($item['checkin_status'] === 'hadir')
                                            <span class="badge bg-success">Hadir</span>
                                        @else
                                            <span class="badge bg-info">{{ ucfirst($item['checkin_status']) }}</span>
                                        @endif
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="text-muted">{{ $item['notes'] ?? '-' }}</span>
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
                    <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                    <h5 class="mb-1">Tidak ada data kehadiran</h5>
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