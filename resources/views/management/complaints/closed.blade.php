<x-layouts.app :title="'Closed Complaints'">
    <div class="space-y-6">
        <div>
            <h1 class="text-xl font-bold tracking-tight text-[#172B4D]">Closed Complaints Archive</h1>
            <p class="text-xs text-slate-500">Historical archive of resolved grievances officially closed by your office</p>
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
                            <th class="px-6 py-3">Status</th>
                            <th class="px-6 py-3">Closed Date</th>
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
                                    {{ $c->student?->full_name }}
                                </td>
                                <td class="px-6 py-3.5 max-w-xs truncate text-slate-800 font-medium">
                                    {{ $c->subject }}
                                </td>
                                <td class="px-6 py-3.5 text-slate-600 whitespace-nowrap">
                                    {{ $c->department?->name ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-3.5 whitespace-nowrap">
                                    <x-status-badge :status="$c->status" />
                                </td>
                                <td class="px-6 py-3.5 text-slate-500 whitespace-nowrap">
                                    {{ $c->closed_at ? $c->closed_at->format('d M Y, H:i') : 'N/A' }}
                                </td>
                                <td class="px-6 py-3.5 text-right whitespace-nowrap">
                                    <a href="{{ route('management.complaints.show', $c->id) }}" class="text-[#2563EB] hover:underline font-medium">
                                        View Record &rarr;
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-slate-500">
                                    <p class="font-medium text-slate-700">No closed complaints in archive.</p>
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
