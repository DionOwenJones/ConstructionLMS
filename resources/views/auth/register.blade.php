@extends('layouts.app')

@section('title', 'Register')

@section('content')
<div class="min-h-screen flex">
    <!-- Left side - Image with Overlay -->
    <div class="hidden lg:block lg:w-1/2 relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-br from-orange-600/30 to-orange-800/30 backdrop-blur-sm z-10"></div>
        <img src="https://images.unsplash.com/photo-1504307651254-35680f356dfd?auto=format&fit=crop&q=80"
            alt="Construction Site" class="w-full h-full object-cover transform scale-105">
    </div>

    <!-- Right side - Registration Form -->
    <div class="w-full lg:w-1/2 flex items-center justify-center px-8 bg-gradient-to-br from-white to-orange-50 overflow-hidden">
        <!-- Decorative elements -->
        <div class="absolute top-0 right-0 w-96 h-96 bg-orange-200 rounded-full mix-blend-multiply filter blur-3xl opacity-10 animate-blob"></div>
        <div class="absolute -bottom-8 right-20 w-96 h-96 bg-orange-300 rounded-full mix-blend-multiply filter blur-3xl opacity-10 animate-blob animation-delay-2000"></div>
        
        <div class="relative max-w-md w-full space-y-4">
            <div class="text-center">
                <img src="https://img.icons8.com/color/96/construction.png"
                    alt="Logo" class="h-12 mx-auto mb-4 transform hover:scale-105 transition-transform duration-300">
                <h2 class="text-3xl font-bold text-gray-900 mb-2">Create Account</h2>
                <p class="text-gray-600 mb-6">Join our construction training platform</p>
            </div>

            <form id="registrationForm" class="space-y-4" method="POST" action="{{ route('register') }}">
                @csrf

                <!-- Account Type -->
                <div class="mb-8">
                    <label for="role" class="block text-sm font-medium text-gray-700 mb-3">Account Type</label>
                    <div class="grid grid-cols-2 gap-4" id="roleContainer">
                        <label class="relative flex items-center justify-center p-4 border border-gray-300 rounded-lg cursor-pointer hover:border-orange-500 transition-colors">
                            <input type="radio" name="role" value="user" class="absolute h-0 w-0 opacity-0" checked>
                            <div class="space-y-2 text-center">
                                <svg class="h-6 w-6 mx-auto text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                <div class="text-sm font-medium text-gray-900">Individual</div>
                                <div class="text-xs text-gray-500">For personal training</div>
                            </div>
                        </label>
                        <label class="relative flex items-center justify-center p-4 border border-gray-300 rounded-lg cursor-pointer hover:border-orange-500 transition-colors">
                            <input type="radio" name="role" value="business" class="absolute h-0 w-0 opacity-0">
                            <div class="space-y-2 text-center">
                                <svg class="h-6 w-6 mx-auto text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                                <div class="text-sm font-medium text-gray-900">Business</div>
                                <div class="text-xs text-gray-500">For company training</div>
                            </div>
                        </label>
                    </div>
                    @error('role')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Name -->
                <div>
                    <label for="name" id="nameLabel" class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <input id="name" name="name" type="text" required
                            class="appearance-none block w-full pl-10 px-3 py-2.5 border border-gray-300 rounded-xl shadow-sm
                            placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-300"
                            value="{{ old('name') }}"
                            placeholder="Enter your full name">
                    </div>
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" id="emailLabel" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                            </svg>
                        </div>
                        <input id="email" name="email" type="email" required
                            class="appearance-none block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-orange-500 focus:border-orange-500 sm:text-sm"
                            value="{{ old('email') }}"
                            placeholder="Enter your email address">
                    </div>
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <input id="password" name="password" type="password" required
                            class="appearance-none block w-full pl-10 px-3 py-2.5 border border-gray-300 rounded-xl shadow-sm
                            placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-300"
                            placeholder="At least 8 characters with numbers and symbols">
                    </div>
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">
                        Password must contain at least 8 characters, including uppercase, lowercase, numbers, and symbols.
                    </p>
                </div>

                <!-- Confirm Password -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">
                        Confirm Password
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <input id="password_confirmation" name="password_confirmation" type="password" required
                            class="appearance-none block w-full pl-10 px-3 py-2.5 border border-gray-300 rounded-xl shadow-sm
                            placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-300"
                            placeholder="Re-enter your password">
                    </div>
                </div>

                <div class="mt-6">
                    <button type="submit"
                        class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-xl shadow-lg text-sm
                        font-medium text-white bg-gradient-to-r from-orange-600 to-orange-500 hover:from-orange-500 hover:to-orange-400 
                        focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500
                        transform hover:-translate-y-0.5 transition-all duration-200">
                        Create Account
                    </button>
                </div>

                <div class="text-center mt-4">
                    <p class="text-sm text-gray-600">
                        Already have an account?
                        <a href="{{ route('login') }}" class="font-medium text-transparent bg-clip-text bg-gradient-to-r from-orange-600 to-orange-400 hover:from-orange-500 hover:to-orange-300 transition-all duration-200">
                            Sign in
                        </a>
                    </p>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const roleInputs = document.querySelectorAll('input[name="role"]');
        const nameLabel = document.getElementById('nameLabel');
        const emailLabel = document.getElementById('emailLabel');
        const nameInput = document.getElementById('name');
        const emailInput = document.getElementById('email');
        const form = document.getElementById('registrationForm');

        function updateFormFields(role) {
            const isBusiness = role === 'business';
            
            // Update labels and placeholders
            nameLabel.textContent = isBusiness ? 'Business Name' : 'Full Name';
            emailLabel.textContent = isBusiness ? 'Business Email' : 'Email Address';
            nameInput.placeholder = isBusiness ? 'Enter your business name' : 'Enter your full name';
            emailInput.placeholder = isBusiness ? 'Enter your business email' : 'Enter your email address';

            // Update form action if needed
            form.action = isBusiness ? '{{ route("register") }}?type=business' : '{{ route("register") }}';

            // Add visual feedback for selected role
            roleInputs.forEach(input => {
                const label = input.closest('label');
                if (input.value === role) {
                    label.classList.add('border-orange-500', 'bg-orange-50');
                    label.classList.remove('border-gray-300');
                } else {
                    label.classList.remove('border-orange-500', 'bg-orange-50');
                    label.classList.add('border-gray-300');
                }
            });
        }

        // Add click event listeners to role inputs
        roleInputs.forEach(input => {
            input.addEventListener('change', (e) => {
                updateFormFields(e.target.value);
            });

            // Also handle the hover effect
            const label = input.closest('label');
            label.addEventListener('mouseenter', () => {
                if (!input.checked) {
                    label.classList.add('border-orange-300');
                    label.classList.remove('border-gray-300');
                }
            });
            label.addEventListener('mouseleave', () => {
                if (!input.checked) {
                    label.classList.remove('border-orange-300');
                    label.classList.add('border-gray-300');
                }
            });
        });

        // Set initial state based on default selected role
        const initialRole = document.querySelector('input[name="role"]:checked');
        if (initialRole) {
            updateFormFields(initialRole.value);
        }

        // Handle form submission
        form.addEventListener('submit', function(e) {
            const selectedRole = document.querySelector('input[name="role"]:checked').value;
            // You can add any additional validation here if needed
        });
    });
</script>
@endpush