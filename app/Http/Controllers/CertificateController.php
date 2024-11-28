<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Certificate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class CertificateController extends Controller
{
    /**
     * Generate new certificate
     */
    public function generate(Course $course)
    {
        try {
            $user = Auth::user();
            
            // Verify course completion
            $enrollment = DB::table('course_user')
                ->where('user_id', $user->id)
                ->where('course_id', $course->id)
                ->where('completed', true)
                ->first();

            if (!$enrollment) {
                return redirect()->route('courses.show', $course)
                    ->with('error', 'You need to complete the course before accessing the certificate.');
            }

            // Check if user can generate certificate (passed all assessments)
            if (!$course->canGenerateCertificate($user->id)) {
                return redirect()->route('courses.show', $course)
                    ->with('error', 'You need to pass all assessments before generating a certificate.');
            }

            // Check if user can still access the course (has attempts remaining)
            if (!$course->canAccessCourse($user->id)) {
                return redirect()->route('courses.show', $course)
                    ->with('error', 'You have exceeded the maximum number of attempts for one or more assessments. Please contact support.');
            }

            // Check if certificate already exists
            $existingCertificate = Certificate::where('user_id', $user->id)
                ->where('course_id', $course->id)
                ->first();

            if ($existingCertificate) {
                return redirect()->route('certificates.download', $existingCertificate->id);
            }

            // Generate new certificate
            $certificate = new Certificate([
                'user_id' => $user->id,
                'course_id' => $course->id,
                'certificate_number' => sprintf('CERT-%s-%s-%s', 
                    strtoupper(substr($user->name, 0, 3)),
                    $course->id,
                    date('Ymd')
                ),
                'issued_at' => now()
            ]);

            // Set expiry if course has expiry
            if ($course->has_expiry && $course->expiry_months > 0) {
                $certificate->has_expiry = true;
                $certificate->expires_at = now()->addMonths($course->expiry_months);
            }

            $certificate->save();

            return redirect()->route('certificates.download', $certificate->id);

        } catch (\Exception $e) {
            \Log::error('Error generating certificate: ' . $e->getMessage());
            return back()->with('error', 'Error generating certificate. Please try again.');
        }
    }

    public function index()
    {
        $user = Auth::user();
        
        // Get all completed courses with certificates
        $completedCourses = DB::table('course_user')
            ->join('courses', 'course_user.course_id', '=', 'courses.id')
            ->where('course_user.user_id', $user->id)
            ->where('course_user.completed', true)
            ->select('courses.*', 'course_user.completed_at')
            ->get();

        return view('certificates.index', [
            'completedCourses' => $completedCourses
        ]);
    }

    /**
     * Download certificate
     */
    public function download($id)
    {
        try {
            // Find certificate by ID
            $certificate = Certificate::with(['user', 'course'])
                ->where('id', $id)
                ->where('user_id', auth()->id())
                ->firstOrFail();

            // Check if course exists
            if (!$certificate->course) {
                return back()->with('error', 'Course not found for this certificate.');
            }

            // Check if certificate is expired
            if ($certificate->isExpired()) {
                return back()->with('error', 'This certificate has expired. Please retake the course to get a new certificate.');
            }

            $data = [
                'user' => $certificate->user,
                'course' => $certificate->course,
                'certificate' => $certificate,
                'completedAt' => $certificate->issued_at,
                'expiryDate' => $certificate->has_expiry ? $certificate->expires_at->format('d/m/Y') : null,
                'certificate_number' => $certificate->certificate_number
            ];

            $pdf = PDF::loadView('certificates.course', $data);
            
            // Set paper size to match 1920x1080
            $pdf->setPaper([0, 0, 1920, 1080], 'landscape');
            
            // Basic PDF options
            $pdf->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'defaultFont' => 'Arial'
            ]);

            $filename = "certificate_{$certificate->id}.pdf";
            
            return $pdf->download($filename);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return back()->with('error', 'Certificate not found.');
        } catch (\Exception $e) {
            \Log::error('Certificate generation failed: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return back()->with('error', 'Failed to generate certificate. Please try again.');
        }
    }
}
