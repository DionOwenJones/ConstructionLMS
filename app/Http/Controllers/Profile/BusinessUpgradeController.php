<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Business;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;

class BusinessUpgradeController extends Controller
{
    public function upgrade()
    {
        $user = Auth::user();
        
        if ($user->role !== User::ROLE_USER) {
            return redirect()->back()->with('error', 'You are not eligible for a business upgrade.');
        }

        // Remove all user's course enrollments and certificates first
        try {
            DB::beginTransaction();

            // Remove all user's course enrollments
            DB::table('course_user')->where('user_id', $user->id)->delete();
            
            // Remove any certificates
            DB::table('certificates')->where('user_id', $user->id)->delete();

            // Update user role but don't set business_id yet
            $user->role = User::ROLE_BUSINESS;
            $user->save();

            // Ensure the business role exists
            $businessRole = Role::firstOrCreate(['name' => User::ROLE_BUSINESS], [
                'guard_name' => 'web',
                'name' => User::ROLE_BUSINESS
            ]);

            // Remove any existing roles and assign the business role
            $user->roles()->detach();
            $user->assignRole($businessRole);

            DB::commit();

            // Redirect to business setup form
            return redirect()->route('profile.upgrade.business.setup')
                ->with('status', 'Please complete your business profile to finish the upgrade process.');
            
        } catch (\Exception $e) {
            Log::error('Business upgrade failed: ' . $e->getMessage());
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to upgrade account. Please try again. Error: ' . $e->getMessage());
        }
    }

    public function setup()
    {
        return view('profile.business-setup');
    }

    public function complete(Request $request)
    {
        $user = Auth::user();
        
        if ($user->role !== User::ROLE_BUSINESS) {
            return redirect()->back()->with('error', 'Invalid account state for business setup.');
        }

        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'address' => 'required|string|max:1000',
            'phone' => 'required|string|max:20',
            'website' => 'nullable|url|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            // Create the business
            $business = Business::create([
                'name' => $validated['company_name'],
                'user_id' => $user->id,
                'address' => $validated['address'],
                'phone' => $validated['phone'],
                'website' => $validated['website'],
                'description' => $validated['description'],
            ]);

            // Update user with business_id
            $user->business_id = $business->id;
            $user->save();

            DB::commit();

            return redirect()->route('business.dashboard')
                ->with('success', 'Business profile created successfully!');

        } catch (\Exception $e) {
            Log::error('Business setup failed: ' . $e->getMessage());
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to complete business setup. Please try again.');
        }
    }

    public function legacy()
    {
        return view('profile.business.legacy');
    }

    public function storeLegacy(Request $request)
    {
        $request->validate([
            'business_name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
        ]);

        try {
            DB::beginTransaction();

            $user = Auth::user();
            
            // Update user role to business
            $user->role = User::ROLE_BUSINESS;
            
            // Create or update business profile
            $business = Business::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'name' => $request->business_name,
                    'address' => $request->address,
                    'phone' => $request->phone,
                ]
            );

            // Set the business_id on the user
            $user->business_id = $business->id;
            $user->save();

            DB::commit();

            return redirect()->route('business.dashboard')
                ->with('success', 'Your business account has been successfully set up!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Failed to set up business account. Please try again.');
        }
    }

    public function downgrade()
    {
        $user = Auth::user();
        
        if ($user->role !== User::ROLE_BUSINESS) {
            return redirect()->back()->with('error', 'Only business accounts can be downgraded.');
        }

        try {
            DB::beginTransaction();

            // Get the business ID before we remove it from the user
            $businessId = $user->business_id;

            // Remove business role and set back to regular user
            $user->role = User::ROLE_USER;
            $user->business_id = null;
            $user->save();

            // Ensure the user role exists
            $userRole = Role::firstOrCreate(['name' => User::ROLE_USER], [
                'guard_name' => 'web',
                'name' => User::ROLE_USER
            ]);

            // Remove business role and assign user role
            $user->removeRole(User::ROLE_BUSINESS);
            $user->assignRole($userRole);

            if ($businessId) {
                try {
                    // Remove all course purchases by this business if the table exists
                    if (Schema::hasTable('business_course')) {
                        DB::table('business_course')->where('business_id', $businessId)->delete();
                    }
                } catch (\Exception $e) {
                    Log::warning('Failed to delete business course records: ' . $e->getMessage());
                    // Continue with the downgrade process even if this fails
                }
                
                // Remove all employee associations
                User::where('business_id', $businessId)
                    ->where('id', '!=', $user->id)
                    ->update(['business_id' => null, 'role' => User::ROLE_USER]);
                
                // Delete the business
                Business::destroy($businessId);
            }

            DB::commit();

            return redirect()->route('dashboard')
                ->with('success', 'Your account has been successfully downgraded to a regular user account.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Account downgrade failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to downgrade account. Please try again. Error: ' . $e->getMessage());
        }
    }
}
