<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BusinessCoursePurchase extends Model
{
    protected $fillable = [
        'business_id',
        'course_id',
        'seats_purchased',
        'seats_allocated',
        'purchased_at'
    ];

    protected $casts = [
        'purchased_at' => 'datetime',
    ];

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function allocations(): HasMany
    {
        return $this->hasMany(BusinessCourseAllocation::class);
    }

    public function getAvailableSeatsAttribute(): int
    {
        return $this->seats_purchased - $this->seats_allocated;
    }

    public function hasAvailableSeats(): bool
    {
        return $this->available_seats > 0;
    }

    public function availableSeats(): int
    {
        return $this->seats_purchased - $this->seats_allocated;
    }

    public function allocate(BusinessEmployee $employee)
    {
        if ($this->availableSeats() <= 0) {
            throw new \Exception('No available seats for this course.');
        }

        $allocation = BusinessCourseAllocation::create([
            'business_course_purchase_id' => $this->id,
            'business_employee_id' => $employee->id,
            'allocated_at' => now(),
        ]);

        $this->increment('seats_allocated');

        // Attach course to user's dashboard
        $employee->user->courses()->attach($this->course_id, [
            'allocated_by_business_id' => $this->business_id,
            'allocated_at' => now(),
            'completed_sections_count' => 0,
            'completed' => false
        ]);

        return $allocation;
    }
}
