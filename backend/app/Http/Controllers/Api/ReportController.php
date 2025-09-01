<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\StudentAttendance;
use App\Models\Leave;
use App\Models\User;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function attendanceReport(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'year' => 'nullable|integer|min:2020|max:' . (date('Y') + 1),
            'month' => 'nullable|integer|min:1|max:12',
            'user_id' => 'nullable|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $year = $request->year ?? now()->year;
        $month = $request->month ?? now()->month;

        $query = Attendance::with(['user:id,name,email'])
            ->whereYear('timestamp', $year)
            ->whereMonth('timestamp', $month);

        if ($request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        // Get attendance summary by status
        $summary = $query->select(
                'user_id',
                'status',
                DB::raw('COUNT(*) as count'),
                DB::raw('DATE(timestamp) as date')
            )
            ->groupBy('user_id', 'status', 'date')
            ->get();

        // Get detailed attendance records
        $attendance = $query->orderBy('timestamp', 'desc')
            ->paginate(100);

        // Calculate statistics
        $stats = [
            'total_days' => $attendance->total(),
            'hadir' => $summary->where('status', 'hadir')->sum('count'),
            'terlambat' => $summary->where('status', 'terlambat')->sum('count'),
            'izin' => $summary->where('status', 'izin')->sum('count'),
            'sakit' => $summary->where('status', 'sakit')->sum('count'),
            'cuti' => $summary->where('status', 'cuti')->sum('count'),
            'dinas_luar' => $summary->where('status', 'dinas_luar')->sum('count'),
            'alpha' => $summary->where('status', 'alpha')->sum('count'),
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'period' => ['year' => $year, 'month' => $month],
                'statistics' => $stats,
                'attendance' => $attendance,
                'summary' => $summary->groupBy('user_id')
            ]
        ]);
    }

    public function studentAttendanceReport(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'year' => 'nullable|integer|min:2020|max:' . (date('Y') + 1),
            'month' => 'nullable|integer|min:1|max:12',
            'class_name' => 'nullable|string',
            'student_id' => 'nullable|exists:students,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $year = $request->year ?? now()->year;
        $month = $request->month ?? now()->month;

        $query = StudentAttendance::with(['student:id,name,class_name', 'teacher:id,name'])
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month);

        if ($request->class_name) {
            $query->byClass($request->class_name);
        }

        if ($request->student_id) {
            $query->where('student_id', $request->student_id);
        }

        // Get attendance summary by status
        $summary = $query->select(
                'student_id',
                'status',
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('student_id', 'status')
            ->get();

        // Get detailed attendance records
        $attendance = $query->orderBy('created_at', 'desc')
            ->paginate(100);

        // Calculate statistics
        $stats = [
            'total_records' => $attendance->total(),
            'hadir' => $summary->where('status', 'hadir')->sum('count'),
            'izin' => $summary->where('status', 'izin')->sum('count'),
            'sakit' => $summary->where('status', 'sakit')->sum('count'),
            'alpha' => $summary->where('status', 'alpha')->sum('count'),
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'period' => ['year' => $year, 'month' => $month],
                'statistics' => $stats,
                'attendance' => $attendance,
                'summary' => $summary->groupBy('student_id')
            ]
        ]);
    }

    public function leaveReport(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'year' => 'nullable|integer|min:2020|max:' . (date('Y') + 1),
            'month' => 'nullable|integer|min:1|max:12',
            'status' => 'nullable|in:menunggu,disetujui,ditolak',
            'leave_type' => 'nullable|in:izin,sakit,cuti,dinas_luar',
            'user_id' => 'nullable|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $year = $request->year ?? now()->year;
        $month = $request->month ?? now()->month;

        $query = Leave::with(['user:id,name,email', 'approver:id,name'])
            ->whereYear('start_date', $year)
            ->whereMonth('start_date', $month);

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->leave_type) {
            $query->where('leave_type', $request->leave_type);
        }

        if ($request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        // Get leave summary
        $summary = $query->select(
                'leave_type',
                'status',
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(DATEDIFF(end_date, start_date) + 1) as total_days')
            )
            ->groupBy('leave_type', 'status')
            ->get();

        // Get detailed leave records
        $leaves = $query->orderBy('created_at', 'desc')
            ->paginate(50);

        // Calculate statistics
        $stats = [
            'total_requests' => $leaves->total(),
            'pending' => $summary->where('status', 'menunggu')->sum('count'),
            'approved' => $summary->where('status', 'disetujui')->sum('count'),
            'rejected' => $summary->where('status', 'ditolak')->sum('count'),
            'total_leave_days' => $summary->where('status', 'disetujui')->sum('total_days'),
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'period' => ['year' => $year, 'month' => $month],
                'statistics' => $stats,
                'leaves' => $leaves,
                'summary' => $summary
            ]
        ]);
    }

    public function summaryReport(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'year' => 'nullable|integer|min:2020|max:' . (date('Y') + 1),
            'month' => 'nullable|integer|min:1|max:12',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $year = $request->year ?? now()->year;
        $month = $request->month ?? now()->month;

        // Employee/Teacher attendance summary
        $employeeAttendance = Attendance::whereYear('timestamp', $year)
            ->whereMonth('timestamp', $month)
            ->select(
                'status',
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('status')
            ->pluck('count', 'status');

        // Student attendance summary
        $studentAttendance = StudentAttendance::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->select(
                'status',
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('status')
            ->pluck('count', 'status');

        // Leave summary
        $leaveSummary = Leave::whereYear('start_date', $year)
            ->whereMonth('start_date', $month)
            ->select(
                'leave_type',
                'status',
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('leave_type', 'status')
            ->get();

        // Active users count
        $activeUsers = User::whereIn('role_id', [2, 3]) // Guru and Pegawai
            ->count();

        // Total students count
        $totalStudents = Student::count();

        // Classes count
        $totalClasses = Student::distinct('class_name')->count();

        return response()->json([
            'success' => true,
            'data' => [
                'period' => ['year' => $year, 'month' => $month],
                'overview' => [
                    'active_employees' => $activeUsers,
                    'total_students' => $totalStudents,
                    'total_classes' => $totalClasses,
                ],
                'employee_attendance' => $employeeAttendance,
                'student_attendance' => $studentAttendance,
                'leave_summary' => $leaveSummary,
            ]
        ]);
    }
}