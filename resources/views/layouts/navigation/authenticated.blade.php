<div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
        <span>{{ __('Dashboard') }}</span>
        @if(auth()->user()->courses->count() > 0)
            <span class="ml-2 px-2 py-0.5 text-xs font-medium bg-green-100 text-green-800 rounded-full">
                {{ auth()->user()->courses->count() }}
            </span>
        @endif
    </x-nav-link>

    <x-nav-link :href="route('courses.index')" :active="request()->routeIs('courses.*')">
        {{ __('Courses') }}
    </x-nav-link>
</div>
