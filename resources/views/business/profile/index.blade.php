@extends('layouts.business')

@section('content')
<div class="min-h-screen bg-gray-50/50">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="md:flex md:items-center md:justify-between mb-8">
            <div class="flex-1 min-w-0">
                <h2 class="text-3xl font-bold leading-7 text-gray-900 sm:truncate sm:text-4xl sm:tracking-tight">
                    Business Profile
                </h2>
                <p class="mt-2 text-lg text-gray-600">
                    Manage your business information and settings
                </p>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-8 rounded-md bg-green-50 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800">
                            {{ session('success') }}
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <div class="bg-white shadow-sm ring-1 ring-gray-900/5 sm:rounded-xl">
            <form action="{{ route('business.profile.update') }}" method="POST" class="space-y-8 p-8">
                @csrf
                @method('PUT')

                <div class="space-y-8 divide-y divide-gray-200">
                    <div class="space-y-6">
                        <div>
                            <h3 class="text-lg font-medium leading-6 text-gray-900">Business Information</h3>
                            <p class="mt-1 text-sm text-gray-500">
                                Update your business details and contact information.
                            </p>
                        </div>

                        <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                            <div class="sm:col-span-4">
                                <label for="company_name" class="block text-sm font-medium text-gray-700">
                                    Company Name
                                </label>
                                <div class="mt-1">
                                    <input type="text" name="company_name" id="company_name"
                                           value="{{ old('company_name', $business->company_name) }}"
                                           class="block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 sm:text-sm">
                                </div>
                                @error('company_name')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="sm:col-span-4">
                                <label for="contact_email" class="block text-sm font-medium text-gray-700">
                                    Contact Email
                                </label>
                                <div class="mt-1">
                                    <input type="email" name="contact_email" id="contact_email"
                                           value="{{ old('contact_email', $business->contact_email) }}"
                                           class="block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 sm:text-sm">
                                </div>
                                @error('contact_email')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="sm:col-span-4">
                                <label for="phone" class="block text-sm font-medium text-gray-700">
                                    Phone Number
                                </label>
                                <div class="mt-1">
                                    <input type="tel" name="phone" id="phone"
                                           value="{{ old('phone', $business->phone) }}"
                                           class="block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 sm:text-sm">
                                </div>
                                @error('phone')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="sm:col-span-6">
                                <label for="address" class="block text-sm font-medium text-gray-700">
                                    Business Address
                                </label>
                                <div class="mt-1">
                                    <textarea name="address" id="address" rows="3"
                                              class="block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 sm:text-sm">{{ old('address', $business->address) }}</textarea>
                                </div>
                                @error('address')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="pt-8 space-y-6">
                        <div>
                            <h3 class="text-lg font-medium leading-6 text-gray-900">Business Settings</h3>
                            <p class="mt-1 text-sm text-gray-500">
                                Configure your business preferences and settings.
                            </p>
                        </div>

                        <div class="space-y-6">
                            <div class="relative flex items-start">
                                <div class="flex h-5 items-center">
                                    <input type="checkbox" name="notifications_enabled" id="notifications_enabled"
                                           {{ $business->notifications_enabled ? 'checked' : '' }}
                                           class="h-4 w-4 rounded border-gray-300 text-orange-600 focus:ring-orange-500">
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="notifications_enabled" class="font-medium text-gray-700">
                                        Email Notifications
                                    </label>
                                    <p class="text-gray-500">Receive email notifications about course completions and certificates.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="pt-5">
                    <div class="flex justify-end">
                        <button type="submit"
                                class="inline-flex justify-center rounded-md border border-transparent bg-orange-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2">
                            Save Changes
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
