<x-layouts.guest>
    <div class="max-w-4xl mx-auto w-full">
        <!-- Minimal Enterprise Hero -->
        <div class="bg-white border border-slate-200 rounded p-8 sm:p-12 mb-8 shadow-xs">
            <span class="inline-flex items-center px-2.5 py-0.5 rounded text-[11px] font-semibold bg-slate-100 text-slate-800 border border-slate-200 mb-4">
                Institutional Grievance Redressal
            </span>
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900 mb-3 uppercase">
                College Complaint Portal
            </h1>
            <p class="text-sm text-slate-600 leading-relaxed max-w-2xl mb-8">
                An institutional, auditable grievance tracking platform. Students submit formal complaints, Management investigates and proposes resolutions, and College Administration verifies and ensures transparent accountability across all departments.
            </p>

            <div class="flex flex-wrap gap-3">
                <a href="{{ route('login', ['role' => 'student']) }}" class="px-5 py-2.5 rounded bg-blue-600 text-white text-xs font-semibold hover:bg-blue-700 transition shadow-2xs">
                    Student Login
                </a>
                <a href="{{ route('register') }}" class="px-5 py-2.5 rounded bg-white text-slate-800 text-xs font-semibold border border-slate-300 hover:bg-slate-50 transition shadow-2xs">
                    New Student Registration
                </a>
                <a href="{{ route('login', ['role' => 'management']) }}" class="px-5 py-2.5 rounded bg-slate-900 text-white text-xs font-semibold hover:bg-black transition shadow-2xs">
                    Management Portal
                </a>
                <a href="{{ route('login', ['role' => 'admin']) }}" class="px-5 py-2.5 rounded bg-slate-900 text-white text-xs font-semibold hover:bg-black transition shadow-2xs">
                    Admin Portal
                </a>
            </div>
        </div>

        <!-- Role Architecture Overview Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white border border-slate-200 rounded p-6 shadow-xs flex flex-col justify-between">
                <div>
                    <div class="w-8 h-8 rounded bg-blue-600 text-white flex items-center justify-center mb-3">
                        <i data-lucide="graduation-cap" class="w-4 h-4"></i>
                    </div>
                    <h3 class="text-sm font-bold text-slate-900 mb-1.5">Student Portal</h3>
                    <p class="text-xs text-slate-600 leading-relaxed mb-4">
                        Submit campus complaints, upload supporting documents, track resolution stages in real-time, and participate in discussion threads.
                    </p>
                </div>
                <a href="{{ route('login', ['role' => 'student']) }}" class="text-xs font-semibold text-blue-600 hover:text-blue-700 hover:underline flex items-center gap-1 pt-3 border-t border-slate-100">
                    <span>Access Portal</span>
                    <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                </a>
            </div>

            <div class="bg-white border border-slate-200 rounded p-6 shadow-xs flex flex-col justify-between">
                <div>
                    <div class="w-8 h-8 rounded bg-slate-900 text-white flex items-center justify-center mb-3">
                        <i data-lucide="briefcase" class="w-4 h-4"></i>
                    </div>
                    <h3 class="text-sm font-bold text-slate-900 mb-1.5">Management Portal</h3>
                    <p class="text-xs text-slate-600 leading-relaxed mb-4">
                        Investigate assigned department grievances, log internal notes, submit verified resolution descriptions for administrative approval.
                    </p>
                </div>
                <a href="{{ route('login', ['role' => 'management']) }}" class="text-xs font-semibold text-slate-900 hover:text-blue-600 hover:underline flex items-center gap-1 pt-3 border-t border-slate-100">
                    <span>Management Sign In</span>
                    <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                </a>
            </div>

            <div class="bg-white border border-slate-200 rounded p-6 shadow-xs flex flex-col justify-between">
                <div>
                    <div class="w-8 h-8 rounded bg-slate-900 text-white flex items-center justify-center mb-3">
                        <i data-lucide="shield-check" class="w-4 h-4"></i>
                    </div>
                    <h3 class="text-sm font-bold text-slate-900 mb-1.5">Admin Oversight</h3>
                    <p class="text-xs text-slate-600 leading-relaxed mb-4">
                        Assign manual/auto routing, review the verification queue, approve or reject resolutions, view audit logs, and export reports.
                    </p>
                </div>
                <a href="{{ route('login', ['role' => 'admin']) }}" class="text-xs font-semibold text-slate-900 hover:text-blue-600 hover:underline flex items-center gap-1 pt-3 border-t border-slate-100">
                    <span>Admin Sign In</span>
                    <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                </a>
            </div>
        </div>
    </div>
</x-layouts.guest>
