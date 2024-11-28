<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\BusinessEmployee;
use App\Models\Business;
use App\Models\Certificate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class BusinessCertificateController extends Controller
{
    public function index()
    {
        try {
            // Get the business for the authenticated user
            $business = Business::where('user_id', Auth::id())->firstOrFail();
            
            $employees = $business->employees()
                ->with(['user' => function($query) {
                    $query->select('id', 'name', 'email');
                }])
                ->paginate(10);

            // Load completed courses for each employee
            foreach ($employees as $employee) {
                $employee->completedCourses = DB::table('course_user')
                    ->join('users', 'course_user.user_id', '=', 'users.id')
                    ->join('courses', 'course_user.course_id', '=', 'courses.id')
                    ->join('business_employees', function($join) use ($business) {
                        $join->on('users.id', '=', 'business_employees.user_id')
                            ->where('business_employees.business_id', '=', $business->id);
                    })
                    ->where('business_employees.id', $employee->id)
                    ->where('course_user.completed', true)
                    ->select([
                        'users.name as employee_name',
                        'courses.title',
                        'courses.id as course_id',
                        'course_user.completed_at',
                        'business_employees.id as employee_id'
                    ])
                    ->orderBy('course_user.completed_at', 'desc')
                    ->get();
            }

            return view('business.certificates.index', [
                'employees' => $employees,
                'business' => $business
            ]);

        } catch (\Exception $e) {
            Log::error('Error viewing certificates: ' . $e->getMessage());
            return back()->with('error', 'Error viewing certificates. Please try again.');
        }
    }

    public function viewEmployeeCertificates($employeeId)
    {
        try {
            // Get the business for the authenticated user
            $business = Business::where('user_id', Auth::id())->firstOrFail();

            // Verify the employee belongs to the business
            $employee = BusinessEmployee::where('id', $employeeId)
                ->where('business_id', $business->id)
                ->with(['user'])
                ->firstOrFail();

            // Get completed courses using the same query structure as the dashboard
            $completedCourses = DB::table('course_user')
                ->join('users', 'course_user.user_id', '=', 'users.id')
                ->join('courses', 'course_user.course_id', '=', 'courses.id')
                ->join('business_employees', function($join) use ($business) {
                    $join->on('users.id', '=', 'business_employees.user_id')
                        ->where('business_employees.business_id', '=', $business->id);
                })
                ->where('business_employees.id', $employeeId)
                ->where('course_user.completed', true)
                ->select([
                    'users.name as employee_name',
                    'courses.title',
                    'courses.id as course_id',
                    'course_user.completed_at',
                    'business_employees.id as employee_id'
                ])
                ->orderBy('course_user.completed_at', 'desc')
                ->get();

            return view('business.certificates.index', [
                'employee' => $employee,
                'completedCourses' => $completedCourses
            ]);

        } catch (\Exception $e) {
            Log::error('Error viewing employee certificates: ' . $e->getMessage());
            return back()->with('error', 'Error viewing certificates. Please try again.');
        }
    }

    public function download($employeeId, $courseId)
    {
        try {
            // Get the business for the authenticated user
            $business = Business::where('user_id', Auth::id())->firstOrFail();

            // Verify the employee belongs to the business
            $employee = BusinessEmployee::where('id', $employeeId)
                ->where('business_id', $business->id)
                ->firstOrFail();

            // Get course and verify completion
            $course = Course::findOrFail($courseId);
            $enrollment = DB::table('course_user')
                ->where('user_id', $employee->user_id)
                ->where('course_id', $courseId)
                ->where('completed', true)
                ->first();

            if (!$enrollment) {
                return back()->with('error', 'Employee has not completed this course yet.');
            }

            // Generate certificate
            $data = [
                'course' => $course,
                'user' => $employee->user,
                'completedAt' => Carbon::parse($enrollment->completed_at),
                'certificate_number' => sprintf('CERT-%s-%s-%s', 
                    strtoupper(substr($employee->user->name, 0, 3)),
                    $course->id,
                    date('Ymd', strtotime($enrollment->completed_at))
                )
            ];

            $pdf = PDF::loadView('certificates.course', $data)
                ->setPaper('a4', 'landscape');
            
            return $pdf->download(sprintf('certificate-%s-%s.pdf', 
                Str::slug($employee->user->name),
                Str::slug($course->title)
            ));

        } catch (\Exception $e) {
            Log::error('Error generating business certificate: ' . $e->getMessage());
            return back()->with('error', 'Error generating certificate. Please try again.');
        }
    }
}
