<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'section_id',
        'type',
        'stem',
        'passage',
        'options',
        'correct_answer',
        'explanation',
        'difficulty',
        'topic',
        'metadata',
        'sequence',
        'source',
    ];

    protected function casts(): array
    {
        return [
            'options' => 'array',
            'metadata' => 'array',
        ];
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(UserAnswer::class);
    }
}
