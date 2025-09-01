<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AttendanceQr;
use App\Models\AuditLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class QrController extends Controller
{
    public function getTodayQr()
    {
        // Always generate a new QR code for every request to ensure fresh QR every 10 seconds
        // This approach ensures dynamic generation as requested by user
        $qr = $this->generateShortTermQr();

        return response()->json([
            'success' => true,
            'data' => [
                'qr_code' => $qr->qr_code,
                'valid_until' => $qr->valid_until,
                'is_valid' => $qr->isValid(),
                'generated_at' => $qr->created_at->format('Y-m-d H:i:s'),
            ]
        ]);
    }
    
    private function generateShortTermQr()
    {
        // Generate QR code with microsecond timestamp for true uniqueness every call
        $timestamp = now()->format('YmdHis') . now()->micro;
        $qrCode = 'QR_' . date('Y-m-d') . '_' . $timestamp . '_' . bin2hex(random_bytes(4));
        $validUntil = now()->addSeconds(15); // Valid for 15 seconds

        return AttendanceQr::create([
            'qr_code' => $qrCode,
            'valid_until' => $validUntil,
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