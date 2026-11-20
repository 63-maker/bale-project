<x-layouts.app :title="'My Profile'">
    <div class="max-w-3xl mx-auto">
        <div class="mb-6">
            <h1 class="text-xl font-bold tracking-tight text-slate-900">My Profile</h1>
            <p class="text-xs text-slate-500">Manage your account information and institutional credentials</p>
        </div>

        <div class="bg-white border border-slate-200 rounded p-6 mb-6">
            <h2 class="text-sm font-semibold text-slate-900 mb-4">Account Information</h2>

            <form method="POST" action="{{ route('profile.update') }}" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label for="full_name" class="block text-xs font-medium text-slate-700 mb-1">Full Name</label>
                    <input type="text" name="full_name" id="full_name" value="{{ old('full_name', $user->full_name) }}" required
                           class="w-full px-3 py-2 text-xs rounded border border-slate-300 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-slate-500 mb-1">Email Address</label>
                        <input type="email" value="{{ $user->email }}" disabled
                               class="w-full px-3 py-2 text-xs rounded border border-slate-200 bg-slate-50 text-slate-500 cursor-not-allowed">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-slate-500 mb-1">Role</label>
                        <input type="text" value="{{ ucfirst($user->role) }}" disabled
                               class="w-full px-3 py-2 text-xs rounded border border-slate-200 bg-slate-50 text-slate-500 cursor-not-allowed capitalize">
                    </div>
                </div>

                @if($user->isStudent())
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-slate-500 mb-1">Register Number</label>
                            <input type="text" value="{{ $user->register_number }}" disabled
                                   class="w-full px-3 py-2 text-xs rounded border border-slate-200 bg-slate-50 text-slate-500 cursor-not-allowed">
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-slate-500 mb-1">Year of Study</label>
                            <input type="text" value="{{ $user->study_year }} Year" disabled
                                   class="w-full px-3 py-2 text-xs rounded border border-slate-200 bg-slate-50 text-slate-500 cursor-not-allowed">
                        </div>
                    </div>
                @elseif($user->isManagement())
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-slate-500 mb-1">Employee ID</label>
                            <input type="text" value="{{ $user->employee_id }}" disabled
                                   class="w-full px-3 py-2 text-xs rounded border border-slate-200 bg-slate-50 text-slate-500 cursor-not-allowed">
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-slate-500 mb-1">Department</label>
                            <input type="text" value="{{ $user->department?->name ?? 'N/A' }}" disabled
                                   class="w-full px-3 py-2 text-xs rounded border border-slate-200 bg-slate-50 text-slate-500 cursor-not-allowed">
                        </div>
                    </div>
                @endif

                <div class="pt-2">
                    <button type="submit" class="px-4 py-2 rounded bg-blue-600 text-white text-xs font-semibold hover:bg-blue-700 transition">
                        Update Profile
                    </button>
                </div>
            </form>
        </div>

        <div class="bg-white border border-slate-200 rounded p-6 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-semibold text-slate-900">Security & Password</h3>
                <p class="text-xs text-slate-500 mt-0.5">Ensure your account uses a secure password</p>
            </div>
            <a href="{{ route('password.change') }}" class="px-3.5 py-2 rounded border border-slate-300 text-slate-700 text-xs font-semibold hover:bg-slate-50 transition">
                Change Password
            </a>
        </div>
    </div>
</x-layouts.app>
