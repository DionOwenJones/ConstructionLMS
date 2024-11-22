<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SectionContentBlock extends Model
{
    protected $table = 'section_content_blocks';

    protected $fillable = [
        'section_id',
        'type',
        'content',
        'order'
    ];

    protected $casts = [
        'content' => 'array'
    ];

    /**
     * Get the section that owns this content block.
     */
    public function section(): BelongsTo
    {
        return $this->belongsTo(CourseSection::class, 'section_id');
    }
}
