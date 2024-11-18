@extends('layouts.business')

@section('content')
<div class="min-h-screen bg-gray-50/50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="md:flex md:items-center md:justify-between mb-8">
            <div class="flex-1 min-w-0">
                <h2 class="text-3xl font-bold leading-7 text-gray-900 sm:truncate sm:text-4xl sm:tracking-tight">
                    Allocate Course Access
                </h2>
                <div class="mt-2 flex flex-col sm:flex-row sm:flex-wrap sm:space-x-6">
                    <div class="mt-2 flex items-center text-lg text-gray-600">
                        {{ $purchase->course->name }}
                    </div>
                    <div class="mt-2 flex items-center text-sm text-gray-600">
                        <div class="flex items-center gap-x-2">
                            <svg class="h-5 w-5 text-gray-400" viewBox="0 0 24 24" fill="none">
                                <path d="M18 18v-3a3 3 0 00-3-3H9a3 3 0 00-3 3v3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M12 12a3 3 0 100-6 3 3 0 000 6z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <span class="inline-flex items-center rounded-lg bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-700/10">
                                {{ $purchase->available_seats }} seats available
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-5 flex lg:ml-4 lg:mt-0 space-x-3">
                <a href="{{ route('business.courses.purchases') }}"
                   class="inline-flex items-center gap-x-2 rounded-xl bg-white px-5 py-3.5 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 transition-colors duration-200">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3" />
                    </svg>
                    Back to Purchases
                </a>
            </div>
        </div>

        @if(session('error'))
            <div class="rounded-xl bg-red-50 p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Allocation Form -->
        <div class="bg-white shadow-sm ring-1 ring-gray-900/5 sm:rounded-xl">
            <form action="{{ route('business.courses.allocate', $purchase) }}" method="POST">
                @csrf
                <div class="border-b border-gray-200">
                    <div class="p-4 sm:p-6">
                        <div class="sm:flex sm:items-center">
                            <div class="sm:flex-auto">
                                <h3 class="text-base font-semibold leading-6 text-gray-900">Select Employees</h3>
                                <p class="mt-2 text-sm text-gray-700">Choose the employees you want to give access to this course.</p>
                            </div>
                            <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
                                <button type="submit"
                                        class="inline-flex items-center gap-x-2 rounded-xl bg-orange-600 px-5 py-3.5 text-sm font-semibold text-white shadow-sm hover:bg-orange-500 active:bg-orange-700 transition-colors duration-200">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Allocate Access
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="p-4 sm:p-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        @forelse($availableEmployees as $employee)
                            <label for="employee_{{ $employee->id }}" 
                                   class="relative flex items-start p-4 cursor-pointer rounded-xl border border-gray-200 hover:bg-gray-50 transition-colors duration-200">
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center gap-x-3">
                                        <input type="checkbox"
                                               name="user_ids[]"
                                               value="{{ $employee->user->id }}"
                                               id="employee_{{ $employee->id }}"
                                               class="h-4 w-4 rounded border-gray-300 text-orange-600 focus:ring-orange-600">
                                        <div>
                                            <p class="font-medium text-gray-900">{{ $employee->user->name }}</p>
                                            <p class="text-sm text-gray-500">{{ $employee->user->email }}</p>
                                        </div>
                                    </div>
                                </div>
                            </label>
                        @empty
                            <div class="col-span-full">
                                <div class="text-center rounded-xl border-2 border-dashed border-gray-300 p-8">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                    <h3 class="mt-2 text-sm font-medium text-gray-900">No employees available</h3>
                                    <p class="mt-1 text-sm text-gray-500">All employees already have access to this course.</p>
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
