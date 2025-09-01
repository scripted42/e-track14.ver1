<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class AttendanceQr extends Model
{
    use HasFactory;

    protected $table = 'attendance_qr';

    protected $fillable = [
        'qr_code',
        'valid_until',
    ];

    protected $casts = [
        'valid_until' => 'datetime',
    ];

    // Relationships
    public function attendance()
    {
        return $this->hasMany(Attendance::class, 'qr_token_id');
    }

    // Scopes
    public function scopeValid($query)
    {
        return $query->where('valid_until', '>', now());
    }

    public function scopeExpired($query)
    {
        return $query->where('valid_until', '<=', now());
    }

    // Helper methods
    public function isValid()
    {
        return $this->valid_until > now();
    }

    public function isExpired()
    {
        return !$this->isValid();
    }

    // Static methods
    public static function generateDailyQr()
    {
        $qrCode = 'QR_' . date('Y-m-d') . '_' . bin2hex(random_bytes(8));
        $validUntil = Carbon::today()->addDay()->startOfDay();

        return self::create([
            'qr_code' => $qrCode,
            'valid_until' => $validUntil,
        ]);
    }

    public static function getTodayQr()
    {
        return self::valid()
            ->whereDate('created_at', today())
            ->first();
    }
}