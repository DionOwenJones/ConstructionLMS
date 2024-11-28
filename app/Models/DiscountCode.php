<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DiscountCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'description',
        'discount_percentage',
        'valid_from',
        'valid_until',
        'is_active',
        'usage_limit',
        'times_used'
    ];

    protected $casts = [
        'valid_from' => 'datetime',
        'valid_until' => 'datetime',
        'is_active' => 'boolean',
        'usage_limit' => 'integer',
        'times_used' => 'integer',
        'discount_percentage' => 'float'
    ];

    public function isValid()
    {
        $now = now();
        return $this->is_active &&
            $now->greaterThanOrEqualTo($this->valid_from) &&
            ($this->valid_until === null || $now->lessThanOrEqualTo($this->valid_until)) &&
            ($this->usage_limit === null || $this->times_used < $this->usage_limit);
    }

    public function incrementUsage()
    {
        $this->increment('times_used');
    }
}
