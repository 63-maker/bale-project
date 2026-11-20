<?php

namespace App\Services;

use App\Models\Complaint;
use App\Models\Notification;
use App\Models\Profile;

class NotificationService
{
    /**
     * Send an in-app notification to a user
     */
    public function notify(
        Profile|string $recipient,
        string $title,
        string $message,
        ?Complaint $complaint = null
    ): Notification {
        $userId = $recipient instanceof Profile ? $recipient->id : $recipient;

        return Notification::create([
            'user_id' => $userId,
            'complaint_id' => $complaint?->id,
            'title' => $title,
            'message' => $message,
            'is_read' => false,
        ]);
    }

    /**
     * Notify all active administrators
     */
    public function notifyAdmins(string $title, string $message, ?Complaint $complaint = null): void
    {
        $admins = Profile::where('role', Profile::ROLE_ADMIN)
            ->where('account_status', Profile::STATUS_ACTIVE)
            ->get();

        foreach ($admins as $admin) {
            $this->notify($admin, $title, $message, $complaint);
        }
    }

    /**
     * Mark single notification as read
     */
    public function markAsRead(Notification $notification, Profile $user): void
    {
        if ($notification->user_id === $user->id) {
            $notification->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
        }
    }

    /**
     * Mark all notifications as read for a user
     */
    public function markAllAsRead(Profile $user): void
    {
        Notification::where('user_id', $user->id)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
    }

    /**
     * Get count of unread notifications for a user
     */
    public function getUnreadCount(Profile $user): int
    {
        return Notification::where('user_id', $user->id)
            ->where('is_read', false)
            ->count();
    }
}
