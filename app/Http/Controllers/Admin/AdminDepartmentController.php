<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AdminDepartmentController extends Controller
{
    public function __construct(
        protected AuditService $auditService
    ) {}

    public function index(): View
    {
        $departments = Department::withCount(['profiles', 'complaints'])->orderBy('name')->get();

        return view('admin.departments.index', compact('departments'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'code' => ['required', 'string', 'max:20', 'unique:departments,code'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $department = Department::create($validated);

        $this->auditService->log(
            action: 'DEPARTMENT_CREATED',
            entityType: 'Department',
            entityId: $department->id,
            details: $validated,
            actor: Auth::user()
        );

        return back()->with('success', "Department {$department->name} created successfully.");
    }

    public function update(Department $department, Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'code' => ['required', 'string', 'max:20', "unique:departments,code,{$department->id}"],
        ]);

        $department->update($validated);

        $this->auditService->log(
            action: 'DEPARTMENT_UPDATED',
            entityType: 'Department',
            entityId: $department->id,
            details: $validated,
            actor: Auth::user()
        );

        return back()->with('success', "Department {$department->name} updated successfully.");
    }

    public function toggleStatus(Department $department): RedirectResponse
    {
        $department->update(['is_active' => ! $department->is_active]);

        $this->auditService->log(
            action: 'DEPARTMENT_STATUS_TOGGLED',
            entityType: 'Department',
            entityId: $department->id,
            details: ['is_active' => $department->is_active],
            actor: Auth::user()
        );

        return back()->with('success', 'Department status updated.');
    }
}
