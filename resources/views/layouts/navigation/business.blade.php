<nav x-data="{ 
    employeesOpen: false, 
    coursesOpen: false, 
    reportsOpen: false, 
    profileOpen: false 
}" class="bg-white shadow">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <!-- Logo -->
            <div class="flex-shrink-0 flex items-center w-1/4">
                <a href="{{ route('business.dashboard') }}" class="flex items-center">
                    <span class="text-xl font-bold text-orange-600 truncate">
                        @if(Auth::user()->getBusiness() && Auth::user()->getBusiness()->company_name)
                            {{ Auth::user()->getBusiness()->company_name }}
                        @else
                            Business Dashboard
                        @endif
                    </span>
                </a>
            </div>

            <!-- Navigation Links -->
            <div class="hidden sm:flex flex-1 justify-center">
                <div class="inline-flex items-center space-x-8">
                    <!-- Dashboard -->
                    <a href="{{ route('business.dashboard') }}" 
                       class="{{ request()->routeIs('business.dashboard') ? 'border-orange-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }} inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                        Dashboard
                    </a>

                    <!-- Employees Dropdown -->
                    <div class="relative" @click.away="employeesOpen = false">
                        <button @click="employeesOpen = !employeesOpen" type="button"
                                class="{{ request()->routeIs('business.employees.*') ? 'border-orange-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }} inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                            <span>Employees</span>
                            @php
                                $employeeCount = Auth::user()->getBusiness() ? Auth::user()->getBusiness()->employees()->count() : 0;
                            @endphp
                            @if($employeeCount > 0)
                                <span class="ml-2 bg-orange-100 text-orange-600 px-2 py-0.5 rounded-full text-xs">
                                    {{ $employeeCount }}
                                </span>
                            @endif
                            <svg :class="{'rotate-180': employeesOpen}" class="ml-2 h-5 w-5 transform transition-transform duration-200" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                        <div x-show="employeesOpen" 
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 translate-y-1"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 translate-y-0"
                             x-transition:leave-end="opacity-0 translate-y-1"
                             class="absolute left-1/2 z-10 mt-3 w-screen max-w-xs -translate-x-1/2 transform px-2 sm:px-0"
                             style="display: none;">
                            <div class="overflow-hidden rounded-lg shadow-lg ring-1 ring-black ring-opacity-5">
                                <div class="relative grid gap-6 bg-white px-5 py-6 sm:gap-8 sm:p-8">
                                    <a href="{{ route('business.employees.index') }}" class="-m-3 block rounded-md p-3 transition duration-150 ease-in-out hover:bg-gray-50">
                                        <p class="text-base font-medium text-gray-900">
                                            All Employees
                                        </p>
                                    </a>
                                    <a href="{{ route('business.employees.create') }}" class="-m-3 block rounded-md p-3 transition duration-150 ease-in-out hover:bg-gray-50">
                                        <p class="text-base font-medium text-gray-900">
                                            Add Employee
                                        </p>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Courses Dropdown -->
                    <div class="relative" @click.away="coursesOpen = false">
                        <button @click="coursesOpen = !coursesOpen" type="button"
                                class="{{ request()->routeIs('business.courses.*') ? 'border-orange-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }} inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                            <span>Courses</span>
                            <svg :class="{'rotate-180': coursesOpen}" class="ml-2 h-5 w-5 transform transition-transform duration-200" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                        <div x-show="coursesOpen"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 translate-y-1"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 translate-y-0"
                             x-transition:leave-end="opacity-0 translate-y-1"
                             class="absolute left-1/2 z-10 mt-3 w-screen max-w-xs -translate-x-1/2 transform px-2 sm:px-0"
                             style="display: none;">
                            <div class="overflow-hidden rounded-lg shadow-lg ring-1 ring-black ring-opacity-5">
                                <div class="relative grid gap-6 bg-white px-5 py-6 sm:gap-8 sm:p-8">
                                    <a href="{{ route('business.courses.index') }}" class="-m-3 block rounded-md p-3 transition duration-150 ease-in-out hover:bg-gray-50">
                                        <p class="text-base font-medium text-gray-900">
                                            Purchased Courses
                                        </p>
                                    </a>
                                    <a href="{{ route('business.courses.available') }}" class="-m-3 block rounded-md p-3 transition duration-150 ease-in-out hover:bg-gray-50">
                                        <p class="text-base font-medium text-gray-900">
                                            Available Courses
                                        </p>
                                    </a>
                                    <a href="{{ route('business.courses.purchases') }}" class="-m-3 block rounded-md p-3 transition duration-150 ease-in-out hover:bg-gray-50">
                                        <p class="text-base font-medium text-gray-900">
                                            Purchase History
                                        </p>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Reports Dropdown -->
                    <div class="relative" @click.away="reportsOpen = false">
                        <button @click="reportsOpen = !reportsOpen" type="button"
                                class="{{ request()->routeIs('business.reports.*') ? 'border-orange-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }} inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                            <span>Reports</span>
                            <svg :class="{'rotate-180': reportsOpen}" class="ml-2 h-5 w-5 transform transition-transform duration-200" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                        <div x-show="reportsOpen"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 translate-y-1"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 translate-y-0"
                             x-transition:leave-end="opacity-0 translate-y-1"
                             class="absolute left-1/2 z-10 mt-3 w-screen max-w-xs -translate-x-1/2 transform px-2 sm:px-0"
                             style="display: none;">
                            <div class="overflow-hidden rounded-lg shadow-lg ring-1 ring-black ring-opacity-5">
                                <div class="relative grid gap-6 bg-white px-5 py-6 sm:gap-8 sm:p-8">
                                    <a href="{{ route('business.reports.progress') }}" class="-m-3 block rounded-md p-3 transition duration-150 ease-in-out hover:bg-gray-50">
                                        <p class="text-base font-medium text-gray-900">
                                            Progress Report
                                        </p>
                                    </a>
                                    <a href="{{ route('business.reports.completion') }}" class="-m-3 block rounded-md p-3 transition duration-150 ease-in-out hover:bg-gray-50">
                                        <p class="text-base font-medium text-gray-900">
                                            Completion Report
                                        </p>
                                    </a>
                                    <a href="{{ route('business.reports.engagement') }}" class="-m-3 block rounded-md p-3 transition duration-150 ease-in-out hover:bg-gray-50">
                                        <p class="text-base font-medium text-gray-900">
                                            Engagement Report
                                        </p>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Profile Dropdown -->
            <div class="flex items-center justify-end w-1/4">
                <div class="relative" @click.away="profileOpen = false">
                    <button @click="profileOpen = !profileOpen" type="button" 
                            class="flex rounded-full bg-white text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2">
                        <span class="sr-only">Open user menu</span>
                        <img class="h-8 w-8 rounded-full" src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}">
                    </button>
                    <div x-show="profileOpen"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 translate-y-1"
                         class="absolute right-0 z-10 mt-2 w-48 origin-top-right rounded-md bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
                         style="display: none;">
                        <a href="{{ route('business.profile') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Business Profile</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                Sign out
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>
