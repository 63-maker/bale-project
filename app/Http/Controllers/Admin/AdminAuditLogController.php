<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminAuditLogController extends Controller
{
    public function index(Request $request): View
    {
        $query = AuditLog::with('actor')->orderByDesc('created_at');

        if ($request->filled('action')) {
            $query->where('action', 'like', '%'.trim($request->input('action')).'%');
        }

        if ($request->filled('actor_id')) {
            $query->where('actor_id', $request->input('actor_id'));
        }

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->input('start_date'));
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->input('end_date'));
        }

        $logs = $query->paginate(20)->withQueryString();
        $actors = Profile::whereIn('id', AuditLog::select('actor_id')->distinct())->orderBy('full_name')->get();

        return view('admin.audit_logs.index', compact('logs', 'actors'));
    }
}
