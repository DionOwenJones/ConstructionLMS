<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\Course;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AssessmentController extends Controller
{
    public function index(Course $course)
    {
        $assessments = $course->assessments()->with('questions')->get();
        return view('admin.assessments.index', compact('course', 'assessments'));
    }

    public function create(Course $course)
    {
        return view('admin.assessments.create', compact('course'));
    }

    public function store(Request $request, Course $course)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'time_limit' => 'nullable|integer|min:1',
            'passing_score' => 'required|integer|min:0|max:100',
            'randomize_questions' => 'boolean',
            'show_feedback' => 'boolean',
            'max_attempts' => 'required|integer|min:1',
            'questions' => 'required|array|min:1',
            'questions.*.type' => 'required|in:multiple_choice,essay,matching',
            'questions.*.question_text' => 'required|string',
            'questions.*.options' => 'required_if:questions.*.type,multiple_choice,matching|array',
            'questions.*.correct_answer' => 'required|array',
            'questions.*.feedback' => 'nullable|string',
            'questions.*.points' => 'required|integer|min:1'
        ]);

        try {
            DB::beginTransaction();

            $assessment = $course->assessments()->create([
                'title' => $validated['title'],
                'description' => $validated['description'],
                'time_limit' => $validated['time_limit'],
                'passing_score' => $validated['passing_score'],
                'randomize_questions' => $validated['randomize_questions'] ?? false,
                'show_feedback' => $validated['show_feedback'] ?? true,
                'max_attempts' => $validated['max_attempts']
            ]);

            foreach ($validated['questions'] as $index => $questionData) {
                $assessment->questions()->create([
                    'type' => $questionData['type'],
                    'question_text' => $questionData['question_text'],
                    'options' => $questionData['options'] ?? null,
                    'correct_answer' => $questionData['correct_answer'],
                    'feedback' => $questionData['feedback'],
                    'points' => $questionData['points'],
                    'order' => $index
                ]);
            }

            DB::commit();

            return redirect()
                ->route('admin.courses.assessments.index', $course)
                ->with('success', 'Assessment created successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Assessment creation failed: ' . $e->getMessage());
            
            return back()
                ->withInput()
                ->with('error', 'Failed to create assessment. Please try again.');
        }
    }

    public function edit(Course $course, Assessment $assessment)
    {
        $assessment->load('questions');
        return view('admin.assessments.edit', compact('course', 'assessment'));
    }

    public function update(Request $request, Course $course, Assessment $assessment)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'time_limit' => 'nullable|integer|min:1',
            'passing_score' => 'required|integer|min:0|max:100',
            'randomize_questions' => 'boolean',
            'show_feedback' => 'boolean',
            'max_attempts' => 'required|integer|min:1',
            'questions' => 'required|array|min:1',
            'questions.*.id' => 'nullable|exists:questions,id',
            'questions.*.type' => 'required|in:multiple_choice,essay,matching',
            'questions.*.question_text' => 'required|string',
            'questions.*.options' => 'required_if:questions.*.type,multiple_choice,matching|array',
            'questions.*.correct_answer' => 'required|array',
            'questions.*.feedback' => 'nullable|string',
            'questions.*.points' => 'required|integer|min:1'
        ]);

        try {
            DB::beginTransaction();

            $assessment->update([
                'title' => $validated['title'],
                'description' => $validated['description'],
                'time_limit' => $validated['time_limit'],
                'passing_score' => $validated['passing_score'],
                'randomize_questions' => $validated['randomize_questions'] ?? false,
                'show_feedback' => $validated['show_feedback'] ?? true,
                'max_attempts' => $validated['max_attempts']
            ]);

            // Delete questions that are not in the update
            $questionIds = collect($validated['questions'])
                ->pluck('id')
                ->filter()
                ->toArray();
            
            $assessment->questions()
                ->whereNotIn('id', $questionIds)
                ->delete();

            // Update or create questions
            foreach ($validated['questions'] as $index => $questionData) {
                $question = isset($questionData['id']) 
                    ? Question::find($questionData['id'])
                    : new Question();

                $question->fill([
                    'assessment_id' => $assessment->id,
                    'type' => $questionData['type'],
                    'question_text' => $questionData['question_text'],
                    'options' => $questionData['options'] ?? null,
                    'correct_answer' => $questionData['correct_answer'],
                    'feedback' => $questionData['feedback'],
                    'points' => $questionData['points'],
                    'order' => $index
                ])->save();
            }

            DB::commit();

            return redirect()
                ->route('admin.courses.assessments.index', $course)
                ->with('success', 'Assessment updated successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Assessment update failed: ' . $e->getMessage());
            
            return back()
                ->withInput()
                ->with('error', 'Failed to update assessment. Please try again.');
        }
    }

    public function destroy(Course $course, Assessment $assessment)
    {
        try {
            $assessment->delete();
            return redirect()
                ->route('admin.courses.assessments.index', $course)
                ->with('success', 'Assessment deleted successfully');
        } catch (\Exception $e) {
            Log::error('Assessment deletion failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to delete assessment. Please try again.');
        }
    }
}
