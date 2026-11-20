<x-layouts.app :title="'Notifications'">
    <div class="max-w-4xl mx-auto space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl font-bold tracking-tight text-[#172B4D]">Notifications</h1>
                <p class="text-xs text-slate-500">In-app notifications on complaint milestones and updates</p>
            </div>
            @if($notifications->where('is_read', false)->count() > 0)
                <form method="POST" action="{{ route('student.notifications.read-all') }}">
                    @csrf
                    <button type="submit" class="px-3.5 py-1.5 rounded border border-slate-300 bg-white text-xs font-semibold text-slate-700 hover:bg-slate-50 transition">
                        Mark All as Read
                    </button>
                </form>
            @endif
        </div>

        <div class="bg-white border border-slate-200 rounded divide-y divide-slate-200">
            @forelse($notifications as $notif)
                <div class="p-4 sm:p-5 flex items-start justify-between gap-4 {{ $notif->is_read ? 'bg-white' : 'bg-blue-50/40' }} transition">
                    <div class="flex items-start gap-3 min-w-0">
                        <div class="w-8 h-8 rounded-full {{ $notif->is_read ? 'bg-slate-100 text-slate-500' : 'bg-blue-100 text-[#2563EB]' }} flex items-center justify-center shrink-0 mt-0.5">
                            <i data-lucide="bell" class="w-4 h-4"></i>
                        </div>
                        <div class="min-w-0 flex-1">
                            <h3 class="text-xs font-semibold {{ $notif->is_read ? 'text-slate-800' : 'text-[#172B4D]' }}">
                                {{ $notif->title }}
                            </h3>
                            <p class="text-xs text-slate-600 mt-1 leading-relaxed">
                                {{ $notif->message }}
                            </p>
                            <div class="flex items-center gap-4 mt-2 text-[11px] text-slate-400">
                                <span>{{ $notif->created_at->diffForHumans() }} ({{ $notif->created_at->format('d M, H:i') }})</span>
                                @if($notif->complaint_id)
                                    <a href="{{ route('student.complaints.show', $notif->complaint_id) }}" class="text-[#2563EB] hover:underline font-medium">
                                        View Complaint &rarr;
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>

                    @if(! $notif->is_read)
                        <form method="POST" action="{{ route('student.notifications.read', $notif->id) }}" class="shrink-0">
                            @csrf
                            @method('PATCH')
                            <button type="submit" title="Mark as read" class="p-1 rounded text-slate-400 hover:text-slate-700 hover:bg-slate-100">
                                <i data-lucide="check" class="w-4 h-4"></i>
                            </button>
                        </form>
                    @endif
                </div>
            @empty
                <div class="py-12 text-center text-slate-400 text-xs">
                    <i data-lucide="bell-off" class="w-8 h-8 text-slate-300 mx-auto mb-2"></i>
                    <p class="font-medium text-slate-600">No notifications yet.</p>
                </div>
            @endforelse
        </div>

        @if($notifications->hasPages())
            <div class="mt-4">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>
</x-layouts.app>
