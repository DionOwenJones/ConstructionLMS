<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\Course;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AssessmentQuestionController extends Controller
{
    public function index(Course $course, Assessment $assessment)
    {
        $questions = $assessment->questions()->orderBy('order')->get();
        return view('admin.assessments.questions.index', compact('course', 'assessment', 'questions'));
    }

    public function create(Course $course, Assessment $assessment)
    {
        return view('admin.assessments.questions.create', compact('course', 'assessment'));
    }

    public function store(Request $request, Course $course, Assessment $assessment)
    {
        $validated = $request->validate([
            'type' => 'required|in:multiple_choice,essay,matching',
            'question_text' => 'required|string',
            'options' => 'required_if:type,multiple_choice,matching|array',
            'correct_answer' => 'required|array',
            'feedback' => 'nullable|string',
            'points' => 'required|integer|min:1',
        ]);

        // Get the last order number
        $lastOrder = $assessment->questions()->max('order') ?? 0;

        $question = $assessment->questions()->create([
            'type' => $validated['type'],
            'question_text' => $validated['question_text'],
            'options' => $validated['options'] ?? null,
            'correct_answer' => $validated['correct_answer'],
            'feedback' => $validated['feedback'],
            'points' => $validated['points'],
            'order' => $lastOrder + 1,
        ]);

        return redirect()
            ->route('admin.courses.assessments.questions.index', [$course, $assessment])
            ->with('success', 'Question added successfully');
    }

    public function edit(Course $course, Assessment $assessment, Question $question)
    {
        return view('admin.assessments.questions.edit', compact('course', 'assessment', 'question'));
    }

    public function update(Request $request, Course $course, Assessment $assessment, Question $question)
    {
        $validated = $request->validate([
            'type' => 'required|in:multiple_choice,essay,matching',
            'question_text' => 'required|string',
            'options' => 'required_if:type,multiple_choice,matching|array',
            'correct_answer' => 'required|array',
            'feedback' => 'nullable|string',
            'points' => 'required|integer|min:1',
        ]);

        $question->update([
            'type' => $validated['type'],
            'question_text' => $validated['question_text'],
            'options' => $validated['options'] ?? null,
            'correct_answer' => $validated['correct_answer'],
            'feedback' => $validated['feedback'],
            'points' => $validated['points'],
        ]);

        return redirect()
            ->route('admin.courses.assessments.questions.index', [$course, $assessment])
            ->with('success', 'Question updated successfully');
    }

    public function destroy(Course $course, Assessment $assessment, Question $question)
    {
        $question->delete();

        // Reorder remaining questions
        $assessment->questions()
            ->where('order', '>', $question->order)
            ->decrement('order');

        return redirect()
            ->route('admin.courses.assessments.questions.index', [$course, $assessment])
            ->with('success', 'Question deleted successfully');
    }
}
