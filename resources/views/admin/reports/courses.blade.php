@extends('layouts.admin')

@section('content')
<div class="p-6 space-y-6 bg-gray-50">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <h2 class="text-2xl font-bold text-gray-900">Courses Report</h2>
        <div class="flex gap-3">
            <button onclick="window.print()" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2">
                <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                </svg>
                Print Report
            </button>
        </div>
    </div>

    <!-- Course Stats Overview -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-4">
        <div class="p-6 bg-white rounded-lg shadow">
            <h3 class="text-sm font-medium text-gray-500">Total Courses</h3>
            <p class="mt-2 text-3xl font-bold text-blue-600">{{ number_format($totalCourses) }}</p>
        </div>
        <div class="p-6 bg-white rounded-lg shadow">
            <h3 class="text-sm font-medium text-gray-500">Total Enrollments</h3>
            <p class="mt-2 text-3xl font-bold text-green-600">{{ number_format($totalEnrollments) }}</p>
        </div>
        <div class="p-6 bg-white rounded-lg shadow">
            <h3 class="text-sm font-medium text-gray-500">Average Completion Rate</h3>
            <p class="mt-2 text-3xl font-bold text-orange-600">{{ number_format($averageCompletionRate, 1) }}%</p>
        </div>
        <div class="p-6 bg-white rounded-lg shadow">
            <h3 class="text-sm font-medium text-gray-500">Active Courses</h3>
            <p class="mt-2 text-3xl font-bold text-purple-600">{{ number_format($activeCourses) }}</p>
        </div>
    </div>

    <!-- Popular Courses -->
    <div class="overflow-hidden bg-white rounded-lg shadow">
        <div class="px-6 py-5 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Most Popular Courses</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Course</th>
                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-center text-gray-500 uppercase">Enrollments</th>
                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-center text-gray-500 uppercase">Completion Rate</th>
                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-center text-gray-500 uppercase">Revenue</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($popularCourses as $course)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $course->title }}</div>
                                    <div class="text-sm text-gray-500">{{ $course->category }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-center text-gray-500 whitespace-nowrap">
                            {{ number_format($course->enrollments_count) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center justify-center">
                                <div class="w-full h-2 mx-4 bg-gray-200 rounded-full" style="max-width: 100px;">
                                    <div class="h-2 bg-green-600 rounded-full" style="width: {{ $course->completion_rate }}%"></div>
                                </div>
                                <span class="text-sm text-gray-500">{{ number_format($course->completion_rate, 1) }}%</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-center text-gray-500 whitespace-nowrap">
                            £{{ number_format($course->revenue, 2) }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Course Completion Rates Chart -->
    <div class="p-6 bg-white rounded-lg shadow">
        <h3 class="mb-4 text-lg font-medium text-gray-900">Course Completion Trends</h3>
        <div style="height: 300px;">
            <canvas id="completionChart"></canvas>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('completionChart').getContext('2d');
    const courseData = @json($courseCompletionRates);
    
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: courseData.map(course => course.title),
            datasets: [{
                label: 'Completion Rate',
                data: courseData.map(course => course.completion_rate),
                backgroundColor: 'rgba(34, 197, 94, 0.5)',
                borderColor: 'rgb(34, 197, 94)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    ticks: {
                        callback: function(value) {
                            return value + '%';
                        }
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
});
</script>
@endpush
@endsection
