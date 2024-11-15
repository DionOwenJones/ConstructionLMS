<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Course;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Get enrolled courses with sections count
        $enrolledCourses = Course::join('course_user', 'courses.id', '=', 'course_user.course_id')
            ->where('course_user.user_id', $user->id)
            ->select([
                'courses.*',
                'course_user.completed',
                'course_user.completed_at',
                'course_user.completed_sections_count',
                'course_user.current_section_id'
            ])
            ->withCount('sections')
            ->get();

        return view('dashboard', [
            'enrolledCourses' => $enrolledCourses
        ]);
    }
}
