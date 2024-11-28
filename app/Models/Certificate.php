<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Certificate extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'course_id',
        'certificate_number',
        'issued_at',
        'has_expiry',
        'expires_at'
    ];

    protected $casts = [
        'issued_at' => 'datetime',
        'expires_at' => 'datetime',
        'has_expiry' => 'boolean'
    ];

    /**
     * Get the user that owns the certificate.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the course for this certificate.
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Generate a unique certificate number.
     */
    public static function generateCertificateNumber(User $user, Course $course): string
    {
        return sprintf('CERT-%s-%s-%s', 
            strtoupper(substr($user->name, 0, 3)),
            $course->id,
            now()->format('Ymd')
        );
    }

    /**
     * Check if the certificate is expired
     */
    public function isExpired(): bool
    {
        if (!$this->has_expiry || !$this->expires_at) {
            return false;
        }

        return $this->expires_at->isPast();
    }

    /**
     * Get expiry status
     */
    public function getExpiryStatus(): string
    {
        if (!$this->has_expiry) {
            return 'No Expiry';
        }

        if ($this->isExpired()) {
            return 'Expired';
        }

        $daysUntilExpiry = Carbon::now()->diffInDays($this->expires_at, false);
        if ($daysUntilExpiry <= 30) {
            return 'Expires Soon';
        }

        return 'Valid';
    }

    /**
     * Set the expiry date based on the course settings
     */
    public function setExpiryFromCourse(): void
    {
        if (!$this->course || !$this->course->has_expiry || !$this->course->expiry_months) {
            $this->has_expiry = false;
            $this->expires_at = null;
            return;
        }

        $this->has_expiry = true;
        $this->expires_at = $this->issued_at->addMonths($this->course->expiry_months);
    }
}
