<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
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
        $type = $request->input("sections.{$index}.type");

        \Illuminate\Support\Facades\Log::info('Processing section content:', [
            'type' => $type,
            'data' => $request->input("sections.{$index}")
        ]);

        switch($type) {
            case 'text':
                return [
                    'type' => 'text',
                    'data' => [
                        'text' => $request->input("sections.{$index}.content")
                    ]
                ];

            case 'video':
                return [
                    'type' => 'video',
                    'data' => [
                        'video_url' => $request->input("sections.{$index}.content")
                    ]
                ];

            case 'image':
                if ($request->hasFile("sections.{$index}.content")) {
                    $path = $request->file("sections.{$index}.content")->store('section-images', 'public');
                    return [
                        'type' => 'image',
                        'data' => [
                            'image_path' => $path
                        ]
                    ];
                }
                break;

            case 'quiz':
                $questions = $request->input("sections.{$index}.quiz.questions", []);
                $answers = $request->input("sections.{$index}.quiz.answers", []);
                $correctAnswers = $request->input("sections.{$index}.quiz.correct", []);

                // Log the raw quiz data
                \Illuminate\Support\Facades\Log::info('Raw quiz data:', [
                    'questions' => $questions,
                    'answers' => $answers,
                    'correct_answers' => $correctAnswers
                ]);

                // Filter out any empty questions
                $validQuestions = array_filter($questions, function($question) {
                    return !empty(trim($question));
                });

                // Only include answers and correct answers for valid questions
                $validIndexes = array_keys($validQuestions);
                $validAnswers = array_intersect_key($answers, array_flip($validIndexes));
                $validCorrectAnswers = array_intersect_key($correctAnswers, array_flip($validIndexes));

                // Reindex arrays to ensure sequential keys
                $validQuestions = array_values($validQuestions);
                
                // Process answers to ensure exactly 4 options per question
                $processedAnswers = [];
                foreach ($validAnswers as $questionAnswers) {
                    // Filter out empty answers and take only the first 4
                    $filteredAnswers = array_filter($questionAnswers, function($answer) {
                        return !empty(trim($answer));
                    });
                    $processedAnswers[] = array_slice(array_values($filteredAnswers), 0, 4);
                }

                $validCorrectAnswers = array_map('intval', array_values($validCorrectAnswers));

                // Log the processed quiz data
                \Illuminate\Support\Facades\Log::info('Processed quiz data:', [
                    'questions' => $validQuestions,
                    'answers' => $processedAnswers,
                    'correct_answers' => $validCorrectAnswers
                ]);

                return [
                    'type' => 'quiz',
                    'data' => [
                        'questions' => $validQuestions,
                        'answers' => $processedAnswers,
                        'correct_answers' => $validCorrectAnswers
                    ]
                ];
        }

        return [
            'type' => $type,
            'data' => []
        ];
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

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

            // Create course
            $course = Course::create([
                'title' => $validated['title'],
                'description' => $validated['description'],
                'price' => $validated['price'],
                'image' => $validated['image'],
                'user_id' => Auth::id(),
                'status' => 'draft',
                'slug' => Str::slug($validated['title'])
            ]);

            // Handle sections if they exist
            if ($request->has('sections')) {
                foreach ($request->sections as $index => $sectionData) {
                    \Illuminate\Support\Facades\Log::info('Processing section:', ['index' => $index, 'data' => $sectionData]);
                    
                    $content = $this->handleSectionContent($sectionData, $request, $index);
                    \Illuminate\Support\Facades\Log::info('Section content processed:', ['content' => $content]);

                    $course->sections()->create([
                        'title' => $sectionData['title'],
                        'content' => json_encode($content),
                        'order' => $index + 1
                    ]);
                }
            }

            DB::commit();

            return redirect()
                ->route('admin.courses.index')
                ->with('success', 'Course created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating course: ' . $e->getMessage());
            return back()
                ->withInput()
                ->withErrors(['error' => 'Error creating course: ' . $e->getMessage()]);
        }
    }

    public function edit(Course $course)
    {
        return view('admin.courses.edit', compact('course'));
    }

    public function update(Request $request, Course $course)
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'description' => 'required',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'status' => 'required|in:draft,published'
        ]);

        $data = [
            'title' => $validated['title'],
            'slug' => Str::slug($validated['title']),
            'description' => $validated['description'],
            'price' => $validated['price'],
            'status' => $validated['status'],
        ];

        // Handle image update if provided
        if ($request->hasFile('image')) {
            // Delete old image
            if ($course->image) {
                Storage::disk('public')->delete($course->image);
            }
            $data['image'] = $request->file('image')->store('courses', 'public');
        }

        // Update published_at timestamp if publishing
        if ($validated['status'] === 'published' && !$course->published_at) {
            $data['published_at'] = now();
        }

        $course->update($data);

        // Handle content updates if needed
        if ($request->has('content')) {
            // Update course sections/content
            $course->sections()->delete(); // Remove old sections
            foreach ($request->content as $section) {
                $course->sections()->create([
                    'title' => $section['title'],
                    'description' => $section['description'],
                    'order' => $section['order'] ?? 0,
                ]);
            }
        }

        $message = $validated['status'] === 'published'
            ? 'Course updated and published!'
            : 'Course saved as draft.';

        return redirect()
            ->route('admin.courses.index')
            ->with('success', $message);
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

        return view('courses.preview', compact('course'));
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
