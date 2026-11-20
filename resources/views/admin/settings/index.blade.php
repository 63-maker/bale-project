<x-layouts.app :title="'System Settings'">
    <div class="max-w-3xl mx-auto space-y-6">
        <div>
            <h1 class="text-xl font-bold tracking-tight text-slate-900">System Configuration & Settings</h1>
            <p class="text-xs text-slate-500">Configure institutional grievance rules, SLA targets, and routing policies</p>
        </div>

        <div class="bg-white border border-slate-200 rounded p-6">
            <form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Institutional Identity -->
                <div>
                    <h3 class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-3">Institutional Identity</h3>
                    <div class="space-y-3">
                        <div>
                            <label for="college_name" class="block text-xs font-semibold text-slate-700 mb-1">
                                College / Institution Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="college_name" id="college_name" value="{{ old('college_name', $settings['college_name']) }}" required
                                   class="w-full px-3 py-2 text-xs rounded border border-slate-300 focus:outline-none focus:border-blue-600">
                        </div>

                        <div>
                            <label for="support_email" class="block text-xs font-semibold text-slate-700 mb-1">
                                Official Grievance Committee Email <span class="text-red-500">*</span>
                            </label>
                            <input type="email" name="support_email" id="support_email" value="{{ old('support_email', $settings['support_email']) }}" required
                                   class="w-full px-3 py-2 text-xs rounded border border-slate-300 focus:outline-none focus:border-blue-600">
                        </div>
                    </div>
                </div>

                <!-- Assignment Engine Policy -->
                <div class="pt-4 border-t border-slate-200">
                    <h3 class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-3">Assignment Engine</h3>
                    <div class="p-4 bg-slate-50 rounded border border-slate-200 flex items-start gap-3">
                        <input type="checkbox" name="auto_assignment_enabled" id="auto_assignment_enabled" value="1" 
                               {{ $settings['auto_assignment_enabled'] ? 'checked' : '' }}
                               class="rounded border-slate-300 text-blue-600 focus:ring-0 mt-0.5">
                        <label for="auto_assignment_enabled" class="text-xs cursor-pointer">
                            <span class="font-bold text-slate-800 block">Enable Automatic Workload Routing</span>
                            <span class="text-slate-500 block mt-0.5">
                                Automatically routes newly submitted complaints to active Management members in the matching department with the lowest current open workload, using employee ID as deterministic tie-breaker. Admin can always manually override.
                            </span>
                        </label>
                    </div>
                </div>

                <!-- SLA Response Deadlines -->
                <div class="pt-4 border-t border-slate-200">
                    <h3 class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Service Level Agreement (SLA) Resolution Windows</h3>
                    <p class="text-xs text-slate-500 mb-4">Set maximum allowed resolution hours before a complaint is flagged as Overdue</p>

                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        <div>
                            <label for="sla_low" class="block text-[11px] font-semibold text-slate-600 mb-1">LOW Priority (Hours)</label>
                            <input type="number" name="sla_low" id="sla_low" value="{{ old('sla_low', $settings['sla_hours']['LOW'] ?? 120) }}" min="1" max="720" required
                                   class="w-full px-3 py-1.5 text-xs rounded border border-slate-300 focus:outline-none focus:border-blue-600">
                        </div>

                        <div>
                            <label for="sla_medium" class="block text-[11px] font-semibold text-slate-600 mb-1">MEDIUM (Hours)</label>
                            <input type="number" name="sla_medium" id="sla_medium" value="{{ old('sla_medium', $settings['sla_hours']['MEDIUM'] ?? 72) }}" min="1" max="720" required
                                   class="w-full px-3 py-1.5 text-xs rounded border border-slate-300 focus:outline-none focus:border-blue-600">
                        </div>

                        <div>
                            <label for="sla_high" class="block text-[11px] font-semibold text-slate-600 mb-1">HIGH (Hours)</label>
                            <input type="number" name="sla_high" id="sla_high" value="{{ old('sla_high', $settings['sla_hours']['HIGH'] ?? 48) }}" min="1" max="720" required
                                   class="w-full px-3 py-1.5 text-xs rounded border border-slate-300 focus:outline-none focus:border-blue-600">
                        </div>

                        <div>
                            <label for="sla_critical" class="block text-[11px] font-semibold text-slate-600 mb-1">CRITICAL (Hours)</label>
                            <input type="number" name="sla_critical" id="sla_critical" value="{{ old('sla_critical', $settings['sla_hours']['CRITICAL'] ?? 24) }}" min="1" max="720" required
                                   class="w-full px-3 py-1.5 text-xs rounded border border-slate-300 focus:outline-none focus:border-blue-600">
                        </div>
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-200 text-right">
                    <button type="submit" class="px-5 py-2.5 rounded bg-slate-900 text-white text-xs font-semibold hover:bg-slate-900 transition">
                        Save System Configuration
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
