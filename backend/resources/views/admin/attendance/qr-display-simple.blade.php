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
            --bg-start: #f8fafc; /* slate-50 */
            --bg-end: #ffffff;   /* white */
            --card-bg: #ffffff;
            --card-border: rgba(15, 23, 42, 0.08);
            --text-primary: #0f172a; /* slate-900 */
            --text-muted: #475569;   /* slate-600 */
            --accent: #06b6d4;       /* cyan-500 */
            --accent-2: #7c3aed;     /* violet-600 */
            --success: #16a34a;
            --danger: #dc2626;
            --warning: #d97706;
        }
        body {
            background:
                radial-gradient(1200px 600px at 20% 0%, rgba(124, 58, 237, 0.06), transparent 60%),
                radial-gradient(800px 400px at 80% 20%, rgba(6, 182, 212, 0.08), transparent 60%),
                linear-gradient(160deg, var(--bg-start), var(--bg-end));
            font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            color: var(--text-primary);
            font-size: clamp(14px, 1.15vw, 16px);
        }
        .grid-overlay {
            position: fixed;
            inset: 0;
            background-image: linear-gradient(rgba(15,23,42,0.05) 1px, transparent 1px),
                              linear-gradient(90deg, rgba(15,23,42,0.05) 1px, transparent 1px);
            background-size: 40px 40px;
            mask-image: radial-gradient(circle at center, black, transparent 70%);
            pointer-events: none;
        }
        .qr-display-container {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 24px 16px 64px;
        }
        .topbar {
            width: 100%;
            max-width: 1100px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 16px;
            padding: 0 8px;
        }
        .brand {
            display: flex;
            align-items: center;
            gap: 14px;
        }
        .brand-details { display:flex; flex-direction:column; justify-content:center; height:44px; }
        .logo {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: grid;
            place-items: center;
            background: linear-gradient(145deg, rgba(124,58,237,0.12), rgba(6,182,212,0.12));
            border: 1px solid var(--card-border);
            box-shadow: 0 10px 30px rgba(2,6,23,0.08);
            color: var(--text-primary);
        }
        .brand-title { font-weight:700; letter-spacing:0.2px; line-height:1.2; margin-bottom:2px; }
        .status-indicator {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 14px;
            border-radius: 999px;
            border: 1px solid var(--card-border);
            background: #fff;
            box-shadow: 0 4px 16px rgba(2,6,23,0.06);
            min-height: 44px;
            line-height: 1;
        }
        .status-float { position: fixed; top: 18px; right: 18px; z-index: 50; }
        .status-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: var(--success);
            box-shadow: 0 0 0 4px rgba(34,197,94,0.15);
        }
        .content {
            width: 100%;
            max-width: 1100px;
            display: grid;
            grid-template-columns: 1.1fr 0.9fr;
            gap: 16px;
        }
        @media (max-width: 992px) {
            .content { grid-template-columns: 1fr; }
        }
        .card {
            border-radius: 20px;
            border: 1px solid var(--card-border);
            background: var(--card-bg);
            box-shadow: 0 10px 30px rgba(2,6,23,0.06);
        }
        .card-body { padding: 20px; }
        .section-title {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 14px;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 600;
            font-size: 12px;
        }
        .page-title { text-align:center; margin-bottom: 10px; }
        .page-title .title { font-weight: 800; font-size: clamp(18px, 2vw, 24px); }
        .page-title .subtitle { color: var(--text-muted); font-size: clamp(12px, 1.4vw, 14px); }
        .qr-wrapper {
            display: grid;
            place-items: center;
            padding: 14px;
            background: rgba(2, 6, 23, 0.03);
            border: 1px dashed rgba(2, 6, 23, 0.08);
            border-radius: 16px;
        }
        .qr-code-container {
            background: #ffffff;
            padding: 12px;
            border-radius: 12px;
            border: 1px solid rgba(2,6,23,0.08);
            box-shadow: 0 6px 24px rgba(2,6,23,0.06);
        }
        .qr-code-container.updating { animation: pulse 0.5s ease-in-out; }
        @keyframes pulse { 0%{transform:scale(1)} 50%{transform:scale(1.02)} 100%{transform:scale(1)} }
        .info-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-top: 14px;
        }
        .info-pill {
            background: #ffffff;
            border: 1px solid rgba(2,6,23,0.08);
            border-radius: 12px;
            padding: 10px 12px;
        }
        .info-label { color: var(--text-muted); font-size: 12px; }
        .info-value { font-weight: 600; margin-top: 2px; font-size: 0.95em; }
        .countdown-wrap { display:flex; align-items:center; gap:14px; }
        .progress { width: 72px; height: 72px; position: relative; }
        .progress svg { transform: rotate(-90deg); }
        .progress .bg { stroke: rgba(2,6,23,0.08); }
        .progress .bar { stroke: url(#grad); transition: stroke-dashoffset 0.3s ease; }
        .progress .center {
            position: absolute; inset: 0; display: grid; place-items: center; font-weight: 700;
        }
        .instructions { color: var(--text-muted); }
        .instructions .step { display:flex; gap:10px; align-items:flex-start; margin-bottom:10px; }
        .footer-info {
            position: fixed; bottom: 18px; left: 50%; transform: translateX(-50%);
            color: var(--text-muted);
            font-size: 14px;
            display: inline-flex; align-items: center; gap: 10px;
            padding: 8px 14px; border-radius: 999px; border: 1px solid var(--card-border);
            background: #fff;
            box-shadow: 0 6px 24px rgba(2,6,23,0.06);
        }
        .loading-spinner { display: none; color: var(--accent); }
    </style>
</head>
<body>
    <div class="grid-overlay"></div>
    <div class="topbar">
        <div class="brand">
            <div class="logo"><i class="fas fa-graduation-cap"></i></div>
            <div class="brand-details">
                <div class="brand-title" style="opacity:0.9">E-Track14</div>
                <div style="font-size:12px; color:var(--text-muted)">QR Attendance Display</div>
            </div>
        </div>
        <div></div>
    </div>
    <div class="status-float">
        <div class="status-indicator" id="status-pill">
            <span class="status-dot" id="status-dot"></span>
        <span id="connection-status">Terhubung</span>
        </div>
    </div>

    <!-- Main Content -->
    <div class="qr-display-container">
        <div class="content">
            <div class="card">
                <div class="card-body">
                    <div class="page-title">
                        <div class="title">SMPN 14 SURABAYA</div>
                        <div class="subtitle">Sistem Absensi Digital</div>
            </div>
                    <div class="section-title" style="justify-content:center"><i class="fas fa-qrcode"></i> Absensi QR Code</div>
                    <div class="qr-wrapper">
            <div id="qr-code-container" class="qr-code-container">
                <div class="loading-spinner">
                                <i class="fas fa-spinner fa-spin fa-2x"></i>
                            </div>
                            <div id="qr-content"></div>
                        </div>
                    </div>
                    <div class="info-row">
                        <div class="info-pill">
                            <div class="info-label">Berlaku Sampai</div>
                            <div class="info-value" id="qr-valid-until">-</div>
                        </div>
                        <div class="info-pill">
                            <div class="info-label">Diperbarui</div>
                            <div class="info-value" id="last-updated">-</div>
                        </div>
                </div>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="section-title"><i class="fas fa-bolt"></i> Pembaruan Otomatis</div>
                    <div class="countdown-wrap">
                        <div class="progress">
                            <svg width="72" height="72" viewBox="0 0 100 100">
                                <defs>
                                    <linearGradient id="grad" x1="0%" y1="0%" x2="100%" y2="0%">
                                        <stop offset="0%" stop-color="#22d3ee"/>
                                        <stop offset="100%" stop-color="#a78bfa"/>
                                    </linearGradient>
                                </defs>
                                <circle class="bg" cx="50" cy="50" r="40" fill="none" stroke-width="8" />
                                <circle id="progress-bar" class="bar" cx="50" cy="50" r="40" fill="none" stroke-width="8"
                                        stroke-linecap="round" stroke-dasharray="264" stroke-dashoffset="0" />
                            </svg>
                            <div class="center" id="countdown">10</div>
                        </div>
                        <div style="flex:1">
                            <div style="font-weight:600">QR akan diperbarui otomatis</div>
                            <div style="color:var(--text-muted); font-size:14px">Ketika penghitung mencapai nol, kode akan dimuat ulang.</div>
            </div>
        </div>
                    <div class="instructions" style="margin-top:16px">
                        <div class="step"><i class="fas fa-mobile-alt" style="color:var(--accent)"></i> Buka aplikasi E-Track14</div>
                        <div class="step"><i class="fas fa-location-arrow" style="color:var(--warning)"></i> Aktifkan GPS dalam radius sekolah</div>
                        <div class="step"><i class="fas fa-camera" style="color:var(--accent-2)"></i> Ambil selfie sesuai instruksi</div>
                        <div class="step"><i class="fas fa-qrcode" style="color:var(--accent)"></i> Scan QR di layar ini</div>
                </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer Info -->
    <div class="footer-info">
        <i class="fas fa-clock"></i>
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
                return;
            }
            
            isInitialized = true;
            
            // Clear any existing intervals first
            clearAllIntervals();
            
            loadQRCode();
            startCountdown();
            startTimeUpdate();
            // Removed parallel auto-update to prevent double refresh; countdown handles reload
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
        }
        
        // Load QR Code from API
        function loadQRCode() {
            if (isLoading) {
                return;
            }
            
            isLoading = true;
            const spinner = document.querySelector('.loading-spinner');
            const content = document.getElementById('qr-content');
            const container = document.getElementById('qr-code-container');
            
            
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
                return response.json();
            })
            .then(data => {
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
            
            
            // Check if this is actually a new QR code
            const lastQRCode = content.getAttribute('data-last-qr-code');
            
            if (lastQRCode === qrData.qr_code) {
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
                    }
                });
            } else {
                // Fallback to server-generated SVG if QRCode.js is not available
                qrContainer.innerHTML = `<img src="/admin/attendance/qr/image/${qrData.qr_code}" alt="QR Code" style="width: 300px; height: 300px;" onerror="this.parentElement.innerHTML='<div>QR Code tidak dapat dimuat</div>';">`;
            }
            
            content.appendChild(qrContainer);
            
            // Update info
            document.getElementById('qr-valid-until').textContent = formatDateTime(qrData.valid_until);
            document.getElementById('last-updated').textContent = formatDateTime(new Date());
            
            // Show generated time if available
            if (qrData.generated_at) {
            }
            
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
            const progressCircle = document.getElementById('progress-bar');
            const circumference = 2 * Math.PI * 40; // r=40 (synced with SVG)
            
            // Clear any existing countdown first
            if (countdownInterval) {
                clearInterval(countdownInterval);
                countdownInterval = null;
            }
            
            
            countdownInterval = setInterval(() => {
                countdownElement.textContent = countdownTimer;
                // Update circular progress
                const ratio = countdownTimer / 10; // 10s cycle
                const offset = circumference * (1 - ratio);
                if (progressCircle) {
                    progressCircle.style.strokeDasharray = `${circumference}`;
                    progressCircle.style.strokeDashoffset = `${offset}`;
                }
                
                if (countdownTimer <= 0) {
                    countdownTimer = 10; // Reset to 10 seconds
                    loadQRCode(); // Reload QR code
                } else {
                    countdownTimer--;
                }
            }, 1000);
        }
        
        // Start auto update
        // Auto update removed; rely on countdown reaching 0 to trigger loadQRCode()
        
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
            const dot = document.getElementById('status-dot');
            const pill = document.getElementById('status-pill');
            if (isConnected) {
                statusElement.innerHTML = 'Terhubung';
                if (dot) dot.style.background = 'var(--success)';
                if (pill) pill.style.borderColor = 'rgba(34,197,94,0.25)';
            } else {
                statusElement.innerHTML = 'Terputus';
                if (dot) dot.style.background = 'var(--danger)';
                if (pill) pill.style.borderColor = 'rgba(239,68,68,0.25)';
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
            clearAllIntervals();
        });
        
        // Handle visibility change (pause when tab is not active)
        document.addEventListener('visibilitychange', function() {
            if (document.hidden) {
                clearAllIntervals();
            } else {
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
            clearAllIntervals();
            isInitialized = false;
        };
    </script>
</body>
</html>