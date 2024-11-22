@extends('layouts.admin')

@section('content')
<div class="p-6 space-y-6 bg-gray-50">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <h2 class="text-2xl font-bold text-gray-900">Users Report</h2>
        <div class="flex gap-3">
            <button onclick="window.print()" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2">
                <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                </svg>
                Print Report
            </button>
        </div>
    </div>

    <!-- User Stats Overview -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-4">
        <div class="p-6 bg-white rounded-lg shadow">
            <h3 class="text-sm font-medium text-gray-500">Total Users</h3>
            <p class="mt-2 text-3xl font-bold text-blue-600">{{ number_format($userStats['total']) }}</p>
        </div>
        <div class="p-6 bg-white rounded-lg shadow">
            <h3 class="text-sm font-medium text-gray-500">New This Month</h3>
            <p class="mt-2 text-3xl font-bold text-green-600">{{ number_format($userStats['new_this_month']) }}</p>
        </div>
        <div class="p-6 bg-white rounded-lg shadow">
            <h3 class="text-sm font-medium text-gray-500">Active Users</h3>
            <p class="mt-2 text-3xl font-bold text-orange-600">{{ number_format($userStats['active']) }}</p>
        </div>
        <div class="p-6 bg-white rounded-lg shadow">
            <h3 class="text-sm font-medium text-gray-500">Average Courses per User</h3>
            <p class="mt-2 text-3xl font-bold text-purple-600">
                {{ number_format($users->avg('course_purchases_count') + $users->avg('course_allocations_count'), 1) }}
            </p>
        </div>
    </div>

    <!-- User Roles Distribution -->
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <!-- Role Distribution Chart -->
        <div class="p-6 bg-white rounded-lg shadow">
            <h3 class="mb-4 text-lg font-medium text-gray-900">User Roles Distribution</h3>
            <div style="height: 300px;">
                <canvas id="roleDistributionChart"></canvas>
            </div>
        </div>

        <!-- User Activity Stats -->
        <div class="p-6 bg-white rounded-lg shadow">
            <h3 class="mb-4 text-lg font-medium text-gray-900">User Activity Overview</h3>
            <dl class="grid grid-cols-1 gap-4">
                @foreach($userStats['roles'] as $role)
                <div class="p-4 bg-gray-50 rounded-lg">
                    <dt class="text-sm font-medium text-gray-500">{{ ucfirst($role->role) }}</dt>
                    <dd class="mt-1">
                        <div class="flex items-center">
                            <div class="flex-1">
                                <div class="h-2 bg-gray-200 rounded-full">
                                    <div class="h-2 bg-blue-600 rounded-full" style="width: {{ ($role->count / $userStats['total']) * 100 }}%"></div>
                                </div>
                            </div>
                            <span class="ml-3 text-sm font-medium text-gray-900">{{ number_format($role->count) }}</span>
                        </div>
                    </dd>
                </div>
                @endforeach
            </dl>
        </div>
    </div>

    <!-- Users Table -->
    <div class="overflow-hidden bg-white rounded-lg shadow">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">User Details</h3>
            <div class="flex items-center gap-4">
                <span class="text-sm text-gray-600">Total {{ $users->total() }} users</span>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">User</th>
                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Role</th>
                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Joined</th>
                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-center text-gray-500 uppercase">Courses</th>
                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-center text-gray-500 uppercase">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($users as $user)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 w-10 h-10">
                                    <div class="flex items-center justify-center w-10 h-10 bg-blue-100 rounded-full">
                                        <span class="text-lg font-medium text-blue-600">{{ substr($user->name, 0, 1) }}</span>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $user->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex px-2 text-xs font-semibold leading-5 {{ $user->role === 'admin' ? 'text-green-800 bg-green-100' : 'text-gray-800 bg-gray-100' }} rounded-full">
                                {{ ucfirst($user->role) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                            {{ $user->created_at->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 text-sm text-center text-gray-500 whitespace-nowrap">
                            {{ $user->course_purchases_count + $user->course_allocations_count }}
                        </td>
                        <td class="px-6 py-4 text-sm text-center whitespace-nowrap">
                            <span class="inline-flex px-2 text-xs font-semibold leading-5 {{ $user->course_purchases_count + $user->course_allocations_count > 0 ? 'text-green-800 bg-green-100' : 'text-gray-800 bg-gray-100' }} rounded-full">
                                {{ $user->course_purchases_count + $user->course_allocations_count > 0 ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $users->links() }}
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('roleDistributionChart').getContext('2d');
    const roleData = @json($userStats['roles']);
    
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: roleData.map(role => role.role.charAt(0).toUpperCase() + role.role.slice(1)),
            datasets: [{
                data: roleData.map(role => role.count),
                backgroundColor: [
                    '#3b82f6',
                    '#22c55e',
                    '#f97316',
                    '#a855f7'
                ],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            },
            cutout: '70%'
        }
    });
});
</script>
@endpush
@endsection
