<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index()
    {
        // Get featured published courses
        $featuredCourses = Course::where('status', 'published')
            ->where('featured', true)  // Only get featured courses
            ->with(['user', 'sections'])
            ->latest()
            ->take(3)
            ->get();

        // If no featured courses, get latest courses
        if ($featuredCourses->isEmpty()) {
            $featuredCourses = Course::where('status', 'published')
                ->with(['user', 'sections'])
                ->latest()
                ->take(3)
                ->get();
        }

        // Get statistics
        $totalCourses = Course::where('status', 'published')->count();
        $totalStudents = DB::table('course_user')->distinct('user_id')->count('user_id');
        $totalInstructors = User::whereHas('courses', function($query) {
            $query->where('status', 'published');
        })->count();

        // If user is authenticated, get their enrolled courses using direct DB query
        $enrolledCourseIds = [];
        if (Auth::check()) {
            $enrolledCourseIds = DB::table('course_user')
                ->where('user_id', Auth::id())
                ->pluck('course_id')
                ->toArray();
        }

        return view('welcome', compact(
            'featuredCourses',
            'enrolledCourseIds',
            'totalCourses',
            'totalStudents',
            'totalInstructors'
        ));
    }
}