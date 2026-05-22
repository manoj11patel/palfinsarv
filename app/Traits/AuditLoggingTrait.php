<?php

namespace App\Traits;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

trait AuditLoggingTrait
{
    /**
     * Log an audit event
     *
     * @param string $action
     * @param string $entityType
     * @param int|null $entityId
     * @param array $meta
     * @return void
     */
    public static function logAudit(string $action, string $entityType, ?int $entityId = null, array $meta = []): void
    {
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'meta' => $meta,
        ]);
    }
}
