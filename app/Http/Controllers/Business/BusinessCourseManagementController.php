<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\User;
use App\Models\Business;
use App\Models\BusinessCoursePurchase;
use App\Models\BusinessCourseAllocation;
use App\Models\BusinessEmployee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Stripe\Exception\CardException;
use Stripe\PaymentIntent;
use Stripe\Stripe;
use Stripe\Customer;
use Stripe\PaymentMethod;
use Stripe\Exception\InvalidRequestException;
use Stripe\Exception\AuthenticationException;

class BusinessCourseManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        try {
            Stripe::setApiKey(config('services.stripe.secret'));
        } catch (\Exception $e) {
            Log::error('Stripe API key error: ' . $e->getMessage());
        }
    }

    protected function getBusiness()
    {
        $business = Business::where('user_id', Auth::id())->first();
        
        if (!$business) {
            return null;
        }

        return $business;
    }

    /**
     * Display available courses for purchase
     */
    public function available()
    {
        $business = $this->getBusiness();
        
        if (!$business) {
            return redirect()->route('business.setup')
                ->with('warning', 'Please set up your business profile first.');
        }

        $courses = Course::where('status', 'published')
            ->withCount(['businessPurchases as total_licenses' => function($query) use ($business) {
                $query->where('business_id', $business->id)
                    ->select(DB::raw('SUM(licenses_purchased)'));
            }])
            ->latest()
            ->paginate(10);

        return view('business.courses.available', compact('courses'));
    }

    /**
     * Display all course purchases
     */
    public function purchases()
    {
        $business = $this->getBusiness();
        
        if (!$business) {
            return redirect()->route('business.setup')
                ->with('warning', 'Please set up your business profile first.');
        }

        $purchases = $business->coursePurchases()
            ->with(['course'])
            ->withCount([
                'allocations as used_licenses',
                'allocations as completed_licenses' => function($query) {
                    $query->whereHas('user.completedCourses', function($q) {
                        $q->wherePivot('completed', true);
                    });
                }
            ])
            ->latest()
            ->paginate(10);

        return view('business.courses.purchases', compact('purchases'));
    }

    /**
     * Display purchased courses and their allocations
     */
    public function purchased()
    {
        $business = $this->getBusiness();
        
        if (!$business) {
            return redirect()->route('business.setup')
                ->with('warning', 'Please set up your business profile first.');
        }

        $purchases = $business->coursePurchases()
            ->with(['course', 'allocations.user'])
            ->withCount(['allocations as used_seats'])
            ->latest()
            ->paginate(10);

        return view('business.courses.purchased', compact('purchases'));
    }

    /**
     * Show course details
     */
    public function show(Course $course)
    {
        $business = $this->getBusiness();
        
        if (!$business) {
            return redirect()->route('business.setup')
                ->with('warning', 'Please set up your business profile first.');
        }

        $purchase = BusinessCoursePurchase::where('business_id', $business->id)
            ->where('course_id', $course->id)
            ->with(['allocations.user'])
            ->first();

        return view('business.courses.show', compact('course', 'purchase'));
    }

    /**
     * Show the purchase form for a course
     */
    public function purchase(Course $course)
    {
        $business = $this->getBusiness();
        
        if (!$business) {
            return redirect()->route('business.setup')
                ->with('error', 'Please set up your business profile before purchasing courses.');
        }

        return view('business.courses.purchase', [
            'course' => $course,
            'business' => $business,
            'stripeKey' => config('services.stripe.key')
        ]);
    }

    /**
     * Process the course purchase with Stripe
     */
    public function processPurchase(Request $request, Course $course)
    {
        try {
            $business = $this->getBusiness();
            
            if (!$business) {
                return response()->json([
                    'error' => 'Business profile not found.'
                ], 400);
            }

            // Log the incoming request data
            Log::info('Business payment request received', [
                'business_id' => $business->id,
                'course_id' => $course->id,
                'request_data' => $request->all()
            ]);

            // Validate request
            $request->validate([
                'licenses' => ['required', 'integer', 'min:1'],
                'payment_method_id' => ['required', 'string'],
                'discount_code' => ['nullable', 'string', 'exists:discount_codes,code']
            ]);

            // Calculate amount in cents
            $licenses = $request->licenses;
            $amount = $course->price * $licenses;
            $discountCode = null;

            // Apply discount if code is provided
            if ($request->filled('discount_code')) {
                $discountCode = \App\Models\DiscountCode::where('code', $request->discount_code)
                    ->where('is_active', true)
                    ->first();
                
                if ($discountCode && $discountCode->isValid()) {
                    $discountAmount = $amount * ($discountCode->discount_percentage / 100);
                    $amount -= $discountAmount;
                    
                    Log::info('Discount applied to business purchase', [
                        'business_id' => $business->id,
                        'course_id' => $course->id,
                        'discount_code' => $discountCode->code,
                        'discount_amount' => $discountAmount
                    ]);
                }
            }

            // Add VAT
            $vat = $amount * 0.2;
            $totalAmount = $amount + $vat;
            $amountInCents = (int)($totalAmount * 100);

            // Create or get Stripe customer
            if (!$business->stripe_id) {
                $customer = Customer::create([
                    'email' => $business->owner->email,
                    'name' => $business->name,
                    'payment_method' => $request->payment_method_id,
                    'invoice_settings' => [
                        'default_payment_method' => $request->payment_method_id
                    ]
                ]);
                $business->stripe_id = $customer->id;
                $business->save();
            }

            // Create payment intent
            $paymentIntent = PaymentIntent::create([
                'amount' => $amountInCents,
                'currency' => 'gbp',
                'customer' => $business->stripe_id,
                'payment_method' => $request->payment_method_id,
                'off_session' => true,
                'confirm' => true,
                'description' => "Business purchase of {$licenses} license(s) for course: {$course->title}",
                'metadata' => [
                    'business_id' => $business->id,
                    'course_id' => $course->id,
                    'licenses' => $licenses,
                    'type' => 'business_course_purchase'
                ]
            ]);

            if ($paymentIntent->status === 'succeeded') {
                // Create purchase record
                $purchase = BusinessCoursePurchase::create([
                    'business_id' => $business->id,
                    'course_id' => $course->id,
                    'licenses_purchased' => $licenses,
                    'price_per_license' => $course->price,
                    'total_amount' => $totalAmount,
                    'payment_id' => $paymentIntent->id,
                    'purchased_at' => now(),
                    'discount_code' => $request->discount_code ?? null
                ]);

                Log::info('Business course purchase completed successfully', [
                    'business_id' => $business->id,
                    'course_id' => $course->id,
                    'purchase_id' => $purchase->id,
                    'payment_intent_id' => $paymentIntent->id
                ]);

                return response()->json([
                    'success' => true,
                    'redirect' => route('business.courses.purchased')
                ]);
            } else {
                throw new \Exception("Payment failed with status: {$paymentIntent->status}");
            }

        } catch (CardException $e) {
            Log::error('Stripe card error', [
                'error' => $e->getMessage(),
                'business_id' => $business->id ?? null,
                'course_id' => $course->id
            ]);
            return response()->json([
                'error' => $e->getMessage()
            ], 400);
        } catch (\Exception $e) {
            Log::error('Business course purchase error', [
                'error' => $e->getMessage(),
                'business_id' => $business->id ?? null,
                'course_id' => $course->id
            ]);
            return response()->json([
                'error' => 'An error occurred while processing your payment. Please try again.'
            ], 500);
        }
    }

    /**
     * Allocate licenses to employees
     */
    public function allocate(Request $request, Course $course)
    {
        $business = $this->getBusiness();
        
        if (!$business) {
            return response()->json([
                'error' => 'Business profile not found.'
            ], 400);
        }

        $request->validate([
            'employee_ids' => ['required', 'array'],
            'employee_ids.*' => ['exists:business_employees,id']
        ]);

        try {
            DB::beginTransaction();

            $purchase = BusinessCoursePurchase::where('business_id', $business->id)
                ->where('course_id', $course->id)
                ->firstOrFail();

            $currentAllocations = $purchase->allocations()->count();
            $newAllocations = count($request->employee_ids);

            if ($currentAllocations + $newAllocations > $purchase->licenses_purchased) {
                throw new \Exception('Not enough licenses available.');
            }

            foreach ($request->employee_ids as $employeeId) {
                BusinessCourseAllocation::create([
                    'business_course_purchase_id' => $purchase->id,
                    'business_employee_id' => $employeeId,
                    'allocated_at' => now()
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Licenses allocated successfully.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Deallocate a license from an employee
     */
    public function deallocate(Course $course, BusinessEmployee $employee)
    {
        $business = $this->getBusiness();
        
        if (!$business) {
            return response()->json([
                'error' => 'Business profile not found.'
            ], 400);
        }

        try {
            $purchase = BusinessCoursePurchase::where('business_id', $business->id)
                ->where('course_id', $course->id)
                ->firstOrFail();

            $allocation = BusinessCourseAllocation::where('business_course_purchase_id', $purchase->id)
                ->where('business_employee_id', $employee->id)
                ->firstOrFail();

            $allocation->delete();

            return response()->json([
                'success' => true,
                'message' => 'License deallocated successfully.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Show allocation form
     */
    public function showAllocationForm(BusinessCoursePurchase $purchase)
    {
        $business = $this->getBusiness();
        
        if (!$business) {
            return redirect()->route('business.setup')
                ->with('warning', 'Please set up your business profile first.');
        }

        if ($purchase->business_id !== $business->id) {
            abort(403, 'Unauthorized action.');
        }

        $purchase->load(['course', 'allocations.user']);
        
        $availableEmployees = $business->employees()
            ->with('user.courses')
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
    public function allocateOld(Request $request, BusinessCoursePurchase $purchase)
    {
        try {
            DB::beginTransaction();

            $request->validate([
                'employee_ids' => ['required', 'array'],
                'employee_ids.*' => ['exists:users,id']
            ]);

            $business = $this->getBusiness();
            
            if (!$business) {
                throw new \Exception('Business profile not found.');
            }

            if ($purchase->business_id !== $business->id) {
                throw new \Exception('Unauthorized access to purchase.');
            }

            $course = $purchase->course;

            foreach ($request->employee_ids as $employeeId) {
                $employee = User::findOrFail($employeeId);
                
                // Check if employee already has this course allocated
                if (!BusinessCourseAllocation::where('business_course_purchase_id', $purchase->id)
                    ->where('user_id', $employeeId)
                    ->exists()) {
                    
                    // Create business course allocation record
                    BusinessCourseAllocation::create([
                        'business_course_purchase_id' => $purchase->id,
                        'user_id' => $employeeId,
                        'allocated_at' => now()
                    ]);

                    // Attach course to user if not already attached
                    if (!$employee->courses()->where('course_id', $course->id)->exists()) {
                        $employee->courses()->attach($course->id, [
                            'business_id' => $business->id,
                            'course_purchase_id' => $purchase->id
                        ]);
                    }
                }
            }

            DB::commit();
            return redirect()->route('business.courses.purchases')
                ->with('success', 'Course allocated to selected employees successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Course allocation error: ' . $e->getMessage());
            return back()->with('error', 'Failed to allocate course: ' . $e->getMessage());
        }
    }

    /**
     * View all allocations
     */
    public function allocations()
    {
        $business = $this->getBusiness();
        
        if (!$business) {
            return redirect()->route('business.setup')
                ->with('warning', 'Please set up your business profile first.');
        }

        $allocations = $business->courseAllocations()
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
        $business = $this->getBusiness();
        
        if (!$business) {
            return redirect()->route('business.setup')
                ->with('warning', 'Please set up your business profile first.');
        }

        if ($allocation->purchase->business_id !== $business->id) {
            abort(403, 'Unauthorized action.');
        }

        $allocation->delete();

        return back()->with('success', 'Course allocation removed successfully');
    }
}
