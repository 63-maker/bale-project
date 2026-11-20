<x-layouts.app :title="'Investigate ' . $complaint->complaint_number">
    <div class="max-w-6xl mx-auto space-y-6">
        <!-- Top Navigation & Action Ribbon -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white border border-slate-200 rounded p-6">
            <div>
                <a href="{{ route('management.assigned.index') }}" class="text-xs font-medium text-slate-500 hover:text-slate-800 flex items-center gap-1 mb-1">
                    &larr; Back to Assigned Queue
                </a>
                <div class="flex items-center gap-3">
                    <h1 class="text-xl font-bold tracking-tight text-slate-900">{{ $complaint->complaint_number }}</h1>
                    <x-status-badge :status="$complaint->status" />
                    <x-priority-badge :priority="$complaint->priority" />
                </div>
            </div>

            <!-- Workflow Action Buttons -->
            <div class="flex flex-wrap items-center gap-2">
                @if(in_array($complaint->status, [\App\Models\Complaint::STATUS_ASSIGNED, \App\Models\Complaint::STATUS_VERIFICATION_REJECTED]))
                    <form method="POST" action="{{ route('management.complaints.start', $complaint->id) }}">
                        @csrf
                        <button type="submit" class="px-4 py-2 rounded bg-blue-600 text-white text-xs font-semibold hover:bg-blue-700 transition">
                            Start Investigation
                        </button>
                    </form>
                @endif

                @if($complaint->status === \App\Models\Complaint::STATUS_VERIFIED_FOR_CLOSURE)
                    <form method="POST" action="{{ route('management.complaints.complete-closure', $complaint->id) }}">
                        @csrf
                        <button type="submit" class="px-4 py-2 rounded bg-emerald-600 text-white text-xs font-semibold hover:bg-emerald-700 transition flex items-center gap-1.5">
                            <i data-lucide="check-check" class="w-4 h-4"></i>
                            <span>Complete Final Closure</span>
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <!-- Verification Alerts -->
        @if($complaint->status === \App\Models\Complaint::STATUS_VERIFICATION_REJECTED)
            <div class="p-4 rounded bg-rose-50 border border-rose-300 text-rose-900 text-xs">
                <div class="font-bold flex items-center gap-2 mb-1 text-rose-800">
                    <i data-lucide="alert-octagon" class="w-4 h-4"></i>
                    <span>Admin Verification Rejected: Revision Required</span>
                </div>
                <p class="leading-relaxed">
                    <strong>Admin Reason:</strong> {{ $complaint->verification_remarks ?: 'Please re-investigate and submit a revised resolution.' }}
                </p>
            </div>
        @elseif($complaint->status === \App\Models\Complaint::STATUS_VERIFIED_FOR_CLOSURE)
            <div class="p-4 rounded bg-emerald-50 border border-emerald-300 text-emerald-900 text-xs flex items-center justify-between">
                <div>
                    <div class="font-bold flex items-center gap-2 text-emerald-800">
                        <i data-lucide="check-circle" class="w-4 h-4"></i>
                        <span>Admin Verified For Closure</span>
                    </div>
                    <p class="mt-0.5 text-slate-600">
                        Administration has approved your resolution. Please click <strong>Complete Final Closure</strong> to conclude the grievance.
                    </p>
                </div>
            </div>
        @elseif($complaint->status === \App\Models\Complaint::STATUS_RESOLUTION_SUBMITTED)
            <div class="p-4 rounded bg-amber-50 border border-amber-300 text-amber-900 text-xs flex items-center gap-3">
                <i data-lucide="clock" class="w-5 h-5 text-amber-600 shrink-0"></i>
                <div>
                    <strong>Resolution Awaiting Admin Verification</strong>
                    <p class="text-slate-600 mt-0.5">Your resolution has been submitted. The Administrator will review and verify before closure can occur.</p>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left 2 Cols: Details, Attachments, Resolution submission, Comments -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Grievance Core Info -->
                <div class="bg-white border border-slate-200 rounded p-6">
                    <h2 class="text-base font-bold text-slate-900 mb-3">{{ $complaint->subject }}</h2>
                    <div class="text-xs text-slate-700 leading-relaxed whitespace-pre-line bg-slate-50 p-4 rounded border border-slate-200 mb-5">
                        {{ $complaint->description }}
                    </div>

                    <!-- Supporting Attachments -->
                    <div>
                        <div class="text-xs font-semibold text-slate-700 mb-2">Student Supporting Attachments ({{ $complaint->attachments->count() }})</div>
                        @if($complaint->attachments->isNotEmpty())
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                @foreach($complaint->attachments as $att)
                                    <a href="{{ route('management.attachments.download', $att->id) }}" class="flex items-center gap-2 p-2.5 rounded border border-slate-200 bg-white hover:bg-slate-50 text-xs transition">
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

                <!-- Resolution Submission Panel -->
                @if(in_array($complaint->status, [\App\Models\Complaint::STATUS_IN_PROGRESS, \App\Models\Complaint::STATUS_VERIFICATION_REJECTED]))
                    <div class="bg-white border border-slate-200 rounded p-6">
                        <h3 class="text-sm font-bold text-slate-900 mb-2 flex items-center gap-2">
                            <i data-lucide="file-check" class="w-4 h-4 text-blue-600"></i>
                            <span>Submit Investigation Resolution</span>
                        </h3>
                        <p class="text-xs text-slate-500 mb-4">
                            Describe the investigation findings, corrective actions taken, and rationale. This will be routed to Admin for verification.
                        </p>

                        <form method="POST" action="{{ route('management.complaints.resolution.store', $complaint->id) }}" class="space-y-4">
                            @csrf
                            <div>
                                <label for="resolution_description" class="block text-xs font-semibold text-slate-700 mb-1">
                                    Resolution Description <span class="text-red-500">*</span>
                                </label>
                                <textarea name="resolution_description" 
                                          id="resolution_description" 
                                          rows="5" 
                                          required 
                                          minlength="10" 
                                          placeholder="Detailed findings, actions implemented, dates, and conclusions..."
                                          class="w-full px-3 py-2 text-xs rounded border border-slate-300 focus:outline-none focus:border-blue-600 leading-relaxed">{{ old('resolution_description', $complaint->resolution_description) }}</textarea>
                            </div>
                            <div class="text-right">
                                <button type="submit" class="px-4 py-2 rounded bg-slate-900 text-white text-xs font-semibold hover:bg-slate-900 transition">
                                    Submit For Admin Verification
                                </button>
                            </div>
                        </form>
                    </div>
                @elseif($complaint->resolution_description)
                    <div class="bg-white border border-slate-200 rounded p-6">
                        <h3 class="text-sm font-bold text-slate-900 mb-2">Submitted Resolution</h3>
                        <div class="text-xs text-slate-700 leading-relaxed bg-slate-50 p-4 rounded border border-slate-200 whitespace-pre-line">
                            {{ $complaint->resolution_description }}
                        </div>
                    </div>
                @endif

                <!-- Communication Thread (Public & Internal) -->
                <div class="bg-white border border-slate-200 rounded p-6" x-data="{ visibility: 'public' }">
                    <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-200">
                        <h3 class="text-sm font-bold text-slate-900">Case Communications</h3>
                        <div class="flex items-center gap-2 text-xs">
                            <span class="text-slate-500 text-[11px]">Includes:</span>
                            <span class="px-2 py-0.5 rounded bg-blue-50 text-blue-700 border border-blue-200 font-medium">Public</span>
                            <span class="px-2 py-0.5 rounded bg-amber-50 text-amber-800 border border-amber-200 font-medium">Internal Notes</span>
                        </div>
                    </div>

                    <!-- Messages Stream -->
                    <div class="space-y-3 mb-6">
                        @forelse($complaint->comments as $c)
                            <div class="p-3.5 rounded text-xs {{ $c->visibility === 'internal' ? 'bg-amber-50/50 border border-amber-200' : ($c->sender_id === auth()->id() ? 'bg-blue-50/50 border border-blue-200' : 'bg-slate-50 border border-slate-200') }}">
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
                                                Internal Note
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
                                No messages or notes logged yet.
                            </div>
                        @endforelse
                    </div>

                    <!-- Post Communication Form -->
                    @if(! in_array($complaint->status, [\App\Models\Complaint::STATUS_CLOSED, \App\Models\Complaint::STATUS_REJECTED]))
                        <form method="POST" action="{{ route('management.complaints.comments.store', $complaint->id) }}" class="space-y-3 pt-3 border-t border-slate-200">
                            @csrf
                            <div class="flex items-center gap-4 text-xs">
                                <label class="font-semibold text-slate-700">Message Visibility:</label>
                                <label class="inline-flex items-center gap-1.5 cursor-pointer">
                                    <input type="radio" name="visibility" value="public" x-model="visibility" class="text-blue-600 focus:ring-0">
                                    <span class="text-slate-700">Public (Visible to Student)</span>
                                </label>
                                <label class="inline-flex items-center gap-1.5 cursor-pointer">
                                    <input type="radio" name="visibility" value="internal" x-model="visibility" class="text-amber-600 focus:ring-0">
                                    <span class="text-amber-800 font-medium">Internal Note (Management & Admin only)</span>
                                </label>
                            </div>

                            <div>
                                <textarea name="message" id="mgmt_message" rows="3" required 
                                          :placeholder="visibility === 'internal' ? 'Enter private investigation notes, logs or remarks (student will NOT see this)...' : 'Type public communication or request for clarification to student...'"
                                          class="w-full px-3 py-2 text-xs rounded border border-slate-300 focus:outline-none focus:border-blue-600"></textarea>
                            </div>

                            <div class="text-right">
                                <button type="submit" class="px-4 py-2 rounded bg-slate-900 text-white text-xs font-semibold hover:bg-slate-900 transition">
                                    Post Communication
                                </button>
                            </div>
                        </form>
                    @endif
                </div>
            </div>

            <!-- Right 1 Col: Student Profile, Meta, Timeline -->
            <div class="space-y-6">
                <!-- Submitting Student Profile -->
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
                                {{ $complaint->student?->department?->code ?? 'N/A' }} &bull; {{ $complaint->student?->study_year }} Year
                            </dd>
                        </div>
                    </dl>
                </div>

                <!-- Assignment & Metadata -->
                <div class="bg-white border border-slate-200 rounded p-6">
                    <h3 class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-4">Routing Details</h3>
                    <dl class="space-y-3 text-xs">
                        <div>
                            <dt class="text-slate-400 font-medium">Department</dt>
                            <dd class="text-slate-800 font-semibold mt-0.5">{{ $complaint->department?->name }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-400 font-medium">Category</dt>
                            <dd class="text-slate-800 font-semibold mt-0.5">{{ $complaint->category?->name }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-400 font-medium">Submitted Date</dt>
                            <dd class="text-slate-800 mt-0.5">{{ $complaint->created_at->format('d M Y, H:i') }}</dd>
                        </div>
                    </dl>
                </div>

                <!-- Status History Timeline -->
                <div class="bg-white border border-slate-200 rounded p-6">
                    <h3 class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-4">Lifecycle Timeline</h3>
                    <div class="relative pl-6 space-y-4 border-l border-slate-200 text-xs">
                        @foreach($complaint->statusHistory as $history)
                            <div class="relative">
                                <div class="absolute -left-[31px] top-0.5 w-2.5 h-2.5 rounded-full bg-blue-600 border-2 border-white"></div>
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
    </div>
</x-layouts.app>
