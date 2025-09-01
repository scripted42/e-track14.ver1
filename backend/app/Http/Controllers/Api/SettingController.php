<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::getSettings();

        return response()->json([
            'success' => true,
            'data' => $settings
        ]);
    }

    public function getCurrent()
    {
        $settings = Setting::getSettings();

        return response()->json([
            'success' => true,
            'data' => [
                'latitude' => $settings->latitude,
                'longitude' => $settings->longitude,
                'radius' => $settings->radius,
                'checkin_start' => $settings->checkin_start,
                'checkin_end' => $settings->checkin_end,
                'checkout_start' => $settings->checkout_start,
                'checkout_end' => $settings->checkout_end,
            ]
        ]);
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'radius' => 'required|integer|min:10|max:1000',
            'checkin_start' => 'required|date_format:H:i:s',
            'checkin_end' => 'required|date_format:H:i:s|after:checkin_start',
            'checkout_start' => 'required|date_format:H:i:s',
            'checkout_end' => 'required|date_format:H:i:s|after:checkout_start',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $settings = Setting::getSettings();
        $oldSettings = $settings->toArray();

        $settings->update([
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'radius' => $request->radius,
            'checkin_start' => $request->checkin_start,
            'checkin_end' => $request->checkin_end,
            'checkout_start' => $request->checkout_start,
            'checkout_end' => $request->checkout_end,
        ]);

        // Log activity
        AuditLog::log('settings_updated', [
            'old_settings' => $oldSettings,
            'new_settings' => $settings->fresh()->toArray()
        ], $user->id);

        return response()->json([
            'success' => true,
            'message' => 'Settings updated successfully',
            'data' => $settings
        ]);
    }
}