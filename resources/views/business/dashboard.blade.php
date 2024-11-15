@extends('layouts.business')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <h1 class="text-2xl font-semibold text-gray-900 mb-6">Business Dashboard</h1>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- Total Employees Card -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="text-gray-900 text-xl font-semibold mb-2">Total Employees</div>
                <div class="text-3xl font-bold text-indigo-600">{{ $totalEmployees }}</div>
            </div>

            <!-- Total Courses Card -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="text-gray-900 text-xl font-semibold mb-2">Total Courses</div>
                <div class="text-3xl font-bold text-indigo-600">{{ $totalCourses }}</div>
            </div>

            <!-- Completed Courses Card -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="text-gray-900 text-xl font-semibold mb-2">Completed Courses</div>
                <div class="text-3xl font-bold text-indigo-600">{{ $completedCourses }}</div>
            </div>
        </div>

        <!-- Business Details -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Business Details</h2>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Company Name</label>
                    <div class="mt-1 text-sm text-gray-900">{{ $business->company_name }}</div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Contact Email</label>
                    <div class="mt-1 text-sm text-gray-900">{{ $business->contact_email ?? 'Not set' }}</div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Contact Phone</label>
                    <div class="mt-1 text-sm text-gray-900">{{ $business->contact_phone ?? 'Not set' }}</div>
                </div>
            </div>
        </div>

        <!-- Recent Course Completions -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mt-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Recent Course Completions</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Course</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Completed Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Certificate</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($recentCompletions as $completion)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $completion->employee_name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $completion->course_title }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $completion->completed_at->format('M d, Y') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <a href="{{ route('business.certificates.download', ['employee' => $completion->employee_id, 'course' => $completion->course_id]) }}"
                                   class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                                    Download Certificate
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
