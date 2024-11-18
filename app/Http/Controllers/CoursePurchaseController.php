<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CoursePurchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Stripe\Exception\CardException;
use Stripe\PaymentIntent;
use Stripe\Stripe;
use Stripe\Customer;

class CoursePurchaseController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        Stripe::setApiKey(config('services.stripe.secret'));
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
     * Process the course purchase with Stripe.
     */
    public function purchase(Request $request, Course $course)
    {
        try {
            $user = Auth::user();
            
            // Log the incoming request data
            Log::info('Payment request received', [
                'user_id' => $user->id,
                'course_id' => $course->id,
                'request_data' => $request->all()
            ]);

            // Check if already purchased
            if ($course->isPurchasedBy($user)) {
                return response()->json([
                    'error' => 'You have already purchased this course.'
                ], 400);
            }

            // Get payment method ID
            $paymentMethodId = $request->input('payment_method_id');
            if (empty($paymentMethodId)) {
                return response()->json([
                    'error' => 'Payment method is required.'
                ], 400);
            }

            // Calculate amount in cents
            $amount = (int)($course->price * 100);

            // Create or get Stripe customer
            if (!$user->stripe_id) {
                $customer = Customer::create([
                    'email' => $user->email,
                    'name' => $user->name,
                    'payment_method' => $paymentMethodId,
                    'invoice_settings' => [
                        'default_payment_method' => $paymentMethodId
                    ]
                ]);
                $user->stripe_id = $customer->id;
                $user->save();
            }

            // Create payment intent
            $paymentIntent = PaymentIntent::create([
                'amount' => $amount,
                'currency' => 'usd',
                'customer' => $user->stripe_id,
                'payment_method' => $paymentMethodId,
                'off_session' => true,
                'confirm' => true,
                'description' => "Purchase of course: {$course->title}",
                'metadata' => [
                    'course_id' => $course->id,
                    'user_id' => $user->id
                ]
            ]);

            if ($paymentIntent->status === 'succeeded') {
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

                Log::info('Course purchase completed successfully', [
                    'user_id' => $user->id,
                    'course_id' => $course->id,
                    'purchase_id' => $purchase->id,
                    'payment_intent_id' => $paymentIntent->id
                ]);

                // Get the first section of the course
                $firstSection = $course->sections()->orderBy('order')->first();
                
                if ($firstSection) {
                    return response()->json([
                        'success' => true,
                        'redirect' => route('courses.show.section', [
                            'course' => $course->id,
                            'section' => $firstSection->id
                        ])
                    ]);
                } else {
                    // If no sections, show empty course view
                    return response()->json([
                        'success' => true,
                        'redirect' => route('courses.show', [
                            'course' => $course->id
                        ])
                    ]);
                }
            } else {
                throw new \Exception("Payment failed with status: {$paymentIntent->status}");
            }

        } catch (CardException $e) {
            Log::error('Stripe card error', [
                'user_id' => Auth::id(),
                'course_id' => $course->id,
                'error' => $e->getMessage(),
                'code' => $e->getStripeCode(),
                'decline_code' => $e->getDeclineCode(),
            ]);

            return response()->json([
                'error' => $e->getMessage()
            ], 422);

        } catch (\Exception $e) {
            Log::error('Course purchase error', [
                'user_id' => Auth::id(),
                'course_id' => $course->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
