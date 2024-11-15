<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $featuredCourses = Course::where('status', 'published')
            ->whereNotNull('published_at')
            ->latest('published_at')
            ->take(6)
            ->get();

        return view('welcome', compact('featuredCourses'));
    }
}
