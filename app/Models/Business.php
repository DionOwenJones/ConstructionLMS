<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Business extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'company_name',
        'email',
        'is_setup_complete'
    ];

    protected $casts = [
        'is_setup_complete' => 'boolean'
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relationship to employees
    public function employees(): HasMany
    {
        return $this->hasMany(BusinessEmployee::class)
            ->with('user');  // Always eager load the user relationship
    }

    // Relationship to course purchases
    public function coursePurchases(): HasMany
    {
        return $this->hasMany(BusinessCoursePurchase::class);
    }

    // Count total number of unique courses purchased
    public function countDistinctCourses()
    {
        return $this->coursePurchases()
            ->distinct('course_id')
            ->count('course_id');
    }

    // Get all courses for this business
    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'business_course_purchases')
            ->withTimestamps();
    }
}
