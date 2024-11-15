<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseMaterial extends Model
{
    protected $fillable = [
        'title',
        'type',
        'file_path',
        'order',
        'course_id'
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
