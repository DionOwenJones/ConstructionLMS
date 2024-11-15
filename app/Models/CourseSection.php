<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseSection extends Model
{
    protected $fillable = [
        'course_id',
        'title',
        'content',
        'image',
        'order',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function units(): HasMany
    {
        return $this->hasMany(CourseUnit::class)->orderBy('order');
    }
}
