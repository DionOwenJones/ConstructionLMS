@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="max-w-3xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold text-gray-900">Edit Allocation</h1>
            <a href="{{ route('admin.allocations.index') }}" class="text-orange-600 hover:text-orange-900">
                Back to Allocations
            </a>
        </div>

        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Please fix the following errors:</strong>
                <ul class="mt-2 list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white shadow sm:rounded-lg">
            <form action="{{ route('admin.allocations.update', $allocation) }}" method="POST" class="space-y-6 p-6">
                @csrf
                @method('PUT')

                <!-- User Information (Read-only) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">User</label>
                    <div class="mt-1 p-3 bg-gray-50 rounded-md">
                        <p class="text-sm text-gray-900">{{ $allocation->user->name }}</p>
                        <p class="text-sm text-gray-500">{{ $allocation->user->email }}</p>
                    </div>
                </div>

                <!-- Course Information (Read-only) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Course</label>
                    <div class="mt-1 p-3 bg-gray-50 rounded-md">
                        <p class="text-sm text-gray-900">{{ $allocation->course->title }}</p>
                    </div>
                </div>

                <!-- Expiry Date -->
                <div>
                    <label for="expires_at" class="block text-sm font-medium text-gray-700">Expiry Date (Optional)</label>
                    <input type="date" name="expires_at" id="expires_at" 
                           value="{{ old('expires_at', $allocation->expires_at ? $allocation->expires_at->format('Y-m-d') : '') }}"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500 sm:text-sm">
                    <p class="mt-1 text-sm text-gray-500">Leave blank for no expiry</p>
                </div>

                <!-- Notes -->
                <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700">Notes (Optional)</label>
                    <textarea id="notes" name="notes" rows="3"
                              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500 sm:text-sm"
                              placeholder="Add any notes about this allocation">{{ old('notes', $allocation->notes) }}</textarea>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="bg-orange-600 hover:bg-orange-700 text-white font-bold py-2 px-4 rounded">
                        Update Allocation
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
