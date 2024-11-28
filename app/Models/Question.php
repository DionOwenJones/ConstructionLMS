<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Question extends Model
{
    protected $fillable = [
        'assessment_id',
        'type',
        'question_text',
        'options',
        'correct_answer',
        'feedback',
        'points',
        'order'
    ];

    protected $casts = [
        'options' => 'array',
        'correct_answer' => 'array',
    ];

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class);
    }

    public function gradeAnswer($answer): array
    {
        $isCorrect = false;
        $score = 0;
        $feedback = $this->feedback;

        switch ($this->type) {
            case 'multiple_choice':
                $isCorrect = $answer === $this->correct_answer['answer'];
                $score = $isCorrect ? $this->points : 0;
                break;

            case 'matching':
                $correctPairs = collect($this->correct_answer['pairs']);
                $userPairs = collect($answer);
                $matchedCount = $correctPairs->intersect($userPairs)->count();
                $score = ($matchedCount / $correctPairs->count()) * $this->points;
                $isCorrect = $score === $this->points;
                break;

            case 'essay':
                // Essay questions require manual grading
                $score = null;
                $isCorrect = null;
                break;
        }

        return [
            'is_correct' => $isCorrect,
            'score' => $score,
            'feedback' => $feedback,
        ];
    }
}
