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
        'licenses_purchased',
        'licenses_allocated',
        'price_per_license',
        'total_amount',
        'stripe_payment_id',
        'purchased_at'
    ];

    protected $casts = [
        'purchased_at' => 'datetime',
        'price_per_license' => 'decimal:2',
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
        return $this->hasMany(BusinessCourseAllocation::class, 'business_course_purchase_id');
    }

    public function getLicensesUsedAttribute(): int
    {
        return $this->allocations()->count();
    }

    public function getAvailableLicensesCount(): int
    {
        return $this->licenses_purchased - $this->licenses_allocated;
    }

    public function hasAvailableLicenses(): bool
    {
        return $this->licenses_allocated < $this->licenses_purchased;
    }

    public function getTotalPriceAttribute(): float
    {
        return $this->total_amount ?? ($this->licenses_purchased * $this->price_per_license);
    }

    public function allocateToEmployee(BusinessEmployee $employee, ?string $expiresAt = null): BusinessCourseAllocation
    {
        if (!$this->hasAvailableLicenses()) {
            throw new \Exception('No available licenses for this course purchase.');
        }

        return BusinessCourseAllocation::create([
            'business_course_purchase_id' => $this->id,
            'user_id' => $employee->user_id,
            'allocated_at' => now(),
            'expires_at' => $expiresAt
        ]);
    }
}
