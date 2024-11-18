@extends('layouts.business')

@section('content')
<div class="min-h-screen bg-gray-50/50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="md:flex md:items-center md:justify-between mb-8">
            <div class="flex-1 min-w-0">
                <h2 class="text-3xl font-bold leading-7 text-gray-900 sm:truncate sm:text-4xl sm:tracking-tight">
                    Employee Certificates
                </h2>
                <p class="mt-2 text-lg text-gray-600">
                    View and download course completion certificates for all employees
                </p>
            </div>
        </div>

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
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">{{ $employee->user->name }}</h3>
                                <p class="mt-1 text-sm text-gray-500">{{ $employee->user->email }}</p>
                            </div>
                            <a href="{{ route('business.certificates.employee', $employee->id) }}" 
                               class="inline-flex items-center gap-x-2 rounded-xl bg-orange-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-orange-500 active:bg-orange-700 transition-colors duration-200">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                View Certificates
                            </a>
                        </div>
                        
                        @if($employee->completedCourses->isEmpty())
                            <p class="text-sm text-gray-500 italic">No completed courses yet</p>
                        @else
                            <div class="mt-4 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                @foreach($employee->completedCourses as $course)
                                    <div class="flex items-center justify-between bg-gray-50 rounded-lg p-4">
                                        <div>
                                            <h4 class="font-medium text-gray-900">{{ $course->title }}</h4>
                                            <p class="text-sm text-gray-500">
                                                Completed {{ Carbon\Carbon::parse($course->completed_at)->format('M d, Y') }}
                                            </p>
                                        </div>
                                        <a href="{{ route('business.certificates.download', ['employeeId' => $course->employee_id, 'courseId' => $course->course_id]) }}" 
                                           class="inline-flex items-center gap-x-1 rounded-lg bg-white px-2.5 py-1.5 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                                            <svg class="w-4 h-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                                            </svg>
                                            Download
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

            <div class="mt-6">
                {{ $employees->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
