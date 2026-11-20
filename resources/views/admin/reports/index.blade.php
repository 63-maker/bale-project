<x-layouts.app :title="'Reports & Analytics'">
    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-xl font-bold tracking-tight text-[#172B4D]">Reports & Analytics</h1>
                <p class="text-xs text-slate-500">Comprehensive grievance metrics, resolution velocity, and institutional audit data</p>
            </div>
            <!-- Export Buttons -->
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.reports.export.csv', request()->query()) }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded border border-slate-300 bg-white text-slate-700 text-xs font-semibold hover:bg-slate-50 transition">
                    <i data-lucide="download" class="w-4 h-4 text-slate-500"></i>
                    <span>Export CSV</span>
                </a>
                <a href="{{ route('admin.reports.export.pdf', request()->query()) }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded bg-[#172B4D] text-white text-xs font-semibold hover:bg-slate-900 transition">
                    <i data-lucide="file-text" class="w-4 h-4"></i>
                    <span>Export PDF Report</span>
                </a>
            </div>
        </div>

        <!-- Filter Card -->
        <div class="bg-white border border-slate-200 rounded p-4">
            <form method="GET" action="{{ route('admin.reports.index') }}" class="grid grid-cols-1 sm:grid-cols-4 lg:grid-cols-7 gap-3">
                <div>
                    <label for="start_date" class="block text-[11px] font-semibold text-slate-600 mb-1">From Date</label>
                    <input type="date" name="start_date" id="start_date" value="{{ request('start_date') }}"
                           class="w-full px-2 py-1.5 text-xs rounded border border-slate-300 focus:outline-none focus:border-[#2563EB]">
                </div>

                <div>
                    <label for="end_date" class="block text-[11px] font-semibold text-slate-600 mb-1">To Date</label>
                    <input type="date" name="end_date" id="end_date" value="{{ request('end_date') }}"
                           class="w-full px-2 py-1.5 text-xs rounded border border-slate-300 focus:outline-none focus:border-[#2563EB]">
                </div>

                <div>
                    <label for="dept" class="block text-[11px] font-semibold text-slate-600 mb-1">Department</label>
                    <select name="department_id" id="dept" class="w-full px-2 py-1.5 text-xs rounded border border-slate-300 focus:outline-none focus:border-[#2563EB] bg-white">
                        <option value="">All</option>
                        @foreach($departments as $d)
                            <option value="{{ $d->id }}" {{ request('department_id') == $d->id ? 'selected' : '' }}>{{ $d->code }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="cat" class="block text-[11px] font-semibold text-slate-600 mb-1">Category</label>
                    <select name="category_id" id="cat" class="w-full px-2 py-1.5 text-xs rounded border border-slate-300 focus:outline-none focus:border-[#2563EB] bg-white">
                        <option value="">All</option>
                        @foreach($categories as $c)
                            <option value="{{ $c->id }}" {{ request('category_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="pri" class="block text-[11px] font-semibold text-slate-600 mb-1">Priority</label>
                    <select name="priority" id="pri" class="w-full px-2 py-1.5 text-xs rounded border border-slate-300 focus:outline-none focus:border-[#2563EB] bg-white">
                        <option value="">All</option>
                        <option value="LOW" {{ request('priority') == 'LOW' ? 'selected' : '' }}>Low</option>
                        <option value="MEDIUM" {{ request('priority') == 'MEDIUM' ? 'selected' : '' }}>Medium</option>
                        <option value="HIGH" {{ request('priority') == 'HIGH' ? 'selected' : '' }}>High</option>
                        <option value="CRITICAL" {{ request('priority') == 'CRITICAL' ? 'selected' : '' }}>Critical</option>
                    </select>
                </div>

                <div>
                    <label for="st" class="block text-[11px] font-semibold text-slate-600 mb-1">Status</label>
                    <select name="status" id="st" class="w-full px-2 py-1.5 text-xs rounded border border-slate-300 focus:outline-none focus:border-[#2563EB] bg-white">
                        <option value="">All</option>
                        <option value="SUBMITTED" {{ request('status') == 'SUBMITTED' ? 'selected' : '' }}>Submitted</option>
                        <option value="ASSIGNED" {{ request('status') == 'ASSIGNED' ? 'selected' : '' }}>Assigned</option>
                        <option value="IN_PROGRESS" {{ request('status') == 'IN_PROGRESS' ? 'selected' : '' }}>In Progress</option>
                        <option value="RESOLUTION_SUBMITTED" {{ request('status') == 'RESOLUTION_SUBMITTED' ? 'selected' : '' }}>Res. Submitted</option>
                        <option value="CLOSED" {{ request('status') == 'CLOSED' ? 'selected' : '' }}>Closed</option>
                        <option value="REJECTED" {{ request('status') == 'REJECTED' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>

                <div class="flex items-end gap-1">
                    <button type="submit" class="px-3 py-1.5 rounded bg-[#172B4D] text-white text-xs font-semibold hover:bg-slate-900 transition flex-1">
                        Apply
                    </button>
                    @if(!empty(array_filter(request()->all())))
                        <a href="{{ route('admin.reports.index') }}" class="px-2 py-1.5 rounded border border-slate-300 text-slate-600 text-xs font-semibold hover:bg-slate-50">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- 6 Metrics Cards Grid -->
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
            <div class="bg-white border border-slate-200 rounded p-4 text-center">
                <div class="text-[10px] font-bold text-slate-500 uppercase">Filtered Total</div>
                <div class="text-xl font-bold text-[#172B4D] mt-1">{{ $analytics['total'] }}</div>
            </div>

            <div class="bg-white border border-slate-200 rounded p-4 text-center">
                <div class="text-[10px] font-bold text-slate-500 uppercase">Pending Review</div>
                <div class="text-xl font-bold text-blue-600 mt-1">{{ $analytics['pending'] }}</div>
            </div>

            <div class="bg-white border border-slate-200 rounded p-4 text-center">
                <div class="text-[10px] font-bold text-slate-500 uppercase">Resolved & Closed</div>
                <div class="text-xl font-bold text-emerald-600 mt-1">{{ $analytics['resolved'] }}</div>
            </div>

            <div class="bg-white border border-slate-200 rounded p-4 text-center">
                <div class="text-[10px] font-bold text-slate-500 uppercase">Overdue Cases</div>
                <div class="text-xl font-bold text-rose-600 mt-1">{{ $analytics['overdue'] }}</div>
            </div>

            <div class="bg-white border border-slate-200 rounded p-4 text-center">
                <div class="text-[10px] font-bold text-slate-500 uppercase">Avg Resolution</div>
                <div class="text-xl font-bold text-slate-800 mt-1">{{ $analytics['avg_resolution_hours'] }}h</div>
            </div>

            <div class="bg-white border border-slate-200 rounded p-4 text-center">
                <div class="text-[10px] font-bold text-slate-500 uppercase">Verification Reject Rate</div>
                <div class="text-xl font-bold text-amber-600 mt-1">{{ $analytics['verification_rejection_rate'] }}%</div>
            </div>
        </div>

        <!-- Filtered Records Table -->
        <div class="bg-white border border-slate-200 rounded overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
                <h3 class="text-sm font-bold text-[#172B4D]">Complaints Audit Data Stream ({{ $complaints->total() }} Records)</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-600 uppercase text-[10px] font-semibold">
                        <tr>
                            <th class="px-6 py-3">Complaint #</th>
                            <th class="px-6 py-3">Student</th>
                            <th class="px-6 py-3">Department</th>
                            <th class="px-6 py-3">Category</th>
                            <th class="px-6 py-3">Priority</th>
                            <th class="px-6 py-3">Status</th>
                            <th class="px-6 py-3">Assigned Management</th>
                            <th class="px-6 py-3">Filed Date</th>
                            <th class="px-6 py-3 text-right">View</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse($complaints as $c)
                            <tr class="hover:bg-slate-50">
                                <td class="px-6 py-3 font-semibold text-slate-900 whitespace-nowrap">{{ $c->complaint_number }}</td>
                                <td class="px-6 py-3 text-slate-700 whitespace-nowrap">{{ $c->student?->full_name }}</td>
                                <td class="px-6 py-3 text-slate-600 whitespace-nowrap">{{ $c->department?->code }}</td>
                                <td class="px-6 py-3 text-slate-600 whitespace-nowrap">{{ $c->category?->name }}</td>
                                <td class="px-6 py-3 whitespace-nowrap"><x-priority-badge :priority="$c->priority" /></td>
                                <td class="px-6 py-3 whitespace-nowrap"><x-status-badge :status="$c->status" /></td>
                                <td class="px-6 py-3 text-slate-700 whitespace-nowrap">{{ $c->activeAssignment?->management?->full_name ?? 'Unassigned' }}</td>
                                <td class="px-6 py-3 text-slate-500 whitespace-nowrap">{{ $c->created_at->format('d M Y') }}</td>
                                <td class="px-6 py-3 text-right whitespace-nowrap">
                                    <a href="{{ route('admin.complaints.show', $c->id) }}" class="text-[#2563EB] hover:underline font-semibold">&rarr;</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-6 py-8 text-center text-slate-500">No records match the applied report criteria.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($complaints->hasPages())
                <div class="px-6 py-3 border-t border-slate-200 bg-slate-50">
                    {{ $complaints->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
