<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ComplaintCategory;
use App\Models\Department;
use App\Models\Profile;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminReportController extends Controller
{
    public function __construct(
        protected ReportService $reportService
    ) {}

    public function index(Request $request): View
    {
        $filters = $request->only([
            'start_date',
            'end_date',
            'department_id',
            'category_id',
            'priority',
            'status',
            'management_id',
        ]);

        $analytics = $this->reportService->getAnalyticsSummary($filters);
        $complaints = $this->reportService->getFilteredComplaintsQuery($filters)->paginate(15)->withQueryString();

        $departments = Department::where('is_active', true)->orderBy('name')->get();
        $categories = ComplaintCategory::where('is_active', true)->orderBy('name')->get();
        $managementMembers = Profile::where('role', Profile::ROLE_MANAGEMENT)->orderBy('full_name')->get();

        return view('admin.reports.index', compact(
            'analytics',
            'complaints',
            'filters',
            'departments',
            'categories',
            'managementMembers'
        ));
    }

    public function exportCsv(Request $request)
    {
        $filters = $request->only([
            'start_date',
            'end_date',
            'department_id',
            'category_id',
            'priority',
            'status',
            'management_id',
        ]);

        return $this->reportService->exportCsv($filters);
    }

    public function exportPdf(Request $request)
    {
        $filters = $request->only([
            'start_date',
            'end_date',
            'department_id',
            'category_id',
            'priority',
            'status',
            'management_id',
        ]);

        return $this->reportService->exportPdf($filters);
    }
}
