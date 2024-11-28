<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Cashier\Billable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, Billable;

    const ROLE_ADMIN = 'admin';
    const ROLE_BUSINESS = 'business';
    const ROLE_USER = 'user';

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'business_id',
        'social_id',
        'social_type',
        'social_avatar',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'last_login_at' => 'datetime',
    ];

    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class)
            ->withPivot(['completed', 'completed_at', 'completed_sections', 'completed_sections_count', 'current_section_id', 'last_accessed_at'])
            ->withTimestamps();
    }

    public function completedCourses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class)
            ->wherePivot('completed', true);
    }

    public function inProgressCourses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class)
            ->wherePivot('completed', false)
            ->wherePivot('completed_sections_count', '>', 0)
            ->withPivot(['completed_sections_count', 'last_accessed_at']);
    }

    /**
     * Check if the user is an admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isBusinessOwner(): bool
    {
        return strtolower($this->role) === self::ROLE_BUSINESS;
    }

    public function isBusiness(): bool
    {
        return strtolower($this->role) === self::ROLE_BUSINESS;
    }

    public function isUser(): bool
    {
        return strtolower($this->role) === self::ROLE_USER;
    }

    public function isBusinessEmployee(): bool
    {
        return $this->business_id !== null;
    }

    public function ownedBusiness(): HasOne
    {
        return $this->hasOne(Business::class, 'user_id');
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function getBusiness()
    {
        return $this->isBusinessOwner() ? $this->ownedBusiness : $this->business;
    }

    public function hasRole($role): bool
    {
        return strtolower($this->role) === strtolower($role);
    }

    public function businessEmployee()
    {
        if (!$this->belongsToBusiness()) {
            return null;
        }

        return BusinessEmployee::where('user_id', $this->id)
            ->where('business_id', $this->business_id)
            ->first();
    }

    public function businessCourses()
    {
        if (!$this->belongsToBusiness() && !$this->isBusinessOwner()) {
            return collect();
        }

        if ($this->isBusinessOwner()) {
            return $this->ownedBusiness->coursePurchases()
                ->with('course')
                ->get()
                ->pluck('course');
        }

        return Course::whereHas('businessPurchases', function ($query) {
            $query->where('business_id', $this->business_id);
        });
    }

    public function certificates()
    {
        return $this->hasMany(Certificate::class);
    }

    public function belongsToBusiness(): bool
    {
        return !is_null($this->business_id);
    }
}
