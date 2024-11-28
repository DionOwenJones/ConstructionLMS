<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\BusinessCourseAllocation;
use App\Models\BusinessCoursePurchase;
use App\Models\BusinessEmployee;
use App\Models\Course;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class BusinessEmployeeController extends Controller
{
    public function index()
    {
        $business = Auth::user()->getBusiness();
        
        if (!$business) {
            return redirect()->route('business.setup')
                ->with('warning', 'Please set up your business profile first.');
        }

        $employees = BusinessEmployee::where('business_id', $business->id)
            ->with(['user', 'courseAllocations'])
            ->paginate(10);

        $availableCourses = BusinessCoursePurchase::where('business_id', $business->id)
            ->withCount('allocations')
            ->having('licenses_purchased', '>', 'allocations_count')
            ->with('course')
            ->get()
            ->map(function($purchase) {
                return [
                    'id' => $purchase->course->id,
                    'name' => $purchase->course->name,
                    'available_licenses' => $purchase->licenses_purchased - $purchase->allocations_count
                ];
            });

        return view('business.employees.index', compact('employees', 'availableCourses'));
    }

    public function create()
    {
        $business = Auth::user()->getBusiness();
        
        if (!$business) {
            return redirect()->route('business.setup')
                ->with('warning', 'Please set up your business profile first.');
        }

        return view('business.employees.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'password.confirmed' => 'The password confirmation does not match.',
            'password.min' => 'The password must be at least 8 characters.',
        ]);

        $business = Auth::user()->getBusiness();
        
        if (!$business) {
            return redirect()->route('business.setup')
                ->with('warning', 'Please set up your business profile first.');
        }

        DB::transaction(function () use ($request, $business) {
            $employee = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'user'
            ]);

            BusinessEmployee::create([
                'business_id' => $business->id,
                'user_id' => $employee->id
            ]);

            return $employee;
        });

        return redirect()->route('business.employees.index')
            ->with('success', 'Team member added successfully.');
    }

    public function edit(BusinessEmployee $employee)
    {
        $business = Auth::user()->getBusiness();
        
        if (!$business) {
            return redirect()->route('business.setup')
                ->with('warning', 'Please set up your business profile first.');
        }

        // Verify employee belongs to this business
        if ($employee->business_id !== $business->id) {
            return redirect()->route('business.employees.index')
                ->with('error', 'Unauthorized access to employee record.');
        }

        return view('business.employees.edit', [
            'employee' => $employee,
            'business' => $business
        ]);
    }

    public function update(Request $request, BusinessEmployee $employee)
    {
        $business = Auth::user()->getBusiness();
        
        if (!$business) {
            return redirect()->route('business.setup')
                ->with('warning', 'Please set up your business profile first.');
        }

        // Verify employee belongs to this business
        if ($employee->business_id !== $business->id) {
            return redirect()->route('business.employees.index')
                ->with('error', 'Unauthorized access to employee record.');
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $employee->user_id],
        ]);

        $employee->user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        if ($request->filled('password')) {
            $request->validate([
                'password' => ['required', 'string', 'min:8', 'confirmed'],
            ]);

            $employee->user->update([
                'password' => Hash::make($request->password),
            ]);
        }

        return redirect()->route('business.employees.index')
            ->with('success', 'Team member updated successfully.');
    }

    public function destroy(BusinessEmployee $employee)
    {
        $business = Auth::user()->getBusiness();
        
        if (!$business || $employee->business_id !== $business->id) {
            return redirect()->route('business.employees.index')
                ->with('error', 'Unauthorized access to employee record.');
        }

        DB::transaction(function () use ($employee) {
            // Remove course allocations
            BusinessCourseAllocation::whereHas('purchase', function($query) use ($employee) {
                    $query->where('business_id', $employee->business_id);
                })
                ->where('user_id', $employee->user_id)
                ->delete();

            // Remove employee record
            $employee->delete();
        });

        return redirect()->route('business.employees.index')
            ->with('success', 'Team member removed successfully.');
    }
}
