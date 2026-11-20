<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ManagementDashboardController extends Controller
{
    public function __construct(
        protected NotificationService $notificationService
    ) {}

    public function dashboard(): View
    {
        $management = Auth::user();

        $baseQuery = Complaint::whereHas('assignments', function ($q) use ($management) {
            $q->where('management_id', $management->id)->where('is_active', true);
        });

        $totalAssigned = (clone $baseQuery)->count();
        $newAssigned = (clone $baseQuery)->where('status', Complaint::STATUS_ASSIGNED)->count();
        $inProgress = (clone $baseQuery)->where('status', Complaint::STATUS_IN_PROGRESS)->count();
        $awaitingVerification = (clone $baseQuery)->where('status', Complaint::STATUS_RESOLUTION_SUBMITTED)->count();
        $verificationRejected = (clone $baseQuery)->where('status', Complaint::STATUS_VERIFICATION_REJECTED)->count();
        $verifiedForClosure = (clone $baseQuery)->where('status', Complaint::STATUS_VERIFIED_FOR_CLOSURE)->count();
        $closed = Complaint::where('closed_by', $management->id)->count();

        $recentComplaints = (clone $baseQuery)
            ->with(['category', 'student', 'department'])
            ->orderByDesc('updated_at')
            ->limit(5)
            ->get();

        $unreadNotifications = $this->notificationService->getUnreadCount($management);

        return view('management.dashboard', compact(
            'management',
            'totalAssigned',
            'newAssigned',
            'inProgress',
            'awaitingVerification',
            'verificationRejected',
            'verifiedForClosure',
            'closed',
            'recentComplaints',
            'unreadNotifications'
        ));
    }
}
