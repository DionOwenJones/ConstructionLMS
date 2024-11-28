<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssessmentAttempt extends Model
{
    protected $fillable = [
        'assessment_id',
        'user_id',
        'answers',
        'score',
        'passed',
        'started_at',
        'completed_at'
    ];

    protected $casts = [
        'answers' => 'array',
        'passed' => 'boolean',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function calculateTimeSpent(): int
    {
        if (!$this->completed_at) {
            return 0;
        }

        return $this->started_at->diffInMinutes($this->completed_at);
    }

    public function isTimeExpired(): bool
    {
        if (!$this->assessment->time_limit) {
            return false;
        }

        $timeSpent = now()->diffInMinutes($this->started_at);
        return $timeSpent >= $this->assessment->time_limit;
    }
}
