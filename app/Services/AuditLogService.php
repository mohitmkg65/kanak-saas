<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditLogService
{
    public function log(
        string $action,
        string $module,
        ?string $entityType = null,
        mixed $entityId = null,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?int $organizationId = null
    ): AuditLog {
        $user = Auth::user();
        $contextOrgId = $organizationId ?? app(OrganizationContextService::class)->id();

        return AuditLog::create([
            'user_id' => $user?->id,
            'organization_id' => $contextOrgId,
            'action' => $action,
            'module' => $module,
            'entity_type' => $entityType,
            'entity_id' => $entityId ? (string) $entityId : null,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'created_at' => now(),
        ]);
    }
}
