<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Siswa Saya - {{ $user->name }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #007bff;
            padding-bottom: 20px;
        }
        
        .header h1 {
            color: #007bff;
            margin: 0;
            font-size: 24px;
        }
        
        .header h2 {
            color: #666;
            margin: 5px 0 0 0;
            font-size: 16px;
            font-weight: normal;
        }
        
        .info-section {
            margin-bottom: 20px;
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }
        
        .info-label {
            font-weight: bold;
            color: #495057;
        }
        
        .info-value {
            color: #007bff;
        }
        
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        .table th {
            background-color: #007bff;
            color: white;
            padding: 10px 8px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #0056b3;
        }
        
        .table td {
            padding: 8px;
            border: 1px solid #dee2e6;
            vertical-align: top;
        }
        
        .table tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
        .table tbody tr:hover {
            background-color: #e9ecef;
        }
        
        .status-badge {
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .status-hadir {
            background-color: #d4edda;
            color: #155724;
        }
        
        .status-terlambat {
            background-color: #fff3cd;
            color: #856404;
        }
        
        .status-tidak-hadir {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .summary-section {
            margin-top: 30px;
            padding: 15px;
            background-color: #e7f3ff;
            border-left: 4px solid #007bff;
        }
        
        .summary-title {
            font-weight: bold;
            color: #007bff;
            margin-bottom: 10px;
        }
        
        .summary-stats {
            display: flex;
            justify-content: space-around;
            text-align: center;
        }
        
        .stat-item {
            flex: 1;
        }
        
        .stat-number {
            font-size: 18px;
            font-weight: bold;
            color: #007bff;
        }
        
        .stat-label {
            font-size: 10px;
            color: #666;
            text-transform: uppercase;
        }
        
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #dee2e6;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Laporan Siswa Saya</h1>
        <h2>Guru: {{ $user->name }}</h2>
    </div>
    
    <div class="info-section">
        <div class="info-row">
            <span class="info-label">Periode Laporan:</span>
            <span class="info-value">{{ $startDate->format('d F Y') }} - {{ $endDate->format('d F Y') }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Tanggal Cetak:</span>
            <span class="info-value">{{ now()->format('d F Y H:i') }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Total Siswa:</span>
            <span class="info-value">{{ $myStudents->count() }} siswa</span>
        </div>
        <div class="info-row">
            <span class="info-label">Total Data Kehadiran:</span>
            <span class="info-value">{{ $attendanceData->count() }} record</span>
        </div>
    </div>
    
    @if($attendanceData->count() > 0)
        <table class="table">
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 25%;">Nama Siswa</th>
                    <th style="width: 15%;">Kelas</th>
                    <th style="width: 12%;">Tanggal</th>
                    <th style="width: 10%;">Status</th>
                    <th style="width: 10%;">Waktu</th>
                    <th style="width: 15%;">Guru</th>
                    <th style="width: 8%;">Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($attendanceData as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item->student->name }}</td>
                        <td>{{ $item->student->classRoom->name ?? $item->student->class_name ?? '-' }}</td>
                        <td>{{ Carbon\Carbon::parse($item->created_at)->format('d/m/Y') }}</td>
                        <td>
                            <span class="status-badge status-{{ str_replace(' ', '-', strtolower($item->status)) }}">
                                {{ ucfirst($item->status) }}
                            </span>
                        </td>
                        <td>{{ Carbon\Carbon::parse($item->created_at)->format('H:i') }}</td>
                        <td>{{ $item->teacher->name ?? '-' }}</td>
                        <td>{{ $item->notes ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div style="text-align: center; padding: 40px; color: #666;">
            <p>Tidak ada data kehadiran untuk periode yang dipilih.</p>
        </div>
    @endif
    
    @if($attendanceData->count() > 0)
        <div class="summary-section">
            <div class="summary-title">Ringkasan Kehadiran</div>
            <div class="summary-stats">
                <div class="stat-item">
                    <div class="stat-number">{{ $attendanceData->where('status', 'hadir')->count() }}</div>
                    <div class="stat-label">Hadir</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">{{ $attendanceData->where('status', 'terlambat')->count() }}</div>
                    <div class="stat-label">Terlambat</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">{{ $attendanceData->where('status', 'tidak_hadir')->count() }}</div>
                    <div class="stat-label">Tidak Hadir</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">{{ $myStudents->count() }}</div>
                    <div class="stat-label">Total Siswa</div>
                </div>
            </div>
        </div>
    @endif
    
    <div class="footer">
        <p>Laporan ini dibuat secara otomatis oleh sistem E-Track14 pada {{ now()->format('d F Y H:i:s') }}</p>
        <p>Guru: {{ $user->name }} | Periode: {{ $startDate->format('d/m/Y') }} - {{ $endDate->format('d/m/Y') }}</p>
    </div>
</body>
</html>
