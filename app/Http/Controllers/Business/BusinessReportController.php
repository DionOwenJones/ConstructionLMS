<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Models\BusinessCoursePurchase;
use App\Models\BusinessCourseAllocation;
use App\Models\BusinessEmployee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

class BusinessReportController extends Controller
{
    /**
     * Show the main reports dashboard
     */
    public function index()
    {
        return view('business.reports.index');
    }

    /**
     * Generate course purchase report
     */
    public function purchases(Request $request)
    {
        $startDate = $request->get('start_date', now()->subMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        $purchases = BusinessCoursePurchase::where('business_id', Auth::user()->business->id)
            ->whereBetween('purchased_at', [$startDate, $endDate])
            ->with(['course'])
            ->select([
                'course_id',
                DB::raw('SUM(licenses_purchased) as total_licenses'),
                DB::raw('SUM(licenses_allocated) as used_licenses'),
                DB::raw('COUNT(*) as purchase_count')
            ])
            ->groupBy('course_id')
            ->get();

        return view('business.reports.purchases', compact('purchases', 'startDate', 'endDate'));
    }

    /**
     * Generate course allocation report
     */
    public function allocations(Request $request)
    {
        $startDate = $request->get('start_date', now()->subMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        $allocations = BusinessCourseAllocation::whereHas('purchase', function($query) {
                $query->where('business_id', Auth::user()->business->id);
            })
            ->whereBetween('allocated_at', [$startDate, $endDate])
            ->with(['purchase.course', 'employee'])
            ->select([
                'business_course_purchase_id',
                'user_id',
                DB::raw('COUNT(*) as allocation_count'),
                DB::raw('SUM(CASE WHEN completed = 1 THEN 1 ELSE 0 END) as completed_count')
            ])
            ->groupBy(['business_course_purchase_id', 'user_id'])
            ->get();

        return view('business.reports.allocations', compact('allocations', 'startDate', 'endDate'));
    }

    /**
     * Generate employee progress report
     */
    public function employeeProgress(Request $request)
    {
        $startDate = $request->get('start_date', now()->subMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        $progress = BusinessCourseAllocation::whereHas('purchase', function($query) {
                $query->where('business_id', Auth::user()->business->id);
            })
            ->whereBetween('allocated_at', [$startDate, $endDate])
            ->with(['employee'])
            ->select([
                'user_id',
                DB::raw('COUNT(*) as total_courses'),
                DB::raw('SUM(CASE WHEN completed = 1 THEN 1 ELSE 0 END) as completed_courses'),
                DB::raw('AVG(CASE WHEN completed = 1 THEN 1 ELSE 0 END) * 100 as completion_rate')
            ])
            ->groupBy('user_id')
            ->get();

        return view('business.reports.employee-progress', compact('progress', 'startDate', 'endDate'));
    }

    /**
     * Show the progress report
     */
    public function progress()
    {
        $business = Auth::user()->business;
        
        $employees = BusinessEmployee::where('business_id', $business->id)
            ->with(['user', 'user.enrolledCourses'])
            ->get();

        $progressData = [];
        foreach ($employees as $employee) {
            $progressData[] = [
                'employee' => $employee,
                'courses' => $employee->user->enrolledCourses->map(function ($course) {
                    return [
                        'course' => $course,
                        'progress' => $course->pivot->progress ?? 0,
                        'completed' => $course->pivot->completed ?? false,
                        'last_accessed' => $course->pivot->last_accessed
                    ];
                })
            ];
        }

        return view('business.reports.progress', compact('progressData'));
    }

    /**
     * Show the completion report
     */
    public function completion()
    {
        $business = Auth::user()->business;
        
        $completionStats = DB::table('course_user')
            ->join('users', 'course_user.user_id', '=', 'users.id')
            ->join('business_employees', 'users.id', '=', 'business_employees.user_id')
            ->join('courses', 'course_user.course_id', '=', 'courses.id')
            ->where('business_employees.business_id', $business->id)
            ->select([
                'courses.title',
                DB::raw('COUNT(*) as total_enrollments'),
                DB::raw('SUM(CASE WHEN course_user.completed = 1 THEN 1 ELSE 0 END) as completed_count'),
                DB::raw('AVG(course_user.progress) as average_progress')
            ])
            ->groupBy('courses.id', 'courses.title')
            ->get();

        return view('business.reports.completion', compact('completionStats'));
    }

    /**
     * Show the engagement report
     */
    public function engagement()
    {
        $business = Auth::user()->business;
        
        $engagementData = DB::table('course_user')
            ->join('users', 'course_user.user_id', '=', 'users.id')
            ->join('business_employees', 'users.id', '=', 'business_employees.user_id')
            ->where('business_employees.business_id', $business->id)
            ->select([
                'users.name',
                DB::raw('COUNT(DISTINCT course_user.course_id) as enrolled_courses'),
                DB::raw('SUM(CASE WHEN course_user.completed = 1 THEN 1 ELSE 0 END) as completed_courses'),
                DB::raw('AVG(course_user.progress) as average_progress'),
                DB::raw('MAX(course_user.last_accessed) as last_activity')
            ])
            ->groupBy('users.id', 'users.name')
            ->get();

        return view('business.reports.engagement', compact('engagementData'));
    }

    /**
     * Export report data to CSV
     */
    public function export(Request $request, string $type)
    {
        // Validate report type
        if (!in_array($type, ['purchases', 'allocations', 'employee-progress'])) {
            return back()->with('error', 'Invalid report type');
        }

        // Get report data based on type
        $data = $this->{$type}($request)->getData()[$type];

        // Generate CSV filename
        $filename = sprintf('report-%s-%s.csv', $type, now()->format('Y-m-d'));

        // Create CSV response
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');

            // Add headers
            fputcsv($file, array_keys($data->first()->toArray()));

            // Add data rows
            foreach ($data as $row) {
                fputcsv($file, $row->toArray());
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export reports data
     */
    public function exportReports(Request $request)
    {
        $business = Auth::user()->business;
        $reportType = $request->get('type', 'progress');
        
        // Get report data based on type
        switch ($reportType) {
            case 'progress':
                $data = $this->getProgressExportData($business);
                $filename = 'progress_report.csv';
                break;
            case 'completion':
                $data = $this->getCompletionExportData($business);
                $filename = 'completion_report.csv';
                break;
            case 'engagement':
                $data = $this->getEngagementExportData($business);
                $filename = 'engagement_report.csv';
                break;
            default:
                return back()->with('error', 'Invalid report type.');
        }

        // Generate CSV
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');
            fputcsv($file, array_keys($data[0]));
            foreach ($data as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    private function getProgressExportData($business)
    {
        return DB::table('course_user')
            ->join('users', 'course_user.user_id', '=', 'users.id')
            ->join('business_employees', 'users.id', '=', 'business_employees.user_id')
            ->join('courses', 'course_user.course_id', '=', 'courses.id')
            ->where('business_employees.business_id', $business->id)
            ->select([
                'users.name as Employee',
                'courses.title as Course',
                'course_user.progress as Progress',
                'course_user.completed as Completed',
                'course_user.last_accessed as Last_Accessed'
            ])
            ->get()
            ->toArray();
    }

    private function getCompletionExportData($business)
    {
        return DB::table('course_user')
            ->join('users', 'course_user.user_id', '=', 'users.id')
            ->join('business_employees', 'users.id', '=', 'business_employees.user_id')
            ->join('courses', 'course_user.course_id', '=', 'courses.id')
            ->where('business_employees.business_id', $business->id)
            ->select([
                'courses.title as Course',
                DB::raw('COUNT(*) as Total_Enrollments'),
                DB::raw('SUM(CASE WHEN course_user.completed = 1 THEN 1 ELSE 0 END) as Completed_Count'),
                DB::raw('AVG(course_user.progress) as Average_Progress')
            ])
            ->groupBy('courses.id', 'courses.title')
            ->get()
            ->toArray();
    }

    private function getEngagementExportData($business)
    {
        return DB::table('course_user')
            ->join('users', 'course_user.user_id', '=', 'users.id')
            ->join('business_employees', 'users.id', '=', 'business_employees.user_id')
            ->where('business_employees.business_id', $business->id)
            ->select([
                'users.name as Employee',
                DB::raw('COUNT(DISTINCT course_user.course_id) as Enrolled_Courses'),
                DB::raw('SUM(CASE WHEN course_user.completed = 1 THEN 1 ELSE 0 END) as Completed_Courses'),
                DB::raw('AVG(course_user.progress) as Average_Progress'),
                DB::raw('MAX(course_user.last_accessed) as Last_Activity')
            ])
            ->groupBy('users.id', 'users.name')
            ->get()
            ->toArray();
    }
}
