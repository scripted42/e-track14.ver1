<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\LeaveController;
use App\Http\Controllers\Api\StudentAttendanceController;
use App\Http\Controllers\Api\SettingController;
use App\Http\Controllers\Api\QrController;
use App\Http\Controllers\Api\ReportController;

// Public routes
Route::post('/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Authentication
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::put('/profile', [AuthController::class, 'updateProfile']);
    Route::post('/refresh-token', [AuthController::class, 'refreshToken']);

    // Attendance (Employee/Teacher)
    Route::prefix('attendance')->group(function () {
        Route::get('/', [AttendanceController::class, 'index']);
        Route::post('/checkin', [AttendanceController::class, 'checkin']);
        Route::post('/checkout', [AttendanceController::class, 'checkout']);
        Route::get('/today', [AttendanceController::class, 'today']);
        Route::get('/history', [AttendanceController::class, 'history']);
    });

    // Leave Management
    Route::prefix('leaves')->group(function () {
        Route::get('/', [LeaveController::class, 'index']);
        Route::post('/', [LeaveController::class, 'store']);
        Route::get('/{leave}', [LeaveController::class, 'show']);
        Route::post('/{leave}/approve', [LeaveController::class, 'approve'])
            ->middleware('role:Admin,Waka Kurikulum');
        Route::post('/{leave}/reject', [LeaveController::class, 'reject'])
            ->middleware('role:Admin,Waka Kurikulum');
    });

    // Student Attendance (Teachers only)
    Route::prefix('student-attendance')->middleware('role:Guru,Admin')->group(function () {
        Route::get('/', [StudentAttendanceController::class, 'index']);
        Route::post('/scan', [StudentAttendanceController::class, 'scanStudent']);
        Route::get('/students', [StudentAttendanceController::class, 'getStudents']);
        Route::get('/classes', [StudentAttendanceController::class, 'getClasses']);
    });

    // QR Code
    Route::prefix('qr')->group(function () {
        Route::get('/today', [QrController::class, 'getTodayQr']);
        Route::post('/validate', [QrController::class, 'validateQr']);
        Route::post('/generate', [QrController::class, 'generateQr'])
            ->middleware('role:Admin');
    });

    // Reports (Admin and Waka Kurikulum)
    Route::prefix('reports')->middleware('role:Admin,Waka Kurikulum')->group(function () {
        Route::get('/attendance', [ReportController::class, 'attendanceReport']);
        Route::get('/student-attendance', [ReportController::class, 'studentAttendanceReport']);
        Route::get('/leaves', [ReportController::class, 'leaveReport']);
        Route::get('/summary', [ReportController::class, 'summaryReport']);
    });

    // Settings (Admin only)
    Route::prefix('settings')->middleware('role:Admin')->group(function () {
        Route::get('/', [SettingController::class, 'index']);
        Route::put('/', [SettingController::class, 'update']);
    });

    // Get current settings (all authenticated users)
    Route::get('/settings/current', [SettingController::class, 'getCurrent']);
});