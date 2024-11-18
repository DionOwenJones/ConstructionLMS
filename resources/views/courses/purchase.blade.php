@extends('layouts.app')

@section('styles')
<style>
    .payment-form {
        background: white;
        padding: 30px;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
    .card-element-container {
        padding: 12px;
        border: 1px solid #E5E7EB;
        border-radius: 6px;
        background: white;
        transition: all 0.2s ease;
    }
    .card-element-container.focused {
        border-color: #EA580C;
        box-shadow: 0 0 0 1px #EA580C;
    }
    .card-element-container.invalid {
        border-color: #dc2626;
    }
    #card-errors {
        margin-top: 8px;
        color: #dc2626;
        font-size: 0.875rem;
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
    }
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    .payment-header {
        border-bottom: 1px solid #E5E7EB;
        padding-bottom: 20px;
        margin-bottom: 20px;
    }
    .secure-badge {
        display: flex;
        align-items: center;
        gap: 8px;
        color: #059669;
        font-size: 0.875rem;
        margin-top: 4px;
    }
</style>
@endsection

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="mb-8">
                    <h2 class="text-3xl font-bold mb-4">{{ $course->title }}</h2>
                    
                    @if($course->image)
                        <img src="{{ asset('storage/' . $course->image) }}" alt="{{ $course->title }}" class="w-full h-64 object-cover rounded-lg mb-6">
                    @endif
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div>
                            <h3 class="text-xl font-semibold mb-4">Course Overview</h3>
                            <p class="text-gray-600">{{ $course->description }}</p>
                        </div>
                        
                        <div>
                            <h3 class="text-xl font-semibold mb-4">What You'll Learn</h3>
                            <ul class="list-disc list-inside text-gray-600">
                                @foreach(explode("\n", $course->learning_outcomes) as $outcome)
                                    @if(trim($outcome))
                                        <li>{{ trim($outcome) }}</li>
                                    @endif
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    
                    <div class="mb-8">
                        <div class="bg-white border border-gray-200 rounded-lg">
                            <div class="payment-header p-6">
                                <h3 class="text-xl font-semibold text-gray-900">Complete Purchase</h3>
                                <p class="text-gray-600 mt-2">Enter your payment details to get instant access to this course.</p>
                                <div class="secure-badge">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                                    </svg>
                                    <span>Secure payment processing by Stripe</span>
                                </div>
                            </div>
                            
                            <div class="p-6 border-t border-gray-200">
                                <form id="payment-form" class="payment-form">
                                    <div class="mb-6">
                                        <label for="card-element" class="block text-sm font-medium text-gray-700 mb-2">
                                            Card Details
                                        </label>
                                        <div class="card-element-container">
                                            <div id="card-element"></div>
                                        </div>
                                        <div id="card-errors" role="alert"></div>
                                    </div>
                                    
                                    <div class="flex items-center justify-between">
                                        <div class="text-lg font-semibold">
                                            Total: ${{ number_format($course->price, 2) }}
                                        </div>
                                        <button type="submit" id="submit-button" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                                            <span id="button-text">Purchase Course</span>
                                            <div id="spinner" class="spinner ml-2 hidden"></div>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://js.stripe.com/v3/"></script>
<script>
    // Get CSRF token from meta tag
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Initialize Stripe with the publishable key
    const stripe = Stripe('{{ env('STRIPE_KEY') }}');
    
    // Create an instance of Elements
    const elements = stripe.elements();
    
    // Create the card Element
    const card = elements.create('card', {
        hidePostalCode: true,
        style: {
            base: {
                color: '#32325d',
                fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
                fontSmoothing: 'antialiased',
                fontSize: '16px',
                '::placeholder': {
                    color: '#aab7c4'
                }
            },
            invalid: {
                color: '#dc2626',
                iconColor: '#dc2626'
            }
        }
    });
    
    // Mount the card Element
    card.mount('#card-element');
    
    // Handle real-time validation errors
    card.addEventListener('change', function(event) {
        const displayError = document.getElementById('card-errors');
        const container = document.querySelector('.card-element-container');
        
        if (event.error) {
            displayError.textContent = event.error.message;
            container.classList.add('invalid');
        } else {
            displayError.textContent = '';
            container.classList.remove('invalid');
        }
    });

    // Handle focus state
    card.addEventListener('focus', function() {
        document.querySelector('.card-element-container').classList.add('focused');
    });

    card.addEventListener('blur', function() {
        document.querySelector('.card-element-container').classList.remove('focused');
    });
    
    // Handle form submission
    const form = document.getElementById('payment-form');
    const submitButton = document.getElementById('submit-button');
    const spinner = document.getElementById('spinner');
    const buttonText = document.getElementById('button-text');
    
    form.addEventListener('submit', async function(event) {
        event.preventDefault();
        setLoading(true);
        
        try {
            // Create payment method
            const { paymentMethod, error } = await stripe.createPaymentMethod({
                type: 'card',
                card: card,
                billing_details: {
                    email: '{{ auth()->user()->email }}'
                }
            });
            
            if (error) {
                throw error;
            }

            // Send payment method ID to server
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
            
            if (result.requires_action) {
                // Handle 3D Secure authentication
                const { error, paymentIntent } = await stripe.handleCardAction(
                    result.payment_intent_client_secret
                );

                if (error) {
                    throw error;
                }

                // Confirm the payment after 3D Secure
                const confirmResponse = await fetch('{{ route('courses.purchase.process', $course) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        payment_intent_id: paymentIntent.id,
                        _token: csrfToken
                    })
                });

                const confirmResult = await confirmResponse.json();
                handleServerResponse(confirmResult);
            } else {
                handleServerResponse(result);
            }
        } catch (error) {
            console.error('Payment Error:', error);
            handleError(error);
        }
    });
    
    function handleServerResponse(response) {
        if (response.error) {
            handleError({ message: response.error });
            return;
        }
        
        if (response.success && response.redirect) {
            window.location.href = response.redirect;
            return;
        }
        
        handleError({ message: 'An unexpected error occurred. Please try again.' });
    }

    function setLoading(isLoading) {
        if (isLoading) {
            submitButton.disabled = true;
            buttonText.style.opacity = '0';
            spinner.classList.remove('hidden');
        } else {
            submitButton.disabled = false;
            buttonText.style.opacity = '1';
            spinner.classList.add('hidden');
        }
    }
    
    function handleError(error) {
        const errorElement = document.getElementById('card-errors');
        errorElement.textContent = error.message || 'An error occurred. Please try again.';
        setLoading(false);
        console.error('Payment Error:', error);
    }
</script>
@endsection
