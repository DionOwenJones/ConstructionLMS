<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\ContentBlock;
use App\Models\User;
use App\Models\Course;

class CourseSection extends Model
{
    use HasFactory;

    protected $table = 'course_sections';

    protected $fillable = [
        'course_id',
        'title',
        'content',
        'order',
        'type',
        'data'
    ];

    protected $casts = [
        'data' => 'array',
        'content' => 'json'
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
     * Get the content blocks for this section.
     */
    public function contentBlocks(): HasMany
    {
        return $this->hasMany(ContentBlock::class, 'section_id')->orderBy('order');
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
