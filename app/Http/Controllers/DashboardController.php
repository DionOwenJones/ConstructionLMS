<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Course;
use App\Models\User;
use App\Models\Certificate;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Redirect based on user role
        if ($user->hasRole(User::ROLE_ADMIN)) {
            return redirect()->route('admin.dashboard');
        } elseif ($user->hasRole(User::ROLE_BUSINESS) && !$user->business_id) {
            // Only redirect to business dashboard if they are a business owner (not an employee)
            return redirect()->route('business.dashboard');
        }
        
        // For regular users and employees
        $enrolledCourses = Course::join('course_user', 'courses.id', '=', 'course_user.course_id')
            ->leftJoin('certificates', function($join) use ($user) {
                $join->on('courses.id', '=', 'certificates.course_id')
                    ->where('certificates.user_id', '=', $user->id);
            })
            ->where('course_user.user_id', $user->id)
            ->select([
                'courses.*',
                'course_user.completed',
                'course_user.completed_at',
                'course_user.completed_sections_count',
                'course_user.current_section_id',
                'certificates.id as certificate_id',
                'certificates.certificate_number'
            ])
            ->withCount('sections')
            ->get();

        return view('dashboard', compact('enrolledCourses'));
    }
}
