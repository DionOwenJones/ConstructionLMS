<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\User;
use App\Models\BusinessCourseAllocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

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
            'course_id' => ['required', 'exists:courses,id'],
        ]);

        BusinessCourseAllocation::create([
            'business_employee_id' => $employee->id,
            'course_id' => $request->course_id,
            'allocated_at' => now(),
        ]);

        return back()->with('success', 'Course allocated successfully.');
    }
}
