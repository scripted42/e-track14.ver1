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
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --secondary-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --accent-gradient: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
            --dark-overlay: rgba(0, 0, 0, 0.7);
            --card-bg: rgba(255, 255, 255, 0.98);
            --text-dark: #2d3748;
            --text-light: #f7fafc;
            --countdown-glow: 0 0 20px rgba(67, 233, 123, 0.7);
        }
        
        body {
            background: var(--primary-gradient);
            font-family: 'Segoe UI', 'Roboto', 'Helvetica Neue', Arial, sans-serif;
            margin: 0;
            padding: 0;
            height: 100vh;
            overflow: hidden;
            animation: gradientShift 15s ease infinite;
            background-size: 400% 400%;
        }
        
        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        
        .qr-display-container {
            height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: var(--text-light);
            text-align: center;
            padding: 20px;
        }
        
        .school-header {
            margin-bottom: 2rem;
            animation: fadeInDown 1s ease-out;
        }
        
        .school-logo {
            width: 140px;
            height: 140px;
            background: var(--card-bg);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            box-shadow: 0 20px 40px rgba(0,0,0,0.3), 0 0 0 4px rgba(255,255,255,0.1);
            position: relative;
            overflow: hidden;
            animation: float 6s ease-in-out infinite;
        }
        
        .school-logo::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: var(--secondary-gradient);
            opacity: 0.1;
            z-index: 0;
        }
        
        .school-logo i {
            font-size: 3.5rem;
            background: var(--secondary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            z-index: 1;
        }
        
        .school-name {
            font-size: 3rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
            text-shadow: 0 4px 10px rgba(0,0,0,0.3);
            letter-spacing: 1px;
            background: linear-gradient(to right, #fff, #f0f0f0);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .school-subtitle {
            font-size: 1.4rem;
            opacity: 0.95;
            margin-bottom: 0;
            font-weight: 300;
            letter-spacing: 1px;
        }
        
        .qr-card {
            background: var(--card-bg);
            border-radius: 30px;
            padding: 3.5rem;
            box-shadow: 0 30px 60px rgba(0,0,0,0.2), 
                        0 0 0 1px rgba(255,255,255,0.3),
                        0 0 40px rgba(118, 75, 162, 0.3);
            margin: 2rem 0;
            min-width: 550px;
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,0.4);
            position: relative;
            overflow: hidden;
            animation: fadeInUp 1s ease-out;
        }
        
        .qr-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: var(--secondary-gradient);
        }
        
        .qr-card h2 {
            color: var(--text-dark);
            margin-bottom: 2rem;
            font-weight: 700;
            font-size: 1.8rem;
        }
        
        .qr-card h2 i {
            background: var(--secondary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .qr-code-container {
            background: linear-gradient(145deg, #ffffff, #f0f0f0);
            padding: 2.5rem;
            border-radius: 25px;
            display: inline-block;
            margin: 1.5rem 0;
            box-shadow: 0 20px 40px rgba(0,0,0,0.15), 
                        inset 0 1px 0 rgba(255,255,255,0.9),
                        0 0 0 1px rgba(0,0,0,0.08);
            border: 2px solid rgba(255,255,255,0.9);
            position: relative;
            transition: all 0.3s ease;
        }
        
        .qr-code-container:hover {
            transform: translateY(-5px);
            box-shadow: 0 25px 50px rgba(0,0,0,0.2), 
                        inset 0 1px 0 rgba(255,255,255,0.9),
                        0 0 0 1px rgba(0,0,0,0.08);
        }
        
        .qr-code-container::before {
            content: '';
            position: absolute;
            top: -2px;
            left: -2px;
            right: -2px;
            bottom: -2px;
            background: var(--secondary-gradient);
            border-radius: 27px;
            z-index: -1;
            opacity: 0.2;
        }
        
        .qr-code-container canvas,
        .qr-code-container img {
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        }
        
        .qr-info {
            color: var(--text-dark);
            margin-top: 2rem;
            background: rgba(102, 126, 234, 0.05);
            border-radius: 15px;
            padding: 1.5rem;
            border: 1px solid rgba(102, 126, 234, 0.1);
        }
        
        .qr-info p {
            margin-bottom: 0.8rem;
            font-size: 1.1rem;
        }
        
        .qr-info strong {
            color: #4a5568;
        }
        
        .countdown-container {
            background: rgba(255, 255, 255, 0.15);
            border-radius: 20px;
            padding: 2rem;
            margin-top: 2.5rem;
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255,255,255,0.2);
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            animation: fadeIn 1s ease-out;
        }
        
        .countdown-title {
            font-size: 1.3rem;
            margin-bottom: 1rem;
            font-weight: 600;
        }
        
        .countdown-timer {
            font-size: 4rem;
            font-weight: 800;
            background: var(--accent-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-shadow: var(--countdown-glow);
            margin: 0.5rem 0;
            transition: all 0.3s ease;
            animation: pulseGlow 2s ease-in-out infinite alternate;
        }
        
        @keyframes pulseGlow {
            0% { text-shadow: var(--countdown-glow); }
            100% { text-shadow: 0 0 30px rgba(67, 233, 123, 0.9); }
        }
        
        .countdown-warning {
            color: #ffd700;
            font-weight: 600;
            margin-top: 0.5rem;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        .countdown-warning.show {
            opacity: 1;
        }
        
        .status-indicator {
            position: fixed;
            top: 25px;
            right: 25px;
            background: var(--dark-overlay);
            color: white;
            padding: 12px 25px;
            border-radius: 30px;
            backdrop-filter: blur(15px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .status-indicator.connected {
            background: rgba(40, 167, 69, 0.8);
        }
        
        .status-indicator.disconnected {
            background: rgba(220, 53, 69, 0.8);
        }
        
        .instructions {
            background: rgba(255, 255, 255, 0.15);
            border-radius: 20px;
            padding: 2.5rem;
            margin-top: 2.5rem;
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255,255,255,0.2);
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            max-width: 800px;
            animation: fadeInUp 1s ease-out 0.3s both;
        }
        
        .instructions h5 {
            font-size: 1.5rem;
            margin-bottom: 1.8rem;
            font-weight: 700;
        }
        
        .instructions h5 i {
            background: var(--accent-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .instruction-step {
            display: flex;
            align-items: center;
            margin-bottom: 1.5rem;
            font-size: 1.2rem;
            text-align: left;
            padding: 1rem;
            border-radius: 15px;
            transition: all 0.3s ease;
        }
        
        .instruction-step:hover {
            background: rgba(255, 255, 255, 0.1);
            transform: translateX(5px);
        }
        
        .step-number {
            background: var(--secondary-gradient);
            color: white;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            margin-right: 1.5rem;
            font-size: 1.2rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            flex-shrink: 0;
        }
        
        .footer-info {
            position: fixed;
            bottom: 25px;
            left: 50%;
            transform: translateX(-50%);
            background: var(--dark-overlay);
            color: white;
            padding: 12px 35px;
            border-radius: 30px;
            backdrop-filter: blur(15px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            font-weight: 500;
        }
        
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.03); }
            100% { transform: scale(1); }
        }
        
        .qr-code-container.updating {
            animation: pulse 0.6s ease-in-out;
        }
        
        .loading-spinner {
            display: none;
            color: #667eea;
        }
        
        .loading-spinner i {
            font-size: 3rem;
            margin-bottom: 1rem;
        }
        
        @keyframes borderGlow {
            0% { opacity: 0.2; }
            100% { opacity: 0.4; }
        }
        
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
            100% { transform: translateY(0px); }
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .qr-card {
                min-width: 90%;
                padding: 2rem;
            }
            
            .school-name {
                font-size: 2.2rem;
            }
            
            .countdown-timer {
                font-size: 3rem;
            }
        }
    </style>
</head>
<body>
    <!-- Status Indicator -->
    <div class="status-indicator connected">
        <i class="fas fa-wifi"></i>
        <span id="connection-status">Terhubung</span>
    </div>

    <!-- Main Content -->
    <div class="qr-display-container">
        <!-- School Header -->
        <div class="school-header">
            <div class="school-logo">
                <i class="fas fa-graduation-cap"></i>
            </div>
            <div class="school-name">SMPN 14 SURABAYA</div>
            <div class="school-subtitle">Sistem Absensi Digital</div>
        </div>

        <!-- QR Code Card -->
        <div class="qr-card">
            <h2>
                <i class="fas fa-qrcode me-2"></i>
                Scan QR Code untuk Absensi
            </h2>
            
            <div id="qr-code-container" class="qr-code-container">
                <div class="loading-spinner">
                    <i class="fas fa-spinner fa-spin"></i>
                    <p>Memuat QR Code...</p>
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
            <div class="countdown-title">
                <i class="fas fa-sync-alt me-2"></i>
                Pembaruan Otomatis
            </div>
            <div class="countdown-timer" id="countdown">10</div>
            <div class="countdown-warning" id="countdown-warning">
                <i class="fas fa-exclamation-triangle me-1"></i>
                QR Code akan diperbarui!
            </div>
        </div>

        <!-- Instructions -->
        <div class="instructions">
            <h5>
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
            qrContainer.style.cssText = 'background: linear-gradient(145deg, #ffffff, #f8f9fa); padding: 25px; border-radius: 20px; display: inline-block; box-shadow: 0 15px 40px rgba(0,0,0,0.1), inset 0 1px 0 rgba(255,255,255,0.9); border: 2px solid rgba(255,255,255,0.8); position: relative;';
            
            // Add subtle animated border effect
            qrContainer.innerHTML = '<div style="position: absolute; top: -2px; left: -2px; right: -2px; bottom: -2px; background: linear-gradient(45deg, #667eea, #764ba2, #667eea); border-radius: 20px; z-index: -1; opacity: 0.1; animation: borderGlow 3s ease-in-out infinite alternate;"></div>';
            
            // Generate QR code using QRCode.js
            if (typeof QRCode !== 'undefined') {
                const canvas = document.createElement('canvas');
                canvas.style.cssText = 'border-radius: 12px; box-shadow: 0 8px 25px rgba(0,0,0,0.1);';
                qrContainer.appendChild(canvas);
                
                QRCode.toCanvas(canvas, qrData.qr_code, {
                    width: 300,
                    height: 300,
                    margin: 3,
                    color: {
                        dark: '#1a1a1a',
                        light: '#ffffff'
                    },
                    errorCorrectionLevel: 'H',
                    type: 'image/png',
                    quality: 1.0,
                    rendererOpts: {
                        quality: 1.0
                    }
                }, function (error) {
                    if (error) {
                        console.error('QR Code generation error:', error);
                        // Enhanced fallback to server-generated SVG
                        qrContainer.innerHTML = `
                            <div style="position: relative; border-radius: 12px; overflow: hidden; box-shadow: 0 8px 25px rgba(0,0,0,0.1);">
                                <img src="/admin/attendance/qr/image/${qrData.qr_code}?t=${Date.now()}" 
                                     alt="QR Code" 
                                     style="width: 300px; height: 300px; display: block;" 
                                     onerror="this.parentElement.innerHTML='<div style=\'background: linear-gradient(145deg, #f8f9fa, #e9ecef); padding: 40px; text-align: center; color: #6c757d; font-family: Arial, sans-serif; font-size: 16px; border-radius: 12px;\'>QR Code tidak dapat dimuat<br><small style=\'font-size: 12px; margin-top: 10px; display: block;\'>Silakan refresh halaman</small></div>';"/>
                            </div>
                        `;
                    } else {
                        console.log('QR Code generated successfully for:', qrData.qr_code);
                    }
                });
            } else {
                console.log('QRCode.js not available, using server fallback');
                // Enhanced fallback to server-generated SVG if QRCode.js is not available
                qrContainer.innerHTML = `
                    <div style="position: relative; border-radius: 12px; overflow: hidden; box-shadow: 0 8px 25px rgba(0,0,0,0.1);">
                        <img src="/admin/attendance/qr/image/${qrData.qr_code}?t=${Date.now()}" 
                             alt="QR Code" 
                             style="width: 300px; height: 300px; display: block;" 
                             onerror="this.parentElement.innerHTML='<div style=\'background: linear-gradient(145deg, #f8f9fa, #e9ecef); padding: 40px; text-align: center; color: #6c757d; font-family: Arial, sans-serif; font-size: 16px; border-radius: 12px;\'>QR Code tidak dapat dimuat<br><small style=\'font-size: 12px; margin-top: 10px; display: block;\'>Silakan refresh halaman</small></div>';"/>
                    </div>
                `;
            }
            
            content.appendChild(qrContainer);
            
            // Update info (removed qr-code-text)
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
            const warningElement = document.getElementById('countdown-warning');
            
            // Clear any existing countdown first
            if (countdownInterval) {
                clearInterval(countdownInterval);
                countdownInterval = null;
            }
            
            console.log('Starting countdown timer from:', countdownTimer);
            
            countdownInterval = setInterval(() => {
                countdownElement.textContent = countdownTimer;
                console.log('Countdown:', countdownTimer, 'at', new Date().toISOString());
                
                // Show warning when countdown is low
                if (countdownTimer <= 3) {
                    warningElement.classList.add('show');
                    countdownElement.style.animation = 'pulse 0.5s ease-in-out infinite';
                } else {
                    warningElement.classList.remove('show');
                    countdownElement.style.animation = 'pulseGlow 2s ease-in-out infinite alternate';
                }
                
                if (countdownTimer <= 0) {
                    console.log('Countdown reached 0, loading new QR code...');
                    loadQRCode(); // Reload QR code first
                    countdownTimer = 10; // Then reset to 10 seconds
                    warningElement.classList.remove('show');
                    countdownElement.style.animation = 'pulseGlow 2s ease-in-out infinite alternate';
                } else {
                    countdownTimer--;
                }
            }, 1000);
        }
        
        // Start auto update - removed duplicate QR loading since countdown handles it
        function startAutoUpdate() {
            // This function is now only used to ensure countdown continues
            // The actual QR refresh is handled by the countdown timer
            console.log('Auto update monitoring enabled (countdown-based)');
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
            const indicator = statusElement.parentElement;
            
            if (isConnected) {
                statusElement.innerHTML = '<i class="fas fa-wifi me-1"></i>Terhubung';
                indicator.className = 'status-indicator connected';
            } else {
                statusElement.innerHTML = '<i class="fas fa-wifi-slash me-1"></i>Terputus';
                indicator.className = 'status-indicator disconnected';
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