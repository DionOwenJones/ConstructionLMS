@extends('layouts.business')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="mb-6">
                    <h2 class="text-2xl font-bold text-gray-800">Employee Certificates</h2>
                    <p class="text-gray-600 mt-1">View and download certificates for your employees' completed courses.</p>
                </div>

                @if($employees->isEmpty())
                    <div class="text-center py-8">
                        <p class="text-gray-500 text-lg">No employees found. Add employees to manage their certificates.</p>
                        <a href="{{ route('business.employees.create') }}" class="mt-4 inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700">
                            Add Employee
                        </a>
                    </div>
                @else
                    <div class="grid grid-cols-1 gap-6">
                        @foreach($employees as $employee)
                            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                                <div class="p-6">
                                    <div class="flex items-center justify-between mb-4">
                                        <div>
                                            <h3 class="text-xl font-semibold text-gray-900">{{ $employee->user->name }}</h3>
                                            <p class="text-sm text-gray-500">{{ $employee->user->email }}</p>
                                        </div>
                                        <span class="px-3 py-1 text-sm rounded-full {{ $employee->completedCourses->count() > 0 ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                            {{ $employee->completedCourses->count() }} {{ Str::plural('Course', $employee->completedCourses->count()) }} Completed
                                        </span>
                                    </div>

                                    @if($employee->completedCourses->isEmpty())
                                        <p class="text-gray-500 text-sm">No completed courses yet.</p>
                                    @else
                                        <div class="space-y-4">
                                            @foreach($employee->completedCourses as $course)
                                                <div class="flex items-center justify-between py-3 border-b border-gray-100 last:border-0">
                                                    <div>
                                                        <h4 class="font-medium text-gray-900">{{ $course->title }}</h4>
                                                        <p class="text-sm text-gray-500">
                                                            Completed {{ Carbon\Carbon::parse($course->pivot->completed_at)->format('M d, Y') }}
                                                        </p>
                                                    </div>
                                                    <a href="{{ route('business.certificates.download', ['employeeId' => $employee->id, 'courseId' => $course->id]) }}"
                                                       class="inline-flex items-center px-3 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700">
                                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                        </svg>
                                                        Download Certificate
                                                    </a>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-6">
                        {{ $employees->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection