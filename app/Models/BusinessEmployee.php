<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class BusinessEmployee extends Model
{
    protected $fillable = [
        'business_id',
        'user_id',
    ];

    /**
     * Get the business that owns the employee.
     */
    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    /**
     * Get the user associated with the employee.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all courses allocated to this employee.
     */
    public function courses()
    {
        return $this->hasManyThrough(
            Course::class,
            BusinessCourseAllocation::class,
            'business_employee_id', // Foreign key on business_course_allocations table
            'id', // Foreign key on courses table
            'id', // Local key on business_employees table
            'course_id' // Local key on business_course_allocations table
        );
    }

    /**
     * Get all course allocations for this employee through their user account.
     */
    public function courseAllocations()
    {
        return $this->hasMany(BusinessCourseAllocation::class, 'user_id', 'user_id');
    }

    /**
     * Get all completed courses for this employee.
     */
    public function completedCourses()
    {
        return Course::join('course_user', 'courses.id', '=', 'course_user.course_id')
            ->where('course_user.user_id', $this->user_id)
            ->where('course_user.completed', true)
            ->select('courses.*', 'course_user.completed_at')
            ->get();
    }

    /**
     * Get all in-progress courses for this employee.
     */
    public function inProgressCourses()
    {
        return Course::join('course_user', 'courses.id', '=', 'course_user.course_id')
            ->where('course_user.user_id', $this->user_id)
            ->where('course_user.completed', false)
            ->select('courses.*', 'course_user.completed_sections_count', 'course_user.last_accessed_at')
            ->get();
    }

    /**
     * Get the course completion percentage for this employee.
     */
    public function courseCompletionPercentage()
    {
        $totalCourses = $this->courses()->count();
        if ($totalCourses === 0) {
            return 0;
        }

        $completedCourses = $this->completedCourses()->count();
        return round(($completedCourses / $totalCourses) * 100);
    }
}
