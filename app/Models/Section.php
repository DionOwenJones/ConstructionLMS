<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    protected $table = 'sections';

    protected $fillable = [
        'title',
        'content',
        'order',
        'course_id',
        'image'
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
