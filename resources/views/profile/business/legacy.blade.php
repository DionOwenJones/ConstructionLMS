@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-orange-50/40 via-white to-orange-50/20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white/80 backdrop-blur-xl rounded-2xl shadow-lg ring-1 ring-gray-100/50 overflow-hidden">
            <div class="p-6 sm:p-8">
                <h2 class="text-2xl font-bold bg-gradient-to-r from-gray-900 via-gray-800 to-gray-900 bg-clip-text text-transparent mb-6">Business Account Setup</h2>
                
                <form method="POST" action="{{ route('profile.upgrade.business.legacy.store') }}" class="space-y-6">
                    @csrf

                    <!-- Business Name -->
                    <div>
                        <label for="business_name" class="block text-sm font-medium text-gray-700">Business Name</label>
                        <input type="text" name="business_name" id="business_name" required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500">
                    </div>

                    <!-- Business Address -->
                    <div>
                        <label for="address" class="block text-sm font-medium text-gray-700">Business Address</label>
                        <input type="text" name="address" id="address" required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500">
                    </div>

                    <!-- Phone -->
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700">Phone Number</label>
                        <input type="tel" name="phone" id="phone" required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500">
                    </div>

                    <div class="flex items-center justify-end mt-6">
                        <button type="submit"
                                class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                            Complete Setup
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
