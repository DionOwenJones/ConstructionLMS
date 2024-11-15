<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class CertificateController extends Controller
{
    public function download($id)
    {
        try {
            // Get course and verify completion
            $course = Course::findOrFail($id);
            $enrollment = DB::table('course_user')
                ->where('user_id', Auth::id())
                ->where('course_id', $course->id)
                ->where('completed', true)
                ->first();

            if (!$enrollment) {
                return back()->with('error', 'You have not completed this course yet.');
            }

            // Generate certificate
            $data = [
                'course' => $course,
                'user' => Auth::user(),
                'completed_at' => Carbon::parse($enrollment->completed_at)->format('F d, Y'),
                'certificate_number' => sprintf('CERT-%s-%s-%s', 
                    strtoupper(substr(Auth::user()->name, 0, 3)),
                    $course->id,
                    date('Ymd', strtotime($enrollment->completed_at))
                )
            ];

            $pdf = PDF::loadView('certificates.template', $data);
            
            return $pdf->download(sprintf('certificate-%s.pdf', Str::slug($course->title)));

        } catch (\Exception $e) {
            Log::error('Error generating certificate: ' . $e->getMessage());
            return back()->with('error', 'Error generating certificate. Please try again.');
        }
    }
}
