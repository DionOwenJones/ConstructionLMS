@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white shadow-sm rounded-2xl p-6">
            <div class="text-center mb-8">
                <h2 class="text-2xl font-bold text-gray-900">Business Setup</h2>
                <p class="mt-2 text-sm text-gray-600">Complete your business profile to get started</p>
            </div>

            <form action="{{ route('business.setup.store') }}" method="POST" class="space-y-6">
                @csrf

                <!-- Company Name -->
                <div>
                    <label for="company_name" class="block text-sm font-medium text-gray-700">Company Name</label>
                    <div class="mt-1">
                        <input type="text" 
                               name="company_name" 
                               id="company_name" 
                               class="shadow-sm focus:ring-orange-500 focus:border-orange-500 block w-full sm:text-sm border-gray-300 rounded-md @error('company_name') border-red-300 @enderror"
                               value="{{ old('company_name') }}"
                               required>
                    </div>
                    @error('company_name')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit Button -->
                <div class="pt-4">
                    <button type="submit" 
                            class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                        Create Business Profile
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
