<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\User;
use App\Models\BusinessCourseAllocation;
use App\Models\Course;
use App\Models\BusinessCoursePurchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class BusinessEmployeeController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        DB::transaction(function () use ($request) {
            // Create user
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            // Attach user to business as employee
            Auth::user()->business->employees()->attach($user->id);
        });

        return redirect()->route('business.employees.index')
            ->with('success', 'Employee added successfully.');
    }

    public function allocateCourse(Request $request, User $employee)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id'
        ]);

        $course = Course::findOrFail($request->course_id);
        $business = auth()->user()->business;

        // Check if employee already has this course allocated
        if ($employee->courses()->where('course_id', $course->id)->exists()) {
            return redirect()->back()->with('error', 'Course is already allocated to this employee.');
        }

        DB::transaction(function () use ($employee, $course, $business) {
            // Create a purchase record
            $purchase = BusinessCoursePurchase::create([
                'business_id' => $business->id,
                'course_id' => $course->id,
                'quantity' => 1,
                'price' => $course->price,
                'total' => $course->price,
                'purchased_at' => now()
            ]);

            // Create business course allocation record
            BusinessCourseAllocation::create([
                'business_course_purchase_id' => $purchase->id,
                'user_id' => $employee->id,
                'allocated_at' => now()
            ]);

            // Attach course to user
            $employee->courses()->attach($course->id, [
                'business_id' => $business->id,
                'course_purchase_id' => $purchase->id
            ]);

            if (config('mail.enabled')) {
                try {
                    Mail::to($employee->email)->send(new CourseAllocated($course, $employee, $business->name));
                } catch (\Exception $e) {
                    Log::error('Failed to send course allocation email: ' . $e->getMessage());
                }
            }
        });

        return redirect()->back()->with('success', 'Course allocated successfully.');
    }
}
