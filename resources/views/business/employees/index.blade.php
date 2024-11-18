@extends('layouts.business')

@section('content')
<div class="min-h-screen bg-gray-50/50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="md:flex md:items-center md:justify-between mb-8">
            <div class="flex-1 min-w-0">
                <h2 class="text-3xl font-bold leading-7 text-gray-900 sm:truncate sm:text-4xl sm:tracking-tight">Team Members</h2>
                <p class="mt-2 text-lg text-gray-600">Manage and track your team's progress and certifications.</p>
            </div>
            <div class="mt-4 flex md:ml-4 md:mt-0">
                <a href="{{ route('business.employees.create') }}" 
                   class="group relative inline-flex items-center justify-center px-6 py-3 bg-orange-600 hover:bg-orange-500 active:bg-orange-700 text-white text-sm font-medium rounded-xl shadow-lg shadow-orange-600/20 transition-all duration-200 ease-out hover:shadow-orange-600/40 hover:scale-[1.02] active:scale-[0.98]">
                    <span class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zM4 19.235v-.11a6.375 6.375 0 0112.75 0v.109A12.318 12.318 0 0110.374 21c-2.331 0-4.512-.645-6.374-1.766z" />
                        </svg>
                        Add Team Member
                    </span>
                </a>
            </div>
        </div>

        <!-- Team Members List -->
        <div class="bg-white shadow-sm ring-1 ring-gray-900/5 sm:rounded-xl">
            <div class="border-b border-gray-200">
                <div class="p-4 sm:p-6">
                    <div class="sm:flex sm:items-center">
                        <div class="sm:flex-auto">
                            <h3 class="text-base font-semibold leading-6 text-gray-900">Team Members</h3>
                            <p class="mt-2 text-sm text-gray-700">A list of all team members including their name, email, role, and status.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="divide-y divide-gray-200">
                @forelse($employees as $employee)
                <div class="p-4 sm:p-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center min-w-0 gap-x-4">
                            <img class="h-12 w-12 flex-none rounded-full bg-gray-50"
                                 src="{{ $employee->user->profile_photo_url ?? 'https://ui-avatars.com/api/?name='.urlencode($employee->user->name) }}"
                                 alt="{{ $employee->user->name }}">
                            <div class="min-w-0 flex-auto">
                                <p class="text-sm font-semibold leading-6 text-gray-900">{{ $employee->user->name }}</p>
                                <p class="mt-1 truncate text-sm leading-5 text-gray-500">{{ $employee->user->email }}</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-x-4 sm:gap-x-6">
                            <div class="hidden sm:flex sm:flex-col sm:items-end">
                                <p class="text-sm leading-6 text-gray-900">Team Member</p>
                                <div class="mt-1">
                                    <span class="inline-flex items-center rounded-full bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">
                                        Active
                                    </span>
                                </div>
                            </div>

                            <div class="flex flex-none items-center gap-x-4">
                                <div class="flex gap-x-4">
                                    <a href="{{ route('business.employees.edit', $employee->id) }}"
                                       class="rounded-md bg-white px-2.5 py-1.5 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                                        Edit
                                    </a>
                                    <form action="{{ route('business.employees.destroy', $employee->id) }}" 
                                          method="POST" 
                                          class="inline-block"
                                          onsubmit="return confirm('Are you sure you want to remove this employee?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="rounded-md bg-red-600 px-2.5 py-1.5 text-sm font-semibold text-white shadow-sm hover:bg-red-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red-600">
                                            Remove
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-semibold text-gray-900">No team members</h3>
                    <p class="mt-1 text-sm text-gray-500">Get started by adding a new team member.</p>
                </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($employees->hasPages())
            <div class="border-t border-gray-200 px-4 py-3 sm:px-6">
                {{ $employees->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
