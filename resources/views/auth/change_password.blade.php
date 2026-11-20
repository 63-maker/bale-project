<x-layouts.app :title="'Change Password'">
    <div class="max-w-md mx-auto">
        <div class="mb-6">
            <h1 class="text-xl font-bold tracking-tight text-[#172B4D]">Change Password</h1>
            <p class="text-xs text-slate-500">Update your account credentials to keep your account secure</p>
        </div>

        <div class="bg-white border border-slate-200 rounded p-6">
            <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label for="current_password" class="block text-xs font-medium text-slate-700 mb-1">
                        Current Password <span class="text-red-500">*</span>
                    </label>
                    <input type="password" name="current_password" id="current_password" required
                           class="w-full px-3 py-2 text-xs rounded border border-slate-300 focus:outline-none focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB]">
                </div>

                <div>
                    <label for="new_password" class="block text-xs font-medium text-slate-700 mb-1">
                        New Password (min 8 characters) <span class="text-red-500">*</span>
                    </label>
                    <input type="password" name="new_password" id="new_password" required
                           class="w-full px-3 py-2 text-xs rounded border border-slate-300 focus:outline-none focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB]">
                </div>

                <div>
                    <label for="new_password_confirmation" class="block text-xs font-medium text-slate-700 mb-1">
                        Confirm New Password <span class="text-red-500">*</span>
                    </label>
                    <input type="password" name="new_password_confirmation" id="new_password_confirmation" required
                           class="w-full px-3 py-2 text-xs rounded border border-slate-300 focus:outline-none focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB]">
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full py-2.5 rounded bg-[#2563EB] text-white text-xs font-semibold hover:bg-blue-700 transition">
                        Update Password
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
