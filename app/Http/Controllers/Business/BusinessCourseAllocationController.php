<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\Course;
use App\Models\User;
use App\Models\BusinessCourseAllocation;
use App\Models\BusinessCoursePurchase;
use App\Notifications\CourseAllocationNotification;
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
                    'licenses_purchased' => 1,
                    'price_per_license' => Course::find($validated['course_id'])->price,
                    'purchased_at' => now()
                ]
            );

            // Create business course allocation
            $allocation = BusinessCourseAllocation::create([
                'business_course_purchase_id' => $purchase->id,
                'user_id' => $user->id,
                'allocated_at' => now(),
                'expires_at' => $validated['expires_at'] ?? null
            ]);

            // Send email notification to the user
            $user->notify(new CourseAllocationNotification($purchase->course, $business));
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
