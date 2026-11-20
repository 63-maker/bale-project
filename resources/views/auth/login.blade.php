<x-layouts.guest>
    <div class="max-w-md mx-auto w-full" x-data="{ role: '{{ $activeTab ?? 'student' }}' }">
        <div class="bg-white border border-slate-200 rounded p-8">
            <div class="mb-6 text-center">
                <h2 class="text-xl font-bold tracking-tight text-[#172B4D]">System Authentication</h2>
                <p class="text-xs text-slate-500 mt-1">Select your designated portal role to access your account</p>
            </div>

            <x-alert />

            <!-- Tab Switcher -->
            <div class="grid grid-cols-3 gap-1 bg-slate-100 p-1 rounded mb-6 text-xs font-semibold">
                <button type="button" 
                        @click="role = 'student'" 
                        :class="role === 'student' ? 'bg-white text-[#172B4D] shadow-xs' : 'text-slate-600 hover:text-slate-900'"
                        class="py-2 rounded transition text-center">
                    Student
                </button>
                <button type="button" 
                        @click="role = 'management'" 
                        :class="role === 'management' ? 'bg-white text-[#172B4D] shadow-xs' : 'text-slate-600 hover:text-slate-900'"
                        class="py-2 rounded transition text-center">
                    Management
                </button>
                <button type="button" 
                        @click="role = 'admin'" 
                        :class="role === 'admin' ? 'bg-white text-[#172B4D] shadow-xs' : 'text-slate-600 hover:text-slate-900'"
                        class="py-2 rounded transition text-center">
                    Admin
                </button>
            </div>

            <!-- Student Login Form -->
            <form x-show="role === 'student'" method="POST" action="{{ route('login.student') }}" class="space-y-4">
                @csrf
                <div>
                    <label for="student_register_number" class="block text-xs font-medium text-slate-700 mb-1">
                        College Register Number
                    </label>
                    <input type="text" 
                           name="register_number" 
                           id="student_register_number" 
                           value="{{ old('register_number') }}"
                           required 
                           autofocus
                           placeholder="e.g. 2026CSE001"
                           class="w-full px-3 py-2 text-xs rounded border border-slate-300 focus:outline-none focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB]">
                </div>

                <div>
                    <label for="student_password" class="block text-xs font-medium text-slate-700 mb-1">
                        Password
                    </label>
                    <input type="password" 
                           name="password" 
                           id="student_password" 
                           required 
                           class="w-full px-3 py-2 text-xs rounded border border-slate-300 focus:outline-none focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB]">
                </div>

                <div class="flex items-center justify-between text-xs">
                    <label class="flex items-center gap-2 text-slate-600">
                        <input type="checkbox" name="remember" class="rounded border-slate-300 text-[#2563EB] focus:ring-0">
                        <span>Remember session</span>
                    </label>
                    <a href="{{ route('register') }}" class="text-[#2563EB] hover:underline font-medium">New student registration</a>
                </div>

                <button type="submit" class="w-full py-2.5 rounded bg-[#2563EB] text-white text-xs font-semibold hover:bg-blue-700 transition">
                    Sign In as Student
                </button>
            </form>

            <!-- Management Login Form -->
            <form x-show="role === 'management'" method="POST" action="{{ route('login.management') }}" class="space-y-4" style="display: none;">
                @csrf
                <div>
                    <label for="mgmt_identifier" class="block text-xs font-medium text-slate-700 mb-1">
                        Employee ID or Email Address
                    </label>
                    <input type="text" 
                           name="identifier" 
                           id="mgmt_identifier" 
                           value="{{ old('identifier') }}"
                           required 
                           placeholder="e.g. EMP1024 or staff@college.edu"
                           class="w-full px-3 py-2 text-xs rounded border border-slate-300 focus:outline-none focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB]">
                </div>

                <div>
                    <label for="mgmt_password" class="block text-xs font-medium text-slate-700 mb-1">
                        Password
                    </label>
                    <input type="password" 
                           name="password" 
                           id="mgmt_password" 
                           required 
                           class="w-full px-3 py-2 text-xs rounded border border-slate-300 focus:outline-none focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB]">
                </div>

                <div class="flex items-center justify-between text-xs">
                    <label class="flex items-center gap-2 text-slate-600">
                        <input type="checkbox" name="remember" class="rounded border-slate-300 text-[#2563EB] focus:ring-0">
                        <span>Remember session</span>
                    </label>
                    <span class="text-slate-400 text-[11px]">Accounts created by Admin</span>
                </div>

                <button type="submit" class="w-full py-2.5 rounded bg-[#172B4D] text-white text-xs font-semibold hover:bg-slate-900 transition">
                    Sign In as Management
                </button>
            </form>

            <!-- Admin Login Form -->
            <form x-show="role === 'admin'" method="POST" action="{{ route('login.admin') }}" class="space-y-4" style="display: none;">
                @csrf
                <div>
                    <label for="admin_identifier" class="block text-xs font-medium text-slate-700 mb-1">
                        Administrator Email or ID
                    </label>
                    <input type="text" 
                           name="identifier" 
                           id="admin_identifier" 
                           value="{{ old('identifier') }}"
                           required 
                           placeholder="e.g. admin@college.edu"
                           class="w-full px-3 py-2 text-xs rounded border border-slate-300 focus:outline-none focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB]">
                </div>

                <div>
                    <label for="admin_password" class="block text-xs font-medium text-slate-700 mb-1">
                        Password
                    </label>
                    <input type="password" 
                           name="password" 
                           id="admin_password" 
                           required 
                           class="w-full px-3 py-2 text-xs rounded border border-slate-300 focus:outline-none focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB]">
                </div>

                <div class="flex items-center justify-between text-xs">
                    <label class="flex items-center gap-2 text-slate-600">
                        <input type="checkbox" name="remember" class="rounded border-slate-300 text-[#2563EB] focus:ring-0">
                        <span>Remember session</span>
                    </label>
                    <span class="text-slate-400 text-[11px]">Protected Administrator Portal</span>
                </div>

                <button type="submit" class="w-full py-2.5 rounded bg-[#172B4D] text-white text-xs font-semibold hover:bg-slate-900 transition">
                    Sign In as Administrator
                </button>
            </form>
        </div>
    </div>
</x-layouts.guest>
