<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReopenComplaintRequest;
use App\Http\Requests\StoreCommentRequest;
use App\Http\Requests\StoreComplaintRequest;
use App\Models\Complaint;
use App\Models\ComplaintAttachment;
use App\Models\ComplaintCategory;
use App\Models\Department;
use App\Services\AttachmentService;
use App\Services\ComplaintService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class StudentComplaintController extends Controller
{
    public function __construct(
        protected ComplaintService $complaintService,
        protected AttachmentService $attachmentService
    ) {}

    /**
     * List all complaints submitted by the student
     */
    public function index(Request $request): View
    {
        $student = Auth::user();

        $query = Complaint::where('student_id', $student->id)
            ->with(['category', 'department', 'activeAssignment.management'])
            ->orderByDesc('created_at');

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('search')) {
            $term = '%'.trim($request->input('search')).'%';
            $query->where(function ($q) use ($term) {
                $q->where('complaint_number', 'like', $term)
                    ->orWhere('subject', 'like', $term);
            });
        }

        $complaints = $query->paginate(10)->withQueryString();

        return view('student.complaints.index', compact('complaints'));
    }

    /**
     * Show complaint submission form
     */
    public function create(): View
    {
        $departments = Department::where('is_active', true)->orderBy('name')->get();
        $categories = ComplaintCategory::where('is_active', true)->with('department')->orderBy('name')->get();

        return view('student.complaints.create', compact('departments', 'categories'));
    }

    /**
     * Store new complaint
     */
    public function store(StoreComplaintRequest $request): RedirectResponse
    {
        $files = $request->file('attachments', []);
        if (! is_array($files)) {
            $files = $files ? [$files] : [];
        }

        $complaint = $this->complaintService->createComplaint(
            Auth::user(),
            $request->validated(),
            $files
        );

        return redirect()->route('student.complaints.show', $complaint->id)
            ->with('success', "Complaint submitted successfully. Reference number: {$complaint->complaint_number}");
    }

    /**
     * View complaint detail, timeline, and conversation
     */
    public function show(Complaint $complaint): View
    {
        Gate::authorize('view', $complaint);

        $complaint->load([
            'category',
            'department',
            'attachments.uploader',
            'publicComments.sender',
            'statusHistory.changer',
            'activeAssignment.management',
        ]);

        return view('student.complaints.show', compact('complaint'));
    }

    /**
     * Student adds comment/reply to complaint
     */
    public function addComment(Complaint $complaint, StoreCommentRequest $request): RedirectResponse
    {
        Gate::authorize('comment', $complaint);

        $this->complaintService->addComment(
            $complaint,
            Auth::user(),
            $request->input('message'),
            'public'
        );

        return back()->with('success', 'Your message has been posted.');
    }

    /**
     * Student requests reopening of closed complaint
     */
    public function reopen(Complaint $complaint, ReopenComplaintRequest $request): RedirectResponse
    {
        Gate::authorize('reopen', $complaint);

        $this->complaintService->reopenComplaint(
            $complaint,
            Auth::user(),
            $request->input('reason')
        );

        return back()->with('success', 'Complaint reopening request has been submitted to Administration.');
    }

    /**
     * Download attachment securely
     */
    public function downloadAttachment(ComplaintAttachment $attachment): BinaryFileResponse
    {
        return $this->attachmentService->downloadAttachment($attachment, Auth::user());
    }
}
