<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class CourseSection extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'title',
        'content',
        'order',
        'type',
        'data'
    ];

    protected $casts = [
        'data' => 'array'
    ];

    /**
     * Get the course that owns the section.
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get the users who have completed this section.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'section_user', 'section_id', 'user_id')
            ->withPivot('completed', 'completed_at')
            ->withTimestamps();
    }

    /**
     * Check if a user has completed this section.
     */
    public function isCompletedByUser(User $user): bool
    {
        if (!$user) {
            return false;
        }

        $completedSections = $this->course->getCompletedSectionsForUser($user);
        return in_array($this->id, $completedSections);
    }
}
