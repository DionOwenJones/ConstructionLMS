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
        // Get the business for the current user
        $business = Auth::user()->getBusiness();
        
        // If no business exists, show the business setup form
        if (!$business) {
            return redirect()->route('business.setup')
                ->with('warning', 'Please set up your business profile first.');
        }
        
        // Get recent course completions
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
                DB::raw('COUNT(DISTINCT business_employees.user_id) as total_enrolled'),
                DB::raw('SUM(CASE WHEN course_user.completed = 1 THEN 1 ELSE 0 END) as completed_count'),
                DB::raw('ROUND(SUM(CASE WHEN course_user.completed = 1 THEN 1 ELSE 0 END) * 100.0 / NULLIF(COUNT(DISTINCT business_employees.user_id), 0), 2) as completion_rate')
            ])
            ->leftJoin('business_employees', 'business_course_purchases.business_id', '=', 'business_employees.business_id')
            ->leftJoin('course_user', function($join) {
                $join->on('courses.id', '=', 'course_user.course_id')
                    ->on('business_employees.user_id', '=', 'course_user.user_id');
            })
            ->groupBy('courses.id', 'courses.title')
            ->get();

        // Get total employees count
        $totalEmployees = BusinessEmployee::where('business_id', $business->id)->count();

        // Get total courses count
        $totalCourses = $business->coursePurchases()->distinct('course_id')->count();

        // Get completed courses count
        $completedCourses = DB::table('course_user')
            ->join('business_employees', function($join) use ($business) {
                $join->on('course_user.user_id', '=', 'business_employees.user_id')
                    ->where('business_employees.business_id', '=', $business->id);
            })
            ->where('completed', true)
            ->count();

        return view('business.dashboard', compact(
            'business',
            'recentCompletions',
            'courseProgress',
            'totalEmployees',
            'totalCourses',
            'completedCourses'
        ));
    }

    public function certificates()
    {
        $business = Auth::user()->getBusiness();
        
        if (!$business) {
            return redirect()->route('business.setup')
                ->with('warning', 'Please set up your business profile first.');
        }
        
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
        $business = Auth::user()->getBusiness();
        
        if (!$business) {
            return redirect()->route('business.setup')
                ->with('warning', 'Please set up your business profile first.');
        }
        
        return view('business.profile.index', compact('business'));
    }

    public function updateProfile(Request $request)
    {
        $business = Auth::user()->getBusiness();
        
        if (!$business) {
            return redirect()->route('business.setup')
                ->with('warning', 'Please set up your business profile first.');
        }
        
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'contact_email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'notifications_enabled' => 'boolean',
        ]);

        // Set notifications_enabled to false if not present in request
        $validated['notifications_enabled'] = $request->has('notifications_enabled');

        $business->update($validated);

        return redirect()->route('business.profile')
            ->with('success', 'Business profile updated successfully');
    }

    public function analytics()
    {
        $business = Auth::user()->getBusiness();
        
        if (!$business) {
            return redirect()->route('business.setup')
                ->with('warning', 'Please set up your business profile first.');
        }
        
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