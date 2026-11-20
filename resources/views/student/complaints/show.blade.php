<x-layouts.app :title="'Complaint ' . $complaint->complaint_number">
    <div class="max-w-5xl mx-auto space-y-6">
        <!-- Back Navigation & Status Bar -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <a href="{{ route('student.complaints.index') }}" class="text-xs font-medium text-slate-500 hover:text-slate-800 flex items-center gap-1 mb-1">
                    &larr; Back to My Complaints
                </a>
                <div class="flex items-center gap-3">
                    <h1 class="text-xl font-bold tracking-tight text-slate-900">{{ $complaint->complaint_number }}</h1>
                    <x-status-badge :status="$complaint->status" />
                    <x-priority-badge :priority="$complaint->priority" />
                </div>
            </div>

            <!-- Reopen Button if Closed -->
            @if($complaint->status === \App\Models\Complaint::STATUS_CLOSED)
                <button type="button" 
                        @click="$dispatch('open-reopen-modal')"
                        class="px-4 py-2 rounded bg-amber-600 text-white text-xs font-semibold hover:bg-amber-700 transition">
                    Request Complaint Reopening
                </button>
            @endif
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left 2 Cols: Details, Conversation, Resolution -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Subject and Description Box -->
                <div class="bg-white border border-slate-200 rounded p-6">
                    <h2 class="text-base font-bold text-slate-900 mb-3">{{ $complaint->subject }}</h2>
                    <div class="text-xs text-slate-700 leading-relaxed whitespace-pre-line bg-slate-50 p-4 rounded border border-slate-200 mb-4">
                        {{ $complaint->description }}
                    </div>

                    <!-- Uploaded Attachments -->
                    <div>
                        <div class="text-xs font-semibold text-slate-700 mb-2">Supporting Attachments ({{ $complaint->attachments->count() }})</div>
                        @if($complaint->attachments->isNotEmpty())
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                @foreach($complaint->attachments as $att)
                                    <a href="{{ route('student.attachments.download', $att->id) }}" class="flex items-center gap-2 p-2.5 rounded border border-slate-200 bg-white hover:bg-slate-50 text-xs transition">
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
                            <div class="text-xs text-slate-400 italic">No attachments uploaded with this complaint.</div>
                        @endif
                    </div>
                </div>

                <!-- Official Resolution Box (When Submitted or Closed) -->
                @if($complaint->resolution_description)
                    <div class="bg-white border border-slate-200 rounded p-6">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="text-sm font-semibold text-slate-900 flex items-center gap-2">
                                <i data-lucide="check-circle" class="w-4 h-4 text-emerald-600"></i>
                                <span>Official Investigation Resolution</span>
                            </h3>
                            @if($complaint->resolution_submitted_at)
                                <span class="text-[11px] text-slate-400">
                                    Submitted {{ $complaint->resolution_submitted_at->format('d M Y, H:i') }}
                                </span>
                            @endif
                        </div>
                        <div class="text-xs text-slate-700 leading-relaxed bg-emerald-50/50 p-4 rounded border border-emerald-200 whitespace-pre-line">
                            {{ $complaint->resolution_description }}
                        </div>
                    </div>
                @endif

                <!-- Discussion & Comments -->
                <div class="bg-white border border-slate-200 rounded p-6">
                    <h3 class="text-sm font-semibold text-slate-900 mb-4">Official Communication Thread</h3>

                    <!-- Message History -->
                    <div class="space-y-3 mb-6">
                        @forelse($complaint->publicComments as $comment)
                            <div class="p-3.5 rounded text-xs {{ $comment->sender_id === auth()->id() ? 'bg-blue-50/60 border border-blue-200 ml-4' : 'bg-slate-50 border border-slate-200 mr-4' }}">
                                <div class="flex items-center justify-between mb-1.5">
                                    <span class="font-semibold {{ $comment->sender_id === auth()->id() ? 'text-blue-600' : 'text-slate-800' }}">
                                        {{ $comment->sender?->full_name ?? 'User' }}
                                        <span class="text-[10px] font-normal text-slate-500 capitalize">
                                            ({{ $comment->sender?->role === 'management' ? 'Management' : ucfirst($comment->sender?->role ?? '') }})
                                        </span>
                                    </span>
                                    <span class="text-[10px] text-slate-400">
                                        {{ $comment->created_at->format('d M Y, H:i') }}
                                    </span>
                                </div>
                                <div class="text-slate-700 leading-relaxed whitespace-pre-line">
                                    {{ $comment->message }}
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-6 text-slate-400 text-xs">
                                No public responses or inquiries posted yet.
                            </div>
                        @endforelse
                    </div>

                    <!-- Post Reply Form -->
                    @if(! in_array($complaint->status, [\App\Models\Complaint::STATUS_CLOSED, \App\Models\Complaint::STATUS_REJECTED]))
                        <form method="POST" action="{{ route('student.complaints.comments.store', $complaint->id) }}" class="space-y-3">
                            @csrf
                            <div>
                                <label for="message" class="block text-xs font-semibold text-slate-700 mb-1">
                                    Send message or reply to Management
                                </label>
                                <textarea name="message" id="message" rows="3" required placeholder="Type your reply or question regarding this complaint..."
                                          class="w-full px-3 py-2 text-xs rounded border border-slate-300 focus:outline-none focus:border-blue-600"></textarea>
                            </div>
                            <div class="text-right">
                                <button type="submit" class="px-4 py-2 rounded bg-blue-600 text-white text-xs font-semibold hover:bg-blue-700 transition">
                                    Post Message
                                </button>
                            </div>
                        </form>
                    @else
                        <div class="p-3 rounded bg-slate-100 text-slate-600 text-xs text-center">
                            This complaint has been {{ strtolower($complaint->status) }}. New messages cannot be posted.
                        </div>
                    @endif
                </div>
            </div>

            <!-- Right 1 Col: Complaint Metadata & Timeline -->
            <div class="space-y-6">
                <!-- Overview Info Card -->
                <div class="bg-white border border-slate-200 rounded p-6">
                    <h3 class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-4">Complaint Metadata</h3>

                    <dl class="space-y-3 text-xs">
                        <div>
                            <dt class="text-slate-400 font-medium">Department</dt>
                            <dd class="text-slate-800 font-semibold mt-0.5">{{ $complaint->department?->name ?? 'N/A' }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-400 font-medium">Category</dt>
                            <dd class="text-slate-800 font-semibold mt-0.5">{{ $complaint->category?->name ?? 'N/A' }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-400 font-medium">Submitted On</dt>
                            <dd class="text-slate-800 mt-0.5">{{ $complaint->created_at->format('d M Y, H:i') }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-400 font-medium">Assigned Management</dt>
                            <dd class="text-slate-800 mt-0.5">
                                @if($complaint->activeAssignment)
                                    <span class="font-semibold text-slate-700">
                                        {{ $complaint->activeAssignment->management?->full_name ?? 'Assigned' }}
                                    </span>
                                @else
                                    <span class="text-slate-400 italic">Pending assignment</span>
                                @endif
                            </dd>
                        </div>
                        @if($complaint->closed_at)
                            <div>
                                <dt class="text-slate-400 font-medium">Closed On</dt>
                                <dd class="text-slate-800 mt-0.5">{{ $complaint->closed_at->format('d M Y, H:i') }}</dd>
                            </div>
                        @endif
                    </dl>
                </div>

                <!-- Status Timeline -->
                <div class="bg-white border border-slate-200 rounded p-6">
                    <h3 class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-4">Status Timeline</h3>

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

        <!-- Student Reopen Modal -->
        <div x-data="{ open: false }" 
             @open-reopen-modal.window="open = true" 
             x-show="open" 
             class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/50 flex items-center justify-center p-4" 
             style="display: none;">
            <div class="bg-white rounded border border-slate-200 max-w-md w-full p-6" @click.outside="open = false">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-bold text-slate-900">Request Complaint Reopening</h3>
                    <button @click="open = false" class="text-slate-400 hover:text-slate-600">&times;</button>
                </div>
                <p class="text-xs text-slate-600 mb-4 leading-relaxed">
                    If the issue has not been satisfactorily resolved, please explain the remaining deficiency. Administration will review your reopening request.
                </p>
                <form method="POST" action="{{ route('student.complaints.reopen', $complaint->id) }}" class="space-y-4">
                    @csrf
                    <div>
                        <label for="reopen_reason" class="block text-xs font-semibold text-slate-700 mb-1">
                            Reason for Reopening <span class="text-red-500">*</span>
                        </label>
                        <textarea name="reason" id="reopen_reason" rows="4" required minlength="10" placeholder="Specify why the issue is still unresolved..."
                                  class="w-full px-3 py-2 text-xs rounded border border-slate-300 focus:outline-none focus:border-blue-600"></textarea>
                    </div>
                    <div class="flex items-center justify-end gap-2">
                        <button type="button" @click="open = false" class="px-3 py-1.5 rounded border border-slate-300 text-xs font-medium text-slate-600 hover:bg-slate-50">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-1.5 rounded bg-amber-600 text-white text-xs font-semibold hover:bg-amber-700">
                            Submit Reopen Request
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app>
