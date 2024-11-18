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
    public function generate(Course $course)
    {
        try {
            // Verify course completion
            $enrollment = DB::table('course_user')
                ->where('user_id', Auth::id())
                ->where('course_id', $course->id)
                ->where('completed', true)
                ->first();

            if (!$enrollment) {
                return redirect()->route('courses.show', $course)
                    ->with('error', 'You need to complete the course before accessing the certificate.');
            }

            // Check if certificate already exists
            $existingCertificate = Certificate::where('user_id', Auth::id())
                ->where('course_id', $course->id)
                ->first();

            if ($existingCertificate) {
                return redirect()->route('certificates.download', $existingCertificate->id);
            }

            // Generate new certificate
            $certificate = Certificate::create([
                'user_id' => Auth::id(),
                'course_id' => $course->id,
                'certificate_number' => sprintf('CERT-%s-%s-%s', 
                    strtoupper(substr(Auth::user()->name, 0, 3)),
                    $course->id,
                    date('Ymd', strtotime($enrollment->completed_at))
                ),
                'issued_at' => Carbon::parse($enrollment->completed_at)
            ]);

            return redirect()->route('certificates.download', $certificate->id);

        } catch (\Exception $e) {
            Log::error('Error generating certificate: ' . $e->getMessage());
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

    public function download($id)
    {
        try {
            $user = Auth::user();
            
            // Get the certificate or check course completion
            $certificate = Certificate::where('id', $id)
                ->where('user_id', $user->id)
                ->first();

            if (!$certificate) {
                // Check if course is completed but certificate not generated
                $course = Course::findOrFail($id);
                $enrollment = DB::table('course_user')
                    ->where('user_id', $user->id)
                    ->where('course_id', $course->id)
                    ->where('completed', true)
                    ->first();

                if (!$enrollment) {
                    return back()->with('error', 'You have not completed this course yet.');
                }

                // Generate new certificate
                $certificate = Certificate::create([
                    'user_id' => $user->id,
                    'course_id' => $course->id,
                    'certificate_number' => sprintf('CERT-%s-%s-%s', 
                        strtoupper(substr($user->name, 0, 3)),
                        $course->id,
                        date('Ymd', strtotime($enrollment->completed_at))
                    ),
                    'issued_at' => Carbon::parse($enrollment->completed_at)
                ]);
            }

            $course = Course::findOrFail($certificate->course_id);

            // Generate PDF
            $data = [
                'course' => $course,
                'user' => $user,
                'completedAt' => $certificate->issued_at,
                'certificate_number' => $certificate->certificate_number
            ];

            $pdf = PDF::loadView('certificates.course', $data)
                ->setPaper('a4', 'landscape');
            
            return $pdf->download(sprintf('certificate-%s.pdf', Str::slug($course->title)));

        } catch (\Exception $e) {
            Log::error('Error generating certificate: ' . $e->getMessage());
            return back()->with('error', 'Error generating certificate. Please try again.');
        }
    }
}
