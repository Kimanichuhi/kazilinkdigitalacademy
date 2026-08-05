<?php

namespace Modules\Marketing\Policies;

use App\Models\User;
use Modules\Core\Support\RoleGroups;

/**
 * Shared policy for advertisements, ctas, statistics — source RLS pattern
 * is public SELECT, admin-only write.
 */
class PublicContentPolicy
{
    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, $model): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(RoleGroups::adminFamily());
    }

    public function update(User $user, $model): bool
    {
        return $user->hasAnyRole(RoleGroups::adminFamily());
    }

    public function delete(User $user, $model): bool
    {
        return $user->hasAnyRole(RoleGroups::adminFamily());
    }
}
