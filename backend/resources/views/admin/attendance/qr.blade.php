@extends('admin.layouts.app')

@section('title', 'Kelola QR Code')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Kelola QR Code Absensi</h1>
            <p class="text-muted">Kelola dan monitor QR code untuk absensi pegawai</p>
        </div>
        <div>
            <a href="{{ route('admin.attendance.qr.display') }}" target="_blank" class="btn btn-primary">
                <i class="fas fa-external-link-alt me-2"></i>
                Tampilkan di Monitor
            </a>
        </div>
    </div>

    <!-- QR Status Card -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-qrcode me-2"></i>
                        Status QR Code Hari Ini
                    </h5>
                    <div>
                        @if($todayQr && $todayQr->isValid())
                            <span class="badge bg-success">Aktif</span>
                        @else
                            <span class="badge bg-warning">Tidak Aktif</span>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    @if($todayQr)
                        <div class="row">
                            <div class="col-md-8">
                                <h6 class="text-muted">Informasi QR Code</h6>
                                <p><strong>Kode:</strong> {{ $todayQr->qr_code }}</p>
                                <p><strong>Dibuat:</strong> {{ $todayQr->created_at->format('d M Y H:i:s') }}</p>
                                <p><strong>Berlaku Sampai:</strong> {{ $todayQr->valid_until->format('d M Y H:i:s') }}</p>
                                <p><strong>Status:</strong> 
                                    @if($todayQr->isValid())
                                        <span class="text-success">Aktif</span>
                                    @else
                                        <span class="text-danger">Tidak Aktif</span>
                                    @endif
                                </p>
                            </div>
                            <div class="col-md-4 text-center">
                                <div id="qr-code-container">
                                    @if($todayQr && $todayQr->qr_code)
                                        <div style="background: linear-gradient(145deg, #ffffff, #f8f9fa); padding: 20px; border-radius: 15px; display: inline-block; box-shadow: 0 8px 25px rgba(0,0,0,0.1), inset 0 1px 0 rgba(255,255,255,0.8); border: 2px solid #e9ecef;">
                                            <div style="background: white; padding: 10px; border-radius: 8px; box-shadow: inset 0 2px 8px rgba(0,0,0,0.1);">
                                                <img src="{{ route('admin.attendance.qr.image', $todayQr->qr_code) }}" 
                                                     alt="QR Code" 
                                                     style="width: 200px; height: 200px; display: block; border-radius: 4px;" />
                                            </div>
                                            <div style="margin-top: 10px; color: #6c757d; font-size: 11px; font-weight: 500; letter-spacing: 0.5px;">
                                                <i class="fas fa-mobile-alt" style="margin-right: 5px;"></i>
                                                SCAN DENGAN SMARTPHONE
                                            </div>
                                        </div>
                                    @else
                                        <div class="text-center p-4" style="background: linear-gradient(145deg, #f8f9fa, #e9ecef); border-radius: 12px; border: 2px dashed #dee2e6;">
                                            <i class="fas fa-qrcode fa-3x text-muted mb-2"></i>
                                            <p class="text-muted mb-0 fw-medium">QR Code Belum Tersedia</p>
                                            <small class="text-muted">Generate QR code untuk memulai</small>
                                        </div>
                                    @endif
                                </div>
                                <small class="text-muted d-block mt-3">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Preview QR Code
                                </small>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                            <h5>Belum Ada QR Code Hari Ini</h5>
                            <p class="text-muted">Belum ada QR code yang dibuat untuk hari ini.</p>
                        </div>
                    @endif
                </div>
                <div class="card-footer">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            @if(!$todayQr || !$todayQr->isValid())
                                <form method="POST" action="{{ route('admin.attendance.qr.generate') }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-plus-circle me-2"></i>
                                        Generate QR Code Baru
                                    </button>
                                </form>
                            @endif
                        </div>
                        <div>
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                QR Code akan diperbarui secara otomatis setiap 10 detik di tampilan monitor
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent QR Codes -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-history me-2"></i>
                        Riwayat QR Code
                    </h5>
                </div>
                <div class="card-body">
                    @if($recentQrs->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Kode QR</th>
                                        <th>Dibuat</th>
                                        <th>Berlaku Sampai</th>
                                        <th>Status</th>
                                        <th>Penggunaan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentQrs as $qr)
                                    <tr>
                                        <td><code>{{ $qr->qr_code }}</code></td>
                                        <td>{{ $qr->created_at->format('d/m/Y H:i:s') }}</td>
                                        <td>{{ $qr->valid_until->format('d/m/Y H:i:s') }}</td>
                                        <td>
                                            @if($qr->isValid())
                                                <span class="badge bg-success">Aktif</span>
                                            @else
                                                <span class="badge bg-secondary">Kedaluwarsa</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-info">
                                                {{ $qr->attendance()->count() }} kali digunakan
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <h5>Belum Ada Riwayat</h5>
                            <p class="text-muted">Belum ada QR code yang pernah dibuat.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Auto refresh page every 30 seconds to update QR status
    setInterval(function() {
        location.reload();
    }, 30000);
</script>
@endpush