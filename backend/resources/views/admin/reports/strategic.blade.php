@extends('admin.layouts.app')

@section('title', 'Laporan Strategis')

@push('styles')
<style>
.detail-card {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    border: 1px solid #e9ecef;
    margin-bottom: 1rem;
}

.detail-header {
    background: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
    padding: 1rem;
    border-radius: 8px 8px 0 0;
}

.detail-body {
    padding: 1rem;
}

.stats-card {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    border: 1px solid #e9ecef;
    padding: 1rem;
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
    color: white;
}

.stats-value {
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
}

.stats-label {
    color: #6c757d;
    font-size: 0.875rem;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.bg-primary { background: #007bff !important; }
.bg-success { background: #28a745 !important; }
.bg-warning { background: #ffc107 !important; }
.bg-danger { background: #dc3545 !important; }
.bg-info { background: #17a2b8 !important; }

.text-primary { color: #007bff !important; }
.text-success { color: #28a745 !important; }
.text-warning { color: #ffc107 !important; }
.text-danger { color: #dc3545 !important; }
.text-info { color: #17a2b8 !important; }

.progress {
    height: 8px;
    border-radius: 4px;
}

.progress-bar {
    border-radius: 4px;
}

.risk-high { color: #dc3545; }
.risk-medium { color: #ffc107; }
.risk-low { color: #28a745; }

.table th {
    background: #f8f9fa;
    border: none;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.75rem;
    letter-spacing: 0.5px;
    color: #495057;
    padding: 1rem;
}

.table td {
    padding: 1rem;
    vertical-align: middle;
    border-color: #f1f3f4;
}

.table tbody tr:hover {
    background-color: #f8f9fa;
}
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="h3 mb-0">Laporan Strategis Sekolah</h1>
            <p class="text-muted mb-0">Strategic Dashboard - {{ \Carbon\Carbon::now()->format('d F Y') }}</p>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-primary" onclick="exportReport('excel')">
                <i class="fas fa-file-excel me-2"></i>Export Excel
            </button>
            <button type="button" class="btn btn-secondary" onclick="exportReport('pdf')">
                <i class="fas fa-file-pdf me-2"></i>Export PDF
            </button>
        </div>
    </div>

    <!-- Strategic Overview -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="alert alert-primary border-0 py-2">
                <div class="d-flex align-items-center">
                    <i class="fas fa-chart-line fa-2x me-3 text-primary"></i>
                    <div>
                        <h5 class="mb-1">Strategic Overview - ISO 21001:2018</h5>
                        <p class="mb-0">Educational Organizations Management System - Strategic Performance Indicators</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Strategic KPIs -->
    <div class="row mb-3">
        <div class="col-md-2 mb-2">
            <div class="stats-card">
                <div class="stats-icon bg-primary">
                    <i class="fas fa-smile"></i>
                </div>
                <div class="stats-value text-primary">{{ $strategicKPIs['employee_satisfaction'] }}%</div>
                <div class="stats-label">Kepuasan Staff</div>
            </div>
        </div>
        <div class="col-md-2 mb-2">
            <div class="stats-card">
                <div class="stats-icon bg-success">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <div class="stats-value text-success">{{ $strategicKPIs['student_satisfaction'] }}%</div>
                <div class="stats-label">Kepuasan Siswa</div>
            </div>
        </div>
        <div class="col-md-2 mb-2">
            <div class="stats-card">
                <div class="stats-icon bg-info">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stats-value text-info">{{ $strategicKPIs['parent_satisfaction'] }}%</div>
                <div class="stats-label">Kepuasan Orang Tua</div>
            </div>
        </div>
        <div class="col-md-2 mb-2">
            <div class="stats-card">
                <div class="stats-icon bg-warning">
                    <i class="fas fa-trophy"></i>
                </div>
                <div class="stats-value text-warning">{{ $strategicKPIs['academic_achievement'] }}%</div>
                <div class="stats-label">Pencapaian Akademik</div>
            </div>
        </div>
        <div class="col-md-2 mb-2">
            <div class="stats-card">
                <div class="stats-icon bg-danger">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <div class="stats-value text-danger">{{ $strategicKPIs['compliance_rate'] }}%</div>
                <div class="stats-label">Tingkat Kepatuhan</div>
            </div>
        </div>
        <div class="col-md-2 mb-2">
            <div class="stats-card">
                <div class="stats-icon bg-secondary">
                    <i class="fas fa-chart-pie"></i>
                </div>
                <div class="stats-value text-secondary">
                    @php
                        $overallScore = ($strategicKPIs['employee_satisfaction'] + $strategicKPIs['student_satisfaction'] + $strategicKPIs['parent_satisfaction'] + $strategicKPIs['academic_achievement'] + $strategicKPIs['compliance_rate']) / 5;
                    @endphp
                    {{ number_format($overallScore, 1) }}%
                </div>
                <div class="stats-label">Skor Keseluruhan</div>
            </div>
        </div>
    </div>

    <!-- Trend Analysis -->
    <div class="row mb-3">
        <div class="col-lg-8 mb-3">
            <div class="detail-card">
                <div class="detail-header">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-line me-2"></i>Analisis Tren
                    </h5>
                </div>
                <div class="detail-body">
                    <canvas id="trendChart" height="300"></canvas>
                </div>
            </div>
        </div>

        <!-- Performance Summary -->
        <div class="col-lg-4 mb-3">
            <div class="detail-card">
                <div class="detail-header">
                    <h5 class="mb-0">
                        <i class="fas fa-trophy me-2"></i>Ringkasan Kinerja
                    </h5>
                </div>
                <div class="detail-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="fw-semibold">Kepuasan Stakeholder</span>
                            <span class="badge bg-success">{{ number_format(($strategicKPIs['employee_satisfaction'] + $strategicKPIs['student_satisfaction'] + $strategicKPIs['parent_satisfaction']) / 3, 1) }}%</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar bg-success" style="width: {{ ($strategicKPIs['employee_satisfaction'] + $strategicKPIs['student_satisfaction'] + $strategicKPIs['parent_satisfaction']) / 3 }}%"></div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="fw-semibold">Pencapaian Akademik</span>
                            <span class="badge bg-warning">{{ $strategicKPIs['academic_achievement'] }}%</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar bg-warning" style="width: {{ $strategicKPIs['academic_achievement'] }}%"></div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="fw-semibold">Kepatuhan ISO</span>
                            <span class="badge bg-danger">{{ $strategicKPIs['compliance_rate'] }}%</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar bg-danger" style="width: {{ $strategicKPIs['compliance_rate'] }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Risk Assessment -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="detail-card">
                <div class="detail-header">
                    <h5 class="mb-0">
                        <i class="fas fa-exclamation-triangle me-2"></i>Penilaian Risiko
                    </h5>
                </div>
                <div class="detail-body">
                    <div class="row">
                        <!-- High Risk Areas -->
                        <div class="col-md-4 mb-3">
                            <div class="card border-danger">
                                <div class="card-header bg-danger text-white">
                                    <h6 class="mb-0">
                                        <i class="fas fa-exclamation-triangle me-2"></i>Risiko Tinggi
                                    </h6>
                                </div>
                                <div class="card-body">
                                    @if(count($riskAssessment['high_risk_areas']) > 0)
                                        <ul class="list-unstyled mb-0">
                                            @foreach($riskAssessment['high_risk_areas'] as $risk)
                                                <li class="mb-2">
                                                    <i class="fas fa-circle text-danger me-2" style="font-size: 8px;"></i>
                                                    {{ $risk }}
                                                </li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <p class="text-muted mb-0">Tidak ada risiko tinggi</p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Medium Risk Areas -->
                        <div class="col-md-4 mb-3">
                            <div class="card border-warning">
                                <div class="card-header bg-warning text-dark">
                                    <h6 class="mb-0">
                                        <i class="fas fa-exclamation-circle me-2"></i>Risiko Sedang
                                    </h6>
                                </div>
                                <div class="card-body">
                                    @if(count($riskAssessment['medium_risk_areas']) > 0)
                                        <ul class="list-unstyled mb-0">
                                            @foreach($riskAssessment['medium_risk_areas'] as $risk)
                                                <li class="mb-2">
                                                    <i class="fas fa-circle text-warning me-2" style="font-size: 8px;"></i>
                                                    {{ $risk }}
                                                </li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <p class="text-muted mb-0">Tidak ada risiko sedang</p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Low Risk Areas -->
                        <div class="col-md-4 mb-3">
                            <div class="card border-success">
                                <div class="card-header bg-success text-white">
                                    <h6 class="mb-0">
                                        <i class="fas fa-check-circle me-2"></i>Risiko Rendah
                                    </h6>
                                </div>
                                <div class="card-body">
                                    @if(count($riskAssessment['low_risk_areas']) > 0)
                                        <ul class="list-unstyled mb-0">
                                            @foreach($riskAssessment['low_risk_areas'] as $risk)
                                                <li class="mb-2">
                                                    <i class="fas fa-circle text-success me-2" style="font-size: 8px;"></i>
                                                    {{ $risk }}
                                                </li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <p class="text-muted mb-0">Tidak ada risiko rendah</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Strategic Recommendations -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="detail-card">
                <div class="detail-header">
                    <h5 class="mb-0">
                        <i class="fas fa-lightbulb me-2"></i>Rekomendasi Strategis
                    </h5>
                </div>
                <div class="detail-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-primary">Rekomendasi Jangka Pendek (1-3 bulan)</h6>
                            <ul>
                                <li>Meningkatkan monitoring kehadiran siswa</li>
                                <li>Memperbaiki sistem persetujuan izin</li>
                                <li>Melakukan pelatihan staff tentang ISO 21001:2018</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-success">Rekomendasi Jangka Panjang (6-12 bulan)</h6>
                            <ul>
                                <li>Implementasi sistem manajemen mutu terintegrasi</li>
                                <li>Pengembangan program peningkatan kepuasan stakeholder</li>
                                <li>Audit internal berkala untuk compliance</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Report Period -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 bg-light">
                <div class="card-body text-center">
                    <div class="row align-items-center">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <div class="d-flex align-items-center justify-content-center">
                                <i class="fas fa-calendar-alt fa-2x text-primary me-3"></i>
                                <div class="text-start">
                                    <h6 class="mb-1">Periode Laporan</h6>
                                    <small class="text-muted">{{ $startDate->format('d M Y') }} - {{ $endDate->format('d M Y') }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center justify-content-center">
                                <i class="fas fa-chart-line fa-2x text-success me-3"></i>
                                <div class="text-start">
                                    <h6 class="mb-1">Strategic Dashboard</h6>
                                    <small class="text-muted">ISO 21001:2018 Compliance</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
@vite(["resources/js/app.js"])
<script>
// Trend Analysis Chart
const trendCtx = document.getElementById('trendChart');
if (trendCtx) {
    const trendData = @json($trendAnalysis);
    
    new Chart(trendCtx, {
        type: 'line',
        data: {
            labels: trendData.map(item => item.month),
            datasets: [{
                label: 'Kehadiran Staff',
                data: trendData.map(item => item.employee_attendance),
                borderColor: '#007bff',
                backgroundColor: 'rgba(0, 123, 255, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4
            }, {
                label: 'Kehadiran Siswa',
                data: trendData.map(item => item.student_attendance),
                borderColor: '#28a745',
                backgroundColor: 'rgba(40, 167, 69, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    display: true,
                    text: 'Tren Kehadiran 12 Bulan Terakhir'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.1)'
                    }
                },
                x: {
                    grid: {
                        color: 'rgba(0, 0, 0, 0.1)'
                    }
                }
            }
        }
    });
}

function exportReport(type) {
    const startDate = '{{ $startDate->format("Y-m-d") }}';
    const endDate = '{{ $endDate->format("Y-m-d") }}';
    
    // Show loading message
    const exportBtn = event.target;
    const originalText = exportBtn.innerHTML;
    exportBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Mengekspor...';
    exportBtn.disabled = true;
    
    // For now, just show a message (future implementation: actual export)
    setTimeout(() => {
        alert(`Fitur export ${type.toUpperCase()} sedang dalam pengembangan\n\nData yang akan diekspor:\n- Periode: ${startDate} - ${endDate}\n- Tipe: Laporan Strategis`);
        
        exportBtn.innerHTML = originalText;
        exportBtn.disabled = false;
    }, 2000);
}
</script>
@endpush
