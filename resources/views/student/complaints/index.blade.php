<x-layouts.app :title="'My Complaints'">
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-xl font-bold tracking-tight text-slate-900">My Complaints</h1>
                <p class="text-xs text-slate-500">Track and manage all your submitted complaints</p>
            </div>
            <a href="{{ route('student.complaints.create') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded bg-blue-600 text-white text-xs font-semibold hover:bg-blue-700 transition">
                <i data-lucide="plus-circle" class="w-4 h-4"></i>
                <span>Submit Complaint</span>
            </a>
        </div>

        <!-- Filter Bar -->
        <div class="bg-white border border-slate-200 rounded p-4">
            <form method="GET" action="{{ route('student.complaints.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div>
                    <label for="search" class="block text-[11px] font-semibold text-slate-600 mb-1">Search Subject or Number</label>
                    <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="e.g. CCMS-2026 or Lab Wi-Fi"
                           class="w-full px-3 py-1.5 text-xs rounded border border-slate-300 focus:outline-none focus:border-blue-600">
                </div>

                <div>
                    <label for="status" class="block text-[11px] font-semibold text-slate-600 mb-1">Status Filter</label>
                    <select name="status" id="status" class="w-full px-3 py-1.5 text-xs rounded border border-slate-300 focus:outline-none focus:border-blue-600 bg-white">
                        <option value="">All Statuses</option>
                        <option value="SUBMITTED" {{ request('status') === 'SUBMITTED' ? 'selected' : '' }}>Submitted</option>
                        <option value="ASSIGNED" {{ request('status') === 'ASSIGNED' ? 'selected' : '' }}>Assigned</option>
                        <option value="IN_PROGRESS" {{ request('status') === 'IN_PROGRESS' ? 'selected' : '' }}>In Progress</option>
                        <option value="RESOLUTION_SUBMITTED" {{ request('status') === 'RESOLUTION_SUBMITTED' ? 'selected' : '' }}>Resolution Submitted</option>
                        <option value="VERIFIED_FOR_CLOSURE" {{ request('status') === 'VERIFIED_FOR_CLOSURE' ? 'selected' : '' }}>Verified for Closure</option>
                        <option value="CLOSED" {{ request('status') === 'CLOSED' ? 'selected' : '' }}>Closed</option>
                        <option value="REOPENED" {{ request('status') === 'REOPENED' ? 'selected' : '' }}>Reopened</option>
                        <option value="REJECTED" {{ request('status') === 'REJECTED' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>

                <div class="flex items-end gap-2">
                    <button type="submit" class="px-4 py-1.5 rounded bg-slate-900 text-white text-xs font-semibold hover:bg-slate-900 transition">
                        Filter
                    </button>
                    @if(request()->hasAny(['search', 'status']))
                        <a href="{{ route('student.complaints.index') }}" class="px-3 py-1.5 rounded border border-slate-300 text-slate-600 text-xs font-semibold hover:bg-slate-50 transition">
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
                            <th class="px-6 py-3">Complaint Number</th>
                            <th class="px-6 py-3">Subject</th>
                            <th class="px-6 py-3">Department</th>
                            <th class="px-6 py-3">Category</th>
                            <th class="px-6 py-3">Priority</th>
                            <th class="px-6 py-3">Status</th>
                            <th class="px-6 py-3">Submitted On</th>
                            <th class="px-6 py-3 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse($complaints as $c)
                            <tr class="hover:bg-slate-50/60 transition">
                                <td class="px-6 py-3.5 font-medium text-slate-900 whitespace-nowrap">
                                    {{ $c->complaint_number }}
                                </td>
                                <td class="px-6 py-3.5 max-w-xs truncate text-slate-800 font-medium">
                                    {{ $c->subject }}
                                </td>
                                <td class="px-6 py-3.5 text-slate-600 whitespace-nowrap">
                                    {{ $c->department?->name ?? 'N/A' }}
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
                                    {{ $c->created_at->format('d M Y, H:i') }}
                                </td>
                                <td class="px-6 py-3.5 text-right whitespace-nowrap">
                                    <a href="{{ route('student.complaints.show', $c->id) }}" class="text-blue-600 hover:underline font-medium">
                                        View &rarr;
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center text-slate-500">
                                    <p class="font-medium text-slate-700">No complaints matching your criteria.</p>
                                    <p class="text-xs text-slate-400 mt-1">Try resetting filters or submit a new complaint.</p>
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
