<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Models\Business;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class BusinessController extends Controller
{
    /**
     * Display business dashboard/overview
     */
    public function dashboard()
    {
        $business = Auth::user()->business;

        $totalEmployees = $business->employees()->count();
        $totalCourses = $business->coursePurchases()->count();

        // Get completed courses count with proper joins
        $completedCourses = DB::table('business_course_allocations as bca')
            ->join('business_employees as be', 'bca.business_employee_id', '=', 'be.id')
            ->join('business_course_purchases as bcp', 'bca.business_course_purchase_id', '=', 'bcp.id')
            ->where('be.business_id', $business->id)
            ->where('bca.completed', true)
            ->count();

        // Get recent completions with proper date handling
        $recentCompletions = DB::table('business_course_allocations as bca')
            ->join('business_employees as be', 'bca.business_employee_id', '=', 'be.id')
            ->join('users', 'be.user_id', '=', 'users.id')
            ->join('business_course_purchases as bcp', 'bca.business_course_purchase_id', '=', 'bcp.id')
            ->join('courses', 'bcp.course_id', '=', 'courses.id')
            ->where('be.business_id', $business->id)
            ->where('bca.completed', true)
            ->select([
                'users.name as employee_name',
                'courses.title as course_title',
                DB::raw('CAST(bca.completed_at AS DATETIME) as completed_at'),
                'be.id as employee_id',
                'courses.id as course_id'
            ])
            ->orderBy('bca.completed_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function($completion) {
                $completion->completed_at = \Carbon\Carbon::parse($completion->completed_at);
                return $completion;
            });

        return view('business.dashboard', compact(
            'business',
            'totalEmployees',
            'totalCourses',
            'completedCourses',
            'recentCompletions'
        ));
    }

    /**
     * Show the business profile/settings
     */
    public function profile()
    {
        $business = Auth::user()->business;
        return view('business.profile', compact('business'));
    }

    /**
     * Update business profile/settings
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'logo' => 'nullable|image|max:2048', // 2MB max
        ]);

        $business = Auth::user()->business;

        // Handle logo upload if provided
        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('business-logos', 'public');
            $validated['logo'] = $path;

            // Delete old logo if exists
            if ($business->logo) {
                Storage::disk('public')->delete($business->logo);
            }
        }

        $business->update($validated);

        return redirect()->route('business.profile')
            ->with('success', 'Business profile updated successfully');
    }

    /**
     * Show business analytics/reports
     */
    public function analytics()
    {
        $business = Auth::user()->business;

        // Get analytics data
        $monthlyCompletions = $business->courseAllocations()
            ->where('completed', true)
            ->whereYear('completed_at', now()->year)
            ->whereMonth('completed_at', now()->month)
            ->count();

        $courseProgress = $business->courseAllocations()
            ->with('course')
            ->select('course_id')
            ->selectRaw('COUNT(*) as total')
            ->selectRaw('SUM(CASE WHEN completed = 1 THEN 1 ELSE 0 END) as completed')
            ->groupBy('course_id')
            ->get();

        return view('business.analytics', compact(
            'business',
            'monthlyCompletions',
            'courseProgress'
        ));
    }
}
