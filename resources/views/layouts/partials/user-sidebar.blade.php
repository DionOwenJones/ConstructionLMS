<div class="h-full">
    <div class="p-6">
        <h2 class="text-lg font-semibold text-gray-900">Dashboard</h2>
        <nav class="mt-6">
            <a href="{{ route('dashboard') }}" class="flex items-center px-4 py-3 text-sm font-medium text-orange-600 rounded-lg bg-orange-50">
                <span class="flex items-center justify-center w-6 h-6 mr-3 text-sm text-white bg-orange-600 rounded-full">1</span>
                My Courses
            </a>
            <form method="POST" action="{{ route('logout') }}" class="mt-2">
                @csrf
                <button type="submit" class="flex items-center w-full px-4 py-3 text-sm font-medium text-gray-600 rounded-lg hover:bg-gray-50">
                    <span class="flex items-center justify-center w-6 h-6 mr-3 text-sm text-gray-400 bg-gray-100 rounded-full">2</span>
                    Logout
                </button>
            </form>
        </nav>
    </div>
</div>
