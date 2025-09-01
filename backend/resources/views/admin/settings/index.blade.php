@extends('admin.layouts.app')

@section('title', 'Pengaturan Sistem')

@push('styles')
<style>
.bg-light-info {
    background-color: #e7f3ff !important;
    border-left: 4px solid #0ea5e9;
}

.bg-light-secondary {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
}

.bg-light-success {
    background-color: #f0f9f4 !important;
    border-left: 4px solid #22c55e;
}

.form-control-lg {
    padding: 0.75rem 1rem;
    font-size: 1rem;
    border-radius: 0.5rem;
    border: 1px solid #e5e7eb;
    transition: all 0.3s ease;
}

.form-control-lg:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    transform: translateY(-1px);
}

.input-group-lg .input-group-text {
    padding: 0.75rem 1rem;
    font-size: 0.9rem;
    border-radius: 0 0.5rem 0.5rem 0;
    border: 1px solid #e5e7eb;
    border-left: none;
}

.card {
    border: none;
    border-radius: 1rem;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
}

.card-header {
    border-radius: 1rem 1rem 0 0 !important;
    border: none;
}

.card-footer {
    border-radius: 0 0 1rem 1rem !important;
}

.btn-lg {
    padding: 0.75rem 1.5rem;
    font-size: 1rem;
    border-radius: 0.5rem;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn-primary {
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    border: none;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
}

.btn-outline-primary {
    border-color: #3b82f6;
    color: #3b82f6;
}

.btn-outline-primary:hover {
    background: #3b82f6;
    border-color: #3b82f6;
    transform: translateY(-1px);
}

.form-text {
    font-size: 0.875rem;
    color: #6b7280;
    margin-top: 0.5rem;
}

.border-start {
    border-left-width: 4px !important;
}

.rounded-circle {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.card {
    animation: fadeInUp 0.6s ease-out;
}

.form-control:focus {
    animation: pulse 0.6s ease-in-out;
}

@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.02); }
    100% { transform: scale(1); }
}
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-cogs me-2"></i>Pengaturan Sistem
                    </h3>
                    <p class="mb-0 mt-1 opacity-75">Kelola pengaturan lokasi dan waktu absensi</p>
                </div>
                
                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('admin.settings.update') }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="card-body">
                        <div class="alert alert-info border-0 bg-light-info">
                            <i class="fas fa-info-circle me-2 text-info"></i>
                            <strong>Update Fleksibel:</strong> Anda dapat mengubah pengaturan individual. Kosongkan field untuk tetap menggunakan nilai yang ada.
                        </div>
                        
                        <div class="row">
                            <!-- Location Settings -->
                            <div class="col-md-6">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="bg-primary bg-gradient rounded-circle p-2 me-3">
                                        <i class="fas fa-map-marker-alt text-white"></i>
                                    </div>
                                    <h5 class="mb-0 text-primary fw-bold">Pengaturan Lokasi</h5>
                                </div>
                                
                                <div class="mb-4">
                                    <label for="latitude" class="form-label fw-semibold">
                                        <i class="fas fa-globe me-1 text-muted"></i>Latitude
                                    </label>
                                    <input type="number" 
                                           class="form-control form-control-lg @error('latitude') is-invalid @enderror" 
                                           id="latitude" 
                                           name="latitude" 
                                           value="{{ old('latitude', $settings->latitude) }}" 
                                           step="0.0000001"
                                           placeholder="Masukkan nilai latitude">
                                    @error('latitude')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text"><i class="fas fa-map-pin me-1"></i>Koordinat latitude lokasi sekolah</div>
                                </div>

                                <div class="mb-4">
                                    <label for="longitude" class="form-label fw-semibold">
                                        <i class="fas fa-globe me-1 text-muted"></i>Longitude
                                    </label>
                                    <input type="number" 
                                           class="form-control form-control-lg @error('longitude') is-invalid @enderror" 
                                           id="longitude" 
                                           name="longitude" 
                                           value="{{ old('longitude', $settings->longitude) }}" 
                                           step="0.0000001"
                                           placeholder="Masukkan nilai longitude">
                                    @error('longitude')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text"><i class="fas fa-map-pin me-1"></i>Koordinat longitude lokasi sekolah</div>
                                </div>

                                <div class="mb-4">
                                    <label for="radius" class="form-label fw-semibold">
                                        <i class="fas fa-circle-notch me-1 text-muted"></i>Radius Absensi (meter)
                                    </label>
                                    <div class="input-group input-group-lg">
                                        <input type="number" 
                                               class="form-control @error('radius') is-invalid @enderror" 
                                               id="radius" 
                                               name="radius" 
                                               value="{{ old('radius', $settings->radius) }}" 
                                               min="1" 
                                               max="1000"
                                               placeholder="Masukkan radius dalam meter">
                                        <span class="input-group-text bg-light">
                                            <i class="fas fa-ruler me-1"></i>meter
                                        </span>
                                        @error('radius')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-text"><i class="fas fa-info-circle me-1"></i>Area radius dimana absensi dapat dilakukan</div>
                                </div>
                            </div>

                            <!-- Time Settings -->
                            <div class="col-md-6">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="bg-success bg-gradient rounded-circle p-2 me-3">
                                        <i class="fas fa-clock text-white"></i>
                                    </div>
                                    <h5 class="mb-0 text-success fw-bold">Pengaturan Waktu</h5>
                                </div>
                                
                                <div class="mb-4">
                                    <label for="checkin_start" class="form-label fw-semibold">
                                        <i class="fas fa-sign-in-alt me-1 text-muted"></i>Waktu Mulai Check-in
                                    </label>
                                    <input type="time" 
                                           class="form-control form-control-lg @error('checkin_start') is-invalid @enderror" 
                                           id="checkin_start" 
                                           name="checkin_start" 
                                           value="{{ old('checkin_start') }}">
                                    @error('checkin_start')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Kosongkan untuk tetap menggunakan: <strong>{{ $settings->checkin_start }}</strong></div>
                                </div>

                                <div class="mb-4">
                                    <label for="checkin_end" class="form-label fw-semibold">
                                        <i class="fas fa-clock me-1 text-muted"></i>Waktu Akhir Check-in
                                    </label>
                                    <input type="time" 
                                           class="form-control form-control-lg @error('checkin_end') is-invalid @enderror" 
                                           id="checkin_end" 
                                           name="checkin_end" 
                                           value="{{ old('checkin_end') }}">
                                    @error('checkin_end')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Kosongkan untuk tetap menggunakan: <strong>{{ $settings->checkin_end }}</strong></div>
                                </div>

                                <div class="mb-4">
                                    <label for="checkout_start" class="form-label fw-semibold">
                                        <i class="fas fa-sign-out-alt me-1 text-muted"></i>Waktu Mulai Check-out
                                    </label>
                                    <input type="time" 
                                           class="form-control form-control-lg @error('checkout_start') is-invalid @enderror" 
                                           id="checkout_start" 
                                           name="checkout_start" 
                                           value="{{ old('checkout_start') }}">
                                    @error('checkout_start')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Kosongkan untuk tetap menggunakan: <strong>{{ $settings->checkout_start }}</strong></div>
                                </div>

                                <div class="mb-4">
                                    <label for="checkout_end" class="form-label fw-semibold">
                                        <i class="fas fa-clock me-1 text-muted"></i>Waktu Akhir Check-out
                                    </label>
                                    <input type="time" 
                                           class="form-control form-control-lg @error('checkout_end') is-invalid @enderror" 
                                           id="checkout_end" 
                                           name="checkout_end" 
                                           value="{{ old('checkout_end') }}">
                                    @error('checkout_end')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Kosongkan untuk tetap menggunakan: <strong>{{ $settings->checkout_end }}</strong></div>
                                </div>
                            </div>
                        </div>

                        <!-- Current Settings Display -->
                        <div class="row mt-5">
                            <div class="col-12">
                                <div class="card border-0 bg-light-secondary">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="bg-info bg-gradient rounded-circle p-2 me-3">
                                                <i class="fas fa-eye text-white"></i>
                                            </div>
                                            <h6 class="card-title mb-0 fw-bold text-info">Pengaturan Saat Ini</h6>
                                        </div>
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <div class="bg-white rounded p-3 border-start border-primary border-4">
                                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                                        <span class="text-muted small fw-semibold">LOKASI</span>
                                                        <i class="fas fa-map-marker-alt text-primary"></i>
                                                    </div>
                                                    <div class="mb-1">
                                                        <strong class="text-dark">Koordinat:</strong> 
                                                        <span class="text-muted">{{ $settings->latitude }}, {{ $settings->longitude }}</span>
                                                    </div>
                                                    <div>
                                                        <strong class="text-dark">Radius:</strong> 
                                                        <span class="badge bg-primary">{{ $settings->radius }} meter</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="bg-white rounded p-3 border-start border-success border-4">
                                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                                        <span class="text-muted small fw-semibold">WAKTU</span>
                                                        <i class="fas fa-clock text-success"></i>
                                                    </div>
                                                    <div class="mb-1">
                                                        <strong class="text-dark">Check-in:</strong> 
                                                        <span class="text-muted">{{ $settings->checkin_start }} - {{ $settings->checkin_end }}</span>
                                                    </div>
                                                    <div>
                                                        <strong class="text-dark">Check-out:</strong> 
                                                        <span class="text-muted">{{ $settings->checkout_start }} - {{ $settings->checkout_end }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer bg-light border-0 py-4">
                        <div class="d-flex flex-wrap gap-2 justify-content-between">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary btn-lg px-4">
                                    <i class="fas fa-save me-2"></i>Simpan Pengaturan
                                </button>
                                <button type="button" class="btn btn-outline-primary btn-lg" onclick="fillAllFields()">
                                    <i class="fas fa-edit me-2"></i>Isi Semua Field
                                </button>
                            </div>
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary btn-lg">
                                <i class="fas fa-arrow-left me-2"></i>Kembali ke Dashboard
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Auto-dismiss alerts after 5 seconds
setTimeout(function() {
    $('.alert').fadeOut('slow');
}, 5000);

// Fill all fields with current values
function fillAllFields() {
    document.getElementById('latitude').value = '{{ $settings->latitude }}';
    document.getElementById('longitude').value = '{{ $settings->longitude }}';
    document.getElementById('radius').value = '{{ $settings->radius }}';
    document.getElementById('checkin_start').value = '{{ $settings->checkin_start }}';
    document.getElementById('checkin_end').value = '{{ $settings->checkin_end }}';
    document.getElementById('checkout_start').value = '{{ $settings->checkout_start }}';
    document.getElementById('checkout_end').value = '{{ $settings->checkout_end }}';
    
    // Show confirmation with modern styling
    const alert = document.createElement('div');
    alert.className = 'alert alert-success alert-dismissible fade show mt-3 border-0 bg-light-success';
    alert.innerHTML = '<i class="fas fa-check-circle me-2 text-success"></i><strong>Berhasil!</strong> Semua field telah diisi dengan nilai saat ini. Anda dapat memodifikasi sesuai kebutuhan. <button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
    document.querySelector('.card-body').prepend(alert);
    
    // Auto dismiss after 4 seconds
    setTimeout(() => {
        if (alert.parentNode) {
            alert.remove();
        }
    }, 4000);
}

// Form validation with Indonesian messages
document.querySelector('form').addEventListener('submit', function(e) {
    const checkinStart = document.getElementById('checkin_start').value;
    const checkinEnd = document.getElementById('checkin_end').value;
    const checkoutStart = document.getElementById('checkout_start').value;
    const checkoutEnd = document.getElementById('checkout_end').value;
    
    // Only validate if fields are filled
    if (checkinStart && checkinEnd && checkinStart >= checkinEnd) {
        e.preventDefault();
        alert('Waktu mulai check-in harus lebih awal dari waktu akhir check-in');
        return false;
    }
    
    if (checkoutStart && checkoutEnd && checkoutStart >= checkoutEnd) {
        e.preventDefault();
        alert('Waktu mulai check-out harus lebih awal dari waktu akhir check-out');
        return false;
    }
    
    if (checkinEnd && checkoutStart && checkinEnd >= checkoutStart) {
        const confirmed = confirm('Waktu akhir check-in lebih dari waktu mulai check-out. Ini dapat menyebabkan overlap. Lanjutkan?');
        if (!confirmed) {
            e.preventDefault();
            return false;
        }
    }
    
    // Show loading state
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Menyimpan...';
    submitBtn.disabled = true;
});

// Add modern input interactions
document.addEventListener('DOMContentLoaded', function() {
    // Add focus effects to inputs
    const inputs = document.querySelectorAll('.form-control');
    inputs.forEach(input => {
        input.addEventListener('focus', function() {
            this.parentElement.style.transform = 'scale(1.02)';
            this.parentElement.style.transition = 'transform 0.2s ease';
        });
        
        input.addEventListener('blur', function() {
            this.parentElement.style.transform = 'scale(1)';
        });
    });
});
</script>
@endpush