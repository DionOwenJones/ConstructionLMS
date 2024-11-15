<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\BusinessEmployee;
use App\Models\Business;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class BusinessCertificateController extends Controller
{
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
                'completed_at' => Carbon::parse($enrollment->completed_at)->format('F d, Y'),
                'certificate_number' => sprintf('CERT-%s-%s-%s', 
                    strtoupper(substr($employee->user->name, 0, 3)),
                    $course->id,
                    date('Ymd', strtotime($enrollment->completed_at))
                ),
                'business_name' => $business->company_name
            ];

            $pdf = PDF::loadView('certificates.business-template', $data);
            
            return $pdf->download(sprintf('certificate-%s-%s.pdf', 
                Str::slug($employee->user->name),
                Str::slug($course->title)
            ));

        } catch (\Exception $e) {
            Log::error('Error generating business certificate: ' . $e->getMessage());
            return back()->with('error', 'Error generating certificate. Please try again.');
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

            // Get completed courses for this employee
            $completedCourses = DB::table('course_user')
                ->where('user_id', $employee->user_id)
                ->where('completed', true)
                ->join('courses', 'course_user.course_id', '=', 'courses.id')
                ->select('courses.*', 'course_user.completed_at')
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
}
