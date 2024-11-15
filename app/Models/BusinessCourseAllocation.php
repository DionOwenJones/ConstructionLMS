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
        'completed',
        'completed_at'
    ];

    protected $casts = [
        'allocated_at' => 'datetime',
        'completed_at' => 'datetime',
        'expires_at' => 'datetime',
        'completed' => 'boolean',
    ];

    public function purchase()
    {
        return $this->belongsTo(BusinessCoursePurchase::class, 'business_course_purchase_id');
    }

    public function employee()
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

    public function markAsCompleted(): void
    {
        $this->update([
            'completed' => true,
            'completed_at' => now(),
        ]);
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }
}
