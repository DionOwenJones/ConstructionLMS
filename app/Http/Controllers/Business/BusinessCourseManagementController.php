<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\User;
use App\Models\BusinessCoursePurchase;
use App\Models\BusinessCourseAllocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BusinessCourseManagementController extends Controller
{
    /**
     * Display available courses for purchase
     */
    public function available()
    {
        $courses = Course::where('status', 'published')
            ->withCount(['purchases as total_seats' => function($query) {
                $query->where('business_id', Auth::user()->business->id)
                    ->select(DB::raw('SUM(seats_purchased)'));
            }])
            ->latest()
            ->paginate(10);

        return view('business.courses.available', compact('courses'));
    }

    /**
     * Display purchased courses and their allocations
     */
    public function purchases()
    {
        $purchases = Auth::user()->business->coursePurchases()
            ->with(['course', 'allocations.user'])
            ->withCount(['allocations as used_seats'])
            ->latest()
            ->paginate(10);

        return view('business.courses.purchases', compact('purchases'));
    }

    /**
     * Purchase seats for a course
     */
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
                'price_per_seat' => $course->price,
                'purchased_at' => now()
            ]);

            DB::commit();

            return redirect()->route('business.courses.purchases')
                ->with('success', "Successfully purchased {$validated['seats']} seats");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error purchasing course seats: ' . $e->getMessage());
        }
    }

    /**
     * Show allocation form
     */
    public function showAllocationForm(BusinessCoursePurchase $purchase)
    {
        $purchase->load(['course', 'allocations.user']);
        
        // Get all employees who haven't been allocated this course yet
        $availableEmployees = Auth::user()->business->employees()
            ->with('user.courses')  // Eager load the user and their courses
            ->whereHas('user', function($query) use ($purchase) {
                $query->whereDoesntHave('courses', function($q) use ($purchase) {
                    $q->where('courses.id', $purchase->course_id);
                });
            })
            ->get();

        return view('business.courses.allocate', compact('purchase', 'availableEmployees'));
    }

    /**
     * Allocate course to employee(s)
     */
    public function allocate(Request $request, BusinessCoursePurchase $purchase)
    {
        $validated = $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
            'expires_at' => 'nullable|date|after:today'
        ]);

        try {
            DB::beginTransaction();

            $availableSeats = $purchase->available_seats;
            $requestedSeats = count($validated['user_ids']);

            if ($requestedSeats > $availableSeats) {
                throw new \Exception("Not enough seats available. Available: {$availableSeats}, Requested: {$requestedSeats}");
            }

            foreach ($validated['user_ids'] as $userId) {
                $purchase->allocateToUser(
                    User::findOrFail($userId), 
                    $validated['expires_at'] ?? null
                );
            }

            DB::commit();
            return redirect()->route('business.courses.purchases')
                ->with('success', 'Successfully allocated course to selected employees');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * View all allocations
     */
    public function allocations()
    {
        $allocations = Auth::user()->business->courseAllocations()
            ->with(['user', 'purchase.course'])
            ->latest()
            ->paginate(10);

        return view('business.courses.allocations', compact('allocations'));
    }

    /**
     * Remove course allocation
     */
    public function removeAllocation(BusinessCourseAllocation $allocation)
    {
        if ($allocation->purchase->business_id !== Auth::user()->business->id) {
            abort(403, 'Unauthorized action.');
        }

        $allocation->delete(); // This will trigger the model event to remove from user's courses

        return back()->with('success', 'Course allocation removed successfully');
    }
}
