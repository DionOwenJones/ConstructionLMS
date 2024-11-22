@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Report Results</h2>
            <p class="mt-1 text-sm text-gray-500">Showing results for {{ request('type') }} between {{ request('date_from') }} and {{ request('date_to') }}.</p>
        </div>
        <div class="flex mt-4 space-x-3 sm:mt-0">
            <a href="{{ route('admin.reports.index') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                Back to Reports
            </a>
        </div>
    </div>

    <!-- Results Table -->
    <div class="flex flex-col">
        <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                <div class="overflow-hidden border-b border-gray-200 shadow sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                @switch(request('type'))
                                    @case('users')
                                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Name</th>
                                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Email</th>
                                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Role</th>
                                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Joined</th>
                                        @break
                                    @case('businesses')
                                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Business Name</th>
                                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Owner</th>
                                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Email</th>
                                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Created</th>
                                        @break
                                    @case('courses')
                                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Title</th>
                                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Category</th>
                                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Status</th>
                                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Created</th>
                                        @break
                                    @case('allocations')
                                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Business</th>
                                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Course</th>
                                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Status</th>
                                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Created</th>
                                        @break
                                @endswitch
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($data as $item)
                                <tr>
                                    @switch(request('type'))
                                        @case('users')
                                            <td class="px-6 py-4 whitespace-nowrap">{{ $item->name }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ $item->email }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ ucfirst($item->role) }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ $item->created_at->format('M d, Y') }}</td>
                                            @break
                                        @case('businesses')
                                            <td class="px-6 py-4 whitespace-nowrap">{{ $item->name }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ $item->owner->name }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ $item->email }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ $item->created_at->format('M d, Y') }}</td>
                                            @break
                                        @case('courses')
                                            <td class="px-6 py-4 whitespace-nowrap">{{ $item->title }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ $item->category }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="inline-flex px-2 text-xs font-semibold leading-5 text-{{ $item->published ? 'green' : 'gray' }}-800 bg-{{ $item->published ? 'green' : 'gray' }}-100 rounded-full">
                                                    {{ $item->published ? 'Published' : 'Draft' }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ $item->created_at->format('M d, Y') }}</td>
                                            @break
                                        @case('allocations')
                                            <td class="px-6 py-4 whitespace-nowrap">{{ $item->business->name }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ $item->course->title }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="inline-flex px-2 text-xs font-semibold leading-5 text-{{ $item->isExpired() ? 'red' : 'green' }}-800 bg-{{ $item->isExpired() ? 'red' : 'green' }}-100 rounded-full">
                                                    {{ $item->isExpired() ? 'Expired' : 'Active' }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ $item->created_at->format('M d, Y') }}</td>
                                            @break
                                    @endswitch
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-4 text-sm text-center text-gray-500">No results found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
