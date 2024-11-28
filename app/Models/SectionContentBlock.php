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
        'text_content',
        'video_url',
        'video_title',
        'image_path',
        'quiz_data',
        'order'
    ];

    protected $casts = [
        'quiz_data' => 'array'
    ];

    /**
     * Get the section that owns this content block.
     */
    public function section(): BelongsTo
    {
        return $this->belongsTo(CourseSection::class, 'section_id');
    }

    /**
     * Get the formatted content based on block type.
     */
    public function getFormattedContentAttribute(): string
    {
        if ($this->type === 'video') {
            return $this->video_url ?? '';
        } elseif ($this->type === 'text') {
            return $this->text_content ?? '';
        }
        
        return '';
    }
}
