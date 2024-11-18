<tr id="employee-{{ $employee->id }}" class="bg-white hover:bg-gray-50/50">
    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
        {{ $employee->user->name }}
    </td>
    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
        {{ $employee->user->email }}
    </td>
    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
        {{ $employee->courseAllocations->count() }}
    </td>
    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
        @php
            $completedCourses = $employee->user->courses()->wherePivot('completed', true)->count();
        @endphp
        {{ $completedCourses }}
    </td>
    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
        <div class="flex justify-end items-center space-x-3">
            <!-- Edit Button -->
            <button type="button"
                    @click="$dispatch('open-edit-modal', { employee: {{ $employee->toJson() }} })"
                    class="text-gray-400 hover:text-gray-500">
                <span class="sr-only">Edit</span>
                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                </svg>
            </button>

            <!-- Delete Button -->
            <button type="button"
                    @click="$dispatch('confirm-delete', { 
                        id: {{ $employee->id }},
                        name: '{{ $employee->user->name }}',
                        url: '{{ route('business.employees.destroy', $employee) }}'
                    })"
                    class="text-gray-400 hover:text-red-500">
                <span class="sr-only">Delete</span>
                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                </svg>
            </button>

            <!-- Allocate Course Button -->
            <button type="button"
                    @click="$dispatch('open-allocate-modal', { employee: {{ $employee->toJson() }} })"
                    class="text-gray-400 hover:text-orange-500">
                <span class="sr-only">Allocate Course</span>
                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                </svg>
            </button>
        </div>
    </td>
</tr>
