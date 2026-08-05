<?php

namespace Modules\Audit\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Log;
use Modules\Audit\Models\AuditLog;

/**
 * Not queued — same reasoning as LogNationalIdReveal: a failure to record a
 * security-sensitive action should be visible in-request, not silently lost
 * in a queue (and this app's shared-hosting target may not always have a
 * queue worker running).
 */
class LogUserLogin
{
    public function handle(Login $event): void
    {
        try {
            AuditLog::create([
                'user_id' => $event->user->id,
                'action' => 'auth.login',
                'resource_type' => 'user',
                'resource_id' => $event->user->id,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        } catch (\Throwable $e) {
            Log::warning('Failed to write audit log for login', ['exception' => $e->getMessage()]);
        }
    }
}
