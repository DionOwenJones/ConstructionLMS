<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Models\BusinessEmployee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\BusinessCoursePurchase;
use App\Models\BusinessCourseAllocation;
use App\Models\Course;

class BusinessEmployeeController extends Controller
{
    public function index()
    {
        $employees = BusinessEmployee::where('business_id', Auth::user()->business->id)
            ->with(['user', 'courseAllocations'])
            ->paginate(10);

        $availableCourses = BusinessCoursePurchase::where('business_id', Auth::user()->business->id)
            ->whereColumn('seats_allocated', '<', 'seats_purchased')
            ->with('course')
            ->get()
            ->map(function($purchase) {
                return [
                    'id' => $purchase->course->id,
                    'name' => $purchase->course->name,
                    'available_seats' => $purchase->seats_purchased - $purchase->seats_allocated
                ];
            });

        return view('business.employees.index', compact('employees', 'availableCourses'));
    }

    public function create()
    {
        return view('business.employees.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        DB::transaction(function () use ($request) {
            // Create the user first
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            // Create the business employee record
            BusinessEmployee::create([
                'business_id' => Auth::user()->business->id,
                'user_id' => $user->id,
            ]);
        });

        return redirect()->route('business.employees.index')
            ->with('success', 'Employee added successfully');
    }

    public function edit(BusinessEmployee $employee)
    {
        return view('business.employees.edit', compact('employee'));
    }

    public function update(Request $request, BusinessEmployee $employee)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:business_employees,email,' . $employee->id,
            'employee_id' => 'nullable|string|max:255'
        ]);

        $employee->update($validated);

        return redirect()->route('business.employees.index')
            ->with('success', 'Employee updated successfully');
    }

    public function destroy(BusinessEmployee $employee)
    {
        $employee->delete();
        return redirect()->route('business.employees.index')
            ->with('success', 'Employee removed successfully');
    }

    public function allocateCourse(Request $request, User $employee)
    {
        $request->validate([
            'course_id' => ['required', 'exists:courses,id'],
        ]);

        DB::transaction(function() use ($request, $employee) {
            $course = Course::findOrFail($request->course_id);

            // Create business course allocation
            BusinessCourseAllocation::create([
                'business_course_purchase_id' => $request->purchase_id,
                'business_employee_id' => $employee->id,
                'allocated_at' => now()
            ]);

            // Attach course to user's dashboard
            $employee->user->courses()->attach($request->course_id, [
                'title' => $course->title,
                'allocated_at' => now(),
                'allocated_by_business_id' => $employee->business_id,
                'completed_sections_count' => 0,
                'completed' => false
            ]);
        });

        return back()->with('success', 'Course allocated successfully.');
    }
}
