<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>QR Code Absensi - E-Track14</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- QRCode.js Library -->
    <script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.3/build/qrcode.min.js"></script>
    
    <!-- Custom CSS -->
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            height: 100vh;
            overflow: hidden;
        }
        
        .qr-display-container {
            height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: white;
            text-align: center;
        }
        
        .school-header {
            margin-bottom: 2rem;
        }
        
        .school-logo {
            width: 120px;
            height: 120px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }
        
        .school-name {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }
        
        .school-subtitle {
            font-size: 1.2rem;
            opacity: 0.9;
            margin-bottom: 0;
        }
        
        .qr-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 25px;
            padding: 3rem;
            box-shadow: 0 25px 80px rgba(0,0,0,0.15), 0 0 0 1px rgba(255,255,255,0.2);
            margin: 2rem 0;
            min-width: 500px;
        }
        
        .qr-code-container {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            display: inline-block;
            margin: 1rem 0;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        
        .qr-code-container canvas,
        .qr-code-container img {
            border-radius: 8px;
        }
        
        .qr-info {
            color: #333;
            margin-top: 1.5rem;
        }
        
        .countdown-container {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 15px;
            padding: 1.5rem;
            margin-top: 2rem;
        }
        
        .countdown-timer {
            font-size: 3rem;
            font-weight: bold;
            color: #FFD700;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
        }
        
        .status-indicator {
            position: fixed;
            top: 20px;
            right: 20px;
            background: rgba(0, 0, 0, 0.7);
            color: white;
            padding: 10px 20px;
            border-radius: 25px;
        }
        
        .instructions {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            padding: 2rem;
            margin-top: 2rem;
        }
        
        .instruction-step {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
            font-size: 1.1rem;
        }
        
        .step-number {
            background: #FFD700;
            color: #333;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            margin-right: 1rem;
        }
        
        .footer-info {
            position: fixed;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(0, 0, 0, 0.7);
            color: white;
            padding: 10px 30px;
            border-radius: 25px;
        }
        
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
        
        .qr-code-container.updating {
            animation: pulse 0.5s ease-in-out;
        }
        
        .loading-spinner {
            display: none;
            color: #007bff;
        }
    </style>
</head>
<body>
    <!-- Status Indicator -->
    <div class="status-indicator">
        <i class="fas fa-wifi"></i>
        <span id="connection-status">Terhubung</span>
    </div>

    <!-- Main Content -->
    <div class="qr-display-container">
        <!-- School Header -->
        <div class="school-header">
            <div class="school-logo">
                <i class="fas fa-graduation-cap fa-3x text-primary"></i>
            </div>
            <div class="school-name">SMPN 14 SURABAYA</div>
            <div class="school-subtitle">Sistem Absensi Digital</div>
        </div>

        <!-- QR Code Card -->
        <div class="qr-card">
            <h2 class="text-dark mb-3">
                <i class="fas fa-qrcode me-2"></i>
                Scan QR Code untuk Absensi
            </h2>
            
            <div id="qr-code-container" class="qr-code-container">
                <div class="loading-spinner">
                    <i class="fas fa-spinner fa-spin fa-3x"></i>
                    <p class="mt-2">Memuat QR Code...</p>
                </div>
                <div id="qr-content">
                    <!-- QR Code will be loaded here -->
                </div>
            </div>
            
            <div class="qr-info">
                <p class="mb-1"><strong>Berlaku sampai:</strong> <span id="qr-valid-until">-</span></p>
                <p class="mb-0"><strong>Diperbarui:</strong> <span id="last-updated">-</span></p>
            </div>
        </div>

        <!-- Countdown -->
        <div class="countdown-container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h5 class="mb-0">Pembaruan Otomatis</h5>
                    <small class="opacity-75">QR Code akan diperbarui dalam:</small>
                </div>
                <div class="col-md-6">
                    <div class="countdown-timer" id="countdown">10</div>
                </div>
            </div>
        </div>

        <!-- Instructions -->
        <div class="instructions">
            <h5 class="mb-3">
                <i class="fas fa-mobile-alt me-2"></i>
                Cara Melakukan Absensi
            </h5>
            <div class="instruction-step">
                <div class="step-number">1</div>
                <span>Buka aplikasi E-Track14 di smartphone Anda</span>
            </div>
            <div class="instruction-step">
                <div class="step-number">2</div>
                <span>Ambil foto selfie dengan GPS aktif dalam radius sekolah</span>
            </div>
            <div class="instruction-step">
                <div class="step-number">3</div>
                <span>Scan QR Code yang ditampilkan di monitor ini</span>
            </div>
            <div class="instruction-step">
                <div class="step-number">4</div>
                <span>Pastikan status absensi berhasil tercatat</span>
            </div>
        </div>
    </div>

    <!-- Footer Info -->
    <div class="footer-info">
        <i class="fas fa-clock me-2"></i>
        <span id="current-time">{{ now()->format('d M Y, H:i:s') }}</span>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        let countdownTimer = 10;
        let countdownInterval;
        let updateInterval;
        let timeInterval;
        let isLoading = false;
        let isInitialized = false; // Prevent multiple initializations
        
        // Initialize the page
        document.addEventListener('DOMContentLoaded', function() {
            // Prevent multiple initializations
            if (isInitialized) {
                console.log('QR Display already initialized, skipping...');
                return;
            }
            
            isInitialized = true;
            console.log('QR Display initialized at:', new Date().toISOString());
            
            // Clear any existing intervals first
            clearAllIntervals();
            
            loadQRCode();
            startCountdown();
            startTimeUpdate();
            startAutoUpdate();
        });
        
        // Function to clear all intervals
        function clearAllIntervals() {
            if (countdownInterval) {
                clearInterval(countdownInterval);
                countdownInterval = null;
            }
            if (updateInterval) {
                clearInterval(updateInterval);
                updateInterval = null;
            }
            if (timeInterval) {
                clearInterval(timeInterval);
                timeInterval = null;
            }
            console.log('All intervals cleared');
        }
        
        // Load QR Code from API
        function loadQRCode() {
            if (isLoading) {
                console.log('QR load already in progress, skipping...');
                return;
            }
            
            isLoading = true;
            const spinner = document.querySelector('.loading-spinner');
            const content = document.getElementById('qr-content');
            const container = document.getElementById('qr-code-container');
            
            console.log('Loading QR code at:', new Date().toISOString());
            
            spinner.style.display = 'block';
            content.style.display = 'none';
            
            fetch('/api/qr/today', {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                },
                cache: 'no-cache' // Ensure fresh data
            })
            .then(response => {
                console.log('API response status:', response.status);
                return response.json();
            })
            .then(data => {
                console.log('QR data received:', data);
                if (data.success && data.data) {
                    updateQRDisplay(data.data);
                    updateConnectionStatus(true);
                } else {
                    console.error('QR data error:', data);
                    showError('QR Code tidak tersedia');
                    updateConnectionStatus(false);
                }
            })
            .catch(error => {
                console.error('Error loading QR code:', error);
                showError('Gagal memuat QR Code');
                updateConnectionStatus(false);
            })
            .finally(() => {
                isLoading = false;
                spinner.style.display = 'none';
                content.style.display = 'block';
            });
        }
        
        // Update QR display with new data
        function updateQRDisplay(qrData) {
            const container = document.getElementById('qr-code-container');
            const content = document.getElementById('qr-content');
            
            console.log('Updating QR display with:', qrData.qr_code, 'at', new Date().toISOString());
            
            // Check if this is actually a new QR code
            const lastQRCode = content.getAttribute('data-last-qr-code');
            
            if (lastQRCode === qrData.qr_code) {
                console.log('Same QR code received, skipping update');
                return;
            }
            
            // Store the new QR code for comparison
            content.setAttribute('data-last-qr-code', qrData.qr_code);
            
            // Add pulse animation
            container.classList.add('updating');
            setTimeout(() => container.classList.remove('updating'), 500);
            
            // Clear previous content
            content.innerHTML = '';
            
            // Create QR code container
            const qrContainer = document.createElement('div');
            
            // Generate QR code using QRCode.js
            if (typeof QRCode !== 'undefined') {
                const canvas = document.createElement('canvas');
                qrContainer.appendChild(canvas);
                
                QRCode.toCanvas(canvas, qrData.qr_code, {
                    width: 300,
                    height: 300,
                    margin: 2,
                    color: {
                        dark: '#000000',
                        light: '#ffffff'
                    }
                }, function (error) {
                    if (error) {
                        console.error('QR Code generation error:', error);
                        // Fallback to server-generated SVG
                        qrContainer.innerHTML = `<img src="/admin/attendance/qr/image/${qrData.qr_code}" alt="QR Code" style="width: 300px; height: 300px;" onerror="this.parentElement.innerHTML='<div>QR Code tidak dapat dimuat</div>';">`;
                    } else {
                        console.log('QR Code generated successfully for:', qrData.qr_code);
                    }
                });
            } else {
                console.log('QRCode.js not available, using server fallback');
                // Fallback to server-generated SVG if QRCode.js is not available
                qrContainer.innerHTML = `<img src="/admin/attendance/qr/image/${qrData.qr_code}" alt="QR Code" style="width: 300px; height: 300px;" onerror="this.parentElement.innerHTML='<div>QR Code tidak dapat dimuat</div>';">`;
            }
            
            content.appendChild(qrContainer);
            
            // Update info
            document.getElementById('qr-valid-until').textContent = formatDateTime(qrData.valid_until);
            document.getElementById('last-updated').textContent = formatDateTime(new Date());
            
            // Show generated time if available
            if (qrData.generated_at) {
                console.log('QR generated at server:', qrData.generated_at);
            }
            
            console.log('QR display updated successfully with new code:', qrData.qr_code);
        }
        
        // Show error message
        function showError(message) {
            const content = document.getElementById('qr-content');
            content.innerHTML = `
                <div class="text-center p-4">
                    <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                    <h5 class="text-dark">${message}</h5>
                    <p class="text-muted">Silakan hubungi administrator</p>
                    <button class="btn btn-primary mt-3" onclick="loadQRCode()">
                        <i class="fas fa-redo me-2"></i>Coba Lagi
                    </button>
                </div>
            `;
        }
        
        // Start countdown timer
        function startCountdown() {
            const countdownElement = document.getElementById('countdown');
            
            // Clear any existing countdown first
            if (countdownInterval) {
                clearInterval(countdownInterval);
                countdownInterval = null;
            }
            
            console.log('Starting countdown timer');
            
            countdownInterval = setInterval(() => {
                countdownElement.textContent = countdownTimer;
                console.log('Countdown:', countdownTimer);
                
                if (countdownTimer <= 0) {
                    console.log('Countdown reached 0, loading new QR code...');
                    countdownTimer = 10; // Reset to 10 seconds
                    loadQRCode(); // Reload QR code
                } else {
                    countdownTimer--;
                }
            }, 1000);
        }
        
        // Start auto update
        function startAutoUpdate() {
            // Clear any existing interval first
            if (updateInterval) {
                clearInterval(updateInterval);
                updateInterval = null;
            }
            
            console.log('Starting auto update interval (10 seconds)');
            
            updateInterval = setInterval(() => {
                console.log('Auto update triggered');
                loadQRCode();
            }, 10000); // Update every 10 seconds
        }
        
        // Start time update
        function startTimeUpdate() {
            // Clear any existing time interval first
            if (timeInterval) {
                clearInterval(timeInterval);
                timeInterval = null;
            }
            
            timeInterval = setInterval(() => {
                const now = new Date();
                const timeString = now.toLocaleDateString('id-ID', {
                    day: '2-digit',
                    month: 'short',
                    year: 'numeric'
                }) + ', ' + now.toLocaleTimeString('id-ID');
                
                document.getElementById('current-time').textContent = timeString;
            }, 1000);
        }
        
        // Update connection status
        function updateConnectionStatus(isConnected) {
            const statusElement = document.getElementById('connection-status');
            if (isConnected) {
                statusElement.innerHTML = '<i class="fas fa-wifi me-1"></i>Terhubung';
                statusElement.parentElement.style.background = 'rgba(40, 167, 69, 0.8)';
            } else {
                statusElement.innerHTML = '<i class="fas fa-wifi-slash me-1"></i>Terputus';
                statusElement.parentElement.style.background = 'rgba(220, 53, 69, 0.8)';
            }
        }
        
        // Format date time
        function formatDateTime(dateTime) {
            const date = new Date(dateTime);
            return date.toLocaleDateString('id-ID', {
                day: '2-digit',
                month: 'short',
                year: 'numeric'
            }) + ' ' + date.toLocaleTimeString('id-ID', {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            });
        }
        
        // Cleanup intervals when page unloads
        window.addEventListener('beforeunload', function() {
            console.log('Page unloading, cleaning up intervals');
            clearAllIntervals();
        });
        
        // Handle visibility change (pause when tab is not active)
        document.addEventListener('visibilitychange', function() {
            if (document.hidden) {
                console.log('Tab hidden, pausing intervals');
                clearAllIntervals();
            } else {
                console.log('Tab visible, resuming intervals');
                if (isInitialized) {
                    countdownTimer = 10; // Reset countdown
                    startCountdown();
                    startAutoUpdate();
                    startTimeUpdate();
                    loadQRCode(); // Refresh immediately when tab becomes active
                }
            }
        });
        
        // Add a global cleanup function accessible from console for debugging
        window.clearQRIntervals = function() {
            console.log('Manual interval cleanup called');
            clearAllIntervals();
            isInitialized = false;
        };
    </script>
</body>
</html>