<x-guest-layout>
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
        <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
            <h2 class="text-2xl font-bold text-center mb-6">Construction Training LMS</h2>
            <p class="text-gray-600 text-center mb-6">Please enter the site password to continue</p>
            
            @if (session('error'))
                <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-600 rounded-md text-sm">
                    {{ session('error') }}
                </div>
            @endif

            <form method="POST" action="{{ route('site.password.check') }}" class="space-y-6">
                @csrf

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                    <input id="password" 
                           type="password" 
                           name="password" 
                           required 
                           autofocus
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <div>
                    <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Access Site
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>
