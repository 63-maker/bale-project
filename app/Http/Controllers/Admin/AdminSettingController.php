<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AdminSettingController extends Controller
{
    public function __construct(
        protected AuditService $auditService
    ) {}

    public function index(): View
    {
        $settings = [
            'auto_assignment_enabled' => SystemSetting::get('auto_assignment_enabled', true),
            'college_name' => SystemSetting::get('college_name', 'Apex Institute of Technology & Engineering'),
            'support_email' => SystemSetting::get('support_email', 'grievance@apex.edu'),
            'sla_hours' => SystemSetting::get('sla_hours', [
                'LOW' => 120,
                'MEDIUM' => 72,
                'HIGH' => 48,
                'CRITICAL' => 24,
            ]),
            'email_notifications_enabled' => SystemSetting::get('email_notifications_enabled', true),
        ];

        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'college_name' => ['required', 'string', 'max:200'],
            'support_email' => ['required', 'email', 'max:255'],
            'auto_assignment_enabled' => ['nullable', 'boolean'],
            'sla_low' => ['required', 'integer', 'min:1', 'max:720'],
            'sla_medium' => ['required', 'integer', 'min:1', 'max:720'],
            'sla_high' => ['required', 'integer', 'min:1', 'max:720'],
            'sla_critical' => ['required', 'integer', 'min:1', 'max:720'],
        ]);

        SystemSetting::set('college_name', $validated['college_name']);
        SystemSetting::set('support_email', $validated['support_email']);
        SystemSetting::set('auto_assignment_enabled', $request->boolean('auto_assignment_enabled'));
        SystemSetting::set('sla_hours', [
            'LOW' => (int) $validated['sla_low'],
            'MEDIUM' => (int) $validated['sla_medium'],
            'HIGH' => (int) $validated['sla_high'],
            'CRITICAL' => (int) $validated['sla_critical'],
        ]);

        $this->auditService->log(
            action: 'SYSTEM_SETTINGS_UPDATED',
            entityType: 'SystemSetting',
            details: $validated,
            actor: Auth::user()
        );

        return back()->with('success', 'System configuration updated successfully.');
    }
}
