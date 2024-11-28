<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Business;
use App\Models\User;
use App\Models\Order;
use App\Models\BusinessCourseAllocation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminReportController extends Controller
{
    public function index()
    {
        $stats = [
            'total_users' => User::count(),
            'total_businesses' => Business::count(),
            'total_courses' => Course::count(),
            'active_allocations' => BusinessCourseAllocation::where('expires_at', '>', now())->count(),
        ];

        return view('admin.reports.index', compact('stats'));
    }

    public function revenue()
    {
        // Get monthly revenue data for the past 12 months
        $revenueData = Order::select(
            DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
            DB::raw('SUM(total_amount) as total_revenue')
        )
            ->where('created_at', '>=', Carbon::now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Calculate total revenue
        $totalRevenue = $revenueData->sum('total_revenue');

        // Get course purchase trends
        $courseTrends = Order::select(
            'courses.id',
            'courses.title',
            DB::raw('DATE_FORMAT(orders.created_at, "%Y-%m") as month'),
            DB::raw('COUNT(*) as purchase_count'),
            DB::raw('SUM(orders.total_amount) as revenue')
        )
            ->join('courses', 'orders.course_id', '=', 'courses.id')
            ->where('orders.created_at', '>=', Carbon::now()->subMonths(12))
            ->groupBy('courses.id', 'courses.title', 'month')
            ->orderBy('month')
            ->get()
            ->groupBy('month');

        // Get top 5 courses by total purchases
        $topCourses = Order::select(
            'courses.id',
            'courses.title',
            DB::raw('COUNT(*) as total_purchases'),
            DB::raw('SUM(orders.total_amount) as total_revenue')
        )
            ->join('courses', 'orders.course_id', '=', 'courses.id')
            ->where('orders.created_at', '>=', Carbon::now()->subMonths(12))
            ->groupBy('courses.id', 'courses.title')
            ->orderByDesc('total_purchases')
            ->limit(5)
            ->get();

        return view('admin.reports.revenue', compact(
            'revenueData',
            'totalRevenue',
            'courseTrends',
            'topCourses'
        ));
    }

    public function users()
    {
        // Get user statistics
        $userStats = [
            'total' => User::count(),
            'new_this_month' => User::where('created_at', '>=', Carbon::now()->startOfMonth())->count(),
            'active' => User::where(function ($query) {
                $query->whereNotNull('last_login_at')
                    ->where('last_login_at', '>=', Carbon::now()->subDays(30))
                    ->orWhere('created_at', '>=', Carbon::now()->subDays(30));
            })->count(),
            'roles' => User::select('role', DB::raw('COUNT(*) as count'))
                ->groupBy('role')
                ->get()
        ];

        // Get paginated users with course counts
        $users = User::withCount(['courses'])
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
        $popularCourses = Course::select([
                'courses.*',
                DB::raw('(SELECT COUNT(*) FROM course_user WHERE course_id = courses.id) as enrollments_count'),
                DB::raw('(SELECT COUNT(*) FROM course_user WHERE course_id = courses.id AND completed = 1) as completions_count'),
                DB::raw('(SELECT SUM(total_amount) FROM orders WHERE course_id = courses.id) as revenue')
            ])
            ->orderBy(DB::raw('(SELECT COUNT(*) FROM course_user WHERE course_id = courses.id)'), 'desc')
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

        // Prepare data for completion rates chart
        $courseCompletionRates = $popularCourses->map(function ($course) {
            return [
                'title' => $course->title,
                'completion_rate' => round($course->completion_rate, 1)
            ];
        });

        return view('admin.reports.courses', compact(
            'totalCourses',
            'activeCourses',
            'totalEnrollments',
            'popularCourses',
            'averageCompletionRate',
            'courseCompletionRates'
        ));
    }

    public function generate(Request $request)
    {
        $request->validate([
            'type' => 'required|in:users,businesses,courses,allocations',
            'date_from' => 'required|date',
            'date_to' => 'required|date|after:date_from',
        ]);

        // Generate report based on type
        switch ($request->type) {
            case 'users':
                $data = User::whereBetween('created_at', [$request->date_from, $request->date_to])->get();
                break;
            case 'businesses':
                $data = Business::whereBetween('created_at', [$request->date_from, $request->date_to])->get();
                break;
            case 'courses':
                $data = Course::whereBetween('created_at', [$request->date_from, $request->date_to])->get();
                break;
            case 'allocations':
                $data = BusinessCourseAllocation::whereBetween('created_at', [$request->date_from, $request->date_to])->get();
                break;
        }

        return view('admin.reports.show', compact('data'));
    }

    public function export($type)
    {
        $data = [];
        $filename = '';

        switch ($type) {
            case 'users':
                $data = User::with(['courses'])->get()->map(function ($user) {
                    return [
                        'ID' => $user->id,
                        'Name' => $user->name,
                        'Email' => $user->email,
                        'Role' => $user->role,
                        'Courses Enrolled' => $user->courses->count(),
                        'Joined Date' => $user->created_at->format('Y-m-d'),
                    ];
                });
                $filename = 'users_report.csv';
                break;

            case 'courses':
                $data = Course::withCount(['users'])->get()->map(function ($course) {
                    return [
                        'ID' => $course->id,
                        'Title' => $course->title,
                        'Status' => $course->status,
                        'Enrollments' => $course->users_count,
                        'Price' => $course->price,
                        'Created Date' => $course->created_at->format('Y-m-d'),
                    ];
                });
                $filename = 'courses_report.csv';
                break;

            case 'revenue':
                $data = Order::with(['user', 'course'])->get()->map(function ($order) {
                    return [
                        'Order ID' => $order->id,
                        'User' => $order->user->name,
                        'Course' => $order->course->title,
                        'Amount' => $order->total_amount,
                        'Type' => $order->user_type,
                        'Date' => $order->created_at->format('Y-m-d'),
                    ];
                });
                $filename = 'revenue_report.csv';
                break;
        }

        // Create CSV
        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $handle = fopen('php://output', 'w');
        
        // Add headers
        if (!empty($data)) {
            fputcsv($handle, array_keys($data[0]));
        }

        // Add data rows
        foreach ($data as $row) {
            fputcsv($handle, $row);
        }

        fclose($handle);

        return response()->stream(
            function () use ($handle, $data) {
                foreach ($data as $row) {
                    fputcsv($handle, $row);
                }
                fclose($handle);
            },
            200,
            $headers
        );
    }
}
