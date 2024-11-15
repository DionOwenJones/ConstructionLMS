<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\Course;
use App\Models\User;
use App\Models\BusinessCourseAllocation;
use App\Models\BusinessCoursePurchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BusinessCourseAllocationController extends Controller
{
    public function index()
    {
        $allocations = Auth::user()->business->courseAllocations()
            ->with(['user', 'course'])
            ->latest()
            ->paginate(10);

        return view('business.allocations.index', compact('allocations'));
    }

    public function create()
    {
        $users = Auth::user()->business->allocatedUsers;
        $courses = Course::all();

        return view('business.allocations.create', compact('users', 'courses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'course_id' => 'required|exists:courses,id',
            'expires_at' => 'nullable|date|after:today',
        ]);

        DB::transaction(function() use ($validated) {
            $user = User::find($validated['user_id']);
            $business = Auth::user()->business;

            // Find or create business course purchase
            $purchase = BusinessCoursePurchase::firstOrCreate(
                [
                    'business_id' => $business->id,
                    'course_id' => $validated['course_id']
                ],
                [
                    'seats_purchased' => 1,
                    'seats_allocated' => 0,
                    'purchased_at' => now()
                ]
            );

            // Create business course allocation
            $allocation = BusinessCourseAllocation::create([
                'business_course_purchase_id' => $purchase->id,
                'business_employee_id' => $user->businessEmployee->id,
                'allocated_at' => now(),
                'expires_at' => $validated['expires_at'] ?? null
            ]);

            // Increment allocated seats
            $purchase->increment('seats_allocated');

            // Add to user's courses
            $user->courses()->attach($validated['course_id'], [
                'allocated_at' => now(),
                'allocated_by_business_id' => $business->id,
                'completed_sections_count' => 0,
                'completed' => false
            ]);
        });

        return redirect()->route('business.allocations.index')
            ->with('success', 'Course allocated successfully');
    }

    public function destroy(BusinessCourseAllocation $allocation)
    {
        $this->authorize('delete', $allocation);

        $allocation->delete();

        return redirect()->route('business.allocations.index')
            ->with('success', 'Course allocation removed successfully');
    }
}
