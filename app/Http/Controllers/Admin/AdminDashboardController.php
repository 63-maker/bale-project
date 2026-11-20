<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Services\NotificationService;
use App\Services\ReportService;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function __construct(
        protected ReportService $reportService,
        protected NotificationService $notificationService
    ) {}

    public function dashboard(): View
    {
        $admin = Auth::user();
        $analytics = $this->reportService->getAnalyticsSummary();

        $verificationQueue = Complaint::where('status', Complaint::STATUS_RESOLUTION_SUBMITTED)
            ->with(['student', 'department', 'category', 'activeAssignment.management'])
            ->orderBy('resolution_submitted_at')
            ->limit(5)
            ->get();

        $recentComplaints = Complaint::with(['student', 'category', 'department', 'activeAssignment.management'])
            ->orderByDesc('created_at')
            ->limit(6)
            ->get();

        $unreadNotifications = $this->notificationService->getUnreadCount($admin);

        return view('admin.dashboard', compact(
            'analytics',
            'verificationQueue',
            'recentComplaints',
            'unreadNotifications'
        ));
    }
}
