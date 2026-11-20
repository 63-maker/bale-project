<x-layouts.app :title="'Verification Queue'">
    <div class="space-y-6">
        <div>
            <h1 class="text-xl font-bold tracking-tight text-slate-900">Resolution Verification Queue</h1>
            <p class="text-xs text-slate-500">Supervise, inspect and approve or reject investigation resolutions submitted by Management</p>
        </div>

        <div class="bg-white border border-slate-200 rounded overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-600 font-semibold uppercase text-[10px] tracking-wider">
                        <tr>
                            <th class="px-6 py-3">Complaint #</th>
                            <th class="px-6 py-3">Student</th>
                            <th class="px-6 py-3">Subject</th>
                            <th class="px-6 py-3">Department</th>
                            <th class="px-6 py-3">Assigned Management</th>
                            <th class="px-6 py-3">Resolution Date</th>
                            <th class="px-6 py-3 text-right">Review Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse($complaints as $c)
                            <tr class="hover:bg-slate-50/60 transition">
                                <td class="px-6 py-3.5 font-medium text-slate-900 whitespace-nowrap">
                                    {{ $c->complaint_number }}
                                </td>
                                <td class="px-6 py-3.5 text-slate-700 whitespace-nowrap">
                                    {{ $c->student?->full_name }}
                                </td>
                                <td class="px-6 py-3.5 max-w-xs truncate text-slate-800 font-medium">
                                    {{ $c->subject }}
                                </td>
                                <td class="px-6 py-3.5 text-slate-600 whitespace-nowrap">
                                    {{ $c->department?->code }}
                                </td>
                                <td class="px-6 py-3.5 text-slate-700 font-medium whitespace-nowrap">
                                    {{ $c->activeAssignment?->management?->full_name ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-3.5 text-slate-500 whitespace-nowrap">
                                    {{ $c->resolution_submitted_at ? $c->resolution_submitted_at->format('d M Y, H:i') : 'N/A' }}
                                </td>
                                <td class="px-6 py-3.5 text-right whitespace-nowrap">
                                    <a href="{{ route('admin.complaints.show', $c->id) }}" class="px-3 py-1 rounded bg-blue-600 text-white text-xs font-semibold hover:bg-blue-700 transition">
                                        Review Resolution &rarr;
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-slate-500">
                                    <i data-lucide="check-circle" class="w-8 h-8 text-emerald-500 mx-auto mb-2"></i>
                                    <p class="font-medium text-slate-700">Verification queue is completely clear.</p>
                                    <p class="text-xs text-slate-400 mt-1">No resolutions are currently awaiting administrative review.</p>
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
