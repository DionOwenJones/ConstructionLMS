<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BusinessCourseAllocation;
use App\Models\Business;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AllocationController extends Controller
{
    /**
     * Display a listing of the course allocations.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = BusinessCourseAllocation::with(['employee.business', 'employee.user', 'course'])
            ->when($request->search, function ($query, $search) {
                return $query->whereHas('employee.business', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                })->orWhereHas('employee.user', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($request->status, function ($query, $status) {
                switch ($status) {
                    case 'completed':
                        return $query->whereHas('courseProgress', function ($q) {
                            $q->where('completed', true);
                        });
                    case 'expired':
                        return $query->where('deadline', '<', now())
                            ->whereDoesntHave('courseProgress', function ($q) {
                                $q->where('completed', true);
                            });
                    case 'active':
                        return $query->where(function ($q) {
                            $q->whereNull('deadline')
                                ->orWhere('deadline', '>', now());
                        })->whereDoesntHave('courseProgress', function ($q) {
                            $q->where('completed', true);
                        });
                }
            });

        $allocations = $query->latest()->paginate(10);

        // Calculate statistics
        $stats = [
            'active' => BusinessCourseAllocation::whereDoesntHave('courseProgress', function ($q) {
                $q->where('completed', true);
            })->count(),
            'completed' => BusinessCourseAllocation::whereHas('courseProgress', function ($q) {
                $q->where('completed', true);
            })->count(),
            'expiring_soon' => BusinessCourseAllocation::where('deadline', '>', now())
                ->where('deadline', '<=', now()->addDays(7))
                ->whereDoesntHave('courseProgress', function ($q) {
                    $q->where('completed', true);
                })->count(),
            'total_businesses' => Business::count()
        ];

        return view('admin.allocations.index', compact('allocations', 'stats'));
    }

    /**
     * Show the form for editing the specified allocation.
     *
     * @param  \App\Models\BusinessCourseAllocation  $allocation
     * @return \Illuminate\View\View
     */
    public function edit(BusinessCourseAllocation $allocation)
    {
        return view('admin.allocations.edit', compact('allocation'));
    }

    /**
     * Update the specified allocation in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\BusinessCourseAllocation  $allocation
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, BusinessCourseAllocation $allocation)
    {
        $validated = $request->validate([
            'deadline' => 'nullable|date',
            'notes' => 'nullable|string|max:500'
        ]);

        $allocation->update($validated);

        return redirect()->route('admin.allocations.index')
            ->with('success', 'Course allocation updated successfully');
    }

    /**
     * Remove the specified allocation from storage.
     *
     * @param  \App\Models\BusinessCourseAllocation  $allocation
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(BusinessCourseAllocation $allocation)
    {
        $allocation->delete();

        return redirect()->route('admin.allocations.index')
            ->with('success', 'Course allocation deleted successfully');
    }

    /**
     * Display the specified allocation.
     *
     * @param  \App\Models\BusinessCourseAllocation  $allocation
     * @return \Illuminate\View\View
     */
    public function show(BusinessCourseAllocation $allocation)
    {
        $allocation->load(['employee.business', 'employee.user', 'course', 'courseProgress']);
        
        return view('admin.allocations.show', compact('allocation'));
    }
}
