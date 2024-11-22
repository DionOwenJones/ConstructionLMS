@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div>
        <h2 class="text-2xl font-bold text-gray-900">Edit User</h2>
        <p class="mt-1 text-sm text-gray-500">Update user information and permissions.</p>
    </div>

    <!-- Form -->
    <div class="p-6 bg-white rounded-lg shadow">
        <form action="{{ route('admin.users.update', $user) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Name -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                <input type="text" name="name" id="name" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500 sm:text-sm" required value="{{ old('name', $user->name) }}">
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" name="email" id="email" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500 sm:text-sm" required value="{{ old('email', $user->email) }}">
                @error('email')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">New Password (leave blank to keep current)</label>
                <input type="password" name="password" id="password" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500 sm:text-sm">
                @error('password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Confirm Password -->
            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm New Password</label>
                <input type="password" name="password_confirmation" id="password_confirmation" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500 sm:text-sm">
            </div>

            <!-- Role -->
            <div>
                <label for="role" class="block text-sm font-medium text-gray-700">Role</label>
                <select name="role" id="role" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500 sm:text-sm" required>
                    <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="user" {{ old('role', $user->role) == 'user' ? 'selected' : '' }}>User</option>
                    <option value="business" {{ old('role', $user->role) == 'business' ? 'selected' : '' }}>Business</option>
                </select>
                @error('role')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Business Section (shown only when role is business) -->
            <div x-data="{ showBusiness: false }" x-show="showBusiness" x-init="showBusiness = document.getElementById('role').value === 'business'" @change.window="showBusiness = document.getElementById('role').value === 'business'" style="display: none;">
                <div class="p-4 mt-4 space-y-4 border rounded-md">
                    <h3 class="text-lg font-medium text-gray-900">Business Information</h3>
                    
                    <!-- Business Name -->
                    <div>
                        <label for="business_name" class="block text-sm font-medium text-gray-700">Business Name</label>
                        <input type="text" name="business_name" id="business_name" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500 sm:text-sm" value="{{ old('business_name', optional($user->business)->name) }}">
                        @error('business_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Business Address -->
                    <div>
                        <label for="business_address" class="block text-sm font-medium text-gray-700">Business Address</label>
                        <textarea name="business_address" id="business_address" rows="3" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500 sm:text-sm">{{ old('business_address', optional($user->business)->address) }}</textarea>
                        @error('business_address')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Business Phone -->
                    <div>
                        <label for="business_phone" class="block text-sm font-medium text-gray-700">Business Phone</label>
                        <input type="tel" name="business_phone" id="business_phone" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500 sm:text-sm" value="{{ old('business_phone', optional($user->business)->phone) }}">
                        @error('business_phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end space-x-3">
                <a href="{{ route('admin.users.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                    Cancel
                </a>
                <button type="submit" class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-white bg-orange-600 border border-transparent rounded-md shadow-sm hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                    Update User
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // Initialize Alpine.js component for role-based form fields
    document.addEventListener('alpine:init', () => {
        Alpine.data('roleForm', () => ({
            showBusiness: false,
            init() {
                this.showBusiness = document.getElementById('role').value === 'business';
                document.getElementById('role').addEventListener('change', (e) => {
                    this.showBusiness = e.target.value === 'business';
                });
            }
        }));
    });
</script>
@endpush
@endsection
