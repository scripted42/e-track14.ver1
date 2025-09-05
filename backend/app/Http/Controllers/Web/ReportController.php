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
        
        $employeeStats = [
            'total_employees' => User::whereIn('role_id', [2, 3, 4, 5])->count(), // Kepala Sekolah, Waka Kurikulum, Guru, Pegawai
            'monthly_attendance' => $employeeQuery->distinct('user_id')->count(),
            'today_attendance' => Attendance::whereDate('timestamp', today())
                ->distinct('user_id')
                ->count(),
            'late_today' => Attendance::whereDate('timestamp', today())
                ->where('status', 'terlambat')
                ->count(),
        ];
        
        // Student attendance summary with date filtering
        $studentQuery = StudentAttendance::whereBetween('created_at', [$startDate, $endDate]);
        
        if ($search) {
            $studentQuery->whereHas('student', function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('class_name', 'LIKE', "%{$search}%");
            });
        }
        
        $studentStats = [
            'total_students' => Student::count(),
            'monthly_attendance' => $studentQuery->count(),
            'today_attendance' => StudentAttendance::whereDate('created_at', today())->count(),
            'classes_count' => Student::distinct('class_name')->count(),
        ];
        
        // Leave summary with date filtering
        $leaveQuery = Leave::whereBetween('start_date', [$startDate, $endDate]);
        
        if ($search) {
            $leaveQuery->whereHas('user', function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }
        
        $leaveStats = [
            'total_leaves' => $leaveQuery->count(),
            'pending_leaves' => Leave::where('status', 'menunggu')->count(),
            'approved_leaves' => $leaveQuery->where('status', 'disetujui')->count(),
            'rejected_leaves' => $leaveQuery->where('status', 'ditolak')->count(),
        ];
        
        // Attendance trend (filtered by date range)
        $attendanceTrend = $this->getAttendanceTrend($startDate, $endDate);
        
        // Top performers (filtered by date range and search)
        $topPerformers = $this->getTopPerformers($startDate, $endDate, $search);
        
        // Real data for charts
        $chartData = $this->getChartData($startDate, $endDate);
        
        return view('admin.reports.index', compact(
            'employeeStats',
            'studentStats',
            'leaveStats',
            'attendanceTrend',
            'topPerformers',
            'chartData',
            'startDate',
            'endDate',
            'reportType',
            'search'
        ));
    }
    
    public function attendance(Request $request)
    {
        $year = $request->get('year', date('Y'));
        $month = $request->get('month', date('m'));
        $userId = $request->get('user_id');
        
        $query = Attendance::with(['user:id,name,email', 'user.role:id,role_name'])
            ->whereYear('timestamp', $year)
            ->whereMonth('timestamp', $month);
        
        if ($userId) {
            $query->where('user_id', $userId);
        }
        
        $attendance = $query->orderBy('timestamp', 'desc')->get();
        
        // Group by user and calculate statistics
        $userStats = $attendance->groupBy('user_id')->map(function ($userAttendance) {
            $user = $userAttendance->first()->user;
            $daysPresent = $userAttendance->where('status', 'hadir')->count();
            $daysLate = $userAttendance->where('status', 'terlambat')->count();
            $daysLeave = $userAttendance->whereIn('status', ['izin', 'sakit', 'cuti', 'dinas_luar'])->count();
            
            return [
                'user' => $user,
                'total_days' => $userAttendance->count(),
                'present' => $daysPresent,
                'late' => $daysLate,
                'leave' => $daysLeave,
                'attendance_rate' => $userAttendance->count() > 0 ? 
                    round(($daysPresent + $daysLate) / $userAttendance->count() * 100, 1) : 0
            ];
        });
        
        $users = User::whereIn('role_id', [2, 3, 4, 5])->orderBy('name')->get(['id', 'name']); // Kepala Sekolah, Waka Kurikulum, Guru, Pegawai
        
        return view('admin.reports.attendance', compact(
            'attendance',
            'userStats',
            'users',
            'year',
            'month',
            'userId'
        ));
    }
    
    public function leaves(Request $request)
    {
        $year = $request->get('year', date('Y'));
        $month = $request->get('month', date('m'));
        $status = $request->get('status');
        $leaveType = $request->get('leave_type');
        
        $query = Leave::with(['user:id,name,email', 'user.role:id,role_name', 'approver:id,name'])
            ->whereYear('start_date', $year)
            ->whereMonth('start_date', $month);
        
        if ($status) {
            $query->where('status', $status);
        }
        
        if ($leaveType) {
            $query->where('leave_type', $leaveType);
        }
        
        $leaves = $query->orderBy('created_at', 'desc')->get();
        
        // Statistics
        $stats = [
            'total' => $leaves->count(),
            'approved' => $leaves->where('status', 'disetujui')->count(),
            'rejected' => $leaves->where('status', 'ditolak')->count(),
            'pending' => $leaves->where('status', 'menunggu')->count(),
            'total_days' => $leaves->where('status', 'disetujui')->sum(function($leave) {
                return $leave->getDurationDays();
            })
        ];
        
        // Group by leave type
        $leaveTypeStats = $leaves->groupBy('leave_type')->map(function ($typeLeaves) {
            return [
                'count' => $typeLeaves->count(),
                'approved' => $typeLeaves->where('status', 'disetujui')->count(),
                'total_days' => $typeLeaves->where('status', 'disetujui')->sum(function($leave) {
                    return $leave->getDurationDays();
                })
            ];
        });
        
        return view('admin.reports.leaves', compact(
            'leaves',
            'stats',
            'leaveTypeStats',
            'year',
            'month',
            'status',
            'leaveType'
        ));
    }
    
    public function students(Request $request)
    {
        $year = $request->get('year', date('Y'));
        $month = $request->get('month', date('m'));
        $className = $request->get('class_name');
        
        $query = StudentAttendance::with(['student:id,name,class_name', 'teacher:id,name'])
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month);
        
        if ($className) {
            $query->whereHas('student', function($q) use ($className) {
                $q->where('class_name', $className);
            });
        }
        
        $attendance = $query->orderBy('created_at', 'desc')->get();
        
        // Group by student and calculate statistics
        $studentStats = $attendance->groupBy('student_id')->map(function ($studentAttendance) {
            $student = $studentAttendance->first()->student;
            $present = $studentAttendance->where('status', 'hadir')->count();
            $late = $studentAttendance->where('status', 'terlambat')->count();
            $absent = $studentAttendance->whereIn('status', ['izin', 'sakit', 'alpha'])->count();
            $total = $studentAttendance->count();
            
            return [
                'student' => $student,
                'total_days' => $total,
                'present' => $present,
                'late' => $late,
                'absent' => $absent,
                'attendance_rate' => $total > 0 ? 
                    round(($present + $late) / $total * 100, 1) : 0
            ];
        });
        
        // Group by class
        $classStats = $attendance->groupBy('student.class_name')->map(function ($classAttendance) {
            $present = $classAttendance->where('status', 'hadir')->count();
            $late = $classAttendance->where('status', 'terlambat')->count();
            $total = $classAttendance->count();
            
            return [
                'total_records' => $total,
                'present' => $present,
                'late' => $late,
                'absent' => $total - $present - $late,
                'attendance_rate' => $total > 0 ? round(($present + $late) / $total * 100, 1) : 0
            ];
        });
        
        // Get classes from class_rooms table first, then fallback to students table
        $classes = \App\Models\ClassRoom::orderBy('name')->pluck('name');
        
        // If no classes from class_rooms, get from students
        if ($classes->isEmpty()) {
            $classes = Student::distinct('class_name')->orderBy('class_name')->pluck('class_name');
        }
        
        // If still empty, create default classes
        if ($classes->isEmpty()) {
            $classes = collect([
                '7A', '7B', '7C', '7D',
                '8A', '8B', '8C', '8D', 
                '9A', '9B', '9C', '9D'
            ]);
        }
        
        return view('admin.reports.students', compact(
            'attendance',
            'studentStats',
            'classStats',
            'classes',
            'year',
            'month',
            'className'
        ));
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
    
    private function exportAttendance(Request $request)
    {
        // Implementation for attendance export
        return response()->json(['message' => 'Attendance export feature - To be implemented']);
    }
    
    private function exportLeaves(Request $request)
    {
        // Implementation for leaves export
        return response()->json(['message' => 'Leaves export feature - To be implemented']);
    }
    
    private function exportStudents(Request $request)
    {
        // Implementation for students export
        return response()->json(['message' => 'Students export feature - To be implemented']);
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
            
            $totalEmployees = User::whereIn('role_id', [2, 3, 4, 5])->count(); // Kepala Sekolah, Waka Kurikulum, Guru, Pegawai
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
}