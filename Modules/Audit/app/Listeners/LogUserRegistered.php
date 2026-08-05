<?php

namespace Modules\Audit\Listeners;

use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Modules\Audit\Models\AuditLog;

class LogUserRegistered implements ShouldQueue
{
    public function handle(Registered $event): void
    {
        try {
            AuditLog::create([
                'user_id' => $event->user->id,
                'action' => 'user.created',
                'resource_type' => 'user',
                'resource_id' => $event->user->id,
                'new_values' => [
                    'name' => $event->user->name,
                    'email' => $event->user->email,
                ],
            ]);
        } catch (\Throwable $e) {
            Log::warning('Failed to write audit log for user registration', ['exception' => $e->getMessage()]);
        }
    }
}
