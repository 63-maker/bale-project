<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class StudentDashboardController extends Controller
{
    public function __construct(
        protected NotificationService $notificationService
    ) {}

    public function dashboard(): View
    {
        $student = Auth::user();

        $totalComplaints = Complaint::where('student_id', $student->id)->count();

        $pendingComplaints = Complaint::where('student_id', $student->id)
            ->whereIn('status', [
                Complaint::STATUS_SUBMITTED,
                Complaint::STATUS_UNDER_REVIEW,
                Complaint::STATUS_ASSIGNED,
                Complaint::STATUS_REOPENED,
            ])
            ->count();

        $inProgressComplaints = Complaint::where('student_id', $student->id)
            ->whereIn('status', [
                Complaint::STATUS_IN_PROGRESS,
                Complaint::STATUS_RESOLUTION_SUBMITTED,
                Complaint::STATUS_VERIFICATION_REJECTED,
            ])
            ->count();

        $resolvedComplaints = Complaint::where('student_id', $student->id)
            ->whereIn('status', [
                Complaint::STATUS_VERIFIED_FOR_CLOSURE,
                Complaint::STATUS_CLOSED,
            ])
            ->count();

        $recentComplaints = Complaint::where('student_id', $student->id)
            ->with(['category', 'department'])
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        $unreadNotifications = $this->notificationService->getUnreadCount($student);

        return view('student.dashboard', compact(
            'student',
            'totalComplaints',
            'pendingComplaints',
            'inProgressComplaints',
            'resolvedComplaints',
            'recentComplaints',
            'unreadNotifications'
        ));
    }
}
