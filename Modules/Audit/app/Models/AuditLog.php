<?php

namespace Modules\Audit\Models;

use Modules\Core\Models\BaseModel;

class AuditLog extends BaseModel
{
    const UPDATED_AT = null;

    protected $fillable = [
        'user_id', 'action', 'resource_type', 'resource_id',
        'old_values', 'new_values', 'ip_address', 'user_agent',
    ];

    protected function casts(): array
    {
        return [
            'old_values' => 'array',
            'new_values' => 'array',
        ];
    }

    /**
     * Audit rows must be immutable once written — block mutation at the
     * model level too, not just via the policy (which nothing currently
     * routes through, since rows are only ever written by event listeners).
     */
    protected static function booted(): void
    {
        static::updating(function () {
            throw new \RuntimeException('Audit log entries are immutable and cannot be updated.');
        });

        static::deleting(function () {
            throw new \RuntimeException('Audit log entries are immutable and cannot be deleted.');
        });
    }
}
