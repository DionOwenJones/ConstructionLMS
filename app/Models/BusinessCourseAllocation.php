<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BusinessCourseAllocation extends Model
{
    protected $fillable = [
        'business_course_purchase_id',
        'business_employee_id',
        'allocated_at',
        'expires_at'
    ];

    protected $casts = [
        'allocated_at' => 'datetime',
        'expires_at' => 'datetime'
    ];

    public function purchase(): BelongsTo
    {
        return $this->belongsTo(BusinessCoursePurchase::class, 'business_course_purchase_id');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(BusinessEmployee::class, 'business_employee_id');
    }

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

    protected static function booted()
    {
        static::created(function ($allocation) {
            // When a course is allocated, also add it to the user's courses
            $allocation->employee->user->courses()->attach(
                $allocation->purchase->course_id,
                [
                    'allocated_by_business_id' => $allocation->purchase->business_id,
                    'allocated_at' => $allocation->allocated_at,
                    'completed_sections_count' => 0,
                    'completed' => false
                ]
            );
        });

        static::deleted(function ($allocation) {
            // When allocation is removed, detach the course from user's courses
            $allocation->employee->user->courses()->detach($allocation->purchase->course_id);
        });
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }
}