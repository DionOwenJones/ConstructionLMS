@extends('layouts.app')

@section('title', 'Register')

@section('content')
<div class="min-h-screen flex">
    <!-- Left side - Image -->
    <div class="hidden lg:block lg:w-1/2">
        <img src="https://images.unsplash.com/photo-1628744876497-eb30460be9f6?q=80&w=2070"
            alt="Construction Site" class="w-full h-full object-cover">
    </div>

    <!-- Right side - Registration Form -->
    <div class="w-full lg:w-1/2 flex items-center justify-center px-8 py-12 bg-gray-50">
        <div class="max-w-md w-full space-y-8">
            <div class="text-center">
                <img src="https://img.icons8.com/color/96/construction.png"
                    alt="Logo" class="h-12 mx-auto mb-4">
                <h2 class="text-3xl font-extrabold text-gray-900 mb-2">Create Account</h2>
                <p class="text-gray-600">Please fill in your details to register</p>
            </div>

            <form class="mt-8 space-y-6" method="POST" action="{{ route('register') }}">
                @csrf

                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Full Name</label>
                    <div class="mt-1">
                        <input id="name" name="name" type="text" required
                            class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm
                            placeholder-gray-400 focus:outline-none focus:ring-orange-500 focus:border-orange-500 sm:text-sm"
                            value="{{ old('name') }}">
                    </div>
                    @error('name')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email address</label>
                    <div class="mt-1">
                        <input id="email" name="email" type="email" required
                            class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm
                            placeholder-gray-400 focus:outline-none focus:ring-orange-500 focus:border-orange-500 sm:text-sm"
                            value="{{ old('email') }}">
                    </div>
                    @error('email')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                    <div class="mt-1">
                        <input id="password" name="password" type="password" required
                            class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm
                            placeholder-gray-400 focus:outline-none focus:ring-orange-500 focus:border-orange-500 sm:text-sm">
                    </div>
                    @error('password')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Confirm Password -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">
                        Confirm Password
                    </label>
                    <div class="mt-1">
                        <input id="password_confirmation" name="password_confirmation" type="password" required
                            class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm
                            placeholder-gray-400 focus:outline-none focus:ring-orange-500 focus:border-orange-500 sm:text-sm">
                    </div>
                </div>

                <div>
                    <button type="submit"
                        class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm
                        font-medium text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2
                        focus:ring-offset-2 focus:ring-orange-500">
                        Register
                    </button>
                </div>

                <div class="text-sm text-center">
                    <p class="text-gray-600">
                        Already have an account?
                        <a href="{{ route('login') }}" class="font-medium text-orange-600 hover:text-orange-500">
                            Sign in
                        </a>
                    </p>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
