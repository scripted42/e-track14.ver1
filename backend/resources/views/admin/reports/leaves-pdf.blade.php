<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Izin & Cuti</title>
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
        .status-menumgu {
            color: #ffc107;
            font-weight: bold;
        }
        .status-disetujui {
            color: #28a745;
            font-weight: bold;
        }
        .status-ditolak {
            color: #dc3545;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Laporan Izin & Cuti</h1>
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
                <th>Jenis Izin</th>
                <th>Tanggal Mulai</th>
                <th>Tanggal Selesai</th>
                <th>Durasi</th>
                <th>Status</th>
                <th>Disetujui Oleh</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $item->user->name }}</td>
                <td>{{ $item->user->role->role_name }}</td>
                <td>{{ ucfirst($item->leave_type) }}</td>
                <td>{{ \Carbon\Carbon::parse($item->start_date)->format('d/m/Y') }}</td>
                <td>{{ \Carbon\Carbon::parse($item->end_date)->format('d/m/Y') }}</td>
                <td>{{ \Carbon\Carbon::parse($item->start_date)->diffInDays(\Carbon\Carbon::parse($item->end_date)) + 1 }} hari</td>
                <td>
                    @if($item->status == 'menunggu')
                        <span class="status-menumgu">Menunggu</span>
                    @elseif($item->status == 'disetujui')
                        <span class="status-disetujui">Disetujui</span>
                    @elseif($item->status == 'ditolak')
                        <span class="status-ditolak">Ditolak</span>
                    @else
                        {{ ucfirst($item->status) }}
                    @endif
                </td>
                <td>{{ $item->approver->name ?? '-' }}</td>
                <td>{{ $item->reason ?? '-' }}</td>
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
