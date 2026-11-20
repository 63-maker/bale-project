<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ForceCloseComplaintRequest;
use App\Http\Requests\StoreCommentRequest;
use App\Http\Requests\VerifyResolutionRequest;
use App\Models\Complaint;
use App\Models\ComplaintAttachment;
use App\Models\ComplaintCategory;
use App\Models\Department;
use App\Models\Profile;
use App\Services\AssignmentService;
use App\Services\AttachmentService;
use App\Services\ComplaintService;
use App\Services\VerificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class AdminComplaintController extends Controller
{
    public function __construct(
        protected ComplaintService $complaintService,
        protected AssignmentService $assignmentService,
        protected VerificationService $verificationService,
        protected AttachmentService $attachmentService
    ) {}

    /**
     * Display all complaints with comprehensive filtering
     */
    public function index(Request $request): View
    {
        $query = Complaint::with(['student', 'category', 'department', 'activeAssignment.management'])
            ->orderByDesc('created_at');

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->input('priority'));
        }

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->input('department_id'));
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        if ($request->filled('search')) {
            $term = '%'.trim($request->input('search')).'%';
            $query->where(function ($q) use ($term) {
                $q->where('complaint_number', 'like', $term)
                    ->orWhere('subject', 'like', $term)
                    ->orWhereHas('student', function ($sq) use ($term) {
                        $sq->where('full_name', 'like', $term)
                            ->orWhere('register_number', 'like', $term);
                    });
            });
        }

        $complaints = $query->paginate(15)->withQueryString();
        $departments = Department::where('is_active', true)->orderBy('name')->get();
        $categories = ComplaintCategory::where('is_active', true)->orderBy('name')->get();

        return view('admin.complaints.index', compact('complaints', 'departments', 'categories'));
    }

    /**
     * Show complaint details and full administrative timeline
     */
    public function show(Complaint $complaint): View
    {
        $complaint->load([
            'category',
            'department',
            'student',
            'verifier',
            'closer',
            'attachments.uploader',
            'comments.sender',
            'statusHistory.changer',
            'verifications.admin',
            'assignments.management',
            'assignments.assigner',
            'activeAssignment.management',
        ]);

        $managementMembers = Profile::where('role', Profile::ROLE_MANAGEMENT)
            ->where('account_status', Profile::STATUS_ACTIVE)
            ->with('department')
            ->orderBy('full_name')
            ->get();

        return view('admin.complaints.show', compact('complaint', 'managementMembers'));
    }

    /**
     * Manually assign complaint to selected management member
     */
    public function assign(Complaint $complaint, Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'management_id' => ['required', 'uuid', 'exists:profiles,id'],
            'remarks' => ['nullable', 'string', 'max:500'],
        ]);

        $management = Profile::findOrFail($validated['management_id']);

        $this->assignmentService->assignManually(
            $complaint,
            $management,
            Auth::user(),
            $validated['remarks'] ?? null
        );

        return back()->with('success', "Complaint #{$complaint->complaint_number} assigned to {$management->full_name}.");
    }

    /**
     * Trigger automatic workload-based assignment
     */
    public function triggerAutoAssign(Complaint $complaint): RedirectResponse
    {
        $assignment = $this->assignmentService->assignAutomatically($complaint, Auth::user());

        if (! $assignment) {
            return back()->with('error', 'No active Management members available for automatic assignment.');
        }

        $assignedTo = $assignment->management?->full_name ?? 'Management member';

        return back()->with('success', "Complaint automatically assigned to {$assignedTo} based on lowest workload.");
    }

    /**
     * Show dedicated verification queue
     */
    public function showVerificationQueue(): View
    {
        $complaints = Complaint::where('status', Complaint::STATUS_RESOLUTION_SUBMITTED)
            ->with(['student', 'category', 'department', 'activeAssignment.management', 'verifications.admin'])
            ->orderBy('resolution_submitted_at')
            ->paginate(15);

        return view('admin.complaints.verification_queue', compact('complaints'));
    }

    /**
     * Verify resolution (Approve or Reject with explanation)
     */
    public function verify(Complaint $complaint, VerifyResolutionRequest $request): RedirectResponse
    {
        $decision = $request->input('decision');
        $remarks = $request->input('remarks');

        if ($decision === 'approved') {
            $this->verificationService->approveVerification($complaint, Auth::user(), $remarks);

            return back()->with('success', "Resolution for #{$complaint->complaint_number} APPROVED for closure.");
        } else {
            $this->verificationService->rejectVerification($complaint, Auth::user(), $remarks);

            return back()->with('info', "Resolution for #{$complaint->complaint_number} REJECTED. Management notified.");
        }
    }

    /**
     * Admin administrative complaint rejection
     */
    public function rejectComplaint(Complaint $complaint, Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'rejection_reason' => ['required', 'string', 'min:5', 'max:2000'],
        ]);

        $this->complaintService->adminReject($complaint, Auth::user(), $validated['rejection_reason']);

        return back()->with('success', "Complaint #{$complaint->complaint_number} has been rejected.");
    }

    /**
     * Admin emergency force-closure with mandatory audit reason
     */
    public function forceClose(Complaint $complaint, ForceCloseComplaintRequest $request): RedirectResponse
    {
        $this->verificationService->emergencyForceClose(
            $complaint,
            Auth::user(),
            $request->input('reason')
        );

        return back()->with('success', "Complaint #{$complaint->complaint_number} has been administratively force-closed.");
    }

    /**
     * Add comment from Admin
     */
    public function addComment(Complaint $complaint, StoreCommentRequest $request): RedirectResponse
    {
        $visibility = $request->input('visibility', 'public');
        if (! in_array($visibility, ['public', 'internal'], true)) {
            $visibility = 'public';
        }

        $this->complaintService->addComment(
            $complaint,
            Auth::user(),
            $request->input('message'),
            $visibility
        );

        return back()->with('success', 'Message posted to complaint discussion.');
    }

    /**
     * Download attachment securely
     */
    public function downloadAttachment(ComplaintAttachment $attachment): BinaryFileResponse
    {
        return $this->attachmentService->downloadAttachment($attachment, Auth::user());
    }
}
