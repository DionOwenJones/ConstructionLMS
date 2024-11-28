@extends('layouts.app')

@section('styles')
<style>
    .payment-form {
        background: white;
        padding: 30px;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
    .StripeElement {
        background-color: white;
        padding: 12px 16px;
        border: 1.5px solid #E5E7EB;
        border-radius: 8px;
        font-size: 16px;
        transition: all 0.2s ease;
    }

    .StripeElement--focus {
        border-color: #EA580C;
        box-shadow: 0 0 0 2px rgba(234, 88, 12, 0.1);
    }

    .StripeElement--invalid {
        border-color: #dc2626;
    }

    .StripeElement--complete {
        border-color: #059669;
    }

    #card-errors {
        margin-top: 8px;
        color: #dc2626;
        font-size: 0.875rem;
        min-height: 1.25rem;
    }

    .course-feature {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.75rem 0;
    }

    .course-feature svg {
        flex-shrink: 0;
    }

    @media (max-width: 640px) {
        .payment-form {
            padding: 20px;
        }
        
        .StripeElement {
            padding: 10px 14px;
            font-size: 14px;
        }
    }
</style>
@endsection

@section('content')
<div class="min-h-screen bg-gradient-to-b from-gray-50 to-white py-6 sm:py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 lg:gap-8">
            <!-- Course Information -->
            <div class="space-y-6 lg:space-y-8 order-2 lg:order-1">
                <!-- Course Header -->
                <div class="bg-white rounded-2xl shadow-sm p-6 sm:p-8">
                    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-6 mb-6">
                        <div class="flex-1">
                            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">{{ $course->title }}</h1>
                            <p class="mt-2 text-base sm:text-lg text-gray-600">{{ Str::limit($course->description, 150) }}</p>
                        </div>
                        <div class="flex items-center justify-center h-16 w-16 rounded-2xl bg-orange-50 flex-shrink-0 mx-auto sm:mx-0">
                            <svg class="h-8 w-8 text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </div>
                    </div>

                    <!-- Course Features -->
                    <div class="border-t border-gray-100 pt-6 mt-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Course Features</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div class="course-feature">
                                <svg class="h-5 w-5 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span class="text-gray-600">Self-paced learning</span>
                            </div>
                            <div class="course-feature">
                                <svg class="h-5 w-5 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span class="text-gray-600">Certificate upon completion</span>
                            </div>
                            <div class="course-feature">
                                <svg class="h-5 w-5 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z" />
                                </svg>
                                <span class="text-gray-600">Lifetime access</span>
                            </div>
                            <div class="course-feature">
                                <svg class="h-5 w-5 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                </svg>
                                <span class="text-gray-600">Mobile-friendly content</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Price Summary -->
                <div class="bg-white rounded-2xl shadow-sm p-6 sm:p-8">
                    <h3 class="text-lg font-semibold text-gray-900 mb-6">Order Summary</h3>
                    
                    <!-- Course Details -->
                    <div class="flex items-start space-x-4 mb-6">
                        <div class="flex-shrink-0 w-16 h-16 bg-orange-100 rounded-lg flex items-center justify-center">
                            <svg class="h-8 w-8 text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h4 class="font-medium text-gray-900">{{ $course->title }}</h4>
                            <p class="text-sm text-gray-500 mt-1">Full course access</p>
                        </div>
                        <div class="text-right">
                            <span class="font-medium text-gray-900">£{{ number_format($course->price, 2) }}</span>
                        </div>
                    </div>

                    <!-- Discount Code -->
                    <div class="mb-6">
                        <div class="relative">
                            <input type="text" id="discount-code" name="discount_code" 
                                class="w-full pr-24 rounded-lg border-gray-200 focus:border-orange-500 focus:ring-orange-500 text-sm" 
                                placeholder="Have a discount code?">
                            <button type="button" id="apply-discount" 
                                class="absolute right-1 top-1 px-3 py-1.5 bg-orange-600 text-white text-sm font-medium rounded-md hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2">
                                Apply
                            </button>
                        </div>
                        <div id="discount-message" class="mt-2 text-sm"></div>
                    </div>

                    <!-- Price Breakdown -->
                    <div class="space-y-4">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Subtotal</span>
                            <span class="font-medium text-gray-900">£{{ number_format($course->price, 2) }}</span>
                        </div>
                        
                        <div id="discount-amount" class="flex justify-between text-sm hidden">
                            <span class="text-gray-600">Discount</span>
                            <span class="font-medium text-green-600">-£<span id="discount-value">0.00</span></span>
                        </div>
                        
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">VAT (20%)</span>
                            <span class="font-medium text-gray-900">£<span id="vat-amount">{{ number_format($course->price * 0.2, 2) }}</span></span>
                        </div>
                        
                        <div class="pt-4 mt-4 border-t border-gray-100">
                            <div class="flex justify-between items-center">
                                <span class="text-base font-medium text-gray-900">Total</span>
                                <div class="text-right">
                                    <span id="total-price" class="text-xl font-bold text-orange-600">£{{ number_format($course->price * 1.2, 2) }}</span>
                                    <p class="text-xs text-gray-500 mt-1">VAT included</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            

            <!-- Payment Form -->
            <div class="bg-white rounded-2xl shadow-sm p-6 sm:p-8 order-1 lg:order-2">
                @if(session('error'))
                <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <div class="flex items-center">
                        <svg class="h-5 w-5 text-red-400 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                        <p class="text-sm text-red-600">{{ session('error') }}</p>
                    </div>
                </div>
                @endif

                <form id="payment-form" class="space-y-6">
                    @csrf
                    <div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-4">Payment Information</h3>
                        
                        <!-- Payment Method Selection -->
                        <div class="mb-6">
                            <label class="text-sm font-medium text-gray-700 mb-2 block">Payment Method</label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <label class="relative flex items-center p-4 border rounded-lg cursor-pointer hover:border-orange-500 transition-colors">
                                    <input type="radio" name="payment_type" value="card" class="h-4 w-4 text-orange-600 focus:ring-orange-500" checked>
                                    <span class="ml-3 flex items-center">
                                        <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                        </svg>
                                        <span class="ml-2 text-gray-700">Credit Card</span>
                                    </span>
                                </label>
                                <label class="relative flex items-center p-4 border rounded-lg cursor-pointer hover:border-orange-500 transition-colors">
                                    <input type="radio" name="payment_type" value="paypal" class="h-4 w-4 text-orange-600 focus:ring-orange-500">
                                    <span class="ml-3">
                                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M7.076 21.337H2.47a.641.641 0 0 1-.633-.74L4.944.901C5.026.382 5.474 0 5.998 0h7.46c2.57 0 4.578.543 5.69 1.81 1.01 1.15 1.304 2.42 1.012 4.287-.023.143-.047.288-.077.437-.983 5.05-4.349 6.797-8.647 6.797h-2.19c-.524 0-.968.382-1.05.9l-1.12 7.106z" />
                                        </svg>
                                        <span class="ml-2 text-gray-700">PayPal</span>
                                    </span>
                                </label>
                            </div>
                        </div>

                        <!-- Card Payment Section -->
                        <div id="card-payment-section" class="mt-6">
                            <div id="card-element" class="mt-2"></div>
                            <div id="card-errors" role="alert" class="text-red-600 text-sm mt-2"></div>
                        </div>

                        <!-- PayPal Payment Section -->
                        <div id="paypal-payment-section" class="hidden mt-6">
                            <div id="paypal-button-container"></div>
                        </div>

                        <!-- Submit Button -->
                        <button id="submit-button" type="submit" class="mt-6 w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-base font-medium text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                            <span id="spinner" class="hidden">
                                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </span>
                            <span id="button-text">Pay £{{ number_format($course->price * 1.2, 2) }}</span>
                        </button>

                        <p class="mt-4 text-sm text-gray-500 text-center">
                            By clicking "Pay", you agree to our 
                            <a href="#" class="text-orange-600 hover:text-orange-500">Terms of Service</a>
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://js.stripe.com/v3/"></script>
<script>
    // Initialize Stripe configuration
    window.stripeConfig = {
        key: '{{ config('services.stripe.key') }}',
        processUrl: '{{ route('courses.processPurchase', $course) }}',
        successUrl: '{{ route('courses.show', $course) }}',
        coursePrice: '{{ $course->price }}', // Original price without VAT
        csrfToken: '{{ csrf_token() }}',
        userEmail: '{{ auth()->user()->email }}',
        validateDiscountUrl: '{{ route('discount.validate') }}',
        applyDiscountUrl: '{{ route('discount.apply') }}'
    };
</script>
<script src="{{ asset('js/stripe-payment.js') }}"></script>
@endsection
