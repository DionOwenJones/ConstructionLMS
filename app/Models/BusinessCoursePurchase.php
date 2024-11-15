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
        'purchased_at'
    ];

    protected $casts = [
        'purchased_at' => 'datetime',
        'price_per_seat' => 'decimal:2'
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
        return $this->seats_purchased * $this->price_per_seat;
    }

    public function allocateToUser(User $user, ?string $expiresAt = null): BusinessCourseAllocation
    {
        if (!$this->hasAvailableSeats()) {
            throw new \Exception('No available seats for this purchase.');
        }

        return $this->allocations()->create([
            'user_id' => $user->id,
            'allocated_at' => now(),
            'expires_at' => $expiresAt ? now()->parse($expiresAt) : null
        ]);
    }
}
