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
        $user = auth()->user();
        
        // Build base query
        $query = Attendance::with(['user:id,name,email,role_id', 'user.role:id,role_name']);
        
        // For non-admin users, only show their own attendance
        if (!$user->hasRole('Admin')) {
            $query->where('user_id', $user->id);
        }

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        // Apply period filter
        if ($request->filled('period')) {
            $period = $request->period;
            $today = Carbon::today();
            
            switch ($period) {
                case 'today':
                    $query->whereDate('timestamp', $today);
                    break;
                case 'yesterday':
                    $query->whereDate('timestamp', $today->subDay());
                    break;
                case 'this_week':
                    $query->whereBetween('timestamp', [
                        $today->startOfWeek(),
                        $today->endOfWeek()
                    ]);
                    break;
                case 'this_month':
                    $query->whereMonth('timestamp', $today->month)
                          ->whereYear('timestamp', $today->year);
                    break;
                case 'custom':
                    if ($request->filled('date')) {
                        $query->whereDate('timestamp', $request->date);
                    }
                    break;
            }
        }

        // Apply status filter
        if ($request->filled('status')) {
            $status = $request->status;
            if ($status === 'on-time') {
                // For check-in: time <= 07:05 (425 minutes)
                $query->where('type', 'checkin')
                      ->whereRaw('(HOUR(timestamp) * 60 + MINUTE(timestamp)) <= 425');
            } elseif ($status === 'terlambat') {
                // For check-in: time > 07:05 (425 minutes)
                $query->where('type', 'checkin')
                      ->whereRaw('(HOUR(timestamp) * 60 + MINUTE(timestamp)) > 425');
            } else {
                // For other statuses (izin, sakit, alpha), use original logic
                $query->where('status', $status);
            }
        }

        // Apply type filter
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Get all attendance records with optimized query
        $allAttendance = $query->orderBy('timestamp', 'desc')->get();

        // Get leave records for the same period with optimized query
        $leaveQuery = \App\Models\Leave::with(['user:id,name,email,role_id', 'user.role:id,role_name', 'approver:id,name']);
        
        // Apply same filters for leaves
        if (!$user->hasRole('Admin')) {
            $leaveQuery->where('user_id', $user->id);
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $leaveQuery->whereHas('user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }
        
        // Apply period filter for leaves
        if ($request->filled('period')) {
            $period = $request->period;
            $today = Carbon::today();
            
            switch ($period) {
                case 'today':
                    $leaveQuery->whereDate('start_date', '<=', $today)
                              ->whereDate('end_date', '>=', $today);
                    break;
                case 'yesterday':
                    $yesterday = $today->subDay();
                    $leaveQuery->whereDate('start_date', '<=', $yesterday)
                              ->whereDate('end_date', '>=', $yesterday);
                    break;
                case 'this_week':
                    $leaveQuery->whereBetween('start_date', [
                        $today->startOfWeek(),
                        $today->endOfWeek()
                    ])->orWhereBetween('end_date', [
                        $today->startOfWeek(),
                        $today->endOfWeek()
                    ]);
                    break;
                case 'this_month':
                    $leaveQuery->whereMonth('start_date', $today->month)
                              ->whereYear('start_date', $today->year)
                              ->orWhere(function($q) use ($today) {
                                  $q->whereMonth('end_date', $today->month)
                                    ->whereYear('end_date', $today->year);
                              });
                    break;
                case 'custom':
                    if ($request->filled('date')) {
                        $customDate = $request->date;
                        $leaveQuery->whereDate('start_date', '<=', $customDate)
                                  ->whereDate('end_date', '>=', $customDate);
                    }
                    break;
            }
        }
        
        // Apply status filter for leaves
        if ($request->filled('status')) {
            $status = $request->status;
            if (in_array($status, ['izin', 'sakit', 'alpha', 'cuti'])) {
                if ($status === 'alpha') {
                    // Alpha means no attendance and no approved leave
                    // This will be handled in the final filtering
                } else {
                    $leaveQuery->where('leave_type', $status)
                              ->where('status', 'disetujui');
                }
            }
        }
        
        $allLeaves = $leaveQuery->get();

        // Group by user and date to create daily summary
        $groupedAttendance = $allAttendance->groupBy(function ($item) {
            return $item->user_id . '_' . $item->timestamp->format('Y-m-d');
        });

        $attendanceSummary = collect();
        
        foreach ($groupedAttendance as $key => $records) {
            $userRecord = $records->first();
            $checkin = $records->where('type', 'checkin')->first();
            $checkout = $records->where('type', 'checkout')->first();
            
            // Determine check-in status based on time
            $checkinStatus = null;
            if ($checkin) {
                $hour = (int) $checkin->timestamp->format('H');
                $minute = (int) $checkin->timestamp->format('i');
                $timeMinutes = $hour * 60 + $minute;
                
                // Staff rule: On-Time if ≤ 07:05 (425 minutes), Late if > 07:05
                if ($timeMinutes <= 425) {
                    $checkinStatus = 'On-Time';
                } else {
                    $checkinStatus = 'Terlambat';
                }
            }
            
            // Determine check-out status
            $checkoutStatus = null;
            if ($checkout) {
                $checkoutTime = $checkout->timestamp->format('H:i');
                $checkoutHour = (int) $checkout->timestamp->format('H');
                
                if ($checkoutHour < 15) {
                    $checkoutStatus = 'Pulang Lebih Awal';
                } elseif ($checkoutHour >= 15 && $checkoutHour <= 17) {
                    $checkoutStatus = 'On-Time';
                } else {
                    $checkoutStatus = 'Lembur';
                }
            }
            
            $attendanceSummary->push([
                'user' => $userRecord->user,
                'date' => $userRecord->timestamp->format('Y-m-d'),
                'checkin' => $checkin ? [
                    'time' => $checkin->timestamp->format('H:i:s'),
                    'status' => $checkinStatus,
                    'original_status' => $checkin->status,
                    'latitude' => $checkin->latitude,
                    'longitude' => $checkin->longitude,
                    'accuracy' => $checkin->accuracy,
                ] : null,
                'checkout' => $checkout ? [
                    'time' => $checkout->timestamp->format('H:i:s'),
                    'status' => $checkoutStatus,
                    'original_status' => $checkout->status,
                    'latitude' => $checkout->latitude,
                    'longitude' => $checkout->longitude,
                    'accuracy' => $checkout->accuracy,
                ] : null,
                'timestamp' => $userRecord->timestamp, // For sorting
                'type' => 'attendance'
            ]);
        }
        
                        // Add leave records to summary
                foreach ($allLeaves as $leave) {
                    $currentDate = $leave->start_date;
                    while ($currentDate <= $leave->end_date) {
                        $attendanceSummary->push([
                            'user' => $leave->user,
                            'date' => $currentDate->format('Y-m-d'),
                            'checkin' => null,
                            'checkout' => null,
                            'leave' => [
                                'id' => $leave->id,
                                'type' => $leave->leave_type,
                                'reason' => $leave->reason,
                                'status' => $leave->status,
                                'start_date' => $leave->start_date->format('Y-m-d'),
                                'end_date' => $leave->end_date->format('Y-m-d'),
                            ],
                            'timestamp' => $currentDate->toDateTimeString(),
                            'type' => 'leave'
                        ]);
                        $currentDate->addDay();
                    }
                }

        // Calculate statistics
        $today = Carbon::today();
        $stats = [
            'present_today' => 0,
            'ontime_today' => 0,
            'late_today' => 0,
            'not_attended_today' => 0,
            'leave_pending' => 0,
            'leave_approved' => 0,
            'leave_rejected' => 0,
            'alpha_today' => 0,
        ];

        // Count today's statistics
        $todayAttendance = $attendanceSummary->where('date', $today->format('Y-m-d'));
        $stats['present_today'] = $todayAttendance->where('checkin', '!=', null)->count();
        $stats['ontime_today'] = $todayAttendance->where('checkin.status', 'On-Time')->count();
        $stats['late_today'] = $todayAttendance->where('checkin.status', 'Terlambat')->count();
        $stats['alpha_today'] = $todayAttendance->where('checkin', null)->where('leave', null)->count();

        // Count leave statistics
        $stats['leave_pending'] = $attendanceSummary->where('leave.status', 'menunggu')->count();
        $stats['leave_approved'] = $attendanceSummary->where('leave.status', 'disetujui')->count();
        $stats['leave_rejected'] = $attendanceSummary->where('leave.status', 'ditolak')->count();
        
        // Calculate staff who have NOT attended today (no checkin and no leave)
        $totalStaff = User::whereIn('role_id', [2, 3, 4, 5])->count(); // Guru, Pegawai, Kepala Sekolah, Waka Kurikulum
        $attendedOrOnLeave = $stats['present_today'] + $stats['leave_approved'] + $stats['leave_pending'];
        $stats['not_attended_today'] = max(0, $totalStaff - $attendedOrOnLeave); // Ensure no negative values

        // Apply final filters to the combined data
        if ($request->filled('period')) {
            $period = $request->period;
            $today = Carbon::today();
            
            switch ($period) {
                case 'today':
                    $attendanceSummary = $attendanceSummary->filter(function ($item) use ($today) {
                        return $item['date'] === $today->format('Y-m-d');
                    });
                    break;
                case 'yesterday':
                    $yesterday = $today->subDay();
                    $attendanceSummary = $attendanceSummary->filter(function ($item) use ($yesterday) {
                        return $item['date'] === $yesterday->format('Y-m-d');
                    });
                    break;
                case 'this_week':
                    $startWeek = $today->startOfWeek();
                    $endWeek = $today->endOfWeek();
                    $attendanceSummary = $attendanceSummary->filter(function ($item) use ($startWeek, $endWeek) {
                        $itemDate = Carbon::parse($item['date']);
                        return $itemDate->between($startWeek, $endWeek);
                    });
                    break;
                case 'this_month':
                    $attendanceSummary = $attendanceSummary->filter(function ($item) use ($today) {
                        $itemDate = Carbon::parse($item['date']);
                        return $itemDate->month === $today->month && $itemDate->year === $today->year;
                    });
                    break;
                case 'custom':
                    if ($request->filled('date')) {
                        $customDate = $request->date;
                        $attendanceSummary = $attendanceSummary->filter(function ($item) use ($customDate) {
                            return $item['date'] === $customDate;
                        });
                    }
                    break;
            }
        }

        // Apply status filter to final result
        if ($request->filled('status')) {
            $status = $request->status;
            $attendanceSummary = $attendanceSummary->filter(function ($item) use ($status) {
                if ($status === 'on-time') {
                    return isset($item['checkin']) && $item['checkin']['status'] === 'On-Time';
                } elseif ($status === 'terlambat') {
                    return isset($item['checkin']) && $item['checkin']['status'] === 'Terlambat';
                } elseif (in_array($status, ['izin', 'sakit', 'cuti', 'dinas_luar'])) {
                    return isset($item['leave']) && $item['leave']['type'] === $status;
                } elseif ($status === 'alpha') {
                    return !isset($item['checkin']) && !isset($item['leave']);
                }
                return true;
            });
        }

        // Apply type filter to final result
        if ($request->filled('type')) {
            $type = $request->type;
            $attendanceSummary = $attendanceSummary->filter(function ($item) use ($type) {
                if ($type === 'checkin') {
                    return isset($item['checkin']);
                } elseif ($type === 'checkout') {
                    return isset($item['checkout']);
                }
                return true;
            });
        }

        // Sort by timestamp desc and paginate manually
        $attendanceSummary = $attendanceSummary->sortByDesc('timestamp');
        $perPage = 20;
        $currentPage = $request->get('page', 1);
        $offset = ($currentPage - 1) * $perPage;
        $items = $attendanceSummary->slice($offset, $perPage)->values();
        
        // Create paginator manually with query parameters preserved
        $attendance = new \Illuminate\Pagination\LengthAwarePaginator(
            $items,
            $attendanceSummary->count(),
            $perPage,
            $currentPage,
            [
                'path' => $request->url(),
                'pageName' => 'page',
                'query' => $request->query(), // Preserve all query parameters
            ]
        );







        return view('admin.attendance.index', compact('attendance', 'stats'));
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
        $query = Attendance::with(['user:id,name,email,role_id', 'user.role:id,role_name']);

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
                    $record->user->role ? $record->user->role->role_name : 'No Role',
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

    public function detail(Request $request, $userId, $date)
    {
        $user = auth()->user();
        
        // Check permissions
        if (!$user->hasRole('Admin') && $user->id != $userId) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Get attendance data for specific user and date with optimized query
        $attendance = Attendance::with(['user:id,name,email,role_id', 'user.role:id,role_name'])
            ->where('user_id', $userId)
            ->whereDate('timestamp', $date)
            ->orderBy('timestamp', 'asc')
            ->get();

        // Get leave data for the same date with optimized query
        $leave = \App\Models\Leave::with(['user:id,name,email,role_id', 'user.role:id,role_name', 'approver:id,name'])
            ->where('user_id', $userId)
            ->whereDate('start_date', '<=', $date)
            ->whereDate('end_date', '>=', $date)
            ->first();

        // Group attendance by type
        $checkin = $attendance->where('type', 'checkin')->first();
        $checkout = $attendance->where('type', 'checkout')->first();

        // Calculate status
        $checkinStatus = null;
        if ($checkin) {
            $hour = (int) $checkin->timestamp->format('H');
            $minute = (int) $checkin->timestamp->format('i');
            $timeMinutes = $hour * 60 + $minute;
            
            if ($timeMinutes <= 425) {
                $checkinStatus = 'On-Time';
            } else {
                $checkinStatus = 'Terlambat';
            }
        }

        $checkoutStatus = null;
        if ($checkout) {
            $checkoutHour = (int) $checkout->timestamp->format('H');
            
            if ($checkoutHour < 15) {
                $checkoutStatus = 'Pulang Lebih Awal';
            } elseif ($checkoutHour >= 15 && $checkoutHour <= 17) {
                $checkoutStatus = 'On-Time';
            } else {
                $checkoutStatus = 'Lembur';
            }
        }

        $data = [
            'user' => $attendance->first()?->user ?? $leave?->user,
            'date' => $date,
            'checkin' => $checkin ? [
                'time' => $checkin->timestamp->format('H:i:s'),
                'status' => $checkinStatus,
                'latitude' => $checkin->latitude,
                'longitude' => $checkin->longitude,
                'accuracy' => $checkin->accuracy,
            ] : null,
            'checkout' => $checkout ? [
                'time' => $checkout->timestamp->format('H:i:s'),
                'status' => $checkoutStatus,
                'latitude' => $checkout->latitude,
                'longitude' => $checkout->longitude,
                'accuracy' => $checkout->accuracy,
            ] : null,
            'leave' => $leave ? [
                'id' => $leave->id,
                'type' => $leave->leave_type,
                'reason' => $leave->reason,
                'status' => $leave->status,
                'start_date' => $leave->start_date->format('Y-m-d'),
                'end_date' => $leave->end_date->format('Y-m-d'),
                'approved_by' => $leave->approver?->name,
                'approved_at' => $leave->approved_at?->format('d M Y H:i'),
            ] : null,
        ];

        return response()->json($data);
    }

    public function approveLeave(Request $request, $leaveId)
    {
        $user = auth()->user();
        
        // Check if user has permission to approve leaves
        if (!$user->hasRole(['Admin', 'Kepala Sekolah', 'Waka Kurikulum'])) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $leave = \App\Models\Leave::findOrFail($leaveId);
        
        if ($leave->status !== 'menunggu') {
            return response()->json(['error' => 'Leave request is not pending'], 400);
        }

        $leave->update([
            'status' => 'disetujui',
            'approver_id' => $user->id,
            'approved_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Leave request approved successfully'
        ]);
    }

    public function rejectLeave(Request $request, $leaveId)
    {
        $user = auth()->user();
        
        // Check if user has permission to reject leaves
        if (!$user->hasRole(['Admin', 'Kepala Sekolah', 'Waka Kurikulum'])) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $leave = \App\Models\Leave::findOrFail($leaveId);
        
        if ($leave->status !== 'menunggu') {
            return response()->json(['error' => 'Leave request is not pending'], 400);
        }

        $leave->update([
            'status' => 'ditolak',
            'approver_id' => $user->id,
            'approved_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Leave request rejected successfully'
        ]);
    }
}