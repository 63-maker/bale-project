<?php

namespace App\Services;

use App\Models\Complaint;
use App\Models\ComplaintStatusHistory;
use App\Models\ComplaintVerification;
use App\Models\Profile;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class VerificationService
{
    public function __construct(
        protected AuditService $auditService,
        protected NotificationService $notificationService
    ) {}

    /**
     * Admin approves resolution verification
     */
    public function approveVerification(Complaint $complaint, Profile $admin, ?string $remarks = null): ComplaintVerification
    {
        if ($complaint->status !== Complaint::STATUS_RESOLUTION_SUBMITTED) {
            throw ValidationException::withMessages([
                'status' => 'Complaint resolution is not currently awaiting verification.',
            ]);
        }

        $activeAssignment = $complaint->activeAssignment;
        $managementId = $activeAssignment?->management_id;

        return DB::transaction(function () use ($complaint, $admin, $remarks, $managementId) {
            $prevStatus = $complaint->status;
            $newStatus = Complaint::STATUS_VERIFIED_FOR_CLOSURE;

            $complaint->update([
                'status' => $newStatus,
                'verified_by' => $admin->id,
                'verified_at' => now(),
                'verification_remarks' => $remarks ?: 'Resolution approved by Administrator.',
            ]);

            $verification = ComplaintVerification::create([
                'complaint_id' => $complaint->id,
                'management_id' => $managementId ?: $admin->id,
                'admin_id' => $admin->id,
                'decision' => ComplaintVerification::DECISION_APPROVED,
                'remarks' => $remarks ?: 'Resolution approved for closure.',
            ]);

            ComplaintStatusHistory::create([
                'complaint_id' => $complaint->id,
                'previous_status' => $prevStatus,
                'new_status' => $newStatus,
                'changed_by' => $admin->id,
                'remarks' => $remarks ?: 'Resolution verified and approved for closure by Administrator.',
            ]);

            $this->auditService->log(
                action: 'VERIFICATION_APPROVED',
                entityType: 'Complaint',
                entityId: $complaint->id,
                details: ['remarks' => $remarks],
                actor: $admin
            );

            if ($managementId) {
                $this->notificationService->notify(
                    $managementId,
                    'Resolution Approved',
                    "Admin approved your resolution for #{$complaint->complaint_number}. You may now complete closure.",
                    $complaint
                );
            }

            return $verification;
        });
    }

    /**
     * Admin rejects resolution verification
     */
    public function rejectVerification(Complaint $complaint, Profile $admin, string $rejectionRemarks): ComplaintVerification
    {
        if ($complaint->status !== Complaint::STATUS_RESOLUTION_SUBMITTED) {
            throw ValidationException::withMessages([
                'status' => 'Complaint resolution is not currently awaiting verification.',
            ]);
        }

        $activeAssignment = $complaint->activeAssignment;
        $managementId = $activeAssignment?->management_id;

        return DB::transaction(function () use ($complaint, $admin, $rejectionRemarks, $managementId) {
            $prevStatus = $complaint->status;
            $newStatus = Complaint::STATUS_VERIFICATION_REJECTED;

            $complaint->update([
                'status' => $newStatus,
                'verified_by' => $admin->id,
                'verified_at' => now(),
                'verification_remarks' => $rejectionRemarks,
            ]);

            $verification = ComplaintVerification::create([
                'complaint_id' => $complaint->id,
                'management_id' => $managementId ?: $admin->id,
                'admin_id' => $admin->id,
                'decision' => ComplaintVerification::DECISION_REJECTED,
                'remarks' => $rejectionRemarks,
            ]);

            ComplaintStatusHistory::create([
                'complaint_id' => $complaint->id,
                'previous_status' => $prevStatus,
                'new_status' => $newStatus,
                'changed_by' => $admin->id,
                'remarks' => "Resolution verification rejected: {$rejectionRemarks}",
            ]);

            $this->auditService->log(
                action: 'VERIFICATION_REJECTED',
                entityType: 'Complaint',
                entityId: $complaint->id,
                details: ['rejection_remarks' => $rejectionRemarks],
                actor: $admin
            );

            if ($managementId) {
                $this->notificationService->notify(
                    $managementId,
                    'Resolution Rejected by Admin',
                    "Resolution for #{$complaint->complaint_number} was rejected by Admin: {$rejectionRemarks}",
                    $complaint
                );
            }

            return $verification;
        });
    }

    /**
     * Management completes complaint closure after Admin approval
     */
    public function completeClosure(Complaint $complaint, Profile $management): void
    {
        if ($complaint->status !== Complaint::STATUS_VERIFIED_FOR_CLOSURE) {
            throw ValidationException::withMessages([
                'status' => 'Complaint can only be closed after Admin verification approval.',
            ]);
        }

        if (! $complaint->isAssignedTo($management->id) && ! $management->isAdmin()) {
            throw ValidationException::withMessages([
                'authorization' => 'You are not authorized to close this complaint.',
            ]);
        }

        DB::transaction(function () use ($complaint, $management) {
            $prevStatus = $complaint->status;
            $newStatus = Complaint::STATUS_CLOSED;

            $complaint->update([
                'status' => $newStatus,
                'closed_by' => $management->id,
                'closed_at' => now(),
            ]);

            ComplaintStatusHistory::create([
                'complaint_id' => $complaint->id,
                'previous_status' => $prevStatus,
                'new_status' => $newStatus,
                'changed_by' => $management->id,
                'remarks' => "Closure completed by {$management->full_name}.",
            ]);

            $this->auditService->log(
                action: 'COMPLAINT_CLOSED_BY_MANAGEMENT',
                entityType: 'Complaint',
                entityId: $complaint->id,
                actor: $management
            );

            // Notify student
            $this->notificationService->notify(
                $complaint->student_id,
                'Complaint Resolved & Closed',
                "Your complaint #{$complaint->complaint_number} has been resolved and closed.",
                $complaint
            );
        });
    }

    /**
     * Admin emergency force-close complaint with mandatory reason
     */
    public function emergencyForceClose(Complaint $complaint, Profile $admin, string $mandatoryReason): void
    {
        DB::transaction(function () use ($complaint, $admin, $mandatoryReason) {
            $prevStatus = $complaint->status;
            $newStatus = Complaint::STATUS_CLOSED;

            $complaint->update([
                'status' => $newStatus,
                'closed_by' => $admin->id,
                'closed_at' => now(),
                'rejection_reason' => $mandatoryReason, // or stored in remarks/history
            ]);

            ComplaintStatusHistory::create([
                'complaint_id' => $complaint->id,
                'previous_status' => $prevStatus,
                'new_status' => $newStatus,
                'changed_by' => $admin->id,
                'remarks' => "Emergency administrative closure: {$mandatoryReason}",
            ]);

            $this->auditService->log(
                action: 'ADMIN_EMERGENCY_FORCE_CLOSE',
                entityType: 'Complaint',
                entityId: $complaint->id,
                details: ['reason' => $mandatoryReason],
                actor: $admin
            );

            $this->notificationService->notify(
                $complaint->student_id,
                'Complaint Administratively Closed',
                "Your complaint #{$complaint->complaint_number} was closed by Admin: {$mandatoryReason}",
                $complaint
            );
        });
    }
}
