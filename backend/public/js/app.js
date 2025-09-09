// E-Track14 Application JavaScript

// Basic app initialization
document.addEventListener('DOMContentLoaded', function() {
    console.log('E-Track14 Application loaded');
    
    // Initialize tooltips if Bootstrap is available
    if (typeof bootstrap !== 'undefined') {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }
    
    // Initialize any custom functionality here
    initializeApp();
});

function initializeApp() {
    // Add any custom initialization code here
    console.log('E-Track14 app initialized');
}

// Make functions globally available
window.initializeApp = initializeApp;