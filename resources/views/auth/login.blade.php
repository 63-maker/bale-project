<x-layouts.guest>
    <div class="max-w-md mx-auto w-full" x-data="{ role: '{{ $activeTab ?? 'student' }}' }">
        <div class="bg-white border border-slate-200 rounded p-8 shadow-xs">
            <div class="mb-6 text-center">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded text-[11px] font-semibold bg-slate-100 text-slate-800 border border-slate-200 mb-2">
                    College Complaint Portal
                </span>
                <h2 class="text-xl font-bold tracking-tight text-slate-900">System Authentication</h2>
                <p class="text-xs text-slate-500 mt-1">Select your designated portal role to access your account</p>
            </div>

            <x-alert />

            <!-- One-Click Demo Credentials Bar -->
            <div class="mb-5 bg-slate-50 border border-slate-200 rounded p-3">
                <div class="text-[10px] font-semibold text-slate-500 uppercase tracking-wider mb-2 flex items-center justify-between">
                    <span>Quick Demo Sign-In</span>
                    <span class="text-[10px] text-slate-400 font-normal">Click to pre-fill</span>
                </div>
                <div class="grid grid-cols-3 gap-1.5">
                    <button type="button" 
                            @click="role = 'student'; $nextTick(() => { document.getElementById('student_register_number').value = '2026CSE001'; document.getElementById('student_password').value = 'Student@12345'; });"
                            class="px-2 py-1.5 text-[11px] font-semibold rounded border border-slate-300 bg-white text-slate-800 hover:border-blue-600 hover:text-blue-600 hover:bg-blue-50/40 transition text-center truncate">
                        Student Demo
                    </button>
                    <button type="button" 
                            @click="role = 'management'; $nextTick(() => { document.getElementById('mgmt_identifier').value = 'EMP1001'; document.getElementById('mgmt_password').value = 'Management@12345'; });"
                            class="px-2 py-1.5 text-[11px] font-semibold rounded border border-slate-300 bg-white text-slate-800 hover:border-blue-600 hover:text-blue-600 hover:bg-blue-50/40 transition text-center truncate">
                        Management Demo
                    </button>
                    <button type="button" 
                            @click="role = 'admin'; $nextTick(() => { document.getElementById('admin_identifier').value = 'admin@college.edu'; document.getElementById('admin_password').value = 'Admin@12345'; });"
                            class="px-2 py-1.5 text-[11px] font-semibold rounded border border-slate-300 bg-white text-slate-800 hover:border-blue-600 hover:text-blue-600 hover:bg-blue-50/40 transition text-center truncate">
                        Admin Demo
                    </button>
                </div>
            </div>

            <!-- Tab Switcher -->
            <div class="grid grid-cols-3 gap-1 bg-slate-100 p-1 rounded mb-5 text-xs font-semibold">
                <button type="button" 
                        @click="role = 'student'" 
                        :class="role === 'student' ? 'bg-white text-slate-900 shadow-2xs font-bold' : 'text-slate-600 hover:text-slate-900'"
                        class="py-2 rounded transition text-center">
                    Student
                </button>
                <button type="button" 
                        @click="role = 'management'" 
                        :class="role === 'management' ? 'bg-white text-slate-900 shadow-2xs font-bold' : 'text-slate-600 hover:text-slate-900'"
                        class="py-2 rounded transition text-center">
                    Management
                </button>
                <button type="button" 
                        @click="role = 'admin'" 
                        :class="role === 'admin' ? 'bg-white text-slate-900 shadow-2xs font-bold' : 'text-slate-600 hover:text-slate-900'"
                        class="py-2 rounded transition text-center">
                    Admin
                </button>
            </div>

            <!-- Student Login Form -->
            <form x-show="role === 'student'" method="POST" action="{{ route('login.student') }}" class="space-y-4">
                @csrf
                <div class="p-2.5 bg-slate-50 border border-slate-200 rounded text-[11px] text-slate-700 leading-relaxed">
                    Sign in with your <strong>College Register Number</strong> to submit grievances, monitor progress, and review updates.
                </div>

                <div>
                    <label for="student_register_number" class="block text-xs font-medium text-slate-700 mb-1">
                        College Register Number
                    </label>
                    <input type="text" 
                           name="register_number" 
                           id="student_register_number" 
                           value="{{ old('register_number', '2026CSE001') }}"
                           required 
                           autofocus
                           placeholder="e.g. 2026CSE001"
                           class="w-full px-3 py-2 text-xs rounded border border-slate-300 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600">
                </div>

                <div>
                    <label for="student_password" class="block text-xs font-medium text-slate-700 mb-1">
                        Password
                    </label>
                    <input type="password" 
                           name="password" 
                           id="student_password" 
                           value="Student@12345"
                           required 
                           class="w-full px-3 py-2 text-xs rounded border border-slate-300 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600">
                </div>

                <div class="flex items-center justify-between text-xs pt-1">
                    <label class="flex items-center gap-2 text-slate-600 cursor-pointer">
                        <input type="checkbox" name="remember" class="rounded border-slate-300 text-blue-600 focus:ring-0">
                        <span>Remember session</span>
                    </label>
                    <a href="{{ route('register') }}" class="text-blue-600 hover:text-blue-700 hover:underline font-medium">New student registration</a>
                </div>

                <button type="submit" class="w-full py-2.5 rounded bg-blue-600 text-white text-xs font-semibold hover:bg-blue-700 transition shadow-2xs">
                    Sign In as Student
                </button>
            </form>

            <!-- Management Login Form -->
            <form x-show="role === 'management'" method="POST" action="{{ route('login.management') }}" class="space-y-4" style="display: none;">
                @csrf
                <div class="p-2.5 bg-slate-50 border border-slate-200 rounded text-[11px] text-slate-700 leading-relaxed">
                    Sign in with your <strong>Employee ID</strong> or official email to investigate assigned department complaints.
                </div>

                <div>
                    <label for="mgmt_identifier" class="block text-xs font-medium text-slate-700 mb-1">
                        Employee ID or Official Email
                    </label>
                    <input type="text" 
                           name="identifier" 
                           id="mgmt_identifier" 
                           value="{{ old('identifier', 'EMP1001') }}"
                           required 
                           placeholder="e.g. EMP1001 or mgmt.cse@college.edu"
                           class="w-full px-3 py-2 text-xs rounded border border-slate-300 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600">
                </div>

                <div>
                    <label for="mgmt_password" class="block text-xs font-medium text-slate-700 mb-1">
                        Password
                    </label>
                    <input type="password" 
                           name="password" 
                           id="mgmt_password" 
                           value="Management@12345"
                           required 
                           class="w-full px-3 py-2 text-xs rounded border border-slate-300 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600">
                </div>

                <div class="flex items-center justify-between text-xs pt-1">
                    <label class="flex items-center gap-2 text-slate-600 cursor-pointer">
                        <input type="checkbox" name="remember" class="rounded border-slate-300 text-blue-600 focus:ring-0">
                        <span>Remember session</span>
                    </label>
                    <span class="text-slate-400 text-[11px]">Accounts provisioned by Admin</span>
                </div>

                <button type="submit" class="w-full py-2.5 rounded bg-slate-900 text-white text-xs font-semibold hover:bg-black transition shadow-2xs">
                    Sign In as Management
                </button>
            </form>

            <!-- Admin Login Form -->
            <form x-show="role === 'admin'" method="POST" action="{{ route('login.admin') }}" class="space-y-4" style="display: none;">
                @csrf
                <div class="p-2.5 bg-slate-50 border border-slate-200 rounded text-[11px] text-slate-700 leading-relaxed">
                    Sign in with your <strong>Administrator credentials</strong> for institutional verification and management.
                </div>

                <div>
                    <label for="admin_identifier" class="block text-xs font-medium text-slate-700 mb-1">
                        Administrator Email or ID
                    </label>
                    <input type="text" 
                           name="identifier" 
                           id="admin_identifier" 
                           value="{{ old('identifier', 'admin@college.edu') }}"
                           required 
                           placeholder="e.g. admin@college.edu"
                           class="w-full px-3 py-2 text-xs rounded border border-slate-300 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600">
                </div>

                <div>
                    <label for="admin_password" class="block text-xs font-medium text-slate-700 mb-1">
                        Password
                    </label>
                    <input type="password" 
                           name="password" 
                           id="admin_password" 
                           value="Admin@12345"
                           required 
                           class="w-full px-3 py-2 text-xs rounded border border-slate-300 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600">
                </div>

                <div class="flex items-center justify-between text-xs pt-1">
                    <label class="flex items-center gap-2 text-slate-600 cursor-pointer">
                        <input type="checkbox" name="remember" class="rounded border-slate-300 text-blue-600 focus:ring-0">
                        <span>Remember session</span>
                    </label>
                    <span class="text-slate-400 text-[11px]">Protected Administrator Portal</span>
                </div>

                <button type="submit" class="w-full py-2.5 rounded bg-slate-900 text-white text-xs font-semibold hover:bg-black transition shadow-2xs">
                    Sign In as Administrator
                </button>
            </form>
        </div>
    </div>
</x-layouts.guest>
