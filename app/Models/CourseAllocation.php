<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseAllocation extends Model
{
    protected $fillable = [
        'user_id',
        'course_id',
        'allocated_by',
        'allocated_at',
        'expires_at',
        'notes'
    ];

    protected $casts = [
        'allocated_at' => 'datetime',
        'expires_at' => 'datetime'
    ];

    /**
     * Get the user that this course is allocated to.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the course that is allocated.
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get the admin who allocated the course.
     */
    public function allocatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'allocated_by');
    }
}
