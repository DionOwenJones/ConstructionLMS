<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\User;

class AdminController extends Controller
{
    public function dashboard()
    {
        $totalRevenue = Course::join('course_user', 'courses.id', '=', 'course_user.course_id')
            ->sum('courses.price');

        $stats = [
            'total_users' => User::count(),
            'total_courses' => Course::count(),
            'total_revenue' => $totalRevenue,
            'recent_users' => User::latest()->take(5)->get(),
            'recent_courses' => Course::with('user')->latest()->take(5)->get(),
            'popular_courses' => Course::withCount('students')
                ->orderByDesc('students_count')
                ->take(5)
                ->get(),
        ];

        return view('admin.dashboard', compact('stats'));
    }
}
