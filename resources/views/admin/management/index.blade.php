<x-layouts.app :title="'Management Accounts'">
    <div class="space-y-6" x-data="{ addModal: false }">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-xl font-bold tracking-tight text-[#172B4D]">Management Accounts</h1>
                <p class="text-xs text-slate-500">Authorized Management investigation officers across college departments</p>
            </div>
            <button type="button" @click="addModal = true" class="inline-flex items-center gap-2 px-4 py-2 rounded bg-[#172B4D] text-white text-xs font-semibold hover:bg-slate-900 transition">
                <i data-lucide="user-plus" class="w-4 h-4"></i>
                <span>Create Management Account</span>
            </button>
        </div>

        <div class="bg-white border border-slate-200 rounded overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-600 font-semibold uppercase text-[10px] tracking-wider">
                        <tr>
                            <th class="px-6 py-3">Officer Name</th>
                            <th class="px-6 py-3">Employee ID</th>
                            <th class="px-6 py-3">Email Address</th>
                            <th class="px-6 py-3">Department</th>
                            <th class="px-6 py-3">Active Workload</th>
                            <th class="px-6 py-3">Status</th>
                            <th class="px-6 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse($managementMembers as $m)
                            <tr class="hover:bg-slate-50/60 transition">
                                <td class="px-6 py-3.5 font-medium text-slate-900 whitespace-nowrap">
                                    {{ $m->full_name }}
                                </td>
                                <td class="px-6 py-3.5 text-slate-700 whitespace-nowrap font-medium">
                                    {{ $m->employee_id }}
                                </td>
                                <td class="px-6 py-3.5 text-slate-600 whitespace-nowrap">
                                    {{ $m->email }}
                                </td>
                                <td class="px-6 py-3.5 text-slate-600 whitespace-nowrap">
                                    {{ $m->department?->name ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-3.5 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold {{ $m->open_workload > 0 ? 'bg-blue-50 text-[#2563EB] border border-blue-200' : 'bg-slate-100 text-slate-600' }}">
                                        {{ $m->open_workload }} open items
                                    </span>
                                </td>
                                <td class="px-6 py-3.5 whitespace-nowrap">
                                    @if($m->isActive())
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            Active
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-rose-50 text-rose-700 border border-rose-200">
                                            Suspended
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-3.5 text-right whitespace-nowrap">
                                    <form method="POST" action="{{ route('admin.management.status', $m->id) }}" class="inline-block">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="px-2.5 py-1 rounded text-xs font-medium {{ $m->isActive() ? 'border border-rose-300 text-rose-700 hover:bg-rose-50' : 'border border-emerald-300 text-emerald-700 hover:bg-emerald-50' }}">
                                            {{ $m->isActive() ? 'Suspend' : 'Activate' }}
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-slate-500">
                                    <p class="font-medium text-slate-700">No Management accounts provisioned yet.</p>
                                    <button type="button" @click="addModal = true" class="mt-2 text-xs text-[#2563EB] hover:underline font-semibold">
                                        Create First Management Account &rarr;
                                    </button>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Create Management Account Modal -->
        <div x-show="addModal" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/50 flex items-center justify-center p-4" style="display: none;">
            <div class="bg-white rounded border border-slate-200 max-w-lg w-full p-6" @click.outside="addModal = false">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-bold text-[#172B4D]">Create New Management Account</h3>
                    <button @click="addModal = false" class="text-slate-400 hover:text-slate-600">&times;</button>
                </div>
                <form method="POST" action="{{ route('admin.management.store') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label for="m_name" class="block text-xs font-semibold text-slate-700 mb-1">Full Name <span class="text-red-500">*</span></label>
                        <input type="text" name="full_name" id="m_name" required placeholder="e.g. Dr. Alan Turing"
                               class="w-full px-3 py-2 text-xs rounded border border-slate-300 focus:outline-none focus:border-[#2563EB]">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label for="m_empid" class="block text-xs font-semibold text-slate-700 mb-1">Employee ID <span class="text-red-500">*</span></label>
                            <input type="text" name="employee_id" id="m_empid" required placeholder="e.g. MGT0042"
                                   class="w-full px-3 py-2 text-xs rounded border border-slate-300 focus:outline-none focus:border-[#2563EB]">
                        </div>
                        <div>
                            <label for="m_email" class="block text-xs font-semibold text-slate-700 mb-1">Email Address <span class="text-red-500">*</span></label>
                            <input type="email" name="email" id="m_email" required placeholder="e.g. mgt@college.edu"
                                   class="w-full px-3 py-2 text-xs rounded border border-slate-300 focus:outline-none focus:border-[#2563EB]">
                        </div>
                    </div>

                    <div>
                        <label for="m_dept" class="block text-xs font-semibold text-slate-700 mb-1">Assigned Department <span class="text-red-500">*</span></label>
                        <select name="department_id" id="m_dept" required class="w-full px-3 py-2 text-xs rounded border border-slate-300 focus:outline-none focus:border-[#2563EB] bg-white">
                            <option value="">Select Department</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}">{{ $dept->name }} ({{ $dept->code }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="m_pass" class="block text-xs font-semibold text-slate-700 mb-1">Temporary Initial Password <span class="text-red-500">*</span></label>
                        <input type="password" name="password" id="m_pass" required minlength="8" placeholder="Minimum 8 characters"
                               class="w-full px-3 py-2 text-xs rounded border border-slate-300 focus:outline-none focus:border-[#2563EB]">
                        <p class="text-[11px] text-slate-400 mt-1">Management member will be required to change password on first login.</p>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-200">
                        <button type="button" @click="addModal = false" class="px-3 py-1.5 rounded border border-slate-300 text-xs text-slate-600 hover:bg-slate-50">Cancel</button>
                        <button type="submit" class="px-4 py-1.5 rounded bg-[#172B4D] text-white text-xs font-semibold hover:bg-slate-900">Create Account</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app>
