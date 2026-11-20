<x-layouts.guest>
    <div class="max-w-4xl mx-auto w-full">
        <!-- Minimal Enterprise College Hero -->
        <div class="bg-white border border-slate-200 rounded p-8 sm:p-12 mb-8">
            <div class="inline-flex items-center px-2.5 py-1 rounded text-xs font-semibold bg-blue-50 text-[#2563EB] border border-blue-200 mb-4">
                Apex Institute Central Portal
            </div>
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-[#172B4D] mb-3">
                College Complaint Management System
            </h1>
            <p class="text-sm text-slate-600 leading-relaxed max-w-2xl mb-8">
                An institutional, auditable grievance tracking platform. Students submit formal complaints, Management investigates and proposes resolutions, and the College Administration verifies and ensures transparent accountability across all departments.
            </p>

            <div class="flex flex-wrap gap-3">
                <a href="{{ route('login', ['role' => 'student']) }}" class="px-5 py-2.5 rounded bg-[#2563EB] text-white text-xs font-semibold hover:bg-blue-700 transition">
                    Student Login
                </a>
                <a href="{{ route('register') }}" class="px-5 py-2.5 rounded bg-white text-slate-700 text-xs font-semibold border border-slate-300 hover:bg-slate-50 transition">
                    New Student Registration
                </a>
                <a href="{{ route('login', ['role' => 'management']) }}" class="px-5 py-2.5 rounded bg-[#172B4D] text-white text-xs font-semibold hover:bg-slate-900 transition">
                    Management Portal
                </a>
                <a href="{{ route('login', ['role' => 'admin']) }}" class="px-5 py-2.5 rounded bg-slate-100 text-slate-800 text-xs font-semibold border border-slate-300 hover:bg-slate-200 transition">
                    Admin Portal
                </a>
            </div>
        </div>

        <!-- Role Architecture Overview Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white border border-slate-200 rounded p-6">
                <div class="w-8 h-8 rounded bg-blue-50 text-[#2563EB] flex items-center justify-center mb-3">
                    <i data-lucide="graduation-cap" class="w-4 h-4"></i>
                </div>
                <h3 class="text-sm font-semibold text-[#172B4D] mb-1.5">Student Portal</h3>
                <p class="text-xs text-slate-600 leading-relaxed mb-4">
                    Submit campus complaints, upload supporting documents, track resolution stages in real-time, and participate in discussion threads.
                </p>
                <a href="{{ route('login', ['role' => 'student']) }}" class="text-xs font-medium text-[#2563EB] hover:underline flex items-center gap-1">
                    Access Portal &rarr;
                </a>
            </div>

            <div class="bg-white border border-slate-200 rounded p-6">
                <div class="w-8 h-8 rounded bg-slate-100 text-[#172B4D] flex items-center justify-center mb-3">
                    <i data-lucide="briefcase" class="w-4 h-4"></i>
                </div>
                <h3 class="text-sm font-semibold text-[#172B4D] mb-1.5">Management Portal</h3>
                <p class="text-xs text-slate-600 leading-relaxed mb-4">
                    Investigate assigned department grievances, log internal notes, submit verified resolution descriptions for administrative approval.
                </p>
                <a href="{{ route('login', ['role' => 'management']) }}" class="text-xs font-medium text-[#2563EB] hover:underline flex items-center gap-1">
                    Management Sign In &rarr;
                </a>
            </div>

            <div class="bg-white border border-slate-200 rounded p-6">
                <div class="w-8 h-8 rounded bg-slate-100 text-[#172B4D] flex items-center justify-center mb-3">
                    <i data-lucide="shield-check" class="w-4 h-4"></i>
                </div>
                <h3 class="text-sm font-semibold text-[#172B4D] mb-1.5">Admin Oversight</h3>
                <p class="text-xs text-slate-600 leading-relaxed mb-4">
                    Assign manual/auto routing, review the verification queue, approve or reject resolutions, view audit logs, and export reports.
                </p>
                <a href="{{ route('login', ['role' => 'admin']) }}" class="text-xs font-medium text-[#2563EB] hover:underline flex items-center gap-1">
                    Admin Sign In &rarr;
                </a>
            </div>
        </div>
    </div>
</x-layouts.guest>
