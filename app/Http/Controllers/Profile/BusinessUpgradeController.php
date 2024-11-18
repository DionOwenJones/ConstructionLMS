<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Business;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class BusinessUpgradeController extends Controller
{
    public function upgrade()
    {
        $user = Auth::user();
        
        if ($user->role !== User::ROLE_USER) {
            return redirect()->back()->with('error', 'You are not eligible for a business upgrade.');
        }

        try {
            DB::beginTransaction();

            // Create a new business
            $business = Business::create([
                'company_name' => $user->name . "'s Business", // Default name, can be updated later
                'owner_id' => $user->id,
            ]);

            // Update user role and business_id in users table
            $user->role = User::ROLE_BUSINESS;
            $user->business_id = $business->id;
            $user->save();

            // Get or create the business role
            $businessRole = Role::firstOrCreate(
                ['name' => User::ROLE_BUSINESS],
                ['guard_name' => 'web']
            );

            // Assign the role using Spatie
            $user->syncRoles([$businessRole]);

            DB::commit();

            return redirect()->route('business.setup')->with('status', 'Your account has been upgraded to a Business account! Please complete your business setup.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to upgrade account. Please try again.');
        }
    }
}
