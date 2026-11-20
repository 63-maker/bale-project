<x-layouts.app :title="'Audit Logs'">
    <div class="space-y-6">
        <div>
            <h1 class="text-xl font-bold tracking-tight text-slate-900">Security & System Audit Logs</h1>
            <p class="text-xs text-slate-500">Immutable chronological record of administrative interventions, status shifts, and logins</p>
        </div>

        <!-- Filter Bar -->
        <div class="bg-white border border-slate-200 rounded p-4">
            <form method="GET" action="{{ route('admin.audit-logs.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                <div>
                    <label for="action" class="block text-[11px] font-semibold text-slate-600 mb-1">Action Name</label>
                    <input type="text" name="action" id="action" value="{{ request('action') }}" placeholder="e.g. COMPLAINT_ASSIGNED..."
                           class="w-full px-3 py-1.5 text-xs rounded border border-slate-300 focus:outline-none focus:border-blue-600">
                </div>

                <div>
                    <label for="start_date" class="block text-[11px] font-semibold text-slate-600 mb-1">From Date</label>
                    <input type="date" name="start_date" id="start_date" value="{{ request('start_date') }}"
                           class="w-full px-3 py-1.5 text-xs rounded border border-slate-300 focus:outline-none focus:border-blue-600">
                </div>

                <div>
                    <label for="end_date" class="block text-[11px] font-semibold text-slate-600 mb-1">To Date</label>
                    <input type="date" name="end_date" id="end_date" value="{{ request('end_date') }}"
                           class="w-full px-3 py-1.5 text-xs rounded border border-slate-300 focus:outline-none focus:border-blue-600">
                </div>

                <div class="flex items-end gap-2">
                    <button type="submit" class="px-4 py-1.5 rounded bg-slate-900 text-white text-xs font-semibold hover:bg-slate-900 transition">
                        Filter Logs
                    </button>
                    @if(request()->hasAny(['action', 'start_date', 'end_date']))
                        <a href="{{ route('admin.audit-logs.index') }}" class="px-3 py-1.5 rounded border border-slate-300 text-slate-600 text-xs font-semibold hover:bg-slate-50 transition">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <div class="bg-white border border-slate-200 rounded overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-600 font-semibold uppercase text-[10px] tracking-wider">
                        <tr>
                            <th class="px-6 py-3">Timestamp</th>
                            <th class="px-6 py-3">Actor</th>
                            <th class="px-6 py-3">Action</th>
                            <th class="px-6 py-3">Entity</th>
                            <th class="px-6 py-3">Details / Context</th>
                            <th class="px-6 py-3">IP Address</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse($logs as $log)
                            <tr class="hover:bg-slate-50/60 transition">
                                <td class="px-6 py-3.5 text-slate-500 whitespace-nowrap font-mono text-[11px]">
                                    {{ $log->created_at->format('Y-m-d H:i:s') }}
                                </td>
                                <td class="px-6 py-3.5 text-slate-800 whitespace-nowrap font-medium">
                                    {{ $log->actor?->full_name ?? 'System / Anonymous' }}
                                    @if($log->actor)
                                        <div class="text-[10px] text-slate-400 capitalize">({{ $log->actor->role }})</div>
                                    @endif
                                </td>
                                <td class="px-6 py-3.5 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold font-mono bg-slate-100 text-slate-900 border border-slate-300">
                                        {{ $log->action }}
                                    </span>
                                </td>
                                <td class="px-6 py-3.5 text-slate-600 whitespace-nowrap">
                                    {{ $log->entity_type }}
                                    @if($log->entity_id)
                                        <span class="text-[10px] text-slate-400 font-mono block">#{{ Str::limit($log->entity_id, 12) }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-3.5 text-slate-700 max-w-sm truncate text-[11px] font-mono">
                                    @if($log->details)
                                        {{ json_encode($log->details) }}
                                    @else
                                        <span class="text-slate-400 italic">None</span>
                                    @endif
                                </td>
                                <td class="px-6 py-3.5 text-slate-400 whitespace-nowrap font-mono text-[11px]">
                                    {{ $log->ip_address ?? '127.0.0.1' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-slate-500">
                                    <p class="font-medium text-slate-700">No audit log entries recorded.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($logs->hasPages())
                <div class="px-6 py-4 border-t border-slate-200 bg-slate-50">
                    {{ $logs->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
