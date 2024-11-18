<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseSection;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Barryvdh\DomPDF\Facade\Pdf;

class CourseController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['index', 'preview']);
    }

    public function index()
    {
        // Get all published courses with sections count
        $courses = Course::where('status', 'published')
            ->withCount('sections')
            ->with(['user'])
            ->latest()
            ->paginate(12);

        // If user is authenticated, get their purchased courses
        $purchasedCourseIds = [];
        if (Auth::check()) {
            $purchasedCourseIds = DB::table('course_purchases')
                ->where('user_id', Auth::id())
                ->where('status', 'completed')
                ->pluck('course_id')
                ->toArray();
        }

        return view('courses.index', compact('courses', 'purchasedCourseIds'));
    }

    public function preview(Course $course)
    {
        // Check if course is published
        if ($course->status !== 'published') {
            abort(404);
        }

        // If user has purchased, redirect to course view
        if (Auth::check() && $course->isPurchasedBy(Auth::user())) {
            $firstSection = $course->sections()->orderBy('order')->first();
            if ($firstSection) {
                return redirect()->route('courses.show.section', [
                    'course' => $course->id,
                    'section' => $firstSection->id
                ]);
            }
            // If no sections, show empty course view
            return redirect()->route('courses.show', ['course' => $course->id]);
        }

        // Get first 3 sections for preview
        $previewSections = $course->sections()
            ->select('id', 'title', 'order')
            ->orderBy('order')
            ->take(3)
            ->get();

        return view('courses.preview', compact('course', 'previewSections'));
    }

    public function show(Course $course, $section = null)
    {
        // Check if user has purchased the course
        if (!$course->isPurchasedBy(Auth::user())) {
            return redirect()->route('courses.preview', $course)
                ->with('error', 'You need to purchase this course to access its content.');
        }

        // Get course sections and current progress
        $sections = $course->sections()
            ->orderBy('order')
            ->get();

        // Load the course user relationship with completed status
        $courseUser = $course->users()
            ->where('user_id', Auth::id())
            ->first();

        if (!$courseUser) {
            return redirect()->route('courses.preview', $course)
                ->with('error', 'Course enrollment not found.');
        }

        // Handle case where course has no sections
        if ($sections->isEmpty()) {
            return view('courses.show', [
                'course' => $course,
                'sections' => collect(),
                'currentSection' => null,
                'progress' => 0,
                'noSections' => true,
                'previousSection' => null,
                'nextSection' => null,
                'completedSections' => [],
                'isCompleted' => $courseUser->pivot->completed ?? false
            ]);
        }

        // If no section is specified, get the current section or first section
        if (!$section) {
            $currentSection = $course->getCurrentSectionForUser(Auth::user()) ?? $sections->first();
        } else {
            // Find the section by ID
            $currentSection = $sections->firstWhere('id', $section);
            if (!$currentSection) {
                abort(404);
            }

            // Update user's current section
            $course->users()->updateExistingPivot(Auth::id(), [
                'current_section_id' => $currentSection->id,
                'last_accessed_at' => now()
            ]);
        }

        // Get completed sections for the user
        $completedSections = $course->getCompletedSectionsForUser(Auth::user());
        
        // Calculate progress
        $totalSections = $sections->count();
        $completedCount = count($completedSections);
        $progress = $totalSections > 0 ? round(($completedCount / $totalSections) * 100) : 0;

        // Get previous and next sections
        $currentIndex = $sections->search(function($item) use ($currentSection) {
            return $item->id === $currentSection->id;
        });

        $previousSection = $currentIndex > 0 ? $sections[$currentIndex - 1] : null;
        $nextSection = $currentIndex < $sections->count() - 1 ? $sections[$currentIndex + 1] : null;

        return view('courses.show', [
            'course' => $course,
            'sections' => $sections,
            'currentSection' => $currentSection,
            'completedSections' => $completedSections,
            'progress' => $progress,
            'previousSection' => $previousSection,
            'nextSection' => $nextSection,
            'isCompleted' => $courseUser->pivot->completed ?? false
        ]);
    }

    public function completeSection(Request $request, Course $course, CourseSection $section)
    {
        try {
            // Verify the section belongs to this course
            if ($section->course_id !== $course->id) {
                throw new \Exception('Invalid section for this course.');
            }

            // Get user's completed sections
            $completedSections = $course->getCompletedSectionsForUser(Auth::user());
            
            // Add current section if not already completed
            if (!in_array($section->id, $completedSections)) {
                $completedSections[] = $section->id;
            }

            // Update the pivot table
            $course->users()->updateExistingPivot(Auth::id(), [
                'completed_sections' => json_encode($completedSections),
                'completed_sections_count' => count($completedSections)
            ]);

            // Calculate progress
            $totalSections = $course->sections()->count();
            $progress = $totalSections > 0 ? round((count($completedSections) / $totalSections) * 100) : 0;

            // Get next section if available
            $nextSection = $course->sections()
                ->where('order', '>', $section->order)
                ->orderBy('order')
                ->first();

            // Check if course is already marked as completed
            $isCompleted = $course->users()
                ->where('user_id', Auth::id())
                ->first()
                ->pivot
                ->completed ?? false;

            return response()->json([
                'success' => true,
                'progress' => $progress,
                'completed' => $isCompleted,
                'nextSection' => $nextSection ? route('courses.show.section', ['course' => $course->id, 'section' => $nextSection->id]) : null
            ]);
        } catch (\Exception $e) {
            Log::error('Error completing section: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'course_id' => $course->id,
                'section_id' => $section->id
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Failed to mark section as completed.'
            ], 500);
        }
    }

    public function completeCourse(Request $request, Course $course)
    {
        try {
            // Verify all sections are completed
            $completedSections = $course->getCompletedSectionsForUser(Auth::user());
            $totalSections = $course->sections()->count();
            
            if (count($completedSections) < $totalSections) {
                throw new \Exception('You must complete all sections before completing the course.');
            }

            // Update completion status
            $course->users()->updateExistingPivot(Auth::id(), [
                'completed' => true,
                'completed_at' => now()
            ]);

            // Return success response
            return response()->json([
                'success' => true,
                'message' => 'Course completed successfully! You can now download your certificate.',
                'certificateUrl' => route('courses.certificate', ['course' => $course])
            ]);

        } catch (\Exception $e) {
            Log::error('Error completing course: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'course_id' => $course->id
            ]);

            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function certificate(Request $request, Course $course)
    {
        try {
            // Check if user has purchased and completed the course
            $courseUser = $course->users()
                ->where('user_id', Auth::id())
                ->first();

            if (!$courseUser) {
                return back()->with('error', 'You have not purchased this course.');
            }

            if (!$courseUser->pivot->completed) {
                return back()->with('error', 'You have not completed this course yet.');
            }

            // Get completion date
            $completedAt = $courseUser->pivot->completed_at;

            // Generate certificate view
            $pdf = Pdf::loadView('certificates.course', [
                'course' => $course,
                'user' => Auth::user(),
                'completedAt' => $completedAt
            ]);

            // Set paper size and orientation
            $pdf->setPaper('a4', 'landscape');

            // Return the PDF for download
            return $pdf->download("certificate-{$course->slug}.pdf");
        } catch (\Exception $e) {
            Log::error('Error generating certificate: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'course_id' => $course->id
            ]);

            return back()->with('error', 'Failed to generate certificate. Please try again later.');
        }
    }
}