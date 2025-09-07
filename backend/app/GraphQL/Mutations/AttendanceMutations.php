<?php

namespace App\GraphQL\Mutations;

use App\Models\Attendance;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class AttendanceMutations
{
    public function checkin($_, array $args)
    {
        $user = auth()->user();
        $input = $args['input'];
        Validator::make($input, [
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'selfie_base64' => 'required|string',
            'qr_code' => 'required|string',
        ])->validate();

        // TODO: validasi geofence & QR signature
        return Attendance::create([
            'user_id' => $user->id,
            'type' => 'checkin',
            'timestamp' => Carbon::now(),
            'latitude' => $input['latitude'],
            'longitude' => $input['longitude'],
            'accuracy' => $input['accuracy'] ?? null,
            'status' => 'hadir',
        ]);
    }

    public function checkout($_, array $args)
    {
        $user = auth()->user();
        $input = $args['input'];
        Validator::make($input, [
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'qr_code' => 'required|string',
        ])->validate();

        return Attendance::create([
            'user_id' => $user->id,
            'type' => 'checkout',
            'timestamp' => Carbon::now(),
            'latitude' => $input['latitude'],
            'longitude' => $input['longitude'],
            'accuracy' => $input['accuracy'] ?? null,
            'status' => 'hadir',
        ]);
    }
}
