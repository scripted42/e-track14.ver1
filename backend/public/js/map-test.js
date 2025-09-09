// Simple Map Test JavaScript
console.log('Map Test JavaScript loaded');

// Simple map initialization
function initSimpleMap() {
    console.log('initSimpleMap() called');
    
    try {
        // Check if Leaflet is loaded
        if (typeof L === 'undefined') {
            console.error('Leaflet library not loaded');
            return;
        }
        
        console.log('Leaflet library loaded successfully');
        
        // Get map container
        const mapContainer = document.getElementById('map');
        if (!mapContainer) {
            console.error('Map container not found');
            return;
        }
        
        console.log('Map container found:', mapContainer);
        
        // Create map
        const map = L.map('map', {
            center: [-7.250445, 112.768845],
            zoom: 16
        });
        
        // Add tile layer
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);
        
        // Add marker
        L.marker([-7.250445, 112.768845]).addTo(map)
            .bindPopup('SMPN 14 Surabaya')
            .openPopup();
        
        // Add circle
        L.circle([-7.250445, 112.768845], {
            color: 'red',
            fillColor: '#f03',
            fillOpacity: 0.2,
            radius: 100
        }).addTo(map);
        
        // Show map and hide loading
        const mapLoading = document.getElementById('mapLoading');
        if (mapLoading) {
            mapLoading.style.display = 'none';
        }
        if (mapContainer) {
            mapContainer.style.display = 'block';
        }
        
        console.log('Simple map initialized successfully');
        
    } catch (error) {
        console.error('Error initializing simple map:', error);
    }
}

// Wait for Leaflet to load
function waitForLeaflet(attempts = 0) {
    console.log('Checking for Leaflet, attempt:', attempts + 1);
    
    if (typeof L !== 'undefined') {
        console.log('Leaflet found, initializing simple map');
        initSimpleMap();
    } else if (attempts < 50) { // Wait up to 5 seconds
        setTimeout(() => waitForLeaflet(attempts + 1), 100);
    } else {
        console.error('Leaflet failed to load after 5 seconds');
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM Content Loaded - Map Test');
    
    // Start waiting for Leaflet
    setTimeout(() => waitForLeaflet(), 1000);
});

// Make functions globally available
window.initSimpleMap = initSimpleMap;
window.waitForLeaflet = waitForLeaflet;
