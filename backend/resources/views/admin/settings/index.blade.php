@extends('admin.layouts.app')

@section('title', 'Pengaturan Sistem')

@push('styles')
<style>
.stats-card {
    background: white;
    border-radius: 8px;
    padding: 1.5rem;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    border: 1px solid #e9ecef;
}

.stats-icon {
    width: 50px;
    height: 50px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    margin-bottom: 1rem;
}

.stats-number {
    font-size: 2rem;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 0.25rem;
}

.stats-label {
    color: #6c757d;
    font-size: 0.875rem;
    font-weight: 500;
}

.btn-modern {
    border-radius: 6px;
    padding: 0.5rem 1.2rem;
    font-weight: 500;
    border: 1px solid #dee2e6;
    transition: all 0.2s ease;
    background: white;
    color: #6c757d;
}

.btn-modern:hover {
    background: #f8f9fa;
    border-color: #adb5bd;
    color: #495057;
}

.btn-primary {
    background: #007bff;
    color: white;
    border-color: #007bff;
}

.btn-primary:hover {
    background: #0056b3;
    border-color: #0056b3;
    color: white;
}

.btn-success {
    background: #28a745;
    color: white;
    border-color: #28a745;
}

.btn-success:hover {
    background: #1e7e34;
    border-color: #1e7e34;
    color: white;
}

.btn-secondary {
    background: #6c757d;
    color: white;
    border-color: #6c757d;
}

.btn-secondary:hover {
    background: #545b62;
    border-color: #545b62;
    color: white;
}

.form-control {
    border-radius: 6px;
    border: 1px solid #dee2e6;
    padding: 0.5rem 1rem;
    transition: all 0.2s ease;
}

.form-control:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
}

.main-card {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    border: 1px solid #e9ecef;
    overflow: hidden;
}

.card-header {
    background: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
    padding: 1.5rem;
}

.card-title {
    margin: 0;
    font-size: 1.25rem;
    font-weight: 600;
    color: #2c3e50;
}

.settings-form {
    background: white;
    border-radius: 8px;
    padding: 1.5rem;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    border: 1px solid #e9ecef;
    margin-bottom: 1.5rem;
}

.map-card {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    border: 1px solid #e9ecef;
    overflow: hidden;
    height: fit-content;
}

.map-container {
    padding: 1.5rem;
    height: 500px;
}

.map-container .leaflet-container {
    height: 100% !important;
    width: 100% !important;
    border-radius: 8px;
}

.map-info {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 1rem;
    margin-top: 1rem;
    border-left: 4px solid #007bff;
}

.alert-info {
    background: #d1ecf1;
    border: 1px solid #bee5eb;
    border-radius: 6px;
    border-left: 4px solid #17a2b8;
}

.alert-danger {
    background: #f8d7da;
    border: 1px solid #f5c6cb;
    border-radius: 6px;
    border-left: 4px solid #dc3545;
}

.alert-success {
    background: #d4edda;
    border: 1px solid #c3e6cb;
    border-radius: 6px;
    border-left: 4px solid #28a745;
}

.input-group-text {
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 6px 0 0 6px;
}

.loading-spinner {
    display: inline-block;
    width: 1rem;
    height: 1rem;
    border: 2px solid #ffffff;
    border-radius: 50%;
    border-top-color: transparent;
    animation: spin 1s ease-in-out infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Pengaturan Sistem</h1>
            <p class="text-muted mb-0">Kelola pengaturan lokasi dan waktu absensi sekolah</p>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Kembali ke Dashboard
        </a>
    </div>



    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>Terjadi kesalahan:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="stats-card">
                <div class="stats-icon" style="background: #fff3cd; color: #856404;">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stats-number">{{ $settings->checkin_start }} - {{ $settings->checkin_end }}</div>
                <div class="stats-label">Waktu Check-in</div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="stats-card">
                <div class="stats-icon" style="background: #f8d7da; color: #721c24;">
                    <i class="fas fa-sign-out-alt"></i>
                </div>
                <div class="stats-number">{{ $settings->checkout_start }} - {{ $settings->checkout_end }}</div>
                <div class="stats-label">Waktu Check-out</div>
            </div>
        </div>
    </div>

    <form action="{{ route('admin.settings.update') }}" method="POST" id="settingsForm">
        @csrf
        @method('PUT')
        
        <div class="row">
            <!-- Left Column: Settings Form -->
            <div class="col-md-6">
                <div class="main-card">
                    <div class="card-header">
                        <h5 class="card-title">
                            <i class="fas fa-cogs me-2"></i>Pengaturan Absensi
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Panduan:</strong> Anda dapat mengubah pengaturan individual sesuai kebutuhan. Kosongkan field untuk tetap menggunakan nilai yang ada.
                        </div>
                        
                        <!-- Location Settings -->
                        <div class="mb-4">
                            <h6 class="fw-bold text-dark mb-3">
                                <i class="fas fa-map-marker-alt me-2 text-primary"></i>
                                Pengaturan Lokasi
                            </h6>
                            
                            <div class="form-group mb-3">
                                <label for="latitude" class="form-label">Latitude</label>
                                <input type="number" 
                                       class="form-control @error('latitude') is-invalid @enderror" 
                                       id="latitude" 
                                       name="latitude" 
                                       value="{{ old('latitude', $settings->latitude) }}" 
                                       step="0.0000001"
                                       min="-90"
                                       max="90"
                                       placeholder="Masukkan nilai latitude (maksimal 7 digit setelah titik)"
                                       required>
                                @error('latitude')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Koordinat latitude lokasi sekolah (format: -7.xxxxxxx)</small>
                            </div>

                            <div class="form-group mb-3">
                                <label for="longitude" class="form-label">Longitude</label>
                                <input type="number" 
                                       class="form-control @error('longitude') is-invalid @enderror" 
                                       id="longitude" 
                                       name="longitude" 
                                       value="{{ old('longitude', $settings->longitude) }}" 
                                       step="0.0000001"
                                       min="-180"
                                       max="180"
                                       placeholder="Masukkan nilai longitude (maksimal 7 digit setelah titik)"
                                       required>
                                @error('longitude')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Koordinat longitude lokasi sekolah (format: 112.xxxxxxx)</small>
                            </div>

                            <div class="form-group mb-3">
                                <label for="radius" class="form-label">Radius Absensi (meter)</label>
                                <div class="input-group">
                                    <input type="number" 
                                           class="form-control @error('radius') is-invalid @enderror" 
                                           id="radius" 
                                           name="radius" 
                                           value="{{ old('radius', $settings->radius) }}" 
                                           min="1" 
                                           max="1000"
                                           placeholder="Masukkan radius dalam meter">
                                    <span class="input-group-text">
                                        <i class="fas fa-ruler me-1"></i>meter
                                    </span>
                                    @error('radius')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <small class="text-muted">Area radius dimana absensi dapat dilakukan</small>
                            </div>
                        </div>

                        <!-- Time Settings -->
                        <div class="mb-4">
                            <h6 class="fw-bold text-dark mb-3">
                                <i class="fas fa-clock me-2 text-primary"></i>
                                Pengaturan Waktu
                            </h6>
                            
                            <div class="form-group mb-3">
                                <label for="checkin_start" class="form-label">Waktu Mulai Check-in</label>
                                <input type="time" 
                                       class="form-control @error('checkin_start') is-invalid @enderror" 
                                       id="checkin_start" 
                                       name="checkin_start" 
                                       value="{{ old('checkin_start') }}">
                                @error('checkin_start')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Kosongkan untuk tetap menggunakan: <strong>{{ $settings->checkin_start }}</strong></small>
                            </div>

                            <div class="form-group mb-3">
                                <label for="checkin_end" class="form-label">Waktu Akhir Check-in</label>
                                <input type="time" 
                                       class="form-control @error('checkin_end') is-invalid @enderror" 
                                       id="checkin_end" 
                                       name="checkin_end" 
                                       value="{{ old('checkin_end') }}">
                                @error('checkin_end')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Kosongkan untuk tetap menggunakan: <strong>{{ $settings->checkin_end }}</strong></small>
                            </div>

                            <div class="form-group mb-3">
                                <label for="checkout_start" class="form-label">Waktu Mulai Check-out</label>
                                <input type="time" 
                                       class="form-control @error('checkout_start') is-invalid @enderror" 
                                       id="checkout_start" 
                                       name="checkout_start" 
                                       value="{{ old('checkout_start') }}">
                                @error('checkout_start')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Kosongkan untuk tetap menggunakan: <strong>{{ $settings->checkout_start }}</strong></small>
                            </div>

                            <div class="form-group mb-3">
                                <label for="checkout_end" class="form-label">Waktu Akhir Check-out</label>
                                <input type="time" 
                                       class="form-control @error('checkout_end') is-invalid @enderror" 
                                       id="checkout_end" 
                                       name="checkout_end" 
                                       value="{{ old('checkout_end') }}">
                                @error('checkout_end')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Kosongkan untuk tetap menggunakan: <strong>{{ $settings->checkout_end }}</strong></small>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex flex-wrap gap-3 justify-content-between mt-4 pt-3 border-top">
                            <div class="d-flex gap-3">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Simpan Pengaturan
                                </button>
                                <button type="button" class="btn btn-modern" onclick="fillAllFields()">
                                    <i class="fas fa-edit me-2"></i>Isi Semua Field
                                </button>
                            </div>
                            <div class="text-muted small">
                                <i class="fas fa-info-circle me-1"></i>
                                Perubahan akan langsung diterapkan
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Map Display -->
            <div class="col-md-6">
                <div class="map-card">
                    <div class="card-header">
                        <h5 class="card-title">
                            <i class="fas fa-map me-2"></i>Peta Lokasi Absensi
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="loading-spinner text-center p-4" id="mapLoading">
                            <i class="fas fa-spinner fa-spin fa-2x mb-2 text-primary"></i>
                            <h6 class="text-muted">Memuat peta...</h6>
                            <p class="text-muted small">Sedang mengambil data lokasi</p>
                        </div>
                        
                        <div id="map" class="map-container" style="display: none;"></div>
                        
                        <div class="map-info">
                            <h6 class="fw-bold text-dark mb-3">
                                <i class="fas fa-info-circle me-2 text-primary"></i>
                                Informasi Lokasi
                            </h6>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="fw-medium">
                                    <i class="fas fa-globe me-2"></i>Koordinat:
                                </span>
                                <span class="text-primary fw-bold" id="currentCoords">{{ $settings->latitude }}, {{ $settings->longitude }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="fw-medium">
                                    <i class="fas fa-circle-notch me-2"></i>Radius:
                                </span>
                                <span class="text-primary fw-bold" id="currentRadius">{{ $settings->radius }} meter</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-medium">
                                    <i class="fas fa-check-circle me-2"></i>Status:
                                </span>
                                <span class="badge bg-success" id="mapStatus">Aktif</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

<!-- Leaflet JavaScript -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
let map;
let marker;
let circle;

// Initialize map
function initMap() {
    try {
        const latitude = parseFloat('{{ $settings->latitude }}') || -7.250445;
        const longitude = parseFloat('{{ $settings->longitude }}') || 112.768845;
        const radius = parseInt('{{ $settings->radius }}') || 100;
        
        console.log('Initializing map with coordinates:', latitude, longitude, 'radius:', radius);
        
        // Create map
        map = L.map('map', {
            center: [latitude, longitude],
            zoom: 16,
            zoomControl: true
        });
        
        // Add tile layer with error handling and fallback
        const tileLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors',
            maxZoom: 19,
            errorTileUrl: 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg==',
            subdomains: ['a', 'b', 'c']
        });
        
        // Add fallback tile layer
        const fallbackTileLayer = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors',
            maxZoom: 19
        });
        
        tileLayer.addTo(map);
        
        // If main tile layer fails, try fallback
        tileLayer.on('tileerror', function() {
            console.log('Main tile layer failed, trying fallback...');
            map.removeLayer(tileLayer);
            fallbackTileLayer.addTo(map);
        });
        
        // Add marker
        marker = L.marker([latitude, longitude], {
            icon: L.divIcon({
                className: 'custom-marker',
                html: '<div style="background: #007bff; color: white; border-radius: 50%; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; font-size: 14px; font-weight: bold; border: 3px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.3);"><i class="fas fa-school"></i></div>',
                iconSize: [30, 30],
                iconAnchor: [15, 15]
            })
        }).addTo(map);
        
        // Add circle for radius
        circle = L.circle([latitude, longitude], {
            color: '#007bff',
            fillColor: '#007bff',
            fillOpacity: 0.2,
            radius: radius,
            weight: 2
        }).addTo(map);
        
        // Add popup
        marker.bindPopup(`
            <div style="text-align: center;">
                <h6 style="margin: 0 0 5px 0; color: #007bff;"><i class="fas fa-school"></i> Lokasi Sekolah</h6>
                <p style="margin: 0; font-size: 12px; color: #666;">
                    Lat: ${latitude.toFixed(6)}<br>
                    Lng: ${longitude.toFixed(6)}<br>
                    Radius: ${radius} meter
                </p>
            </div>
        `).openPopup();
        
        // Force map to resize and invalidate size
        setTimeout(() => {
            map.invalidateSize();
            console.log('Map invalidated and resized');
        }, 100);
        
        // Hide loading and show map
        document.getElementById('mapLoading').style.display = 'none';
        document.getElementById('map').style.display = 'block';
        
        console.log('Map initialized successfully');
        
    } catch (error) {
        console.error('Error initializing map:', error);
        document.getElementById('mapLoading').innerHTML = '<i class="fas fa-exclamation-triangle fa-2x text-danger mb-2"></i><p class="text-danger">Gagal memuat peta. Silakan refresh halaman.</p>';
    }
}

// Update map when form values change
function updateMap() {
    const latitude = parseFloat(document.getElementById('latitude').value) || -7.250445;
    const longitude = parseFloat(document.getElementById('longitude').value) || 112.768845;
    const radius = parseInt(document.getElementById('radius').value) || 100;
    
    console.log('Updating map with coordinates:', latitude, longitude, 'radius:', radius);
    
    if (map && marker && circle) {
        try {
            // Update map center
            map.setView([latitude, longitude], map.getZoom());
            
            // Update marker position
            marker.setLatLng([latitude, longitude]);
            
            // Update circle
            circle.setLatLng([latitude, longitude]);
            circle.setRadius(radius);
            
            // Update popup
            marker.bindPopup(`
                <div style="text-align: center;">
                    <h6 style="margin: 0 0 5px 0; color: #007bff;"><i class="fas fa-school"></i> Lokasi Sekolah</h6>
                    <p style="margin: 0; font-size: 12px; color: #666;">
                        Lat: ${latitude.toFixed(6)}<br>
                        Lng: ${longitude.toFixed(6)}<br>
                        Radius: ${radius} meter
                    </p>
                </div>
            `);
            
            // Update info display
            document.getElementById('currentCoords').textContent = `${latitude.toFixed(6)}, ${longitude.toFixed(6)}`;
            document.getElementById('currentRadius').textContent = `${radius} meter`;
            
            // Force map to invalidate size after update
            setTimeout(() => {
                map.invalidateSize();
            }, 50);
            
            console.log('Map updated successfully');
            
        } catch (error) {
            console.error('Error updating map:', error);
        }
    } else {
        console.warn('Map, marker, or circle not initialized yet');
    }
}

// Helper function to show alert
function showAlert(message, type = 'info') {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        <i class="fas fa-${type === 'danger' ? 'exclamation-triangle' : 'info-circle'} me-2"></i>
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    // Insert at the top of the form
    const form = document.getElementById('settingsForm');
    form.parentNode.insertBefore(alertDiv, form);
    
    // Auto dismiss after 5 seconds
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 5000);
}

// Helper function to scroll to top smoothly
function scrollToTop() {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
}

// Fill all fields with current values
function fillAllFields() {
    document.getElementById('latitude').value = '{{ $settings->latitude }}';
    document.getElementById('longitude').value = '{{ $settings->longitude }}';
    document.getElementById('radius').value = '{{ $settings->radius }}';
    document.getElementById('checkin_start').value = '{{ $settings->checkin_start }}';
    document.getElementById('checkin_end').value = '{{ $settings->checkin_end }}';
    document.getElementById('checkout_start').value = '{{ $settings->checkout_start }}';
    document.getElementById('checkout_end').value = '{{ $settings->checkout_end }}';
    
    // Update map
    updateMap();
    
    // Show success message
    const alert = document.createElement('div');
    alert.className = 'alert alert-info alert-dismissible fade show mt-3';
    alert.innerHTML = '<i class="fas fa-check-circle me-2"></i><strong>Berhasil!</strong> Semua field telah diisi dengan nilai saat ini. Anda dapat memodifikasi sesuai kebutuhan. <button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
    document.querySelector('.card-body').prepend(alert);
    
    // Auto dismiss after 4 seconds
    setTimeout(() => {
        if (alert.parentNode) {
            alert.remove();
        }
    }, 4000);
}

// Form validation
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('settingsForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            console.log('Form submit event triggered');
            
            const latitude = document.getElementById('latitude').value;
            const longitude = document.getElementById('longitude').value;
            const radius = document.getElementById('radius').value;
            const checkinStart = document.getElementById('checkin_start').value;
            const checkinEnd = document.getElementById('checkin_end').value;
            const checkoutStart = document.getElementById('checkout_start').value;
            const checkoutEnd = document.getElementById('checkout_end').value;
            
            console.log('Form values:', {
                latitude, longitude, radius, checkinStart, checkinEnd, checkoutStart, checkoutEnd
            });
            
            // Check if location fields are filled (only if user is trying to change them)
            if ((latitude && !longitude) || (!latitude && longitude)) {
                e.preventDefault();
                showAlert('Latitude dan Longitude harus diisi bersamaan', 'danger');
                scrollToTop();
                return false;
            }
            
            // Validate coordinate format (max 7 decimal places)
            if (latitude) {
                const latStr = latitude.toString();
                const latParts = latStr.split('.');
                if (latParts.length > 1 && latParts[1].length > 7) {
                    e.preventDefault();
                    showAlert('Latitude maksimal 7 digit setelah titik desimal (contoh: -7.1234567)', 'danger');
                    scrollToTop();
                    return false;
                }
            }
            
            if (longitude) {
                const lngStr = longitude.toString();
                const lngParts = lngStr.split('.');
                if (lngParts.length > 1 && lngParts[1].length > 7) {
                    e.preventDefault();
                    showAlert('Longitude maksimal 7 digit setelah titik desimal (contoh: 112.1234567)', 'danger');
                    scrollToTop();
                    return false;
                }
            }
            
            // Only validate time fields if they are filled
            if (checkinStart && checkinEnd && checkinStart >= checkinEnd) {
                e.preventDefault();
                showAlert('Waktu mulai check-in harus lebih awal dari waktu akhir check-in', 'danger');
                scrollToTop();
                return false;
            }
            
            if (checkoutStart && checkoutEnd && checkoutStart >= checkoutEnd) {
                e.preventDefault();
                showAlert('Waktu mulai check-out harus lebih awal dari waktu akhir check-out', 'danger');
                scrollToTop();
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
            
            console.log('Form submitting...');
            
            // Don't prevent default - let form submit normally
        });
    }
});

// Add event listeners for real-time map updates
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM Content Loaded');
    
    // Wait a bit for the page to fully load before initializing map
    setTimeout(() => {
        initMap();
    }, 200);
    
    // Add event listeners to form inputs
    const locationInputs = ['latitude', 'longitude', 'radius'];
    locationInputs.forEach(inputId => {
        const input = document.getElementById(inputId);
        if (input) {
            input.addEventListener('input', updateMap);
            console.log('Added input listener for:', inputId);
        }
    });
    
    // Handle window resize to fix map display issues
    window.addEventListener('resize', function() {
        if (map) {
            setTimeout(() => {
                map.invalidateSize();
            }, 100);
        }
    });
    

    
    // Test form submission
    const form = document.getElementById('settingsForm');
    if (form) {
        console.log('Form found:', form);
        
        // Add click listener to submit button
        const submitBtn = form.querySelector('button[type="submit"]');
        if (submitBtn) {
            submitBtn.addEventListener('click', function(e) {
                console.log('Submit button clicked');
                console.log('Form data:', new FormData(form));
            });
        }
    }
});

// Auto-dismiss alerts after 5 seconds
setTimeout(function() {
    const alerts = document.querySelectorAll('.alert:not(.alert-dismissible)');
    alerts.forEach(alert => {
        alert.style.transition = 'opacity 0.5s ease';
        alert.style.opacity = '0';
        setTimeout(() => {
            if (alert.parentNode) {
                alert.remove();
            }
        }, 500);
    });
}, 5000);
</script>
@endpush