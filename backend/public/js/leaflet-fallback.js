// Leaflet.js Fallback for Offline/Network Issues
// This file provides a simple fallback if Leaflet CDN fails

window.LeafletFallback = {
    init: function() {
        // Check if Leaflet is loaded
        if (typeof L === 'undefined') {
            console.warn('Leaflet not loaded, using fallback');
            this.showFallbackMessage();
        }
    },
    
    showFallbackMessage: function() {
        const mapContainer = document.getElementById('map');
        const mapLoading = document.getElementById('mapLoading');
        
        if (mapLoading) {
            mapLoading.innerHTML = `
                <div class="text-center p-4">
                    <i class="fas fa-map-marked-alt fa-3x text-muted mb-3"></i>
                    <h6 class="text-muted">Peta Tidak Tersedia</h6>
                    <p class="text-muted small mb-3">
                        Peta tidak dapat dimuat karena masalah koneksi atau CDN tidak dapat diakses.
                    </p>
                    <div class="alert alert-info">
                        <strong>Koordinat Saat Ini:</strong><br>
                        Latitude: <span id="fallback-lat">-7.250445</span><br>
                        Longitude: <span id="fallback-lng">112.768845</span><br>
                        Radius: <span id="fallback-radius">100</span> meter
                    </div>
                    <button class="btn btn-sm btn-primary" onclick="location.reload()">
                        <i class="fas fa-refresh me-1"></i> Refresh Halaman
                    </button>
                </div>
            `;
        }
        
        // Update fallback coordinates
        this.updateFallbackCoordinates();
    },
    
    updateFallbackCoordinates: function() {
        const latInput = document.getElementById('latitude');
        const lngInput = document.getElementById('longitude');
        const radiusInput = document.getElementById('radius');
        
        if (latInput) {
            document.getElementById('fallback-lat').textContent = latInput.value || '-7.250445';
            latInput.addEventListener('input', () => {
                document.getElementById('fallback-lat').textContent = latInput.value;
            });
        }
        
        if (lngInput) {
            document.getElementById('fallback-lng').textContent = lngInput.value || '112.768845';
            lngInput.addEventListener('input', () => {
                document.getElementById('fallback-lng').textContent = lngInput.value;
            });
        }
        
        if (radiusInput) {
            document.getElementById('fallback-radius').textContent = radiusInput.value || '100';
            radiusInput.addEventListener('input', () => {
                document.getElementById('fallback-radius').textContent = radiusInput.value;
            });
        }
    }
};

// Initialize fallback when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(() => {
        LeafletFallback.init();
    }, 2000); // Wait 2 seconds for Leaflet to load
});
