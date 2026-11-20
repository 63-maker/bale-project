<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateManagementRequest;
use App\Models\ComplaintAssignment;
use App\Models\Department;
use App\Models\Profile;
use App\Services\AuditService;
use App\Services\AuthenticationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AdminManagementController extends Controller
{
    public function __construct(
        protected AuthenticationService $authService,
        protected AuditService $auditService
    ) {}

    public function index(): View
    {
        $managementMembers = Profile::where('role', Profile::ROLE_MANAGEMENT)
            ->with('department')
            ->orderBy('full_name')
            ->get()
            ->map(function (Profile $member) {
                $openWorkload = ComplaintAssignment::where('management_id', $member->id)
                    ->where('is_active', true)
                    ->whereHas('complaint', function ($q) {
                        $q->whereNotIn('status', ['CLOSED', 'REJECTED']);
                    })
                    ->count();

                $member->open_workload = $openWorkload;

                return $member;
            });

        $departments = Department::where('is_active', true)->orderBy('name')->get();

        return view('admin.management.index', compact('managementMembers', 'departments'));
    }

    public function store(CreateManagementRequest $request): RedirectResponse
    {
        $management = $this->authService->createManagementAccount($request->validated(), Auth::user());

        return back()->with('success', "Management account for {$management->full_name} ({$management->employee_id}) created successfully.");
    }

    public function update(Profile $management, Request $request): RedirectResponse
    {
        if (! $management->isManagement()) {
            abort(404);
        }

        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:150'],
            'department_id' => ['required', 'uuid', 'exists:departments,id'],
            'email' => ['required', 'email', 'max:255', "unique:profiles,email,{$management->id}"],
        ]);

        $management->update($validated);

        $this->auditService->log(
            action: 'MANAGEMENT_UPDATED_BY_ADMIN',
            entityType: 'Profile',
            entityId: $management->id,
            details: $validated,
            actor: Auth::user()
        );

        return back()->with('success', "Management account {$management->full_name} updated successfully.");
    }

    public function toggleStatus(Profile $management): RedirectResponse
    {
        if (! $management->isManagement()) {
            abort(404);
        }

        $newStatus = $management->isActive() ? Profile::STATUS_SUSPENDED : Profile::STATUS_ACTIVE;
        $management->update(['account_status' => $newStatus]);

        $this->auditService->log(
            action: 'MANAGEMENT_STATUS_CHANGED',
            entityType: 'Profile',
            entityId: $management->id,
            details: ['new_status' => $newStatus],
            actor: Auth::user()
        );

        $statusLabel = $newStatus === Profile::STATUS_ACTIVE ? 'activated' : 'suspended';

        return back()->with('success', "Management account {$management->full_name} has been {$statusLabel}.");
    }
}
