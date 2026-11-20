<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCommentRequest;
use App\Http\Requests\SubmitResolutionRequest;
use App\Models\Complaint;
use App\Models\ComplaintAttachment;
use App\Services\AttachmentService;
use App\Services\ComplaintService;
use App\Services\VerificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ManagementComplaintController extends Controller
{
    public function __construct(
        protected ComplaintService $complaintService,
        protected VerificationService $verificationService,
        protected AttachmentService $attachmentService
    ) {}

    /**
     * List assigned complaints
     */
    public function index(Request $request): View
    {
        $management = Auth::user();

        $query = Complaint::whereHas('assignments', function ($q) use ($management) {
            $q->where('management_id', $management->id)->where('is_active', true);
        })
            ->with(['category', 'student', 'department'])
            ->orderByDesc('created_at');

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->input('priority'));
        }

        if ($request->filled('search')) {
            $term = '%'.trim($request->input('search')).'%';
            $query->where(function ($q) use ($term) {
                $q->where('complaint_number', 'like', $term)
                    ->orWhere('subject', 'like', $term);
            });
        }

        $complaints = $query->paginate(10)->withQueryString();

        return view('management.complaints.index', compact('complaints'));
    }

    /**
     * View complaint detail and investigation panel
     */
    public function show(Complaint $complaint): View
    {
        Gate::authorize('view', $complaint);

        $complaint->load([
            'category',
            'department',
            'student',
            'attachments.uploader',
            'comments.sender',
            'statusHistory.changer',
            'verifications.admin',
            'activeAssignment.management',
        ]);

        return view('management.complaints.show', compact('complaint'));
    }

    /**
     * Start active investigation on complaint
     */
    public function startInvestigation(Complaint $complaint): RedirectResponse
    {
        Gate::authorize('startInvestigation', $complaint);

        $this->complaintService->startInvestigation($complaint, Auth::user());

        return back()->with('success', 'Investigation officially commenced. Complaint status updated to IN PROGRESS.');
    }

    /**
     * Add comment (Public communication or Internal Note)
     */
    public function addComment(Complaint $complaint, StoreCommentRequest $request): RedirectResponse
    {
        Gate::authorize('comment', $complaint);

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

        $typeLabel = $visibility === 'internal' ? 'Internal note' : 'Response';

        return back()->with('success', "{$typeLabel} posted successfully.");
    }

    /**
     * Submit resolution description for Admin verification
     */
    public function submitResolution(Complaint $complaint, SubmitResolutionRequest $request): RedirectResponse
    {
        Gate::authorize('submitResolution', $complaint);

        $this->complaintService->submitResolution(
            $complaint,
            Auth::user(),
            $request->input('resolution_description')
        );

        return back()->with('success', 'Resolution submitted successfully. It is now awaiting Admin verification.');
    }

    /**
     * Complete closure after Admin verification approval
     */
    public function completeClosure(Complaint $complaint): RedirectResponse
    {
        Gate::authorize('completeClosure', $complaint);

        $this->verificationService->completeClosure($complaint, Auth::user());

        return back()->with('success', 'Complaint closure finalized and marked as CLOSED.');
    }

    /**
     * Verification queue / status tracking for management
     */
    public function verificationStatus(): View
    {
        $management = Auth::user();

        $complaints = Complaint::whereHas('assignments', function ($q) use ($management) {
            $q->where('management_id', $management->id)->where('is_active', true);
        })
            ->whereIn('status', [
                Complaint::STATUS_RESOLUTION_SUBMITTED,
                Complaint::STATUS_VERIFICATION_REJECTED,
                Complaint::STATUS_VERIFIED_FOR_CLOSURE,
            ])
            ->with(['category', 'student', 'department', 'verifications.admin'])
            ->orderByDesc('updated_at')
            ->paginate(15);

        return view('management.complaints.verification_status', compact('complaints'));
    }

    /**
     * Closed complaints history
     */
    public function closed(): View
    {
        $management = Auth::user();

        $complaints = Complaint::where('closed_by', $management->id)
            ->with(['category', 'student', 'department'])
            ->orderByDesc('closed_at')
            ->paginate(15);

        return view('management.complaints.closed', compact('complaints'));
    }

    /**
     * Download attachment securely
     */
    public function downloadAttachment(ComplaintAttachment $attachment): BinaryFileResponse
    {
        return $this->attachmentService->downloadAttachment($attachment, Auth::user());
    }
}
