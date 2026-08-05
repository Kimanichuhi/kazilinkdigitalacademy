<?php

namespace Modules\Payment\Policies;

use App\Models\User;
use Modules\Core\Support\RoleGroups;

/**
 * Admin/finance-only read; rows are created exclusively by the Payment
 * module's STK push service (Phase 6), never by a user-facing action.
 */
class MpesaTransactionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(RoleGroups::adminFamily());
    }

    public function view(User $user, $model): bool
    {
        return $user->hasAnyRole(RoleGroups::adminFamily());
    }
}
