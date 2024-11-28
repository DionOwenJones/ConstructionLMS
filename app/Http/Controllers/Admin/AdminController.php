<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\User;
use App\Models\CoursePurchase;
use App\Models\BusinessCoursePurchase;
use App\Models\SectionContentBlock;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function dashboard()
    {
        // Calculate total revenue from individual course purchases
        $individualRevenue = CoursePurchase::where('status', 'completed')
            ->sum('amount_paid');

        // Calculate total revenue from business course purchases
        $businessRevenue = BusinessCoursePurchase::sum('total_amount');

        // Total revenue from both individual and business purchases
        $totalRevenue = $individualRevenue + $businessRevenue;

        // Get monthly revenue for the chart
        $monthlyRevenue = DB::table('course_purchases')
            ->select(DB::raw('DATE_FORMAT(purchased_at, "%Y-%m") as month'), DB::raw('SUM(amount_paid) as revenue'))
            ->where('status', 'completed')
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->take(12)
            ->get()
            ->reverse();

        // Get business monthly revenue
        $businessMonthlyRevenue = DB::table('business_course_purchases')
            ->select(DB::raw('DATE_FORMAT(purchased_at, "%Y-%m") as month'), DB::raw('SUM(total_amount) as revenue'))
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->take(12)
            ->get()
            ->reverse();

        // Combine individual and business monthly revenue
        $combinedMonthlyRevenue = collect();
        foreach ($monthlyRevenue as $month) {
            $businessRevForMonth = $businessMonthlyRevenue->firstWhere('month', $month->month);
            $combinedMonthlyRevenue->push([
                'month' => $month->month,
                'revenue' => $month->revenue + ($businessRevForMonth ? $businessRevForMonth->revenue : 0)
            ]);
        }

        $stats = [
            'total_users' => User::count(),
            'total_courses' => Course::count(),
            'total_revenue' => number_format($totalRevenue, 2),
            'recent_users' => User::latest()->take(5)->get(),
            'recent_courses' => Course::with('user')->latest()->take(5)->get(),
            'popular_courses' => Course::withCount('users')
                ->orderByDesc('users_count')
                ->take(5)
                ->get(),
            'monthly_revenue' => $combinedMonthlyRevenue,
            'individual_revenue' => number_format($individualRevenue, 2),
            'business_revenue' => number_format($businessRevenue, 2)
        ];

        return view('admin.dashboard', compact('stats'));
    }

    public function reports()
    {
        return view('admin.reports.index');
    }

    public function profile()
    {
        return view('admin.profile');
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
        ]);

        $user->update($validated);

        return redirect()->route('admin.profile')->with('success', 'Profile updated successfully.');
    }

    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required|current_password',
            'password' => 'required|string|min:8|confirmed',
        ]);

        auth()->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('admin.profile')->with('success', 'Password updated successfully.');
    }

    public function revenueReport()
    {
        // Get monthly revenue data for the past 12 months
        $monthlyRevenue = DB::table('course_purchases')
            ->select(DB::raw('DATE_FORMAT(purchased_at, "%Y-%m") as month'), DB::raw('SUM(amount_paid) as revenue'))
            ->where('status', 'completed')
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->take(12)
            ->get();

        // Get business monthly revenue
        $businessMonthlyRevenue = DB::table('business_course_purchases')
            ->select(DB::raw('DATE_FORMAT(purchased_at, "%Y-%m") as month'), DB::raw('SUM(total_amount) as revenue'))
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->take(12)
            ->get();

        // Combine individual and business revenue
        $combinedRevenue = collect();
        foreach ($monthlyRevenue as $month) {
            $businessRev = $businessMonthlyRevenue->firstWhere('month', $month->month);
            $combinedRevenue->push([
                'month' => $month->month,
                'individual_revenue' => $month->revenue,
                'business_revenue' => $businessRev ? $businessRev->revenue : 0,
                'total_revenue' => $month->revenue + ($businessRev ? $businessRev->revenue : 0)
            ]);
        }

        return view('admin.reports.revenue', [
            'revenueData' => $combinedRevenue,
            'totalRevenue' => $combinedRevenue->sum('total_revenue'),
            'individualRevenue' => $combinedRevenue->sum('individual_revenue'),
            'businessRevenue' => $combinedRevenue->sum('business_revenue')
        ]);
    }

    public function usersReport()
    {
        // Get users with course counts
        $users = User::withCount(['coursePurchases', 'courseAllocations'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Calculate user statistics
        $userStats = [
            'total' => User::count(),
            'new_this_month' => User::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
            'active' => User::whereHas('coursePurchases')
                ->orWhereHas('courseAllocations')
                ->count(),
            'roles' => User::select('role', DB::raw('count(*) as count'))
                ->groupBy('role')
                ->get()
        ];

        return view('admin.reports.users', compact('users', 'userStats'));
    }

    public function coursesReport()
    {
        $courses = Course::withCount(['users', 'purchases', 'businessPurchases'])
            ->with(['user'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $courseStats = [
            'total' => Course::count(),
            'total_revenue' => Course::join('course_purchases', 'courses.id', '=', 'course_purchases.course_id')
                ->where('course_purchases.status', 'completed')
                ->sum('course_purchases.amount_paid'),
            'most_popular' => Course::withCount('users')->orderByDesc('users_count')->take(5)->get(),
            'recent' => Course::latest()->take(5)->get()
        ];

        return view('admin.reports.courses', compact('courses', 'courseStats'));
    }

    /**
     * Delete a content block
     */
    public function deleteContentBlock(\App\Models\SectionContentBlock $contentBlock)
    {
        $contentBlock->delete();
        return back()->with('success', 'Content block deleted successfully');
    }
}
