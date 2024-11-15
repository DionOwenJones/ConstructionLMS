@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Course Summary -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex items-center space-x-4">
                @if($course->image)
                    <img src="{{ Storage::url($course->image) }}" alt="{{ $course->title }}" class="w-20 h-20 object-cover rounded-lg">
                @endif
                <div>
                    <h2 class="text-xl font-semibold text-gray-900">{{ $course->title }}</h2>
                    <p class="text-2xl font-bold text-orange-500 mt-2">${{ number_format($course->price, 2) }}</p>
                </div>
            </div>
        </div>

        <!-- Payment Form -->
        <div class="bg-white rounded-lg shadow-sm p-8">
            <h3 class="text-lg font-medium text-gray-900 mb-6">Payment Information</h3>

            @if (session('error'))
                <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-6">
                    <p class="text-sm text-red-700">{{ session('error') }}</p>
                </div>
            @endif

            <form id="payment-form" class="space-y-6">
                @csrf
                <div>
                    <div id="payment-element" class="mt-2"></div>
                </div>

                <div id="card-errors" class="text-red-600 text-sm mt-2" role="alert"></div>

                <button type="submit" id="submit-button"
                        class="w-full bg-orange-600 text-white py-2 px-4 rounded-md hover:bg-orange-700">
                    Pay ${{ number_format($course->price, 2) }}
                </button>
            </form>

            <div class="mt-6 flex items-center justify-center space-x-4 text-sm text-gray-500">
                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
                <span>Secure payment powered by Stripe</span>
            </div>
        </div>
    </div>
</div>

<script src="https://js.stripe.com/v3/"></script>
<script>
    // Define variables needed by stripe-payment.js
    window.stripeConfig = {
        key: '{{ env('STRIPE_KEY') }}',
        processUrl: '{{ route('courses.enroll', $course) }}',
        successUrl: '{{ route('courses.payment.success', $course) }}',
        csrfToken: '{{ csrf_token() }}',
        coursePrice: '{{ number_format($course->price, 2) }}'
    };
</script>
<script src="{{ asset('js/stripe-payment.js') }}"></script>
@endsection
