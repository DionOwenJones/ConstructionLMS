<?php
/**
 * Business Employee Model
 * 
 * This model represents the relationship between a business and its employees.
 * It handles employee management, course allocations, and role assignments within a business context.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BusinessEmployee extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'business_id',
        'user_id',
        'role',
        'department',
        'position',
        'employee_number',
        'start_date',
        'status'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'start_date' => 'datetime',
    ];

    /**
     * Get the business that the employee belongs to.
     * 
     * @return BelongsTo
     */
    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    /**
     * Get the user associated with the employee.
     * 
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all course allocations for this employee.
     * These are courses that have been assigned to the employee by the business.
     * 
     * @return HasMany
     */
    public function courseAllocations(): HasMany
    {
        return $this->hasMany(BusinessCourseAllocation::class, 'user_id', 'user_id')
            ->whereHas('purchase', function($query) {
                $query->where('business_id', $this->business_id);
            });
    }

    /**
     * Get all completed courses for this employee.
     * 
     * @return array
     */
    public function getCompletedCourses(): array
    {
        return $this->courseAllocations()
            ->whereHas('courseProgress', function ($query) {
                $query->where('completed', true);
            })
            ->with(['course', 'courseProgress'])
            ->get()
            ->toArray();
    }

    /**
     * Get the completion rate for all allocated courses.
     * 
     * @return float
     */
    public function getCompletionRate(): float
    {
        $totalCourses = $this->courseAllocations()->count();
        if ($totalCourses === 0) {
            return 0;
        }

        $completedCourses = $this->courseAllocations()
            ->whereHas('courseProgress', function ($query) {
                $query->where('completed', true);
            })
            ->count();

        return round(($completedCourses / $totalCourses) * 100, 2);
    }

    /**
     * Check if the employee has access to a specific course.
     * 
     * @param int $courseId
     * @return bool
     */
    public function hasAccessToCourse(int $courseId): bool
    {
        return $this->courseAllocations()
            ->where('course_id', $courseId)
            ->exists();
    }

    /**
     * Get the employee's progress for a specific course.
     * 
     * @param int $courseId
     * @return array|null
     */
    public function getCourseProgress(int $courseId): ?array
    {
        $allocation = $this->courseAllocations()
            ->where('course_id', $courseId)
            ->with('courseProgress')
            ->first();

        return $allocation ? $allocation->courseProgress->toArray() : null;
    }

    /**
     * Check if the employee has a manager role.
     * 
     * @return bool
     */
    public function isManager(): bool
    {
        return $this->role === 'manager';
    }

    /**
     * Get all certificates earned by the employee.
     * 
     * @return HasMany
     */
    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class);
    }
}
