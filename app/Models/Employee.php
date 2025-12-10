<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    protected $fillable = [
        'nik',
        'name',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function faceEmbedding(): HasOne
    {
        return $this->hasOne(FaceEmbedding::class, 'nik', 'nik');
    }

    public function attendanceLogs(): HasMany
    {
        return $this->hasMany(AttendanceLog::class, 'nik', 'nik');
    }
    
    public function hasFaceRegistered(): bool
    {
        return $this->faceEmbedding()->exists();
    }
}
