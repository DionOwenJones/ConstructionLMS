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
        'price_per_seat',
        'total_amount',
        'stripe_payment_id',
        'purchased_at'
    ];

    protected $casts = [
        'purchased_at' => 'datetime',
        'price_per_seat' => 'decimal:2',
        'total_amount' => 'decimal:2'
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
        return $this->seats_purchased - $this->allocations()->count();
    }

    public function hasAvailableSeats(): bool
    {
        return $this->available_seats > 0;
    }

    public function getTotalPriceAttribute(): float
    {
        return $this->total_amount ?? ($this->seats_purchased * $this->price_per_seat);
    }

    public function allocateToEmployee(BusinessEmployee $employee, ?string $expiresAt = null): BusinessCourseAllocation
    {
        if (!$this->hasAvailableSeats()) {
            throw new \Exception('No available seats for this course purchase.');
        }

        return BusinessCourseAllocation::create([
            'business_course_purchase_id' => $this->id,
            'business_employee_id' => $employee->id,
            'expires_at' => $expiresAt
        ]);
    }
}
