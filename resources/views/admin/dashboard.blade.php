<x-layouts.app :title="'Administrator Dashboard'">
    <div class="space-y-6">
        <!-- Dashboard Top Banner -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white border border-slate-200 rounded p-6">
            <div>
                <h1 class="text-xl font-bold tracking-tight text-[#172B4D]">Central Administrative Supervision</h1>
                <p class="text-xs text-slate-500 mt-1">Real-time institutional oversight across all grievance categories and departments</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.verification-queue') }}" class="px-4 py-2 rounded bg-amber-600 text-white text-xs font-semibold hover:bg-amber-700 transition flex items-center gap-2 shadow-xs">
                    <i data-lucide="check-circle" class="w-4 h-4"></i>
                    <span>Verification Queue ({{ $verificationQueue->count() }})</span>
                </a>
                <a href="{{ route('admin.reports.index') }}" class="px-4 py-2 rounded bg-[#172B4D] text-white text-xs font-semibold hover:bg-slate-900 transition flex items-center gap-2">
                    <i data-lucide="bar-chart-2" class="w-4 h-4"></i>
                    <span>Full Analytics</span>
                </a>
            </div>
        </div>

        <!-- 4 Key Enterprise Metric Cards (Real Computed Data - No Fake Numbers) -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white border border-slate-200 rounded p-5">
                <div class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Total Complaints</div>
                <div class="text-2xl font-bold text-[#172B4D] mt-1">{{ $analytics['total'] }}</div>
                <div class="text-[11px] text-slate-400 mt-1">Institutional filings recorded</div>
            </div>

            <div class="bg-white border border-slate-200 rounded p-5">
                <div class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Open / In Progress</div>
                <div class="text-2xl font-bold text-blue-600 mt-1">{{ $analytics['pending'] }}</div>
                <div class="text-[11px] text-slate-400 mt-1">Pending resolution or review</div>
            </div>

            <div class="bg-white border border-slate-200 rounded p-5">
                <div class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Resolved & Closed</div>
                <div class="text-2xl font-bold text-emerald-600 mt-1">{{ $analytics['resolved'] }}</div>
                <div class="text-[11px] text-slate-400 mt-1">Successfully verified & closed</div>
            </div>

            <div class="bg-white border border-slate-200 rounded p-5">
                <div class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">SLA Overdue</div>
                <div class="text-2xl font-bold text-rose-600 mt-1">{{ $analytics['overdue'] }}</div>
                <div class="text-[11px] text-slate-400 mt-1">Exceeding SLA response window</div>
            </div>
        </div>

        <!-- Verification Queue Section -->
        @if($verificationQueue->isNotEmpty())
            <div class="bg-white border border-amber-300 rounded overflow-hidden">
                <div class="px-6 py-4 bg-amber-50/70 border-b border-amber-200 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i data-lucide="shield-alert" class="w-5 h-5 text-amber-700"></i>
                        <div>
                            <h2 class="text-sm font-bold text-amber-900">Verification Queue Awaiting Admin Action</h2>
                            <p class="text-[11px] text-amber-700">Resolutions submitted by Management that require administrative review and verification</p>
                        </div>
                    </div>
                    <a href="{{ route('admin.verification-queue') }}" class="text-xs font-bold text-amber-900 hover:underline">
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
                        <tbody class="divide-y divide-slate-200">
                            @foreach($verificationQueue as $vq)
                                <tr class="hover:bg-slate-50">
                                    <td class="px-6 py-3 font-semibold text-slate-900">{{ $vq->complaint_number }}</td>
                                    <td class="px-6 py-3 text-slate-700">{{ $vq->student?->full_name }}</td>
                                    <td class="px-6 py-3 text-slate-800 font-medium max-w-xs truncate">{{ $vq->subject }}</td>
                                    <td class="px-6 py-3 text-slate-600">{{ $vq->department?->code }}</td>
                                    <td class="px-6 py-3 text-slate-700 font-medium">{{ $vq->activeAssignment?->management?->full_name ?? 'N/A' }}</td>
                                    <td class="px-6 py-3 text-right">
                                        <a href="{{ route('admin.complaints.show', $vq->id) }}" class="px-3 py-1 rounded bg-[#2563EB] text-white text-xs font-semibold hover:bg-blue-700 transition">
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

        <!-- Chart Analytics Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Monthly Trend Chart -->
            <div class="bg-white border border-slate-200 rounded p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-bold text-[#172B4D]">Monthly Grievance Activity (6 Months)</h3>
                    <span class="text-[11px] text-slate-400">Actual DB Aggregation</span>
                </div>
                <div class="h-64">
                    <canvas id="monthlyTrendChart"></canvas>
                </div>
            </div>

            <!-- Department Distribution Chart -->
            <div class="bg-white border border-slate-200 rounded p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-bold text-[#172B4D]">Complaints by Department</h3>
                    <span class="text-[11px] text-slate-400">Distribution</span>
                </div>
                <div class="h-64">
                    <canvas id="departmentChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Management Workload & Recent Complaints Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Management Workload -->
            <div class="bg-white border border-slate-200 rounded p-6">
                <div class="flex items-center justify-between mb-4 pb-2 border-b border-slate-200">
                    <h3 class="text-sm font-bold text-[#172B4D]">Management Workload</h3>
                    <a href="{{ route('admin.management.index') }}" class="text-xs text-[#2563EB] hover:underline font-medium">Manage &rarr;</a>
                </div>
                <div class="space-y-3">
                    @forelse($analytics['management_workload']->take(6) as $mw)
                        <div class="flex items-center justify-between text-xs p-2 rounded bg-slate-50 border border-slate-200">
                            <div>
                                <div class="font-semibold text-slate-800">{{ $mw['name'] }}</div>
                                <div class="text-[11px] text-slate-400">{{ $mw['department'] }} ({{ $mw['employee_id'] }})</div>
                            </div>
                            <div class="text-right">
                                <span class="px-2 py-0.5 rounded text-xs font-bold {{ $mw['active_workload'] > 0 ? 'bg-blue-100 text-[#2563EB]' : 'bg-slate-200 text-slate-600' }}">
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
            <div class="lg:col-span-2 bg-white border border-slate-200 rounded p-6">
                <div class="flex items-center justify-between mb-4 pb-2 border-b border-slate-200">
                    <h3 class="text-sm font-bold text-[#172B4D]">Recent Institutional Complaints</h3>
                    <a href="{{ route('admin.complaints.index') }}" class="text-xs text-[#2563EB] hover:underline font-medium">All Complaints &rarr;</a>
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
                                <tr class="hover:bg-slate-50/50">
                                    <td class="py-2 px-3 font-semibold text-slate-900 whitespace-nowrap">{{ $rc->complaint_number }}</td>
                                    <td class="py-2 px-3 text-slate-700 whitespace-nowrap">{{ $rc->student?->full_name }}</td>
                                    <td class="py-2 px-3 text-slate-800 truncate max-w-xs">{{ $rc->subject }}</td>
                                    <td class="py-2 px-3 whitespace-nowrap"><x-status-badge :status="$rc->status" /></td>
                                    <td class="py-2 px-3 text-right whitespace-nowrap">
                                        <a href="{{ route('admin.complaints.show', $rc->id) }}" class="text-[#2563EB] hover:underline font-medium">&rarr;</a>
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

            // Monthly Activity Trend
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
                                borderColor: '#16A34A',
                                backgroundColor: '#16A34A',
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

            // Department Bar Chart
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
                            backgroundColor: '#172B4D',
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
