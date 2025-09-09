// Map Radius Fix JavaScript - E-Track14
console.log('Map Radius Fix JavaScript loaded');

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
        
        // Get coordinates from form inputs or use defaults
        const latitudeInput = document.getElementById('latitude');
        const longitudeInput = document.getElementById('longitude');
        const radiusInput = document.getElementById('radius');
        
        const latitude = latitudeInput ? parseFloat(latitudeInput.value) || -7.2374729 : -7.2374729;
        const longitude = longitudeInput ? parseFloat(longitudeInput.value) || 112.6087911 : 112.6087911;
        const radius = radiusInput ? parseInt(radiusInput.value) || 100 : 100;
        
        console.log('Map coordinates:', { latitude, longitude, radius });
        
        // Get map container
        const mapContainer = document.getElementById('map');
        if (!mapContainer) {
            console.error('Map container not found');
            return;
        }
        
        console.log('Map container found:', mapContainer);
        
        // Create map
        map = L.map('map', {
            center: [latitude, longitude],
            zoom: 16,
            zoomControl: true
        });
        console.log('Map created successfully:', map);

        // Create a dedicated pane for radius so it's always on top of tiles/overlays
        if (!map.getPane('radiusPane')) {
            map.createPane('radiusPane');
            map.getPane('radiusPane').style.zIndex = 650; // above markerPane (600)
        }
        
        // Add tile layer
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors',
            maxZoom: 19,
        }).addTo(map);
        
        // Add marker
        marker = L.marker([latitude, longitude]).addTo(map);
        console.log('Marker added:', marker);
        
        // Add circle for radius - EXPLICIT CREATION (blue) in dedicated pane
        circle = L.circle([latitude, longitude], {
            color: '#007bff',
            fillColor: '#007bff',
            fillOpacity: 0.25,
            radius: radius,
            weight: 4,
            opacity: 1,
            stroke: true,
            fill: true,
            pane: 'radiusPane'
        });
        
        // Add circle to map explicitly
        map.addLayer(circle);
        console.log('Circle added with radius:', radius, 'meters');
        
        // Verify circle was added
        if (circle) {
            console.log('Circle successfully added to map');
            console.log('Circle center:', circle.getLatLng());
            console.log('Circle radius:', circle.getRadius());
            console.log('Circle bounds:', circle.getBounds());
            
            // Force circle to be visible after a delay
            setTimeout(() => {
                if (circle) {
                    circle.setStyle({
                        color: '#007bff',
                        fillColor: '#007bff',
                        fillOpacity: 0.4,
                        weight: 4,
                        opacity: 1,
                        stroke: true,
                        fill: true
                    });
                    if (circle.bringToFront) circle.bringToFront();
                    if (circle.redraw) circle.redraw();
                    console.log('Circle style forced for visibility');
                }
            }, 1000);
        } else {
            console.error('Failed to add circle to map');
        }
        
        // Ensure circle is above tiles and marker
        if (circle && circle.bringToFront) {
            circle.bringToFront();
        }
        
        // Show map and hide loading
        const mapLoading = document.getElementById('mapLoading');
        if (mapLoading) {
            mapLoading.style.display = 'none';
        }
        if (mapContainer) {
            mapContainer.style.display = 'block';
            console.log('Map container displayed');
            // Force map resize
            setTimeout(() => {
                if (map) {
                    map.invalidateSize();
                    console.log('Map invalidated size');
                }
            }, 100);
        }
        
        // Update info display
        updateInfoDisplay(latitude, longitude, radius);
        
        console.log('Map initialization completed successfully');
        
    } catch (error) {
        console.error('Error initializing map:', error);
        const mapLoading = document.getElementById('mapLoading');
        if (mapLoading) {
            mapLoading.innerHTML = '<div class="text-center p-4"><i class="fas fa-exclamation-triangle fa-2x text-danger mb-2"></i><p class="text-danger">Gagal memuat peta: ' + error.message + '</p><button class="btn btn-sm btn-primary mt-2" onclick="initMap()">Coba Lagi</button></div>';
        }
    }
}

// Update info display
function updateInfoDisplay(latitude, longitude, radius) {
    const currentCoords = document.getElementById('currentCoords');
    const currentRadius = document.getElementById('currentRadius');
    const mapStatus = document.getElementById('mapStatus');
    
    if (currentCoords) {
        currentCoords.textContent = `${latitude.toFixed(6)}, ${longitude.toFixed(6)}`;
    }
    if (currentRadius) {
        currentRadius.textContent = `${radius} meter`;
    }
    if (mapStatus) {
        mapStatus.textContent = 'Aktif';
        mapStatus.className = 'badge bg-success';
    }
}

// Update map when form values change
function updateMap() {
    if (map && marker && circle) {
        const latitudeInput = document.getElementById('latitude');
        const longitudeInput = document.getElementById('longitude');
        const radiusInput = document.getElementById('radius');
        
        const latitude = latitudeInput ? parseFloat(latitudeInput.value) || -7.2374729 : -7.2374729;
        const longitude = longitudeInput ? parseFloat(longitudeInput.value) || 112.6087911 : 112.6087911;
        const radius = radiusInput ? parseInt(radiusInput.value) || 100 : 100;
        
        console.log('Updating map with:', { latitude, longitude, radius });
        
        // Update marker position
        marker.setLatLng([latitude, longitude]);
        
        // Update circle position and radius
        circle.setLatLng([latitude, longitude]);
        circle.setRadius(radius);
        
        console.log('Circle updated - new radius:', circle.getRadius());
        
        // Keep circle on top
        if (circle && circle.bringToFront) {
            circle.bringToFront();
        }
        
        // Update map center
        map.setView([latitude, longitude], map.getZoom());
        
        // Update info display
        updateInfoDisplay(latitude, longitude, radius);
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
    console.log('DOM Content Loaded - Map Radius Fix');
    
    // Start waiting for Leaflet
    setTimeout(() => waitForLeaflet(), 1000);
    
    // Add event listeners to form inputs
    const locationInputs = ['latitude', 'longitude', 'radius'];
    locationInputs.forEach(inputId => {
        const input = document.getElementById(inputId);
        if (input) {
            input.addEventListener('input', updateMap);
        }
    });
    
    // Handle window resize
    window.addEventListener('resize', function() {
        if (map) {
            setTimeout(() => {
                map.invalidateSize();
            }, 100);
        }
    });
});

// Make functions globally available
window.initMap = initMap;
window.updateMap = updateMap;
window.waitForLeaflet = waitForLeaflet;
