<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\HasSlug;

class Course extends Model
{
    use HasSlug;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'price',
        'image',
        'status',
        'published_at',
        'user_id',
        'business_id'
    ];


    protected $dates = [
        'published_at'
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'price' => 'decimal:2'
    ];

    // Relationship with users who created the course
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relationship with enrolled students
    public function students()
    {
        return $this->belongsToMany(User::class, 'course_user')
            ->withTimestamps()
            ->withPivot('completed');
    }

    // Relationship with transactions
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function sections()
    {
        return $this->hasMany(CourseSection::class);
    }

    public function getIsNewAttribute()
    {
        return $this->created_at->gt(now()->subDays(7));
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function isPublished()
    {
        return $this->status === 'published' &&
               $this->published_at &&
               $this->published_at <= now();
    }

    public function isNew()
    {
        return $this->created_at->gt(now()->subDays(7));
    }

    public function isReadyToPublish()
    {
        return $this->title
            && $this->description
            && $this->price
            && $this->image
            && $this->sections()->count() > 0;
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function purchases()
    {
        return $this->hasMany(BusinessCoursePurchase::class);
    }

    public function businesses()
    {
        return $this->belongsToMany(Business::class, 'business_course_purchases')
            ->withPivot(['seats_purchased', 'seats_allocated'])
            ->withTimestamps();
    }

    public function allocations()
    {
        return $this->hasManyThrough(
            BusinessCourseAllocation::class,
            BusinessCoursePurchase::class,
            'course_id', // Foreign key on business_course_purchases table
            'business_course_purchase_id', // Foreign key on business_course_allocations table
            'id', // Local key on courses table
            'id' // Local key on business_course_purchases table
        );
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
