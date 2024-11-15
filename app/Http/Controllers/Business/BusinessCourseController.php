<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Business;
use App\Models\BusinessCoursePurchase;
use App\Models\BusinessEmployee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BusinessCourseController extends Controller
{
    public function available()
    {
        $courses = Course::where('status', 'published')
            ->latest()
            ->paginate(10);

        return view('business.courses.available', compact('courses'));
    }

    public function purchased()
    {
        $purchases = Auth::user()->business->coursePurchases()
            ->with(['course'])
            ->latest()
            ->paginate(10);

        return view('business.courses.purchased', compact('purchases'));
    }

    public function purchase(Request $request, Course $course)
    {
        $validated = $request->validate([
            'seats' => 'required|integer|min:1'
        ]);

        try {
            DB::beginTransaction();

            $purchase = BusinessCoursePurchase::create([
                'business_id' => Auth::user()->business->id,
                'course_id' => $course->id,
                'seats_purchased' => $validated['seats'],
                'seats_allocated' => 0,
                'purchased_at' => now()
            ]);

            DB::commit();

            return redirect()->route('business.courses.purchased')
                ->with('success', "Successfully purchased {$validated['seats']} seats for {$course->title}");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error purchasing course: ' . $e->getMessage());
        }
    }

    public function allocate(Request $request, BusinessCoursePurchase $purchase)
    {
        $validated = $request->validate([
            'employee_ids' => 'required|array',
            'employee_ids.*' => 'exists:business_employees,id'
        ]);

        try {
            DB::beginTransaction();

            $availableSeats = $purchase->availableSeats();
            $requestedSeats = count($validated['employee_ids']);

            if ($requestedSeats > $availableSeats) {
                throw new \Exception("Not enough seats available. Available: {$availableSeats}, Requested: {$requestedSeats}");
            }

            foreach ($validated['employee_ids'] as $employeeId) {
                $employee = BusinessEmployee::findOrFail($employeeId);
                $purchase->allocate($employee);
            }

            DB::commit();
            return back()->with('success', 'Successfully allocated course to employees');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    public function showAllocationForm(BusinessCoursePurchase $purchase)
    {
        $employees = Auth::user()->business->employees()
            ->whereNotIn('id', $purchase->allocations->pluck('business_employee_id'))
            ->get();

        return view('business.courses.allocate', compact('purchase', 'employees'));
    }

    public function index()
    {
        $courses = Auth::user()->business->coursePurchases()
            ->with(['course', 'allocations'])
            ->latest()
            ->paginate(10);

        $courseCount = $courses->total();

        return view('business.courses.index', compact('courses', 'courseCount'));
    }
}
