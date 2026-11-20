<x-layouts.guest>
    <div class="max-w-lg mx-auto w-full">
        <div class="bg-white border border-slate-200 rounded p-8">
            <div class="mb-6">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded text-[11px] font-semibold bg-slate-100 text-slate-800 border border-slate-200 mb-2">
                    College Complaint Portal
                </span>
                <h2 class="text-xl font-bold tracking-tight text-slate-900">Student Self-Registration</h2>
                <p class="text-xs text-slate-500 mt-1">Create an institutional student account to submit and monitor complaints</p>
            </div>

            <x-alert />

            <form method="POST" action="{{ route('register.post') }}" class="space-y-4">
                @csrf

                <div>
                    <label for="full_name" class="block text-xs font-medium text-slate-700 mb-1">
                        Full Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="full_name" 
                           id="full_name" 
                           value="{{ old('full_name') }}"
                           required 
                           placeholder="e.g. John Doe"
                           class="w-full px-3 py-2 text-xs rounded border border-slate-300 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="register_number" class="block text-xs font-medium text-slate-700 mb-1">
                            Register Number <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="register_number" 
                               id="register_number" 
                               value="{{ old('register_number') }}"
                               required 
                               placeholder="e.g. 2026CSE001"
                               class="w-full px-3 py-2 text-xs rounded border border-slate-300 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600">
                    </div>

                    <div>
                        <label for="email" class="block text-xs font-medium text-slate-700 mb-1">
                            College Email <span class="text-red-500">*</span>
                        </label>
                        <input type="email" 
                               name="email" 
                               id="email" 
                               value="{{ old('email') }}"
                               required 
                               placeholder="e.g. student@college.edu"
                               class="w-full px-3 py-2 text-xs rounded border border-slate-300 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="department_id" class="block text-xs font-medium text-slate-700 mb-1">
                            Department <span class="text-red-500">*</span>
                        </label>
                        <select name="department_id" 
                                id="department_id" 
                                required 
                                class="w-full px-3 py-2 text-xs rounded border border-slate-300 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 bg-white">
                            <option value="">Select Department</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>
                                    {{ $dept->name }} ({{ $dept->code }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="study_year" class="block text-xs font-medium text-slate-700 mb-1">
                            Year of Study <span class="text-red-500">*</span>
                        </label>
                        <select name="study_year" 
                                id="study_year" 
                                required 
                                class="w-full px-3 py-2 text-xs rounded border border-slate-300 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 bg-white">
                            <option value="">Select Year</option>
                            <option value="1" {{ old('study_year') == '1' ? 'selected' : '' }}>1st Year</option>
                            <option value="2" {{ old('study_year') == '2' ? 'selected' : '' }}>2nd Year</option>
                            <option value="3" {{ old('study_year') == '3' ? 'selected' : '' }}>3rd Year</option>
                            <option value="4" {{ old('study_year') == '4' ? 'selected' : '' }}>4th Year</option>
                            <option value="5" {{ old('study_year') == '5' ? 'selected' : '' }}>5th Year</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="password" class="block text-xs font-medium text-slate-700 mb-1">
                            Password (min 8 chars) <span class="text-red-500">*</span>
                        </label>
                        <input type="password" 
                               name="password" 
                               id="password" 
                               required 
                               class="w-full px-3 py-2 text-xs rounded border border-slate-300 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600">
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-xs font-medium text-slate-700 mb-1">
                            Confirm Password <span class="text-red-500">*</span>
                        </label>
                        <input type="password" 
                               name="password_confirmation" 
                               id="password_confirmation" 
                               required 
                               class="w-full px-3 py-2 text-xs rounded border border-slate-300 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600">
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full py-2.5 rounded bg-blue-600 text-white text-xs font-semibold hover:bg-blue-700 transition">
                        Register Student Account
                    </button>
                </div>

                <div class="text-center text-xs text-slate-600 pt-2">
                    Already registered? 
                    <a href="{{ route('login', ['role' => 'student']) }}" class="text-blue-600 font-medium hover:underline">
                        Sign in to your account
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-layouts.guest>
