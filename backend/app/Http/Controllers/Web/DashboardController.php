<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Student;
use App\Models\Attendance;
use App\Models\StudentAttendance;
use App\Models\Leave;
use App\Models\AttendanceQr;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $roleId = $user->role_id;
        
        // Route to appropriate dashboard based on role
        switch ($roleId) {
            case 1: // Admin
                return $this->adminDashboard();
            case 2: // Guru
                return $this->guruDashboard();
            case 3: // Pegawai
                return $this->pegawaiDashboard();
            case 5: // Waka Kurikulum
                return $this->wakaKurikulumDashboard();
            case 6: // Kepala Sekolah
                return $this->kepalaSekolahDashboard();
            default:
                return $this->adminDashboard(); // Default fallback
        }
    }
    
    private function adminDashboard()
    {
        // Get today's statistics
        $today = Carbon::today();
        
        // Employee/Teacher attendance today
        $todayAttendance = Attendance::whereDate('timestamp', $today)->count();
        $totalEmployees = User::whereIn('role_id', [2, 3])->count(); // Guru and Pegawai
        
        // Student attendance today
        $todayStudentAttendance = StudentAttendance::whereDate('created_at', $today)->count();
        $totalStudents = Student::count();
        
        // Leave requests
        $pendingLeaves = Leave::where('status', 'menunggu')->count();
        $approvedLeavesToday = Leave::where('status', 'disetujui')
            ->whereDate('approved_at', $today)
            ->count();
        
        // QR Code status
        $todayQr = AttendanceQr::whereDate('created_at', $today)
            ->where('valid_until', '>', now())
            ->first();
        
        // Recent activities
        $recentAttendance = Attendance::with('user:id,name')
            ->whereDate('timestamp', $today)
            ->orderBy('timestamp', 'desc')
            ->take(10)
            ->get();
        
        $recentLeaves = Leave::with('user:id,name')
            ->where('status', 'menunggu')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        
        // Weekly attendance chart data
        $weeklyData = $this->getWeeklyAttendanceData();
        
        // Monthly statistics
        $monthlyStats = $this->getMonthlyStats();
        
        return view('admin.dashboard', compact(
            'todayAttendance',
            'totalEmployees',
            'todayStudentAttendance',
            'totalStudents',
            'pendingLeaves',
            'approvedLeavesToday',
            'todayQr',
            'recentAttendance',
            'recentLeaves',
            'weeklyData',
            'monthlyStats'
        ));
    }
    
    private function guruDashboard()
    {
        $user = auth()->user();
        $today = Carbon::today();
        
        // Guru's own attendance today
        $myAttendanceToday = Attendance::where('user_id', $user->id)
            ->whereDate('timestamp', $today)
            ->get();
        
        // My attendance status today
        $isPresentToday = $myAttendanceToday->count() > 0;
        $isLateToday = $myAttendanceToday->where('status', 'terlambat')->count() > 0;
        
        // My students (if walikelas)
        $myStudents = collect();
        if ($user->classRooms()->count() > 0) {
            $myStudents = Student::whereIn('class_room_id', $user->classRooms()->pluck('id'))->get();
        }
        
        // My students attendance today
        $myStudentsPresentToday = StudentAttendance::whereIn('student_id', $myStudents->pluck('id'))
            ->whereDate('created_at', $today)
            ->count();
        
        $myStudentsLateToday = StudentAttendance::whereIn('student_id', $myStudents->pluck('id'))
            ->whereDate('created_at', $today)
            ->where('status', 'terlambat')
            ->count();
        
        $myStudentsAbsentToday = $myStudents->count() - $myStudentsPresentToday;
        
        // My leave requests
        $myLeaves = Leave::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        
        // My leave statistics this month
        $thisMonthLeaves = Leave::where('user_id', $user->id)
            ->whereYear('start_date', $today->year)
            ->whereMonth('start_date', $today->month)
            ->get();
        
        $myPendingLeaves = $thisMonthLeaves->where('status', 'menunggu')->count();
        $myApprovedLeaves = $thisMonthLeaves->where('status', 'disetujui')->count();
        $myRejectedLeaves = $thisMonthLeaves->where('status', 'ditolak')->count();
        
        // My recent attendance
        $myRecentAttendance = Attendance::where('user_id', $user->id)
            ->orderBy('timestamp', 'desc')
            ->take(10)
            ->get();
        
        // My attendance this month
        $thisMonthAttendance = Attendance::where('user_id', $user->id)
            ->whereYear('timestamp', $today->year)
            ->whereMonth('timestamp', $today->month)
            ->count();
        
        $thisMonthLate = Attendance::where('user_id', $user->id)
            ->whereYear('timestamp', $today->year)
            ->whereMonth('timestamp', $today->month)
            ->where('status', 'terlambat')
            ->count();
        
        // Weekly attendance for my students
        $weeklyStudentData = $this->getWeeklyStudentAttendanceData($myStudents->pluck('id'));
        
        return view('admin.dashboard', compact(
            'isPresentToday',
            'isLateToday',
            'myStudents',
            'myStudentsPresentToday',
            'myStudentsLateToday',
            'myStudentsAbsentToday',
            'myLeaves',
            'myPendingLeaves',
            'myApprovedLeaves',
            'myRejectedLeaves',
            'myRecentAttendance',
            'thisMonthAttendance',
            'thisMonthLate',
            'weeklyStudentData'
        ));
    }
    
    private function pegawaiDashboard()
    {
        $user = auth()->user();
        $today = Carbon::today();
        
        // My attendance today
        $myAttendanceToday = Attendance::where('user_id', $user->id)
            ->whereDate('timestamp', $today)
            ->get();
        
        // My leave requests
        $myLeaves = Leave::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        
        // My recent attendance
        $myRecentAttendance = Attendance::where('user_id', $user->id)
            ->orderBy('timestamp', 'desc')
            ->take(10)
            ->get();
        
        // My attendance this month
        $thisMonthAttendance = Attendance::where('user_id', $user->id)
            ->whereYear('timestamp', $today->year)
            ->whereMonth('timestamp', $today->month)
            ->count();
        
        // My attendance summary
        $attendanceSummary = $this->getMyAttendanceSummary($user->id);
        
        return view('pegawai.dashboard', compact(
            'myAttendanceToday',
            'myLeaves',
            'myRecentAttendance',
            'thisMonthAttendance',
            'attendanceSummary'
        ));
    }
    
    private function wakaKurikulumDashboard()
    {
        $today = Carbon::today();
        
        // All teachers attendance today
        $teachersAttendanceToday = Attendance::whereHas('user', function($query) {
            $query->where('role_id', 2); // Guru
        })->whereDate('timestamp', $today)->count();
        
        $totalTeachers = User::where('role_id', 2)->count();
        
        // All students attendance today
        $studentsAttendanceToday = StudentAttendance::whereDate('created_at', $today)->count();
        $totalStudents = Student::count();
        
        // Class attendance summary
        $classAttendanceSummary = $this->getClassAttendanceSummary();
        
        // Recent teacher activities
        $recentTeacherAttendance = Attendance::with('user:id,name')
            ->whereHas('user', function($query) {
                $query->where('role_id', 2);
            })
            ->whereDate('timestamp', $today)
            ->orderBy('timestamp', 'desc')
            ->take(10)
            ->get();
        
        // Weekly data for teachers and students
        $weeklyData = $this->getWeeklyAttendanceData();
        
        return view('waka-kurikulum.dashboard', compact(
            'teachersAttendanceToday',
            'totalTeachers',
            'studentsAttendanceToday',
            'totalStudents',
            'classAttendanceSummary',
            'recentTeacherAttendance',
            'weeklyData'
        ));
    }
    
    private function kepalaSekolahDashboard()
    {
        $today = Carbon::today();
        
        // Overall school statistics
        $todayEmployeeAttendance = Attendance::whereDate('timestamp', $today)->count();
        $totalEmployees = User::whereIn('role_id', [2, 3, 5, 6])->count(); // All staff
        
        $todayStudentAttendance = StudentAttendance::whereDate('created_at', $today)->count();
        $totalStudents = Student::count();
        
        // Leave requests pending approval
        $pendingLeaves = Leave::where('status', 'menunggu')->count();
        
        // Department-wise attendance
        $departmentStats = $this->getDepartmentStats();
        
        // Recent activities
        $recentAttendance = Attendance::with('user:id,name')
            ->whereDate('timestamp', $today)
            ->orderBy('timestamp', 'desc')
            ->take(10)
            ->get();
        
        $recentLeaves = Leave::with('user:id,name')
            ->where('status', 'menunggu')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        
        // Monthly trends
        $monthlyTrends = $this->getMonthlyTrends();
        
        // Weekly overview
        $weeklyData = $this->getWeeklyAttendanceData();
        
        return view('kepala-sekolah.dashboard', compact(
            'todayEmployeeAttendance',
            'totalEmployees',
            'todayStudentAttendance',
            'totalStudents',
            'pendingLeaves',
            'departmentStats',
            'recentAttendance',
            'recentLeaves',
            'monthlyTrends',
            'weeklyData'
        ));
    }
    
    private function getWeeklyAttendanceData()
    {
        $data = [];
        $startOfWeek = Carbon::now()->startOfWeek();
        
        for ($i = 0; $i < 7; $i++) {
            $date = $startOfWeek->copy()->addDays($i);
            $employeeCount = Attendance::whereDate('timestamp', $date)
                ->distinct('user_id')
                ->count('user_id');
            $studentCount = StudentAttendance::whereDate('created_at', $date)->count();
            
            $data[] = [
                'date' => $date->format('Y-m-d'),
                'day' => $date->format('D'),
                'employees' => $employeeCount,
                'students' => $studentCount,
            ];
        }
        
        return $data;
    }
    
    private function getMonthlyStats()
    {
        $currentMonth = Carbon::now();
        $lastMonth = Carbon::now()->subMonth();
        
        // Current month stats
        $currentMonthAttendance = Attendance::whereYear('timestamp', $currentMonth->year)
            ->whereMonth('timestamp', $currentMonth->month)
            ->distinct('user_id')
            ->count('user_id');
        
        $currentMonthStudents = StudentAttendance::whereYear('created_at', $currentMonth->year)
            ->whereMonth('created_at', $currentMonth->month)
            ->count();
        
        $currentMonthLeaves = Leave::whereYear('start_date', $currentMonth->year)
            ->whereMonth('start_date', $currentMonth->month)
            ->count();
        
        // Last month stats
        $lastMonthAttendance = Attendance::whereYear('timestamp', $lastMonth->year)
            ->whereMonth('timestamp', $lastMonth->month)
            ->distinct('user_id')
            ->count('user_id');
        
        $lastMonthStudents = StudentAttendance::whereYear('created_at', $lastMonth->year)
            ->whereMonth('created_at', $lastMonth->month)
            ->count();
        
        $lastMonthLeaves = Leave::whereYear('start_date', $lastMonth->year)
            ->whereMonth('start_date', $lastMonth->month)
            ->count();
        
        return [
            'attendance' => [
                'current' => $currentMonthAttendance,
                'last' => $lastMonthAttendance,
                'change' => $lastMonthAttendance > 0 
                    ? (($currentMonthAttendance - $lastMonthAttendance) / $lastMonthAttendance) * 100 
                    : 0
            ],
            'students' => [
                'current' => $currentMonthStudents,
                'last' => $lastMonthStudents,
                'change' => $lastMonthStudents > 0 
                    ? (($currentMonthStudents - $lastMonthStudents) / $lastMonthStudents) * 100 
                    : 0
            ],
            'leaves' => [
                'current' => $currentMonthLeaves,
                'last' => $lastMonthLeaves,
                'change' => $lastMonthLeaves > 0 
                    ? (($currentMonthLeaves - $lastMonthLeaves) / $lastMonthLeaves) * 100 
                    : 0
            ]
        ];
    }
    
    private function getWeeklyStudentAttendanceData($studentIds)
    {
        $data = [];
        $startOfWeek = Carbon::now()->startOfWeek();
        
        for ($i = 0; $i < 7; $i++) {
            $date = $startOfWeek->copy()->addDays($i);
            $studentCount = StudentAttendance::whereIn('student_id', $studentIds)
                ->whereDate('created_at', $date)
                ->count();
            
            $data[] = [
                'date' => $date->format('Y-m-d'),
                'day' => $date->format('D'),
                'students' => $studentCount,
            ];
        }
        
        return $data;
    }
    
    private function getMyAttendanceSummary($userId)
    {
        $today = Carbon::today();
        $thisMonth = Carbon::now();
        
        $thisMonthAttendance = Attendance::where('user_id', $userId)
            ->whereYear('timestamp', $thisMonth->year)
            ->whereMonth('timestamp', $thisMonth->month)
            ->count();
        
        $thisMonthLate = Attendance::where('user_id', $userId)
            ->whereYear('timestamp', $thisMonth->year)
            ->whereMonth('timestamp', $thisMonth->month)
            ->where('status', 'terlambat')
            ->count();
        
        $thisMonthOnTime = $thisMonthAttendance - $thisMonthLate;
        
        return [
            'total' => $thisMonthAttendance,
            'on_time' => $thisMonthOnTime,
            'late' => $thisMonthLate,
            'percentage' => $thisMonthAttendance > 0 ? ($thisMonthOnTime / $thisMonthAttendance) * 100 : 0
        ];
    }
    
    private function getClassAttendanceSummary()
    {
        $today = Carbon::today();
        
        $classes = \App\Models\ClassRoom::with('students')->get();
        $summary = [];
        
        foreach ($classes as $class) {
            $totalStudents = $class->students->count();
            $presentToday = StudentAttendance::whereIn('student_id', $class->students->pluck('id'))
                ->whereDate('created_at', $today)
                ->count();
            
            $summary[] = [
                'class_name' => $class->name,
                'total_students' => $totalStudents,
                'present_today' => $presentToday,
                'percentage' => $totalStudents > 0 ? ($presentToday / $totalStudents) * 100 : 0
            ];
        }
        
        return $summary;
    }
    
    private function getDepartmentStats()
    {
        $today = Carbon::today();
        
        $departments = [
            'Guru' => 2,
            'Pegawai' => 3,
            'Waka Kurikulum' => 5,
            'Kepala Sekolah' => 6
        ];
        
        $stats = [];
        foreach ($departments as $name => $roleId) {
            $total = User::where('role_id', $roleId)->count();
            $present = Attendance::whereHas('user', function($query) use ($roleId) {
                $query->where('role_id', $roleId);
            })->whereDate('timestamp', $today)->distinct('user_id')->count();
            
            $stats[] = [
                'department' => $name,
                'total' => $total,
                'present' => $present,
                'percentage' => $total > 0 ? ($present / $total) * 100 : 0
            ];
        }
        
        return $stats;
    }
    
    private function getMonthlyTrends()
    {
        $currentMonth = Carbon::now();
        $lastMonth = Carbon::now()->subMonth();
        
        // Employee attendance trends
        $currentMonthEmployees = Attendance::whereYear('timestamp', $currentMonth->year)
            ->whereMonth('timestamp', $currentMonth->month)
            ->distinct('user_id')
            ->count('user_id');
        
        $lastMonthEmployees = Attendance::whereYear('timestamp', $lastMonth->year)
            ->whereMonth('timestamp', $lastMonth->month)
            ->distinct('user_id')
            ->count('user_id');
        
        // Student attendance trends
        $currentMonthStudents = StudentAttendance::whereYear('created_at', $currentMonth->year)
            ->whereMonth('created_at', $currentMonth->month)
            ->count();
        
        $lastMonthStudents = StudentAttendance::whereYear('created_at', $lastMonth->year)
            ->whereMonth('created_at', $lastMonth->month)
            ->count();
        
        return [
            'employees' => [
                'current' => $currentMonthEmployees,
                'last' => $lastMonthEmployees,
                'change' => $lastMonthEmployees > 0 
                    ? (($currentMonthEmployees - $lastMonthEmployees) / $lastMonthEmployees) * 100 
                    : 0
            ],
            'students' => [
                'current' => $currentMonthStudents,
                'last' => $lastMonthStudents,
                'change' => $lastMonthStudents > 0 
                    ? (($currentMonthStudents - $lastMonthStudents) / $lastMonthStudents) * 100 
                    : 0
            ]
        ];
    }
}