@extends('layouts.admin')

@section('content')
<div class="p-6 space-y-6 bg-gray-50">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <h2 class="text-2xl font-bold text-gray-900">Business Revenue Report</h2>
        <div class="flex gap-3">
            <button onclick="window.print()" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2">
                <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                </svg>
                Print Report
            </button>
        </div>
    </div>

    <!-- Revenue Overview -->
    <div class="p-6 bg-white rounded-lg shadow">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-lg font-medium text-gray-900">Total Revenue</h3>
                <p class="mt-2 text-4xl font-bold text-green-600">£{{ number_format($totalRevenue, 2) }}</p>
            </div>
            <div class="p-4 bg-green-50 rounded-full">
                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Top Courses -->
    <div class="p-6 bg-white rounded-lg shadow">
        <h3 class="mb-4 text-lg font-medium text-gray-900">Top Performing Courses (Last 12 Months)</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Course</th>
                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-center text-gray-500 uppercase">Total Purchases</th>
                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-right text-gray-500 uppercase">Total Revenue</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($topCourses as $course)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $course->title }}</div>
                        </td>
                        <td class="px-6 py-4 text-sm text-center text-gray-500 whitespace-nowrap">
                            {{ number_format($course->total_purchases) }}
                        </td>
                        <td class="px-6 py-4 text-sm text-right text-gray-900 whitespace-nowrap">
                            £{{ number_format($course->total_revenue, 2) }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Course Purchase Trends Chart -->
    <div class="p-6 bg-white rounded-lg shadow">
        <h3 class="mb-4 text-lg font-medium text-gray-900">Course Purchase Trends</h3>
        <div class="mt-4" style="height: 400px;">
            <canvas id="courseTrendsChart"></canvas>
        </div>
    </div>

    <!-- Monthly Revenue Chart -->
    <div class="p-6 bg-white rounded-lg shadow">
        <h3 class="text-lg font-medium text-gray-900">Monthly Revenue</h3>
        <div class="mt-4" style="height: 400px;">
            <canvas id="revenueChart"></canvas>
        </div>
    </div>

    <!-- Monthly Revenue Table -->
    <div class="overflow-hidden bg-white rounded-lg shadow">
        <div class="px-6 py-5 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Monthly Revenue Details</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Month</th>
                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-right text-gray-500 uppercase">Revenue</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($revenueData as $data)
                    <tr>
                        <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">
                            {{ \Carbon\Carbon::createFromFormat('Y-m', $data['month'])->format('F Y') }}
                        </td>
                        <td class="px-6 py-4 text-sm font-medium text-right text-gray-900 whitespace-nowrap">
                            £{{ number_format($data['total_revenue'], 2) }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Revenue Chart
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    const revenueData = @json($revenueData);
    
    new Chart(revenueCtx, {
        type: 'bar',
        data: {
            labels: revenueData.map(item => {
                const [year, month] = item.month.split('-');
                return new Date(year, month - 1).toLocaleDateString('en-GB', { month: 'short', year: 'numeric' });
            }),
            datasets: [{
                label: 'Revenue',
                data: revenueData.map(item => item.total_revenue),
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
                    ticks: {
                        callback: function(value) {
                            return '£' + value;
                        }
                    }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Revenue: £' + context.raw.toFixed(2);
                        }
                    }
                }
            }
        }
    });

    // Course Trends Chart
    const trendsCtx = document.getElementById('courseTrendsChart').getContext('2d');
    const courseTrends = @json($courseTrends);
    const topCourses = @json($topCourses);
    
    // Get all months in the last 12 months
    const months = Object.keys(courseTrends).sort();
    
    // Create datasets for top 5 courses
    const datasets = topCourses.map((course, index) => {
        const colors = [
            'rgb(34, 197, 94)',
            'rgb(59, 130, 246)',
            'rgb(249, 115, 22)',
            'rgb(168, 85, 247)',
            'rgb(236, 72, 153)'
        ];

        const monthlyData = months.map(month => {
            const monthData = courseTrends[month] || [];
            const courseData = monthlyData.find(data => data.id === course.id);
            return courseData ? courseData.purchase_count : 0;
        });

        return {
            label: course.title,
            data: monthlyData,
            borderColor: colors[index],
            backgroundColor: colors[index].replace('rgb', 'rgba').replace(')', ', 0.1)'),
            borderWidth: 2,
            fill: true,
            tension: 0.4
        };
    });

    new Chart(trendsCtx, {
        type: 'line',
        data: {
            labels: months.map(month => {
                const [year, m] = month.split('-');
                return new Date(year, m - 1).toLocaleDateString('en-GB', { month: 'short', year: 'numeric' });
            }),
            datasets: datasets
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            },
            plugins: {
                tooltip: {
                    mode: 'index',
                    intersect: false
                },
                legend: {
                    position: 'bottom',
                    labels: {
                        boxWidth: 12,
                        usePointStyle: true
                    }
                }
            }
        }
    });
});
</script>
@endpush
@endsection
