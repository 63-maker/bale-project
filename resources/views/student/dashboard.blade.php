<x-layouts.app :title="'Student Dashboard'">
    <div class="space-y-6">
        <!-- 1. DETAILS ACCORDING TO USER INPUT -->
        <div class="bg-white border border-slate-200 rounded p-6 shadow-xs">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 mb-4 border-b border-slate-200">
                <div>
                    <h1 class="text-xl font-bold tracking-tight text-slate-900">
                        Welcome, {{ $student->full_name }}
                    </h1>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Authenticated Student Profile &bull; Verified via Register No: <span class="font-mono font-semibold text-slate-800">{{ $student->register_number }}</span>
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center px-2.5 py-1 rounded text-xs font-semibold bg-slate-100 text-slate-800 border border-slate-200">
                        Account Active
                    </span>
                    <a href="{{ route('profile') }}" class="px-3 py-1.5 rounded border border-slate-300 bg-white text-slate-700 text-xs font-medium hover:bg-slate-50 transition flex items-center gap-1.5">
                        <i data-lucide="user" class="w-3.5 h-3.5 text-slate-500"></i>
                        <span>Edit Profile</span>
                    </a>
                </div>
            </div>

            <!-- Profile Details Grid -->
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">
                <div class="bg-slate-50/80 p-3 rounded border border-slate-200 flex flex-col justify-between">
                    <span class="text-[10px] font-semibold text-slate-500 uppercase tracking-wider">Register Number</span>
                    <span class="text-xs font-bold font-mono text-blue-700 mt-1 truncate">{{ $student->register_number }}</span>
                </div>

                <div class="bg-slate-50/80 p-3 rounded border border-slate-200 flex flex-col justify-between">
                    <span class="text-[10px] font-semibold text-slate-500 uppercase tracking-wider">Full Name</span>
                    <span class="text-xs font-bold text-slate-900 mt-1 truncate">{{ $student->full_name }}</span>
                </div>

                <div class="bg-slate-50/80 p-3 rounded border border-slate-200 flex flex-col justify-between">
                    <span class="text-[10px] font-semibold text-slate-500 uppercase tracking-wider">College Email</span>
                    <span class="text-xs font-medium text-slate-700 mt-1 truncate" title="{{ $student->email }}">{{ $student->email }}</span>
                </div>

                <div class="bg-slate-50/80 p-3 rounded border border-slate-200 flex flex-col justify-between">
                    <span class="text-[10px] font-semibold text-slate-500 uppercase tracking-wider">Department</span>
                    <span class="text-xs font-bold text-slate-900 mt-1 truncate">{{ $student->department?->code ?? 'N/A' }} - {{ $student->department?->name ?? 'General' }}</span>
                </div>

                <div class="bg-slate-50/80 p-3 rounded border border-slate-200 flex flex-col justify-between">
                    <span class="text-[10px] font-semibold text-slate-500 uppercase tracking-wider">Year of Study</span>
                    <span class="text-xs font-bold text-slate-900 mt-1 truncate">{{ $student->study_year }} Year Undergraduate</span>
                </div>

                <div class="bg-slate-50/80 p-3 rounded border border-slate-200 flex flex-col justify-between">
                    <span class="text-[10px] font-semibold text-slate-500 uppercase tracking-wider">Portal Role</span>
                    <span class="text-xs font-bold text-slate-900 mt-1 capitalize">{{ $student->role }}</span>
                </div>
            </div>
        </div>

        <!-- 2. AUTHORIZED STUDENT FUNCTIONS -->
        <div class="bg-white border border-slate-200 rounded p-6 shadow-xs">
            <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-200">
                <div>
                    <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Authorized Student Functions</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Operational tools and actions available for your student account</p>
                </div>
                <span class="text-xs text-slate-400 font-medium">6 Functions Available</span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <!-- Function 1: Submit New Complaint -->
                <a href="{{ route('student.complaints.create') }}" class="group p-5 rounded border border-slate-200 bg-white hover:border-blue-600 hover:shadow-xs transition flex flex-col justify-between">
                    <div>
                        <div class="w-8 h-8 rounded bg-blue-600 text-white flex items-center justify-center mb-3">
                            <i data-lucide="plus" class="w-4 h-4"></i>
                        </div>
                        <h3 class="text-xs font-bold text-slate-900 group-hover:text-blue-600 transition">Submit New Complaint</h3>
                        <p class="text-[11px] text-slate-600 mt-1 leading-relaxed">
                            File a new institutional grievance with category, description, and supporting evidence.
                        </p>
                    </div>
                    <div class="mt-4 pt-3 border-t border-slate-100 flex items-center text-xs font-semibold text-blue-600">
                        <span>File Complaint</span>
                        <i data-lucide="arrow-right" class="w-3.5 h-3.5 ml-1 group-hover:translate-x-0.5 transition-transform"></i>
                    </div>
                </a>

                <!-- Function 2: Track Complaints -->
                <a href="{{ route('student.complaints.index') }}" class="group p-5 rounded border border-slate-200 bg-white hover:border-blue-600 hover:shadow-xs transition flex flex-col justify-between">
                    <div>
                        <div class="w-8 h-8 rounded bg-slate-900 text-white flex items-center justify-center mb-3">
                            <i data-lucide="list" class="w-4 h-4"></i>
                        </div>
                        <h3 class="text-xs font-bold text-slate-900 group-hover:text-blue-600 transition">My Complaints Queue</h3>
                        <p class="text-[11px] text-slate-600 mt-1 leading-relaxed">
                            Track the real-time status, department assignment, and investigation history of all your filings.
                        </p>
                    </div>
                    <div class="mt-4 pt-3 border-t border-slate-100 flex items-center text-xs font-semibold text-slate-800 group-hover:text-blue-600">
                        <span>View All ({{ $totalComplaints }})</span>
                        <i data-lucide="arrow-right" class="w-3.5 h-3.5 ml-1 group-hover:translate-x-0.5 transition-transform"></i>
                    </div>
                </a>

                <!-- Function 3: Officer Discussions -->
                <a href="{{ route('student.complaints.index') }}" class="group p-5 rounded border border-slate-200 bg-white hover:border-blue-600 hover:shadow-xs transition flex flex-col justify-between">
                    <div>
                        <div class="w-8 h-8 rounded bg-slate-900 text-white flex items-center justify-center mb-3">
                            <i data-lucide="message-square" class="w-4 h-4"></i>
                        </div>
                        <h3 class="text-xs font-bold text-slate-900 group-hover:text-blue-600 transition">Officer Discussions</h3>
                        <p class="text-[11px] text-slate-600 mt-1 leading-relaxed">
                            Participate in case inquiry discussions and review responses from investigating Management officers.
                        </p>
                    </div>
                    <div class="mt-4 pt-3 border-t border-slate-100 flex items-center text-xs font-semibold text-slate-800 group-hover:text-blue-600">
                        <span>Open Discussions</span>
                        <i data-lucide="arrow-right" class="w-3.5 h-3.5 ml-1 group-hover:translate-x-0.5 transition-transform"></i>
                    </div>
                </a>

                <!-- Function 4: Reopen Complaints -->
                <a href="{{ route('student.complaints.index', ['status' => 'CLOSED']) }}" class="group p-5 rounded border border-slate-200 bg-white hover:border-blue-600 hover:shadow-xs transition flex flex-col justify-between">
                    <div>
                        <div class="w-8 h-8 rounded bg-slate-900 text-white flex items-center justify-center mb-3">
                            <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                        </div>
                        <h3 class="text-xs font-bold text-slate-900 group-hover:text-blue-600 transition">Request Reopening</h3>
                        <p class="text-[11px] text-slate-600 mt-1 leading-relaxed">
                            Request reopened investigation if a grievance resolution was unsatisfactory.
                        </p>
                    </div>
                    <div class="mt-4 pt-3 border-t border-slate-100 flex items-center text-xs font-semibold text-slate-800 group-hover:text-blue-600">
                        <span>Review Closed Cases</span>
                        <i data-lucide="arrow-right" class="w-3.5 h-3.5 ml-1 group-hover:translate-x-0.5 transition-transform"></i>
                    </div>
                </a>

                <!-- Function 5: Notifications & Updates -->
                <a href="{{ route('student.notifications.index') }}" class="group p-5 rounded border border-slate-200 bg-white hover:border-blue-600 hover:shadow-xs transition flex flex-col justify-between">
                    <div>
                        <div class="w-8 h-8 rounded bg-slate-900 text-white flex items-center justify-center mb-3">
                            <i data-lucide="bell" class="w-4 h-4"></i>
                        </div>
                        <h3 class="text-xs font-bold text-slate-900 group-hover:text-blue-600 transition">Notifications & Alerts</h3>
                        <p class="text-[11px] text-slate-600 mt-1 leading-relaxed">
                            Check real-time status updates, official resolution remarks, and institutional announcements.
                        </p>
                    </div>
                    <div class="mt-4 pt-3 border-t border-slate-100 flex items-center text-xs font-semibold text-slate-800 group-hover:text-blue-600">
                        <span>Alerts ({{ $unreadNotifications }} unread)</span>
                        <i data-lucide="arrow-right" class="w-3.5 h-3.5 ml-1 group-hover:translate-x-0.5 transition-transform"></i>
                    </div>
                </a>

                <!-- Function 6: Profile & Password -->
                <a href="{{ route('profile') }}" class="group p-5 rounded border border-slate-200 bg-white hover:border-blue-600 hover:shadow-xs transition flex flex-col justify-between">
                    <div>
                        <div class="w-8 h-8 rounded bg-slate-900 text-white flex items-center justify-center mb-3">
                            <i data-lucide="shield" class="w-4 h-4"></i>
                        </div>
                        <h3 class="text-xs font-bold text-slate-900 group-hover:text-blue-600 transition">Account & Security</h3>
                        <p class="text-[11px] text-slate-600 mt-1 leading-relaxed">
                            Manage permitted profile details, review session security, and update your account password.
                        </p>
                    </div>
                    <div class="mt-4 pt-3 border-t border-slate-100 flex items-center text-xs font-semibold text-slate-800 group-hover:text-blue-600">
                        <span>Security Settings</span>
                        <i data-lucide="arrow-right" class="w-3.5 h-3.5 ml-1 group-hover:translate-x-0.5 transition-transform"></i>
                    </div>
                </a>
            </div>
        </div>

        <!-- 3. LIVE CASE METRICS -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white border border-slate-200 rounded p-5 shadow-xs">
                <div class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Total Complaints</div>
                <div class="text-2xl font-bold text-slate-900 mt-1">{{ $totalComplaints }}</div>
                <div class="text-[11px] text-slate-400 mt-1">All historical filings</div>
            </div>

            <div class="bg-white border border-slate-200 rounded p-5 shadow-xs">
                <div class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Pending Assignment</div>
                <div class="text-2xl font-bold text-slate-900 mt-1">{{ $pendingComplaints }}</div>
                <div class="text-[11px] text-slate-400 mt-1">Submitted or awaiting review</div>
            </div>

            <div class="bg-white border border-slate-200 rounded p-5 shadow-xs">
                <div class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Under Investigation</div>
                <div class="text-2xl font-bold text-blue-600 mt-1">{{ $inProgressComplaints }}</div>
                <div class="text-[11px] text-slate-400 mt-1">Active investigation by Management</div>
            </div>

            <div class="bg-white border border-slate-200 rounded p-5 shadow-xs">
                <div class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Resolved & Closed</div>
                <div class="text-2xl font-bold text-slate-900 mt-1">{{ $resolvedComplaints }}</div>
                <div class="text-[11px] text-slate-400 mt-1">Officially verified & closed</div>
            </div>
        </div>

        <!-- 4. RECENT COMPLAINTS TABLE -->
        <div class="bg-white border border-slate-200 rounded shadow-xs overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between bg-white">
                <div>
                    <h2 class="text-sm font-semibold text-slate-900">Recent Complaints</h2>
                    <p class="text-xs text-slate-500">Track your most recently submitted grievance filings</p>
                </div>
                <a href="{{ route('student.complaints.index') }}" class="text-xs font-semibold text-blue-600 hover:text-blue-700 hover:underline">
                    View All ({{ $totalComplaints }}) &rarr;
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-600 font-semibold uppercase text-[10px] tracking-wider">
                        <tr>
                            <th class="px-6 py-3">Complaint #</th>
                            <th class="px-6 py-3">Subject</th>
                            <th class="px-6 py-3">Category</th>
                            <th class="px-6 py-3">Department</th>
                            <th class="px-6 py-3">Priority</th>
                            <th class="px-6 py-3">Status</th>
                            <th class="px-6 py-3">Date</th>
                            <th class="px-6 py-3 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($recentComplaints as $c)
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="px-6 py-3.5 font-semibold text-slate-900 whitespace-nowrap font-mono text-[11px]">
                                    {{ $c->complaint_number }}
                                </td>
                                <td class="px-6 py-3.5 max-w-xs truncate text-slate-800 font-medium">
                                    {{ $c->subject }}
                                </td>
                                <td class="px-6 py-3.5 text-slate-600 whitespace-nowrap">
                                    {{ $c->category?->name ?? 'General' }}
                                </td>
                                <td class="px-6 py-3.5 text-slate-600 whitespace-nowrap">
                                    {{ $c->department?->code ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-3.5 whitespace-nowrap">
                                    <x-priority-badge :priority="$c->priority" />
                                </td>
                                <td class="px-6 py-3.5 whitespace-nowrap">
                                    <x-status-badge :status="$c->status" />
                                </td>
                                <td class="px-6 py-3.5 text-slate-500 whitespace-nowrap font-mono text-[11px]">
                                    {{ $c->created_at->format('d M Y') }}
                                </td>
                                <td class="px-6 py-3.5 text-right whitespace-nowrap">
                                    <a href="{{ route('student.complaints.show', $c->id) }}" class="text-blue-600 hover:text-blue-700 hover:underline font-semibold">
                                        View Details &rarr;
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-8 text-center text-slate-400">
                                    No complaints filed yet. Click "Submit New Complaint" above to register your first grievance.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts.app>
