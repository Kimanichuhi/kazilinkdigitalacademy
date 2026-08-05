<?php

namespace Modules\Audit\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Modules\Audit\Models\AuditLog;
use Modules\User\Events\UserProfileUpdated;

class LogUserProfileUpdated implements ShouldQueue
{
    public function handle(UserProfileUpdated $event): void
    {
        try {
            AuditLog::create([
                'user_id' => $event->userId,
                'action' => 'user.profile_updated',
                'resource_type' => 'user',
                'resource_id' => $event->userId,
                'old_values' => $event->oldValues,
                'new_values' => $event->newValues,
                'ip_address' => $event->ipAddress,
                'user_agent' => $event->userAgent,
            ]);
        } catch (\Throwable $e) {
            Log::warning('Failed to write audit log for profile update', ['exception' => $e->getMessage()]);
        }
    }
}
