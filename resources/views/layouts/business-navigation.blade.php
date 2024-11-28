<!-- Business Navigation -->
<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex h-16">
            <!-- Left Side (Logo) -->
            <div class="flex-shrink-0 flex items-center w-1/4">
                <a href="{{ route('business.dashboard') }}">
                    <span class="text-xl font-bold text-orange-600 truncate">
                        {{ Auth::user()->business ? Auth::user()->business->company_name : 'Business Dashboard' }}
                    </span>
                </a>
            </div>

            <!-- Center Navigation Links -->
            <div class="hidden sm:flex flex-1 items-center justify-center">
                <div class="inline-flex items-center space-x-8">
                    <x-nav-link :href="route('business.dashboard')" :active="request()->routeIs('business.dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>

                    @if(Auth::user()->isBusinessOwner())
                        <x-nav-link :href="route('business.certificates.index')" :active="request()->routeIs('business.certificates.*')">
                            {{ __('Certificates') }}
                        </x-nav-link>

                        <x-nav-link :href="route('business.employees.index')" :active="request()->routeIs('business.employees.*')">
                            {{ __('Employees') }}
                        </x-nav-link>

                        <x-nav-link :href="route('business.courses.available')" :active="request()->routeIs('business.courses.*')">
                            {{ __('Courses') }}
                        </x-nav-link>
                    @endif
                </div>
            </div>

            <!-- Right Side (Settings Dropdown) -->
            <div class="hidden sm:flex items-center justify-end w-1/4">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ml-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('business.profile')" class="flex items-center">
                            <svg class="mr-3 h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {{ __('Business Profile') }}
                        </x-dropdown-link>

                        <div class="border-t border-gray-100 my-1"></div>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full px-4 py-2 text-left text-sm leading-5 text-red-600 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out flex items-center">
                                <svg class="mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                                {{ __('Log Out') }}
                            </button>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="flex items-center sm:hidden">
                <button @click="open = !open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': !open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile menu -->
    <div :class="{'block': open, 'hidden': !open}" class="sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('business.dashboard')" :active="request()->routeIs('business.dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>

            @if(Auth::user()->isBusinessOwner())
                <x-responsive-nav-link :href="route('business.certificates.index')" :active="request()->routeIs('business.certificates.*')">
                    {{ __('Certificates') }}
                </x-responsive-nav-link>

                <x-responsive-nav-link :href="route('business.employees.index')" :active="request()->routeIs('business.employees.*')">
                    {{ __('Employees') }}
                </x-responsive-nav-link>

                <x-responsive-nav-link :href="route('business.courses.available')" :active="request()->routeIs('business.courses.*')">
                    {{ __('Courses') }}
                </x-responsive-nav-link>
            @endif
        </div>

        <!-- Mobile menu profile section -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('business.profile')">
                    {{ __('Business Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form-mobile').submit();" class="block pl-3 pr-4 py-2 text-base font-medium text-red-600 hover:text-red-700 hover:bg-gray-50 focus:outline-none transition duration-150 ease-in-out">
                    {{ __('Log Out') }}
                </a>
                <form id="logout-form-mobile" action="{{ route('logout') }}" method="POST" class="hidden">
                    @csrf
                </form>
            </div>
        </div>
    </div>
</nav>
