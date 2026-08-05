<?php

namespace Modules\Audit\Listeners;

use App\Events\PasswordChanged;
use Illuminate\Support\Facades\Log;
use Modules\Audit\Models\AuditLog;

class LogPasswordChanged
{
    public function handle(PasswordChanged $event): void
    {
        try {
            AuditLog::create([
                'user_id' => $event->userId,
                'action' => 'auth.password_changed',
                'resource_type' => 'user',
                'resource_id' => $event->userId,
                'ip_address' => $event->ipAddress,
                'user_agent' => $event->userAgent,
            ]);
        } catch (\Throwable $e) {
            Log::warning('Failed to write audit log for password change', ['exception' => $e->getMessage()]);
        }
    }
}
