<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Certificate;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class CertificateController extends Controller
{
    public function show(Course $course)
    {
        $this->validateCourseCompletion($course);

        $certificate = $this->getOrCreateCertificate($course);
        $data = $this->prepareCertificateData($certificate, $course);

        return PDF::loadView('certificates.template', $data)->stream('certificate.pdf');
    }

    public function download(Course $course)
    {
        $this->validateCourseCompletion($course);

        $certificate = $this->getOrCreateCertificate($course);
        $data = $this->prepareCertificateData($certificate, $course);

        return PDF::loadView('certificates.template', $data)
            ->download("certificate-{$certificate->certificate_number}.pdf");
    }

    private function validateCourseCompletion(Course $course)
    {
        $enrollment = DB::table('course_user')
            ->where('user_id', Auth::id())
            ->where('course_id', $course->id)
            ->where(function($query) {
                $query->where('completed', true)
                      ->orWhere('completed_at', '!=', null);
            })
            ->first();

        $businessAllocation = DB::table('business_course_allocations')
            ->join('business_employees', 'business_course_allocations.business_employee_id', '=', 'business_employees.id')
            ->join('business_course_purchases', 'business_course_allocations.business_course_purchase_id', '=', 'business_course_purchases.id')
            ->where('business_employees.user_id', Auth::id())
            ->where('business_course_purchases.course_id', $course->id)
            ->where('business_course_allocations.completed', true)
            ->first();

        if (!$enrollment && !$businessAllocation) {
            throw new \Illuminate\Auth\Access\AuthorizationException(
                'You must complete the course to access the certificate.'
            );
        }
    }

    private function getOrCreateCertificate(Course $course)
    {
        return Certificate::firstOrCreate(
            [
                'user_id' => Auth::id(),
                'course_id' => $course->id,
            ],
            [
                'certificate_number' => 'CERT-' . Str::random(10),
                'issued_at' => now(),
            ]
        );
    }

    private function prepareCertificateData(Certificate $certificate, Course $course)
    {
        return [
            'user_name' => Auth::user()->name,
            'course_name' => $course->title,
            'completion_date' => $certificate->issued_at->format('F d, Y'),
            'certificate_number' => $certificate->certificate_number
        ];
    }
}
