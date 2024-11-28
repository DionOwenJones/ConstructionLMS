<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\CoursePurchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Notifications\CoursePurchaseNotification;
use Stripe\Exception\CardException;
use Stripe\Stripe;
use Stripe\Customer;
use Stripe\PaymentIntent;
use Stripe\PaymentMethod;
use Stripe\StripeClient;

class CoursePurchaseController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    /**
     * Show the purchase form for a course.
     */
    public function showPurchaseForm(Course $course)
    {
        if ($course->isPurchasedBy(Auth::user())) {
            return redirect()->route('courses.show', $course)
                ->with('message', 'You have already purchased this course.');
        }

        return view('courses.purchase', [
            'course' => $course,
            'intent' => Auth::user()->createSetupIntent()
        ]);
    }

    /**
     * Show the purchase confirmation page.
     */
    public function show(Course $course)
    {
        if ($course->isPurchasedBy(Auth::user())) {
            return redirect()->route('courses.preview', ['course' => $course->id])
                ->with('message', 'You have already purchased this course.');
        }

        return view('courses.purchase', [
            'course' => $course,
            'intent' => Auth::user()->createSetupIntent()
        ]);
    }

    /**
     * Process the course purchase.
     */
    public function purchase(Course $course)
    {
        return redirect()->route('courses.purchase.form', $course);
    }

    /**
     * Show the checkout page.
     */
    public function checkout(Course $course)
    {
        if ($course->isPurchasedBy(Auth::user())) {
            return redirect()->route('courses.show', $course)
                ->with('message', 'You have already purchased this course.');
        }

        return view('courses.purchase', [
            'course' => $course,
            'intent' => Auth::user()->createSetupIntent()
        ]);
    }

    /**
     * Handle successful purchase.
     */
    public function success(Course $course)
    {
        return redirect()->route('courses.show', $course)
            ->with('success', 'Course purchased successfully! You now have full access to all content.');
    }

    /**
     * Handle cancelled purchase.
     */
    public function cancel(Course $course)
    {
        return redirect()->route('courses.show', $course)
            ->with('error', 'Course purchase was cancelled.');
    }

    /**
     * Process the course purchase with Stripe.
     */
    public function processPurchase(Request $request, Course $course)
    {
        try {
            Log::info('Starting payment process', [
                'user_id' => auth()->id(),
                'course_id' => $course->id,
                'request_data' => $request->all()
            ]);

            $request->validate([
                'payment_method_id' => 'required|string',
                'discount_code' => 'nullable|string|exists:discount_codes,code'
            ]);

            $paymentMethodId = $request->input('payment_method_id');
            $discountCode = $request->input('discount_code');
            $user = auth()->user();
            
            Log::info('Payment method validated', [
                'payment_method_id' => $paymentMethodId,
                'discount_code' => $discountCode,
                'user_id' => $user->id
            ]);

            // Check if already purchased
            if ($course->isPurchasedBy($user)) {
                return response()->json([
                    'success' => false,
                    'error' => 'You have already purchased this course.'
                ], 400);
            }

            // Calculate price with discount if applicable
            $price = $course->price;
            if ($discountCode) {
                $discount = \App\Models\DiscountCode::where('code', $discountCode)->first();
                if ($discount && $discount->isValid()) {
                    $price = $price * (1 - ($discount->discount_percentage / 100));
                }
            }

            // Add VAT (20%)
            $priceWithVAT = $price * 1.2;

            // Initialize Stripe with explicit error handling
            try {
                $stripe = new \Stripe\StripeClient(config('services.stripe.secret'));
                Log::info('Stripe client initialized successfully');
            } catch (\Exception $e) {
                Log::error('Failed to initialize Stripe client', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                throw new \Exception('Failed to initialize payment processor');
            }

            // Get or create customer with better error handling
            if (!$user->stripe_id) {
                Log::info('Creating new Stripe customer', ['user_id' => $user->id]);
                try {
                    $customer = $stripe->customers->create([
                        'email' => $user->email,
                        'payment_method' => $paymentMethodId,
                        'invoice_settings' => [
                            'default_payment_method' => $paymentMethodId,
                        ],
                    ]);
                    $user->stripe_id = $customer->id;
                    $user->save();
                    Log::info('Created Stripe customer', ['stripe_customer_id' => $customer->id]);
                } catch (\Exception $e) {
                    Log::error('Error creating Stripe customer', [
                        'error' => $e->getMessage(),
                        'error_type' => get_class($e),
                        'user_id' => $user->id,
                        'trace' => $e->getTraceAsString()
                    ]);
                    throw new \Exception('Failed to create customer profile: ' . $e->getMessage());
                }
            }

            try {
                Log::info('Attaching payment method to customer', [
                    'payment_method_id' => $paymentMethodId,
                    'customer_id' => $user->stripe_id
                ]);
                // Attach the payment method to the customer if not already attached
                $stripe->paymentMethods->attach($paymentMethodId, [
                    'customer' => $user->stripe_id,
                ]);
            } catch (\Exception $e) {
                // Only ignore if already attached
                if (!str_contains($e->getMessage(), 'already been attached')) {
                    Log::error('Error attaching payment method', [
                        'error' => $e->getMessage(),
                        'error_type' => get_class($e),
                        'payment_method_id' => $paymentMethodId,
                        'customer_id' => $user->stripe_id,
                        'trace' => $e->getTraceAsString()
                    ]);
                    throw $e;
                }
            }

            Log::info('Creating payment intent', [
                'amount' => (int)($priceWithVAT * 100),
                'customer_id' => $user->stripe_id,
                'original_price' => $course->price,
                'discount_code' => $discountCode,
                'final_price' => $priceWithVAT
            ]);

            // Create PaymentIntent with better error handling
            try {
                $paymentIntent = $stripe->paymentIntents->create([
                    'amount' => (int)($priceWithVAT * 100),
                    'currency' => 'gbp',
                    'customer' => $user->stripe_id,
                    'payment_method' => $paymentMethodId,
                    'off_session' => true,
                    'confirm' => true,
                    'metadata' => [
                        'course_id' => $course->id,
                        'user_id' => $user->id,
                        'discount_code' => $discountCode,
                        'original_price' => $course->price,
                        'final_price' => $priceWithVAT
                    ]
                ]);

                Log::info('Payment intent created', [
                    'payment_intent_id' => $paymentIntent->id,
                    'status' => $paymentIntent->status,
                    'amount' => $paymentIntent->amount
                ]);
            } catch (\Stripe\Exception\CardException $e) {
                Log::error('Stripe card error during payment intent creation', [
                    'error' => $e->getMessage(),
                    'error_type' => get_class($e),
                    'decline_code' => $e->getDeclineCode(),
                    'user_id' => $user->id,
                    'trace' => $e->getTraceAsString()
                ]);
                throw $e;
            } catch (\Exception $e) {
                Log::error('Error creating payment intent', [
                    'error' => $e->getMessage(),
                    'error_type' => get_class($e),
                    'user_id' => $user->id,
                    'trace' => $e->getTraceAsString()
                ]);
                throw new \Exception('Failed to process payment: ' . $e->getMessage());
            }

            if ($paymentIntent->status === 'requires_action') {
                Log::warning('Payment requires additional action', [
                    'payment_intent_id' => $paymentIntent->id
                ]);
                return response()->json([
                    'success' => false,
                    'error' => 'This card requires additional authentication. Please try a different card.'
                ]);
            }

            if ($paymentIntent->status !== 'succeeded') {
                Log::error('Payment intent failed', [
                    'payment_intent_id' => $paymentIntent->id,
                    'status' => $paymentIntent->status
                ]);
                return response()->json([
                    'success' => false,
                    'error' => 'Payment was not successful. Please try again.'
                ]);
            }

            // Create purchase record
            $purchase = CoursePurchase::create([
                'user_id' => $user->id,
                'course_id' => $course->id,
                'original_price' => $course->price,
                'discount_amount' => $discountCode ? ($course->price * ($discount->discount_percentage / 100)) : 0,
                'discount_code_id' => $discountCode ? $discount->id : null,
                'amount_paid' => $priceWithVAT,
                'status' => 'completed',
                'purchased_at' => now(),
                'payment_id' => $paymentIntent->id,
                'payment_method' => 'stripe'
            ]);

            if ($discountCode) {
                $discount->incrementUsage();
            }

            // Attach user to course
            $course->users()->attach($user->id, [
                'completed' => false,
                'completed_at' => null,
                'current_section_id' => null,
                'last_accessed_at' => now(),
                'completed_sections' => '[]',
                'completed_sections_count' => 0
            ]);

            // Send email notification
            $user->notify(new CoursePurchaseNotification($course));

            Log::info('Course purchase completed successfully', [
                'user_id' => $user->id,
                'course_id' => $course->id,
                'purchase_id' => $purchase->id,
                'payment_intent_id' => $paymentIntent->id
            ]);

            return response()->json([
                'success' => true,
                'redirect' => route('courses.show', $course)
            ]);

        } catch (CardException $e) {
            Log::error('Stripe card error: ' . $e->getMessage(), [
                'user_id' => auth()->id(),
                'course_id' => $course->id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ]);
            
        } catch (\Exception $e) {
            Log::error('Course purchase error: ' . $e->getMessage(), [
                'user_id' => auth()->id(),
                'course_id' => $course->id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'An error occurred while processing your payment. Please try again.'
            ]);
        }
    }

    /**
     * Process the course purchase with PayPal.
     */
    public function processPayPalPayment(Request $request, Course $course)
    {
        try {
            // Validate the PayPal payment details
            $request->validate([
                'orderID' => 'required|string',
                'paymentDetails' => 'required|array',
            ]);

            // Verify the payment amount matches the course price
            $paidAmount = $request->input('paymentDetails.purchase_units.0.amount.value');
            $expectedAmount = number_format($course->price * 1.2, 2, '.', '');

            if ($paidAmount != $expectedAmount) {
                throw new \Exception('Payment amount mismatch');
            }

            // Create the purchase record
            $purchase = CoursePurchase::create([
                'user_id' => auth()->id(),
                'course_id' => $course->id,
                'amount_paid' => $course->price,
                'payment_method' => 'paypal',
                'payment_id' => $request->input('orderID'),
                'status' => 'completed',
            ]);

            // Attach user to course
            $user = auth()->user();
            $course->users()->attach($user->id, [
                'completed' => false,
                'completed_at' => null,
                'current_section_id' => null,
                'last_accessed_at' => now(),
                'completed_sections' => '[]',
                'completed_sections_count' => 0
            ]);

            return response()->json([
                'success' => true,
                'redirect' => route('courses.show', $course),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 400);
        }
    }
}
