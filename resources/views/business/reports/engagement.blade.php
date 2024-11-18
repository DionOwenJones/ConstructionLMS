@extends('layouts.business')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <h2 class="text-2xl font-semibold mb-6">Employee Engagement Report</h2>

                @if($engagementData->isEmpty())
                    <p class="text-gray-600">No engagement data available.</p>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($engagementData as $data)
                            <div class="bg-white rounded-lg shadow p-6 border border-gray-200">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-lg font-medium text-gray-900">{{ $data->name }}</h3>
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $data->average_progress >= 75 ? 'bg-green-100 text-green-800' : ($data->average_progress >= 50 ? 'bg-orange-100 text-orange-800' : 'bg-red-100 text-red-800') }}">
                                        {{ number_format($data->average_progress, 1) }}% Avg Progress
                                    </span>
                                </div>

                                <div class="space-y-4">
                                    <div>
                                        <div class="flex justify-between text-sm mb-1">
                                            <span class="text-gray-500">Enrolled Courses</span>
                                            <span class="font-medium">{{ $data->enrolled_courses }}</span>
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-2">
                                            <div class="bg-orange-600 h-2 rounded-full" style="width: {{ min(100, ($data->enrolled_courses / max(1, $data->enrolled_courses)) * 100) }}%"></div>
                                        </div>
                                    </div>

                                    <div>
                                        <div class="flex justify-between text-sm mb-1">
                                            <span class="text-gray-500">Completed Courses</span>
                                            <span class="font-medium">{{ $data->completed_courses }} / {{ $data->enrolled_courses }}</span>
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-2">
                                            <div class="bg-green-600 h-2 rounded-full" style="width: {{ ($data->completed_courses / max(1, $data->enrolled_courses)) * 100 }}%"></div>
                                        </div>
                                    </div>

                                    <div class="pt-4 border-t border-gray-200">
                                        <div class="flex justify-between items-center">
                                            <span class="text-sm text-gray-500">Last Activity:</span>
                                            <span class="text-sm font-medium">
                                                {{ $data->last_activity ? \Carbon\Carbon::parse($data->last_activity)->diffForHumans() : 'Never' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                <div class="mt-6">
                    <a href="{{ route('business.reports.export', ['type' => 'engagement']) }}" 
                       class="inline-flex items-center px-4 py-2 bg-orange-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-orange-700 focus:bg-orange-700 active:bg-orange-900 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        Export Report
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
