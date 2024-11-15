@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-semibold text-gray-900">
                Allocate {{ $purchase->course->name }}
                <span class="text-sm text-gray-500">
                    ({{ $purchase->availableSeats() }} seats available)
                </span>
            </h2>
            <a href="{{ route('business.courses.index') }}"
               class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                Back to Courses
            </a>
        </div>

        @if(session('error'))
            <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded relative">
                {{ session('error') }}
            </div>
        @endif

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <form action="{{ route('business.courses.allocate', $purchase) }}" method="POST">
                    @csrf

                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Select Employees</label>
                        @forelse($employees as $employee)
                            <div class="flex items-center space-x-3 mb-2">
                                <input type="checkbox"
                                       name="employee_ids[]"
                                       value="{{ $employee->id }}"
                                       id="employee_{{ $employee->id }}"
                                       class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <label for="employee_{{ $employee->id }}" class="text-sm text-gray-700">
                                    {{ $employee->user->name }}
                                </label>
                            </div>
                        @empty
                            <p class="text-gray-500">No employees available for allocation.</p>
                        @endforelse
                    </div>

                    <div class="flex justify-end">
                        <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                            Allocate to Selected Employees
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
