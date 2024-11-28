<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BusinessSetupController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:business')->except(['show', 'store']);
    }

    public function show()
    {
        $user = Auth::user();
        
        if (!$user->isBusiness()) {
            return redirect()->route('dashboard');
        }

        $business = Business::where('user_id', $user->id)->first();

        if ($business && $business->name) {
            return redirect()->route('business.dashboard');
        }

        return view('business.setup');
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->isBusiness()) {
            return redirect()->route('dashboard');
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
        ]);

        DB::beginTransaction();

        try {
            $business = Business::updateOrCreate(
                ['user_id' => Auth::id()],
                [
                    'name' => $request->name,
                    'email' => $request->email,
                    'company_name' => $request->name,
                ]
            );

            DB::commit();

            return redirect()->route('business.dashboard')
                ->with('success', 'Business profile setup completed successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'There was an error setting up your business profile. Please try again.');
        }
    }
}
