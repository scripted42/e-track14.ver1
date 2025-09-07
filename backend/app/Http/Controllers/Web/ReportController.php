<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\StudentAttendance;
use App\Models\Leave;
use App\Models\User;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        // Get filter parameters
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));
        $reportType = $request->get('report_type', 'all');
        $search = $request->get('search', '');
        
        // Convert dates to Carbon instances
        $startDate = Carbon::parse($startDate);
        $endDate = Carbon::parse($endDate)->endOfDay();
        
        // Current month statistics (for compatibility)
        $currentMonth = Carbon::now();
        
        // Employee attendance summary with date filtering
        $employeeQuery = Attendance::whereBetween('timestamp', [$startDate, $endDate]);
        
        if ($search) {
            $employeeQuery->whereHas('user', function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }
        
        $totalEmployees = User::whereIn('role_id', [2, 3, 5])->count();
        $todayAttendance = Attendance::whereDate('timestamp', today())
            ->whereHas('user', function($q) {
                $q->whereNotIn('role_id', [6]); // Exclude Kepala Sekolah
            })
            ->distinct('user_id')
            ->count();
        $lateToday = Attendance::whereDate('timestamp', today())
            ->where('type', 'checkin')
            ->whereRaw('(HOUR(timestamp) * 60 + MINUTE(timestamp)) > 425')
            ->whereHas('user', function($q) {
                $q->whereNotIn('role_id', [6]); // Exclude Kepala Sekolah
            })
            ->distinct('user_id')
            ->count();
        
        $employeeStats = [
            'total_employees' => $totalEmployees,
            'monthly_attendance' => $employeeQuery->distinct('user_id')->count(),
            'today_attendance' => $todayAttendance,
            'late_today' => $lateToday,
            'attendance_rate' => $totalEmployees > 0 ? round(($todayAttendance / $totalEmployees) * 100, 1) : 0,
        ];
        
        // Student attendance summary with date filtering
        $studentQuery = StudentAttendance::whereBetween('created_at', [$startDate, $endDate]);
        
        if ($search) {
            $studentQuery->whereHas('student', function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('class_name', 'LIKE', "%{$search}%");
            });
        }
        
        $totalStudents = Student::count();
        $todayStudentAttendance = StudentAttendance::whereDate('created_at', today())->count();
        
        $studentStats = [
            'total_students' => $totalStudents,
            'monthly_attendance' => $studentQuery->count(),
            'today_attendance' => $todayStudentAttendance,
            'classes_count' => Student::distinct('class_name')->count(),
            'attendance_rate' => $totalStudents > 0 ? round(($todayStudentAttendance / $totalStudents) * 100, 1) : 0,
        ];
        
        // Leave summary with date filtering
        $leaveQuery = Leave::whereBetween('start_date', [$startDate, $endDate]);
        
        if ($search) {
            $leaveQuery->whereHas('user', function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }
        
        $totalLeaves = $leaveQuery->count();
        $approvedLeaves = $leaveQuery->where('status', 'disetujui')->count();
        
        $leaveStats = [
            'total_leaves' => $totalLeaves,
            'pending_leaves' => Leave::where('status', 'menunggu')->count(),
            'approved_leaves' => $approvedLeaves,
            'rejected_leaves' => $leaveQuery->where('status', 'ditolak')->count(),
            'approval_rate' => $totalLeaves > 0 ? round(($approvedLeaves / $totalLeaves) * 100, 1) : 0,
        ];
        
        // Calculate stakeholder satisfaction based on system performance indicators
        $attendanceRate = $totalEmployees > 0 ? round(($todayAttendance / $totalEmployees) * 100, 1) : 0;
        $studentAttendanceRate = $totalStudents > 0 ? round(($todayStudentAttendance / $totalStudents) * 100, 1) : 0;
        $leaveApprovalRate = $totalLeaves > 0 ? round(($approvedLeaves / $totalLeaves) * 100, 1) : 0;
        
        // Stakeholder satisfaction is calculated based on:
        // - Employee attendance rate (40%)
        // - Student attendance rate (30%) 
        // - Leave approval efficiency (30%)
        $stakeholderSatisfaction = round(
            ($attendanceRate * 0.4) + 
            ($studentAttendanceRate * 0.3) + 
            ($leaveApprovalRate * 0.3), 
            1
        );
        
        $employeeStats['satisfaction_rate'] = $stakeholderSatisfaction;
        
        // Attendance trend (filtered by date range)
        $attendanceTrend = $this->getAttendanceTrend($startDate, $endDate);
        
        // Top performers (filtered by date range and search)
        $topPerformers = $this->getTopPerformers($startDate, $endDate, $search);
        
        // Real data for charts
        $chartData = $this->getChartData($startDate, $endDate);
        
        // Department performance (from comprehensive report)
        $departmentStats = $this->getDepartmentPerformance($startDate, $endDate);
        
        // Academic indicators (from comprehensive report)
        $academicIndicators = $this->getAcademicIndicators($startDate, $endDate);
        
        return view('admin.reports.index', compact(
            'employeeStats',
            'studentStats',
            'leaveStats',
            'attendanceTrend',
            'topPerformers',
            'chartData',
            'departmentStats',
            'academicIndicators',
            'startDate',
            'endDate',
            'reportType',
            'search'
        ));
    }
    
    public function attendance(Request $request)
    {
        // Get filter parameters
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));
        $search = $request->get('search', '');
        $status = $request->get('status', '');
        
        // Convert dates to Carbon instances
        $startDate = Carbon::parse($startDate);
        $endDate = Carbon::parse($endDate)->endOfDay();
        
        // Build query to get attendance data grouped by user and date
        $query = Attendance::with(['user:id,name,email,role_id', 'user.role:id,role_name'])
            ->whereBetween('timestamp', [$startDate, $endDate]);
        
        // Apply search filter
        if ($search) {
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }
        
        // Get all attendance records
        $attendanceRecords = $query->orderBy('timestamp', 'desc')->get();
        
        // Group by user and date to combine checkin/checkout
        $groupedData = [];
        foreach ($attendanceRecords as $record) {
            $date = Carbon::parse($record->timestamp)->format('Y-m-d');
            $userId = $record->user_id;
            $key = $userId . '_' . $date;
            
            if (!isset($groupedData[$key])) {
                $groupedData[$key] = [
                    'user' => $record->user,
                    'date' => $date,
                    'checkin_time' => null,
                    'checkout_time' => null,
                    'checkin_status' => null,
                    'checkout_status' => null,
                    'notes' => $record->notes,
                    'timestamp' => $record->timestamp
                ];
            }
            
            if ($record->type === 'checkin') {
                $groupedData[$key]['checkin_time'] = Carbon::parse($record->timestamp)->format('H:i');
                $groupedData[$key]['checkin_status'] = $record->status;
                $groupedData[$key]['timestamp'] = $record->timestamp; // Keep latest timestamp for sorting
            } elseif ($record->type === 'checkout') {
                $groupedData[$key]['checkout_time'] = Carbon::parse($record->timestamp)->format('H:i');
                $groupedData[$key]['checkout_status'] = $record->status;
            }
        }
        
        // Apply status filter to grouped data
        if ($status) {
            $groupedData = array_filter($groupedData, function($item) use ($status) {
                if ($status === 'terlambat') {
                    return $item['checkin_status'] === 'terlambat';
                } elseif ($status === 'hadir') {
                    return $item['checkin_status'] === 'hadir';
                }
                return true;
            });
        }
        
        // Convert to collection and paginate manually
        $data = collect($groupedData)->sortByDesc('timestamp');
        $perPage = 15;
        $currentPage = $request->get('page', 1);
        $offset = ($currentPage - 1) * $perPage;
        $items = $data->slice($offset, $perPage)->values();
        
        // Create paginator manually
        $data = new \Illuminate\Pagination\LengthAwarePaginator(
            $items,
            $data->count(),
            $perPage,
            $currentPage,
            [
                'path' => $request->url(),
                'pageName' => 'page',
            ]
        );
        
        // Calculate summary statistics
        $totalStaff = User::whereIn('role_id', [2, 3, 5])->count(); // Guru, Pegawai, Waka Kurikulum (exclude Kepala Sekolah)
        $presentToday = Attendance::whereDate('timestamp', today())
            ->where('type', 'checkin')
            ->whereRaw('(HOUR(timestamp) * 60 + MINUTE(timestamp)) <= 425')
            ->distinct('user_id')
            ->count();
        $lateToday = Attendance::whereDate('timestamp', today())
            ->where('type', 'checkin')
            ->whereRaw('(HOUR(timestamp) * 60 + MINUTE(timestamp)) > 425')
            ->distinct('user_id')
            ->count();
        $absentToday = $totalStaff - $presentToday - $lateToday;
        
        return view('admin.reports.attendance', compact(
            'data',
            'totalStaff',
            'presentToday',
            'lateToday',
            'absentToday',
            'startDate',
            'endDate',
            'search',
            'status'
        ))->with('title', 'Laporan Kehadiran Staff');
    }
    
    public function leaves(Request $request)
    {
        // Get filter parameters
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));
        $search = $request->get('search', '');
        $status = $request->get('status', '');
        
        // Convert dates to Carbon instances
        $startDate = Carbon::parse($startDate);
        $endDate = Carbon::parse($endDate)->endOfDay();
        
        // Build query
        $query = Leave::with(['user:id,name,email,role_id', 'user.role:id,role_name', 'approver:id,name'])
            ->whereBetween('start_date', [$startDate, $endDate]);
        
        // Apply search filter
        if ($search) {
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }
        
        // Apply status filter
        if ($status) {
            $query->where('status', $status);
        }
        
        // Get paginated data
        $data = $query->orderBy('created_at', 'desc')->paginate(15);
        
        // Calculate summary statistics
        $totalLeaves = Leave::whereBetween('start_date', [$startDate, $endDate])->count();
        $pendingLeaves = Leave::whereBetween('start_date', [$startDate, $endDate])
            ->where('status', 'menunggu')
            ->count();
        $approvedLeaves = Leave::whereBetween('start_date', [$startDate, $endDate])
            ->where('status', 'disetujui')
            ->count();
        $rejectedLeaves = Leave::whereBetween('start_date', [$startDate, $endDate])
            ->where('status', 'ditolak')
            ->count();
        
        return view('admin.reports.leaves', compact(
            'data',
            'totalLeaves',
            'pendingLeaves',
            'approvedLeaves',
            'rejectedLeaves',
            'startDate',
            'endDate',
            'search',
            'status'
        ))->with('title', 'Laporan Izin & Cuti');
    }
    
    public function students(Request $request)
    {
        // Get filter parameters
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));
        $search = $request->get('search', '');
        $status = $request->get('status', '');
        
        // Convert dates to Carbon instances
        $startDate = Carbon::parse($startDate);
        $endDate = Carbon::parse($endDate)->endOfDay();
        
        // Build query
        $query = StudentAttendance::with(['student:id,name,class_room_id,status', 'student.classRoom:id,name', 'teacher:id,name'])
            ->whereBetween('created_at', [$startDate, $endDate]);
        
        // Apply search filter
        if ($search) {
            $query->whereHas('student', function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%");
            });
        }
        
        // Apply status filter
        if ($status) {
            $query->where('status', $status);
        }
        
        // Get paginated data
        $data = $query->orderBy('created_at', 'desc')->paginate(15);
        
        // Calculate summary statistics
        $totalStudents = Student::where('status', 'aktif')->count();
        $presentToday = StudentAttendance::whereDate('created_at', today())
            ->where('status', 'hadir')
            ->count();
        $lateToday = StudentAttendance::whereDate('created_at', today())
            ->where('status', 'terlambat')
            ->count();
        $absentToday = StudentAttendance::whereDate('created_at', today())
            ->where('status', 'alpha')
            ->count();
        
        return view('admin.reports.students', compact(
            'data',
            'totalStudents',
            'presentToday',
            'lateToday',
            'absentToday',
            'startDate',
            'endDate',
            'search',
            'status'
        ))->with('title', 'Laporan Kehadiran Siswa');
    }
    
    public function export(Request $request, $type)
    {
        switch ($type) {
            case 'attendance':
                return $this->exportAttendance($request);
            case 'leaves':
                return $this->exportLeaves($request);
            case 'students':
                return $this->exportStudents($request);
            default:
                return redirect()->back()->with('error', 'Invalid export type');
        }
    }
    
    private function getAttendanceTrend($startDate = null, $endDate = null)
    {
        $data = [];
        
        // If no dates provided, use last 30 days
        if (!$startDate || !$endDate) {
            $startDate = Carbon::now()->subDays(29);
            $endDate = Carbon::now();
        }
        
        $period = $startDate->diffInDays($endDate);
        
        for ($i = 0; $i <= $period; $i++) {
            $date = $startDate->copy()->addDays($i);
            $employeeCount = Attendance::whereDate('timestamp', $date)
                ->distinct('user_id')
                ->count();
            $studentCount = StudentAttendance::whereDate('created_at', $date)->count();
            
            $data[] = [
                'date' => $date->format('Y-m-d'),
                'employees' => $employeeCount,
                'students' => $studentCount,
            ];
        }
        
        return $data;
    }
    
    private function getTopPerformers($startDate = null, $endDate = null, $search = '')
    {
        // If no dates provided, use current month
        if (!$startDate || !$endDate) {
            $currentMonth = Carbon::now();
            $startDate = $currentMonth->copy()->startOfMonth();
            $endDate = $currentMonth->copy()->endOfMonth();
        }
        
        $query = DB::table('attendance')
            ->select(
                'users.name',
                DB::raw('COUNT(DISTINCT DATE(timestamp)) as days_present'),
                DB::raw('COUNT(*) as total_records')
            )
            ->join('users', 'attendance.user_id', '=', 'users.id')
            ->whereBetween('attendance.timestamp', [$startDate, $endDate])
            ->whereIn('attendance.status', ['hadir', 'terlambat'])
            ->groupBy('users.id', 'users.name')
            ->orderByDesc('days_present');
            
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('users.name', 'LIKE', "%{$search}%")
                  ->orWhere('users.email', 'LIKE', "%{$search}%");
            });
        }
            
        return $query->take(10)->get();
    }
    
    
    private function getChartData($startDate, $endDate)
    {
        // Get real attendance data for the last 30 days
        $attendanceData = [];
        $studentAttendanceData = [];
        $labels = [];
        
        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $labels[] = $date->format('d/m');
            
            // Employee attendance for this day
            $employeeCount = Attendance::whereDate('timestamp', $date->format('Y-m-d'))
                ->whereIn('status', ['hadir', 'terlambat'])
                ->distinct('user_id')
                ->count();
            $attendanceData[] = $employeeCount;
            
            // Student attendance for this day
            $studentCount = StudentAttendance::whereDate('created_at', $date->format('Y-m-d'))
                ->where('status', 'hadir')
                ->count();
            $studentAttendanceData[] = $studentCount;
        }
        
        // Get monthly comparison data (last 12 months)
        $monthlyEmployeeData = [];
        $monthlyStudentData = [];
        $monthlyLabels = [];
        
        for ($i = 11; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $monthlyLabels[] = $month->format('M');
            
            // Employee attendance rate for this month
            $employeeAttendance = Attendance::whereYear('timestamp', $month->year)
                ->whereMonth('timestamp', $month->month)
                ->whereIn('status', ['hadir', 'terlambat'])
                ->distinct('user_id')
                ->count();
            
            $totalEmployees = User::whereIn('role_id', [2, 3, 5])->count(); // Guru, Pegawai, Waka Kurikulum (exclude Kepala Sekolah)
            $employeeRate = $totalEmployees > 0 ? round(($employeeAttendance / $totalEmployees) * 100, 1) : 0;
            $monthlyEmployeeData[] = $employeeRate;
            
            // Student attendance rate for this month
            $studentAttendance = StudentAttendance::whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->where('status', 'hadir')
                ->count();
            
            $totalStudents = Student::count();
            $studentRate = $totalStudents > 0 ? round(($studentAttendance / $totalStudents) * 100, 1) : 0;
            $monthlyStudentData[] = $studentRate;
        }
        
        // Get class attendance data
        $classAttendanceData = [];
        $classLabels = [];
        
        $classes = Student::select('class_name')
            ->distinct()
            ->orderBy('class_name')
            ->get();
            
        foreach ($classes as $class) {
            $classLabels[] = $class->class_name;
            
            $classStudents = Student::where('class_name', $class->class_name)->count();
            $classAttendance = StudentAttendance::whereHas('student', function($query) use ($class) {
                $query->where('class_name', $class->class_name);
            })
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'hadir')
            ->count();
            
            $attendanceRate = $classStudents > 0 ? round(($classAttendance / $classStudents) * 100, 1) : 0;
            $classAttendanceData[] = $attendanceRate;
        }
        
        return [
            'attendance_trend' => [
                'labels' => $labels,
                'employee_data' => $attendanceData,
                'student_data' => $studentAttendanceData
            ],
            'monthly_comparison' => [
                'labels' => $monthlyLabels,
                'employee_data' => $monthlyEmployeeData,
                'student_data' => $monthlyStudentData
            ],
            'class_attendance' => [
                'labels' => $classLabels,
                'data' => $classAttendanceData
            ]
        ];
    }
    
    // Export methods for Attendance
    public function attendanceExportExcel(Request $request)
    {
        // Get the same data as attendance method
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));
        $search = $request->get('search', '');
        $status = $request->get('status', '');
        
        $startDate = Carbon::parse($startDate);
        $endDate = Carbon::parse($endDate)->endOfDay();
        
        // Build query to get attendance data grouped by user and date
        $query = Attendance::with(['user:id,name,email,role_id', 'user.role:id,role_name'])
            ->whereBetween('timestamp', [$startDate, $endDate]);
        
        if ($search) {
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }
        
        // Get all attendance records
        $attendanceRecords = $query->orderBy('timestamp', 'desc')->get();
        
        // Group by user and date to combine checkin/checkout
        $groupedData = [];
        foreach ($attendanceRecords as $record) {
            $date = Carbon::parse($record->timestamp)->format('Y-m-d');
            $userId = $record->user_id;
            $key = $userId . '_' . $date;
            
            if (!isset($groupedData[$key])) {
                $groupedData[$key] = [
                    'user' => $record->user,
                    'date' => $date,
                    'checkin_time' => null,
                    'checkout_time' => null,
                    'checkin_status' => null,
                    'checkout_status' => null,
                    'notes' => $record->notes,
                    'timestamp' => $record->timestamp
                ];
            }
            
            if ($record->type === 'checkin') {
                $groupedData[$key]['checkin_time'] = Carbon::parse($record->timestamp)->format('H:i');
                $groupedData[$key]['checkin_status'] = $record->status;
                $groupedData[$key]['timestamp'] = $record->timestamp;
            } elseif ($record->type === 'checkout') {
                $groupedData[$key]['checkout_time'] = Carbon::parse($record->timestamp)->format('H:i');
                $groupedData[$key]['checkout_status'] = $record->status;
            }
        }
        
        // Apply status filter to grouped data
        if ($status) {
            $groupedData = array_filter($groupedData, function($item) use ($status) {
                if ($status === 'terlambat') {
                    return $item['checkin_status'] === 'terlambat';
                } elseif ($status === 'hadir') {
                    return $item['checkin_status'] === 'hadir';
                }
                return true;
            });
        }
        
        $data = collect($groupedData)->sortByDesc('timestamp');
        
        // Debug: Check if data is valid
        if ($data->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada data untuk diekspor.');
        }
        
        $filename = 'laporan_kehadiran_staff_' . now()->format('Y-m-d_H-i-s') . '.csv';
        
        // Return CSV response with correct content type
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0'
        ];
        
        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');
            
            // Add BOM for UTF-8
            fwrite($file, "\xEF\xBB\xBF");
            
            // Add headers
            fputcsv($file, ['No', 'Nama', 'Role', 'Tanggal', 'Check In', 'Check Out', 'Status', 'Keterangan']);
            
            // Add data
            foreach ($data as $index => $item) {
                $checkInTime = $item['checkin_time'] ?? '-';
                $checkOutTime = $item['checkout_time'] ?? '-';
                
                $status = '-';
                if ($item['checkin_status']) {
                    $status = $item['checkin_status'] === 'terlambat' ? 'Terlambat' : 'Hadir';
                }
                
                fputcsv($file, [
                    (int)$index + 1,
                    $item['user']->name ?? 'N/A',
                    $item['user']->role->role_name ?? 'No Role',
                    \Carbon\Carbon::createFromFormat('Y-m-d', $item['date'])->format('d/m/Y'),
                    $checkInTime,
                    $checkOutTime,
                    $status,
                    $item['notes'] ?? '-'
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
    
    public function attendanceExportPdf(Request $request)
    {
        // Get the same data as attendance method
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));
        $search = $request->get('search', '');
        $status = $request->get('status', '');
        
        $startDate = Carbon::parse($startDate);
        $endDate = Carbon::parse($endDate)->endOfDay();
        
        // Build query to get attendance data grouped by user and date
        $query = Attendance::with(['user:id,name,email,role_id', 'user.role:id,role_name'])
            ->whereBetween('timestamp', [$startDate, $endDate]);
        
        if ($search) {
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }
        
        // Get all attendance records
        $attendanceRecords = $query->orderBy('timestamp', 'desc')->get();
        
        // Group by user and date to combine checkin/checkout
        $groupedData = [];
        foreach ($attendanceRecords as $record) {
            $date = Carbon::parse($record->timestamp)->format('Y-m-d');
            $userId = $record->user_id;
            $key = $userId . '_' . $date;
            
            if (!isset($groupedData[$key])) {
                $groupedData[$key] = [
                    'user' => $record->user,
                    'date' => $date,
                    'checkin_time' => null,
                    'checkout_time' => null,
                    'checkin_status' => null,
                    'checkout_status' => null,
                    'notes' => $record->notes,
                    'timestamp' => $record->timestamp
                ];
            }
            
            if ($record->type === 'checkin') {
                $groupedData[$key]['checkin_time'] = Carbon::parse($record->timestamp)->format('H:i');
                $groupedData[$key]['checkin_status'] = $record->status;
                $groupedData[$key]['timestamp'] = $record->timestamp;
            } elseif ($record->type === 'checkout') {
                $groupedData[$key]['checkout_time'] = Carbon::parse($record->timestamp)->format('H:i');
                $groupedData[$key]['checkout_status'] = $record->status;
            }
        }
        
        // Apply status filter to grouped data
        if ($status) {
            $groupedData = array_filter($groupedData, function($item) use ($status) {
                if ($status === 'terlambat') {
                    return $item['checkin_status'] === 'terlambat';
                } elseif ($status === 'hadir') {
                    return $item['checkin_status'] === 'hadir';
                }
                return true;
            });
        }
        
        $data = collect($groupedData)->sortByDesc('timestamp');
        
        // Debug: Check if data is valid
        if ($data->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada data untuk diekspor.');
        }
        
        $pdf = Pdf::loadView('admin.reports.attendance-pdf', compact('data', 'startDate', 'endDate'));
        $pdf->setPaper('A4', 'landscape');
        
        return $pdf->download('laporan_kehadiran_staff_' . now()->format('Y-m-d_H-i-s') . '.pdf');
    }
    
    
    // Strategic report for Kepala Sekolah
    public function strategic(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfYear()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));
        
        $startDate = Carbon::parse($startDate);
        $endDate = Carbon::parse($endDate)->endOfDay();
        
        // Strategic KPIs
        $strategicKPIs = $this->getStrategicKPIs($startDate, $endDate);
        
        // Trend analysis
        $trendAnalysis = $this->getTrendAnalysis($startDate, $endDate);
        
        // Risk assessment
        $riskAssessment = $this->getRiskAssessment($startDate, $endDate);
        
        // Strategic recommendations
        $strategicRecommendations = $this->getStrategicRecommendations($strategicKPIs, $riskAssessment);
        
        return view('admin.reports.strategic', compact(
            'strategicKPIs',
            'trendAnalysis',
            'riskAssessment',
            'strategicRecommendations',
            'startDate',
            'endDate'
        ));
    }
    
    // Class-specific report for Guru
    public function classReport(Request $request, $classId)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));
        
        $startDate = Carbon::parse($startDate);
        $endDate = Carbon::parse($endDate)->endOfDay();
        
        // Get class information
        $class = \App\Models\ClassRoom::findOrFail($classId);
        
        // Get students in this class
        $students = Student::where('class_room_id', $classId)->get();
        
        // Get attendance data for this class
        $attendanceData = StudentAttendance::whereIn('student_id', $students->pluck('id'))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->with('student')
            ->get();
        
        // Calculate class statistics
        $classStats = $this->getClassStatistics($students, $attendanceData, $startDate, $endDate);
        
        return view('admin.reports.class', compact(
            'class',
            'students',
            'attendanceData',
            'classStats',
            'startDate',
            'endDate'
        ));
    }
    
    // My students report for Guru
    public function myStudentsReport(Request $request)
    {
        $user = auth()->user();
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));
        
        $startDate = Carbon::parse($startDate);
        $endDate = Carbon::parse($endDate)->endOfDay();
        
        // Get classes taught by this teacher
        $myClasses = $user->classRooms;
        $classIds = $myClasses->pluck('id');
        
        // Get students in my classes
        $myStudents = Student::whereIn('class_room_id', $classIds)->get();
        
        // Get attendance data
        $attendanceData = StudentAttendance::whereIn('student_id', $myStudents->pluck('id'))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->with('student')
            ->get();
        
        // Calculate statistics
        $myStats = $this->getMyStudentsStatistics($myStudents, $attendanceData, $startDate, $endDate);
        
        return view('admin.reports.my-students', compact(
            'myClasses',
            'myStudents',
            'attendanceData',
            'myStats',
            'startDate',
            'endDate'
        ));
    }
    
    // Export methods for my-students report
    public function myStudentsExportExcel(Request $request)
    {
        $user = auth()->user();
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));
        
        $startDate = Carbon::parse($startDate);
        $endDate = Carbon::parse($endDate)->endOfDay();
        
        // Get classes taught by this teacher
        $myClasses = $user->classRooms;
        $classIds = $myClasses->pluck('id');
        
        // Get students in my classes
        $myStudents = Student::whereIn('class_room_id', $classIds)->get();
        
        // Get attendance data
        $attendanceData = StudentAttendance::whereIn('student_id', $myStudents->pluck('id'))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->with(['student:id,name,class_room_id', 'student.classRoom:id,name', 'teacher:id,name'])
            ->get();

        $filename = 'laporan_siswa_saya_' . now()->format('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0'
        ];

        $callback = function() use ($attendanceData, $myStudents, $startDate, $endDate) {
            $file = fopen('php://output', 'w');

            // Add BOM for UTF-8
            fwrite($file, "\xEF\xBB\xBF");

            // Add headers
            fputcsv($file, ['No', 'Nama Siswa', 'Kelas', 'Tanggal', 'Status', 'Waktu', 'Guru', 'Keterangan']);

            // Add data
            foreach ($attendanceData as $index => $item) {
                fputcsv($file, [
                    (int)$index + 1,
                    $item->student->name ?? 'N/A',
                    $item->student->classRoom->name ?? $item->student->class_name ?? 'N/A',
                    Carbon::parse($item->created_at)->format('d/m/Y'),
                    ucfirst($item->status ?? 'N/A'),
                    Carbon::parse($item->created_at)->format('H:i'),
                    $item->teacher->name ?? 'N/A',
                    $item->notes ?? '-'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function myStudentsExportPdf(Request $request)
    {
        $user = auth()->user();
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));
        
        $startDate = Carbon::parse($startDate);
        $endDate = Carbon::parse($endDate)->endOfDay();
        
        // Get classes taught by this teacher
        $myClasses = $user->classRooms;
        $classIds = $myClasses->pluck('id');
        
        // Get students in my classes
        $myStudents = Student::whereIn('class_room_id', $classIds)->get();
        
        // Get attendance data
        $attendanceData = StudentAttendance::whereIn('student_id', $myStudents->pluck('id'))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->with(['student:id,name,class_room_id', 'student.classRoom:id,name', 'teacher:id,name'])
            ->get();

        $pdf = Pdf::loadView('admin.reports.my-students-pdf', compact('attendanceData', 'myStudents', 'startDate', 'endDate', 'user'));
        $pdf->setPaper('A4', 'landscape');

        return $pdf->download('laporan_siswa_saya_' . now()->format('Y-m-d_H-i-s') . '.pdf');
    }

    // Helper methods
    private function getDepartmentPerformance($startDate, $endDate)
    {
        $departments = [
            'Waka Kurikulum' => 5,
            'Guru' => 2,
            'Pegawai' => 3
        ];
        
        $stats = [];
        foreach ($departments as $name => $roleId) {
            $total = User::where('role_id', $roleId)->count();
            $present = Attendance::whereHas('user', function($query) use ($roleId) {
                $query->where('role_id', $roleId);
            })->whereBetween('timestamp', [$startDate, $endDate])->distinct('user_id')->count();
            
            $stats[] = [
                'department' => $name,
                'total' => $total,
                'present' => $present,
                'percentage' => $total > 0 ? round(($present / $total) * 100, 1) : 0
            ];
        }
        
        return $stats;
    }
    
    private function getLeaveStatistics($startDate, $endDate)
    {
        return [
            'total' => Leave::whereBetween('start_date', [$startDate, $endDate])->count(),
            'pending' => Leave::whereBetween('start_date', [$startDate, $endDate])->where('status', 'menunggu')->count(),
            'approved' => Leave::whereBetween('start_date', [$startDate, $endDate])->where('status', 'disetujui')->count(),
            'rejected' => Leave::whereBetween('start_date', [$startDate, $endDate])->where('status', 'ditolak')->count(),
        ];
    }
    
    private function getAcademicIndicators($startDate, $endDate)
    {
        $totalStudents = Student::count();
        $presentStudents = StudentAttendance::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'hadir')
            ->distinct('student_id')
            ->count();
        
        return [
            'attendance_rate' => $totalStudents > 0 ? round(($presentStudents / $totalStudents) * 100, 1) : 0,
            'total_students' => $totalStudents,
            'present_students' => $presentStudents
        ];
    }
    
    private function getStrategicKPIs($startDate, $endDate)
    {
        // Calculate employee satisfaction based on attendance and punctuality
        $totalEmployees = User::whereIn('role_id', [2, 3, 5])->count();
        $presentEmployees = Attendance::whereBetween('timestamp', [$startDate, $endDate])
            ->whereHas('user', function($q) {
                $q->whereNotIn('role_id', [6]);
            })
            ->distinct('user_id')
            ->count();
        
        $onTimeEmployees = Attendance::whereBetween('timestamp', [$startDate, $endDate])
            ->where('type', 'checkin')
            ->whereRaw('(HOUR(timestamp) * 60 + MINUTE(timestamp)) <= 425')
            ->whereHas('user', function($q) {
                $q->whereNotIn('role_id', [6]);
            })
            ->distinct('user_id')
            ->count();
        
        $employeeSatisfaction = $totalEmployees > 0 ? round((($presentEmployees + $onTimeEmployees) / ($totalEmployees * 2)) * 100, 1) : 0;
        
        // Calculate student satisfaction based on attendance
        $totalStudents = Student::count();
        $presentStudents = StudentAttendance::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'hadir')
            ->distinct('student_id')
            ->count();
        
        $studentSatisfaction = $totalStudents > 0 ? round(($presentStudents / $totalStudents) * 100, 1) : 0;
        
        // Calculate parent satisfaction based on student attendance consistency
        $consistentStudents = StudentAttendance::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'hadir')
            ->select('student_id')
            ->groupBy('student_id')
            ->havingRaw('COUNT(*) >= ?', [($startDate->diffInDays($endDate) * 0.8)]) // 80% attendance threshold
            ->count();
        
        $parentSatisfaction = $totalStudents > 0 ? round(($consistentStudents / $totalStudents) * 100, 1) : 0;
        
        // Calculate academic achievement based on attendance rate
        $academicAchievement = $studentSatisfaction; // Using attendance as proxy for academic achievement
        
        // Calculate compliance rate based on system usage and data completeness
        $totalWorkDays = $startDate->diffInDays($endDate) + 1;
        $expectedAttendanceRecords = $totalEmployees * $totalWorkDays;
        $actualAttendanceRecords = Attendance::whereBetween('timestamp', [$startDate, $endDate])
            ->whereHas('user', function($q) {
                $q->whereNotIn('role_id', [6]);
            })
            ->count();
        
        $complianceRate = $expectedAttendanceRecords > 0 ? round(($actualAttendanceRecords / $expectedAttendanceRecords) * 100, 1) : 0;
        
        return [
            'employee_satisfaction' => $employeeSatisfaction,
            'student_satisfaction' => $studentSatisfaction,
            'parent_satisfaction' => $parentSatisfaction,
            'academic_achievement' => $academicAchievement,
            'compliance_rate' => $complianceRate,
            'overall_score' => round(($employeeSatisfaction + $studentSatisfaction + $parentSatisfaction + $academicAchievement + $complianceRate) / 5, 1)
        ];
    }
    
    private function getTrendAnalysis($startDate, $endDate)
    {
        // Monthly trends for the period
        $months = [];
        $current = $startDate->copy()->startOfMonth();
        $end = $endDate->copy()->endOfMonth();
        
        while ($current->lte($end)) {
            $monthStart = $current->copy();
            $monthEnd = $current->copy()->endOfMonth();
            
            $employeeAttendance = Attendance::whereBetween('timestamp', [$monthStart, $monthEnd])
                ->distinct('user_id')
                ->count();
            
            $studentAttendance = StudentAttendance::whereBetween('created_at', [$monthStart, $monthEnd])
                ->distinct('student_id')
                ->count();
            
            $months[] = [
                'month' => $current->format('M Y'),
                'employee_attendance' => $employeeAttendance,
                'student_attendance' => $studentAttendance
            ];
            
            $current->addMonth();
        }
        
        return $months;
    }
    
    private function getRiskAssessment($startDate, $endDate)
    {
        $highRiskAreas = [];
        $mediumRiskAreas = [];
        $lowRiskAreas = [];
        
        // Check attendance rates by class
        $classes = Student::select('class_name')
            ->distinct()
            ->orderBy('class_name')
            ->get();
            
        foreach ($classes as $class) {
            $classStudents = Student::where('class_name', $class->class_name)->count();
            $classAttendance = StudentAttendance::whereHas('student', function($query) use ($class) {
                $query->where('class_name', $class->class_name);
            })
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'hadir')
            ->count();
            
            $attendanceRate = $classStudents > 0 ? round(($classAttendance / $classStudents) * 100, 1) : 0;
            
            if ($attendanceRate < 70) {
                $highRiskAreas[] = "Kelas {$class->class_name}: Tingkat kehadiran rendah ({$attendanceRate}%)";
            } elseif ($attendanceRate < 85) {
                $mediumRiskAreas[] = "Kelas {$class->class_name}: Tingkat kehadiran sedang ({$attendanceRate}%)";
            } else {
                $lowRiskAreas[] = "Kelas {$class->class_name}: Tingkat kehadiran baik ({$attendanceRate}%)";
            }
        }
        
        // Check pending leave requests
        $pendingLeaves = Leave::where('status', 'menunggu')->count();
        if ($pendingLeaves > 10) {
            $highRiskAreas[] = "Backlog permohonan izin tinggi ({$pendingLeaves} permohonan)";
        } elseif ($pendingLeaves > 5) {
            $mediumRiskAreas[] = "Backlog permohonan izin sedang ({$pendingLeaves} permohonan)";
        } else {
            $lowRiskAreas[] = "Backlog permohonan izin rendah ({$pendingLeaves} permohonan)";
        }
        
        // Check employee tardiness
        $totalEmployees = User::whereIn('role_id', [2, 3, 5])->count();
        $lateEmployees = Attendance::whereBetween('timestamp', [$startDate, $endDate])
            ->where('type', 'checkin')
            ->whereRaw('(HOUR(timestamp) * 60 + MINUTE(timestamp)) > 425')
            ->whereHas('user', function($q) {
                $q->whereNotIn('role_id', [6]);
            })
            ->distinct('user_id')
            ->count();
        
        $tardinessRate = $totalEmployees > 0 ? round(($lateEmployees / $totalEmployees) * 100, 1) : 0;
        
        if ($tardinessRate > 30) {
            $highRiskAreas[] = "Tingkat keterlambatan karyawan tinggi ({$tardinessRate}%)";
        } elseif ($tardinessRate > 15) {
            $mediumRiskAreas[] = "Tingkat keterlambatan karyawan sedang ({$tardinessRate}%)";
        } else {
            $lowRiskAreas[] = "Tingkat keterlambatan karyawan rendah ({$tardinessRate}%)";
        }
        
        // Check system compliance
        $totalWorkDays = $startDate->diffInDays($endDate) + 1;
        $expectedRecords = $totalEmployees * $totalWorkDays;
        $actualRecords = Attendance::whereBetween('timestamp', [$startDate, $endDate])
            ->whereHas('user', function($q) {
                $q->whereNotIn('role_id', [6]);
            })
            ->count();
        
        $complianceRate = $expectedRecords > 0 ? round(($actualRecords / $expectedRecords) * 100, 1) : 0;
        
        if ($complianceRate < 80) {
            $highRiskAreas[] = "Tingkat kepatuhan sistem rendah ({$complianceRate}%)";
        } elseif ($complianceRate < 90) {
            $mediumRiskAreas[] = "Tingkat kepatuhan sistem sedang ({$complianceRate}%)";
        } else {
            $lowRiskAreas[] = "Tingkat kepatuhan sistem baik ({$complianceRate}%)";
        }
        
        return [
            'high_risk_areas' => $highRiskAreas,
            'medium_risk_areas' => $mediumRiskAreas,
            'low_risk_areas' => $lowRiskAreas,
            'overall_risk_level' => count($highRiskAreas) > 2 ? 'High' : (count($mediumRiskAreas) > 3 ? 'Medium' : 'Low')
        ];
    }
    
    private function getStrategicRecommendations($strategicKPIs, $riskAssessment)
    {
        $shortTermRecommendations = [];
        $longTermRecommendations = [];
        
        // Short-term recommendations based on current performance
        if ($strategicKPIs['employee_satisfaction'] < 80) {
            $shortTermRecommendations[] = "Implementasi program motivasi karyawan untuk meningkatkan kepuasan kerja";
        }
        
        if ($strategicKPIs['student_satisfaction'] < 85) {
            $shortTermRecommendations[] = "Review dan perbaiki sistem kehadiran siswa untuk meningkatkan partisipasi";
        }
        
        if ($strategicKPIs['compliance_rate'] < 90) {
            $shortTermRecommendations[] = "Sosialisasi dan pelatihan penggunaan sistem untuk meningkatkan kepatuhan";
        }
        
        // Long-term recommendations based on risk assessment
        if ($riskAssessment['overall_risk_level'] === 'High') {
            $longTermRecommendations[] = "Implementasi sistem monitoring real-time untuk mengidentifikasi masalah lebih cepat";
            $longTermRecommendations[] = "Pengembangan program peningkatan kualitas layanan pendidikan";
        }
        
        if (count($riskAssessment['high_risk_areas']) > 0) {
            $longTermRecommendations[] = "Pembentukan tim khusus untuk menangani area berisiko tinggi";
        }
        
        // General recommendations based on overall score
        if ($strategicKPIs['overall_score'] < 80) {
            $longTermRecommendations[] = "Implementasi sistem manajemen mutu ISO 21001:2018 secara menyeluruh";
            $longTermRecommendations[] = "Pengembangan dashboard eksekutif untuk monitoring real-time";
        }
        
        // Default recommendations if no specific issues
        if (empty($shortTermRecommendations)) {
            $shortTermRecommendations[] = "Pertahankan performa yang baik dengan monitoring berkala";
        }
        
        if (empty($longTermRecommendations)) {
            $longTermRecommendations[] = "Kembangkan inovasi dalam layanan pendidikan";
            $longTermRecommendations[] = "Ekspansi fitur sistem untuk mendukung pertumbuhan organisasi";
        }
        
        return [
            'short_term' => $shortTermRecommendations,
            'long_term' => $longTermRecommendations,
            'priority_level' => $riskAssessment['overall_risk_level'] === 'High' ? 'High' : 'Medium'
        ];
    }
    
    private function getClassStatistics($students, $attendanceData, $startDate, $endDate)
    {
        $totalStudents = $students->count();
        $presentStudents = $attendanceData->where('status', 'hadir')->count();
        $lateStudents = $attendanceData->where('status', 'terlambat')->count();
        $absentStudents = $totalStudents - $presentStudents - $lateStudents;
        
        return [
            'total_students' => $totalStudents,
            'present' => $presentStudents,
            'late' => $lateStudents,
            'absent' => $absentStudents,
            'attendance_rate' => $totalStudents > 0 ? round(($presentStudents / $totalStudents) * 100, 1) : 0
        ];
    }
    
    private function getMyStudentsStatistics($students, $attendanceData, $startDate, $endDate)
    {
        $totalStudents = $students->count();
        $presentStudents = $attendanceData->where('status', 'hadir')->count();
        $lateStudents = $attendanceData->where('status', 'terlambat')->count();
        
        return [
            'total_students' => $totalStudents,
            'present' => $presentStudents,
            'late' => $lateStudents,
            'attendance_rate' => $totalStudents > 0 ? round(($presentStudents / $totalStudents) * 100, 1) : 0,
            'classes_count' => $students->groupBy('class_room_id')->count()
        ];
    }
    
    // Export methods for Students
    public function studentsExportExcel(Request $request)
    {
        // Get the same data as students method
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));
        $search = $request->get('search', '');
        $status = $request->get('status', '');
        
        $startDate = Carbon::parse($startDate);
        $endDate = Carbon::parse($endDate)->endOfDay();
        
        $query = StudentAttendance::with(['student:id,name,class_room_id,status', 'student.classRoom:id,name', 'teacher:id,name'])
            ->whereBetween('created_at', [$startDate, $endDate]);
        
        if ($search) {
            $query->whereHas('student', function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%");
            });
        }
        
        if ($status) {
            $query->where('status', $status);
        }
        
        $data = $query->orderBy('created_at', 'desc')->get();
        
        $filename = 'laporan_kehadiran_siswa_' . now()->format('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0'
        ];
        
        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');
            
            // Add BOM for UTF-8
            fwrite($file, "\xEF\xBB\xBF");
            
            // Add headers
            fputcsv($file, ['No', 'Nama Siswa', 'Kelas', 'Tanggal', 'Status', 'Waktu', 'Guru', 'Keterangan']);
            
            // Add data
            foreach ($data as $index => $item) {
                fputcsv($file, [
                    (int)$index + 1,
                    $item->student->name ?? 'N/A',
                    $item->student->classRoom->name ?? $item->student->class_name ?? 'N/A',
                    Carbon::parse($item->created_at)->format('d/m/Y'),
                    ucfirst($item->status ?? 'N/A'),
                    Carbon::parse($item->created_at)->format('H:i'),
                    $item->teacher->name ?? 'N/A',
                    $item->notes ?? '-'
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
    
    public function studentsExportPdf(Request $request)
    {
        // Get the same data as students method
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));
        $search = $request->get('search', '');
        $status = $request->get('status', '');
        
        $startDate = Carbon::parse($startDate);
        $endDate = Carbon::parse($endDate)->endOfDay();
        
        $query = StudentAttendance::with(['student:id,name,class_room_id,status', 'student.classRoom:id,name', 'teacher:id,name'])
            ->whereBetween('created_at', [$startDate, $endDate]);
        
        if ($search) {
            $query->whereHas('student', function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%");
            });
        }
        
        if ($status) {
            $query->where('status', $status);
        }
        
        $data = $query->orderBy('created_at', 'desc')->get();
        
        $pdf = Pdf::loadView('admin.reports.students-pdf', compact('data', 'startDate', 'endDate'));
        $pdf->setPaper('A4', 'landscape');
        
        return $pdf->download('laporan_kehadiran_siswa_' . now()->format('Y-m-d_H-i-s') . '.pdf');
    }
    
    // Export methods for Leaves
    public function leavesExportExcel(Request $request)
    {
        // Get the same data as leaves method
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));
        $search = $request->get('search', '');
        $status = $request->get('status', '');
        
        $startDate = Carbon::parse($startDate);
        $endDate = Carbon::parse($endDate)->endOfDay();
        
        $query = Leave::with(['user:id,name,email,role_id', 'user.role:id,role_name', 'approver:id,name'])
            ->whereBetween('start_date', [$startDate, $endDate]);
        
        if ($search) {
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }
        
        if ($status) {
            $query->where('status', $status);
        }
        
        $data = $query->orderBy('created_at', 'desc')->get();
        
        $filename = 'laporan_izin_cuti_' . now()->format('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0'
        ];
        
        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');
            
            // Add BOM for UTF-8
            fwrite($file, "\xEF\xBB\xBF");
            
            // Add headers
            fputcsv($file, ['No', 'Nama', 'Role', 'Jenis Izin', 'Tanggal Mulai', 'Tanggal Selesai', 'Durasi', 'Status', 'Disetujui Oleh', 'Keterangan']);
            
            // Add data
            foreach ($data as $index => $item) {
                $duration = Carbon::parse($item->start_date)->diffInDays(Carbon::parse($item->end_date)) + 1;
                
                fputcsv($file, [
                    (int)$index + 1,
                    $item->user->name ?? 'N/A',
                    $item->user->role->role_name ?? 'No Role',
                    ucfirst($item->leave_type ?? 'N/A'),
                    Carbon::parse($item->start_date)->format('d/m/Y'),
                    Carbon::parse($item->end_date)->format('d/m/Y'),
                    $duration . ' hari',
                    ucfirst($item->status ?? 'N/A'),
                    $item->approver->name ?? '-',
                    $item->reason ?? '-'
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
    
    public function leavesExportPdf(Request $request)
    {
        // Get the same data as leaves method
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));
        $search = $request->get('search', '');
        $status = $request->get('status', '');
        
        $startDate = Carbon::parse($startDate);
        $endDate = Carbon::parse($endDate)->endOfDay();
        
        $query = Leave::with(['user:id,name,email,role_id', 'user.role:id,role_name', 'approver:id,name'])
            ->whereBetween('start_date', [$startDate, $endDate]);
        
        if ($search) {
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }
        
        if ($status) {
            $query->where('status', $status);
        }
        
        $data = $query->orderBy('created_at', 'desc')->get();
        
        $pdf = Pdf::loadView('admin.reports.leaves-pdf', compact('data', 'startDate', 'endDate'));
        $pdf->setPaper('A4', 'landscape');
        
        return $pdf->download('laporan_izin_cuti_' . now()->format('Y-m-d_H-i-s') . '.pdf');
    }
}