<?php

namespace App\Observers;

use App\Models\Course;
use Illuminate\Support\Str;

class CourseObserver
{
    /**
     * Handle the Course "creating" event.
     */
    public function creating(Course $course): void
    {
        // Generate slug from title if not set
        if (empty($course->slug)) {
            $course->slug = $this->generateUniqueSlug($course->title);
        }
    }

    /**
     * Handle the Course "updating" event.
     */
    public function updating(Course $course): void
    {
        // Update slug if title has changed
        if ($course->isDirty('title')) {
            $course->slug = $this->generateUniqueSlug($course->title);
        }
    }

    /**
     * Generate a unique slug for the course.
     */
    protected function generateUniqueSlug(string $title): string
    {
        $slug = Str::slug($title);
        $count = 2;

        while (Course::where('slug', $slug)->exists()) {
            $slug = Str::slug($title) . '-' . $count;
            $count++;
        }

        return $slug;
    }
}
