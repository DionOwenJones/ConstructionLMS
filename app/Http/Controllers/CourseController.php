<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CourseController extends Controller
{
    public function view(Course $course)
    {
        $userId = Auth::id();
        $enrollment = DB::table('course_user')
            ->where('user_id', $userId)
            ->where('course_id', $course->id)
            ->first();

        if (!$enrollment) {
            return redirect()->route('courses.show', $course);
        }

        $sections = DB::table('course_sections')
            ->select('id', 'title', 'content', 'order')
            ->where('course_id', $course->id)
            ->orderBy('order')
            ->get();

        // Get the first section ID if no current section is set
        $currentSectionId = $enrollment->current_section_id ?? $sections->first()->id ?? null;

        // Create progress object with default values
        $progress = (object)[
            'completed_sections' => json_decode($enrollment->completed_sections ?? '[]'),
            'completed_sections_count' => $enrollment->completed_sections_count ?? 0,
            'current_section_id' => $currentSectionId
        ];

        return view('courses.view', compact('course', 'sections', 'progress'));
    }

    public function show(Course $course)
    {
        // Check if user is authenticated
        if (Auth::check()) {
            // Check if user is enrolled in this course
            $enrollment = DB::table('course_user')
                ->where('user_id', Auth::id())
                ->where('course_id', $course->id)
                ->first();

            if ($enrollment) {
                // Get progress data
                $progress = DB::table('course_user')
                    ->where('user_id', Auth::id())
                    ->where('course_id', $course->id)
                    ->select(['completed_sections', 'completed_sections_count'])
                    ->first();

                // Get course sections
                $sections = $course->sections()
                    ->orderBy('order')
                    ->get();

                // Return the course view for enrolled users
                return view('courses.view', compact('course', 'sections', 'progress'));
            }
        }

        // For non-enrolled users, show the preview/landing page
        $previewSections = $course->sections()
            ->orderBy('order')
            ->limit(3)
            ->get();

        return view('courses.preview', compact('course', 'previewSections'));
    }
}
