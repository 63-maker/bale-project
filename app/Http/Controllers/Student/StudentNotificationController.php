<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class StudentNotificationController extends Controller
{
    public function __construct(
        protected NotificationService $notificationService
    ) {}

    public function index(): View
    {
        $notifications = Notification::where('user_id', Auth::id())
            ->with('complaint')
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('student.notifications.index', compact('notifications'));
    }

    public function markAsRead(Notification $notification): RedirectResponse
    {
        $this->notificationService->markAsRead($notification, Auth::user());

        return back()->with('success', 'Notification marked as read.');
    }

    public function markAllAsRead(): RedirectResponse
    {
        $this->notificationService->markAllAsRead(Auth::user());

        return back()->with('success', 'All notifications marked as read.');
    }
}
