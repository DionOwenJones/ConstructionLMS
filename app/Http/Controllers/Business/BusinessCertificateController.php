<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Models\BusinessEmployee;
use App\Models\Course;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class BusinessCertificateController extends Controller
{
    public function download(BusinessEmployee $employee, Course $course)
    {
        // Verify the business owns this employee and course
        $this->authorize('view', $employee);

        $certificate = $employee->certificates()
            ->where('course_id', $course->id)
            ->firstOrFail();

        $data = [
            'user_name' => $employee->user->name,
            'course_name' => $course->title,
            'completion_date' => $certificate->issued_at->format('F d, Y'),
            'certificate_number' => $certificate->certificate_number,
            'business_name' => Auth::user()->business->company_name
        ];

        return PDF::loadView('certificates.template', $data)
            ->download("certificate-{$certificate->certificate_number}.pdf");
    }
}
