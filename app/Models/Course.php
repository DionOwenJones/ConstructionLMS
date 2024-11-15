<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Traits\HasSlug;
use Carbon\Carbon;

class Course extends Model
{
    use HasFactory;
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
        'business_id',
        'featured',
        'estimated_hours'
    ];


    protected $dates = [
        'published_at'
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'price' => 'decimal:2',
        'featured' => 'boolean'
    ];

    // Relationship with users who created the course
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Relationship with enrolled students
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
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

    // Relationship with transactions
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function sections(): HasMany
    {
        return $this->hasMany(CourseSection::class)->orderBy('order');
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

    public function isNewCourse(): bool
    {
        return $this->created_at->diffInDays(now()) <= 14; // Course is new if created within last 2 weeks
    }

    public function isPopularCourse(): bool
    {
        // Course is popular if it has more than 10 enrollments
        $enrollmentCount = $this->users()->count();
        return $enrollmentCount >= 10;
    }

    public function getEnrollmentCount(): int
    {
        return $this->users()->count();
    }
}
