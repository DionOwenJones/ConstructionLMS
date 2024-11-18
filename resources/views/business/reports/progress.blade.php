@extends('layouts.business')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <h2 class="text-2xl font-semibold mb-6">Employee Progress Report</h2>

                @foreach($progressData as $data)
                    <div class="mb-8 bg-gray-50 p-6 rounded-lg">
                        <h3 class="text-xl font-medium mb-4">{{ $data['employee']->user->name }}</h3>
                        
                        @if($data['courses']->isEmpty())
                            <p class="text-gray-600">No courses enrolled.</p>
                        @else
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                @foreach($data['courses'] as $courseData)
                                    <div class="bg-white p-4 rounded-lg shadow">
                                        <h4 class="font-medium mb-2">{{ $courseData['course']->title }}</h4>
                                        <div class="space-y-2">
                                            <div class="flex justify-between text-sm">
                                                <span class="text-gray-600">Progress:</span>
                                                <span class="font-medium">{{ $courseData['progress'] }}%</span>
                                            </div>
                                            <div class="w-full bg-gray-200 rounded-full h-2">
                                                <div class="bg-orange-600 h-2 rounded-full" style="width: {{ $courseData['progress'] }}%"></div>
                                            </div>
                                            <div class="flex justify-between text-sm">
                                                <span class="text-gray-600">Status:</span>
                                                <span class="font-medium {{ $courseData['completed'] ? 'text-green-600' : 'text-orange-600' }}">
                                                    {{ $courseData['completed'] ? 'Completed' : 'In Progress' }}
                                                </span>
                                            </div>
                                            @if($courseData['last_accessed'])
                                                <div class="flex justify-between text-sm">
                                                    <span class="text-gray-600">Last Activity:</span>
                                                    <span class="font-medium">{{ \Carbon\Carbon::parse($courseData['last_accessed'])->diffForHumans() }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach

                <div class="mt-6">
                    <a href="{{ route('business.reports.export', ['type' => 'progress']) }}" 
                       class="inline-flex items-center px-4 py-2 bg-orange-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-orange-700 focus:bg-orange-700 active:bg-orange-900 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        Export Report
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
