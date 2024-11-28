<?php

namespace App\Http\Controllers\Business;

/**
 * Business Management Controller
 * 
 * This controller handles all business-related operations including:
 * - Business dashboard and analytics
 * - Employee management
 * - Course allocation tracking
 * - Business profile management
 */


use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\BusinessEmployee;
use App\Models\Course;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BusinessController extends Controller
{
    /**
     * Display the business dashboard
     * 
     * Shows key metrics and analytics including:
     * - Recent course completions by employees
     * - Course progress overview
     * - Total employees and courses
     * - Completion rates
     */
    public function dashboard()
    {
        // Get the business for the current user
        $business = Auth::user()->getBusiness();
        
        // If no business exists, show the business setup form
        if (!$business) {
            return redirect()->route('business.setup')
                ->with('warning', 'Please set up your business profile first.');
        }
        
        // Get recent course completions by employees
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

        // Get course progress overview with completion rates
        $courseProgress = DB::table('courses')
            ->join('business_course_purchases', 'courses.id', '=', 'business_course_purchases.course_id')
            ->where('business_course_purchases.business_id', $business->id)
            ->select([
                'courses.id',
                'courses.title',
                DB::raw('COUNT(DISTINCT business_course_allocations.user_id) as total_enrolled'),
                DB::raw('SUM(CASE WHEN course_user.completed = 1 THEN 1 ELSE 0 END) as completed_count'),
                DB::raw('ROUND(SUM(CASE WHEN course_user.completed = 1 THEN 1 ELSE 0 END) * 100.0 / NULLIF(COUNT(DISTINCT business_course_allocations.user_id), 0), 2) as completion_rate')
            ])
            ->leftJoin('business_course_allocations', 'business_course_purchases.id', '=', 'business_course_allocations.business_course_purchase_id')
            ->leftJoin('course_user', function($join) {
                $join->on('courses.id', '=', 'course_user.course_id')
                    ->on('business_course_allocations.user_id', '=', 'course_user.user_id');
            })
            ->groupBy('courses.id', 'courses.title')
            ->get();

        // Calculate business statistics
        $totalEmployees = BusinessEmployee::where('business_id', $business->id)->count();
        $totalCourses = $business->coursePurchases()->distinct('course_id')->count();
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

    /**
     * Show the business setup form
     * 
     * Displays the form for creating or updating business profile
     * including company details and settings
     */
    public function setup()
    {
        $business = Auth::user()->getBusiness();
        return view('business.setup', compact('business'));
    }

    /**
     * Store or update business profile
     * 
     * Handles the creation or update of business information
     * including validation and file uploads
     */
    public function store(Request $request)
    {
        // Validate business information
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'contact_email' => 'required|email',
            'contact_phone' => 'required|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'business_number' => 'required|string|unique:businesses,business_number,' . 
                ($request->user()->business_id ?? 'NULL') . ',id'
        ]);

        try {
            DB::beginTransaction();

            // Handle logo upload if provided
            if ($request->hasFile('logo')) {
                $logoPath = $request->file('logo')->store('business-logos', 'public');
                $validated['logo'] = $logoPath;
            }

            // Create or update business profile
            $business = Business::updateOrCreate(
                ['id' => $request->user()->business_id],
                $validated
            );

            // Update user's business relationship
            $request->user()->update(['business_id' => $business->id]);

            DB::commit();

            return redirect()->route('business.dashboard')
                ->with('success', 'Business profile updated successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Business profile update failed: ' . $e->getMessage());
            
            return back()->withInput()
                ->with('error', 'Failed to update business profile: ' . $e->getMessage());
        }
    }

    /**
     * Show employee management interface
     * 
     * Displays list of employees with their course progress
     * and options for managing their access
     */
    public function employees()
    {
        $business = Auth::user()->getBusiness();
        
        if (!$business) {
            return redirect()->route('business.setup')
                ->with('warning', 'Please set up your business profile first.');
        }

        $employees = $business->employees()
            ->with(['user', 'courseAllocations.course'])
            ->paginate(10);

        return view('business.employees.index', compact('employees'));
    }

    /**
     * Add new employee to business
     * 
     * Creates a new employee account or links existing user
     * to the business with appropriate role assignments
     */
    public function addEmployee(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|exists:users,email',
            'role' => 'required|in:employee,manager'
        ]);

        try {
            DB::beginTransaction();

            $user = User::where('email', $validated['email'])->firstOrFail();
            $business = Auth::user()->getBusiness();

            // Create employee record
            BusinessEmployee::create([
                'business_id' => $business->id,
                'user_id' => $user->id,
                'role' => $validated['role']
            ]);

            DB::commit();

            return redirect()->route('business.employees')
                ->with('success', 'Employee added successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Adding employee failed: ' . $e->getMessage());
            
            return back()->withInput()
                ->with('error', 'Failed to add employee: ' . $e->getMessage());
        }
    }

    /**
     * Remove employee from business
     * 
     * Removes employee access to business courses while
     * preserving their course progress data
     */
    public function removeEmployee(BusinessEmployee $employee)
    {
        try {
            $employee->delete();
            return redirect()->route('business.employees')
                ->with('success', 'Employee removed successfully');
        } catch (\Exception $e) {
            Log::error('Removing employee failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to remove employee');
        }
    }

    /**
     * Show certificates interface
     * 
     * Displays list of employees with their completed courses
     * and options for managing their certificates
     */
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

    /**
     * Show the business profile page
     */
    public function profile()
    {
        $business = Auth::user()->getBusiness();
        
        if (!$business) {
            return redirect()->route('business.setup')
                ->with('warning', 'Please set up your business profile first.');
        }

        return view('business.profile.index', compact('business'));
    }

    /**
     * Update the business profile
     */
    public function updateProfile(Request $request)
    {
        $business = Auth::user()->getBusiness();
        
        if (!$business) {
            return redirect()->route('business.setup')
                ->with('error', 'Business profile not found.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'company_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'vat_number' => 'nullable|string|max:50'
        ]);

        try {
            $business->update($validated);
            return redirect()->route('business.profile')
                ->with('success', 'Business profile updated successfully.');
        } catch (\Exception $e) {
            Log::error('Error updating business profile: ' . $e->getMessage());
            return redirect()->route('business.profile')
                ->with('error', 'Failed to update business profile. Please try again.');
        }
    }

    /**
     * Display business analytics
     */
    public function analytics()
    {
        $user = Auth::user();
        $business = $user->getBusiness();
        
        if (!$business) {
            return redirect()->route('business.setup')
                ->with('warning', 'Please set up your business profile first.');
        }

        // Get course completion statistics
        $courseStats = DB::table('business_course_purchases')
            ->select(
                'business_course_purchases.course_id',
                'courses.title',
                DB::raw('COUNT(DISTINCT business_course_allocations.id) as total_allocations'),
                DB::raw('COUNT(DISTINCT course_user.id) as total_completions')
            )
            ->join('courses', 'business_course_purchases.course_id', '=', 'courses.id')
            ->leftJoin('business_course_allocations', 'business_course_purchases.id', '=', 'business_course_allocations.business_course_purchase_id')
            ->leftJoin('business_employees', 'business_course_allocations.business_employee_id', '=', 'business_employees.id')
            ->leftJoin('course_user', function($join) {
                $join->on('courses.id', '=', 'course_user.course_id')
                    ->on('business_employees.user_id', '=', 'course_user.user_id')
                    ->where('course_user.completed', '=', true);
            })
            ->where('business_course_purchases.business_id', $business->id)
            ->groupBy('business_course_purchases.course_id', 'courses.title')
            ->get();

        // Get employee engagement statistics
        $employeeStats = DB::table('business_employees')
            ->select(
                'business_employees.id',
                'users.name',
                DB::raw('COUNT(DISTINCT business_course_allocations.id) as courses_allocated'),
                DB::raw('COUNT(DISTINCT CASE WHEN course_user.completed = 1 THEN course_user.id END) as courses_completed')
            )
            ->join('users', 'business_employees.user_id', '=', 'users.id')
            ->leftJoin('business_course_allocations', 'business_employees.id', '=', 'business_course_allocations.business_employee_id')
            ->leftJoin('business_course_purchases', 'business_course_allocations.business_course_purchase_id', '=', 'business_course_purchases.id')
            ->leftJoin('course_user', function($join) {
                $join->on('business_course_purchases.course_id', '=', 'course_user.course_id')
                    ->on('business_employees.user_id', '=', 'course_user.user_id');
            })
            ->where('business_employees.business_id', $business->id)
            ->groupBy('business_employees.id', 'users.name')
            ->get();

        // Calculate overall statistics
        $totalEmployees = $business->employees()->count();
        $totalCourses = $business->coursePurchases()->count();
        $totalCompletions = DB::table('course_user')
            ->join('business_employees', function($join) use ($business) {
                $join->on('course_user.user_id', '=', 'business_employees.user_id')
                    ->where('business_employees.business_id', '=', $business->id);
            })
            ->where('completed', true)
            ->count();

        return view('business.analytics', compact(
            'courseStats',
            'employeeStats',
            'totalEmployees',
            'totalCourses',
            'totalCompletions'
        ));
    }
}