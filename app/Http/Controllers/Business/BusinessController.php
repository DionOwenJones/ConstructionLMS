<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\BusinessEmployee;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BusinessController extends Controller
{
    public function dashboard()
    {
        $business = Business::where('user_id', Auth::id())->firstOrFail();
        
        // Get recent course completions with proper joins and selection
        $recentCompletions = DB::table('course_user')
            ->join('users', 'course_user.user_id', '=', 'users.id')
            ->join('courses', 'course_user.course_id', '=', 'courses.id')
            ->join('business_employees', function($join) use ($business) {
                $join->on('users.id', '=', 'business_employees.user_id')
                    ->where('business_employees.business_id', '=', $business->id);
            })
            ->where('course_user.completed', true)
            ->select([
                'users.name as employee_name',
                'courses.title as course_title',
                'course_user.completed_at',
                'business_employees.id as employee_id',
                'courses.id as course_id'
            ])
            ->orderBy('course_user.completed_at', 'desc')
            ->limit(5)
            ->get();

        // Get course progress overview
        $courseProgress = DB::table('courses')
            ->join('business_course_purchases', 'courses.id', '=', 'business_course_purchases.course_id')
            ->where('business_course_purchases.business_id', $business->id)
            ->select([
                'courses.id',
                'courses.title',
                DB::raw('COUNT(DISTINCT course_user.user_id) as total_count'),
                DB::raw('COUNT(DISTINCT CASE WHEN course_user.completed = 1 THEN course_user.user_id END) as completed_count'),
                DB::raw('COALESCE(AVG(CASE WHEN course_user.completed = 1 THEN 100 ELSE COALESCE(course_user.completed_sections_count, 0) END), 0) as average_progress')
            ])
            ->leftJoin('course_user', function($join) use ($business) {
                $join->on('courses.id', '=', 'course_user.course_id')
                    ->join('business_employees', 'course_user.user_id', '=', 'business_employees.user_id')
                    ->where('business_employees.business_id', '=', $business->id);
            })
            ->groupBy('courses.id', 'courses.title')
            ->get();

        // Get total completed courses
        $completedCourses = DB::table('course_user')
            ->join('business_employees', 'course_user.user_id', '=', 'business_employees.user_id')
            ->where('business_employees.business_id', $business->id)
            ->where('course_user.completed', true)
            ->count();

        return view('business.dashboard', [
            'business' => $business,
            'totalEmployees' => $business->employees()->count(),
            'totalCourses' => $business->countDistinctCourses(),
            'completedCourses' => $completedCourses,
            'recentCompletions' => $recentCompletions,
            'courseProgress' => $courseProgress,
        ]);
    }

    public function certificates()
    {
        $business = Business::where('user_id', Auth::id())->firstOrFail();
        
        $employees = $business->employees()
            ->with(['user' => function($query) {
                $query->select('id', 'name', 'email');
            }])
            ->paginate(10);

        // Load completed courses for each employee with proper selection
        foreach ($employees as $employee) {
            $employee->completedCourses = Course::join('course_user', 'courses.id', '=', 'course_user.course_id')
                ->where('course_user.user_id', $employee->user_id)
                ->where('course_user.completed', true)
                ->select([
                    'courses.*',
                    'course_user.completed_at',
                    'course_user.id as enrollment_id'
                ])
                ->get();
        }

        return view('business.dashboard.certificates', [
            'employees' => $employees,
        ]);
    }

    public function profile()
    {
        $business = Business::where('user_id', Auth::id())->firstOrFail();
        return view('business.profile', compact('business'));
    }

    public function update(Request $request)
    {
        $business = Business::where('user_id', Auth::id())->firstOrFail();
        
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
        ]);

        $business->update($validated);

        return redirect()->route('business.profile')->with('success', 'Business profile updated successfully.');
    }

    public function analytics()
    {
        $business = Business::where('user_id', Auth::id())->firstOrFail();
        
        $analytics = [
            'totalEmployees' => $business->employees()->count(),
            'totalCourses' => $business->countDistinctCourses(),
            'employeeProgress' => $business->employees()->with('user')->get()->map(function ($employee) {
                return [
                    'name' => $employee->user->name,
                    'completion' => $employee->courseCompletionPercentage(),
                ];
            }),
        ];

        return view('business.analytics', compact('analytics'));
    }
}