<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Setting;
use App\Models\AttendanceQr;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        
        $query = Attendance::with(['user:id,name']);
        
        // If not admin, only show own attendance
        if (!$user->isAdmin()) {
            $query->where('user_id', $user->id);
        }
        
        $attendance = $query->orderBy('timestamp', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $attendance
        ]);
    }

    public function today(Request $request)
    {
        $user = $request->user();
        
        $todayAttendance = Attendance::byUser($user->id)
            ->today()
            ->orderBy('timestamp', 'asc')
            ->get();

        $checkin = $todayAttendance->where('type', 'checkin')->first();
        $checkout = $todayAttendance->where('type', 'checkout')->first();

        return response()->json([
            'success' => true,
            'data' => [
                'checkin' => $checkin,
                'checkout' => $checkout,
                'has_checkin' => !is_null($checkin),
                'has_checkout' => !is_null($checkout),
            ]
        ]);
    }

    public function checkin(Request $request)
    {
        $user = $request->user();
        
        // Check if already checked in today
        $existingCheckin = Attendance::byUser($user->id)
            ->today()
            ->where('type', 'checkin')
            ->first();

        if ($existingCheckin) {
            return response()->json([
                'success' => false,
                'message' => 'You have already checked in today'
            ], 422);
        }

        $validator = Validator::make($request->all(), [
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'accuracy' => 'required|numeric',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'qr_code' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $settings = Setting::getSettings();

        // Validate location
        if (!$settings->isWithinRadius($request->latitude, $request->longitude)) {
            return response()->json([
                'success' => false,
                'message' => 'You are outside the allowed attendance area'
            ], 422);
        }

        // Validate QR code
        $qrToken = AttendanceQr::where('qr_code', $request->qr_code)
            ->valid()
            ->first();

        if (!$qrToken) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired QR code'
            ], 422);
        }

        // Store photo
        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
            $filename = 'attendance_' . $user->id . '_' . time() . '.' . $photo->getClientOriginalExtension();
            $photoPath = $photo->storeAs('attendance/photos', $filename, 'public');
        }

        // Determine status based on time
        $status = 'hadir';
        if (!$settings->isCheckinTime()) {
            $status = 'terlambat';
        }

        // Create attendance record
        $attendance = Attendance::create([
            'user_id' => $user->id,
            'type' => 'checkin',
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'accuracy' => $request->accuracy,
            'photo_path' => $photoPath,
            'qr_token_id' => $qrToken->id,
            'status' => $status,
            'timestamp' => now(),
            'synced' => false,
        ]);

        // Log activity
        AuditLog::log('checkin', [
            'attendance_id' => $attendance->id,
            'location' => [$request->latitude, $request->longitude],
            'status' => $status
        ], $user->id);

        return response()->json([
            'success' => true,
            'message' => 'Check-in successful',
            'data' => $attendance
        ]);
    }

    public function checkout(Request $request)
    {
        $user = $request->user();
        
        // Check if has checked in today
        $checkin = Attendance::byUser($user->id)
            ->today()
            ->where('type', 'checkin')
            ->first();

        if (!$checkin) {
            return response()->json([
                'success' => false,
                'message' => 'You must check-in first before checking out'
            ], 422);
        }

        // Check if already checked out today
        $existingCheckout = Attendance::byUser($user->id)
            ->today()
            ->where('type', 'checkout')
            ->first();

        if ($existingCheckout) {
            return response()->json([
                'success' => false,
                'message' => 'You have already checked out today'
            ], 422);
        }

        $validator = Validator::make($request->all(), [
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'accuracy' => 'required|numeric',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $settings = Setting::getSettings();

        // Validate location
        if (!$settings->isWithinRadius($request->latitude, $request->longitude)) {
            return response()->json([
                'success' => false,
                'message' => 'You are outside the allowed attendance area'
            ], 422);
        }

        // Store photo
        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
            $filename = 'checkout_' . $user->id . '_' . time() . '.' . $photo->getClientOriginalExtension();
            $photoPath = $photo->storeAs('attendance/photos', $filename, 'public');
        }

        // Create attendance record
        $attendance = Attendance::create([
            'user_id' => $user->id,
            'type' => 'checkout',
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'accuracy' => $request->accuracy,
            'photo_path' => $photoPath,
            'qr_token_id' => null,
            'status' => 'hadir',
            'timestamp' => now(),
            'synced' => false,
        ]);

        // Log activity
        AuditLog::log('checkout', [
            'attendance_id' => $attendance->id,
            'location' => [$request->latitude, $request->longitude]
        ], $user->id);

        return response()->json([
            'success' => true,
            'message' => 'Check-out successful',
            'data' => $attendance
        ]);
    }

    public function history(Request $request)
    {
        $user = $request->user();
        
        $validator = Validator::make($request->all(), [
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'nullable|in:hadir,terlambat,izin,sakit,alpha,cuti,dinas_luar',
            'type' => 'nullable|in:checkin,checkout',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $query = Attendance::byUser($user->id);

        if ($request->start_date) {
            $query->whereDate('timestamp', '>=', $request->start_date);
        }

        if ($request->end_date) {
            $query->whereDate('timestamp', '<=', $request->end_date);
        }

        if ($request->status) {
            $query->byStatus($request->status);
        }

        if ($request->type) {
            $query->byType($request->type);
        }

        $attendance = $query->orderBy('timestamp', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $attendance
        ]);
    }
}