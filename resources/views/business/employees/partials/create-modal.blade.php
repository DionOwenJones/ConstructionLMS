<div x-data="{ open: false }"
     @keydown.escape.window="open = false">

    <!-- Trigger Button -->
    <button @click="open = true"
            class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-indigo-500 to-indigo-600 hover:from-indigo-600 hover:to-indigo-700 rounded-full text-white shadow-lg transition-all duration-200 ease-in-out transform hover:scale-105">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
        </svg>
        Add Team Member
    </button>

    <!-- Modal Container -->
    <div x-show="open"
         class="fixed inset-0 z-50 overflow-y-auto"
         style="display: none;"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">

        <!-- Backdrop -->
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity"
             @click="open = false"></div>

        <!-- Modal Content -->
        <div class="relative min-h-screen flex items-center justify-center p-4">
            <div class="relative bg-white rounded-2xl shadow-xl max-w-md w-full mx-auto"
                 @click.stop
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95">

                <form action="{{ route('business.employees.store') }}" method="POST">
                    @csrf
                    <!-- Header -->
                    <div class="relative bg-gradient-to-r from-indigo-500 to-indigo-600 p-6 rounded-t-2xl">
                        <h5 class="text-xl font-semibold text-white">Welcome a New Team Member ✨</h5>
                        <button type="button"
                                @click="open = false"
                                class="absolute top-4 right-4 text-white hover:text-indigo-100 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Body -->
                    <div class="p-6 space-y-6">
                        <!-- Name Input -->
                        <div class="relative">
                            <label class="block text-sm font-medium text-slate-700 mb-2" for="name">Full Name</label>
                            <div class="relative">
                                <input id="name"
                                       class="form-input w-full pl-10 rounded-xl border-slate-200 hover:border-slate-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 transition-colors duration-200"
                                       type="text"
                                       name="name"
                                       placeholder="John Doe"
                                       required />
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <!-- Email Input -->
                        <div class="relative">
                            <label class="block text-sm font-medium text-slate-700 mb-2" for="email">Email Address</label>
                            <div class="relative">
                                <input id="email"
                                       class="form-input w-full pl-10 rounded-xl border-slate-200 hover:border-slate-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 transition-colors duration-200"
                                       type="email"
                                       name="email"
                                       placeholder="john@example.com"
                                       required />
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <!-- Password Input -->
                        <div class="relative">
                            <label class="block text-sm font-medium text-slate-700 mb-2" for="password">Password</label>
                            <div class="relative">
                                <input id="password"
                                       class="form-input w-full pl-10 rounded-xl border-slate-200 hover:border-slate-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 transition-colors duration-200"
                                       type="password"
                                       name="password"
                                       required />
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="bg-slate-50 border-t border-slate-100 p-4 flex justify-end space-x-3 rounded-b-2xl">
                        <button type="button"
                                @click="open = false"
                                class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-100 hover:border-slate-300 transition-colors duration-200">
                            Cancel
                        </button>
                        <button type="submit"
                                class="px-4 py-2 rounded-xl bg-gradient-to-r from-indigo-500 to-indigo-600 hover:from-indigo-600 hover:to-indigo-700 text-white shadow-sm hover:shadow transition-all duration-200">
                            Add Team Member
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
        // Initialize Alpine.js if needed
        if (typeof Alpine !== 'undefined') {
            Alpine.start();
        }
    });
</script>
@endpush
