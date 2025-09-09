// Settings Map JavaScript - E-Track14
console.log('Settings Map JavaScript loaded');

let map;
let marker;
let circle;

// Initialize map function
function initMap() {
    console.log('initMap() called');
    try {
        // Check if Leaflet is loaded
        if (typeof L === 'undefined') {
            console.error('Leaflet library not loaded');
            const mapLoading = document.getElementById('mapLoading');
            if (mapLoading) {
                mapLoading.innerHTML = '<div class="text-center p-4"><i class="fas fa-exclamation-triangle fa-2x text-danger mb-2"></i><p class="text-danger">Leaflet library tidak dimuat. Silakan refresh halaman.</p></div>';
            }
            return;
        }
        
        console.log('Leaflet library loaded successfully');
        
        // Get coordinates from form or use defaults
        const latitude = parseFloat(document.getElementById('latitude')?.value) || -7.250445;
        const longitude = parseFloat(document.getElementById('longitude')?.value) || 112.768845;
        const radius = parseInt(document.getElementById('radius')?.value) || 100;
        
        console.log('Map coordinates:', { latitude, longitude, radius });
        
        // Create map
        console.log('Creating map with container:', document.getElementById('map'));
        map = L.map('map', {
            center: [latitude, longitude],
            zoom: 16,
            zoomControl: true
        });
        console.log('Map created successfully:', map);
        
        // Add tile layer
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors',
            maxZoom: 19,
        }).addTo(map);
        
        // Add marker
        marker = L.marker([latitude, longitude]).addTo(map);
        
        // Add circle
        circle = L.circle([latitude, longitude], {
            color: 'red',
            fillColor: '#f03',
            fillOpacity: 0.2,
            radius: radius
        }).addTo(map);
        
        // Show map and hide loading
        const mapLoading = document.getElementById('mapLoading');
        const mapElement = document.getElementById('map');
        
        if (mapLoading) {
            mapLoading.style.display = 'none';
        }
        if (mapElement) {
            mapElement.style.display = 'block';
            // Force map resize
            setTimeout(() => {
                if (map) {
                    map.invalidateSize();
                }
            }, 100);
        }
        
        console.log('Map initialization completed successfully');
        
    } catch (error) {
        console.error('Error initializing map:', error);
        const mapLoading = document.getElementById('mapLoading');
        if (mapLoading) {
            mapLoading.innerHTML = '<div class="text-center p-4"><i class="fas fa-exclamation-triangle fa-2x text-danger mb-2"></i><p class="text-danger">Gagal memuat peta: ' + error.message + '</p><button class="btn btn-sm btn-primary mt-2" onclick="initMap()">Coba Lagi</button></div>';
        }
    }
}

// Update map when form values change
function updateMap() {
    if (map && marker && circle) {
        const latitude = parseFloat(document.getElementById('latitude')?.value) || -7.250445;
        const longitude = parseFloat(document.getElementById('longitude')?.value) || 112.768845;
        const radius = parseInt(document.getElementById('radius')?.value) || 100;
        
        // Update marker position
        marker.setLatLng([latitude, longitude]);
        
        // Update circle position and radius
        circle.setLatLng([latitude, longitude]);
        circle.setRadius(radius);
        
        // Update map center
        map.setView([latitude, longitude], map.getZoom());
    }
}

// Wait for Leaflet to load
function waitForLeaflet(attempts = 0) {
    console.log('Checking for Leaflet, attempt:', attempts + 1);
    
    if (typeof L !== 'undefined') {
        console.log('Leaflet found, initializing map');
        initMap();
    } else if (attempts < 50) { // Wait up to 5 seconds
        setTimeout(() => waitForLeaflet(attempts + 1), 100);
    } else {
        console.error('Leaflet failed to load after 5 seconds');
        // Show fallback message
        const mapLoading = document.getElementById('mapLoading');
        if (mapLoading) {
            mapLoading.innerHTML = `
                <div class="text-center p-4">
                    <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                    <h6 class="text-warning">Peta Tidak Dapat Dimuat</h6>
                    <p class="text-muted small mb-3">
                        Leaflet library tidak dapat dimuat. Silakan coba refresh halaman.
                    </p>
                    <button class="btn btn-sm btn-primary" onclick="location.reload()">
                        <i class="fas fa-refresh me-1"></i> Refresh Halaman
                    </button>
                </div>
            `;
        }
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM Content Loaded - Settings Map');
    
    // Start waiting for Leaflet
    setTimeout(() => waitForLeaflet(), 1000);
    
    // Add event listeners to form inputs
    const locationInputs = ['latitude', 'longitude', 'radius'];
    locationInputs.forEach(inputId => {
        const input = document.getElementById(inputId);
        if (input) {
            input.addEventListener('input', function() {
                if (map && marker && circle) {
                    const lat = parseFloat(document.getElementById('latitude').value) || -7.250445;
                    const lng = parseFloat(document.getElementById('longitude').value) || 112.768845;
                    const rad = parseInt(document.getElementById('radius').value) || 100;
                    
                    // Update marker position
                    marker.setLatLng([lat, lng]);
                    
                    // Update circle position and radius
                    circle.setLatLng([lat, lng]);
                    circle.setRadius(rad);
                    
                    // Update map center
                    map.setView([lat, lng], map.getZoom());
                }
            });
        }
    });
});

// Make functions globally available
window.initMap = initMap;
window.waitForLeaflet = waitForLeaflet;
