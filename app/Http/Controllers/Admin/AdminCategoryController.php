<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ComplaintCategory;
use App\Models\Department;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AdminCategoryController extends Controller
{
    public function __construct(
        protected AuditService $auditService
    ) {}

    public function index(): View
    {
        $categories = ComplaintCategory::with(['department'])
            ->withCount('complaints')
            ->orderBy('name')
            ->get();

        $departments = Department::where('is_active', true)->orderBy('name')->get();

        return view('admin.categories.index', compact('categories', 'departments'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:complaint_categories,name'],
            'description' => ['nullable', 'string', 'max:500'],
            'department_id' => ['nullable', 'uuid', 'exists:departments,id'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $category = ComplaintCategory::create($validated);

        $this->auditService->log(
            action: 'CATEGORY_CREATED',
            entityType: 'ComplaintCategory',
            entityId: $category->id,
            details: $validated,
            actor: Auth::user()
        );

        return back()->with('success', "Category {$category->name} created successfully.");
    }

    public function update(ComplaintCategory $category, Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', "unique:complaint_categories,name,{$category->id}"],
            'description' => ['nullable', 'string', 'max:500'],
            'department_id' => ['nullable', 'uuid', 'exists:departments,id'],
        ]);

        $category->update($validated);

        $this->auditService->log(
            action: 'CATEGORY_UPDATED',
            entityType: 'ComplaintCategory',
            entityId: $category->id,
            details: $validated,
            actor: Auth::user()
        );

        return back()->with('success', "Category {$category->name} updated successfully.");
    }

    public function toggleStatus(ComplaintCategory $category): RedirectResponse
    {
        $category->update(['is_active' => ! $category->is_active]);

        $this->auditService->log(
            action: 'CATEGORY_STATUS_TOGGLED',
            entityType: 'ComplaintCategory',
            entityId: $category->id,
            details: ['is_active' => $category->is_active],
            actor: Auth::user()
        );

        return back()->with('success', 'Category status updated.');
    }
}
