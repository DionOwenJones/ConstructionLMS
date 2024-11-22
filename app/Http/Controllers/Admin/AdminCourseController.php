<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Section;
use App\Models\ContentBlock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

class AdminCourseController extends Controller
{
    public function index()
    {
        $courses = Course::with('user')
            ->latest()
            ->paginate(10);
        return view('admin.courses.index', compact('courses'));
    }

    public function create()
    {
        return view('admin.courses.create');
    }

    protected function handleSectionContent($section, $request, $index)
    {
        // Get all blocks for this section
        $blocks = collect($request->input("sections.{$index}.blocks", []))
            ->map(function ($block, $blockIndex) use ($request, $index) {
                \Illuminate\Support\Facades\Log::info('Processing block:', [
                    'block' => $block,
                    'index' => $blockIndex
                ]);

                $type = $block['type'] ?? null;
                
                if (!$type) {
                    return null;
                }

                switch($type) {
                    case 'text':
                        return [
                            'type' => 'text',
                            'text_content' => $block['text_content'] ?? '',
                            'order' => $blockIndex
                        ];

                    case 'video':
                        return [
                            'type' => 'video',
                            'video_url' => $block['video_url'] ?? '',
                            'video_title' => $block['video_title'] ?? '',
                            'order' => $blockIndex
                        ];

                    case 'image':
                        if ($request->hasFile("sections.{$index}.blocks.{$blockIndex}.image_path")) {
                            $path = $request->file("sections.{$index}.blocks.{$blockIndex}.image_path")
                                ->store('section-images', 'public');
                            return [
                                'type' => 'image',
                                'image_path' => $path,
                                'order' => $blockIndex
                            ];
                        }
                        break;

                    case 'quiz':
                        $quizData = json_decode($block['quiz_data'] ?? '[]', true);
                        return [
                            'type' => 'quiz',
                            'quiz_data' => $quizData,
                            'order' => $blockIndex
                        ];
                }

                return null;
            })
            ->filter()
            ->values()
            ->all();

        \Illuminate\Support\Facades\Log::info('Section content processed:', ['blocks' => $blocks]);

        return $blocks;
    }

    protected function extractYouTubeId($url) {
        $pattern = '/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/i';
        if (preg_match($pattern, $url, $matches)) {
            return $matches[1];
        }
        return null;
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            // Validate basic course information
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'price' => 'required|numeric|min:0',
                'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            ]);

            // Handle image upload
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('courses', 'public');
                $validated['image'] = $imagePath;
            }

            // Generate a unique slug
            $baseSlug = Str::slug($validated['title']);
            $slug = $baseSlug;
            $counter = 1;

            while (Course::where('slug', $slug)->exists()) {
                $slug = $baseSlug . '-' . $counter;
                $counter++;
            }

            // Create course
            $course = Course::create([
                'title' => $validated['title'],
                'description' => $validated['description'],
                'price' => $validated['price'],
                'image' => $validated['image'],
                'user_id' => Auth::id(),
                'status' => 'draft',
                'slug' => $slug
            ]);

            // Handle sections if they exist
            if ($request->has('sections')) {
                foreach ($request->input('sections') as $index => $sectionData) {
                    if (empty($sectionData['title'])) {
                        continue;
                    }

                    \Illuminate\Support\Facades\Log::info('Processing section:', [
                        'index' => $index,
                        'data' => $sectionData
                    ]);
                    
                    $blocks = $this->handleSectionContent($sectionData, $request, $index);
                    \Illuminate\Support\Facades\Log::info('Section content processed:', ['blocks' => $blocks]);

                    // Create section
                    $section = $course->sections()->create([
                        'title' => $sectionData['title'],
                        'order' => $index + 1,
                        'content' => json_encode($blocks)
                    ]);

                    // Create content blocks
                    foreach ($blocks as $block) {
                        $blockData = [
                            'type' => $block['type'],
                            'order' => $block['order']
                        ];

                        // Add specific content based on type
                        switch ($block['type']) {
                            case 'text':
                                $blockData['text_content'] = $block['text_content'] ?? '';
                                break;
                            case 'video':
                                $blockData['video_url'] = $block['video_url'] ?? '';
                                $blockData['video_title'] = $block['video_title'] ?? '';
                                break;
                            case 'image':
                                $blockData['image_path'] = $block['image_path'] ?? '';
                                break;
                            case 'quiz':
                                $blockData['quiz_data'] = json_encode($block['quiz_data'] ?? []);
                                break;
                        }

                        $section->contentBlocks()->create($blockData);
                    }
                }
            }

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Course created successfully!',
                    'redirect' => route('admin.courses.index')
                ]);
            }

            return redirect()
                ->route('admin.courses.index')
                ->with('success', 'Course created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating course: ' . $e->getMessage(), [
                'exception' => $e,
                'request_data' => $request->all()
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error creating course: ' . $e->getMessage()
                ], 422);
            }

            return back()
                ->withInput()
                ->withErrors(['error' => 'Error creating course: ' . $e->getMessage()]);
        }
    }

    public function edit(Course $course)
    {
        $course->load('sections'); // Eager load sections
        return view('admin.courses.edit', compact('course'));
    }

    public function update(Request $request, Course $course)
    {
        try {
            DB::beginTransaction();

            // Validate basic course information
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'price' => 'required|numeric|min:0',
                'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            ]);

            // Handle image update if provided
            if ($request->hasFile('image')) {
                // Delete old image
                if ($course->image) {
                    Storage::disk('public')->delete($course->image);
                }
                $validated['image'] = $request->file('image')->store('courses', 'public');
            }

            // Update course
            $course->update([
                'title' => $validated['title'],
                'description' => $validated['description'],
                'price' => $validated['price'],
                'image' => $validated['image'] ?? $course->image,
            ]);

            // Handle sections if they exist
            if ($request->has('sections')) {
                // Delete old sections
                $course->sections()->delete();
                
                foreach ($request->input('sections') as $index => $sectionData) {
                    if (empty($sectionData['title'])) {
                        continue;
                    }

                    \Illuminate\Support\Facades\Log::info('Processing section for update:', [
                        'index' => $index,
                        'data' => $sectionData
                    ]);
                    
                    $blocks = $this->handleSectionContent($sectionData, $request, $index);
                    \Illuminate\Support\Facades\Log::info('Section content processed for update:', ['blocks' => $blocks]);

                    // Create section
                    $section = $course->sections()->create([
                        'title' => $sectionData['title'],
                        'order' => $index + 1,
                        'content' => json_encode($blocks)
                    ]);

                    // Create content blocks
                    foreach ($blocks as $block) {
                        $blockData = [
                            'type' => $block['type'],
                            'order' => $block['order']
                        ];

                        // Add specific content based on type
                        switch ($block['type']) {
                            case 'text':
                                $blockData['text_content'] = $block['text_content'] ?? '';
                                break;
                            case 'video':
                                $blockData['video_url'] = $block['video_url'] ?? '';
                                $blockData['video_title'] = $block['video_title'] ?? '';
                                break;
                            case 'image':
                                $blockData['image_path'] = $block['image_path'] ?? '';
                                break;
                            case 'quiz':
                                $blockData['quiz_data'] = json_encode($block['quiz_data'] ?? []);
                                break;
                        }

                        $section->contentBlocks()->create($blockData);
                    }
                }
            }

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Course updated successfully!',
                    'redirect' => route('admin.courses.index')
                ]);
            }

            return redirect()
                ->route('admin.courses.index')
                ->with('success', 'Course updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating course: ' . $e->getMessage(), [
                'exception' => $e,
                'request_data' => $request->all()
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error updating course: ' . $e->getMessage()
                ], 422);
            }

            return back()
                ->withInput()
                ->withErrors(['error' => 'Error updating course: ' . $e->getMessage()]);
        }
    }

    public function destroy(Course $course)
    {
        if ($course->image) {
            Storage::disk('public')->delete($course->image);
        }
        $course->delete();
        return redirect()->route('admin.courses.index')
            ->with('success', 'Course deleted successfully.');
    }

    public function preview(Course $course)
    {
        $course->load(['user', 'sections' => function ($query) {
            $query->orderBy('order');
        }]);

        // Get the first 3 sections for preview
        $previewSections = $course->sections->take(3);

        return view('courses.preview', compact('course', 'previewSections'));
    }

    public function publish(Course $course)
    {
        try {
            DB::beginTransaction();

            $course->update([
                'status' => 'published',
                'published_at' => now()
            ]);

            DB::commit();

            return redirect()
                ->route('admin.courses.index')
                ->with('success', 'Course has been published successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error publishing course: ' . $e->getMessage());
            return back()->with('error', 'Error publishing course: ' . $e->getMessage());
        }
    }

    public function unpublish(Course $course)
    {
        try {
            DB::beginTransaction();

            $course->update([
                'status' => 'draft',
                'published_at' => null
            ]);

            DB::commit();

            return redirect()
                ->route('admin.courses.index')
                ->with('success', 'Course has been unpublished successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error unpublishing course: ' . $e->getMessage());
        }
    }


    public function courses()
    {
        $courses = Course::with(['purchases' => function($query) {
            $query->withCount('allocations');
        }])
        ->withCount(['purchases', 'sections'])
        ->latest()
        ->paginate(10);

        return view('admin.courses.index', compact('courses'));
    }

    public function show(Course $course)
    {
        $course->load(['sections', 'purchases.allocations']);

        return view('admin.courses.show', compact('course'));
    }

    public function dashboard()
    {
        // Get recent courses
        $recentCourses = Course::with('user')
            ->latest()
            ->take(5)
            ->get();

        // Get recent users
        $recentUsers = \App\Models\User::latest()
            ->take(5)
            ->get();

        // Calculate total revenue (from course purchases)
        $totalRevenue = \App\Models\Course::sum('price');

        // Get statistics
        $stats = [
            'total_courses' => Course::count(),
            'total_users' => \App\Models\User::count(),
            'total_revenue' => $totalRevenue,
            'recent_courses' => $recentCourses,
            'recent_users' => $recentUsers
        ];

        // Get all courses for the courses table
        $courses = Course::withCount(['purchases', 'allocations'])
            ->latest()
            ->paginate(10);

        return view('admin.dashboard', compact('courses', 'stats'));
    }
}
