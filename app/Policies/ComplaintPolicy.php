<?php

namespace App\Policies;

use App\Models\Complaint;
use App\Models\Profile;

class ComplaintPolicy
{
    /**
     * Determine whether the user can view the complaint
     */
    public function view(Profile $user, Complaint $complaint): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isStudent()) {
            return $complaint->student_id === $user->id;
        }

        if ($user->isManagement()) {
            return $complaint->isAssignedTo($user->id);
        }

        return false;
    }

    /**
     * Determine whether the user can comment on the complaint
     */
    public function comment(Profile $user, Complaint $complaint): bool
    {
        return $this->view($user, $complaint);
    }

    /**
     * Determine whether the management user can start investigation
     */
    public function startInvestigation(Profile $user, Complaint $complaint): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $user->isManagement() && $complaint->isAssignedTo($user->id);
    }

    /**
     * Determine whether the user can submit a resolution
     */
    public function submitResolution(Profile $user, Complaint $complaint): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $user->isManagement() && $complaint->isAssignedTo($user->id);
    }

    /**
     * Determine whether the user can verify the resolution
     * Strictly Admin only - Management cannot verify resolutions
     */
    public function verify(Profile $user, Complaint $complaint): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can finalize closure
     * Management can only close when verified for closure
     */
    public function completeClosure(Profile $user, Complaint $complaint): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $user->isManagement()
            && $complaint->isAssignedTo($user->id)
            && $complaint->status === Complaint::STATUS_VERIFIED_FOR_CLOSURE;
    }

    /**
     * Determine whether student can request reopening
     */
    public function reopen(Profile $user, Complaint $complaint): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $user->isStudent()
            && $complaint->student_id === $user->id
            && $complaint->status === Complaint::STATUS_CLOSED;
    }

    /**
     * Determine whether admin can emergency force-close
     */
    public function forceClose(Profile $user, Complaint $complaint): bool
    {
        return $user->isAdmin();
    }
}
