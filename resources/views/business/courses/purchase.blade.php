@extends('layouts.business')

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
                <h1 class="text-2xl font-bold text-gray-900 mb-4">Purchase Course for Your Team</h1>
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-xl font-semibold text-gray-900">{{ $course->title }}</h2>
                        <p class="text-gray-600">{{ Str::limit($course->description, 100) }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-2xl font-bold text-gray-900">${{ number_format($course->price, 2) }}</p>
                        <p class="text-sm text-gray-500">per seat</p>
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
            <form id="payment-form" action="{{ route('business.courses.purchase.process', $course) }}" method="POST">
                @csrf
                <input type="hidden" name="payment_method" id="payment_method">
                
                <!-- Number of Seats -->
                <div class="mb-6">
                    <label for="seats" class="block text-sm font-medium text-gray-700 mb-2">Number of Seats</label>
                    <div class="flex items-center gap-x-4">
                        <button type="button" onclick="decrementSeats()" class="p-2 rounded-lg border border-gray-300 hover:bg-gray-50">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd" />
                            </svg>
                        </button>
                        <input type="number" name="seats" id="seats" min="1" value="{{ old('seats', 1) }}" onchange="updateTotal()"
                            class="block w-20 rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 text-center">
                        <button type="button" onclick="incrementSeats()" class="p-2 rounded-lg border border-gray-300 hover:bg-gray-50">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                    @error('seats')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Total Amount -->
                <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-700">Total Amount:</span>
                        <span class="text-2xl font-bold text-gray-900">$<span id="total-amount">{{ number_format($course->price * old('seats', 1), 2) }}</span></span>
                    </div>
                </div>

                <!-- Card Element -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Card Information</label>
                    <div class="card-element-container">
                        <div id="card-element"></div>
                    </div>
                    <div id="card-errors" role="alert"></div>
                </div>

                <!-- Submit Button -->
                <button type="submit" id="submit-button"
                    class="w-full bg-orange-600 text-white py-3 px-4 rounded-lg hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed">
                    <span id="button-text">Complete Purchase</span>
                    <span id="spinner" class="spinner hidden"></span>
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://js.stripe.com/v3/"></script>
<script>
    const stripe = Stripe('{{ $stripeKey }}');
    const elements = stripe.elements();
    
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
    
    card.mount('#card-element');
    
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

        if (event.elementType === 'card' && event.complete) {
            container.classList.add('focused');
        } else {
            container.classList.remove('focused');
        }
    });
    
    const form = document.getElementById('payment-form');
    const submitButton = document.getElementById('submit-button');
    const buttonText = document.getElementById('button-text');
    const spinner = document.getElementById('spinner');

    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        setLoading(true);

        try {
            const {paymentMethod, error} = await stripe.createPaymentMethod({
                type: 'card',
                card: card,
                billing_details: {
                    name: '{{ Auth::user()->name }}',
                    email: '{{ Auth::user()->email }}'
                }
            });

            if (error) {
                const errorElement = document.getElementById('card-errors');
                errorElement.textContent = error.message;
                setLoading(false);
                return;
            }

            document.getElementById('payment_method').value = paymentMethod.id;
            form.submit();

        } catch (error) {
            const errorElement = document.getElementById('card-errors');
            errorElement.textContent = error.message || 'An error occurred while processing your payment.';
            setLoading(false);
        }
    });

    function updateTotal() {
        const seats = parseInt(document.getElementById('seats').value) || 1;
        if (seats < 1) {
            document.getElementById('seats').value = 1;
            seats = 1;
        }
        const pricePerSeat = {{ $course->price }};
        const total = (seats * pricePerSeat).toFixed(2);
        document.getElementById('total-amount').textContent = total;
    }

    function incrementSeats() {
        const seatsInput = document.getElementById('seats');
        seatsInput.value = parseInt(seatsInput.value) + 1;
        updateTotal();
    }

    function decrementSeats() {
        const seatsInput = document.getElementById('seats');
        const currentValue = parseInt(seatsInput.value);
        if (currentValue > 1) {
            seatsInput.value = currentValue - 1;
            updateTotal();
        }
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
</script>
@endsection
