<x-layouts.app :title="'Student Dashboard'">
    <div class="space-y-6">
        <!-- Top Header & Action -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white border border-slate-200 rounded p-6">
            <div>
                <h1 class="text-xl font-bold tracking-tight text-[#172B4D]">Welcome, {{ $student->full_name }}</h1>
                <p class="text-xs text-slate-500 mt-1">
                    Register No: <span class="font-semibold text-slate-700">{{ $student->register_number }}</span> &bull; 
                    Department: <span class="font-semibold text-slate-700">{{ $student->department?->name ?? 'N/A' }}</span> &bull;
                    Year: <span class="font-semibold text-slate-700">{{ $student->study_year }} Year</span>
                </p>
            </div>
            <div>
                <a href="{{ route('student.complaints.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded bg-[#2563EB] text-white text-xs font-semibold hover:bg-blue-700 transition shadow-xs">
                    <i data-lucide="plus-circle" class="w-4 h-4"></i>
                    <span>Submit New Complaint</span>
                </a>
            </div>
        </div>

        <!-- 4 Stat Metric Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white border border-slate-200 rounded p-5">
                <div class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Total Complaints</div>
                <div class="text-2xl font-bold text-[#172B4D] mt-1">{{ $totalComplaints }}</div>
                <div class="text-[11px] text-slate-400 mt-1">All historical filings</div>
            </div>

            <div class="bg-white border border-slate-200 rounded p-5">
                <div class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Pending Assignment</div>
                <div class="text-2xl font-bold text-amber-600 mt-1">{{ $pendingComplaints }}</div>
                <div class="text-[11px] text-slate-400 mt-1">Submitted or awaiting review</div>
            </div>

            <div class="bg-white border border-slate-200 rounded p-5">
                <div class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Under Investigation</div>
                <div class="text-2xl font-bold text-blue-600 mt-1">{{ $inProgressComplaints }}</div>
                <div class="text-[11px] text-slate-400 mt-1">Active investigation by Management</div>
            </div>

            <div class="bg-white border border-slate-200 rounded p-5">
                <div class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Resolved & Closed</div>
                <div class="text-2xl font-bold text-emerald-600 mt-1">{{ $resolvedComplaints }}</div>
                <div class="text-[11px] text-slate-400 mt-1">Officially verified & closed</div>
            </div>
        </div>

        <!-- Recent Complaints Section -->
        <div class="bg-white border border-slate-200 rounded">
            <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-semibold text-[#172B4D]">Recent Complaints</h2>
                    <p class="text-xs text-slate-500">Track your most recently submitted grievance filings</p>
                </div>
                <a href="{{ route('student.complaints.index') }}" class="text-xs font-medium text-[#2563EB] hover:underline">
                    View All ({{ $totalComplaints }}) &rarr;
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-600 font-semibold uppercase text-[10px] tracking-wider">
                        <tr>
                            <th class="px-6 py-3">Complaint #</th>
                            <th class="px-6 py-3">Subject</th>
                            <th class="px-6 py-3">Category</th>
                            <th class="px-6 py-3">Department</th>
                            <th class="px-6 py-3">Priority</th>
                            <th class="px-6 py-3">Status</th>
                            <th class="px-6 py-3">Date</th>
                            <th class="px-6 py-3 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse($recentComplaints as $c)
                            <tr class="hover:bg-slate-50/60 transition">
                                <td class="px-6 py-3.5 font-medium text-slate-900 whitespace-nowrap">
                                    {{ $c->complaint_number }}
                                </td>
                                <td class="px-6 py-3.5 max-w-xs truncate text-slate-800 font-medium">
                                    {{ $c->subject }}
                                </td>
                                <td class="px-6 py-3.5 text-slate-600 whitespace-nowrap">
                                    {{ $c->category?->name ?? 'General' }}
                                </td>
                                <td class="px-6 py-3.5 text-slate-600 whitespace-nowrap">
                                    {{ $c->department?->code ?? 'N/A' }}
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
                                    <a href="{{ route('student.complaints.show', $c->id) }}" class="text-[#2563EB] hover:underline font-medium">
                                        View Details
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center text-slate-500">
                                    <i data-lucide="inbox" class="w-8 h-8 text-slate-400 mx-auto mb-2"></i>
                                    <p class="font-medium text-slate-700">No complaints submitted yet.</p>
                                    <p class="text-[11px] text-slate-400 mt-1">If you have any campus grievance, submit a formal request.</p>
                                    <a href="{{ route('student.complaints.create') }}" class="inline-block mt-3 px-3.5 py-1.5 rounded bg-[#2563EB] text-white text-xs font-semibold hover:bg-blue-700">
                                        Submit Complaint
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts.app>
