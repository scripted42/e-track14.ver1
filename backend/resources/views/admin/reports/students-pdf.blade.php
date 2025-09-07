<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Kehadiran Siswa</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            color: #333;
            font-size: 24px;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .info {
            margin-bottom: 20px;
            background: #f5f5f5;
            padding: 15px;
            border-radius: 5px;
        }
        .info p {
            margin: 5px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
        .status-hadir {
            color: #28a745;
            font-weight: bold;
        }
        .status-terlambat {
            color: #ffc107;
            font-weight: bold;
        }
        .status-alpha {
            color: #dc3545;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Laporan Kehadiran Siswa</h1>
        <p>SMP Negeri 14 Surabaya</p>
        <p>Periode: {{ $startDate->format('d F Y') }} - {{ $endDate->format('d F Y') }}</p>
    </div>

    <div class="info">
        <p><strong>Tanggal Generate:</strong> {{ now()->format('d F Y H:i:s') }}</p>
        <p><strong>Total Data:</strong> {{ $data->count() }} record</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Siswa</th>
                <th>Kelas</th>
                <th>Tanggal</th>
                <th>Status</th>
                <th>Waktu</th>
                <th>Guru</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $item->student->name }}</td>
                <td>{{ $item->student->classRoom->name ?? $item->student->class_name ?? '-' }}</td>
                <td>{{ \Carbon\Carbon::parse($item->created_at)->format('d/m/Y') }}</td>
                <td>
                    @if($item->status == 'hadir')
                        <span class="status-hadir">Hadir</span>
                    @elseif($item->status == 'terlambat')
                        <span class="status-terlambat">Terlambat</span>
                    @elseif($item->status == 'alpha')
                        <span class="status-alpha">Alpha</span>
                    @else
                        {{ ucfirst($item->status) }}
                    @endif
                </td>
                <td>{{ \Carbon\Carbon::parse($item->created_at)->format('H:i') }}</td>
                <td>{{ $item->teacher->name ?? '-' }}</td>
                <td>{{ $item->notes ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Dokumen ini dibuat secara otomatis oleh sistem E-Track14</p>
        <p>Halaman 1 dari 1</p>
    </div>
</body>
</html>
