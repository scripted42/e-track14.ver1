<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\AdminController;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\AttendanceController;
use App\Http\Controllers\Web\LeaveController;
use App\Http\Controllers\Web\StudentController;
use App\Http\Controllers\Web\UserController;
use App\Http\Controllers\Web\ClassRoomController;
use App\Http\Controllers\Web\SettingController;
use App\Http\Controllers\Web\ReportController;

Route::get('/', function () {
    return redirect('/admin');
});

// Default login route (Laravel expects this)
Route::get('/login', function () {
    return redirect('/admin/login');
})->name('login');

// Admin routes
Route::prefix('admin')->group(function () {
    // Guest routes
    Route::middleware('guest')->group(function () {
        Route::get('/login', [AuthController::class, 'showLoginForm'])->name('admin.login');
        Route::post('/login', [AuthController::class, 'login']);
    });

    // Public QR routes for monitor displays (no auth required)
    Route::prefix('attendance')->name('admin.attendance.')->group(function () {
        Route::get('/qr/display', [AttendanceController::class, 'qrDisplay'])->name('qr.display');
        Route::get('/qr/image/{code}', [AttendanceController::class, 'qrImage'])->name('qr.image');
    });
    


    // Authenticated routes
    Route::middleware('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout'])->name('admin.logout');
        
        // Dashboard - accessible by all authenticated users
        Route::get('/', [DashboardController::class, 'index'])->name('admin.dashboard');
        Route::get('/dashboard', [DashboardController::class, 'index']);
        
        // User Management - Admin only
        Route::middleware('role:Admin')->group(function () {
            // User Import Routes (must be before resource routes)
            Route::get('/users/import', [UserController::class, 'import'])->name('admin.users.import');
            Route::get('/users/import/template', [UserController::class, 'downloadTemplate'])->name('admin.users.import.template');
            Route::post('/users/import/preview', [UserController::class, 'previewImport'])->name('admin.users.import.preview');
            Route::post('/users/import/process', [UserController::class, 'processImport'])->name('admin.users.import.process');
            
            // User Management
            Route::resource('users', UserController::class)->names([
                'index' => 'admin.users.index',
                'create' => 'admin.users.create',
                'store' => 'admin.users.store',
                'show' => 'admin.users.show',
                'edit' => 'admin.users.edit',
                'update' => 'admin.users.update',
                'destroy' => 'admin.users.destroy',
            ]);
            
            // Class Room Management
            Route::resource('classrooms', ClassRoomController::class)->names([
                'index' => 'admin.classrooms.index',
                'create' => 'admin.classrooms.create',
                'store' => 'admin.classrooms.store',
                'show' => 'admin.classrooms.show',
                'edit' => 'admin.classrooms.edit',
                'update' => 'admin.classrooms.update',
                'destroy' => 'admin.classrooms.destroy',
            ]);
            
            // Walikelas Management Routes
            Route::post('/classrooms/{classroom}/assign-walikelas', [ClassRoomController::class, 'assignWalikelas'])->name('admin.classrooms.assign-walikelas');
            Route::delete('/classrooms/{classroom}/remove-walikelas', [ClassRoomController::class, 'removeWalikelas'])->name('admin.classrooms.remove-walikelas');
            Route::post('/classrooms/transfer-walikelas', [ClassRoomController::class, 'transferWalikelas'])->name('admin.classrooms.transfer-walikelas');
            Route::get('/classrooms/available-teachers', [ClassRoomController::class, 'getAvailableTeachers'])->name('admin.classrooms.available-teachers');
        });
        
        // Attendance Management - accessible by all authenticated users
        Route::prefix('attendance')->name('admin.attendance.')->group(function () {
            Route::get('/', [AttendanceController::class, 'index'])->name('index');
            Route::get('/qr', [AttendanceController::class, 'qr'])->name('qr');
            Route::post('/qr/generate', [AttendanceController::class, 'generateQr'])->name('qr.generate');
            Route::get('/export', [AttendanceController::class, 'export'])->name('export');
            Route::get('/detail/{userId}/{date}', [AttendanceController::class, 'detail'])->name('detail');
            Route::post('/leave/{leaveId}/approve', [AttendanceController::class, 'approveLeave'])->name('leave.approve')->middleware('role:Admin,Kepala Sekolah,Waka Kurikulum');
            Route::post('/leave/{leaveId}/reject', [AttendanceController::class, 'rejectLeave'])->name('leave.reject')->middleware('role:Admin,Kepala Sekolah,Waka Kurikulum');
            Route::get('/monitor', [AttendanceController::class, 'monitor'])->name('monitor');
        });
        
        // Student Attendance Management - accessible by Admin, Guru, Kepala Sekolah, Waka Kurikulum
        Route::prefix('student-attendance')->name('admin.student-attendance.')->middleware('role:Admin,Guru,Kepala Sekolah,Waka Kurikulum')->group(function () {
            Route::get('/', [\App\Http\Controllers\Web\StudentAttendanceController::class, 'index'])->name('index');
            Route::get('/export', [\App\Http\Controllers\Web\StudentAttendanceController::class, 'export'])->name('export');
            Route::get('/detail/{studentId}/{date}', [\App\Http\Controllers\Web\StudentAttendanceController::class, 'detail'])->name('detail');
        });
        
        // Leave Management - accessible by all authenticated users
        Route::prefix('leaves')->name('admin.leaves.')->group(function () {
            Route::get('/', [LeaveController::class, 'index'])->name('index');
            Route::post('/', [LeaveController::class, 'store'])->name('store');
            Route::get('/{leave}', [LeaveController::class, 'show'])->name('show');
            Route::get('/evidence/{leave}', [LeaveController::class, 'viewEvidence'])->name('evidence');
            Route::post('/{leave}/approve', [LeaveController::class, 'approve'])->name('approve')->middleware('role:Admin,Kepala Sekolah,Waka Kurikulum');
            Route::post('/{leave}/reject', [LeaveController::class, 'reject'])->name('reject')->middleware('role:Admin,Kepala Sekolah,Waka Kurikulum');
        });
        
        // Student Management - accessible by Admin, Guru, Kepala Sekolah, Waka Kurikulum
        Route::prefix('students')->name('admin.students.')->middleware('role:Admin,Guru,Kepala Sekolah,Waka Kurikulum')->group(function () {
            Route::get('/', [StudentController::class, 'index'])->name('index');
            Route::get('/create', [StudentController::class, 'create'])->name('create');
            Route::post('/', [StudentController::class, 'store'])->name('store');
            Route::get('/import', [StudentController::class, 'showImport'])->name('import');
            Route::get('/import/template', [StudentController::class, 'downloadTemplate'])->name('import.template');
            Route::post('/import/preview', [StudentController::class, 'previewImport'])->name('import.preview');
            Route::post('/import/process', [StudentController::class, 'processImport'])->name('import.process');
            Route::get('/attendance', [StudentController::class, 'attendance'])->name('attendance');
            Route::get('/{student}', [StudentController::class, 'show'])->name('show')->whereNumber('student');
            Route::get('/{student}/edit', [StudentController::class, 'edit'])->name('edit')->whereNumber('student');
            Route::put('/{student}', [StudentController::class, 'update'])->name('update')->whereNumber('student');
            Route::delete('/{student}', [StudentController::class, 'destroy'])->name('destroy')->whereNumber('student');
            Route::get('/{student}/qr-code', [StudentController::class, 'qrCode'])->name('qr-code')->whereNumber('student');
            
        });
        
        // Student Promotion and Graduation - accessible by Admin, Kepala Sekolah, Waka Kurikulum
        Route::get('/students/promotion', [\App\Http\Controllers\Web\StudentPromotionController::class, 'index'])
            ->middleware('role:Admin,Kepala Sekolah,Waka Kurikulum')
            ->name('admin.students.promotion.index');
        
        Route::prefix('students/promotion')->name('admin.students.promotion.')->middleware('role:Admin,Kepala Sekolah,Waka Kurikulum')->group(function () {
            Route::get('/test', function() { return 'Test route works!'; })->name('test');
            Route::post('/promote-class', [\App\Http\Controllers\Web\StudentPromotionController::class, 'promoteClass'])->name('promote-class');
            Route::post('/graduate-class', [\App\Http\Controllers\Web\StudentPromotionController::class, 'graduateClass'])->name('graduate-class');
            Route::post('/batch-promotion', [\App\Http\Controllers\Web\StudentPromotionController::class, 'batchPromotion'])->name('batch-promotion');
            Route::patch('/update-status/{student}', [\App\Http\Controllers\Web\StudentPromotionController::class, 'updateStudentStatus'])->name('update-status');
            Route::get('/students-by-status/{status}', [\App\Http\Controllers\Web\StudentPromotionController::class, 'getStudentsByStatus'])->name('students-by-status');
            Route::get('/export-graduation', [\App\Http\Controllers\Web\StudentPromotionController::class, 'exportGraduationReport'])->name('export-graduation');
        });
        
        // Reports - accessible by Admin, Kepala Sekolah, Waka Kurikulum
        Route::prefix('reports')->name('admin.reports.')->middleware('role:Admin,Kepala Sekolah,Waka Kurikulum')->group(function () {
            Route::get('/', [ReportController::class, 'index'])->name('index');
            Route::get('/attendance', [ReportController::class, 'attendance'])->name('attendance');
            Route::get('/leaves', [ReportController::class, 'leaves'])->name('leaves');
            Route::get('/students', [ReportController::class, 'students'])->name('students');
            Route::get('/export/{type}', [ReportController::class, 'export'])->name('export');
            
            // Export routes
            Route::get('/attendance/export/excel', [ReportController::class, 'attendanceExportExcel'])->name('attendance.export.excel');
            Route::get('/attendance/export/pdf', [ReportController::class, 'attendanceExportPdf'])->name('attendance.export.pdf');
            Route::get('/leaves/export/excel', [ReportController::class, 'leavesExportExcel'])->name('leaves.export.excel');
            Route::get('/leaves/export/pdf', [ReportController::class, 'leavesExportPdf'])->name('leaves.export.pdf');
            Route::get('/students/export/excel', [ReportController::class, 'studentsExportExcel'])->name('students.export.excel');
            Route::get('/students/export/pdf', [ReportController::class, 'studentsExportPdf'])->name('students.export.pdf');
        });
        
        // Additional routes for Kepala Sekolah
        Route::middleware('role:Kepala Sekolah')->group(function () {
            // Strategic reports for Kepala Sekolah
            Route::get('/reports/strategic', [ReportController::class, 'strategic'])->name('admin.reports.strategic');
            
            // Enhanced leave management for Kepala Sekolah
            Route::post('/leaves/{leave}/approve', [LeaveController::class, 'approve'])->name('admin.leaves.approve');
            Route::post('/leaves/{leave}/reject', [LeaveController::class, 'reject'])->name('admin.leaves.reject');
        });
        
        // Additional routes for Guru
        Route::middleware('role:Guru')->group(function () {
            // Class-specific reports for Guru
            Route::get('/reports/class/{classId}', [ReportController::class, 'classReport'])->name('admin.reports.class');
            Route::get('/reports/my-students', [ReportController::class, 'myStudentsReport'])->name('admin.reports.my-students');
            Route::get('/reports/my-students/export/excel', [ReportController::class, 'myStudentsExportExcel'])->name('admin.reports.my-students.excel');
            Route::get('/reports/my-students/export/pdf', [ReportController::class, 'myStudentsExportPdf'])->name('admin.reports.my-students.pdf');
            
            // Enhanced student management for Guru
            Route::get('/students/class/{classId}', [StudentController::class, 'classStudents'])->name('admin.students.class');
            Route::get('/students/my-class', [StudentController::class, 'myClassStudents'])->name('admin.students.my-class');
        });
        
        // Settings - Admin only
        Route::prefix('settings')->name('admin.settings.')->middleware('role:Admin')->group(function () {
            Route::get('/', [SettingController::class, 'index'])->name('index');
            Route::put('/', [SettingController::class, 'update'])->name('update');
        });
    });
});