<form method="POST" action="{{ route('password.update') }}" class="space-y-6">
    @csrf
    <input type="hidden" name="token" value="{{ $token }}">

    <!-- Email field -->
    <div>
        <label for="email" class="block text-sm font-medium text-gray-700">
            Email address
        </label>
        <input type="email" name="email" id="email" required
            class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-orange-500 focus:ring-orange-500">
    </div>

    <!-- Password field -->
    <div>
        <label for="password" class="block text-sm font-medium text-gray-700">
            New Password
        </label>
        <input type="password" name="password" id="password" required
            class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-orange-500 focus:ring-orange-500">
    </div>

    <!-- Password confirmation field -->
    <div>
        <label for="password_confirmation" class="block text-sm font-medium text-gray-700">
            Confirm Password
        </label>
        <input type="password" name="password_confirmation" id="password_confirmation" required
            class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-orange-500 focus:ring-orange-500">
    </div>

    <button type="submit"
        class="flex justify-center w-full px-4 py-2 text-sm font-medium text-white bg-orange-600 border border-transparent rounded-md shadow-sm hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
        Reset Password
    </button>
</form>
