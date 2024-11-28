<?php
/**
 * Business Course Allocation Model
 * 
 * This model manages the allocation of courses to employees within a business.
 * It tracks which courses are assigned to which employees, including completion deadlines
 * and progress tracking.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BusinessCourseAllocation extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'business_course_purchase_id',
        'user_id',
        'allocated_at',
        'expires_at'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'allocated_at' => 'datetime',
        'expires_at' => 'datetime'
    ];

    /**
     * Get the purchase that owns this course allocation.
     * 
     * @return BelongsTo
     */
    public function purchase(): BelongsTo
    {
        return $this->belongsTo(BusinessCoursePurchase::class, 'business_course_purchase_id');
    }

    /**
     * Get the user this course is allocated to.
     * 
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the employee this course is allocated to.
     * 
     * @return BelongsTo
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(BusinessEmployee::class, 'user_id', 'user_id')
            ->where('business_id', function($query) {
                $query->select('business_id')
                    ->from('business_course_purchases')
                    ->where('id', $this->business_course_purchase_id)
                    ->limit(1);
            });
    }

    /**
     * Get the course that is allocated.
     * 
     * @return HasOneThrough
     */
    public function course()
    {
        return $this->hasOneThrough(
            Course::class,
            BusinessCoursePurchase::class,
            'id',
            'id',
            'business_course_purchase_id',
            'course_id'
        );
    }

    /**
     * Check if the course allocation has expired.
     * 
     * @return bool
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Booted method to handle events.
     */
    protected static function booted()
    {
        static::created(function ($allocation) {
            // When a course is allocated, also add it to the user's courses if not already added
            if (!$allocation->user->courses()->where('course_id', $allocation->purchase->course_id)->exists()) {
                $allocation->user->courses()->attach(
                    $allocation->purchase->course_id,
                    [
                        'allocated_by_business_id' => $allocation->purchase->business_id,
                        'allocated_at' => $allocation->allocated_at,
                        'completed_sections_count' => 0,
                        'completed' => false
                    ]
                );
            }
        });

        static::deleted(function ($allocation) {
            // When allocation is removed, detach the course from user's courses
            $allocation->user->courses()->detach($allocation->purchase->course_id);
        });
    }
}