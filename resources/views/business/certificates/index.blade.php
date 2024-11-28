@extends('layouts.business')

@section('content')
<div class="min-h-screen bg-gray-50/50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="md:flex md:items-center md:justify-between mb-8">
            <div class="flex-1 min-w-0">
                <h2 class="text-3xl font-bold leading-7 text-gray-900 sm:truncate sm:text-4xl sm:tracking-tight">
                    @if(isset($employee))
                        {{ $employee->user->name }}'s Certificates
                    @else
                        Employee Certificates
                    @endif
                </h2>
                <p class="mt-2 text-lg text-gray-600">
                    @if(isset($employee))
                        View and download course completion certificates
                    @else
                        View and download course completion certificates for all employees
                    @endif
                </p>
            </div>
        </div>

        @if(isset($employee))
            <!-- Single Employee Certificates -->
            <div class="bg-white shadow-sm ring-1 ring-gray-900/5 sm:rounded-xl">
                @if($completedCourses->isEmpty())
                    <div class="px-4 py-12 text-center sm:px-6">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-semibold text-gray-900">No certificates found</h3>
                        <p class="mt-1 text-sm text-gray-500">This employee hasn't completed any courses yet.</p>
                    </div>
                @else
                    <div class="p-6 space-y-6">
                        @foreach($completedCourses as $course)
                            <div class="flex items-center justify-between border-b border-gray-200 pb-4 last:border-0 last:pb-0">
                                <div>
                                    <h4 class="text-lg font-medium text-gray-900">{{ $course->title }}</h4>
                                    <p class="mt-1 text-sm text-gray-500">
                                        Completed on {{ \Carbon\Carbon::parse($course->completed_at)->format('F j, Y') }}
                                    </p>
                                </div>
                                <a href="{{ route('business.certificates.download', ['employeeId' => $employee->id, 'courseId' => $course->course_id]) }}" 
                                   class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-orange-600 hover:bg-orange-700">
                                    Download Certificate
                                </a>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        @elseif(isset($employees))
            <!-- All Employees List -->
            @if($employees->isEmpty())
                <div class="bg-white shadow-sm ring-1 ring-gray-900/5 sm:rounded-xl">
                    <div class="px-4 py-12 text-center sm:px-6">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-semibold text-gray-900">No employees found</h3>
                        <p class="mt-1 text-sm text-gray-500">Get started by adding employees to your business.</p>
                        <div class="mt-6">
                            <a href="{{ route('business.employees.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-orange-600 hover:bg-orange-700">
                                Add Employee
                            </a>
                        </div>
                    </div>
                </div>
            @else
                <div class="bg-white shadow-sm ring-1 ring-gray-900/5 sm:rounded-xl divide-y divide-gray-200">
                    @foreach($employees as $employee)
                        <div class="p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">{{ $employee->user->name }}</h3>
                                    <p class="mt-1 text-sm text-gray-500">{{ $employee->user->email }}</p>
                                </div>
                                <a href="{{ route('business.certificates.employee', $employee->id) }}" 
                                   class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-orange-700 bg-orange-100 hover:bg-orange-200">
                                    View Certificates
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        @else
            <div class="bg-white shadow-sm ring-1 ring-gray-900/5 sm:rounded-xl">
                <div class="px-4 py-12 text-center sm:px-6">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-semibold text-gray-900">No data available</h3>
                    <p class="mt-1 text-sm text-gray-500">Please try refreshing the page or contact support if the issue persists.</p>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
