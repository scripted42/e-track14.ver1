<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\AttendanceQr;
use App\Models\User;
use App\Models\AuditLog;
use Carbon\Carbon;
use Illuminate\Http\Request;

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