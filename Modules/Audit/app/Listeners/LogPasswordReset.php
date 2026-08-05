<?php

namespace Modules\Audit\Listeners;

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Log;
use Modules\Audit\Models\AuditLog;

class LogPasswordReset
{
    public function handle(PasswordReset $event): void
    {
        try {
            AuditLog::create([
                'user_id' => $event->user->id,
                'action' => 'auth.password_reset',
                'resource_type' => 'user',
                'resource_id' => $event->user->id,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        } catch (\Throwable $e) {
            Log::warning('Failed to write audit log for password reset', ['exception' => $e->getMessage()]);
        }
    }
}
