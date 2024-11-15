<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Get all required counts and recent data
        $stats = [
            'total_courses' => Course::count(),
            'total_users' => User::count(),
            'total_enrollments' => DB::table('course_user')->count(),
            'published_courses' => Course::where('status', 'published')->count(),
            'recent_courses' => Course::with('teacher') // Add teacher relationship if it exists
                                    ->latest()
                                    ->take(5)
                                    ->get(),
            'recent_users' => User::latest()
                                 ->take(5)
                                 ->get()
        ];

        return view('admin.dashboard', compact('stats'));
    }
}
