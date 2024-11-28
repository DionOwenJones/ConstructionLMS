@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <div>
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Manage Businesses') }}
        </h2>
    </div>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex justify-between mb-6">
                        <div class="flex items-center">
                            <input type="text" placeholder="Search businesses..." class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                        </div>
                        <a href="{{ route('admin.businesses.create') }}" class="px-4 py-2 text-white bg-orange-500 rounded-lg hover:bg-orange-600">Add Business</a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Name</th>
                                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Owner</th>
                                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Email</th>
                                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Active Courses</th>
                                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($businesses as $business)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $business->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $business->owner->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $business->email }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $business->allocations_count ?? 0 }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex space-x-3">
                                            <a href="{{ route('admin.businesses.show', $business) }}" class="text-orange-600 hover:text-orange-900">View</a>
                                            <a href="{{ route('admin.businesses.edit', $business) }}" class="text-blue-600 hover:text-blue-900">Edit</a>
                                            <form action="{{ route('admin.businesses.destroy', $business) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure you want to delete this business?')">Delete</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $businesses->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
