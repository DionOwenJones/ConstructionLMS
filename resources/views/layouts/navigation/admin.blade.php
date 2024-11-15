<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <!-- Left Side (Logo) -->
            <div class="flex-shrink-0 flex items-center">
                <a href="{{ route('admin.dashboard') }}">
                    <span class="text-xl font-bold">Admin Panel</span>
                </a>
            </div>

            <!-- Middle (Navigation Links) -->
            <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                    Dashboard
                </x-nav-link>
                <x-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')">
                    Users
                </x-nav-link>
                <x-nav-link :href="route('admin.courses.index')" :active="request()->routeIs('admin.courses.*')">
                    Courses
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
