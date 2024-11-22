<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportsController extends Controller
{
    public function index()
    {
        return view('admin.reports.index');
    }

    public function revenue()
    {
        // Get monthly revenue data for the past 12 months
        $revenueData = Order::select(
            DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
            DB::raw('SUM(CASE WHEN user_type = "individual" THEN total_amount ELSE 0 END) as individual_revenue'),
            DB::raw('SUM(CASE WHEN user_type = "business" THEN total_amount ELSE 0 END) as business_revenue'),
            DB::raw('SUM(total_amount) as total_revenue')
        )
            ->where('created_at', '>=', Carbon::now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Calculate totals
        $totalRevenue = $revenueData->sum('total_revenue');
        $individualRevenue = $revenueData->sum('individual_revenue');
        $businessRevenue = $revenueData->sum('business_revenue');

        return view('admin.reports.revenue', compact(
            'revenueData',
            'totalRevenue',
            'individualRevenue',
            'businessRevenue'
        ));
    }

    public function users()
    {
        // Get user statistics
        $userStats = [
            'total' => User::count(),
            'new_this_month' => User::where('created_at', '>=', Carbon::now()->startOfMonth())->count(),
            'active' => User::where('last_login_at', '>=', Carbon::now()->subDays(30))->count(),
            'roles' => User::select('role', DB::raw('COUNT(*) as count'))
                ->groupBy('role')
                ->get()
        ];

        // Get paginated users with course counts
        $users = User::withCount(['coursePurchases', 'courseAllocations'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.reports.users', compact('userStats', 'users'));
    }

    public function courses()
    {
        // Get course statistics
        $totalCourses = Course::count();
        $activeCourses = Course::where('status', 'published')->count();
        $totalEnrollments = DB::table('course_user')->count();

        // Get popular courses with enrollment and completion data
        $popularCourses = Course::withCount(['enrollments', 'completions'])
            ->with('category')
            ->select('courses.*', DB::raw('(SELECT SUM(total_amount) FROM orders WHERE course_id = courses.id) as revenue'))
            ->orderBy('enrollments_count', 'desc')
            ->take(10)
            ->get()
            ->map(function ($course) {
                $course->completion_rate = $course->enrollments_count > 0
                    ? ($course->completions_count / $course->enrollments_count) * 100
                    : 0;
                return $course;
            });

        // Calculate average completion rate
        $averageCompletionRate = $popularCourses->avg('completion_rate');

        // Get course completion rates for chart
        $courseCompletionRates = $popularCourses->map(function ($course) {
            return [
                'title' => $course->title,
                'completion_rate' => $course->completion_rate
            ];
        });

        return view('admin.reports.courses', compact(
            'totalCourses',
            'activeCourses',
            'totalEnrollments',
            'averageCompletionRate',
            'popularCourses',
            'courseCompletionRates'
        ));
    }

    public function export(Request $request)
    {
        $type = $request->input('type', 'revenue');
        $format = $request->input('format', 'csv');

        switch ($type) {
            case 'revenue':
                return $this->exportRevenue($format);
            case 'users':
                return $this->exportUsers($format);
            case 'courses':
                return $this->exportCourses($format);
            default:
                return back()->with('error', 'Invalid report type');
        }
    }

    private function exportRevenue($format)
    {
        // Implementation for revenue export
        // TODO: Implement export functionality
    }

    private function exportUsers($format)
    {
        // Implementation for users export
        // TODO: Implement export functionality
    }

    private function exportCourses($format)
    {
        // Implementation for courses export
        // TODO: Implement export functionality
    }
}
