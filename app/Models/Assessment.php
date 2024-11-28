<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Assessment extends Model
{
    protected $fillable = [
        'course_id',
        'title',
        'description',
        'time_limit',
        'passing_score',
        'randomize_questions',
        'show_feedback',
        'max_attempts'
    ];

    protected $casts = [
        'randomize_questions' => 'boolean',
        'show_feedback' => 'boolean',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class)->orderBy('order');
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(AssessmentAttempt::class);
    }

    public function getRemainingAttemptsForUser($userId): int
    {
        $attemptCount = $this->attempts()
            ->where('user_id', $userId)
            ->count();
        
        return max(0, $this->max_attempts - $attemptCount);
    }

    public function hasPassedAttempt($userId): bool
    {
        return $this->attempts()
            ->where('user_id', $userId)
            ->where('passed', true)
            ->exists();
    }
}
