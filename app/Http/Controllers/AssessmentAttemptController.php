<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\AssessmentAttempt;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AssessmentAttemptController extends Controller
{
    public function start(Assessment $assessment)
    {
        // Check if user has remaining attempts
        $remainingAttempts = $assessment->getRemainingAttemptsForUser(Auth::id());
        if ($remainingAttempts <= 0) {
            return redirect()->back()
                ->with('error', 'You have used all your attempts for this assessment.');
        }

        // Get questions
        $questions = $assessment->questions;
        
        // Randomize questions if enabled
        if ($assessment->randomize_questions) {
            $questions = $questions->shuffle();
        }

        return view('assessments.attempt', compact('assessment', 'questions'));
    }

    public function submit(Request $request, Assessment $assessment)
    {
        // Validate submission time
        $startTime = Carbon::parse($request->input('start_time'));
        $timeTaken = $startTime->diffInMinutes(now());
        
        if ($timeTaken > $assessment->time_limit) {
            return redirect()->back()
                ->with('error', 'Assessment time limit exceeded.');
        }

        // Calculate score
        $totalPoints = 0;
        $earnedPoints = 0;
        $feedback = [];

        foreach ($request->input('answers', []) as $questionId => $answer) {
            $question = Question::find($questionId);
            $totalPoints += $question->points;

            if ($question->type === 'multiple_choice') {
                $correctAnswer = $question->correct_answer;
                $isCorrect = $answer == $correctAnswer;
                $points = $isCorrect ? $question->points : 0;
                $earnedPoints += $points;

                if ($assessment->show_feedback) {
                    $feedback[$questionId] = [
                        'correct' => $isCorrect,
                        'points' => $points,
                        'explanation' => $question->explanation,
                        'your_answer' => $answer,
                        'correct_answer' => $correctAnswer
                    ];
                }
            } elseif ($question->type === 'matching') {
                $correctPairs = json_decode($question->matching_pairs, true);
                $points = 0;
                $pairFeedback = [];

                foreach ($answer as $index => $userMatch) {
                    $isCorrect = $correctPairs[$index]['right'] === $userMatch;
                    if ($isCorrect) {
                        $points += $question->points / count($correctPairs);
                    }

                    if ($assessment->show_feedback) {
                        $pairFeedback[] = [
                            'left' => $correctPairs[$index]['left'],
                            'your_answer' => $userMatch,
                            'correct_answer' => $correctPairs[$index]['right'],
                            'correct' => $isCorrect
                        ];
                    }
                }

                $earnedPoints += round($points);

                if ($assessment->show_feedback) {
                    $feedback[$questionId] = [
                        'pairs' => $pairFeedback,
                        'points' => round($points),
                        'total_possible' => $question->points
                    ];
                }
            }
        }

        // Calculate percentage score
        $percentageScore = ($totalPoints > 0) ? ($earnedPoints / $totalPoints) * 100 : 0;
        $passed = $percentageScore >= $assessment->passing_score;

        // Create attempt record
        $attempt = AssessmentAttempt::create([
            'assessment_id' => $assessment->id,
            'user_id' => Auth::id(),
            'score' => $percentageScore,
            'passed' => $passed,
            'time_taken' => $timeTaken,
            'answers' => $request->input('answers'),
            'feedback' => $feedback
        ]);

        // Redirect to results page
        return redirect()->route('assessments.results', $attempt)
            ->with('feedback', $assessment->show_feedback ? $feedback : null);
    }

    public function results(AssessmentAttempt $attempt)
    {
        $this->authorize('view', $attempt);

        return view('assessments.results', [
            'attempt' => $attempt,
            'assessment' => $attempt->assessment,
            'feedback' => session('feedback')
        ]);
    }
}
