<?php

namespace App\Http\Controllers;

use App\Models\CourseSection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Course;

class SectionController extends Controller
{
    public function show(CourseSection $section)
    {
        $userId = Auth::id();
        $enrollment = DB::table('course_user')
            ->where('user_id', $userId)
            ->where('course_id', $section->course_id)
            ->first();

        if (!$enrollment) {
            return response()->json(['error' => 'Not enrolled'], 403);
        }

        // Handle JSON content if stored as such
        $content = is_string($section->content) ? $section->content : json_decode($section->content);

        return response()->json([
            'title' => $section->title,
            'content' => $content,
            'completed' => in_array($section->id, json_decode($enrollment->completed_sections ?? '[]', true))
        ]);
    }

    public function complete(Course $course, CourseSection $courseSection)
    {
        $userId = Auth::id();
        $enrollment = DB::table('course_user')
            ->where('user_id', $userId)
            ->where('course_id', $course->id)
            ->first();

        if (!$enrollment) {
            return response()->json(['error' => 'Not enrolled'], 403);
        }

        $completedSections = json_decode($enrollment->completed_sections ?? '[]', true);
        if (!in_array($courseSection->id, $completedSections)) {
            $completedSections[] = $courseSection->id;
        }

        // Calculate progress
        $totalSections = $course->sections()->count();
        $progress = round((count($completedSections) / $totalSections) * 100);

        DB::table('course_user')
            ->where('user_id', $userId)
            ->where('course_id', $course->id)
            ->update([
                'completed_sections' => json_encode($completedSections),
                'completed_sections_count' => count($completedSections),
                'current_section_id' => $courseSection->id,
                'completed_at' => $progress === 100 ? now() : null,
                'completed' => $progress === 100 ? true : false
            ]);

        if ($progress === 100) {
            // Single update for course_user record
            DB::table('course_user')
                ->where('user_id', $userId)
                ->where('course_id', $course->id)
                ->update([
                    'completed_sections' => json_encode($completedSections),
                    'completed_sections_count' => count($completedSections),
                    'current_section_id' => $courseSection->id,
                    'completed' => true,
                    'completed_at' => now(),
                    'last_accessed_at' => now()
                ]);

            // Check if this is a business-allocated course
            $businessAllocation = DB::table('business_course_allocations')
                ->where('user_id', $userId)
                ->first();

            if ($businessAllocation) {
                DB::table('business_course_allocations')
                    ->where('id', $businessAllocation->id)
                    ->update([
                        'completed' => true,
                        'completed_at' => now()
                    ]);
            }

            return response()->json([
                'success' => true,
                'progress' => $progress,
                'redirect' => route('dashboard'),
                'message' => 'Course completed! You can now generate your certificate.'
            ]);
        }

        return response()->json([
            'success' => true,
            'progress' => $progress
        ]);
    }

    public function markCurrent(CourseSection $section)
    {
        $userId = Auth::id();

        DB::table('course_user')
            ->where('user_id', $userId)
            ->where('course_id', $section->course_id)
            ->update(['current_section_id' => $section->id]);

        return response()->json(['success' => true]);
    }
}
