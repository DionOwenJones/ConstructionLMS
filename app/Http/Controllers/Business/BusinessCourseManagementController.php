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
        $business = Business::where('owner_id', Auth::id())->first();
        
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
            ->withCount(['businessPurchases as total_seats' => function($query) use ($business) {
                $query->where('business_id', $business->id)
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

        return view('business.courses.purchases', compact('purchases'));
    }

    /**
     * Show the purchase form for a course
     */
    public function showPurchaseForm(Course $course)
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
    public function purchaseCourse(Request $request, Course $course)
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
                'seats' => ['required', 'integer', 'min:1'],
                'payment_method_id' => ['required', 'string']
            ]);

            // Calculate amount in cents
            $seats = $request->seats;
            $amount = $course->price * $seats;
            $amountInCents = (int)($amount * 100);

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
                'description' => "Business purchase of {$seats} seat(s) for course: {$course->title}",
                'metadata' => [
                    'business_id' => $business->id,
                    'course_id' => $course->id,
                    'seats' => $seats,
                    'type' => 'business_course_purchase'
                ]
            ]);

            if ($paymentIntent->status === 'succeeded') {
                // Create purchase record
                $purchase = BusinessCoursePurchase::create([
                    'business_id' => $business->id,
                    'course_id' => $course->id,
                    'seats_purchased' => $seats,
                    'price_per_seat' => $course->price,
                    'total_amount' => $course->price * $seats,
                    'payment_id' => $paymentIntent->id,
                    'purchased_at' => now()
                ]);

                Log::info('Business course purchase completed successfully', [
                    'business_id' => $business->id,
                    'course_id' => $course->id,
                    'purchase_id' => $purchase->id,
                    'payment_intent_id' => $paymentIntent->id
                ]);

                return response()->json([
                    'success' => true,
                    'redirect' => route('business.courses.purchases')
                ]);
            } else {
                throw new \Exception("Payment failed with status: {$paymentIntent->status}");
            }

        } catch (CardException $e) {
            Log::error('Stripe card error', [
                'business_id' => $business->id ?? null,
                'course_id' => $course->id,
                'error' => $e->getMessage(),
                'code' => $e->getStripeCode(),
                'decline_code' => $e->getDeclineCode(),
            ]);

            return response()->json([
                'error' => $e->getMessage()
            ], 422);

        } catch (\Exception $e) {
            Log::error('Business course purchase error', [
                'business_id' => $business->id ?? null,
                'course_id' => $course->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => $e->getMessage()
            ], 500);
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
    public function allocate(Request $request, BusinessCoursePurchase $purchase)
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
                
                if (!$employee->courses->contains($course->id)) {
                    // Create business course allocation record
                    BusinessCourseAllocation::create([
                        'business_course_purchase_id' => $purchase->id,
                        'user_id' => $employeeId,
                        'allocated_at' => now()
                    ]);

                    // Attach course to user
                    $employee->courses()->attach($course->id, [
                        'business_id' => $business->id,
                        'course_purchase_id' => $purchase->id
                    ]);

                    if (config('mail.enabled')) {
                        try {
                            Mail::to($employee->email)->send(new CourseAllocated($course, $employee, $business->name));
                        } catch (\Exception $e) {
                            Log::error('Failed to send course allocation email: ' . $e->getMessage());
                        }
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
