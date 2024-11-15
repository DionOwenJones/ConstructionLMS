<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\BusinessCoursePurchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BusinessCoursePurchaseController extends Controller
{
    public function index()
    {
        $purchases = Auth::user()->business->coursePurchases()
            ->with(['course'])
            ->latest()
            ->paginate(10);

        return view('business.purchases.index', compact('purchases'));
    }

    public function create()
    {
        $courses = Course::where('status', 'published')->get();
        return view('business.purchases.create', compact('courses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'seats_purchased' => 'required|integer|min:1'
        ]);

        try {
            DB::beginTransaction();

            $purchase = BusinessCoursePurchase::create([
                'business_id' => Auth::user()->business->id,
                'course_id' => $validated['course_id'],
                'seats_purchased' => $validated['seats_purchased'],
                'seats_allocated' => 0,
                'purchased_at' => now()
            ]);

            DB::commit();

            return redirect()->route('business.purchases.index')
                ->with('success', "Successfully purchased {$validated['seats_purchased']} seats");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error purchasing course seats: ' . $e->getMessage());
        }
    }

    public function show(BusinessCoursePurchase $purchase)
    {
        $purchase->load(['course', 'allocations.user']);
        return view('business.purchases.show', compact('purchase'));
    }

    public function destroy(BusinessCoursePurchase $purchase)
    {
        if ($purchase->seats_allocated > 0) {
            return back()->with('error', 'Cannot delete purchase with allocated seats');
        }

        $purchase->delete();
        return redirect()->route('business.purchases.index')
            ->with('success', 'Purchase deleted successfully');
    }
}
