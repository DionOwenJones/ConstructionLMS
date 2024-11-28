@extends('layouts.business')

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

    .license-input {
        width: 80px;
        text-align: center;
        font-size: 1.125rem;
        padding: 0.5rem;
        border-radius: 0.5rem;
        border: 1px solid #E5E7EB;
    }

    .license-button {
        padding: 0.5rem;
        border-radius: 0.5rem;
        background-color: #F3F4F6;
        transition: all 0.2s;
    }

    .license-button:hover {
        background-color: #E5E7EB;
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
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                <span class="text-gray-600">Team management</span>
                            </div>
                            <div class="course-feature">
                                <svg class="h-5 w-5 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span class="text-gray-600">Certification included</span>
                            </div>
                            <div class="course-feature">
                                <svg class="h-5 w-5 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <span class="text-gray-600">Progress tracking</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- License Calculator -->
                <div class="bg-white rounded-2xl shadow-sm p-6 sm:p-8 border border-gray-100">
                    <h3 class="text-xl font-semibold text-gray-900 mb-6">License Calculator</h3>
                    
                    <!-- License Quantity Selector -->
                    <div class="bg-gray-50 rounded-xl p-4 mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-3">Number of Licenses</label>
                        <div class="flex items-center justify-center space-x-4">
                            <button type="button" class="license-btn-decrease inline-flex items-center justify-center w-10 h-10 rounded-lg bg-white border border-gray-200 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition-colors">
                                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                </svg>
                            </button>
                            <input type="number" id="licenses" name="licenses" value="1" min="1" 
                                class="block w-20 text-center border-gray-200 rounded-lg text-lg font-semibold focus:ring-orange-500 focus:border-orange-500">
                            <button type="button" class="license-btn-increase inline-flex items-center justify-center w-10 h-10 rounded-lg bg-white border border-gray-200 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition-colors">
                                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Price Breakdown -->
                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Price per license</span>
                            <span class="font-medium text-gray-900">£{{ number_format($course->price, 2) }}</span>
                        </div>

                        <!-- Discount Section -->
                        <div class="relative bg-orange-50 rounded-xl p-4 border border-orange-100">
                            <div class="flex items-center space-x-3 mb-3">
                                <svg class="w-5 h-5 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                </svg>
                                <span class="text-sm font-medium text-gray-900">Add Discount Code</span>
                            </div>
                            <div class="flex space-x-2">
                                <input type="text" id="discount-code" name="discount_code" 
                                    class="flex-1 rounded-lg border-orange-200 bg-white focus:border-orange-500 focus:ring-orange-500 text-sm" 
                                    placeholder="Enter code">
                                <button type="button" id="apply-discount" 
                                    class="px-4 py-2 bg-orange-600 text-white text-sm font-medium rounded-lg hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition-colors">
                                    Apply
                                </button>
                            </div>
                            <div id="discount-message" class="mt-2 text-sm min-h-[1.25rem]"></div>
                        </div>

                        <!-- Price Summary -->
                        <div class="space-y-3 pt-4">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Subtotal</span>
                                <div class="flex items-center">
                                    <span class="font-medium text-gray-900">£<span id="subtotal-amount" class="transition-all duration-300">{{ number_format($course->price, 2) }}</span></span>
                                </div>
                            </div>
                            
                            <div id="discount-amount" class="flex justify-between text-sm hidden">
                                <span class="text-gray-600">Discount</span>
                                <div class="flex items-center text-green-600">
                                    <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                                    </svg>
                                    <span class="font-medium">-£<span id="discount-value" class="transition-all duration-300">0.00</span></span>
                                </div>
                            </div>
                            
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">VAT (20%)</span>
                                <span class="font-medium text-gray-900">£<span id="vat-amount" class="transition-all duration-300">{{ number_format($course->price * 0.2, 2) }}</span></span>
                            </div>
                        </div>
                        
                        <!-- Total -->
                        <div class="pt-4 mt-4 border-t border-gray-100">
                            <div class="flex justify-between items-center">
                                <span class="text-base font-medium text-gray-900">Total Amount</span>
                                <div class="text-right">
                                    <span class="text-2xl font-bold bg-gradient-to-r from-orange-600 to-orange-500 bg-clip-text text-transparent">
                                        £<span id="total-amount" class="transition-all duration-300">{{ number_format($course->price * 1.2, 2) }}</span>
                                    </span>
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
                        <svg class="h-5 w-5 text-red-400 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                        <p class="text-sm text-red-600">{{ session('error') }}</p>
                    </div>
                </div>
                @endif

                <form id="payment-form" method="POST" action="{{ route('business.courses.purchase.process', $course) }}" class="space-y-6">
                    @csrf
                    <input type="hidden" id="form-payment-method-id" name="payment_method_id" value="">
                    <input type="hidden" id="form-licenses" name="licenses" value="1">
                    <div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-4">Payment Information</h3>
                        
                        <!-- Payment Method Selection -->
                        <div class="mb-6">
                            <label class="text-sm font-medium text-gray-700 mb-2 block">Payment Method</label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <label class="relative flex items-center p-4 border rounded-lg cursor-pointer hover:border-orange-500 transition-colors">
                                    <input type="radio" name="payment_method" value="card" class="h-4 w-4 text-orange-600 focus:ring-orange-500" checked>
                                    <span class="ml-3 flex items-center">
                                        <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                        </svg>
                                        <span class="ml-2 text-gray-700">Credit Card</span>
                                    </span>
                                </label>
                                <label class="relative flex items-center p-4 border rounded-lg cursor-pointer hover:border-orange-500 transition-colors">
                                    <input type="radio" name="payment_method" value="paypal" class="h-4 w-4 text-orange-600 focus:ring-orange-500">
                                    <span class="ml-3">
                                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M7.076 21.337H2.47a.641.641 0 0 1-.633-.74L4.944.901C5.026.382 5.474 0 5.998 0h7.46c2.57 0 4.578.543 5.69 1.81 1.01 1.15 1.304 2.42 1.012 4.287-.023.143-.047.288-.077.437-.983 5.05-4.349 6.797-8.647 6.797h-2.19c-.524 0-.968.382-1.05.9l-1.12 7.106zm14.146-14.42a3.35 3.35 0 0 0-.607-.541c-.013.076-.026.175-.041.254-.93 4.778-4.005 7.201-9.138 7.201h-2.19a.563.563 0 0 0-.556.476l-1.187 7.527h4.606c.467 0 .864-.34.937-.799l.039-.203.738-4.682.047-.245a.94.94 0 0 1 .936-.799h.591c3.819 0 6.8-1.549 7.676-6.036.37-1.878-.003-3.44-1.272-4.534l-.579-.62z"/>
                                        </svg>
                                    </span>
                                </label>
                            </div>
                        </div>

                        <!-- Card Payment Section -->
                        <div id="card-payment-section" class="mt-6">
                            <label class="text-sm font-medium text-gray-700 mb-2 block">Card Details</label>
                            <div id="card-element" class="p-3 border rounded-lg"></div>
                            <div id="card-errors" role="alert" class="mt-2 text-sm text-red-600"></div>
                        </div>

                        <!-- PayPal Section -->
                        <div id="paypal-payment-section" class="hidden mt-6">
                            <div id="paypal-button-container"></div>
                        </div>
                    </div>

                    <!-- Submit Button (for card payments) -->
                    <button type="submit" id="submit-button" class="w-full flex justify-center items-center px-6 py-3 border border-transparent text-base font-medium rounded-xl text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition-colors duration-200">
                        <span id="button-text">Complete Purchase</span>
                        <div id="spinner" class="hidden">
                            <svg class="animate-spin ml-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </div>
                    </button>

                    <p class="mt-4 text-sm text-gray-500 text-center">
                        By clicking "Complete Purchase", you agree to our 
                        <a href="#" class="text-orange-600 hover:text-orange-500">Terms of Service</a>
                    </p>
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
        processUrl: '{{ route('business.courses.purchase.process', $course) }}',
        successUrl: '{{ route('business.courses.show', $course) }}',
        coursePrice: '{{ $course->price }}', // Original price without VAT
        csrfToken: '{{ csrf_token() }}',
        userEmail: '{{ auth()->user()->email }}',
        validateDiscountUrl: '{{ route('discount.validate') }}',
        applyDiscountUrl: '{{ route('discount.apply') }}'
    };
</script>
<script src="{{ asset('js/business-stripe-payment.js') }}"></script>
@endsection