// Simple E-Track14 Application JavaScript
console.log('Simple E-Track14 Application loaded');

// Basic initialization
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM Content Loaded - Simple App');
    
    // Initialize tooltips if Bootstrap is available
    if (typeof bootstrap !== 'undefined') {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }
    
    console.log('Simple app initialized successfully');
});

// Make functions globally available
window.simpleApp = {
    init: function() {
        console.log('Simple app init called');
    }
};
