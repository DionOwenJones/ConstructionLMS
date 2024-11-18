<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\User;
use App\Models\Business;
use App\Models\BusinessCoursePurchase;
use App\Models\BusinessCourseAllocation;
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
                ->with('warning', 'Please set up your business profile first.');
        }

        return view('business.courses.purchase', [
            'course' => $course,
            'stripeKey' => config('services.stripe.key')
        ]);
    }

    /**
     * Process the course purchase with Stripe
     */
    public function purchaseCourse(Request $request, Course $course)
    {
        try {
            DB::beginTransaction();

            $request->validate([
                'seats' => ['required', 'integer', 'min:1'],
                'payment_method' => ['required', 'string']
            ]);

            $business = $this->getBusiness();
            
            if (!$business) {
                throw new \Exception('Business profile not found.');
            }

            $seats = $request->seats;
            $amount = $course->price * $seats;
            $amountInCents = (int)($amount * 100);

            // Create or get Stripe Customer for the business
            if (!$business->stripe_id) {
                $customer = Customer::create([
                    'email' => $business->owner->email,
                    'name' => $business->name,
                    'metadata' => [
                        'business_id' => $business->id,
                        'owner_id' => $business->owner_id
                    ]
                ]);
                $business->stripe_id = $customer->id;
                $business->save();
            }

            // Retrieve the payment method
            $paymentMethod = PaymentMethod::retrieve($request->payment_method);
            
            // Attach payment method to customer if not already attached
            try {
                $paymentMethod->attach(['customer' => $business->stripe_id]);
            } catch (\Exception $e) {
                // Ignore if already attached
                if (!str_contains($e->getMessage(), 'already been attached')) {
                    throw $e;
                }
            }

            // Create Payment Intent
            $paymentIntent = PaymentIntent::create([
                'amount' => $amountInCents,
                'currency' => 'gbp',
                'customer' => $business->stripe_id,
                'payment_method' => $request->payment_method,
                'confirm' => true,
                'metadata' => [
                    'business_id' => $business->id,
                    'course_id' => $course->id,
                    'seats' => $seats,
                    'type' => 'business_course_purchase'
                ],
                'description' => "Business purchase of {$seats} seat(s) for course: {$course->title}"
            ]);

            if ($paymentIntent->status !== 'succeeded') {
                throw new \Exception('Payment was not successful. Status: ' . $paymentIntent->status);
            }

            // Create purchase record
            BusinessCoursePurchase::create([
                'business_id' => $business->id,
                'course_id' => $course->id,
                'seats_purchased' => $seats,
                'amount_paid' => $amount,
                'payment_id' => $paymentIntent->id,
                'purchased_at' => now()
            ]);

            DB::commit();

            // Send confirmation email if enabled
            if (config('mail.enabled')) {
                try {
                    Mail::to($business->owner->email)->send(new CoursePurchased($course, $business, $seats));
                } catch (\Exception $e) {
                    Log::error('Failed to send purchase confirmation email: ' . $e->getMessage());
                }
            }

            return redirect()->route('business.courses.purchases')
                ->with('success', 'Course purchased successfully. You can now allocate it to your employees.');

        } catch (CardException $e) {
            DB::rollBack();
            Log::error('Stripe card error: ' . $e->getMessage());
            return back()->with('error', 'Payment failed: ' . $e->getMessage())
                ->withInput();
            
        } catch (InvalidRequestException $e) {
            DB::rollBack();
            Log::error('Stripe invalid request: ' . $e->getMessage(), [
                'payment_method' => $request->payment_method,
                'amount' => $amountInCents ?? null,
                'error' => $e->getMessage()
            ]);
            return back()->with('error', 'Invalid payment request. Please check your card details and try again.')
                ->withInput();
            
        } catch (AuthenticationException $e) {
            DB::rollBack();
            Log::error('Stripe authentication error: ' . $e->getMessage());
            return back()->with('error', 'Payment system authentication error. Please contact support.')
                ->withInput();
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Course purchase error: ' . $e->getMessage());
            return back()->with('error', $e->getMessage())
                ->withInput();
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
