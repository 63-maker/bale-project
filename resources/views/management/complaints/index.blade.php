<x-layouts.app :title="'Assigned Complaints'">
    <div class="space-y-6">
        <div>
            <h1 class="text-xl font-bold tracking-tight text-[#172B4D]">Assigned Complaints</h1>
            <p class="text-xs text-slate-500">Manage and investigate grievances assigned to your portfolio</p>
        </div>

        <!-- Filter Bar -->
        <div class="bg-white border border-slate-200 rounded p-4">
            <form method="GET" action="{{ route('management.assigned.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                <div>
                    <label for="search" class="block text-[11px] font-semibold text-slate-600 mb-1">Search</label>
                    <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Subject or Number..."
                           class="w-full px-3 py-1.5 text-xs rounded border border-slate-300 focus:outline-none focus:border-[#2563EB]">
                </div>

                <div>
                    <label for="status" class="block text-[11px] font-semibold text-slate-600 mb-1">Status</label>
                    <select name="status" id="status" class="w-full px-3 py-1.5 text-xs rounded border border-slate-300 focus:outline-none focus:border-[#2563EB] bg-white">
                        <option value="">All Statuses</option>
                        <option value="ASSIGNED" {{ request('status') === 'ASSIGNED' ? 'selected' : '' }}>Assigned (New)</option>
                        <option value="IN_PROGRESS" {{ request('status') === 'IN_PROGRESS' ? 'selected' : '' }}>In Progress</option>
                        <option value="RESOLUTION_SUBMITTED" {{ request('status') === 'RESOLUTION_SUBMITTED' ? 'selected' : '' }}>Resolution Submitted</option>
                        <option value="VERIFICATION_REJECTED" {{ request('status') === 'VERIFICATION_REJECTED' ? 'selected' : '' }}>Verification Rejected</option>
                        <option value="VERIFIED_FOR_CLOSURE" {{ request('status') === 'VERIFIED_FOR_CLOSURE' ? 'selected' : '' }}>Verified for Closure</option>
                    </select>
                </div>

                <div>
                    <label for="priority" class="block text-[11px] font-semibold text-slate-600 mb-1">Priority</label>
                    <select name="priority" id="priority" class="w-full px-3 py-1.5 text-xs rounded border border-slate-300 focus:outline-none focus:border-[#2563EB] bg-white">
                        <option value="">All Priorities</option>
                        <option value="LOW" {{ request('priority') === 'LOW' ? 'selected' : '' }}>Low</option>
                        <option value="MEDIUM" {{ request('priority') === 'MEDIUM' ? 'selected' : '' }}>Medium</option>
                        <option value="HIGH" {{ request('priority') === 'HIGH' ? 'selected' : '' }}>High</option>
                        <option value="CRITICAL" {{ request('priority') === 'CRITICAL' ? 'selected' : '' }}>Critical</option>
                    </select>
                </div>

                <div class="flex items-end gap-2">
                    <button type="submit" class="px-4 py-1.5 rounded bg-[#172B4D] text-white text-xs font-semibold hover:bg-slate-900 transition">
                        Filter
                    </button>
                    @if(request()->hasAny(['search', 'status', 'priority']))
                        <a href="{{ route('management.assigned.index') }}" class="px-3 py-1.5 rounded border border-slate-300 text-slate-600 text-xs font-semibold hover:bg-slate-50 transition">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Table -->
        <div class="bg-white border border-slate-200 rounded overflow-hidden">
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
                            <th class="px-6 py-3">Assigned Date</th>
                            <th class="px-6 py-3 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse($complaints as $c)
                            <tr class="hover:bg-slate-50/60 transition">
                                <td class="px-6 py-3.5 font-medium text-slate-900 whitespace-nowrap">
                                    {{ $c->complaint_number }}
                                </td>
                                <td class="px-6 py-3.5 text-slate-700 whitespace-nowrap">
                                    {{ $c->student?->full_name ?? 'N/A' }}
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
                                    <a href="{{ route('management.complaints.show', $c->id) }}" class="px-3 py-1 rounded bg-[#2563EB] text-white text-xs font-semibold hover:bg-blue-700 transition">
                                        Investigate
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center text-slate-500">
                                    <p class="font-medium text-slate-700">No complaints matching your criteria.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($complaints->hasPages())
                <div class="px-6 py-4 border-t border-slate-200 bg-slate-50">
                    {{ $complaints->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
