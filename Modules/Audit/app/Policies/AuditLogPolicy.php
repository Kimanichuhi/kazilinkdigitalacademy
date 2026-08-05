<?php

namespace Modules\Audit\Policies;

use App\Models\User;
use Modules\Core\Support\RoleGroups;

/**
 * Admin-only read, no manual write — audit rows are created exclusively by
 * queued event listeners (see Modules\Audit\Listeners), never directly by
 * a controller/policy-gated action.
 */
class AuditLogPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(RoleGroups::adminFamily());
    }

    public function view(User $user, $model): bool
    {
        return $user->hasAnyRole(RoleGroups::adminFamily());
    }

    public function update(User $user, $model): bool
    {
        return false;
    }

    public function delete(User $user, $model): bool
    {
        return false;
    }
}
