<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AttendanceQr;
use App\Models\AuditLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class QrController extends Controller
{
    public function getTodayQr()
    {
        // Reuse the same QR within a 10s window; validity lasts 15s total
        // Use MySQL advisory lock to prevent concurrent double-generation
        $lockName = 'qr_generation_lock';
        $lockAcquired = false;
        try {
            $lock = DB::select('SELECT GET_LOCK(?, ? ) as l', [$lockName, 2]);
            $lockAcquired = isset($lock[0]) && (int)($lock[0]->l) === 1;

            // Always re-check to avoid race conditions
            $existing = AttendanceQr::where('valid_until', '>', now())
                ->orderByDesc('created_at')
                ->first();

            if ($existing && $existing->created_at->gt(now()->subSeconds(10))) {
                $qr = $existing;
            } else if ($lockAcquired) {
                // Only generate if we hold the lock
                $qr = $this->generateShortTermQr();
            } else {
                // Could not acquire lock: brief wait then re-check, avoid generating
                usleep(200000); // 200ms
                $existing = AttendanceQr::where('valid_until', '>', now())
                    ->orderByDesc('created_at')
                    ->first();
                $qr = $existing;
            }
        } finally {
            if ($lockAcquired) {
                DB::select('SELECT RELEASE_LOCK(?)', [$lockName]);
            }
        }

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
        // Generate a unique QR code; valid for 15 seconds (10s + 5s spare)
        $timestamp = now()->format('YmdHis');
        $qrCode = 'QR_' . date('Y-m-d') . '_' . $timestamp . '_' . bin2hex(random_bytes(4));
        $validUntil = now()->addSeconds(15);

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