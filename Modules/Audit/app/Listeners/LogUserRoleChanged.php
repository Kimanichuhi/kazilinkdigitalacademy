<?php

namespace Modules\Audit\Listeners;

use Illuminate\Support\Facades\Log;
use Modules\Audit\Models\AuditLog;
use Modules\User\Events\UserRoleChanged;

/**
 * Not queued — role changes are a high-privilege security action.
 */
class LogUserRoleChanged
{
    public function handle(UserRoleChanged $event): void
    {
        try {
            AuditLog::create([
                'user_id' => $event->changedByUserId,
                'action' => 'user.role_changed',
                'resource_type' => 'user',
                'resource_id' => $event->targetUserId,
                'old_values' => ['role' => $event->oldRole],
                'new_values' => ['role' => $event->newRole],
                'ip_address' => $event->ipAddress,
                'user_agent' => $event->userAgent,
            ]);
        } catch (\Throwable $e) {
            Log::warning('Failed to write audit log for role change', ['exception' => $e->getMessage()]);
        }
    }
}
