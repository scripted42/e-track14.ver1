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
}