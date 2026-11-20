<x-layouts.app :title="'Management Dashboard'">
    <div class="space-y-6">
        <!-- Top Info Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white border border-slate-200 rounded p-6">
            <div>
                <h1 class="text-xl font-bold tracking-tight text-[#172B4D]">Management Investigation Portal</h1>
                <p class="text-xs text-slate-500 mt-1">
                    Officer: <span class="font-semibold text-slate-700">{{ $management->full_name }}</span> &bull; 
                    Employee ID: <span class="font-semibold text-slate-700">{{ $management->employee_id }}</span> &bull; 
                    Department: <span class="font-semibold text-slate-700">{{ $management->department?->name ?? 'N/A' }}</span>
                </p>
            </div>
            <div>
                <a href="{{ route('management.assigned.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded bg-[#172B4D] text-white text-xs font-semibold hover:bg-slate-900 transition shadow-xs">
                    <i data-lucide="inbox" class="w-4 h-4"></i>
                    <span>View Assigned Complaints</span>
                </a>
            </div>
        </div>

        <!-- Metric Stat Cards -->
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

        <!-- Assigned Complaints Table -->
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
                                    {{ $c->category?->name ?? 'N/A' }}
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
                                    <a href="{{ route('management.complaints.show', $c->id) }}" class="px-2.5 py-1 rounded bg-[#2563EB] text-white text-[11px] font-semibold hover:bg-blue-700 transition">
                                        Investigate &rarr;
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center text-slate-500">
                                    <i data-lucide="check-circle" class="w-8 h-8 text-emerald-400 mx-auto mb-2"></i>
                                    <p class="font-medium text-slate-700">No active complaints pending in your queue.</p>
                                    <p class="text-[11px] text-slate-400 mt-1">Your workload is currently clear.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts.app>
