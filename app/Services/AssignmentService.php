<?php

namespace App\Services;

use App\Models\Complaint;
use App\Models\ComplaintAssignment;
use App\Models\ComplaintStatusHistory;
use App\Models\Profile;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AssignmentService
{
    public function __construct(
        protected AuditService $auditService,
        protected NotificationService $notificationService
    ) {}

    /**
     * Manually assign complaint to a management member
     */
    public function assignManually(
        Complaint $complaint,
        Profile $management,
        Profile $admin,
        ?string $remarks = null
    ): ComplaintAssignment {
        if (! $management->isManagement() || ! $management->isActive()) {
            throw ValidationException::withMessages([
                'management_id' => 'Selected user is not an active Management member.',
            ]);
        }

        return DB::transaction(function () use ($complaint, $management, $admin, $remarks) {
            // Deactivate previous active assignment
            ComplaintAssignment::where('complaint_id', $complaint->id)
                ->where('is_active', true)
                ->update([
                    'is_active' => false,
                    'unassigned_at' => now(),
                ]);

            $assignment = ComplaintAssignment::create([
                'complaint_id' => $complaint->id,
                'management_id' => $management->id,
                'assigned_by' => $admin->id,
                'assignment_method' => ComplaintAssignment::METHOD_MANUAL,
                'is_active' => true,
                'assigned_at' => now(),
            ]);

            $prevStatus = $complaint->status;
            $newStatus = Complaint::STATUS_ASSIGNED;

            $complaint->update([
                'status' => $newStatus,
            ]);

            ComplaintStatusHistory::create([
                'complaint_id' => $complaint->id,
                'previous_status' => $prevStatus,
                'new_status' => $newStatus,
                'changed_by' => $admin->id,
                'remarks' => $remarks ?: "Manually assigned to {$management->full_name} ({$management->employee_id})",
            ]);

            $this->auditService->log(
                action: 'COMPLAINT_ASSIGNED_MANUAL',
                entityType: 'Complaint',
                entityId: $complaint->id,
                details: [
                    'management_id' => $management->id,
                    'management_name' => $management->full_name,
                    'employee_id' => $management->employee_id,
                ],
                actor: $admin
            );

            $this->notificationService->notify(
                $management,
                'Complaint Assigned',
                "Complaint #{$complaint->complaint_number} ({$complaint->subject}) has been assigned to you for investigation.",
                $complaint
            );

            return $assignment;
        });
    }

    /**
     * Automatically assign complaint to eligible management member with lowest workload
     */
    public function assignAutomatically(Complaint $complaint, ?Profile $triggeredBy = null): ?ComplaintAssignment
    {
        return DB::transaction(function () use ($complaint, $triggeredBy) {
            // Find active management members in the complaint's department
            $candidates = Profile::where('role', Profile::ROLE_MANAGEMENT)
                ->where('department_id', $complaint->department_id)
                ->where('account_status', Profile::STATUS_ACTIVE)
                ->get();

            if ($candidates->isEmpty()) {
                // Fallback: check all active management members
                $candidates = Profile::where('role', Profile::ROLE_MANAGEMENT)
                    ->where('account_status', Profile::STATUS_ACTIVE)
                    ->get();
            }

            if ($candidates->isEmpty()) {
                return null;
            }

            // Calculate workload: open active assignments on complaints that are not closed/rejected
            $candidateWorkloads = $candidates->map(function (Profile $member) {
                $openWorkload = ComplaintAssignment::where('management_id', $member->id)
                    ->where('is_active', true)
                    ->whereHas('complaint', function ($query) {
                        $query->whereNotIn('status', [Complaint::STATUS_CLOSED, Complaint::STATUS_REJECTED]);
                    })
                    ->count();

                return [
                    'profile' => $member,
                    'workload' => $openWorkload,
                    'employee_id' => $member->employee_id ?? $member->id,
                ];
            });

            // Sort by workload ascending, then deterministic tie-breaker on employee_id ascending
            $selected = $candidateWorkloads->sortBy([
                ['workload', 'asc'],
                ['employee_id', 'asc'],
            ])->first();

            /** @var Profile $selectedManagement */
            $selectedManagement = $selected['profile'];

            // Deactivate existing active assignment if any
            ComplaintAssignment::where('complaint_id', $complaint->id)
                ->where('is_active', true)
                ->update([
                    'is_active' => false,
                    'unassigned_at' => now(),
                ]);

            $assignerId = $triggeredBy?->id ?? $complaint->student_id;

            $assignment = ComplaintAssignment::create([
                'complaint_id' => $complaint->id,
                'management_id' => $selectedManagement->id,
                'assigned_by' => $assignerId,
                'assignment_method' => ComplaintAssignment::METHOD_AUTOMATIC,
                'is_active' => true,
                'assigned_at' => now(),
            ]);

            $prevStatus = $complaint->status;
            $newStatus = Complaint::STATUS_ASSIGNED;

            $complaint->update([
                'status' => $newStatus,
            ]);

            ComplaintStatusHistory::create([
                'complaint_id' => $complaint->id,
                'previous_status' => $prevStatus,
                'new_status' => $newStatus,
                'changed_by' => $triggeredBy?->id,
                'remarks' => "Automatically assigned to {$selectedManagement->full_name} (Workload: {$selected['workload']} open items)",
            ]);

            $this->auditService->log(
                action: 'COMPLAINT_ASSIGNED_AUTOMATIC',
                entityType: 'Complaint',
                entityId: $complaint->id,
                details: [
                    'management_id' => $selectedManagement->id,
                    'workload_at_assignment' => $selected['workload'],
                ],
                actor: $triggeredBy
            );

            $this->notificationService->notify(
                $selectedManagement,
                'New Complaint Assigned (Auto)',
                "Complaint #{$complaint->complaint_number} has been automatically routed and assigned to you.",
                $complaint
            );

            return $assignment;
        });
    }

    /**
     * Reassign complaint with preserved history
     */
    public function reassign(
        Complaint $complaint,
        Profile $newManagement,
        Profile $admin,
        ?string $reason = null
    ): ComplaintAssignment {
        return $this->assignManually($complaint, $newManagement, $admin, $reason ? "Reassigned: {$reason}" : null);
    }
}
