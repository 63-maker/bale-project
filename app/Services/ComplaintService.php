<?php

namespace App\Services;

use App\Models\Complaint;
use App\Models\ComplaintComment;
use App\Models\ComplaintCounter;
use App\Models\ComplaintStatusHistory;
use App\Models\Profile;
use App\Models\SystemSetting;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ComplaintService
{
    public function __construct(
        protected AssignmentService $assignmentService,
        protected AttachmentService $attachmentService,
        protected AuditService $auditService,
        protected NotificationService $notificationService
    ) {}

    /**
     * Submit a new complaint by student
     */
    public function createComplaint(Profile $student, array $data, array $uploadedFiles = []): Complaint
    {
        return DB::transaction(function () use ($student, $data, $uploadedFiles) {
            $year = (int) date('Y');
            $complaintNumber = ComplaintCounter::getNextComplaintNumber($year);

            $complaint = Complaint::create([
                'complaint_number' => $complaintNumber,
                'student_id' => $student->id,
                'category_id' => $data['category_id'],
                'department_id' => $data['department_id'],
                'subject' => $data['subject'],
                'description' => $data['description'],
                'priority' => $data['priority'] ?? Complaint::PRIORITY_MEDIUM,
                'status' => Complaint::STATUS_SUBMITTED,
            ]);

            // Save status history
            ComplaintStatusHistory::create([
                'complaint_id' => $complaint->id,
                'previous_status' => null,
                'new_status' => Complaint::STATUS_SUBMITTED,
                'changed_by' => $student->id,
                'remarks' => 'Complaint submitted by student.',
            ]);

            // Process uploaded attachments
            foreach ($uploadedFiles as $file) {
                if ($file instanceof UploadedFile) {
                    $this->attachmentService->storeAttachment($file, $complaint, $student);
                }
            }

            $this->auditService->log(
                action: 'COMPLAINT_SUBMITTED',
                entityType: 'Complaint',
                entityId: $complaint->id,
                details: [
                    'complaint_number' => $complaintNumber,
                    'category_id' => $complaint->category_id,
                    'department_id' => $complaint->department_id,
                ],
                actor: $student
            );

            // Notify Admin
            $this->notificationService->notifyAdmins(
                'New Complaint Submitted',
                "Student {$student->full_name} submitted complaint #{$complaintNumber}: {$complaint->subject}",
                $complaint
            );

            // If auto-assignment is enabled in system settings, trigger assignment
            if (SystemSetting::get('auto_assignment_enabled', true)) {
                $this->assignmentService->assignAutomatically($complaint, $student);
            }

            return $complaint;
        });
    }

    /**
     * Management starts investigation
     */
    public function startInvestigation(Complaint $complaint, Profile $management): void
    {
        if (! $complaint->isAssignedTo($management->id) && ! $management->isAdmin()) {
            throw ValidationException::withMessages([
                'authorization' => 'You are not assigned to investigate this complaint.',
            ]);
        }

        if (! in_array($complaint->status, [Complaint::STATUS_ASSIGNED, Complaint::STATUS_VERIFICATION_REJECTED], true)) {
            throw ValidationException::withMessages([
                'status' => 'Complaint investigation can only be started from ASSIGNED or VERIFICATION_REJECTED status.',
            ]);
        }

        DB::transaction(function () use ($complaint, $management) {
            $prevStatus = $complaint->status;
            $newStatus = Complaint::STATUS_IN_PROGRESS;

            $complaint->update(['status' => $newStatus]);

            ComplaintStatusHistory::create([
                'complaint_id' => $complaint->id,
                'previous_status' => $prevStatus,
                'new_status' => $newStatus,
                'changed_by' => $management->id,
                'remarks' => "Investigation officially commenced by {$management->full_name}.",
            ]);

            $this->auditService->log(
                action: 'INVESTIGATION_STARTED',
                entityType: 'Complaint',
                entityId: $complaint->id,
                actor: $management
            );

            $this->notificationService->notify(
                $complaint->student_id,
                'Investigation Started',
                "Management has started active investigation on your complaint #{$complaint->complaint_number}.",
                $complaint
            );
        });
    }

    /**
     * Management submits resolution for admin verification
     */
    public function submitResolution(Complaint $complaint, Profile $management, string $resolutionDescription): void
    {
        if (! $complaint->isAssignedTo($management->id) && ! $management->isAdmin()) {
            throw ValidationException::withMessages([
                'authorization' => 'You are not assigned to this complaint.',
            ]);
        }

        if (! in_array($complaint->status, [Complaint::STATUS_IN_PROGRESS, Complaint::STATUS_VERIFICATION_REJECTED], true)) {
            throw ValidationException::withMessages([
                'status' => 'Resolution can only be submitted when complaint is IN_PROGRESS or VERIFICATION_REJECTED.',
            ]);
        }

        DB::transaction(function () use ($complaint, $management, $resolutionDescription) {
            $prevStatus = $complaint->status;
            $newStatus = Complaint::STATUS_RESOLUTION_SUBMITTED;

            $complaint->update([
                'status' => $newStatus,
                'resolution_description' => $resolutionDescription,
                'resolution_submitted_at' => now(),
            ]);

            ComplaintStatusHistory::create([
                'complaint_id' => $complaint->id,
                'previous_status' => $prevStatus,
                'new_status' => $newStatus,
                'changed_by' => $management->id,
                'remarks' => "Resolution submitted by {$management->full_name} for Admin verification.",
            ]);

            $this->auditService->log(
                action: 'RESOLUTION_SUBMITTED',
                entityType: 'Complaint',
                entityId: $complaint->id,
                details: ['resolution' => $resolutionDescription],
                actor: $management
            );

            // Notify Admin
            $this->notificationService->notifyAdmins(
                'Resolution Awaiting Verification',
                "Management submitted resolution for complaint #{$complaint->complaint_number}. Verification required.",
                $complaint
            );
        });
    }

    /**
     * Student requests reopening of closed complaint
     */
    public function reopenComplaint(Complaint $complaint, Profile $student, string $reason): void
    {
        if ($complaint->student_id !== $student->id && ! $student->isAdmin()) {
            throw ValidationException::withMessages([
                'authorization' => 'Only the submitting student can request complaint reopening.',
            ]);
        }

        if ($complaint->status !== Complaint::STATUS_CLOSED) {
            throw ValidationException::withMessages([
                'status' => 'Only closed complaints can be reopened.',
            ]);
        }

        DB::transaction(function () use ($complaint, $student, $reason) {
            $prevStatus = $complaint->status;
            $newStatus = Complaint::STATUS_REOPENED;

            $complaint->update([
                'status' => $newStatus,
                'reopened_reason' => $reason,
            ]);

            ComplaintStatusHistory::create([
                'complaint_id' => $complaint->id,
                'previous_status' => $prevStatus,
                'new_status' => $newStatus,
                'changed_by' => $student->id,
                'remarks' => "Reopened by student. Reason: {$reason}",
            ]);

            $this->auditService->log(
                action: 'COMPLAINT_REOPENED',
                entityType: 'Complaint',
                entityId: $complaint->id,
                details: ['reopen_reason' => $reason],
                actor: $student
            );

            $this->notificationService->notifyAdmins(
                'Complaint Reopened by Student',
                "Complaint #{$complaint->complaint_number} was reopened by student. Reason: {$reason}",
                $complaint
            );

            // Also notify active assigned management if exists
            if ($assignment = $complaint->activeAssignment) {
                $this->notificationService->notify(
                    $assignment->management_id,
                    'Complaint Reopened',
                    "Complaint #{$complaint->complaint_number} previously assigned to you was reopened.",
                    $complaint
                );
            }
        });
    }

    /**
     * Admin rejects complaint with mandatory reason
     */
    public function adminReject(Complaint $complaint, Profile $admin, string $reason): void
    {
        DB::transaction(function () use ($complaint, $admin, $reason) {
            $prevStatus = $complaint->status;
            $newStatus = Complaint::STATUS_REJECTED;

            $complaint->update([
                'status' => $newStatus,
                'rejection_reason' => $reason,
            ]);

            ComplaintStatusHistory::create([
                'complaint_id' => $complaint->id,
                'previous_status' => $prevStatus,
                'new_status' => $newStatus,
                'changed_by' => $admin->id,
                'remarks' => "Complaint rejected by Administrator: {$reason}",
            ]);

            $this->auditService->log(
                action: 'COMPLAINT_REJECTED_BY_ADMIN',
                entityType: 'Complaint',
                entityId: $complaint->id,
                details: ['reason' => $reason],
                actor: $admin
            );

            $this->notificationService->notify(
                $complaint->student_id,
                'Complaint Rejected',
                "Your complaint #{$complaint->complaint_number} was rejected by Admin: {$reason}",
                $complaint
            );
        });
    }

    /**
     * Add comment to complaint conversation
     */
    public function addComment(
        Complaint $complaint,
        Profile $sender,
        string $message,
        string $visibility = ComplaintComment::VISIBILITY_PUBLIC
    ): ComplaintComment {
        // Students can only post public comments
        if ($sender->isStudent()) {
            $visibility = ComplaintComment::VISIBILITY_PUBLIC;
            if ($complaint->student_id !== $sender->id) {
                throw ValidationException::withMessages([
                    'authorization' => 'Unauthorized to comment on this complaint.',
                ]);
            }
        } elseif ($sender->isManagement()) {
            if (! $complaint->isAssignedTo($sender->id) && ! $sender->isAdmin()) {
                throw ValidationException::withMessages([
                    'authorization' => 'Unauthorized to comment on this complaint.',
                ]);
            }
        }

        $comment = ComplaintComment::create([
            'complaint_id' => $complaint->id,
            'sender_id' => $sender->id,
            'message' => $message,
            'visibility' => $visibility,
        ]);

        $this->auditService->log(
            action: 'COMMENT_ADDED',
            entityType: 'ComplaintComment',
            entityId: $comment->id,
            details: ['visibility' => $visibility],
            actor: $sender
        );

        // Notifications
        if ($visibility === ComplaintComment::VISIBILITY_PUBLIC) {
            if ($sender->isStudent()) {
                // Notify assigned management
                if ($assignment = $complaint->activeAssignment) {
                    $this->notificationService->notify(
                        $assignment->management_id,
                        'New Comment from Student',
                        "Student replied on complaint #{$complaint->complaint_number}.",
                        $complaint
                    );
                }
            } else {
                // Notify student
                $this->notificationService->notify(
                    $complaint->student_id,
                    'New Response on Complaint',
                    "Management posted an update on complaint #{$complaint->complaint_number}.",
                    $complaint
                );
            }
        }

        return $comment;
    }
}
