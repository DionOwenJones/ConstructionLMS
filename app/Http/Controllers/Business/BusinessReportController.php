<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Models\BusinessCoursePurchase;
use App\Models\BusinessCourseAllocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
                DB::raw('SUM(seats_purchased) as total_seats'),
                DB::raw('SUM(seats_allocated) as used_seats'),
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
                'business_employee_id',
                DB::raw('COUNT(*) as allocation_count'),
                DB::raw('SUM(CASE WHEN completed = 1 THEN 1 ELSE 0 END) as completed_count')
            ])
            ->groupBy(['business_course_purchase_id', 'business_employee_id'])
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
                'business_employee_id',
                DB::raw('COUNT(*) as total_courses'),
                DB::raw('SUM(CASE WHEN completed = 1 THEN 1 ELSE 0 END) as completed_courses'),
                DB::raw('AVG(CASE WHEN completed = 1 THEN 1 ELSE 0 END) * 100 as completion_rate')
            ])
            ->groupBy('business_employee_id')
            ->get();

        return view('business.reports.employee-progress', compact('progress', 'startDate', 'endDate'));
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
}
