<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <!-- Left Side (Logo) -->
            <div class="flex-shrink-0 flex items-center">
                <a href="{{ route('business.dashboard') }}">
                    <span class="text-xl font-bold">{{ Auth::user()->business->company_name }}</span>
                </a>
            </div>

            <!-- Middle (Navigation Links) -->
            <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                <x-nav-link :href="route('business.dashboard')" :active="request()->routeIs('business.dashboard')">
                    {{ __('Dashboard') }}
                </x-nav-link>

                <x-nav-link :href="route('business.employees.index')" :active="request()->routeIs('business.employees.*')">
                    <span>{{ __('Team Members') }}</span>
                    @if($employeeCount = auth()->user()->business->employees->count())
                        <span class="ml-2 px-2 py-0.5 text-xs font-medium bg-indigo-100 text-indigo-800 rounded-full">
                            {{ $employeeCount }}
                        </span>
                    @endif
                </x-nav-link>

                <x-nav-link :href="route('business.courses.index')" :active="request()->routeIs('business.courses.*')">
                    <span>{{ __('Courses') }}</span>
                    @if($courseCount > 0)
                        <span class="ml-2 px-2 py-0.5 text-xs font-medium bg-green-100 text-green-800 rounded-full">
                            {{ $courseCount }}
                        </span>
                    @endif
                </x-nav-link>
            </div>

            <!-- Right Side (User Dropdown) -->
            <x-navigation-dropdown>
                <x-dropdown-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-dropdown-link>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-dropdown-link :href="route('logout')"
                            onclick="event.preventDefault();
                                    this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-dropdown-link>
                </form>
            </x-navigation-dropdown>
        </div>
    </div>
</nav>
