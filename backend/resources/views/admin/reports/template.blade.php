@extends('admin.layouts.app')

@section('title', $title)

@push('styles')
<style>
.report-card {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    border: 1px solid #e9ecef;
    margin-bottom: 1.5rem;
}

.report-header {
    background: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
    padding: 1.5rem;
    border-radius: 8px 8px 0 0;
}

.report-body {
    padding: 1.5rem;
}

.stats-card {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    border: 1px solid #e9ecef;
    padding: 1.5rem;
    text-align: center;
    transition: all 0.2s ease;
}

.stats-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.stats-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    margin: 0 auto 1rem;
}

/* Fix icon sizes */
.fas {
    font-size: inherit;
}

.btn i.fas {
    font-size: 0.875rem;
}

.btn-sm i.fas {
    font-size: 0.75rem;
}

/* Fix SVG icon sizes */
svg {
    width: 1em;
    height: 1em;
    vertical-align: middle;
}

.btn svg {
    width: 0.875rem;
    height: 0.875rem;
}

.btn-sm svg {
    width: 0.75rem;
    height: 0.75rem;
}

/* Pagination styling */
.pagination {
    margin: 0;
    gap: 0.25rem;
}

.pagination .page-link {
    border: 1px solid #dee2e6;
    color: #6c757d;
    padding: 0.5rem 0.75rem;
    font-size: 0.875rem;
    border-radius: 0.375rem;
    margin: 0 0.125rem;
    transition: all 0.15s ease-in-out;
}

.pagination .page-link:hover {
    color: #495057;
    background-color: #e9ecef;
    border-color: #adb5bd;
}

.pagination .page-item.active .page-link {
    background-color: #007bff;
    border-color: #007bff;
    color: white;
}

.pagination .page-item.disabled .page-link {
    color: #6c757d;
    background-color: #fff;
    border-color: #dee2e6;
    cursor: not-allowed;
}

.pagination-info {
    font-size: 0.875rem;
    color: #6c757d;
    font-weight: 500;
}

.pagination-wrapper {
    background: #f8f9fa;
    border: 1px solid #e9ecef;
    border-radius: 0.5rem;
    padding: 1rem 1.5rem;
    margin-top: 1rem;
}

.stats-number {
    font-size: 2rem;
    font-weight: bold;
    margin-bottom: 0.5rem;
}

.stats-label {
    color: #6c757d;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.table-responsive {
    border-radius: 8px;
    overflow: hidden;
}

.table th {
    background-color: #f8f9fa;
    border-bottom: 2px solid #dee2e6;
    font-weight: 600;
    color: #495057;
    padding: 1rem 0.75rem;
}

.table td {
    padding: 0.75rem;
    vertical-align: middle;
    border-bottom: 1px solid #dee2e6;
}

.table tbody tr:hover {
    background-color: #f8f9fa;
}

.badge {
    font-size: 0.75rem;
    padding: 0.375rem 0.75rem;
}

.export-buttons {
    gap: 0.5rem;
}

.export-buttons .btn {
    border-radius: 6px;
    font-weight: 500;
    padding: 0.5rem 1rem;
}

.btn-excel {
    background-color: #28a745;
    border-color: #28a745;
    color: white;
}

.btn-excel:hover {
    background-color: #218838;
    border-color: #1e7e34;
    color: white;
}

.btn-pdf {
    background-color: #dc3545;
    border-color: #dc3545;
    color: white;
}

.btn-pdf:hover {
    background-color: #c82333;
    border-color: #bd2130;
    color: white;
}

.pagination {
    justify-content: center;
    margin-top: 2rem;
}

.page-link {
    color: #007bff;
    border: 1px solid #dee2e6;
    padding: 0.5rem 0.75rem;
}

.page-link:hover {
    color: #0056b3;
    background-color: #e9ecef;
    border-color: #dee2e6;
}

.page-item.active .page-link {
    background-color: #007bff;
    border-color: #007bff;
}

.filter-card {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    border: 1px solid #e9ecef;
    margin-bottom: 1.5rem;
}

.filter-header {
    background: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
    padding: 1rem 1.5rem;
    border-radius: 8px 8px 0 0;
}

.filter-body {
    padding: 1.5rem;
}

.form-label {
    font-weight: 600;
    color: #495057;
    margin-bottom: 0.5rem;
}

.form-control, .form-select {
    border: 1px solid #ced4da;
    border-radius: 6px;
    padding: 0.5rem 0.75rem;
}

.form-control:focus, .form-select:focus {
    border-color: #80bdff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.btn-primary {
    background-color: #007bff;
    border-color: #007bff;
    border-radius: 6px;
    font-weight: 500;
    padding: 0.5rem 1rem;
}

.btn-primary:hover {
    background-color: #0056b3;
    border-color: #004085;
}

.btn-secondary {
    background-color: #6c757d;
    border-color: #6c757d;
    border-radius: 6px;
    font-weight: 500;
    padding: 0.5rem 1rem;
}

.btn-secondary:hover {
    background-color: #545b62;
    border-color: #4e555b;
}
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-chart-line me-2"></i>
            {{ $title }}
        </h1>
        <div class="d-flex export-buttons">
            <a href="{{ route($exportExcelRoute, request()->query()) }}" class="btn btn-excel">
                <i class="fas fa-file-excel me-2"></i>
                Export Excel
            </a>
            <a href="{{ route($exportPdfRoute, request()->query()) }}" class="btn btn-pdf">
                <i class="fas fa-file-pdf me-2"></i>
                Export PDF
            </a>
        </div>
    </div>

    <!-- Summary Cards -->
    @if(isset($summaryCards) && count($summaryCards) > 0)
    <div class="row mb-4">
        @foreach($summaryCards as $card)
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stats-card">
                <div class="stats-icon" style="background-color: {{ $card['color'] }}20; color: {{ $card['color'] }};">
                    <i class="{{ $card['icon'] }}"></i>
                </div>
                <div class="stats-number" style="color: {{ $card['color'] }};">
                    {{ $card['value'] }}
                </div>
                <div class="stats-label">{{ $card['label'] }}</div>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    <!-- Filter Card -->
    <div class="filter-card">
        <div class="filter-header">
            <h6 class="mb-0">
                <i class="fas fa-filter me-2"></i>
                Filter Data
            </h6>
        </div>
        <div class="filter-body">
            <form method="GET" action="{{ request()->url() }}">
                <div class="row">
                    <div class="col-md-3">
                        <label for="start_date" class="form-label">Tanggal Mulai</label>
                        <input type="date" class="form-control" id="start_date" name="start_date" 
                               value="{{ request('start_date', now()->startOfMonth()->format('Y-m-d')) }}">
                    </div>
                    <div class="col-md-3">
                        <label for="end_date" class="form-label">Tanggal Selesai</label>
                        <input type="date" class="form-control" id="end_date" name="end_date" 
                               value="{{ request('end_date', now()->format('Y-m-d')) }}">
                    </div>
                    <div class="col-md-3">
                        <label for="search" class="form-label">Pencarian</label>
                        <input type="text" class="form-control" id="search" name="search" 
                               value="{{ request('search') }}" placeholder="Cari nama...">
                    </div>
                    <div class="col-md-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status">
                            <option value="">Semua Status</option>
                            @if(isset($statusOptions))
                                @foreach($statusOptions as $value => $label)
                                <option value="{{ $value }}" {{ request('status') == $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search me-2"></i>
                            Filter Data
                        </button>
                        <a href="{{ request()->url() }}" class="btn btn-secondary">
                            <i class="fas fa-refresh me-2"></i>
                            Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Data Table -->
    <div class="report-card">
        <div class="report-header">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="mb-0">
                    <i class="fas fa-table me-2"></i>
                    Data {{ $title }}
                </h6>
                <div class="btn-group" role="group">
                    @yield('export-buttons')
                </div>
            </div>
        </div>
        <div class="report-body">
            @if(isset($data) && $data->count() > 0)
                <!-- Total Records Info -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="text-muted">
                        <i class="fas fa-info-circle me-1"></i>
                        Menampilkan {{ $data->firstItem() ?? 0 }} - {{ $data->lastItem() ?? 0 }} dari {{ $data->total() }} total data
                    </div>
                    <div class="text-muted">
                        Halaman {{ $data->currentPage() }} dari {{ $data->lastPage() }}
                    </div>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th width="50" class="text-center">No</th>
                                @yield('table-headers')
                            </tr>
                        </thead>
                    <tbody>
                        @yield('table-rows')
                    </tbody>
                </table>
            </div>

            <!-- Enhanced Pagination -->
            <div class="pagination-wrapper">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="pagination-info">
                        <i class="fas fa-info-circle me-1"></i>
                        Menampilkan {{ $data->firstItem() ?? 0 }} - {{ $data->lastItem() ?? 0 }} dari {{ $data->total() }} total data | 
                        Halaman {{ $data->currentPage() }} dari {{ $data->lastPage() }}
                    </div>
                    <div>
                        {{ $data->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
            @else
            <div class="text-center py-5">
                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">Tidak ada data</h5>
                <p class="text-muted">Tidak ada data yang sesuai dengan filter yang dipilih.</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Auto-submit form when date inputs change
document.addEventListener('DOMContentLoaded', function() {
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
