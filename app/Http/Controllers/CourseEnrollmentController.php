<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourseEnrollmentController extends Controller
{
    public function enroll(Course $course)
    {
        try {
            // Check if user is already enrolled
            if (Auth::user()->courses->contains($course)) {
                return redirect()->route('dashboard')
                    ->with('error', 'You are already enrolled in this course.');
            }

            // Create enrollment record
            CourseUser::create([
                'user_id' => Auth::id(),
                'course_id' => $course->id,
                'completed' => false
            ]);

            return redirect()->route('dashboard')
                ->with('success', 'Successfully enrolled in the course!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error enrolling in course: ' . $e->getMessage());
        }
    }
}
