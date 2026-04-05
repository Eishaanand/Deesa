<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Exam extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'sequence_number',
        'description',
        'status',
        'total_duration_seconds',
        'is_ai_supported',
        'requires_subscription',
        'questions_per_section',
    ];

    protected function casts(): array
    {
        return [
            'is_ai_supported' => 'boolean',
            'requires_subscription' => 'boolean',
        ];
    }

    public function sections(): HasMany
    {
        return $this->hasMany(Section::class)->orderBy('sequence');
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(UserExam::class);
    }
}
