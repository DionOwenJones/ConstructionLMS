<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContentBlock extends Model
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
     * The validation rules that apply to the model.
     *
     * @var array
     */
    public static $rules = [
        'section_id' => 'required|exists:course_sections,id',
        'type' => 'required|in:text,video,image,quiz',
        'text_content' => 'required_if:type,text',
        'video_url' => 'required_if:type,video|url',
        'video_title' => 'nullable|string|max:255',
        'image_path' => 'required_if:type,image|string',
        'quiz_data' => 'required_if:type,quiz|array',
        'order' => 'required|integer|min:0'
    ];

    public function section(): BelongsTo
    {
        return $this->belongsTo(CourseSection::class, 'section_id');
    }

    /**
     * Get the formatted content based on the block type
     *
     * @return mixed
     */
    public function getFormattedContentAttribute()
    {
        switch($this->type) {
            case 'text':
                return $this->text_content;
            
            case 'video':
                if (!$this->video_url) return null;
                
                // Extract video ID from various YouTube URL formats
                $patterns = [
                    '/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/i',
                    '/^[^"&?\/\s]{11}$/' // Direct video ID
                ];
                
                $videoId = null;
                foreach ($patterns as $pattern) {
                    if (preg_match($pattern, $this->video_url, $matches)) {
                        $videoId = $matches[1] ?? $matches[0];
                        break;
                    }
                }
                
                return [
                    'url' => $this->video_url,
                    'id' => $videoId,
                    'title' => $this->video_title
                ];
            
            case 'image':
                return $this->image_path;
            
            case 'quiz':
                return $this->quiz_data;
            
            default:
                return null;
        }
    }

    /**
     * Validate quiz data structure
     *
     * @param array $data
     * @return bool
     */
    public static function validateQuizData($data)
    {
        if (!is_array($data)) return false;
        if (!isset($data['questions']) || !is_array($data['questions'])) return false;

        foreach ($data['questions'] as $question) {
            if (!isset($question['question']) || !is_string($question['question'])) return false;
            if (!isset($question['options']) || !is_array($question['options'])) return false;
            if (!isset($question['correct_answer']) || !is_numeric($question['correct_answer'])) return false;
            
            // Validate options
            if (count($question['options']) < 2) return false;
            foreach ($question['options'] as $option) {
                if (!is_string($option)) return false;
            }
            
            // Validate correct_answer is within options range
            if ($question['correct_answer'] < 0 || $question['correct_answer'] >= count($question['options'])) return false;
        }

        return true;
    }
}
