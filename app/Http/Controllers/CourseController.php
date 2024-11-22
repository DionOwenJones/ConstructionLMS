<?php
/**
 * Course Management Controller
 * 
 * This controller handles all course-related operations for regular users including:
 * - Course listing and viewing
 * - Course enrollment and progress tracking
 * - Course content access and completion
 */

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseSection;
use App\Models\Certificate;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;

class CourseController extends Controller
{
    /**
     * Display a listing of published courses
     * 
     * Shows all published courses with section counts and purchase status
     * for the authenticated user
     */
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

    /**
     * Display a course preview
     * 
     * Shows a preview of the course content including the first two sections
     * and upcoming sections. Handles access control and navigation.
     * 
     * @param Course $course The course to preview
     */
    public function preview(Course $course)
    {
        // Check if course is published
        if ($course->status !== 'published') {
            abort(404);
        }

        // If user has purchased, redirect to course view
        if (Auth::check() && $course->isPurchasedBy(Auth::user())) {
            $firstSection = $course->sections()->with('contentBlocks')->orderBy('order')->first();
            if ($firstSection) {
                return redirect()->route('courses.show.section', [
                    'course' => $course->id,
                    'section' => $firstSection->id
                ]);
            }
            return redirect()->route('courses.show', ['course' => $course->id]);
        }

        // Get first 2 sections for preview with full content
        $previewSections = $course->sections()
            ->with('contentBlocks')
            ->orderBy('order')
            ->take(2)
            ->get();

        // Get next sections (3rd onwards) for preview titles only
        $upcomingSections = $course->sections()
            ->select('id', 'title', 'order')
            ->orderBy('order')
            ->skip(2)
            ->take(3)
            ->get();

        return view('courses.public-preview', compact('course', 'previewSections', 'upcomingSections'));
    }

    /**
     * Display a course section preview
     * 
     * Shows a preview of a specific section in the course. Handles access control
     * and navigation between sections.
     * 
     * @param Course $course The course to preview
     * @param CourseSection $section The section to preview
     */
    public function previewSection(Course $course, CourseSection $section)
    {
        // Check if course is published
        if ($course->status !== 'published') {
            abort(404);
        }

        $user = Auth::user();
        $isEnrolled = $course->isUserEnrolled($user);

        // Check if this section is one of the first two sections
        $previewableSectionIds = $course->sections()
            ->orderBy('order')
            ->take(2)
            ->pluck('id')
            ->toArray();

        if (!in_array($section->id, $previewableSectionIds)) {
            return redirect()->route('courses.preview', $course)
                ->with('error', 'This section is only available after purchase.');
        }

        // Get all sections for navigation
        $sections = $course->sections()
            ->orderBy('order')
            ->get();

        // For preview sections, we'll show 0% progress
        $progress = 0;
        // For preview, no sections are completed
        $completedSections = [];

        // Get previous and next sections for navigation
        $currentIndex = $sections->search(function($item) use ($section) {
            return $item->id === $section->id;
        });

        $previousSection = $currentIndex > 0 ? $sections[$currentIndex - 1] : null;
        $nextSection = ($currentIndex !== false && $currentIndex < $sections->count() - 1) 
            ? $sections[$currentIndex + 1] 
            : null;

        // Only allow navigation to preview sections
        if ($previousSection && !in_array($previousSection->id, $previewableSectionIds)) {
            $previousSection = null;
        }
        if ($nextSection && !in_array($nextSection->id, $previewableSectionIds)) {
            $nextSection = null;
        }

        $currentSection = $section->load('contentBlocks');

        return view('courses.show', compact(
            'course', 
            'section', 
            'sections', 
            'progress', 
            'previousSection', 
            'nextSection',
            'currentSection',
            'completedSections',
            'isEnrolled',
            'currentIndex'
        ));
    }

    /**
     * Display a course and its content
     * 
     * Shows the course content including sections, progress tracking,
     * and navigation between sections. Handles access control and
     * progress updates.
     * 
     * @param Course $course The course to display
     * @param mixed $section Optional specific section to display
     */
    public function show(Course $course, $section = null)
    {
        $user = Auth::user();
        $isEnrolled = $course->isUserEnrolled($user);

        // Get accessible sections for this user
        $sections = $course->getAccessibleSections($user)->get();

        // If a specific section is requested
        if ($section) {
            $currentSection = $course->sections()->find($section);
            
            // Check if user can access this section
            if (!$currentSection || 
                (!$isEnrolled && $sections->where('id', $currentSection->id)->isEmpty())) {
                return redirect()->route('courses.preview', $course)
                    ->with('error', 'This section is only available after enrollment.');
            }
        } else {
            $currentSection = $sections->first();
        }

        // For enrolled users, get progress
        $progress = 0;
        $completedSections = [];
        if ($isEnrolled) {
            $courseUser = $course->users()->where('user_id', $user->id)->first();
            if ($courseUser) {
                $progress = $courseUser->pivot->completed_sections_count / $course->sections()->count() * 100;
                $completedSections = json_decode($courseUser->pivot->completed_sections ?? '[]', true) ?? [];
            }
        }

        // Get navigation sections
        $currentIndex = $sections->search(function($item) use ($currentSection) {
            return $item->id === $currentSection->id;
        });

        $previousSection = $currentIndex > 0 ? $sections[$currentIndex - 1] : null;
        $nextSection = ($currentIndex !== false && $currentIndex < $sections->count() - 1) 
            ? $sections[$currentIndex + 1] 
            : null;

        // Load content blocks for current section
        if ($currentSection) {
            $currentSection->load('contentBlocks');
        }

        return view('courses.show', compact(
            'course',
            'sections',
            'currentSection',
            'progress',
            'previousSection',
            'nextSection',
            'completedSections',
            'isEnrolled',
            'currentIndex'
        ));
    }

    /**
     * Display a specific section of a course.
     */
    public function showSection(Course $course, CourseSection $section)
    {
        $user = auth()->user();
        $isEnrolled = $course->isUserEnrolled($user);

        // Get all sections
        $sections = $course->sections()->orderBy('order')->get();

        // For enrolled users, get progress
        $progress = 0;
        $completedSections = [];
        if ($isEnrolled) {
            $courseUser = $course->users()->where('user_id', $user->id)->first();
            if ($courseUser) {
                $progress = $courseUser->pivot->completed_sections_count / $course->sections()->count() * 100;
                $completedSections = json_decode($courseUser->pivot->completed_sections ?? '[]', true) ?? [];
            }
        }

        // Get previous and next sections for navigation
        $currentIndex = $sections->search(function($item) use ($section) {
            return $item->id === $section->id;
        });
        $previousSection = $currentIndex > 0 ? $sections[$currentIndex - 1] : null;
        $nextSection = ($currentIndex !== false && $currentIndex < $sections->count() - 1) 
            ? $sections[$currentIndex + 1] 
            : null;

        // Load content blocks for current section
        $currentSection = $section->load('contentBlocks');

        return view('courses.show', compact(
            'course',
            'sections',
            'currentSection',
            'progress',
            'previousSection',
            'nextSection',
            'completedSections',
            'isEnrolled',
            'currentIndex'
        ));
    }

    /**
     * Mark a section as complete for the current user
     * 
     * Updates the user's progress in the course by marking
     * the specified section as complete
     */
    public function completeSection(Course $course, CourseSection $section)
    {
        $user = auth()->user();
        
        // Check if user is enrolled
        if (!$course->isUserEnrolled($user)) {
            return redirect()->back()->with('error', 'You must be enrolled in this course to mark sections as complete.');
        }

        // Get the current completed sections
        $courseUser = $course->users()->where('user_id', $user->id)->first();
        $completedSections = json_decode($courseUser->pivot->completed_sections ?? '[]', true) ?? [];

        // Add the current section if not already completed
        if (!in_array($section->id, $completedSections)) {
            $completedSections[] = $section->id;
            
            // Update the pivot table
            $course->users()->updateExistingPivot($user->id, [
                'completed_sections' => json_encode($completedSections),
                'completed_sections_count' => count($completedSections)
            ]);
        }

        // Find the next section
        $sections = $course->sections()->orderBy('order')->get();
        $currentIndex = $sections->search(function($item) use ($section) {
            return $item->id === $section->id;
        });

        // If there is a next section, redirect to it
        if ($currentIndex !== false && $currentIndex < $sections->count() - 1) {
            $nextSection = $sections[$currentIndex + 1];
            return redirect()->route('courses.section', [
                'course' => $course,
                'section' => $nextSection
            ])->with('success', 'Section marked as complete!');
        }

        // If this was the last section, redirect back with success message
        return redirect()->back()->with('success', 'Section marked as complete! You have completed all sections.');
    }

    /**
     * Display the certificate for a completed course.
     */
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

            // Get completion date and certificate
            $completedAt = $courseUser->pivot->completed_at;
            $certificate = Certificate::where('user_id', Auth::id())
                ->where('course_id', $course->id)
                ->first();

            if (!$certificate) {
                // Create certificate if it doesn't exist
                $certificate = Certificate::create([
                    'user_id' => Auth::id(),
                    'course_id' => $course->id,
                    'certificate_number' => 'CERT-' . strtoupper(Str::random(10)),
                    'issued_at' => $completedAt
                ]);
            }

            // Generate certificate view
            $pdf = Pdf::loadView('certificates.course', [
                'course' => $course,
                'user' => Auth::user(),
                'completedAt' => $completedAt,
                'certificate' => $certificate
            ]);

            // Set paper size and orientation
            $pdf->setPaper('a4', 'landscape');

            // Return the PDF for download
            return $pdf->stream("certificate-{$course->slug}.pdf");

        } catch (\Exception $e) {
            Log::error('Error generating certificate: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'course_id' => $course->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->with('error', 'Failed to generate certificate. Please try again later.');
        }
    }

    /**
     * Mark the entire course as complete and generate certificate
     */
    public function completeCourse(Course $course)
    {
        try {
            $user = auth()->user();
            
            // Check if user is enrolled
            if (!$course->isUserEnrolled($user)) {
                return redirect()->back()->with('error', 'You must be enrolled in this course to complete it.');
            }

            // Check if all sections are completed
            $courseUser = $course->users()->where('user_id', $user->id)->first();
            $completedSections = json_decode($courseUser->pivot->completed_sections ?? '[]', true) ?? [];
            $totalSections = $course->sections()->count();

            if (count($completedSections) < $totalSections) {
                return redirect()->back()->with('error', 'You must complete all sections before completing the course.');
            }

            // Check if already completed
            if ($courseUser->pivot->completed) {
                return redirect()->route('courses.certificate.generate', ['course' => $course])
                    ->with('info', 'You have already completed this course.');
            }

            // Start database transaction
            DB::beginTransaction();

            try {
                // Mark the course as completed
                $course->users()->updateExistingPivot($user->id, [
                    'completed' => true,
                    'completed_at' => now()
                ]);

                DB::commit();

                return redirect()->route('courses.certificate.generate', ['course' => $course])
                    ->with('success', 'Congratulations! You have completed the course.');
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
        } catch (\Exception $e) {
            \Log::error('Error completing course: ' . $e->getMessage(), [
                'user_id' => auth()->id(),
                'course_id' => $course->id
            ]);

            return redirect()->back()->with('error', 'Failed to complete course. Please try again later.');
        }
    }

    /**
     * Generate and download a course completion certificate
     * 
     * Generates a PDF certificate for the user upon course completion
     * and returns it for download
     */
    public function downloadCertificate(Request $request, Course $course)
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

    /**
     * Display a listing of all courses for admin
     * 
     * Shows all courses for the admin user, including pagination
     */
    public function adminIndex()
    {
        $courses = Course::orderBy('created_at', 'desc')
            ->paginate(5); // Show 5 courses initially

        if (request()->ajax()) {
            return view('admin.courses.partials.course-list', compact('courses'));
        }

        return view('admin.courses.index', compact('courses'));
    }

    /**
     * Store a new course
     * 
     * Creates a new course with its sections and content blocks
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'has_expiry' => 'boolean',
            'validity_months' => 'required_if:has_expiry,1|integer|min:1',
            'sections' => 'required|array',
            'sections.*.title' => 'required|string|max:255',
            'sections.*.content_blocks' => 'required|array',
            'sections.*.content_blocks.*.type' => 'required|string|in:text,youtube,image',
            'sections.*.content_blocks.*.content' => 'required|array'
        ]);

        try {
            DB::beginTransaction();

            // Handle image upload
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('courses', 'public');
                $validatedData['image'] = $imagePath;
            }

            // Create the course
            $course = Course::create([
                'title' => $validatedData['title'],
                'description' => $validatedData['description'],
                'price' => $validatedData['price'],
                'image' => $validatedData['image'],
                'has_expiry' => $request->has('has_expiry'),
                'validity_months' => $request->has('has_expiry') ? $request->validity_months : null,
                'user_id' => auth()->id(),
                'status' => 'draft'
            ]);

            // Create sections and content blocks
            foreach ($request->sections as $sectionIndex => $sectionData) {
                // Create section
                $section = $course->sections()->create([
                    'title' => $sectionData['title'],
                    'order' => $sectionIndex
                ]);

                // Create content blocks
                foreach ($sectionData['content_blocks'] as $blockIndex => $blockData) {
                    $content = [];

                    // Process content based on block type
                    switch ($blockData['type']) {
                        case 'text':
                            $content = [
                                'text' => $blockData['content']['text']
                            ];
                            break;

                        case 'youtube':
                            $content = [
                                'video_id' => $blockData['content']['video_id']
                            ];
                            break;

                        case 'image':
                            // Handle image upload
                            if (isset($blockData['content']['image']) && $blockData['content']['image'] instanceof \Illuminate\Http\UploadedFile) {
                                $imagePath = $blockData['content']['image']->store('section-images', 'public');
                                $content = [
                                    'image_path' => $imagePath,
                                    'alt' => $blockData['content']['alt'] ?? '',
                                    'caption' => $blockData['content']['caption'] ?? ''
                                ];
                            }
                            break;
                    }

                    // Create content block
                    $section->contentBlocks()->create([
                        'type' => $blockData['type'],
                        'content' => $content,
                        'order' => $blockIndex
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Course created successfully',
                'course' => $course->load('sections.contentBlocks')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Course creation failed: ' . $e->getMessage(), [
                'user_id' => auth()->id(),
                'request_data' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to create course: ' . $e->getMessage()
            ], 500);
        }
    }
}