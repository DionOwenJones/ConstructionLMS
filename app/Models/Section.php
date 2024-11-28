<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CourseSection extends Model
{
    protected $table = 'course_sections';

    protected $fillable = [
        'title',
        'content',
        'order',
        'course_id',
        'image'
    ];

    protected $with = ['contentBlocks'];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function contentBlocks(): HasMany
    {
        return $this->hasMany(ContentBlock::class, 'section_id')->orderBy('order');
    }

    public function getContentAttribute($value)
    {
        return json_decode($value, true);
    }

    public function setContentAttribute($value)
    {
        $this->attributes['content'] = is_array($value) ? json_encode($value) : $value;
    }
}
