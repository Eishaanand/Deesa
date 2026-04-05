<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UserExam extends Model
{
    use HasFactory;

    protected $table = 'user_exams';

    protected $fillable = [
        'user_id',
        'exam_id',
        'status',
        'current_section_id',
        'current_question_index',
        'section_seconds_remaining',
        'started_at',
        'paused_at',
        'submitted_at',
        'ended_at',
        'total_score',
        'accuracy_percentage',
        'analytics',
        'assigned_question_ids',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'paused_at' => 'datetime',
            'submitted_at' => 'datetime',
            'ended_at' => 'datetime',
            'analytics' => 'array',
            'assigned_question_ids' => 'array',
            'total_score' => 'float',
            'accuracy_percentage' => 'float',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    public function currentSection(): BelongsTo
    {
        return $this->belongsTo(Section::class, 'current_section_id');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(UserAnswer::class, 'attempt_id');
    }

    public function isPaused(): bool
    {
        return $this->status === 'paused';
    }
}
