<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Kehadiran Staff</title>
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
        .status-checkout {
            color: #6c757d;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Laporan Kehadiran Staff</h1>
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
                <th>Nama</th>
                <th>Role</th>
                <th>Tanggal</th>
                <th>Check In</th>
                <th>Check Out</th>
                <th>Status</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $index => $item)
            <tr>
                <td>{{ (int)$index + 1 }}</td>
                <td>{{ $item['user']->name ?? 'N/A' }}</td>
                <td>{{ $item['user']->role->role_name ?? 'No Role' }}</td>
                <td>{{ \Carbon\Carbon::createFromFormat('Y-m-d', $item['date'])->format('d/m/Y') }}</td>
                <td>
                    @if($item['checkin_time'])
                        {{ $item['checkin_time'] }}
                    @else
                        -
                    @endif
                </td>
                <td>
                    @if($item['checkout_time'])
                        {{ $item['checkout_time'] }}
                    @else
                        -
                    @endif
                </td>
                <td>
                    @if($item['checkin_status'])
                        @if($item['checkin_status'] === 'terlambat')
                            <span class="status-terlambat">Terlambat</span>
                        @elseif($item['checkin_status'] === 'hadir')
                            <span class="status-hadir">Hadir</span>
                        @else
                            <span class="status-checkout">{{ ucfirst($item['checkin_status']) }}</span>
                        @endif
                    @else
                        -
                    @endif
                </td>
                <td>{{ $item['notes'] ?? '-' }}</td>
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
