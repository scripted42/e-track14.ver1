<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\StudentAttendance;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StudentAttendanceController extends Controller
{
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'class_name' => 'nullable|string',
            'date' => 'nullable|date',
            'status' => 'nullable|in:hadir,izin,sakit,alpha',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $query = StudentAttendance::with(['student:id,name,class_name', 'teacher:id,name']);

        // Filter by date (default to today)
        $date = $request->date ?? today();
        $query->whereDate('created_at', $date);

        if ($request->class_name) {
            $query->byClass($request->class_name);
        }

        if ($request->status) {
            $query->byStatus($request->status);
        }

        $attendance = $query->orderBy('created_at', 'desc')
            ->paginate(50);

        return response()->json([
            'success' => true,
            'data' => $attendance
        ]);
    }

    public function scanStudent(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'qr_code' => 'required|string',
            'status' => 'nullable|in:hadir,izin,sakit,alpha',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Find student by QR code
        $student = Student::byQrCode($request->qr_code)->first();

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Student not found with this QR code'
            ], 404);
        }

        // Check if student already has attendance today
        $existingAttendance = $student->getTodayAttendance();

        if ($existingAttendance) {
            return response()->json([
                'success' => false,
                'message' => 'Student has already been marked for attendance today',
                'data' => [
                    'student' => $student,
                    'attendance' => $existingAttendance
                ]
            ], 422);
        }

        // Create attendance record
        $attendance = StudentAttendance::create([
            'student_id' => $student->id,
            'teacher_id' => $user->id,
            'status' => $request->status ?? 'hadir',
        ]);

        // Log activity
        AuditLog::log('student_attendance_recorded', [
            'student_id' => $student->id,
            'student_name' => $student->name,
            'class_name' => $student->class_name,
            'status' => $attendance->status,
            'attendance_id' => $attendance->id
        ], $user->id);

        return response()->json([
            'success' => true,
            'message' => 'Student attendance recorded successfully',
            'data' => [
                'student' => $student,
                'attendance' => $attendance,
                'teacher' => $user->only(['id', 'name'])
            ]
        ]);
    }

    public function getStudents(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'class_name' => 'nullable|string',
            'search' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $query = Student::query();

        if ($request->class_name) {
            $query->byClass($request->class_name);
        }

        if ($request->search) {
            $query->where('name', 'LIKE', '%' . $request->search . '%');
        }

        $students = $query->orderBy('class_name')
            ->orderBy('name')
            ->paginate(50);

        return response()->json([
            'success' => true,
            'data' => $students
        ]);
    }

    public function getClasses()
    {
        $classes = Student::select('class_name')
            ->distinct()
            ->orderBy('class_name')
            ->pluck('class_name');

        return response()->json([
            'success' => true,
            'data' => $classes
        ]);
    }
}