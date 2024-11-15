<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function index()
    {
        $enrolledCourses = DB::table('courses')
            ->join('course_user', 'courses.id', '=', 'course_user.course_id')
            ->where('course_user.user_id', Auth::id())
            ->select([
                'courses.id',
                'courses.title',
                'courses.description',
                'courses.image',
                'course_user.completed',
                'course_user.completed_at',
                'course_user.completed_sections_count',
                DB::raw('(SELECT COUNT(*) FROM course_sections WHERE course_id = courses.id) as total_sections')
            ])
            ->get();

        return view('dashboard', compact('enrolledCourses'));
    }
}
