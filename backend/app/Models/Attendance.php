<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $table = 'attendance';

    protected $fillable = [
        'user_id',
        'type',
        'latitude',
        'longitude',
        'accuracy',
        'photo_path',
        'qr_token_id',
        'status',
        'timestamp',
        'synced',
    ];

    protected $casts = [
        'timestamp' => 'datetime',
        'date_only' => 'date',
        'synced' => 'boolean',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'accuracy' => 'decimal:2',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function qrToken()
    {
        return $this->belongsTo(AttendanceQr::class, 'qr_token_id');
    }

    // Scopes
    public function scopeToday($query)
    {
        return $query->whereDate('timestamp', today());
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    // Helper methods
    public function isCheckIn()
    {
        return $this->type === 'checkin';
    }

    public function isCheckOut()
    {
        return $this->type === 'checkout';
    }
}