<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * Spatie permission guard name.
     */
    protected $guard_name = 'web';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'nip_nik',
        'photo',
        'address',
        'status',
        'email',
        'password',
        'role_id',
        'is_walikelas',
        'must_change_password',
        'class_room_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Relationships
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function attendance()
    {
        return $this->hasMany(Attendance::class);
    }

    public function leaves()
    {
        return $this->hasMany(Leave::class);
    }

    public function approvedLeaves()
    {
        return $this->hasMany(Leave::class, 'approved_by');
    }

    public function studentAttendance()
    {
        return $this->hasMany(StudentAttendance::class, 'teacher_id');
    }

    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class);
    }

    public function classRooms()
    {
        return $this->hasMany(ClassRoom::class, 'walikelas_id');
    }

    public function classRoom()
    {
        return $this->belongsTo(ClassRoom::class, 'class_room_id');
    }

    // Helper methods
    public function hasRole($roleName)
    {
        return $this->role && $this->role->role_name === $roleName;
    }

    public function isAdmin()
    {
        return $this->hasRole('Admin');
    }

    public function isTeacher()
    {
        return $this->hasRole('Guru');
    }

    public function isEmployee()
    {
        return $this->hasRole('Pegawai');
    }

    public function isStudent()
    {
        return $this->hasRole('Siswa');
    }

    public function isVicePrincipal()
    {
        return $this->hasRole('Waka Kurikulum');
    }

    public function isKepalaSekolah()
    {
        return $this->hasRole('Kepala Sekolah');
    }

    public function canApproveLeaves()
    {
        return $this->isAdmin() || $this->isVicePrincipal() || $this->isKepalaSekolah();
    }

    public function isWalikelas()
    {
        return $this->classRooms()->exists();
    }

    public function getWalikelasClass()
    {
        return $this->classRooms()->first();
    }

    public function getWalikelasStudents()
    {
        $class = $this->getWalikelasClass();
        return $class ? $class->students : collect();
    }
}
