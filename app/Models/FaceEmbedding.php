<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FaceEmbedding extends Model
{
    protected $fillable = [
        'nik',
        'embedding_path',
        'face_image_path',
        'confidence_score',
        'bbox',
    ];

    protected $casts = [
        'confidence_score' => 'float',
        'bbox' => 'array',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'nik', 'nik');
    }
}
