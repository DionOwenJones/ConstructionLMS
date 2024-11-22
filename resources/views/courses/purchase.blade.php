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

    .spinner {
        border: 3px solid #f3f3f3;
        border-radius: 50%;
        border-top: 3px solid #EA580C;
        width: 20px;
        height: 20px;
        animation: spin 1s linear infinite;
        display: inline-block;
        vertical-align: middle;
        margin-left: 8px;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
</style>
@endsection

@section('content')
<div class="min-h-screen bg-gray-50/50">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white rounded-2xl shadow-sm p-8">
            <!-- Course Details -->
            <div class="mb-8 pb-8 border-b border-gray-200">
                <h1 class="text-2xl font-bold text-gray-900 mb-4">Purchase Course</h1>
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-xl font-semibold text-gray-900">{{ $course->title }}</h2>
                        <p class="text-gray-600">{{ Str::limit($course->description, 100) }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-2xl font-bold text-gray-900">£{{ number_format($course->price, 2) }}</p>
                    </div>
                </div>
            </div>

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

            <!-- Purchase Form -->
            <form id="payment-form" method="POST" action="{{ route('courses.purchase.process', $course) }}">
                @csrf
                <!-- Total Amount -->
                <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-700">Total Amount:</span>
                        <span class="text-2xl font-bold text-gray-900">£{{ number_format($course->price, 2) }}</span>
                    </div>
                </div>

                <!-- Card Details -->
                <div class="mb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Payment Information</h3>
                    <div id="card-element"></div>
                    <div id="card-errors" role="alert"></div>
                </div>

                <!-- Submit Button -->
                <button type="submit" 
                    id="submit-button"
                    class="w-full bg-orange-600 text-white py-3 px-4 rounded-lg hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200"
                >
                    <span id="button-text">Complete Purchase</span>
                    <span id="spinner" class="spinner hidden"></span>
                </button>

                <p class="text-sm text-gray-500 text-center mt-4">
                    <svg class="inline-block h-5 w-5 text-gray-400 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                    Secure payment powered by Stripe
                </p>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://js.stripe.com/v3/"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    const stripe = Stripe('{{ config('services.stripe.key') }}');
    const elements = stripe.elements();

    const card = elements.create('card', {
        style: {
            base: {
                fontSize: '16px',
                color: '#1F2937',
                '::placeholder': {
                    color: '#6B7280',
                },
            },
        },
        hidePostalCode: true
    });

    card.mount('#card-element');

    card.addEventListener('change', function(event) {
        const displayError = document.getElementById('card-errors');
        if (event.error) {
            displayError.textContent = event.error.message;
        } else {
            displayError.textContent = '';
        }
    });

    const form = document.getElementById('payment-form');
    const submitButton = document.getElementById('submit-button');
    const buttonText = document.getElementById('button-text');
    const spinner = document.getElementById('spinner');

    form.addEventListener('submit', async function(e) {
        e.preventDefault();

        submitButton.disabled = true;
        buttonText.textContent = 'Processing...';
        spinner.classList.remove('hidden');

        try {
            const { paymentMethod, error } = await stripe.createPaymentMethod({
                type: 'card',
                card: card,
            });

            if (error) {
                throw new Error(error.message);
            }

            const response = await fetch('{{ route('courses.purchase.process', $course) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    payment_method_id: paymentMethod.id,
                    _token: csrfToken
                })
            });

            const result = await response.json();

            if (!response.ok) {
                throw new Error(result.error || 'An error occurred during payment processing.');
            }

            if (result.success) {
                window.location.href = result.redirect;
            } else {
                throw new Error(result.error || 'Payment failed.');
            }

        } catch (error) {
            const errorElement = document.getElementById('card-errors');
            errorElement.textContent = error.message;
            submitButton.disabled = false;
            buttonText.textContent = 'Complete Purchase';
            spinner.classList.add('hidden');
        }
    });
});
</script>
@endsection
