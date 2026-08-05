<?php

namespace Modules\Cms\Policies;

use App\Models\User;
use Modules\Core\Support\RoleGroups;

class ContactSubmissionPolicy
{
    /**
     * The public contact form requires no login (source: /contact is public).
     */
    public function create(?User $user): bool
    {
        return true;
    }

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
        return $user->hasAnyRole(RoleGroups::adminFamily());
    }

    public function delete(User $user, $model): bool
    {
        return $user->hasAnyRole(RoleGroups::adminFamily());
    }
}
