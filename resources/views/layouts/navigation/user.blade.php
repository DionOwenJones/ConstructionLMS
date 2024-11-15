<nav class="bg-white border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <!-- Left Side (Logo) -->
            <div class="flex-shrink-0 flex items-center">
                <a href="/">
                    <span class="text-xl font-bold">{{ config('app.name') }}</span>
                </a>
            </div>

            <!-- Middle (Navigation Links) -->
            @include('layouts.navigation.authenticated')

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
