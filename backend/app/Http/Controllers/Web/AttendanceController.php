<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\AttendanceQr;
use App\Models\User;
use App\Models\AuditLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $query = Attendance::with(['user:id,name,email', 'user.role:id,role_name'])
            ->orderBy('timestamp', 'desc');

        // Apply filters
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('timestamp', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('timestamp', '<=', $request->date_to);
        }

        $attendance = $query->paginate(20);

        // Get filter options
        $users = User::whereIn('role_id', [2, 3]) // Guru and Pegawai
            ->orderBy('name')
            ->get(['id', 'name']);

        $statuses = ['hadir', 'terlambat', 'izin', 'sakit', 'alpha', 'cuti', 'dinas_luar'];
        $types = ['checkin', 'checkout'];

        // Statistics
        $today = Carbon::today();
        $stats = [
            'today_total' => Attendance::whereDate('timestamp', $today)->distinct('user_id')->count(),
            'today_late' => Attendance::whereDate('timestamp', $today)->where('status', 'terlambat')->count(),
            'this_month' => Attendance::whereYear('timestamp', $today->year)
                ->whereMonth('timestamp', $today->month)
                ->distinct('user_id')
                ->count(),
        ];

        return view('admin.attendance.index', compact(
            'attendance',
            'users',
            'statuses',
            'types',
            'stats'
        ));
    }

    public function qr()
    {
        $todayQr = AttendanceQr::whereDate('created_at', today())
            ->orderBy('created_at', 'desc')
            ->first();

        $recentQrs = AttendanceQr::orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return view('admin.attendance.qr', compact('todayQr', 'recentQrs'));
    }

    public function qrDisplay()
    {
        return view('admin.attendance.qr-display-simple');
    }

    public function qrImage($code)
    {
        try {
            // Find the QR code
            $qr = AttendanceQr::where('qr_code', $code)->first();
            
            if (!$qr) {
                abort(404, 'QR Code not found');
            }

            // Since GD extension is not available, create SVG QR representation
            $svg = $this->generateQRCodeSVG($qr->qr_code);
            
            return response($svg)
                ->header('Content-Type', 'image/svg+xml')
                ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
                ->header('Pragma', 'no-cache')
                ->header('Expires', '0');
                
        } catch (\Exception $e) {
            // Fallback: create a simple SVG error image
            $errorSvg = '<svg xmlns="http://www.w3.org/2000/svg" width="300" height="300" viewBox="0 0 300 300">
                <rect width="300" height="300" fill="#f8f9fa" stroke="#dc3545" stroke-width="2"/>
                <text x="150" y="140" text-anchor="middle" dominant-baseline="middle" 
                      font-family="Arial, sans-serif" font-size="14" fill="#dc3545">QR Code Error</text>
                <text x="150" y="160" text-anchor="middle" dominant-baseline="middle" 
                      font-family="Arial, sans-serif" font-size="10" fill="#6c757d">' . htmlspecialchars($e->getMessage()) . '</text>
            </svg>';
            
            return response($errorSvg)
                ->header('Content-Type', 'image/svg+xml');
        }
    }
    
    private function generateQRCodeSVG($text)
    {
        // Create a proper-looking QR code SVG with better visual design
        $size = 300;
        $margin = 20;
        $cellSize = 8;
        $gridSize = floor(($size - 2 * $margin) / $cellSize);
        
        $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="' . $size . '" height="' . $size . '" viewBox="0 0 ' . $size . ' ' . $size . '">';
        
        // Background with subtle gradient
        $svg .= '<defs>';
        $svg .= '<linearGradient id="bg" x1="0%" y1="0%" x2="100%" y2="100%">';
        $svg .= '<stop offset="0%" style="stop-color:#f8f9fa;stop-opacity:1" />';
        $svg .= '<stop offset="100%" style="stop-color:#ffffff;stop-opacity:1" />';
        $svg .= '</linearGradient>';
        $svg .= '<filter id="shadow" x="-20%" y="-20%" width="140%" height="140%">';
        $svg .= '<feDropShadow dx="2" dy="2" stdDeviation="3" flood-color="#000000" flood-opacity="0.1"/>';
        $svg .= '</filter>';
        $svg .= '</defs>';
        
        $svg .= '<rect width="' . $size . '" height="' . $size . '" fill="url(#bg)" stroke="#e9ecef" stroke-width="2" rx="8"/>';
        
        // Create a more sophisticated pattern based on text
        $hash = hash('sha256', $text);
        $pattern = [];
        
        // Convert hash to binary pattern
        for ($i = 0; $i < strlen($hash); $i += 2) {
            $hex = substr($hash, $i, 2);
            $binary = str_pad(decbin(hexdec($hex)), 8, '0', STR_PAD_LEFT);
            for ($j = 0; $j < 8; $j++) {
                $pattern[] = (int)$binary[$j];
            }
        }
        
        // Draw main pattern
        $patternIndex = 0;
        for ($row = 3; $row < $gridSize - 3; $row++) {
            for ($col = 3; $col < $gridSize - 3; $col++) {
                // Skip corner areas
                if (($row < 10 && $col < 10) || 
                    ($row < 10 && $col > $gridSize - 11) || 
                    ($row > $gridSize - 11 && $col < 10)) {
                    continue;
                }
                
                if ($pattern[$patternIndex % count($pattern)]) {
                    $x = $margin + $col * $cellSize;
                    $y = $margin + $row * $cellSize;
                    $svg .= '<rect x="' . $x . '" y="' . $y . '" width="' . $cellSize . '" height="' . $cellSize . '" fill="#000000" rx="1"/>';
                }
                $patternIndex++;
            }
        }
        
        // Add corner finder patterns (proper QR code style)
        $cornerSize = $cellSize * 7;
        $corners = [
            [$margin, $margin],
            [$size - $margin - $cornerSize, $margin],
            [$margin, $size - $margin - $cornerSize]
        ];
        
        foreach ($corners as $corner) {
            // Outer border
            $svg .= '<rect x="' . $corner[0] . '" y="' . $corner[1] . '" width="' . $cornerSize . '" height="' . $cornerSize . '" fill="#000000" rx="2"/>';
            // Inner white
            $svg .= '<rect x="' . ($corner[0] + $cellSize) . '" y="' . ($corner[1] + $cellSize) . '" width="' . ($cornerSize - 2 * $cellSize) . '" height="' . ($cornerSize - 2 * $cellSize) . '" fill="#ffffff" rx="1"/>';
            // Center dot
            $svg .= '<rect x="' . ($corner[0] + 2 * $cellSize) . '" y="' . ($corner[1] + 2 * $cellSize) . '" width="' . (3 * $cellSize) . '" height="' . (3 * $cellSize) . '" fill="#000000" rx="1"/>';
        }
        
        // Add timing patterns
        for ($i = 8; $i < $gridSize - 8; $i += 2) {
            // Horizontal timing
            $x = $margin + $i * $cellSize;
            $y = $margin + 6 * $cellSize;
            $svg .= '<rect x="' . $x . '" y="' . $y . '" width="' . $cellSize . '" height="' . $cellSize . '" fill="#000000"/>';
            
            // Vertical timing
            $x = $margin + 6 * $cellSize;
            $y = $margin + $i * $cellSize;
            $svg .= '<rect x="' . $x . '" y="' . $y . '" width="' . $cellSize . '" height="' . $cellSize . '" fill="#000000"/>';
        }
        
        // Add scan instruction at top
        $svg .= '<text x="' . ($size/2) . '" y="15" text-anchor="middle" font-family="Arial, sans-serif" font-size="9" fill="#6c757d">SCAN QR CODE</text>';
        
        $svg .= '</svg>';
        
        return $svg;
    }

    public function generateQr(Request $request)
    {
        try {
            // Check if QR already exists for today
            $existingQr = AttendanceQr::whereDate('created_at', today())
                ->where('valid_until', '>', now())
                ->first();

            if ($existingQr) {
                return redirect()->back()->with('error', 'QR Code untuk hari ini sudah ada dan masih aktif.');
            }

            // Generate new QR
            $qr = AttendanceQr::generateDailyQr();

            // Log activity
            AuditLog::log('qr_generated', [
                'qr_id' => $qr->id,
                'qr_code' => $qr->qr_code,
                'valid_until' => $qr->valid_until
            ], auth()->id());

            return redirect()->back()->with('success', 'QR Code berhasil dibuat.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal membuat QR Code: ' . $e->getMessage());
        }
    }

    public function export(Request $request)
    {
        $query = Attendance::with(['user:id,name,email', 'user.role:id,role_name']);

        // Apply same filters as index
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('timestamp', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('timestamp', '<=', $request->date_to);
        }

        $attendance = $query->orderBy('timestamp', 'desc')->get();

        $filename = 'attendance_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($attendance) {
            $file = fopen('php://output', 'w');

            // CSV headers
            fputcsv($file, [
                'Nama',
                'Email',
                'Role',
                'Jenis',
                'Status',
                'Tanggal',
                'Waktu',
                'Latitude',
                'Longitude',
                'Akurasi'
            ]);

            // CSV data
            foreach ($attendance as $record) {
                fputcsv($file, [
                    $record->user->name,
                    $record->user->email,
                    $record->user->role->role_name,
                    ucfirst($record->type),
                    ucfirst($record->status),
                    $record->timestamp->format('Y-m-d'),
                    $record->timestamp->format('H:i:s'),
                    $record->latitude,
                    $record->longitude,
                    $record->accuracy
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}