<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseSection;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CourseController extends Controller
{
    public function index()
    {
        // Get all published courses
        $courses = Course::where('status', 'published')
            ->with(['user', 'sections'])
            ->latest()
            ->paginate(12);

        // If user is authenticated, get their enrolled courses
        $enrolledCourseIds = [];
        if (Auth::check()) {
            $enrolledCourseIds = DB::table('course_user')
                ->where('user_id', Auth::id())
                ->pluck('course_id')
                ->toArray();
        }

        return view('courses.index', compact('courses', 'enrolledCourseIds'));
    }

    public function preview($id)
    {
        // Get course by ID
        $course = Course::findOrFail($id);

        // Check if course is published
        if ($course->status !== 'published') {
            abort(404);
        }

        // If user is enrolled, redirect to course view
        if (Auth::check()) {
            $enrollment = DB::table('course_user')
                ->where('user_id', Auth::id())
                ->where('course_id', $course->id)
                ->first();

            if ($enrollment) {
                return redirect()->route('courses.view', ['id' => $course->id]);
            }
        }

        // Get first 3 sections for preview
        $previewSections = $course->sections()
            ->select('id', 'title', 'order')
            ->orderBy('order')
            ->take(3)
            ->get();

        // Load the total sections count for the course
        $course->loadCount('sections');

        return view('courses.preview', compact('course', 'previewSections'));
    }

    public function view($id)
    {
        // Get course by ID
        $course = Course::findOrFail($id);
        $userId = Auth::id();

        $enrollment = DB::table('course_user')
            ->where('user_id', $userId)
            ->where('course_id', $course->id)
            ->first();

        // If not enrolled, redirect to course preview
        if (!$enrollment) {
            return redirect()->route('courses.preview', ['id' => $course->id]);
        }

        $sections = CourseSection::where('course_id', $course->id)
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

    public function enroll($id)
    {
        try {
            DB::beginTransaction();

            // Get course by ID
            $course = Course::findOrFail($id);

            // Check if user is already enrolled
            $enrollment = DB::table('course_user')
                ->where('user_id', Auth::id())
                ->where('course_id', $course->id)
                ->first();

            if ($enrollment) {
                DB::rollBack();
                return redirect()->route('courses.view', ['id' => $course->id]);
            }

            // Enroll user in course
            DB::table('course_user')->insert([
                'user_id' => Auth::id(),
                'course_id' => $course->id,
                'enrolled_at' => now(),
                'completed' => false,
                'completed_sections' => '[]',
                'completed_sections_count' => 0,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            DB::commit();

            return redirect()->route('courses.view', ['id' => $course->id])
                ->with('success', 'Successfully enrolled in course!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error enrolling in course: ' . $e->getMessage());
            return back()->with('error', 'Error enrolling in course. Please try again.');
        }
    }

    public function completeSection($id, $sectionId)
    {
        try {
            DB::beginTransaction();

            // Get course and section
            $course = Course::findOrFail($id);
            $section = CourseSection::findOrFail($sectionId);

            // Get enrollment
            $enrollment = DB::table('course_user')
                ->where('user_id', Auth::id())
                ->where('course_id', $course->id)
                ->first();

            if (!$enrollment) {
                DB::rollBack();
                return back()->with('error', 'Not enrolled in this course.');
            }

            // Get completed sections array
            $completedSections = json_decode($enrollment->completed_sections ?? '[]', true);

            // Add section to completed sections if not already completed
            if (!in_array($sectionId, $completedSections)) {
                $completedSections[] = $sectionId;
                $completedSectionsCount = count($completedSections);

                // Update enrollment
                DB::table('course_user')
                    ->where('user_id', Auth::id())
                    ->where('course_id', $course->id)
                    ->update([
                        'completed_sections' => json_encode($completedSections),
                        'completed_sections_count' => $completedSectionsCount,
                        'current_section_id' => $sectionId
                    ]);

                // Check if all sections are completed
                $totalSections = $course->sections()->count();
                if ($completedSectionsCount >= $totalSections) {
                    DB::table('course_user')
                        ->where('user_id', Auth::id())
                        ->where('course_id', $course->id)
                        ->update([
                            'completed' => true,
                            'completed_at' => now()
                        ]);
                }
            }

            DB::commit();

            return back()->with('success', 'Section completed!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error completing section: ' . $e->getMessage());
            return back()->with('error', 'Error completing section. Please try again.');
        }
    }

    public function updateCurrentSection($id, $sectionId)
    {
        try {
            DB::beginTransaction();

            // Get course and section
            $course = Course::findOrFail($id);
            $section = CourseSection::findOrFail($sectionId);

            // Update current section
            DB::table('course_user')
                ->where('user_id', Auth::id())
                ->where('course_id', $course->id)
                ->update([
                    'current_section_id' => $sectionId
                ]);

            DB::commit();

            return back()->with('success', 'Current section updated.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating current section: ' . $e->getMessage());
            return back()->with('error', 'Error updating current section. Please try again.');
        }
    }

    public function nextSection($id, $sectionId)
    {
        try {
            DB::beginTransaction();

            // Get course and current section
            $course = Course::findOrFail($id);
            $currentSection = CourseSection::findOrFail($sectionId);

            // Get next section
            $nextSection = CourseSection::where('course_id', $course->id)
                ->where('order', '>', $currentSection->order)
                ->orderBy('order')
                ->first();

            if (!$nextSection) {
                DB::rollBack();
                return back()->with('error', 'No next section available.');
            }

            // Update current section
            DB::table('course_user')
                ->where('user_id', Auth::id())
                ->where('course_id', $course->id)
                ->update([
                    'current_section_id' => $nextSection->id
                ]);

            DB::commit();

            return back()->with('success', 'Moved to next section.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error moving to next section: ' . $e->getMessage());
            return back()->with('error', 'Error moving to next section. Please try again.');
        }
    }

    public function previousSection($id, $sectionId)
    {
        try {
            DB::beginTransaction();

            // Get course and current section
            $course = Course::findOrFail($id);
            $currentSection = CourseSection::findOrFail($sectionId);

            // Get previous section
            $previousSection = CourseSection::where('course_id', $course->id)
                ->where('order', '<', $currentSection->order)
                ->orderByDesc('order')
                ->first();

            if (!$previousSection) {
                DB::rollBack();
                return back()->with('error', 'No previous section available.');
            }

            // Update current section
            DB::table('course_user')
                ->where('user_id', Auth::id())
                ->where('course_id', $course->id)
                ->update([
                    'current_section_id' => $previousSection->id
                ]);

            DB::commit();

            return back()->with('success', 'Moved to previous section.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error moving to previous section: ' . $e->getMessage());
            return back()->with('error', 'Error moving to previous section. Please try again.');
        }
    }

    public function showSection($sectionId)
    {
        try {
            $section = CourseSection::findOrFail($sectionId);
            $course = $section->course;

            // Check if user is enrolled
            $enrollment = DB::table('course_user')
                ->where('user_id', Auth::id())
                ->where('course_id', $course->id)
                ->first();

            if (!$enrollment) {
                return response()->json(['error' => 'Not enrolled in this course.'], 403);
            }

            // Update current section
            DB::table('course_user')
                ->where('user_id', Auth::id())
                ->where('course_id', $course->id)
                ->update([
                    'current_section_id' => $sectionId
                ]);

            return response()->json([
                'section' => $section,
                'content' => json_decode($section->content)
            ]);

        } catch (\Exception $e) {
            Log::error('Error showing section: ' . $e->getMessage());
            return response()->json(['error' => 'Error showing section.'], 500);
        }
    }

    public function markCurrentSection($sectionId)
    {
        try {
            $section = CourseSection::findOrFail($sectionId);
            $course = $section->course;

            // Update current section
            DB::table('course_user')
                ->where('user_id', Auth::id())
                ->where('course_id', $course->id)
                ->update([
                    'current_section_id' => $sectionId
                ]);

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            Log::error('Error marking current section: ' . $e->getMessage());
            return response()->json(['error' => 'Error marking current section.'], 500);
        }
    }

    public function completeCourse($id)
    {
        try {
            DB::beginTransaction();

            // Get course
            $course = Course::findOrFail($id);

            // Get enrollment
            $enrollment = DB::table('course_user')
                ->where('user_id', Auth::id())
                ->where('course_id', $course->id)
                ->first();

            if (!$enrollment) {
                DB::rollBack();
                return back()->with('error', 'Not enrolled in this course.');
            }

            // Verify all sections are completed
            $sections = CourseSection::where('course_id', $course->id)->count();
            $completedSections = count(json_decode($enrollment->completed_sections ?? '[]'));

            if ($completedSections < $sections) {
                DB::rollBack();
                return back()->with('error', 'Please complete all sections before completing the course.');
            }

            // Update enrollment to mark course as completed
            DB::table('course_user')
                ->where('user_id', Auth::id())
                ->where('course_id', $course->id)
                ->update([
                    'completed' => true,
                    'completed_at' => now()
                ]);

            DB::commit();

            return redirect()->route('dashboard')
                ->with('success', 'Congratulations! You have completed the course.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error completing course: ' . $e->getMessage());
            return back()->with('error', 'Error completing course. Please try again.');
        }
    }
}