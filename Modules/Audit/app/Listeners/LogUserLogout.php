<?php

namespace Modules\Audit\Listeners;

use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Log;
use Modules\Audit\Models\AuditLog;

class LogUserLogout
{
    public function handle(Logout $event): void
    {
        if (! $event->user) {
            return;
        }

        try {
            AuditLog::create([
                'user_id' => $event->user->id,
                'action' => 'auth.logout',
                'resource_type' => 'user',
                'resource_id' => $event->user->id,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        } catch (\Throwable $e) {
            Log::warning('Failed to write audit log for logout', ['exception' => $e->getMessage()]);
        }
    }
}
