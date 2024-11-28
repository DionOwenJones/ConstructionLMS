<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\User;
use App\Models\CourseAllocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminAllocationController extends Controller
{
    public function index()
    {
        $allocations = CourseAllocation::with(['user', 'course', 'allocatedBy'])
            ->latest()
            ->paginate(10);

        return view('admin.allocations.index', compact('allocations'));
    }

    public function create()
    {
        $users = User::role('user')->get();
        $courses = Course::all();

        return view('admin.allocations.create', compact('users', 'courses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'course_id' => 'required|exists:courses,id',
            'expires_at' => 'nullable|date',
            'notes' => 'nullable|string|max:500'
        ]);

        // Check for existing allocation
        $existingAllocation = CourseAllocation::where('user_id', $validated['user_id'])
            ->where('course_id', $validated['course_id'])
            ->first();

        if ($existingAllocation) {
            return back()->with('error', 'This course is already allocated to this user.');
        }

        // Create new allocation
        $allocation = CourseAllocation::create([
            'user_id' => $validated['user_id'],
            'course_id' => $validated['course_id'],
            'allocated_by' => auth()->id(),
            'allocated_at' => now(),
            'expires_at' => $validated['expires_at'] ?? null,
            'notes' => $validated['notes'] ?? null
        ]);

        // Also add the course to the user's courses
        DB::table('course_user')->insert([
            'user_id' => $validated['user_id'],
            'course_id' => $validated['course_id'],
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return redirect()->route('admin.allocations.index')
            ->with('success', 'Course allocated successfully.');
    }

    public function show(CourseAllocation $allocation)
    {
        return view('admin.allocations.show', compact('allocation'));
    }

    public function edit(CourseAllocation $allocation)
    {
        $users = User::role('user')->get();
        $courses = Course::all();

        return view('admin.allocations.edit', compact('allocation', 'users', 'courses'));
    }

    public function update(Request $request, CourseAllocation $allocation)
    {
        $validated = $request->validate([
            'expires_at' => 'nullable|date',
            'notes' => 'nullable|string|max:500'
        ]);

        $allocation->update([
            'expires_at' => $validated['expires_at'] ?? null,
            'notes' => $validated['notes'] ?? null
        ]);

        return redirect()->route('admin.allocations.index')
            ->with('success', 'Allocation updated successfully.');
    }

    public function destroy(CourseAllocation $allocation)
    {
        // Remove from course_user table
        DB::table('course_user')
            ->where('user_id', $allocation->user_id)
            ->where('course_id', $allocation->course_id)
            ->delete();

        // Delete the allocation
        $allocation->delete();

        return redirect()->route('admin.allocations.index')
            ->with('success', 'Allocation removed successfully.');
    }
}
