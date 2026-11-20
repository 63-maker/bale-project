<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\Profile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditService
{
    /**
     * Log an administrative, security, or state transition action
     */
    public function log(
        string $action,
        string $entityType,
        ?string $entityId = null,
        ?array $details = null,
        ?Profile $actor = null
    ): AuditLog {
        $actorId = $actor?->id ?? Auth::id();

        return AuditLog::create([
            'actor_id' => $actorId,
            'action' => $action,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'details' => $details,
            'ip_address' => Request::ip(),
        ]);
    }
}
