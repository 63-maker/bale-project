<x-layouts.app :title="'Admin Manage ' . $complaint->complaint_number">
    <div class="max-w-6xl mx-auto space-y-6" x-data="{ assignModal: false, forceCloseModal: false, rejectModal: false }">
        <!-- Top Action Ribbon -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white border border-slate-200 rounded p-6">
            <div>
                <a href="{{ route('admin.complaints.index') }}" class="text-xs font-medium text-slate-500 hover:text-slate-800 flex items-center gap-1 mb-1">
                    &larr; Back to Complaints Registry
                </a>
                <div class="flex items-center gap-3">
                    <h1 class="text-xl font-bold tracking-tight text-[#172B4D]">{{ $complaint->complaint_number }}</h1>
                    <x-status-badge :status="$complaint->status" />
                    <x-priority-badge :priority="$complaint->priority" />
                </div>
            </div>

            <!-- Administrative Action Buttons -->
            <div class="flex flex-wrap items-center gap-2">
                @if(! in_array($complaint->status, [\App\Models\Complaint::STATUS_CLOSED, \App\Models\Complaint::STATUS_REJECTED]))
                    <button type="button" @click="assignModal = true" class="px-3.5 py-2 rounded bg-[#172B4D] text-white text-xs font-semibold hover:bg-slate-900 transition flex items-center gap-1.5">
                        <i data-lucide="user-plus" class="w-4 h-4"></i>
                        <span>Assign / Reassign</span>
                    </button>

                    <form method="POST" action="{{ route('admin.complaints.auto-assign', $complaint->id) }}">
                        @csrf
                        <button type="submit" class="px-3.5 py-2 rounded border border-slate-300 bg-white text-slate-700 text-xs font-semibold hover:bg-slate-50 transition flex items-center gap-1.5" title="Auto-assign based on lowest active workload in department">
                            <i data-lucide="zap" class="w-4 h-4 text-amber-500"></i>
                            <span>Auto Route</span>
                        </button>
                    </form>

                    <button type="button" @click="forceCloseModal = true" class="px-3.5 py-2 rounded bg-rose-600 text-white text-xs font-semibold hover:bg-rose-700 transition flex items-center gap-1.5">
                        <i data-lucide="x-circle" class="w-4 h-4"></i>
                        <span>Emergency Close</span>
                    </button>

                    <button type="button" @click="rejectModal = true" class="px-3.5 py-2 rounded border border-rose-300 text-rose-700 text-xs font-semibold hover:bg-rose-50 transition">
                        Reject Complaint
                    </button>
                @endif
            </div>
        </div>

        <!-- Verification Queue Action Box (When RESOLUTION_SUBMITTED) -->
        @if($complaint->status === \App\Models\Complaint::STATUS_RESOLUTION_SUBMITTED)
            <div class="bg-amber-50 border-2 border-amber-300 rounded p-6">
                <div class="flex items-center gap-2 mb-2 text-amber-900">
                    <i data-lucide="shield-check" class="w-5 h-5 text-amber-700"></i>
                    <h2 class="text-sm font-bold">Admin Verification Required</h2>
                </div>
                <p class="text-xs text-amber-800 mb-4 leading-relaxed">
                    Management has submitted a resolution. Review the proposed resolution description and evidence below, then record your official decision.
                </p>

                <div class="bg-white p-4 rounded border border-amber-200 mb-4 text-xs text-slate-800 whitespace-pre-line leading-relaxed">
                    <span class="font-bold text-slate-900 block mb-1">Submitted Resolution Description:</span>
                    {{ $complaint->resolution_description }}
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Approve Form -->
                    <form method="POST" action="{{ route('admin.complaints.verify', $complaint->id) }}" class="bg-white p-4 rounded border border-emerald-300 space-y-3">
                        @csrf
                        <input type="hidden" name="decision" value="approved">
                        <h4 class="text-xs font-bold text-emerald-800 flex items-center gap-1.5">
                            <i data-lucide="check" class="w-4 h-4 text-emerald-600"></i>
                            <span>Approve Resolution for Closure</span>
                        </h4>
                        <div>
                            <input type="text" name="remarks" placeholder="Optional approval remarks..."
                                   class="w-full px-3 py-1.5 text-xs rounded border border-slate-300 focus:outline-none focus:border-emerald-600">
                        </div>
                        <button type="submit" class="w-full py-2 rounded bg-emerald-600 text-white text-xs font-semibold hover:bg-emerald-700 transition">
                            Approve for Closure
                        </button>
                    </form>

                    <!-- Reject Form -->
                    <form method="POST" action="{{ route('admin.complaints.verify', $complaint->id) }}" class="bg-white p-4 rounded border border-rose-300 space-y-3">
                        @csrf
                        <input type="hidden" name="decision" value="rejected">
                        <h4 class="text-xs font-bold text-rose-800 flex items-center gap-1.5">
                            <i data-lucide="x" class="w-4 h-4 text-rose-600"></i>
                            <span>Reject Resolution (Requires Explanation)</span>
                        </h4>
                        <div>
                            <input type="text" name="remarks" required placeholder="Mandatory explanation for rejection..."
                                   class="w-full px-3 py-1.5 text-xs rounded border border-slate-300 focus:outline-none focus:border-rose-600">
                        </div>
                        <button type="submit" class="w-full py-2 rounded bg-rose-600 text-white text-xs font-semibold hover:bg-rose-700 transition">
                            Reject Resolution & Request Revision
                        </button>
                    </form>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left 2 Cols: Details, Attachments, Comments -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Grievance Core Info -->
                <div class="bg-white border border-slate-200 rounded p-6">
                    <h2 class="text-base font-bold text-[#172B4D] mb-3">{{ $complaint->subject }}</h2>
                    <div class="text-xs text-slate-700 leading-relaxed whitespace-pre-line bg-slate-50 p-4 rounded border border-slate-200 mb-5">
                        {{ $complaint->description }}
                    </div>

                    <!-- Supporting Attachments -->
                    <div>
                        <div class="text-xs font-semibold text-slate-700 mb-2">Student Attachments ({{ $complaint->attachments->count() }})</div>
                        @if($complaint->attachments->isNotEmpty())
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                @foreach($complaint->attachments as $att)
                                    <a href="{{ route('admin.attachments.download', $att->id) }}" class="flex items-center gap-2 p-2.5 rounded border border-slate-200 bg-white hover:bg-slate-50 text-xs transition">
                                        <i data-lucide="paperclip" class="w-4 h-4 text-slate-400"></i>
                                        <div class="min-w-0 flex-1 truncate font-medium text-slate-700">
                                            {{ $att->original_filename }}
                                        </div>
                                        <span class="text-[10px] text-slate-400">
                                            {{ round($att->file_size / 1024, 0) }} KB
                                        </span>
                                    </a>
                                @endforeach
                            </div>
                        @else
                            <div class="text-xs text-slate-400 italic">No attachments attached.</div>
                        @endif
                    </div>
                </div>

                <!-- Resolution Detail (If Exists) -->
                @if($complaint->resolution_description)
                    <div class="bg-white border border-slate-200 rounded p-6">
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="text-sm font-bold text-[#172B4D]">Investigation Resolution</h3>
                            @if($complaint->verified_at)
                                <span class="text-[11px] text-emerald-700 font-semibold flex items-center gap-1">
                                    <i data-lucide="check" class="w-3.5 h-3.5"></i> Verified by Admin
                                </span>
                            @endif
                        </div>
                        <div class="text-xs text-slate-700 leading-relaxed bg-slate-50 p-4 rounded border border-slate-200 whitespace-pre-line">
                            {{ $complaint->resolution_description }}
                        </div>
                    </div>
                @endif

                <!-- Discussion & Audit Conversation Thread -->
                <div class="bg-white border border-slate-200 rounded p-6" x-data="{ visibility: 'internal' }">
                    <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-200">
                        <h3 class="text-sm font-bold text-[#172B4D]">Case Discussion & Internal Notes</h3>
                    </div>

                    <!-- Messages -->
                    <div class="space-y-3 mb-6">
                        @forelse($complaint->comments as $c)
                            <div class="p-3.5 rounded text-xs {{ $c->visibility === 'internal' ? 'bg-amber-50/50 border border-amber-200' : 'bg-slate-50 border border-slate-200' }}">
                                <div class="flex items-center justify-between mb-1.5">
                                    <div class="flex items-center gap-2">
                                        <span class="font-semibold text-slate-900">
                                            {{ $c->sender?->full_name ?? 'User' }}
                                        </span>
                                        <span class="text-[10px] text-slate-500 capitalize">
                                            ({{ $c->sender?->role === 'management' ? 'Management' : ucfirst($c->sender?->role ?? '') }})
                                        </span>
                                        @if($c->visibility === 'internal')
                                            <span class="px-1.5 py-0.2 rounded text-[10px] font-bold bg-amber-200 text-amber-900 uppercase">
                                                Internal
                                            </span>
                                        @else
                                            <span class="px-1.5 py-0.2 rounded text-[10px] font-medium bg-blue-100 text-blue-800">
                                                Public
                                            </span>
                                        @endif
                                    </div>
                                    <span class="text-[10px] text-slate-400">
                                        {{ $c->created_at->format('d M Y, H:i') }}
                                    </span>
                                </div>
                                <div class="text-slate-700 leading-relaxed whitespace-pre-line">
                                    {{ $c->message }}
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-6 text-slate-400 text-xs">
                                No communications recorded on this complaint yet.
                            </div>
                        @endforelse
                    </div>

                    <!-- Admin Post Communication -->
                    <form method="POST" action="{{ route('admin.complaints.comments.store', $complaint->id) }}" class="space-y-3 pt-3 border-t border-slate-200">
                        @csrf
                        <div class="flex items-center gap-4 text-xs">
                            <span class="font-semibold text-slate-700">Admin Message Visibility:</span>
                            <label class="inline-flex items-center gap-1.5 cursor-pointer">
                                <input type="radio" name="visibility" value="public" x-model="visibility" class="text-[#2563EB] focus:ring-0">
                                <span class="text-slate-700">Public (Visible to Student & Management)</span>
                            </label>
                            <label class="inline-flex items-center gap-1.5 cursor-pointer">
                                <input type="radio" name="visibility" value="internal" x-model="visibility" class="text-amber-600 focus:ring-0">
                                <span class="text-amber-800 font-medium">Internal Note (Staff/Admin Only)</span>
                            </label>
                        </div>
                        <div>
                            <textarea name="message" rows="3" required placeholder="Type official administrative remark or instruction..."
                                      class="w-full px-3 py-2 text-xs rounded border border-slate-300 focus:outline-none focus:border-[#2563EB]"></textarea>
                        </div>
                        <div class="text-right">
                            <button type="submit" class="px-4 py-2 rounded bg-[#172B4D] text-white text-xs font-semibold hover:bg-slate-900 transition">
                                Post Directive
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Right 1 Col: Student, Routing, Assignments, Timeline -->
            <div class="space-y-6">
                <!-- Submitting Student Info -->
                <div class="bg-white border border-slate-200 rounded p-6">
                    <h3 class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-4">Student Profile</h3>
                    <dl class="space-y-3 text-xs">
                        <div>
                            <dt class="text-slate-400 font-medium">Student Name</dt>
                            <dd class="text-slate-800 font-semibold mt-0.5">{{ $complaint->student?->full_name }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-400 font-medium">Register Number</dt>
                            <dd class="text-slate-800 font-semibold mt-0.5">{{ $complaint->student?->register_number }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-400 font-medium">College Email</dt>
                            <dd class="text-slate-800 mt-0.5">{{ $complaint->student?->email }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-400 font-medium">Department & Year</dt>
                            <dd class="text-slate-800 mt-0.5">
                                {{ $complaint->student?->department?->name ?? 'N/A' }} &bull; {{ $complaint->student?->study_year }} Year
                            </dd>
                        </div>
                    </dl>
                </div>

                <!-- Assigned Management Officer Info -->
                <div class="bg-white border border-slate-200 rounded p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Assigned Management</h3>
                        @if($complaint->activeAssignment)
                            <span class="px-1.5 py-0.2 rounded text-[10px] font-bold bg-blue-100 text-blue-800 capitalize">
                                {{ $complaint->activeAssignment->assignment_method }}
                            </span>
                        @endif
                    </div>
                    @if($complaint->activeAssignment)
                        <dl class="space-y-3 text-xs">
                            <div>
                                <dt class="text-slate-400 font-medium">Officer Name</dt>
                                <dd class="text-slate-800 font-semibold mt-0.5">
                                    {{ $complaint->activeAssignment->management?->full_name }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-slate-400 font-medium">Employee ID</dt>
                                <dd class="text-slate-800 mt-0.5">{{ $complaint->activeAssignment->management?->employee_id }}</dd>
                            </div>
                            <div>
                                <dt class="text-slate-400 font-medium">Assigned On</dt>
                                <dd class="text-slate-800 mt-0.5">{{ $complaint->activeAssignment->assigned_at->format('d M Y, H:i') }}</dd>
                            </div>
                        </dl>
                    @else
                        <div class="text-xs text-slate-400 italic mb-3">No active Management officer currently assigned.</div>
                        <button type="button" @click="assignModal = true" class="w-full py-1.5 rounded bg-[#172B4D] text-white text-xs font-semibold hover:bg-slate-900">
                            Assign Now
                        </button>
                    @endif
                </div>

                <!-- Status Timeline -->
                <div class="bg-white border border-slate-200 rounded p-6">
                    <h3 class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-4">Audit Status History</h3>
                    <div class="relative pl-6 space-y-4 border-l border-slate-200 text-xs">
                        @foreach($complaint->statusHistory as $history)
                            <div class="relative">
                                <div class="absolute -left-[31px] top-0.5 w-2.5 h-2.5 rounded-full bg-[#2563EB] border-2 border-white"></div>
                                <div class="font-semibold text-slate-800">
                                    <x-status-badge :status="$history->new_status" />
                                </div>
                                <div class="text-[11px] text-slate-500 mt-1">
                                    {{ $history->remarks }}
                                </div>
                                <div class="text-[10px] text-slate-400 mt-0.5">
                                    {{ $history->created_at->format('d M Y, H:i') }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Assignment Modal -->
        <div x-show="assignModal" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/50 flex items-center justify-center p-4" style="display: none;">
            <div class="bg-white rounded border border-slate-200 max-w-md w-full p-6" @click.outside="assignModal = false">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-bold text-[#172B4D]">Assign / Reassign Complaint</h3>
                    <button @click="assignModal = false" class="text-slate-400 hover:text-slate-600">&times;</button>
                </div>
                <form method="POST" action="{{ route('admin.complaints.assign', $complaint->id) }}" class="space-y-4">
                    @csrf
                    <div>
                        <label for="management_id" class="block text-xs font-semibold text-slate-700 mb-1">Select Management Member <span class="text-red-500">*</span></label>
                        <select name="management_id" id="management_id" required class="w-full px-3 py-2 text-xs rounded border border-slate-300 focus:outline-none focus:border-[#2563EB] bg-white">
                            <option value="">Choose Management Officer</option>
                            @foreach($managementMembers as $m)
                                <option value="{{ $m->id }}" {{ $complaint->activeAssignment?->management_id === $m->id ? 'selected' : '' }}>
                                    {{ $m->full_name }} ({{ $m->employee_id }}) - {{ $m->department?->code }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="remarks" class="block text-xs font-semibold text-slate-700 mb-1">Administrative Remarks (Optional)</label>
                        <input type="text" name="remarks" id="remarks" placeholder="e.g. Assigned for immediate laboratory inspection"
                               class="w-full px-3 py-2 text-xs rounded border border-slate-300 focus:outline-none focus:border-[#2563EB]">
                    </div>
                    <div class="flex items-center justify-end gap-2 pt-2">
                        <button type="button" @click="assignModal = false" class="px-3 py-1.5 rounded border border-slate-300 text-xs text-slate-600 hover:bg-slate-50">Cancel</button>
                        <button type="submit" class="px-4 py-1.5 rounded bg-[#172B4D] text-white text-xs font-semibold hover:bg-slate-900">Confirm Assignment</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Emergency Force-Close Modal -->
        <div x-show="forceCloseModal" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/50 flex items-center justify-center p-4" style="display: none;">
            <div class="bg-white rounded border border-slate-200 max-w-md w-full p-6" @click.outside="forceCloseModal = false">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-bold text-rose-700">Administrative Emergency Force-Closure</h3>
                    <button @click="forceCloseModal = false" class="text-slate-400 hover:text-slate-600">&times;</button>
                </div>
                <p class="text-xs text-slate-600 mb-4 leading-relaxed">
                    Emergency closure bypasses normal management resolution verification. A mandatory justification reason is logged for institutional audit trails.
                </p>
                <form method="POST" action="{{ route('admin.complaints.force-close', $complaint->id) }}" class="space-y-4">
                    @csrf
                    <div>
                        <label for="fc_reason" class="block text-xs font-semibold text-slate-700 mb-1">Mandatory Justification Reason <span class="text-red-500">*</span></label>
                        <textarea name="reason" id="fc_reason" rows="4" required minlength="5" placeholder="Specify administrative audit reason for immediate closure..."
                                  class="w-full px-3 py-2 text-xs rounded border border-slate-300 focus:outline-none focus:border-rose-600"></textarea>
                    </div>
                    <div class="flex items-center justify-end gap-2 pt-2">
                        <button type="button" @click="forceCloseModal = false" class="px-3 py-1.5 rounded border border-slate-300 text-xs text-slate-600 hover:bg-slate-50">Cancel</button>
                        <button type="submit" class="px-4 py-1.5 rounded bg-rose-600 text-white text-xs font-semibold hover:bg-rose-700">Confirm Emergency Closure</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Administrative Reject Modal -->
        <div x-show="rejectModal" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/50 flex items-center justify-center p-4" style="display: none;">
            <div class="bg-white rounded border border-slate-200 max-w-md w-full p-6" @click.outside="rejectModal = false">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-bold text-slate-800">Reject Complaint</h3>
                    <button @click="rejectModal = false" class="text-slate-400 hover:text-slate-600">&times;</button>
                </div>
                <form method="POST" action="{{ route('admin.complaints.reject', $complaint->id) }}" class="space-y-4">
                    @csrf
                    <div>
                        <label for="rej_reason" class="block text-xs font-semibold text-slate-700 mb-1">Mandatory Rejection Explanation <span class="text-red-500">*</span></label>
                        <textarea name="rejection_reason" id="rej_reason" rows="4" required minlength="5" placeholder="Explain why this grievance is invalid or rejected..."
                                  class="w-full px-3 py-2 text-xs rounded border border-slate-300 focus:outline-none focus:border-slate-800"></textarea>
                    </div>
                    <div class="flex items-center justify-end gap-2 pt-2">
                        <button type="button" @click="rejectModal = false" class="px-3 py-1.5 rounded border border-slate-300 text-xs text-slate-600 hover:bg-slate-50">Cancel</button>
                        <button type="submit" class="px-4 py-1.5 rounded bg-rose-600 text-white text-xs font-semibold hover:bg-rose-700">Reject Complaint</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app>
