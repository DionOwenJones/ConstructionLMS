<div x-data="{ open: false }"
     @keydown.escape.window="open = false">
    
    <!-- Trigger Button -->
    <button @click="open = true"
            class="group relative inline-flex items-center justify-center px-6 py-3 bg-orange-600 hover:bg-orange-500 active:bg-orange-700 text-white text-sm font-medium rounded-xl shadow-lg shadow-orange-600/20 transition-all duration-200 ease-out hover:shadow-orange-600/40 hover:scale-[1.02] active:scale-[0.98]">
        <span class="flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zM4 19.235v-.11a6.375 6.375 0 0112.75 0v.109A12.318 12.318 0 0110.374 21c-2.331 0-4.512-.645-6.374-1.766z" />
            </svg>
            Add Team Member
        </span>
    </button>

    <!-- Modal Container -->
    <div x-show="open"
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto"
         @click.self="open = false">

        <!-- Backdrop -->
        <div x-show="open"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-gray-900/75 backdrop-blur-sm"></div>

        <!-- Modal Content -->
        <div class="relative min-h-screen flex items-center justify-center p-4">
            <div x-show="open"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="relative bg-white rounded-2xl shadow-2xl ring-1 ring-gray-900/5 max-w-lg w-full mx-auto overflow-hidden">

                <!-- Header -->
                <div class="px-6 py-8 border-b border-gray-100">
                    <div class="flex items-start justify-between">
                        <div class="space-y-1">
                            <h2 class="text-2xl font-semibold tracking-tight text-gray-900">Add New Team Member</h2>
                            <p class="text-base text-gray-500">Add a new member to your team and manage their access.</p>
                        </div>
                        <button type="button"
                                @click="open = false"
                                class="flex items-center justify-center w-8 h-8 rounded-full text-gray-400 hover:text-gray-500 hover:bg-gray-100 transition-colors duration-200">
                            <span class="sr-only">Close</span>
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>

                <form action="{{ route('business.employees.store') }}" method="POST">
                    @csrf
                    <!-- Body -->
                    <div class="px-6 py-8 space-y-6">
                        <!-- Name Input -->
                        <div class="space-y-2">
                            <label for="name" class="block text-sm font-medium text-gray-900">Full Name</label>
                            <div class="group relative mt-1.5">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400 group-focus-within:text-orange-500 transition-colors duration-200" 
                                         viewBox="0 0 24 24" 
                                         fill="none" 
                                         stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                    </svg>
                                </div>
                                <input type="text"
                                       name="name"
                                       id="name"
                                       required
                                       class="block w-full rounded-xl border-0 py-3.5 pl-11 pr-4 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-orange-500 sm:text-sm sm:leading-6 transition-shadow duration-200"
                                       placeholder="John Doe">
                            </div>
                        </div>

                        <!-- Email Input -->
                        <div class="space-y-2">
                            <label for="email" class="block text-sm font-medium text-gray-900">Email Address</label>
                            <div class="group relative mt-1.5">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400 group-focus-within:text-orange-500 transition-colors duration-200" 
                                         viewBox="0 0 24 24" 
                                         fill="none" 
                                         stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                                    </svg>
                                </div>
                                <input type="email"
                                       name="email"
                                       id="email"
                                       required
                                       class="block w-full rounded-xl border-0 py-3.5 pl-11 pr-4 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-orange-500 sm:text-sm sm:leading-6 transition-shadow duration-200"
                                       placeholder="john@example.com">
                            </div>
                        </div>

                        <!-- Password Input -->
                        <div class="space-y-2">
                            <label for="password" class="block text-sm font-medium text-gray-900">Password</label>
                            <div class="group relative mt-1.5">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400 group-focus-within:text-orange-500 transition-colors duration-200" 
                                         viewBox="0 0 24 24" 
                                         fill="none" 
                                         stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                                    </svg>
                                </div>
                                <input type="password"
                                       name="password"
                                       id="password"
                                       required
                                       class="block w-full rounded-xl border-0 py-3.5 pl-11 pr-4 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-orange-500 sm:text-sm sm:leading-6 transition-shadow duration-200"
                                       placeholder="••••••••">
                            </div>
                            <p class="mt-2.5 text-sm text-gray-500 flex items-center gap-1.5">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none">
                                    <path d="M12 16V12M12 8H12.01M22 12C22 17.5228 17.5228 22 12 22C6.47715 22 2 17.5228 2 12C2 6.47715 6.47715 2 12 2C17.5228 2 22 6.47715 22 12Z" 
                                          stroke="currentColor" 
                                          stroke-width="2" 
                                          stroke-linecap="round" 
                                          stroke-linejoin="round"/>
                                </svg>
                                Password must be at least 8 characters long
                            </p>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="bg-gray-50/50 px-6 py-6 flex flex-col sm:flex-row-reverse gap-3 border-t border-gray-100">
                        <button type="submit"
                                class="inline-flex justify-center items-center px-5 py-3.5 sm:w-auto w-full rounded-xl bg-orange-600 text-sm font-semibold text-white shadow-sm hover:bg-orange-500 active:bg-orange-700 transition-colors duration-200 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-orange-600">
                            Add Team Member
                        </button>
                        <button type="button"
                                @click="open = false"
                                class="inline-flex justify-center items-center px-5 py-3.5 sm:w-auto w-full rounded-xl bg-white text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 active:bg-gray-100 transition-colors duration-200">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Any additional JavaScript
    });
</script>
@endpush
