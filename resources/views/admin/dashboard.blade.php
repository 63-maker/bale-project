<x-layouts.app :title="'Administrator Dashboard'">
    <div class="space-y-6">
        <!-- 1. DETAILS ACCORDING TO USER INPUT -->
        <div class="bg-white border border-slate-200 rounded p-6 shadow-xs">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 mb-4 border-b border-slate-200">
                <div>
                    <h1 class="text-xl font-bold tracking-tight text-slate-900">
                        {{ $admin->full_name }}
                    </h1>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Authenticated System Administrator Profile &bull; Identifier: <span class="font-mono font-semibold text-slate-800">{{ $admin->email }}</span>
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center px-2.5 py-1 rounded text-xs font-semibold bg-slate-100 text-slate-800 border border-slate-200">
                        Supervision Active
                    </span>
                    <a href="{{ route('admin.settings.index') }}" class="px-3 py-1.5 rounded border border-slate-300 bg-white text-slate-700 text-xs font-medium hover:bg-slate-50 transition flex items-center gap-1.5">
                        <i data-lucide="settings" class="w-3.5 h-3.5 text-slate-500"></i>
                        <span>System Settings</span>
                    </a>
                </div>
            </div>

            <!-- Profile Details Grid -->
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">
                <div class="bg-slate-50/80 p-3 rounded border border-slate-200 flex flex-col justify-between">
                    <span class="text-[10px] font-semibold text-slate-500 uppercase tracking-wider">Administrator ID</span>
                    <span class="text-xs font-bold font-mono text-slate-900 mt-1 truncate" title="{{ $admin->email }}">{{ $admin->email }}</span>
                </div>

                <div class="bg-slate-50/80 p-3 rounded border border-slate-200 flex flex-col justify-between">
                    <span class="text-[10px] font-semibold text-slate-500 uppercase tracking-wider">Full Name</span>
                    <span class="text-xs font-bold text-slate-900 mt-1 truncate">{{ $admin->full_name }}</span>
                </div>

                <div class="bg-slate-50/80 p-3 rounded border border-slate-200 flex flex-col justify-between">
                    <span class="text-[10px] font-semibold text-slate-500 uppercase tracking-wider">Operational Dept</span>
                    <span class="text-xs font-bold text-slate-900 mt-1 truncate">{{ $admin->department?->name ?? 'Central Administration' }}</span>
                </div>

                <div class="bg-slate-50/80 p-3 rounded border border-slate-200 flex flex-col justify-between">
                    <span class="text-[10px] font-semibold text-slate-500 uppercase tracking-wider">Supervision Scope</span>
                    <span class="text-xs font-bold text-blue-700 mt-1 truncate">All College Departments</span>
                </div>

                <div class="bg-slate-50/80 p-3 rounded border border-slate-200 flex flex-col justify-between">
                    <span class="text-[10px] font-semibold text-slate-500 uppercase tracking-wider">Security Clearance</span>
                    <span class="text-xs font-bold text-slate-900 mt-1 truncate">Level 1 - Master Admin</span>
                </div>

                <div class="bg-slate-50/80 p-3 rounded border border-slate-200 flex flex-col justify-between">
                    <span class="text-[10px] font-semibold text-slate-500 uppercase tracking-wider">Portal Role</span>
                    <span class="text-xs font-bold text-slate-900 mt-1 capitalize">{{ $admin->role }}</span>
                </div>
            </div>
        </div>

        <!-- 2. AUTHORIZED ADMINISTRATIVE FUNCTIONS -->
        <div class="bg-white border border-slate-200 rounded p-6 shadow-xs">
            <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-200">
                <div>
                    <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Authorized Administrative Functions</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Central supervision, verification, assignment, configuration, and audit controls</p>
                </div>
                <span class="text-xs text-slate-400 font-medium">8 Functions Available</span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Function 1: Verification Queue -->
                <a href="{{ route('admin.verification-queue') }}" class="group p-5 rounded border border-slate-200 bg-white hover:border-blue-600 hover:shadow-xs transition flex flex-col justify-between">
                    <div>
                        <div class="w-8 h-8 rounded bg-slate-900 text-white flex items-center justify-center mb-3">
                            <i data-lucide="shield-check" class="w-4 h-4"></i>
                        </div>
                        <h3 class="text-xs font-bold text-slate-900 group-hover:text-blue-600 transition">Verification Queue</h3>
                        <p class="text-[11px] text-slate-600 mt-1 leading-relaxed">
                            Review Management resolutions, approve verified closures, or reject with mandatory remarks.
                        </p>
                    </div>
                    <div class="mt-4 pt-3 border-t border-slate-100 flex items-center text-xs font-semibold text-slate-800 group-hover:text-blue-600">
                        <span>Queue ({{ $verificationQueue->count() }} pending)</span>
                        <i data-lucide="arrow-right" class="w-3.5 h-3.5 ml-1 group-hover:translate-x-0.5 transition-transform"></i>
                    </div>
                </a>

                <!-- Function 2: Complaint Assignment -->
                <a href="{{ route('admin.complaints.index') }}" class="group p-5 rounded border border-slate-200 bg-white hover:border-blue-600 hover:shadow-xs transition flex flex-col justify-between">
                    <div>
                        <div class="w-8 h-8 rounded bg-blue-600 text-white flex items-center justify-center mb-3">
                            <i data-lucide="shuffle" class="w-4 h-4"></i>
                        </div>
                        <h3 class="text-xs font-bold text-slate-900 group-hover:text-blue-600 transition">Complaint Assignment</h3>
                        <p class="text-[11px] text-slate-600 mt-1 leading-relaxed">
                            Manually assign complaints to Management or trigger automated lowest-workload routing.
                        </p>
                    </div>
                    <div class="mt-4 pt-3 border-t border-slate-100 flex items-center text-xs font-semibold text-blue-600">
                        <span>All Complaints ({{ $analytics['total'] }})</span>
                        <i data-lucide="arrow-right" class="w-3.5 h-3.5 ml-1 group-hover:translate-x-0.5 transition-transform"></i>
                    </div>
                </a>

                <!-- Function 3: Management Personnel -->
                <a href="{{ route('admin.management.index') }}" class="group p-5 rounded border border-slate-200 bg-white hover:border-blue-600 hover:shadow-xs transition flex flex-col justify-between">
                    <div>
                        <div class="w-8 h-8 rounded bg-slate-900 text-white flex items-center justify-center mb-3">
                            <i data-lucide="users" class="w-4 h-4"></i>
                        </div>
                        <h3 class="text-xs font-bold text-slate-900 group-hover:text-blue-600 transition">Management Accounts</h3>
                        <p class="text-[11px] text-slate-600 mt-1 leading-relaxed">
                            Provision Management officer accounts, assign department roles, and toggle access statuses.
                        </p>
                    </div>
                    <div class="mt-4 pt-3 border-t border-slate-100 flex items-center text-xs font-semibold text-slate-800 group-hover:text-blue-600">
                        <span>Manage Personnel</span>
                        <i data-lucide="arrow-right" class="w-3.5 h-3.5 ml-1 group-hover:translate-x-0.5 transition-transform"></i>
                    </div>
                </a>

                <!-- Function 4: Student Directory -->
                <a href="{{ route('admin.students.index') }}" class="group p-5 rounded border border-slate-200 bg-white hover:border-blue-600 hover:shadow-xs transition flex flex-col justify-between">
                    <div>
                        <div class="w-8 h-8 rounded bg-slate-900 text-white flex items-center justify-center mb-3">
                            <i data-lucide="graduation-cap" class="w-4 h-4"></i>
                        </div>
                        <h3 class="text-xs font-bold text-slate-900 group-hover:text-blue-600 transition">Student Directory</h3>
                        <p class="text-[11px] text-slate-600 mt-1 leading-relaxed">
                            Inspect enrolled student accounts, verify register numbers, and manage account statuses.
                        </p>
                    </div>
                    <div class="mt-4 pt-3 border-t border-slate-100 flex items-center text-xs font-semibold text-slate-800 group-hover:text-blue-600">
                        <span>Student Records</span>
                        <i data-lucide="arrow-right" class="w-3.5 h-3.5 ml-1 group-hover:translate-x-0.5 transition-transform"></i>
                    </div>
                </a>

                <!-- Function 5: Departments & Categories -->
                <a href="{{ route('admin.departments.index') }}" class="group p-5 rounded border border-slate-200 bg-white hover:border-blue-600 hover:shadow-xs transition flex flex-col justify-between">
                    <div>
                        <div class="w-8 h-8 rounded bg-slate-900 text-white flex items-center justify-center mb-3">
                            <i data-lucide="building" class="w-4 h-4"></i>
                        </div>
                        <h3 class="text-xs font-bold text-slate-900 group-hover:text-blue-600 transition">Departments & Categories</h3>
                        <p class="text-[11px] text-slate-600 mt-1 leading-relaxed">
                            Configure college departments, grievance categories, and standard SLA resolution targets.
                        </p>
                    </div>
                    <div class="mt-4 pt-3 border-t border-slate-100 flex items-center text-xs font-semibold text-slate-800 group-hover:text-blue-600">
                        <span>Manage Config</span>
                        <i data-lucide="arrow-right" class="w-3.5 h-3.5 ml-1 group-hover:translate-x-0.5 transition-transform"></i>
                    </div>
                </a>

                <!-- Function 6: Institutional Analytics & Reports -->
                <a href="{{ route('admin.reports.index') }}" class="group p-5 rounded border border-slate-200 bg-white hover:border-blue-600 hover:shadow-xs transition flex flex-col justify-between">
                    <div>
                        <div class="w-8 h-8 rounded bg-slate-900 text-white flex items-center justify-center mb-3">
                            <i data-lucide="file-text" class="w-4 h-4"></i>
                        </div>
                        <h3 class="text-xs font-bold text-slate-900 group-hover:text-blue-600 transition">Analytics & Reports</h3>
                        <p class="text-[11px] text-slate-600 mt-1 leading-relaxed">
                            Audit college-wide performance metrics and export formal reports in CSV and DomPDF formats.
                        </p>
                    </div>
                    <div class="mt-4 pt-3 border-t border-slate-100 flex items-center text-xs font-semibold text-slate-800 group-hover:text-blue-600">
                        <span>Export CSV / PDF</span>
                        <i data-lucide="arrow-right" class="w-3.5 h-3.5 ml-1 group-hover:translate-x-0.5 transition-transform"></i>
                    </div>
                </a>

                <!-- Function 7: Security Audit Logs -->
                <a href="{{ route('admin.audit-logs.index') }}" class="group p-5 rounded border border-slate-200 bg-white hover:border-blue-600 hover:shadow-xs transition flex flex-col justify-between">
                    <div>
                        <div class="w-8 h-8 rounded bg-slate-900 text-white flex items-center justify-center mb-3">
                            <i data-lucide="shield-alert" class="w-4 h-4"></i>
                        </div>
                        <h3 class="text-xs font-bold text-slate-900 group-hover:text-blue-600 transition">Security Audit Logs</h3>
                        <p class="text-[11px] text-slate-600 mt-1 leading-relaxed">
                            Review immutable chronological audit trail tracking user logins, status changes, and IP addresses.
                        </p>
                    </div>
                    <div class="mt-4 pt-3 border-t border-slate-100 flex items-center text-xs font-semibold text-slate-800 group-hover:text-blue-600">
                        <span>View Audit Trail</span>
                        <i data-lucide="arrow-right" class="w-3.5 h-3.5 ml-1 group-hover:translate-x-0.5 transition-transform"></i>
                    </div>
                </a>

                <!-- Function 8: System Settings -->
                <a href="{{ route('admin.settings.index') }}" class="group p-5 rounded border border-slate-200 bg-white hover:border-blue-600 hover:shadow-xs transition flex flex-col justify-between">
                    <div>
                        <div class="w-8 h-8 rounded bg-slate-900 text-white flex items-center justify-center mb-3">
                            <i data-lucide="settings" class="w-4 h-4"></i>
                        </div>
                        <h3 class="text-xs font-bold text-slate-900 group-hover:text-blue-600 transition">Institutional Settings</h3>
                        <p class="text-[11px] text-slate-600 mt-1 leading-relaxed">
                            Configure college name, automated routing toggles, email alerts, and default SLA windows.
                        </p>
                    </div>
                    <div class="mt-4 pt-3 border-t border-slate-100 flex items-center text-xs font-semibold text-slate-800 group-hover:text-blue-600">
                        <span>Configure System</span>
                        <i data-lucide="arrow-right" class="w-3.5 h-3.5 ml-1 group-hover:translate-x-0.5 transition-transform"></i>
                    </div>
                </a>
            </div>
        </div>

        <!-- 3. 4 KEY ENTERPRISE METRIC CARDS -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white border border-slate-200 rounded p-5 shadow-xs">
                <div class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Total Complaints</div>
                <div class="text-2xl font-bold text-slate-900 mt-1">{{ $analytics['total'] }}</div>
                <div class="text-[11px] text-slate-400 mt-1">Institutional filings recorded</div>
            </div>

            <div class="bg-white border border-slate-200 rounded p-5 shadow-xs">
                <div class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Open / In Progress</div>
                <div class="text-2xl font-bold text-blue-600 mt-1">{{ $analytics['pending'] }}</div>
                <div class="text-[11px] text-slate-400 mt-1">Pending resolution or review</div>
            </div>

            <div class="bg-white border border-slate-200 rounded p-5 shadow-xs">
                <div class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Resolved & Closed</div>
                <div class="text-2xl font-bold text-slate-900 mt-1">{{ $analytics['resolved'] }}</div>
                <div class="text-[11px] text-slate-400 mt-1">Successfully verified & closed</div>
            </div>

            <div class="bg-white border border-slate-200 rounded p-5 shadow-xs">
                <div class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">SLA Overdue</div>
                <div class="text-2xl font-bold text-slate-900 mt-1">{{ $analytics['overdue'] }}</div>
                <div class="text-[11px] text-slate-400 mt-1">Exceeding SLA response window</div>
            </div>
        </div>

        <!-- 4. VERIFICATION QUEUE SECTION (Awaiting Admin Action) -->
        @if($verificationQueue->isNotEmpty())
            <div class="bg-white border border-slate-200 rounded shadow-xs overflow-hidden">
                <div class="px-6 py-4 bg-slate-900 text-white flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <i data-lucide="shield-check" class="w-4 h-4 text-blue-400"></i>
                        <div>
                            <h2 class="text-sm font-bold text-white">Verification Queue Awaiting Admin Action</h2>
                            <p class="text-[11px] text-slate-300">Resolutions submitted by Management that require administrative review and verification</p>
                        </div>
                    </div>
                    <a href="{{ route('admin.verification-queue') }}" class="text-xs font-semibold text-blue-400 hover:text-white transition underline">
                        View All ({{ $verificationQueue->count() }}) &rarr;
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 border-b border-slate-200 text-slate-600 font-semibold uppercase text-[10px]">
                            <tr>
                                <th class="px-6 py-3">Complaint #</th>
                                <th class="px-6 py-3">Student</th>
                                <th class="px-6 py-3">Subject</th>
                                <th class="px-6 py-3">Department</th>
                                <th class="px-6 py-3">Assigned Management</th>
                                <th class="px-6 py-3 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($verificationQueue as $vq)
                                <tr class="hover:bg-slate-50/70">
                                    <td class="px-6 py-3 font-semibold text-slate-900 font-mono text-[11px]">{{ $vq->complaint_number }}</td>
                                    <td class="px-6 py-3 text-slate-800">{{ $vq->student?->full_name }}</td>
                                    <td class="px-6 py-3 text-slate-800 font-medium max-w-xs truncate">{{ $vq->subject }}</td>
                                    <td class="px-6 py-3 text-slate-600">{{ $vq->department?->code }}</td>
                                    <td class="px-6 py-3 text-slate-800 font-medium">{{ $vq->activeAssignment?->management?->full_name ?? 'N/A' }}</td>
                                    <td class="px-6 py-3 text-right">
                                        <a href="{{ route('admin.complaints.show', $vq->id) }}" class="px-3 py-1 rounded bg-blue-600 text-white text-xs font-semibold hover:bg-blue-700 transition">
                                            Review Resolution &rarr;
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <!-- 5. CHART ANALYTICS SECTION -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Monthly Trend Chart -->
            <div class="bg-white border border-slate-200 rounded p-6 shadow-xs">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-bold text-slate-900">Monthly Grievance Activity (6 Months)</h3>
                    <span class="text-[11px] text-slate-400">Actual DB Aggregation</span>
                </div>
                <div class="h-64">
                    <canvas id="monthlyTrendChart"></canvas>
                </div>
            </div>

            <!-- Department Distribution Chart -->
            <div class="bg-white border border-slate-200 rounded p-6 shadow-xs">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-bold text-slate-900">Complaints by Department</h3>
                    <span class="text-[11px] text-slate-400">Distribution</span>
                </div>
                <div class="h-64">
                    <canvas id="departmentChart"></canvas>
                </div>
            </div>
        </div>

        <!-- 6. MANAGEMENT WORKLOAD & RECENT COMPLAINTS GRID -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Management Workload -->
            <div class="bg-white border border-slate-200 rounded p-6 shadow-xs">
                <div class="flex items-center justify-between mb-4 pb-2 border-b border-slate-200">
                    <h3 class="text-sm font-bold text-slate-900">Management Workload</h3>
                    <a href="{{ route('admin.management.index') }}" class="text-xs text-blue-600 hover:text-blue-700 hover:underline font-semibold">Manage &rarr;</a>
                </div>
                <div class="space-y-3">
                    @forelse($analytics['management_workload']->take(6) as $mw)
                        <div class="flex items-center justify-between text-xs p-2.5 rounded bg-slate-50 border border-slate-200">
                            <div>
                                <div class="font-semibold text-slate-900">{{ $mw['name'] }}</div>
                                <div class="text-[11px] text-slate-500">{{ $mw['department'] }} ({{ $mw['employee_id'] }})</div>
                            </div>
                            <div class="text-right">
                                <span class="px-2 py-0.5 rounded text-xs font-semibold {{ $mw['active_workload'] > 0 ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'bg-slate-100 text-slate-600 border border-slate-200' }}">
                                    {{ $mw['active_workload'] }} open
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="text-xs text-slate-400 text-center py-4">No management officers provisioned.</div>
                    @endforelse
                </div>
            </div>

            <!-- Recent Complaints -->
            <div class="lg:col-span-2 bg-white border border-slate-200 rounded p-6 shadow-xs">
                <div class="flex items-center justify-between mb-4 pb-2 border-b border-slate-200">
                    <h3 class="text-sm font-bold text-slate-900">Recent Institutional Complaints</h3>
                    <a href="{{ route('admin.complaints.index') }}" class="text-xs text-blue-600 hover:text-blue-700 hover:underline font-semibold">All Complaints &rarr;</a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 text-slate-600 uppercase text-[10px] font-semibold">
                            <tr>
                                <th class="py-2.5 px-3">Complaint #</th>
                                <th class="py-2.5 px-3">Student</th>
                                <th class="py-2.5 px-3">Subject</th>
                                <th class="py-2.5 px-3">Status</th>
                                <th class="py-2.5 px-3 text-right">View</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($recentComplaints as $rc)
                                <tr class="hover:bg-slate-50/70">
                                    <td class="py-2 px-3 font-semibold text-slate-900 font-mono text-[11px] whitespace-nowrap">{{ $rc->complaint_number }}</td>
                                    <td class="py-2 px-3 text-slate-800 whitespace-nowrap">{{ $rc->student?->full_name }}</td>
                                    <td class="py-2 px-3 text-slate-800 truncate max-w-xs">{{ $rc->subject }}</td>
                                    <td class="py-2 px-3 whitespace-nowrap"><x-status-badge :status="$rc->status" /></td>
                                    <td class="py-2 px-3 text-right whitespace-nowrap">
                                        <a href="{{ route('admin.complaints.show', $rc->id) }}" class="text-blue-600 hover:text-blue-700 hover:underline font-semibold">&rarr;</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart.js Initialization -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (typeof Chart === 'undefined') return;

            // Monthly Activity Trend (Premium Black & Blue Palette)
            const trendCtx = document.getElementById('monthlyTrendChart')?.getContext('2d');
            if (trendCtx) {
                const trendData = @json($analytics['monthly_trends']);
                new Chart(trendCtx, {
                    type: 'line',
                    data: {
                        labels: trendData.map(d => d.month),
                        datasets: [
                            {
                                label: 'Submitted',
                                data: trendData.map(d => d.submitted),
                                borderColor: '#2563EB',
                                backgroundColor: '#2563EB',
                                tension: 0.1,
                                borderWidth: 2,
                            },
                            {
                                label: 'Resolved & Closed',
                                data: trendData.map(d => d.resolved),
                                borderColor: '#0F172A',
                                backgroundColor: '#0F172A',
                                tension: 0.1,
                                borderWidth: 2,
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 11 } } }
                        },
                        scales: {
                            y: { beginAtZero: true, ticks: { precision: 0 } }
                        }
                    }
                });
            }

            // Department Bar Chart (Premium Slate / Blue Palette)
            const deptCtx = document.getElementById('departmentChart')?.getContext('2d');
            if (deptCtx) {
                const deptData = @json($analytics['departments']);
                new Chart(deptCtx, {
                    type: 'bar',
                    data: {
                        labels: deptData.map(d => d.code),
                        datasets: [{
                            label: 'Complaints',
                            data: deptData.map(d => d.complaints_count),
                            backgroundColor: '#0F172A',
                            borderRadius: 2,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false }
                        },
                        scales: {
                            y: { beginAtZero: true, ticks: { precision: 0 } }
                        }
                    }
                });
            }
        });
    </script>
</x-layouts.app>
