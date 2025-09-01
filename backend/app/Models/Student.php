<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'class_name',
        'card_qr_code',
    ];

    // Relationships
    public function attendance()
    {
        return $this->hasMany(StudentAttendance::class);
    }

    // Scopes
    public function scopeByClass($query, $className)
    {
        return $query->where('class_name', $className);
    }

    public function scopeByQrCode($query, $qrCode)
    {
        return $query->where('card_qr_code', $qrCode);
    }

    // Helper methods
    public function getTodayAttendance()
    {
        return $this->attendance()
                    ->whereDate('created_at', today())
                    ->first();
    }

    public function getAttendanceByDate($date)
    {
        return $this->attendance()
                    ->whereDate('created_at', $date)
                    ->first();
    }

    public function getMonthlyAttendance($year = null, $month = null)
    {
        $year = $year ?? now()->year;
        $month = $month ?? now()->month;

        return $this->attendance()
                    ->whereYear('created_at', $year)
                    ->whereMonth('created_at', $month)
                    ->get();
    }
}