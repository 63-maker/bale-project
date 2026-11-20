<x-layouts.app :title="'Management Dashboard'">
    <div class="space-y-6">
        <!-- 1. DETAILS ACCORDING TO USER INPUT -->
        <div class="bg-white border border-slate-200 rounded p-6 shadow-xs">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 mb-4 border-b border-slate-200">
                <div>
                    <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded text-[11px] font-semibold bg-slate-100 text-[#172B4D] border border-slate-300 mb-1">
                        <i data-lucide="shield" class="w-3.5 h-3.5"></i>
                        <span>Authenticated Management Profile</span>
                    </div>
                    <h1 class="text-xl font-bold tracking-tight text-[#172B4D]">
                        Officer {{ $management->full_name }}
                    </h1>
                    <p class="text-xs text-slate-500">
                        Details verified and loaded according to your sign-in input.
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        Account Active
                    </span>
                    <a href="{{ route('profile') }}" class="px-3 py-1.5 rounded border border-slate-300 bg-white text-slate-700 text-xs font-medium hover:bg-slate-50 transition flex items-center gap-1.5">
                        <i data-lucide="user" class="w-3.5 h-3.5"></i>
                        <span>Profile & Password</span>
                    </a>
                </div>
            </div>

            <!-- Profile Details Grid -->
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                <div class="bg-slate-50 p-3 rounded border border-slate-200">
                    <span class="block text-[10px] font-semibold text-slate-500 uppercase tracking-wider">Employee ID</span>
                    <span class="text-xs font-bold font-mono text-[#172B4D] mt-0.5 block truncate">{{ $management->employee_id }}</span>
                </div>

                <div class="bg-slate-50 p-3 rounded border border-slate-200">
                    <span class="block text-[10px] font-semibold text-slate-500 uppercase tracking-wider">Full Name</span>
                    <span class="text-xs font-bold text-slate-800 mt-0.5 block truncate">{{ $management->full_name }}</span>
                </div>

                <div class="bg-slate-50 p-3 rounded border border-slate-200">
                    <span class="block text-[10px] font-semibold text-slate-500 uppercase tracking-wider">Official Email</span>
                    <span class="text-xs font-medium text-slate-700 mt-0.5 block truncate" title="{{ $management->email }}">{{ $management->email }}</span>
                </div>

                <div class="bg-slate-50 p-3 rounded border border-slate-200">
                    <span class="block text-[10px] font-semibold text-slate-500 uppercase tracking-wider">Department</span>
                    <span class="text-xs font-bold text-slate-800 mt-0.5 block truncate">{{ $management->department?->code ?? 'N/A' }} - {{ $management->department?->name ?? 'Unassigned' }}</span>
                </div>

                <div class="bg-slate-50 p-3 rounded border border-slate-200">
                    <span class="block text-[10px] font-semibold text-slate-500 uppercase tracking-wider">Active Caseload</span>
                    <span class="text-xs font-bold text-[#2563EB] mt-0.5 block truncate">{{ $totalAssigned }} Assigned Cases</span>
                </div>

                <div class="bg-slate-50 p-3 rounded border border-slate-200">
                    <span class="block text-[10px] font-semibold text-slate-500 uppercase tracking-wider">Portal Role</span>
                    <span class="text-xs font-bold text-[#172B4D] mt-0.5 block capitalize">{{ $management->role }}</span>
                </div>
            </div>
        </div>

        <!-- 2. AUTHORIZED MANAGEMENT FUNCTIONS -->
        <div class="bg-white border border-slate-200 rounded p-6 shadow-xs">
            <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-200">
                <div>
                    <h2 class="text-sm font-bold text-[#172B4D] uppercase tracking-wider">Authorized Management Functions</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Investigation workflows and resolution operations for your caseload</p>
                </div>
                <span class="text-xs text-slate-400 font-medium">6 Functions Available</span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <!-- Function 1: Assigned Complaints Queue -->
                <a href="{{ route('management.assigned.index') }}" class="group p-4 rounded border border-slate-200 bg-slate-50/50 hover:bg-white hover:border-[#172B4D] transition flex flex-col justify-between">
                    <div>
                        <div class="w-8 h-8 rounded bg-[#172B4D] text-white flex items-center justify-center mb-3">
                            <i data-lucide="inbox" class="w-4 h-4"></i>
                        </div>
                        <h3 class="text-xs font-bold text-[#172B4D] group-hover:text-[#2563EB] transition">Assigned Complaints Queue</h3>
                        <p class="text-[11px] text-slate-600 mt-1 leading-relaxed">
                            Access all student grievances routed to your department workload and track investigation stages.
                        </p>
                    </div>
                    <div class="mt-3 pt-3 border-t border-slate-200 flex items-center text-xs font-semibold text-[#172B4D] group-hover:text-[#2563EB]">
                        <span>Assigned Queue ({{ $totalAssigned }})</span>
                        <i data-lucide="arrow-right" class="w-3.5 h-3.5 ml-1 group-hover:translate-x-0.5 transition-transform"></i>
                    </div>
                </a>

                <!-- Function 2: Start Investigation -->
                <a href="{{ route('management.assigned.index') }}" class="group p-4 rounded border border-blue-200 bg-blue-50/40 hover:bg-blue-50 hover:border-[#2563EB] transition flex flex-col justify-between">
                    <div>
                        <div class="w-8 h-8 rounded bg-[#2563EB] text-white flex items-center justify-center mb-3">
                            <i data-lucide="play-circle" class="w-4 h-4"></i>
                        </div>
                        <h3 class="text-xs font-bold text-[#172B4D] group-hover:text-[#2563EB] transition">Start Investigation</h3>
                        <p class="text-[11px] text-slate-600 mt-1 leading-relaxed">
                            Formally transition newly assigned grievances to IN PROGRESS and initiate fact-finding procedures.
                        </p>
                    </div>
                    <div class="mt-3 pt-3 border-t border-blue-100 flex items-center text-xs font-semibold text-[#2563EB]">
                        <span>New Cases ({{ $newAssigned }})</span>
                        <i data-lucide="arrow-right" class="w-3.5 h-3.5 ml-1 group-hover:translate-x-0.5 transition-transform"></i>
                    </div>
                </a>

                <!-- Function 3: Student Communication & Notes -->
                <a href="{{ route('management.assigned.index') }}" class="group p-4 rounded border border-slate-200 bg-slate-50/50 hover:bg-white hover:border-[#172B4D] transition flex flex-col justify-between">
                    <div>
                        <div class="w-8 h-8 rounded bg-slate-700 text-white flex items-center justify-center mb-3">
                            <i data-lucide="messages-square" class="w-4 h-4"></i>
                        </div>
                        <h3 class="text-xs font-bold text-[#172B4D] group-hover:text-[#2563EB] transition">Case Discussions & Notes</h3>
                        <p class="text-[11px] text-slate-600 mt-1 leading-relaxed">
                            Communicate inquiry questions to students or record confidential internal investigation notes.
                        </p>
                    </div>
                    <div class="mt-3 pt-3 border-t border-slate-200 flex items-center text-xs font-semibold text-slate-700 group-hover:text-[#2563EB]">
                        <span>Case Messaging</span>
                        <i data-lucide="arrow-right" class="w-3.5 h-3.5 ml-1 group-hover:translate-x-0.5 transition-transform"></i>
                    </div>
                </a>

                <!-- Function 4: Submit Resolution for Verification -->
                <a href="{{ route('management.assigned.index') }}" class="group p-4 rounded border border-slate-200 bg-slate-50/50 hover:bg-white hover:border-[#172B4D] transition flex flex-col justify-between">
                    <div>
                        <div class="w-8 h-8 rounded bg-emerald-600 text-white flex items-center justify-center mb-3">
                            <i data-lucide="file-check-2" class="w-4 h-4"></i>
                        </div>
                        <h3 class="text-xs font-bold text-[#172B4D] group-hover:text-[#2563EB] transition">Submit Resolution</h3>
                        <p class="text-[11px] text-slate-600 mt-1 leading-relaxed">
                            Document corrective actions and submit verified resolution reports for administrative verification.
                        </p>
                    </div>
                    <div class="mt-3 pt-3 border-t border-slate-200 flex items-center text-xs font-semibold text-slate-700 group-hover:text-[#2563EB]">
                        <span>In-Progress ({{ $inProgress }})</span>
                        <i data-lucide="arrow-right" class="w-3.5 h-3.5 ml-1 group-hover:translate-x-0.5 transition-transform"></i>
                    </div>
                </a>

                <!-- Function 5: Verification Status & Revisions -->
                <a href="{{ route('management.verification-status') }}" class="group p-4 rounded border {{ $verificationRejected > 0 ? 'border-amber-300 bg-amber-50/50' : 'border-slate-200 bg-slate-50/50' }} hover:bg-white hover:border-amber-600 transition flex flex-col justify-between">
                    <div>
                        <div class="w-8 h-8 rounded bg-amber-600 text-white flex items-center justify-center mb-3">
                            <i data-lucide="shield-alert" class="w-4 h-4"></i>
                        </div>
                        <h3 class="text-xs font-bold text-[#172B4D] group-hover:text-amber-700 transition">Verification Status</h3>
                        <p class="text-[11px] text-slate-600 mt-1 leading-relaxed">
                            Track Administrative review decisions and correct resolutions rejected with remarks.
                        </p>
                    </div>
                    <div class="mt-3 pt-3 border-t border-slate-200 flex items-center text-xs font-semibold {{ $verificationRejected > 0 ? 'text-amber-700' : 'text-slate-700' }}">
                        <span>Rejections ({{ $verificationRejected }})</span>
                        <i data-lucide="arrow-right" class="w-3.5 h-3.5 ml-1 group-hover:translate-x-0.5 transition-transform"></i>
                    </div>
                </a>

                <!-- Function 6: Complete Final Closure -->
                <a href="{{ route('management.closed') }}" class="group p-4 rounded border border-slate-200 bg-slate-50/50 hover:bg-white hover:border-[#172B4D] transition flex flex-col justify-between">
                    <div>
                        <div class="w-8 h-8 rounded bg-teal-600 text-white flex items-center justify-center mb-3">
                            <i data-lucide="check-check" class="w-4 h-4"></i>
                        </div>
                        <h3 class="text-xs font-bold text-[#172B4D] group-hover:text-[#2563EB] transition">Complete Final Closure</h3>
                        <p class="text-[11px] text-slate-600 mt-1 leading-relaxed">
                            Execute formal case closure for complaints verified and approved by Administration.
                        </p>
                    </div>
                    <div class="mt-3 pt-3 border-t border-slate-200 flex items-center text-xs font-semibold text-slate-700 group-hover:text-[#2563EB]">
                        <span>Ready to Close ({{ $verifiedForClosure }})</span>
                        <i data-lucide="arrow-right" class="w-3.5 h-3.5 ml-1 group-hover:translate-x-0.5 transition-transform"></i>
                    </div>
                </a>
            </div>
        </div>

        <!-- 3. METRIC STAT CARDS -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white border border-slate-200 rounded p-5">
                <div class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Total Active Assigned</div>
                <div class="text-2xl font-bold text-[#172B4D] mt-1">{{ $totalAssigned }}</div>
                <div class="text-[11px] text-slate-400 mt-1">Under your operational queue</div>
            </div>

            <div class="bg-white border border-slate-200 rounded p-5">
                <div class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">New Assignments</div>
                <div class="text-2xl font-bold text-blue-600 mt-1">{{ $newAssigned }}</div>
                <div class="text-[11px] text-slate-400 mt-1">Awaiting investigation start</div>
            </div>

            <div class="bg-white border border-slate-200 rounded p-5">
                <div class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Active In-Progress</div>
                <div class="text-2xl font-bold text-indigo-600 mt-1">{{ $inProgress }}</div>
                <div class="text-[11px] text-slate-400 mt-1">Investigation underway</div>
            </div>

            <div class="bg-white border border-slate-200 rounded p-5">
                <div class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Verified For Closure</div>
                <div class="text-2xl font-bold text-emerald-600 mt-1">{{ $verifiedForClosure }}</div>
                <div class="text-[11px] text-slate-400 mt-1">Admin approved; ready to close</div>
            </div>
        </div>

        <!-- Alert if items rejected by admin -->
        @if($verificationRejected > 0)
            <div class="p-4 bg-orange-50 border border-orange-200 rounded flex items-center justify-between text-xs text-orange-900">
                <div class="flex items-center gap-2">
                    <i data-lucide="alert-triangle" class="w-5 h-5 text-orange-600 shrink-0"></i>
                    <span>You have <strong>{{ $verificationRejected }}</strong> resolution(s) rejected by Administration requiring revision.</span>
                </div>
                <a href="{{ route('management.verification-status') }}" class="font-semibold text-orange-700 hover:underline">
                    View Rejections &rarr;
                </a>
            </div>
        @endif

        <!-- 4. ACTIVE ASSIGNED COMPLAINTS TABLE -->
        <div class="bg-white border border-slate-200 rounded">
            <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-semibold text-[#172B4D]">Active Assigned Complaints</h2>
                    <p class="text-xs text-slate-500">Complaints currently routed to your department workload</p>
                </div>
                <a href="{{ route('management.assigned.index') }}" class="text-xs font-medium text-[#2563EB] hover:underline">
                    View All Queue &rarr;
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-600 font-semibold uppercase text-[10px] tracking-wider">
                        <tr>
                            <th class="px-6 py-3">Complaint #</th>
                            <th class="px-6 py-3">Student</th>
                            <th class="px-6 py-3">Subject</th>
                            <th class="px-6 py-3">Category</th>
                            <th class="px-6 py-3">Priority</th>
                            <th class="px-6 py-3">Status</th>
                            <th class="px-6 py-3">Submitted</th>
                            <th class="px-6 py-3 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse($recentComplaints as $c)
                            <tr class="hover:bg-slate-50/60 transition">
                                <td class="px-6 py-3.5 font-medium text-slate-900 whitespace-nowrap">
                                    {{ $c->complaint_number }}
                                </td>
                                <td class="px-6 py-3.5 text-slate-700 whitespace-nowrap">
                                    {{ $c->student?->full_name ?? 'Student' }}
                                    <div class="text-[10px] text-slate-400">{{ $c->student?->register_number }}</div>
                                </td>
                                <td class="px-6 py-3.5 max-w-xs truncate text-slate-800 font-medium">
                                    {{ $c->subject }}
                                </td>
                                <td class="px-6 py-3.5 text-slate-600 whitespace-nowrap">
                                    {{ $c->category?->name ?? 'General' }}
                                </td>
                                <td class="px-6 py-3.5 whitespace-nowrap">
                                    <x-priority-badge :priority="$c->priority" />
                                </td>
                                <td class="px-6 py-3.5 whitespace-nowrap">
                                    <x-status-badge :status="$c->status" />
                                </td>
                                <td class="px-6 py-3.5 text-slate-500 whitespace-nowrap">
                                    {{ $c->created_at->format('d M Y') }}
                                </td>
                                <td class="px-6 py-3.5 text-right whitespace-nowrap">
                                    <a href="{{ route('management.complaints.show', $c->id) }}" class="text-[#2563EB] hover:underline font-medium">
                                        Investigate &rarr;
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-8 text-center text-slate-400">
                                    No active complaints assigned to your department queue.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts.app>
