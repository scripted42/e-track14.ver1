<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AttendanceQr;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class QrController extends Controller
{
    public function getTodayQr()
    {
        $qr = AttendanceQr::getTodayQr();

        if (!$qr) {
            // Generate new QR if none exists for today
            $qr = AttendanceQr::generateDailyQr();
        }

        return response()->json([
            'success' => true,
            'data' => [
                'qr_code' => $qr->qr_code,
                'valid_until' => $qr->valid_until,
                'is_valid' => $qr->isValid(),
            ]
        ]);
    }

    public function validateQr(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'qr_code' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $qr = AttendanceQr::where('qr_code', $request->qr_code)->first();

        if (!$qr) {
            return response()->json([
                'success' => false,
                'message' => 'QR code not found'
            ], 404);
        }

        $isValid = $qr->isValid();

        return response()->json([
            'success' => true,
            'data' => [
                'is_valid' => $isValid,
                'qr_code' => $qr->qr_code,
                'valid_until' => $qr->valid_until,
                'message' => $isValid ? 'QR code is valid' : 'QR code has expired'
            ]
        ]);
    }

    public function generateQr(Request $request)
    {
        $user = $request->user();

        // Generate new QR code
        $qr = AttendanceQr::generateDailyQr();

        // Log activity
        AuditLog::log('qr_generated', [
            'qr_id' => $qr->id,
            'qr_code' => $qr->qr_code,
            'valid_until' => $qr->valid_until
        ], $user->id);

        return response()->json([
            'success' => true,
            'message' => 'QR code generated successfully',
            'data' => [
                'qr_code' => $qr->qr_code,
                'valid_until' => $qr->valid_until,
            ]
        ]);
    }
}