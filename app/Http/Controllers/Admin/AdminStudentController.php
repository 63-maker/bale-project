<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Profile;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AdminStudentController extends Controller
{
    public function __construct(
        protected AuditService $auditService
    ) {}

    public function index(Request $request): View
    {
        $query = Profile::where('role', Profile::ROLE_STUDENT)
            ->with(['department'])
            ->withCount('complaints')
            ->orderBy('full_name');

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->input('department_id'));
        }

        if ($request->filled('study_year')) {
            $query->where('study_year', $request->input('study_year'));
        }

        if ($request->filled('status')) {
            $query->where('account_status', $request->input('status'));
        }

        if ($request->filled('search')) {
            $term = '%'.trim($request->input('search')).'%';
            $query->where(function ($q) use ($term) {
                $q->where('full_name', 'like', $term)
                    ->orWhere('register_number', 'like', $term)
                    ->orWhere('email', 'like', $term);
            });
        }

        $students = $query->paginate(15)->withQueryString();
        $departments = Department::where('is_active', true)->orderBy('name')->get();

        return view('admin.students.index', compact('students', 'departments'));
    }

    public function toggleStatus(Profile $student): RedirectResponse
    {
        if (! $student->isStudent()) {
            abort(404);
        }

        $newStatus = $student->isActive() ? Profile::STATUS_SUSPENDED : Profile::STATUS_ACTIVE;
        $student->update(['account_status' => $newStatus]);

        $this->auditService->log(
            action: 'STUDENT_STATUS_CHANGED',
            entityType: 'Profile',
            entityId: $student->id,
            details: ['new_status' => $newStatus],
            actor: Auth::user()
        );

        $statusLabel = $newStatus === Profile::STATUS_ACTIVE ? 'activated' : 'suspended';

        return back()->with('success', "Student account {$student->full_name} ({$student->register_number}) has been {$statusLabel}.");
    }
}
