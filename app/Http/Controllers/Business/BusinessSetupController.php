<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Models\Business;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BusinessSetupController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function show()
    {
        // If user already has a business, redirect to dashboard
        if (Auth::user()->business) {
            return redirect()->route('business.dashboard');
        }

        return view('business.setup');
    }

    public function store(Request $request)
    {
        // Validate request
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
        ]);

        // Create business
        $business = Business::create([
            'company_name' => $validated['company_name'],
            'owner_id' => Auth::id(),
            'user_id' => Auth::id(), // Adding both owner_id and user_id during transition
        ]);

        // Assign role to user
        Auth::user()->assignRole('business');

        // Update user's business relationship
        Auth::user()->business()->associate($business);
        Auth::user()->save();

        return redirect()->route('business.dashboard')
            ->with('success', 'Business profile created successfully!');
    }
}
