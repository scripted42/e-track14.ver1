<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClassRoom extends Model
{
    protected $fillable = [
        'name',
        'level',
        'description',
        'walikelas_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relationships
    public function walikelas(): BelongsTo
    {
        return $this->belongsTo(User::class, 'walikelas_id');
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class, 'class_room_id');
    }

    public function walikelasUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'walikelas_id');
    }

    // Helper methods
    public function getStudentCountAttribute()
    {
        return $this->students()->count();
    }

    public function isActive()
    {
        return $this->is_active;
    }
}
