@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
            <div class="max-w-xl">
                @if (session('status'))
                    <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                        {{ session('status') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                        {{ session('error') }}
                    </div>
                @endif

                <section>
                    <header class="mb-6">
                        <h2 class="text-lg font-medium text-gray-900">
                            Profile Information
                        </h2>
                        <p class="mt-1 text-sm text-gray-600">
                            Update your account's profile information and password.
                        </p>
                    </header>

                    <form method="post" action="{{ route('profile.update') }}" class="space-y-6">
                        @csrf
                        @method('patch')

                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                            <input type="text" name="name" id="name" value="{{ old('name', Auth::user()->name) }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                            <input type="email" name="email" id="email" value="{{ old('email', Auth::user()->email) }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                            @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="current_password" class="block text-sm font-medium text-gray-700">Current Password</label>
                            <input type="password" name="current_password" id="current_password"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                            @error('current_password')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700">New Password</label>
                            <input type="password" name="password" id="password"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                            @error('password')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                            <input type="password" name="password_confirmation" id="password_confirmation"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                        </div>

                        <div class="flex items-center gap-4">
                            <button type="submit"
                                    class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                                Save Changes
                            </button>
                        </div>
                    </form>
                </section>

                @if(Auth::user()->role)
                    <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                        <h3 class="text-md font-medium text-gray-900">Your Role</h3>
                        <div class="mt-2">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                {{ ucfirst(Auth::user()->role) }}
                            </span>
                            
                            @if(Auth::user()->role === \App\Models\User::ROLE_USER)
                                <form method="POST" action="{{ route('profile.upgrade.business.legacy') }}" class="mt-4" id="upgradeForm">
                                    @csrf
                                    <button type="button"
                                        onclick="confirmUpgrade()"
                                        class="inline-flex items-center px-4 py-2 bg-orange-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-orange-700 focus:bg-orange-700 active:bg-orange-900 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                        Upgrade to Business Account
                                    </button>
                                </form>
                                <p class="mt-2 text-sm text-gray-600">
                                    Upgrade to a Business account to manage employees and purchase courses for your team.
                                </p>
                            @endif

                            <!-- Confirmation Modals -->
                            <!-- Upgrade Modal -->
                            <div id="confirmationModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 hidden" style="z-index: 100;">
                                <div class="flex items-center justify-center min-h-screen">
                                    <div class="bg-white rounded-lg p-8 max-w-md w-full mx-4">
                                        <h3 class="text-lg font-medium text-gray-900 mb-4">Confirm Account Upgrade</h3>
                                        <p class="text-sm text-gray-600 mb-6">
                                            <strong class="text-red-600">Important:</strong> Upgrading to a Business Account will:
                                            <ul class="list-disc ml-5 mt-2">
                                                <li>Convert your account to a Business Account</li>
                                                <li>Remove all your currently enrolled courses</li>
                                                <li>You'll need to purchase courses as a business after upgrade</li>
                                            </ul>
                                        </p>
                                        <div class="flex justify-end gap-4">
                                            <button type="button" onclick="hideConfirmationModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                                                Cancel
                                            </button>
                                            <button type="button" onclick="submitUpgradeForm()" class="px-4 py-2 text-sm font-medium text-white bg-orange-600 rounded-md hover:bg-orange-700">
                                                Confirm Upgrade
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <script>
                                function confirmUpgrade() {
                                    document.getElementById('confirmationModal').classList.remove('hidden');
                                }

                                function hideConfirmationModal() {
                                    document.getElementById('confirmationModal').classList.add('hidden');
                                }

                                function submitUpgradeForm() {
                                    document.getElementById('upgradeForm').submit();
                                }
                            </script>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function confirmUpgrade() {
        document.getElementById('confirmationModal').classList.remove('hidden');
    }

    function hideConfirmationModal() {
        document.getElementById('confirmationModal').classList.add('hidden');
    }

    function submitUpgradeForm() {
        document.getElementById('upgradeForm').submit();
    }
</script>
@endpush
