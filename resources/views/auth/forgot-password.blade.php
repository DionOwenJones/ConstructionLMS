<form method="POST" action="{{ route('password.email') }}" class="space-y-6">
    @csrf
    <!-- Email field -->
    <div>
        <label for="email" class="block text-sm font-medium text-gray-700">
            Email address
        </label>
        <input type="email" name="email" id="email" required
            class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-orange-500 focus:ring-orange-500">
    </div>

    <button type="submit"
        class="flex justify-center w-full px-4 py-2 text-sm font-medium text-white bg-orange-600 border border-transparent rounded-md shadow-sm hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
        Send Password Reset Link
    </button>
</form>
