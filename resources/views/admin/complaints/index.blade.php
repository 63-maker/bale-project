<x-layouts.app :title="'All Complaints'">
    <div class="space-y-6">
        <div>
            <h1 class="text-xl font-bold tracking-tight text-[#172B4D]">All Complaints Registry</h1>
            <p class="text-xs text-slate-500">Comprehensive administrative control and oversight over all campus complaints</p>
        </div>

        <!-- Filter Bar -->
        <div class="bg-white border border-slate-200 rounded p-4">
            <form method="GET" action="{{ route('admin.complaints.index') }}" class="grid grid-cols-1 sm:grid-cols-5 gap-3">
                <div class="sm:col-span-2">
                    <label for="search" class="block text-[11px] font-semibold text-slate-600 mb-1">Search</label>
                    <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Subject, Number, Student Name, Register No..."
                           class="w-full px-3 py-1.5 text-xs rounded border border-slate-300 focus:outline-none focus:border-[#2563EB]">
                </div>

                <div>
                    <label for="status" class="block text-[11px] font-semibold text-slate-600 mb-1">Status</label>
                    <select name="status" id="status" class="w-full px-3 py-1.5 text-xs rounded border border-slate-300 focus:outline-none focus:border-[#2563EB] bg-white">
                        <option value="">All Statuses</option>
                        <option value="SUBMITTED" {{ request('status') === 'SUBMITTED' ? 'selected' : '' }}>Submitted</option>
                        <option value="ASSIGNED" {{ request('status') === 'ASSIGNED' ? 'selected' : '' }}>Assigned</option>
                        <option value="IN_PROGRESS" {{ request('status') === 'IN_PROGRESS' ? 'selected' : '' }}>In Progress</option>
                        <option value="RESOLUTION_SUBMITTED" {{ request('status') === 'RESOLUTION_SUBMITTED' ? 'selected' : '' }}>Resolution Submitted</option>
                        <option value="VERIFICATION_REJECTED" {{ request('status') === 'VERIFICATION_REJECTED' ? 'selected' : '' }}>Verification Rejected</option>
                        <option value="VERIFIED_FOR_CLOSURE" {{ request('status') === 'VERIFIED_FOR_CLOSURE' ? 'selected' : '' }}>Verified for Closure</option>
                        <option value="CLOSED" {{ request('status') === 'CLOSED' ? 'selected' : '' }}>Closed</option>
                        <option value="REOPENED" {{ request('status') === 'REOPENED' ? 'selected' : '' }}>Reopened</option>
                        <option value="REJECTED" {{ request('status') === 'REJECTED' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>

                <div>
                    <label for="department_id" class="block text-[11px] font-semibold text-slate-600 mb-1">Department</label>
                    <select name="department_id" id="department_id" class="w-full px-3 py-1.5 text-xs rounded border border-slate-300 focus:outline-none focus:border-[#2563EB] bg-white">
                        <option value="">All Departments</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-end gap-2">
                    <button type="submit" class="px-4 py-1.5 rounded bg-[#172B4D] text-white text-xs font-semibold hover:bg-slate-900 transition">
                        Filter
                    </button>
                    @if(request()->hasAny(['search', 'status', 'department_id', 'priority', 'category_id']))
                        <a href="{{ route('admin.complaints.index') }}" class="px-3 py-1.5 rounded border border-slate-300 text-slate-600 text-xs font-semibold hover:bg-slate-50 transition">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Complaints Table -->
        <div class="bg-white border border-slate-200 rounded overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-600 font-semibold uppercase text-[10px] tracking-wider">
                        <tr>
                            <th class="px-6 py-3">Complaint #</th>
                            <th class="px-6 py-3">Student</th>
                            <th class="px-6 py-3">Subject</th>
                            <th class="px-6 py-3">Department</th>
                            <th class="px-6 py-3">Priority</th>
                            <th class="px-6 py-3">Status</th>
                            <th class="px-6 py-3">Assigned Management</th>
                            <th class="px-6 py-3">Date</th>
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
                                    {{ $c->department?->code ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-3.5 whitespace-nowrap">
                                    <x-priority-badge :priority="$c->priority" />
                                </td>
                                <td class="px-6 py-3.5 whitespace-nowrap">
                                    <x-status-badge :status="$c->status" />
                                </td>
                                <td class="px-6 py-3.5 text-slate-700 font-medium whitespace-nowrap">
                                    {{ $c->activeAssignment?->management?->full_name ?? 'Unassigned' }}
                                </td>
                                <td class="px-6 py-3.5 text-slate-500 whitespace-nowrap">
                                    {{ $c->created_at->format('d M Y') }}
                                </td>
                                <td class="px-6 py-3.5 text-right whitespace-nowrap">
                                    <a href="{{ route('admin.complaints.show', $c->id) }}" class="text-[#2563EB] hover:underline font-semibold">
                                        Manage &rarr;
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-6 py-12 text-center text-slate-500">
                                    <p class="font-medium text-slate-700">No complaints matching filter criteria.</p>
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
