<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'latitude',
        'longitude',
        'radius',
        'checkin_start',
        'checkin_end',
        'checkout_start',
        'checkout_end',
    ];

    protected $casts = [
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
    ];

    // Static methods
    public static function getSettings()
    {
        return self::first() ?? self::create([
            'latitude' => -7.250445,
            'longitude' => 112.768845,
            'radius' => 100,
            'checkin_start' => '07:00:00',
            'checkin_end' => '08:00:00',
            'checkout_start' => '15:00:00',
            'checkout_end' => '17:00:00',
        ]);
    }

    // Helper methods
    public function isWithinRadius($userLat, $userLng)
    {
        $distance = $this->calculateDistance(
            $this->latitude,
            $this->longitude,
            $userLat,
            $userLng
        );

        return $distance <= $this->radius;
    }

    public function isCheckinTime()
    {
        $now = now()->format('H:i:s');
        return $now >= $this->checkin_start && $now <= $this->checkin_end;
    }

    public function isCheckoutTime()
    {
        $now = now()->format('H:i:s');
        return $now >= $this->checkout_start && $now <= $this->checkout_end;
    }

    private function calculateDistance($lat1, $lng1, $lat2, $lng2)
    {
        $earthRadius = 6371000; // meters

        $lat1Rad = deg2rad($lat1);
        $lng1Rad = deg2rad($lng1);
        $lat2Rad = deg2rad($lat2);
        $lng2Rad = deg2rad($lng2);

        $deltaLat = $lat2Rad - $lat1Rad;
        $deltaLng = $lng2Rad - $lng1Rad;

        $a = sin($deltaLat / 2) * sin($deltaLat / 2) +
             cos($lat1Rad) * cos($lat2Rad) *
             sin($deltaLng / 2) * sin($deltaLng / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}