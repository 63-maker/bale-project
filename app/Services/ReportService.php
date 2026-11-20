<?php

namespace App\Services;

use App\Models\Complaint;
use App\Models\ComplaintAssignment;
use App\Models\ComplaintCategory;
use App\Models\ComplaintVerification;
use App\Models\Department;
use App\Models\Profile;
use App\Models\SystemSetting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportService
{
    /**
     * Build base query with filters applied
     */
    public function getFilteredComplaintsQuery(array $filters): Builder
    {
        $query = Complaint::with(['student', 'category', 'department', 'activeAssignment.management']);

        if (! empty($filters['start_date'])) {
            $query->whereDate('created_at', '>=', $filters['start_date']);
        }

        if (! empty($filters['end_date'])) {
            $query->whereDate('created_at', '<=', $filters['end_date']);
        }

        if (! empty($filters['department_id'])) {
            $query->where('department_id', $filters['department_id']);
        }

        if (! empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (! empty($filters['priority'])) {
            $query->where('priority', $filters['priority']);
        }

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['management_id'])) {
            $query->whereHas('assignments', function ($q) use ($filters) {
                $q->where('management_id', $filters['management_id'])->where('is_active', true);
            });
        }

        return $query->orderByDesc('created_at');
    }

    /**
     * Generate actual computed analytics summary for Admin dashboard & reports
     */
    public function getAnalyticsSummary(array $filters = []): array
    {
        $query = $this->getFilteredComplaintsQuery($filters);

        $total = (clone $query)->count();
        $pending = (clone $query)->whereIn('status', [
            Complaint::STATUS_SUBMITTED,
            Complaint::STATUS_UNDER_REVIEW,
            Complaint::STATUS_ASSIGNED,
            Complaint::STATUS_IN_PROGRESS,
            Complaint::STATUS_RESOLUTION_SUBMITTED,
            Complaint::STATUS_REOPENED,
        ])->count();

        $resolved = (clone $query)->whereIn('status', [
            Complaint::STATUS_VERIFIED_FOR_CLOSURE,
            Complaint::STATUS_CLOSED,
        ])->count();

        $rejected = (clone $query)->where('status', Complaint::STATUS_REJECTED)->count();

        // Overdue complaints based on SLA hours
        $sla = SystemSetting::get('sla_hours', [
            'LOW' => 120,
            'MEDIUM' => 72,
            'HIGH' => 48,
            'CRITICAL' => 24,
        ]);

        $openComplaints = (clone $query)->whereNotIn('status', [Complaint::STATUS_CLOSED, Complaint::STATUS_REJECTED])->get();
        $overdueCount = 0;
        $now = now();

        foreach ($openComplaints as $c) {
            $allowedHours = $sla[$c->priority] ?? 72;
            if ($c->created_at->addHours($allowedHours)->isPast()) {
                $overdueCount++;
            }
        }

        // Average resolution time (hours)
        $closedComplaints = (clone $query)->whereNotNull('closed_at')->get();
        $avgHours = 0;
        if ($closedComplaints->isNotEmpty()) {
            $totalHours = $closedComplaints->reduce(function ($carry, $item) {
                return $carry + $item->created_at->diffInHours($item->closed_at);
            }, 0);
            $avgHours = round($totalHours / $closedComplaints->count(), 1);
        }

        // Department breakdown
        $departments = Department::withCount(['complaints' => function ($q) use ($filters) {
            if (! empty($filters['start_date'])) {
                $q->whereDate('created_at', '>=', $filters['start_date']);
            }
            if (! empty($filters['end_date'])) {
                $q->whereDate('created_at', '<=', $filters['end_date']);
            }
        }])->get();

        // Category breakdown
        $categories = ComplaintCategory::withCount(['complaints' => function ($q) use ($filters) {
            if (! empty($filters['start_date'])) {
                $q->whereDate('created_at', '>=', $filters['start_date']);
            }
            if (! empty($filters['end_date'])) {
                $q->whereDate('created_at', '<=', $filters['end_date']);
            }
        }])->get();

        // Verification rejection rate
        $totalVerifications = ComplaintVerification::count();
        $rejectedVerifications = ComplaintVerification::where('decision', ComplaintVerification::DECISION_REJECTED)->count();
        $rejectionRate = $totalVerifications > 0
            ? round(($rejectedVerifications / $totalVerifications) * 100, 1)
            : 0;

        // Management workload
        $managementWorkload = Profile::where('role', Profile::ROLE_MANAGEMENT)
            ->where('account_status', Profile::STATUS_ACTIVE)
            ->with(['department'])
            ->get()
            ->map(function (Profile $m) {
                $activeCount = ComplaintAssignment::where('management_id', $m->id)
                    ->where('is_active', true)
                    ->whereHas('complaint', function ($q) {
                        $q->whereNotIn('status', [Complaint::STATUS_CLOSED, Complaint::STATUS_REJECTED]);
                    })
                    ->count();

                $closedCount = Complaint::where('closed_by', $m->id)->count();

                return [
                    'id' => $m->id,
                    'name' => $m->full_name,
                    'employee_id' => $m->employee_id,
                    'department' => $m->department?->name ?? 'N/A',
                    'active_workload' => $activeCount,
                    'resolved_count' => $closedCount,
                ];
            });

        // Monthly trends for last 6 months
        $monthlyTrends = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $monthKey = $month->format('M Y');
            $count = Complaint::whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->count();
            $resolvedMonthly = Complaint::whereYear('closed_at', $month->year)
                ->whereMonth('closed_at', $month->month)
                ->count();

            $monthlyTrends[] = [
                'month' => $monthKey,
                'submitted' => $count,
                'resolved' => $resolvedMonthly,
            ];
        }

        return [
            'total' => $total,
            'pending' => $pending,
            'resolved' => $resolved,
            'rejected' => $rejected,
            'overdue' => $overdueCount,
            'avg_resolution_hours' => $avgHours,
            'departments' => $departments,
            'categories' => $categories,
            'management_workload' => $managementWorkload,
            'verification_rejection_rate' => $rejectionRate,
            'monthly_trends' => $monthlyTrends,
        ];
    }

    /**
     * Export complaints as CSV stream
     */
    public function exportCsv(array $filters): StreamedResponse
    {
        $complaints = $this->getFilteredComplaintsQuery($filters)->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="ccms_complaints_report_'.date('Ymd_His').'.csv"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        return response()->stream(function () use ($complaints) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, [
                'Complaint Number',
                'Submission Date',
                'Student Name',
                'Register Number',
                'Department',
                'Category',
                'Subject',
                'Priority',
                'Status',
                'Assigned Management',
                'Resolution Date',
                'Closed Date',
            ]);

            foreach ($complaints as $c) {
                fputcsv($handle, [
                    $c->complaint_number,
                    $c->created_at->format('Y-m-d H:i:s'),
                    $c->student?->full_name ?? 'N/A',
                    $c->student?->register_number ?? 'N/A',
                    $c->department?->name ?? 'N/A',
                    $c->category?->name ?? 'N/A',
                    $c->subject,
                    $c->priority,
                    $c->status,
                    $c->activeAssignment?->management?->full_name ?? 'Unassigned',
                    $c->resolution_submitted_at ? $c->resolution_submitted_at->format('Y-m-d H:i:s') : 'N/A',
                    $c->closed_at ? $c->closed_at->format('Y-m-d H:i:s') : 'N/A',
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }

    /**
     * Export complaints report as PDF
     */
    public function exportPdf(array $filters)
    {
        $complaints = $this->getFilteredComplaintsQuery($filters)->limit(200)->get();
        $analytics = $this->getAnalyticsSummary($filters);

        $pdf = Pdf::loadView('reports.complaints_pdf', [
            'complaints' => $complaints,
            'analytics' => $analytics,
            'filters' => $filters,
            'generatedAt' => now()->format('d M Y, H:i'),
        ]);

        $pdf->setPaper('a4', 'landscape');

        return $pdf->download('ccms_complaints_report_'.date('Ymd_His').'.pdf');
    }
}
