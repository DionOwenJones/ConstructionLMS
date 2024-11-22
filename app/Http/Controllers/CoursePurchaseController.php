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
            $request->validate([
                'payment_method_id' => 'required|string'
            ]);

            $paymentMethodId = $request->input('payment_method_id');
            $user = auth()->user();
            
            // Check if already purchased
            if ($course->isPurchasedBy($user)) {
                return response()->json([
                    'success' => false,
                    'error' => 'You have already purchased this course.'
                ], 400);
            }

            $stripe = new \Stripe\StripeClient(config('services.stripe.secret'));

            // Get or create customer
            if (!$user->stripe_id) {
                $customer = $stripe->customers->create([
                    'email' => $user->email,
                    'payment_method' => $paymentMethodId,
                    'invoice_settings' => [
                        'default_payment_method' => $paymentMethodId,
                    ],
                ]);
                $user->stripe_id = $customer->id;
                $user->save();
            } else {
                try {
                    // Attach the payment method to the customer
                    $stripe->paymentMethods->attach($paymentMethodId, [
                        'customer' => $user->stripe_id,
                    ]);
                } catch (\Exception $e) {
                    // If attachment fails, it might be already attached or customer might not exist
                    // We can proceed anyway as the payment intent creation will handle any issues
                    Log::warning('Payment method attachment warning: ' . $e->getMessage());
                }
            }

            // Create PaymentIntent
            $paymentIntent = $stripe->paymentIntents->create([
                'amount' => (int)($course->price * 100), // Convert to cents
                'currency' => 'gbp',
                'customer' => $user->stripe_id,
                'payment_method' => $paymentMethodId,
                'off_session' => true,
                'confirm' => true,
                'metadata' => [
                    'course_id' => $course->id,
                    'user_id' => $user->id
                ]
            ]);

            if ($paymentIntent->status !== 'succeeded') {
                throw new \Exception('Payment was not successful. Status: ' . $paymentIntent->status);
            }

            // Create purchase record
            $purchase = CoursePurchase::create([
                'user_id' => $user->id,
                'course_id' => $course->id,
                'amount_paid' => $course->price,
                'status' => 'completed',
                'purchased_at' => now(),
                'payment_id' => $paymentIntent->id,
                'payment_method' => 'stripe'
            ]);

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

        } catch (\Stripe\Exception\CardException $e) {
            Log::error('Stripe Card Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Payment Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'An error occurred while processing your payment. Please try again.'
            ], 500);
        }
    }
}
