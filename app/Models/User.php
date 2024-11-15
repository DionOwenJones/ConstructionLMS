<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Available roles for users
     */
    const ROLE_ADMIN = 'admin';        // Can create and manage courses
    const ROLE_BUSINESS = 'business';   // Can buy courses and allocate to employees
    const ROLE_USER = 'user';          // Regular user or employee

    /**
     * Check if user has a specific role
     */
    public function hasRole($role)
    {
        if (is_array($role)) {
            return in_array($this->role, $role);
        }
        return $this->role === $role;
    }

    /**
     * Check if user is an admin
     */
    public function isAdmin()
    {
        return $this->role === self::ROLE_ADMIN;
    }

    /**
     * Check if user is a business owner
     */
    public function isBusiness()
    {
        return $this->role === self::ROLE_BUSINESS;
    }

    /**
     * Check if user is a regular user or employee
     */
    public function isUser()
    {
        return $this->role === self::ROLE_USER;
    }

    /**
     * Check if user is an employee (user associated with a business)
     */
    public function isEmployee()
    {
        return $this->role === self::ROLE_USER && $this->businesses()->exists();
    }

    /**
     * Get the courses that the user is enrolled in.
     */
    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'course_user')
            ->withPivot([
                'enrolled_at',
                'completed',
                'completed_at',
                'current_section_id',
                'completed_sections',
                'completed_sections_count',
                'last_accessed_at'
            ])
            ->withTimestamps();
    }

    /**
     * Get the user's enrolled courses that are not completed.
     */
    public function enrolledCourses(): BelongsToMany
    {
        return $this->courses()->wherePivot('completed', false);
    }

    /**
     * Get the user's completed courses.
     */
    public function completedCourses(): BelongsToMany
    {
        return $this->courses()->wherePivot('completed', true);
    }

    /**
     * Get the businesses that the user belongs to (as an employee).
     */
    public function businesses(): BelongsToMany
    {
        return $this->belongsToMany(Business::class, 'business_employees')
            ->withTimestamps();
    }

    /**
     * Get the business that the user owns (if they are a business owner).
     */
    public function business()
    {
        return $this->hasOne(Business::class);
    }

    /**
     * Get the employee record for this user at a specific business
     */
    public function employeeAt(Business $business)
    {
        return BusinessEmployee::where('user_id', $this->id)
            ->where('business_id', $business->id)
            ->first();
    }

    /**
     * Get the user's profile photo URL.
     */
    public function getProfilePhotoUrlAttribute()
    {
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&color=7F9CF5&background=EBF4FF';
    }

    /**
     * Get the available roles.
     */
    public static function getRoles()
    {
        return ['admin', 'business', 'user'];
    }
}
