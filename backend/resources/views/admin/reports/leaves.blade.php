@extends('admin.layouts.app')

@section('title', 'Laporan Izin & Cuti')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Laporan Izin & Cuti</h1>
            <p class="text-muted mb-0">Laporan detail pengajuan izin dan cuti pegawai</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.reports.leaves.export.excel', request()->query()) }}" class="btn btn-success">
                <i class="fas fa-file-excel me-2"></i>Export Excel
            </a>
            <a href="{{ route('admin.reports.leaves.export.pdf', request()->query()) }}" class="btn btn-danger">
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
                            <h4 class="mb-0">{{ $totalLeaves }}</h4>
                            <p class="mb-0">Total Izin</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-calendar-alt fa-2x opacity-75"></i>
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
                            <h4 class="mb-0">{{ $pendingLeaves }}</h4>
                            <p class="mb-0">Menunggu</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-clock fa-2x opacity-75"></i>
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
                            <h4 class="mb-0">{{ $approvedLeaves }}</h4>
                            <p class="mb-0">Disetujui</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-check-circle fa-2x opacity-75"></i>
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
                            <h4 class="mb-0">{{ $rejectedLeaves }}</h4>
                            <p class="mb-0">Ditolak</p>
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
            <form method="GET" action="{{ route('admin.reports.leaves') }}" class="row g-3">
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
                        <option value="menunggu" {{ request('status') == 'menunggu' ? 'selected' : '' }}>Menunggu</option>
                        <option value="disetujui" {{ request('status') == 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                        <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                    </select>
                </div>
                
                <div class="col-12">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Filter Data
                        </button>
                        <a href="{{ route('admin.reports.leaves') }}" class="btn btn-outline-secondary">
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
                        Menampilkan {{ $data->firstItem() ?? 0 }} - {{ $data->lastItem() ?? 0 }} dari {{ $data->total() }} total pengajuan
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
                                <th>Pegawai</th>
<th>Role</th>
<th>Tanggal Mulai</th>
<th>Tanggal Selesai</th>
<th>Jenis Izin</th>
<th>Status</th>
<th>Disetujui Oleh</th>
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
                                    <div class="fw-semibold">{{ $item->user->name }}</div>
                                    <small class="text-muted">{{ $item->user->email }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ $item->user->role->role_name }}</span>
                                </td>
                                <td>
                                    <span class="fw-medium">{{ \Carbon\Carbon::parse($item->start_date)->format('d M Y') }}</span>
                                </td>
                                <td>
                                    <span class="fw-medium">{{ \Carbon\Carbon::parse($item->end_date)->format('d M Y') }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ ucfirst(str_replace('_', ' ', $item->leave_type)) }}</span>
                                </td>
    <td>
        @switch($item->status)
            @case('menunggu')
                <span class="badge bg-warning">Menunggu</span>
                @break
            @case('disetujui')
                <span class="badge bg-success">Disetujui</span>
                @break
            @case('ditolak')
                <span class="badge bg-danger">Ditolak</span>
                @break
            @default
                <span class="badge bg-secondary">{{ ucfirst($item->status) }}</span>
        @endswitch
    </td>
                                <td>
                                    <span class="text-muted">{{ $item->approver->name ?? '-' }}</span>
                                </td>
                                <td>
                                    <span class="text-muted">{{ Str::limit($item->reason, 50) ?? '-' }}</span>
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
                                Total: {{ $data->total() }} pengajuan | 
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
                    <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                    <h5 class="mb-1">Tidak ada data pengajuan izin</h5>
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