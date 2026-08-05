<?php

namespace Modules\Audit\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Modules\Audit\Models\AuditLog;
use Modules\Cms\Events\SiteSettingsUpdated;

class LogSiteSettingsUpdated implements ShouldQueue
{
    public function handle(SiteSettingsUpdated $event): void
    {
        if (empty($event->changedKeys)) {
            return;
        }

        try {
            AuditLog::create([
                'user_id' => $event->userId,
                'action' => 'settings.updated',
                'resource_type' => 'site_setting',
                'new_values' => ['changed_keys' => $event->changedKeys],
                'ip_address' => $event->ipAddress,
                'user_agent' => $event->userAgent,
            ]);
        } catch (\Throwable $e) {
            Log::warning('Failed to write audit log for settings update', ['exception' => $e->getMessage()]);
        }
    }
}
