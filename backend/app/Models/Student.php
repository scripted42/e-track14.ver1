<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'nisn',
        'name',
        'photo',
        'class_name',
        'class_room_id',
        'address',
        'card_qr_code',
        'status',
    ];

    // Relationships
    public function attendance()
    {
        return $this->hasMany(StudentAttendance::class);
    }

    public function classRoom()
    {
        return $this->belongsTo(ClassRoom::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'Aktif');
    }

    public function scopeGraduated($query)
    {
        // Jika sistem memakai status 'Lulus', query akan menghitungnya; jika tidak, hasil 0 tanpa error
        return $query->where('status', 'Lulus');
    }

    public function scopeTransferred($query)
    {
        return $query->where('status', 'Pindah');
    }

    public function scopeDropOut($query)
    {
        return $query->where('status', 'Drop Out');
    }

    public function scopeRetained($query)
    {
        return $query->where('status', 'Tidak Naik Kelas');
    }
    public function scopeByClass($query, $className)
    {
        return $query->where('class_name', $className);
    }

    public function scopeByQrCode($query, $qrCode)
    {
        return $query->where('card_qr_code', $qrCode);
    }

    // Helper methods
    public function promoteToClass(string $newClassName, ?int $classRoomId = null): void
    {
        if (Schema::hasColumn($this->getTable(), 'previous_class')) {
            $this->previous_class = $this->class_name;
        }
        $this->class_name = $newClassName;
        if (Schema::hasColumn($this->getTable(), 'class_room_id')) {
            $this->class_room_id = $classRoomId;
        }
        // Pastikan tetap aktif setelah promosi
        $this->status = 'Aktif';
    }

    public function markAsGraduated(?string $graduationDate = null): void
    {
        // Jika kolom graduation_date ada, set nilainya
        if (Schema::hasColumn($this->getTable(), 'graduation_date')) {
            $this->graduation_date = $graduationDate;
        }
        // Tandai status Lulus jika kolom status ada
        if (Schema::hasColumn($this->getTable(), 'status')) {
            $this->status = 'Lulus';
        }
    }

    public function markAsTransferred(?string $date = null): void
    {
        // Jika ada kolom transfer_date maka isi, kalau tidak abaikan
        if (Schema::hasColumn($this->getTable(), 'transfer_date')) {
            $this->transfer_date = $date;
        }
        if (Schema::hasColumn($this->getTable(), 'status')) {
            $this->status = 'Pindah';
        }
    }

    public function markAsDroppedOut(?string $date = null): void
    {
        if (Schema::hasColumn($this->getTable(), 'dropout_date')) {
            $this->dropout_date = $date;
        }
        if (Schema::hasColumn($this->getTable(), 'status')) {
            $this->status = 'Drop Out';
        }
    }

    public function markAsRetained(): void
    {
        // Tetap di kelas saat ini, tandai sebagai Aktif
        $this->status = 'Aktif';
    }
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