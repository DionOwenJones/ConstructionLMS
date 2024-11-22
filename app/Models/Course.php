<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'status',
        'image',
        'user_id',
        'price',
        'stripe_price_id',
        'stripe_product_id',
        'is_free',
        'business_id',
        'slug',
        'has_expiry',
        'validity_months'
    ];

    protected $casts = [
        'is_free' => 'boolean',
        'price' => 'decimal:2',
        'has_expiry' => 'boolean',
        'validity_months' => 'integer'
    ];

    /**
     * Get the user that owns the course.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the sections for the course.
     */
    public function sections(): HasMany
    {
        return $this->hasMany(CourseSection::class)->orderBy('order');
    }

    /**
     * Get the users enrolled in the course.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'course_user')
            ->withPivot('completed', 'completed_at', 'current_section_id', 'last_accessed_at', 'completed_sections', 'completed_sections_count')
            ->withTimestamps();
    }

    /**
     * Get the business that owns the course.
     */
    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    /**
     * Get the orders for this course.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Get the purchases for this course.
     */
    public function purchases()
    {
        return $this->hasMany(CoursePurchase::class);
    }

    /**
     * Get the business purchases for this course.
     */
    public function businessPurchases()
    {
        return $this->hasMany(BusinessCoursePurchase::class);
    }

    /**
     * Check if a user has purchased this course or has it allocated through a business.
     */
    public function isPurchasedBy(?User $user): bool
    {
        // If no user (guest), they haven't purchased
        if (!$user) {
            return false;
        }

        // Check for direct purchase
        $directPurchase = $this->purchases()
            ->where('user_id', $user->id)
            ->where('status', 'completed')
            ->exists();

        if ($directPurchase) {
            return true;
        }

        // Check for business allocation
        return BusinessCourseAllocation::query()
            ->whereHas('purchase', function ($query) {
                $query->where('course_id', $this->id);
            })
            ->where('user_id', $user->id)
            ->exists();
    }

    /**
     * Get formatted price attribute.
     */
    public function getFormattedPriceAttribute(): string
    {
        if ($this->is_free || $this->price == 0) {
            return 'Free';
        }
        return '$' . number_format($this->price, 2);
    }

    /**
     * Get the businesses that have purchased this course.
     */
    public function businesses()
    {
        return $this->belongsToMany(Business::class, 'business_course_purchases')
            ->withPivot(['seats_purchased', 'price_per_seat'])
            ->withTimestamps();
    }

    /**
     * Check if the course can be accessed by the user.
     */
    public function canBeAccessedBy(User $user): bool
    {
        // Check if user has purchased the course
        if ($this->isPurchasedBy($user)) {
            return true;
        }

        // If user is a business employee, check if their business has purchased the course
        if ($user->isBusinessEmployee()) {
            return $user->business->hasPurchasedCourse($this);
        }

        return false;
    }

    /**
     * Get the progress for a specific user.
     */
    public function getProgressForUser(User $user): float
    {
        $totalSections = $this->sections()->count();
        if ($totalSections === 0) {
            return 0;
        }

        $completedSections = DB::table('section_user')
            ->where('user_id', $user->id)
            ->whereIn('section_id', $this->sections()->pluck('id'))
            ->where('completed', true)
            ->count();

        return ($completedSections / $totalSections) * 100;
    }

    /**
     * Get the current section for a specific user.
     */
    public function getCurrentSectionForUser(User $user)
    {
        // Get the enrollment record
        $enrollment = $this->users()->where('user_id', $user->id)->first();
        
        // If there's no enrollment or no current section set
        if (!$enrollment || !$enrollment->pivot->current_section_id) {
            // Try to get the first section
            $firstSection = $this->sections()->orderBy('order')->first();
            
            // If we found a first section, update the enrollment
            if ($firstSection && $enrollment) {
                $this->users()->updateExistingPivot($user->id, [
                    'current_section_id' => $firstSection->id
                ]);
                return $firstSection;
            }
            
            // If no sections exist, return null
            return null;
        }

        return CourseSection::find($enrollment->pivot->current_section_id);
    }

    /**
     * Get the current section for a user.
     */
    public function getCurrentSectionForUserNew(User $user)
    {
        $pivot = $this->users()->where('user_id', $user->id)->first()?->pivot;
        if (!$pivot || !$pivot->current_section_id) {
            return null;
        }
        return $this->sections()->find($pivot->current_section_id);
    }

    /**
     * Get completed sections for a user.
     */
    public function getCompletedSectionsForUser(User $user): array
    {
        $pivot = $this->users()->where('user_id', $user->id)->first()?->pivot;
        if (!$pivot || !$pivot->completed_sections) {
            return [];
        }
        return json_decode($pivot->completed_sections, true) ?? [];
    }

    /**
     * Get the progress percentage for a user.
     */
    public function getProgressForUserNew(User $user): int
    {
        $totalSections = $this->sections()->count();
        if ($totalSections === 0) {
            return 0;
        }

        $completedCount = count($this->getCompletedSectionsForUser($user));
        return round(($completedCount / $totalSections) * 100);
    }

    /**
     * Check if the course is completed by a user.
     */
    public function isCompletedByUser(User $user): bool
    {
        $pivot = $this->users()->where('user_id', $user->id)->first()?->pivot;
        if (!$pivot) {
            return false;
        }

        $isCompleted = (bool)$pivot->completed;

        // If course is completed but no certificate exists, generate one
        if ($isCompleted && !$this->certificates()->where('user_id', $user->id)->exists()) {
            $this->generateCertificate($user);
        }

        return $isCompleted;
    }

    /**
     * Generate a certificate for a user.
     */
    protected function generateCertificate(User $user): void
    {
        $enrollment = $this->users()->where('user_id', $user->id)->first()?->pivot;
        if (!$enrollment || !$enrollment->completed) {
            return;
        }

        Certificate::create([
            'user_id' => $user->id,
            'course_id' => $this->id,
            'certificate_number' => sprintf('CERT-%s-%s-%s', 
                strtoupper(substr($user->name, 0, 3)),
                $this->id,
                date('Ymd', strtotime($enrollment->completed_at))
            ),
            'issued_at' => $enrollment->completed_at
        ]);
    }

    /**
     * Get the certificates for this course.
     */
    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class);
    }

    /**
     * Get the certificate for a specific user.
     */
    public function certificate()
    {
        return $this->hasOne(Certificate::class)->where('user_id', auth()->id());
    }

    /**
     * Check if all sections are completed by the user.
     */
    public function isCompletedByUserOld(User $user): bool
    {
        $totalSections = $this->sections()->count();
        if ($totalSections === 0) {
            return false;
        }

        $completedSections = DB::table('section_user')
            ->where('user_id', $user->id)
            ->whereIn('section_id', $this->sections()->pluck('id'))
            ->where('completed', true)
            ->count();

        return $completedSections === $totalSections;
    }

    /**
     * Update user's progress in the course.
     */
    public function updateUserProgress(User $user): void
    {
        if ($this->isCompletedByUserOld($user)) {
            $this->users()->updateExistingPivot($user->id, [
                'completed' => true,
                'completed_at' => now()
            ]);
        }
    }

    /**
     * Check if the course is completed for a user.
     */
    public function isCompletedForUser(User $user): bool
    {
        $enrollment = $this->users()->where('user_id', $user->id)->first();
        return $enrollment && $enrollment->pivot->completed;
    }

    /**
     * Calculate the expiry date for the course.
     */
    public function calculateExpiryDate()
    {
        if (!$this->has_expiry) {
            return null;
        }
        return now()->addMonths($this->validity_months);
    }

    /**
     * Get the days until expiry for a certificate.
     */
    public function getDaysUntilExpiry($certificateDate)
    {
        if (!$this->has_expiry || !$this->expiry_months || !$certificateDate) {
            return null;
        }

        $expiryDate = Carbon::parse($certificateDate)->addMonths($this->expiry_months);
        $now = Carbon::now();

        if ($now->gt($expiryDate)) {
            return 0;
        }

        return $now->diffInDays($expiryDate);
    }

    /**
     * Check if a certificate has expired.
     */
    public function isExpired($certificateDate)
    {
        if (!$this->has_expiry || !$certificateDate) {
            return false;
        }
        
        $expiryDate = $certificateDate->copy()->addMonths($this->validity_months);
        return now()->greaterThan($expiryDate);
    }

    /**
     * Check if a user is enrolled in this course
     */
    public function isUserEnrolled(?User $user): bool
    {
        if (!$user) {
            return false;
        }

        return $this->isPurchasedBy($user);
    }

    /**
     * Get the sections that a user can access
     */
    public function getAccessibleSections(?User $user)
    {
        $query = $this->sections()->orderBy('order');
        
        if (!$this->isUserEnrolled($user)) {
            $query->take(2); // Only first 2 sections for non-enrolled users
        }
        
        return $query;
    }
}
